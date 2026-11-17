<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\GalleryPhoto;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Services\PageContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_stat_cards_link_to_their_management_menus(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $html = $this->get(route('admin.dashboard'))->assertOk()->getContent();

        foreach ([
            'Kategori Layanan' => route('admin.categories.index'),
            'Aktivitas Usaha' => route('admin.activities.index'),
            'Foto Galeri' => route('admin.gallery.index'),
            'Halaman Publik' => route('admin.pages.edit', 'home'),
        ] as $label => $url) {
            $this->assertStringContainsString('href="'.$url.'" aria-label="Kelola '.$label.'"', $html, $label);
        }

        // Animasi hover tipis hanya untuk kartu tautan: tanpa bayangan dan tanpa gerakan kartu.
        $this->assertStringContainsString('hover:border-brand/40', $html);
        $this->assertStringNotContainsString('hover:shadow', $html);
        $this->assertStringNotContainsString('hover:-translate-y', $html);
    }

    public function test_public_pages_card_counts_the_editable_pages(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.dashboard'))->assertSee('Teks dan foto bisa diubah');
        $this->assertCount(5, app(PageContentService::class)->pages());
    }

    public function test_shortcuts_point_to_the_content_editors_in_navbar_order(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $html = $this->get(route('admin.dashboard'))->assertOk()->assertSee('Pintasan Pengelolaan')->assertDontSee('Pintasan Website')->assertDontSee('Profil Perusahaan')->getContent();

        $positions = array_map(fn (string $slug) => strpos($html, 'href="'.route('admin.pages.edit', $slug).'" class="group flex'), ['home', 'about', 'services', 'gallery', 'contact', 'general']);
        $this->assertNotContains(false, $positions);
        $sorted = $positions;
        sort($sorted);
        $this->assertSame($sorted, $positions);
    }

    public function test_recent_activity_card_shows_the_latest_five_changes_and_links_to_the_full_history(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create(['name' => 'Budi Admin']));

        $this->get(route('admin.dashboard'))->assertSee('Aktivitas Terbaru')->assertSee('Belum ada aktivitas yang tercatat.');

        foreach (range(1, 7) as $i) {
            ServiceCategory::create(['name' => "Kategori Uji {$i}", 'slug' => "kategori-uji-{$i}", 'icon' => 'office', 'sort_order' => $i, 'is_published' => true]);
        }
        GalleryPhoto::create(['title' => 'Foto Terbaru', 'image' => 'gallery/x.webp', 'sort_order' => 1]);

        $this->assertSame(8, ActivityLog::count());

        $response = $this->get(route('admin.dashboard'))->assertOk()->assertSee('Foto Terbaru')->assertSee('Kategori Uji 7')->assertSee('Kategori Uji 4')
            ->assertDontSee('Kategori Uji 3')->assertSee('Budi Admin')->assertSee('Menampilkan 5 perubahan terakhir')
            ->assertSee('href="'.route('admin.activity-logs.index').'"', false);

        $this->assertStringContainsString('Ditambah', $response->getContent());
    }
}
