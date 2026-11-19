<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\GalleryPhoto;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogPageTest extends TestCase
{
    use RefreshDatabase;

    private function category(string $name): ServiceCategory
    {
        return ServiceCategory::create(['name' => $name, 'slug' => str($name)->slug()->toString(), 'icon' => 'office', 'sort_order' => 1, 'is_published' => true]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.activity-logs.index'))->assertRedirect(route('login'));
    }

    public function test_page_lists_who_changed_what(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create(['name' => 'Budi Admin']));

        $category = $this->category('Peralatan Uji');
        $category->update(['name' => 'Peralatan Uji Baru']);

        $this->get(route('admin.activity-logs.index'))
            ->assertOk()->assertSee('Budi Admin')->assertSee('Peralatan Uji')->assertSee('Ditambah')->assertSee('Diubah')
            ->assertSee('Mengubah kategori usaha &quot;Peralatan Uji Baru&quot;', false)->assertSee('Yang diubah: Nama');
    }

    public function test_filters_by_action_menu_and_keyword(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create(['name' => 'Budi Admin']));

        $this->category('Kategori Satu');
        GalleryPhoto::create(['title' => 'Foto Dua', 'image' => 'gallery/x.webp', 'sort_order' => 1]);

        $this->get(route('admin.activity-logs.index', ['menu' => 'Foto Galeri']))->assertSee('Foto Dua')->assertDontSee('Kategori Satu');
        $this->get(route('admin.activity-logs.index', ['menu' => 'Kategori Usaha']))->assertSee('Kategori Satu')->assertDontSee('Foto Dua');
        $this->get(route('admin.activity-logs.index', ['action' => 'deleted']))->assertSee('Tidak ada catatan yang cocok');
        $this->get(route('admin.activity-logs.index', ['q' => 'Dua']))->assertSee('Foto Dua')->assertDontSee('Kategori Satu');
        $this->get(route('admin.activity-logs.index', ['q' => 'Budi']))->assertSee('Foto Dua')->assertSee('Kategori Satu');
    }

    public function test_keyword_matches_the_description_of_access_and_default_actions(): void
    {
        $this->withoutVite();
        $user = User::factory()->create();
        $this->actingAs($user);

        $log = fn (string $action, string $type = User::class, ?string $label = null) => ActivityLog::create(['user_id' => $user->id, 'action' => $action, 'subject_type' => $type, 'subject_id' => 0, 'subject_label' => $label]);
        $log(ActivityLog::ACTION_LOGIN);
        $log(ActivityLog::ACTION_LOGOUT);
        $log(ActivityLog::ACTION_DEFAULT_CLEARED, AppModelsPage::class, 'Beranda');

        $this->get(route('admin.activity-logs.index', ['q' => 'dashboard']))->assertSee('Masuk ke dashboard')->assertSee('Keluar dari dashboard')->assertDontSee('Menghapus setelan default');
        $this->get(route('admin.activity-logs.index', ['q' => 'bawaan sistem']))->assertSee('Menghapus setelan default')->assertDontSee('Masuk ke dashboard');
    }

    public function test_keyword_also_matches_the_description_column(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create(['name' => 'Budi Admin']));

        $kategori = $this->category('Kategori Satu');
        $kategori->update(['name' => 'Kategori Satu B']);
        GalleryPhoto::create(['title' => 'Foto Dua', 'image' => 'gallery/x.webp', 'sort_order' => 1]);

        // Kata kerja: hanya baris "Mengubah" yang muncul
        $this->get(route('admin.activity-logs.index', ['q' => 'mengubah']))->assertSee('Mengubah kategori usaha', false)->assertDontSee('Foto Dua');
        // Sebutan menu pada keterangan
        $this->get(route('admin.activity-logs.index', ['q' => 'galeri']))->assertSee('Foto Dua')->assertDontSee('Kategori Satu B');
        // Nama kolom yang diubah ("Yang diubah: Nama")
        $this->get(route('admin.activity-logs.index', ['q' => 'nama']))->assertSee('Yang diubah: Nama')->assertDontSee('Foto Dua');
    }

    public function test_filters_by_date_range_and_swaps_reversed_dates(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->category('Lama');
        ActivityLog::query()->update(['created_at' => now()->subDays(20)]);
        $this->category('Baru');

        $today = now()->toDateString();
        $weekAgo = now()->subDays(6)->toDateString();

        $this->get(route('admin.activity-logs.index', ['dari' => $weekAgo, 'sampai' => $today]))->assertSee('Baru')->assertDontSee('Lama');
        $this->get(route('admin.activity-logs.index', ['dari' => $today, 'sampai' => $weekAgo]))->assertSee('Baru')->assertDontSee('Lama');
        $this->get(route('admin.activity-logs.index', ['dari' => now()->subDays(30)->toDateString(), 'sampai' => now()->subDays(10)->toDateString()]))->assertSee('Lama')->assertDontSee('Baru');
    }

    public function test_invalid_filters_are_rejected(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.activity-logs.index', ['dari' => 'bukan-tanggal']))->assertSessionHasErrors('dari');
        $this->get(route('admin.activity-logs.index', ['menu' => 'Tidak Ada']))->assertSessionHasErrors('menu');
    }

    public function test_date_filter_button_shows_the_active_range(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.activity-logs.index', ['dari' => '2026-11-01', 'sampai' => '2026-11-07']))->assertSee('01 Nov 2026 – 07 Nov 2026', false);
    }

    public function test_edit_page_shows_the_last_change(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create(['name' => 'Siti Editor']));

        $category = $this->category('Kategori Uji');

        $this->get(route('admin.categories.edit', $category))->assertOk()->assertSee('Terakhir diubah')->assertSee('Siti Editor');
    }
}
