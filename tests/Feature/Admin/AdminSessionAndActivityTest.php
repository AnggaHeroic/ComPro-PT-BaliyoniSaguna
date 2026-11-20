<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\BusinessActivity;
use App\Models\GalleryPhoto;
use App\Models\Page;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Services\IdleSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Logout otomatis karena tidak aktif, dan catatan riwayat untuk masuk, keluar, urutan, serta setelan default. */
class AdminSessionAndActivityTest extends TestCase
{
    use RefreshDatabase;

    private function lastLog(): ActivityLog
    {
        return ActivityLog::latest('id')->firstOrFail();
    }

    // ---- Masuk dan keluar ----

    public function test_login_and_manual_logout_are_recorded(): void
    {
        $user = User::factory()->create(['name' => 'Budi Admin']);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('admin.dashboard'));

        $login = $this->lastLog();
        $this->assertSame(ActivityLog::ACTION_LOGIN, $login->action);
        $this->assertSame($user->id, $login->user_id);
        $this->assertSame('Budi Admin', $login->subject_label);
        $this->assertSame('Masuk ke dashboard', $login->detailLines()[0]);

        $this->post(route('logout'))->assertRedirect();

        $logout = $this->lastLog();
        $this->assertSame(ActivityLog::ACTION_LOGOUT, $logout->action);
        $this->assertSame($user->id, $logout->user_id);
        $this->assertSame('Keluar dari dashboard', $logout->detailLines()[0]);
    }

    public function test_failed_login_is_not_recorded(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'salah']);

        $this->assertSame(0, ActivityLog::count());
    }

    // ---- Keluar otomatis ----

    public function test_a_session_idle_past_the_limit_is_logged_out_recorded_and_told_why(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['name' => 'Siti Admin']);
        $minutes = app(IdleSessionService::class)->timeoutMinutes();

        $this->actingAs($user)->withSession([IdleSessionService::KEY => now()->subMinutes($minutes + 1)->timestamp])
            ->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $this->assertGuest();
        $log = $this->lastLog();
        $this->assertSame(ActivityLog::ACTION_TIMEOUT, $log->action);
        $this->assertSame($user->id, $log->user_id);
        $this->assertSame("Keluar otomatis karena tidak aktif selama {$minutes} menit", $log->detailLines()[0]);
        // Hanya "Keluar otomatis" yang tercatat, tanpa "Keluar" biasa.
        $this->assertSame(0, ActivityLog::where('action', ActivityLog::ACTION_LOGOUT)->count());

        $this->get(route('login'))->assertSee("Anda keluar otomatis karena tidak aktif selama {$minutes} menit");
    }

    public function test_an_active_session_is_kept_and_its_clock_refreshed(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();

        $this->actingAs($user)->withSession([IdleSessionService::KEY => now()->subMinutes(5)->timestamp])
            ->get(route('admin.dashboard'))->assertOk();

        $this->assertAuthenticated();
        $this->assertLessThan(5, now()->timestamp - session(IdleSessionService::KEY));
        $this->assertSame(0, ActivityLog::count());
    }

    public function test_the_warning_freezes_the_clock_until_stay_signed_in_is_pressed(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $limit = app(IdleSessionService::class)->timeoutMinutes() * 60;

        // Peringatan tampil saat admin sudah diam hampir sampai batas.
        $this->actingAs($user)->post(route('admin.idle-warning'), ['idle' => $limit - 100])->assertNoContent();
        $this->assertTrue(session(IdleSessionService::WARNING_KEY));

        // Memuat halaman lain tidak menyegarkan hitungan.
        $this->get(route('admin.dashboard'))->assertOk();
        $this->assertGreaterThanOrEqual($limit - 101, now()->timestamp - session(IdleSessionService::KEY));

        // "Tetap masuk": sesi dilanjutkan dari sekarang dan peringatan hilang.
        $this->post(route('admin.keep-alive'))->assertNoContent();
        $this->assertNull(session(IdleSessionService::WARNING_KEY));
        $this->assertLessThan(5, now()->timestamp - session(IdleSessionService::KEY));
    }

    public function test_idle_warning_validates_its_input_and_caps_the_idle_time(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('admin.idle-warning'), ['idle' => 'abc'])->assertSessionHasErrors('idle');

        $limit = app(IdleSessionService::class)->timeoutMinutes() * 60;
        $this->post(route('admin.idle-warning'), ['idle' => $limit * 10])->assertNoContent();
        $this->assertLessThanOrEqual($limit + 2, now()->timestamp - session(IdleSessionService::KEY));
    }

    public function test_the_browser_timer_logout_is_confirmed_by_the_server(): void
    {
        $user = User::factory()->create();
        $limit = app(IdleSessionService::class)->timeoutMinutes() * 60;

        // Server menilai admin masih aktif (mis. di tab lain): tidak dikeluarkan.
        $this->actingAs($user)->withSession([IdleSessionService::KEY => now()->timestamp])->from(route('admin.dashboard'))
            ->post(route('admin.idle-logout'))->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->assertSame(0, ActivityLog::count());

        // Benar-benar diam: dikeluarkan dan dicatat sebagai keluar otomatis.
        $this->withSession([IdleSessionService::KEY => now()->timestamp - $limit - 5])->post(route('admin.idle-logout'))->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertSame(ActivityLog::ACTION_TIMEOUT, $this->lastLog()->action);
    }

    public function test_every_admin_page_carries_the_idle_guard_and_the_csrf_meta(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $html = $this->get(route('admin.dashboard'))->assertOk()->assertSee('Sesi akan berakhir')->assertSee('Tetap masuk')->assertSee('name="csrf-token"', false)->getContent();

        $this->assertStringContainsString(route('admin.idle-logout'), $html);
        $this->assertStringContainsString('x-data="idleGuard(', $html);
        $this->get(route('login'))->assertDontSee('Sesi akan berakhir');
    }

    // ---- Ubah urutan ----

    public function test_reorders_are_recorded_once_each(): void
    {
        $this->actingAs(User::factory()->create(['name' => 'Budi Admin']));
        $a = ServiceCategory::create(['name' => 'Alat A', 'slug' => 'alat-a', 'icon' => 'office', 'sort_order' => 1, 'is_published' => true]);
        $b = ServiceCategory::create(['name' => 'Alat B', 'slug' => 'alat-b', 'icon' => 'office', 'sort_order' => 2, 'is_published' => true]);
        $x = BusinessActivity::create(['service_category_id' => $a->id, 'name' => 'Akt X', 'slug' => 'akt-x', 'sort_order' => 1, 'is_published' => true]);
        $y = BusinessActivity::create(['service_category_id' => $a->id, 'name' => 'Akt Y', 'slug' => 'akt-y', 'sort_order' => 2, 'is_published' => true]);
        $p = GalleryPhoto::create(['title' => 'Foto P', 'image' => 'gallery/p.webp', 'sort_order' => 1, 'is_published' => true]);
        $q = GalleryPhoto::create(['title' => 'Foto Q', 'image' => 'gallery/q.webp', 'sort_order' => 2, 'is_published' => true]);
        ActivityLog::query()->delete();

        $this->put(route('admin.categories.reorder'), ['order' => [$b->id, $a->id]]);
        $this->assertSame([ServiceCategory::class, 'Mengubah urutan kategori usaha'], [$this->lastLog()->subject_type, $this->lastLog()->detailLines()[0]]);

        $this->put(route('admin.activities.reorder'), ['orders' => [$a->id => [$y->id, $x->id]]]);
        $this->assertSame(BusinessActivity::class, $this->lastLog()->subject_type);
        $this->assertSame(['Mengubah urutan aktivitas usaha', 'Kategori: Alat A'], $this->lastLog()->detailLines());

        $this->put(route('admin.gallery.reorder'), ['order' => [$q->id, $p->id]]);
        $this->assertSame(GalleryPhoto::class, $this->lastLog()->subject_type);

        $this->assertSame(3, ActivityLog::where('action', ActivityLog::ACTION_REORDERED)->count());
        $this->assertSame(3, ActivityLog::count());
    }

    // ---- Setelan default ----

    public function test_set_reset_and_clear_default_are_recorded_with_their_own_actions(): void
    {
        $this->actingAs(User::factory()->create(['name' => 'Budi Admin']));
        $this->put(route('admin.pages.update', 'home'), ['content' => ['hero_title' => 'Judul Kustom']] + $this->homePayload(['hero_title' => 'Judul Kustom']));
        ActivityLog::query()->delete();

        $this->post(route('admin.pages.default.set', 'home'));
        $this->assertSame(ActivityLog::ACTION_DEFAULT, $this->lastLog()->action);
        $this->assertSame(Page::class, $this->lastLog()->subject_type);
        $this->assertSame('Menetapkan isi halaman "Beranda" yang tersimpan sebagai setelan default baru', $this->lastLog()->detailLines()[0]);

        $this->delete(route('admin.pages.reset', 'home'));
        $this->assertSame(ActivityLog::ACTION_RESET, $this->lastLog()->action);
        $this->assertSame('Mengembalikan seluruh isi halaman "Beranda" ke setelan default', $this->lastLog()->detailLines()[0]);

        $this->delete(route('admin.pages.default.clear', 'home'));
        $this->assertSame(ActivityLog::ACTION_DEFAULT_CLEARED, $this->lastLog()->action);

        // Masing-masing hanya satu catatan: tidak menyusul catatan "Diubah" otomatis.
        $this->assertSame(3, ActivityLog::count());
        $this->assertSame(0, ActivityLog::where('action', ActivityLog::ACTION_UPDATED)->count());
    }

    /** @return array{content: array<string, mixed>} */
    private function homePayload(array $overrides = []): array
    {
        $content = [];

        foreach (config('page_contents.home.sections') as $section) {
            foreach ($section['fields'] as $key => $field) {
                if (($field['type'] ?? 'text') !== 'image') {
                    $content[$key] = $field['default'];
                }
            }
        }

        return ['content' => [...$content, ...$overrides]];
    }

    // ---- Pengelompokan di halaman Riwayat Aktivitas ----

    public function test_history_is_grouped_into_all_content_and_access_tabs_with_counts(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['name' => 'Budi Admin']);
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        ServiceCategory::create(['name' => 'Kategori Uji', 'slug' => 'kategori-uji', 'icon' => 'office', 'sort_order' => 1, 'is_published' => true]);

        $all = $this->get(route('admin.activity-logs.index'))->assertOk()->assertSee('Semua')->assertSee('Perubahan konten')->assertSee('Akses akun')
            ->assertSee('Kategori Uji')->assertSee('Masuk ke dashboard')->getContent();
        $this->assertMatchesRegularExpression('/Semua\s*<span[^>]*>2<\/span>/', $all);
        $this->assertMatchesRegularExpression('/Perubahan konten\s*<span[^>]*>1<\/span>/', $all);
        $this->assertMatchesRegularExpression('/Akses akun\s*<span[^>]*>1<\/span>/', $all);

        $this->get(route('admin.activity-logs.index', ['jenis' => 'konten']))->assertSee('Kategori Uji')->assertDontSee('Masuk ke dashboard')->assertSee('Semua menu');
        $this->get(route('admin.activity-logs.index', ['jenis' => 'akses']))->assertSee('Masuk ke dashboard')->assertDontSee('Kategori Uji')->assertDontSee('Semua menu');
        $this->get(route('admin.activity-logs.index', ['jenis' => 'tidak-ada']))->assertSessionHasErrors('jenis');
    }

    public function test_group_filters_only_offer_actions_that_apply_and_search_finds_the_new_actions(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['name' => 'Budi Admin']);
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $this->post(route('logout'));
        $this->actingAs($user);

        $access = $this->get(route('admin.activity-logs.index', ['jenis' => 'akses']))->getContent();
        $this->assertStringContainsString('Keluar otomatis', $access);
        $this->assertStringNotContainsString('Reset default', $access);

        $content = $this->get(route('admin.activity-logs.index', ['jenis' => 'konten']))->getContent();
        $this->assertStringContainsString('Reset default', $content);

        $this->get(route('admin.activity-logs.index', ['q' => 'keluar']))->assertSee('Keluar dari dashboard')->assertDontSee('Masuk ke dashboard');
        $this->get(route('admin.activity-logs.index', ['jenis' => 'akses', 'action' => 'login']))->assertSee('Masuk ke dashboard')->assertDontSee('Keluar dari dashboard');
    }

    public function test_dashboard_recent_activity_card_skips_login_and_logout_records(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        ServiceCategory::create(['name' => 'Kategori Terbaru', 'slug' => 'kategori-terbaru', 'icon' => 'office', 'sort_order' => 1, 'is_published' => true]);

        $this->get(route('admin.dashboard'))->assertSee('Kategori Terbaru')->assertDontSee('Masuk ke dashboard');
    }
}
