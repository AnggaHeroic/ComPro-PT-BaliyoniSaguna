<?php

namespace Tests\Feature\Admin;

use App\Models\BusinessActivity;
use App\Models\GalleryPhoto;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUiRefinementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->actingAs(User::factory()->create());
    }

    private function category(string $name, int $order = 1): ServiceCategory
    {
        return ServiceCategory::create(['name' => $name, 'slug' => str($name)->slug()->toString(), 'icon' => 'office', 'sort_order' => $order, 'is_published' => true]);
    }

    private function photo(string $title, int $order, bool $published = true): GalleryPhoto
    {
        return GalleryPhoto::create(['title' => $title, 'image' => 'gallery/'.str($title)->slug().'.webp', 'sort_order' => $order, 'is_published' => $published]);
    }

    public function test_pagination_uses_the_admin_theme(): void
    {
        foreach (range(1, 16) as $i) {
            $this->category("Kategori {$i}", $i);
        }

        $this->get(route('admin.categories.index'))
            ->assertSee('aria-label="Paginasi"', false)
            ->assertSee('border-b-brand bg-black-soft', false)
            ->assertSee('Menampilkan', false)
            ->assertDontSee('dark:bg-gray-800', false);
    }

    public function test_edit_pages_have_a_delete_section_that_deletes(): void
    {
        $category = $this->category('Hapus Lewat Form');
        $activity = BusinessActivity::create(['service_category_id' => $category->id, 'name' => 'Aktivitas', 'slug' => 'aktivitas', 'sort_order' => 1]);
        $photo = $this->photo('Foto Hapus', 1);

        $this->get(route('admin.categories.edit', $category))->assertSee('Hapus kategori')->assertSee(route('admin.categories.destroy', $category), false);
        $this->get(route('admin.activities.edit', $activity))->assertSee('Hapus aktivitas')->assertSee(route('admin.activities.destroy', $activity), false);
        $this->get(route('admin.gallery.edit', $photo))->assertSee('Hapus foto')->assertSee(route('admin.gallery.destroy', $photo), false);

        $this->delete(route('admin.activities.destroy', $activity))->assertRedirect();
        $this->delete(route('admin.categories.destroy', $category))->assertRedirect(route('admin.categories.index'));
        $this->assertModelMissing($category);
    }

    public function test_custom_icon_field_comes_before_the_icon_picker(): void
    {
        $html = $this->get(route('admin.categories.create'))->getContent();

        $this->assertLessThan(strpos($html, 'iconPicker('), strpos($html, 'name="icon_image"'));
    }

    public function test_popup_is_centered_with_animation_and_success_closes_itself(): void
    {
        $html = $this->followingRedirects()->post(route('admin.categories.store'), ['name' => 'Popup', 'icon' => 'tools', 'status' => 'published'])->getContent();

        $this->assertStringContainsString('data-flash-popup="success"', $html);
        $this->assertStringContainsString('fixed inset-0 z-[90] flex items-center justify-center', $html);
        $this->assertStringContainsString('flash-panel', $html);
        $this->assertStringContainsString('flash-icon', $html);
        $this->assertStringContainsString('setTimeout(() => show = false, 3500)', $html);
    }

    public function test_error_popup_stays_until_closed(): void
    {
        $category = $this->category('Berisi');
        BusinessActivity::create(['service_category_id' => $category->id, 'name' => 'A', 'slug' => 'a', 'sort_order' => 1]);

        $html = $this->from(route('admin.categories.edit', $category))->followingRedirects()->delete(route('admin.categories.destroy', $category))->getContent();

        $this->assertStringContainsString('data-flash-popup="error"', $html);
        $this->assertStringNotContainsString('setTimeout(() => show = false', $html);
    }

    public function test_gallery_preview_grid_lists_only_published_photos_with_a_viewer_and_a_clean_action_bar(): void
    {
        $this->photo('Foto Terbit', 1);
        $this->photo('Foto Draft', 2, false);

        $html = $this->get(route('admin.gallery.index'))->assertOk()->getContent();

        $this->assertStringContainsString('galleryLightbox', $html);
        $this->assertStringContainsString('Pratinjau foto galeri', $html);
        $this->assertStringContainsString('aria-label="Lihat foto: Foto Terbit"', $html);
        $this->assertStringNotContainsString('title="Lihat"', $html);
        $this->assertStringContainsString('aria-label="Ubah Foto Terbit"', $html);
        $this->assertStringContainsString('aria-label="Hapus Foto Terbit"', $html);
        $this->assertStringNotContainsString('foto-draft.webp', $html);
    }

    public function test_status_tabs_show_the_bento_grid_for_published_and_a_list_with_thumbnails_for_drafts(): void
    {
        $this->photo('Foto Terbit', 1);
        $this->photo('Foto Draft', 2, false);

        $published = $this->get(route('admin.gallery.index'))->assertOk()->assertSee('aria-label="Status foto"', false)->assertSee('Foto Terbit')->assertDontSee('Foto Draft')->getContent();
        $this->assertStringContainsString('lg:auto-rows-[13rem]', $published);
        $this->assertStringNotContainsString('foto-draft.webp', $published);

        $draft = $this->get(route('admin.gallery.index', ['status' => 'draft']))->assertOk()->assertSee('Foto Draft')->assertDontSee('Foto Terbit')->getContent();
        $this->assertStringNotContainsString('lg:auto-rows-[13rem]', $draft);
        $this->assertStringContainsString('<table', $draft);
        $this->assertStringContainsString('foto-draft.webp', $draft);
        $this->assertStringContainsString('size-12 shrink-0 overflow-hidden', $draft);
        $this->assertStringContainsString(route('admin.gallery.edit', GalleryPhoto::firstWhere('title', 'Foto Draft')), $draft);
    }

    public function test_tabs_show_counts_and_reorder_is_only_offered_on_the_published_tab(): void
    {
        $this->photo('A', 1);
        $this->photo('B', 2);
        $this->photo('D', 3, false);

        $this->get(route('admin.gallery.index'))->assertSee('role="switch"', false);
        $this->get(route('admin.gallery.index', ['status' => 'draft']))->assertDontSee('role="switch"', false);
        $this->get(route('admin.gallery.index', ['status' => 'draft', 'reorder' => 1]))->assertDontSee('x-data="reorderList"', false);
        $this->get(route('admin.gallery.index', ['reorder' => 1]))->assertDontSee('aria-label="Status foto"', false);
    }

    public function test_empty_draft_tab_has_a_message(): void
    {
        $this->photo('Hanya Terbit', 1);

        $this->get(route('admin.gallery.index', ['status' => 'draft']))->assertSee('Tidak ada foto draft');
    }

    public function test_preview_has_a_centered_magnifier_and_the_action_bar_only_has_edit_and_delete(): void
    {
        $this->photo('Foto Terbit', 1);

        $html = $this->get(route('admin.gallery.index'))->getContent();

        $this->assertStringContainsString('pointer-events-none absolute inset-0 flex items-center justify-center bg-black/0', $html);
        $this->assertStringContainsString('group-hover:opacity-100', $html);
        $this->assertStringContainsString('flex size-10 scale-90 items-center justify-center rounded-full bg-white/90', $html);
        $this->assertStringContainsString('group-hover:bg-black/25', $html);
        $this->assertStringNotContainsString('size-12 text-white', $html);
    }

    public function test_category_menu_is_named_kategori_usaha(): void
    {
        $this->get(route('admin.categories.index'))->assertSee('Kategori Usaha');
        $this->get(route('admin.categories.create'))->assertSee('Tambah Kategori Usaha');
        $this->get(route('admin.dashboard'))->assertSee('Kategori Usaha');
    }

    public function test_reorder_mode_lets_photos_be_dragged_anywhere_with_transparent_arrows_and_swapped_number(): void
    {
        $this->photo('A', 1);
        $this->photo('B', 2);

        $html = $this->get(route('admin.gallery.index', ['reorder' => 1]))->assertOk()->getContent();

        $this->assertStringContainsString('data-drag-anywhere', $html);
        $this->assertStringNotContainsString('data-drag-handle', $html);
        $this->assertStringContainsString('bg-black-deep/35', $html);
        $this->assertStringNotContainsString('bg-white text-muted', $html);
        $this->assertStringContainsString('touch-none', $html);
        // Panah di kiri atas, nomor di kanan atas.
        $this->assertLessThan(strpos($html, 'right-3 top-3'), strpos($html, 'left-2 top-2'));
        $this->assertStringNotContainsString('galleryLightbox', $html);
    }

    public function test_reorder_only_covers_published_photos_and_ignores_drafts(): void
    {
        $a = $this->photo('A', 1);
        $b = $this->photo('B', 2);
        $draft = $this->photo('Draft', 3, false);

        $this->put(route('admin.gallery.reorder'), ['order' => [$b->id, $a->id]])->assertSessionHas('success');
        $this->assertSame([1, 2, 3], [$b->fresh()->sort_order, $a->fresh()->sort_order, $draft->fresh()->sort_order]);

        $this->put(route('admin.gallery.reorder'), ['order' => [$a->id, $b->id, $draft->id]])->assertSessionHas('error');
    }

    public function test_public_gallery_still_has_the_viewer_after_extracting_it_into_a_component(): void
    {
        $this->photo('Foto Publik', 1);

        $this->get(route('gallery'))->assertOk()->assertSee('Pratinjau foto galeri')->assertSee('galleryLightbox', false);
    }

    public function test_popups_sit_directly_under_body_outside_the_content_container(): void
    {
        $html = $this->followingRedirects()->post(route('admin.categories.store'), ['name' => 'Tengah', 'icon' => 'tools', 'status' => 'published'])->getContent();

        $this->assertGreaterThan(strpos($html, '</main>'), strpos($html, 'data-flash-popup'));
        $this->assertGreaterThan(strpos($html, '</main>'), strpos($html, 'role="alertdialog" aria-modal="true" aria-labelledby="confirm-title"'));
    }

    public function test_sidebar_has_a_public_pages_group_with_inactive_items_for_now(): void
    {
        $html = $this->get(route('admin.dashboard'))->assertOk()->assertSee('Isi Halaman')->getContent();

        foreach (['Beranda', 'Layanan', 'Tentang Kami', 'Kontak'] as $label) {
            $this->assertMatchesRegularExpression('/aria-disabled="true"[^>]*>\s*<svg[\s\S]*?<\/svg>\s*'.preg_quote($label, '/').'/', $html, $label);
        }

        $this->assertLessThan(strpos($html, 'Riwayat Aktivitas'), strpos($html, 'Isi Halaman'));
    }

    public function test_every_list_search_has_a_search_button(): void
    {
        foreach (['admin.categories.index', 'admin.activities.index', 'admin.activity-logs.index'] as $route) {
            $html = $this->get(route($route))->assertOk()->getContent();

            $this->assertStringContainsString('name="q"', $html, $route);
            $this->assertMatchesRegularExpression('/>\s*Cari\s*<\/button>/', $html, $route);
        }
    }

    public function test_search_still_filters_when_submitted(): void
    {
        $this->category('Alfa Unik', 1);
        $this->category('Beta Lain', 2);

        $this->get(route('admin.categories.index', ['q' => 'alfa']))->assertSee('Alfa Unik')->assertDontSee('Beta Lain');
    }

    public function test_sidebar_titles_are_red_and_the_scrollbar_matches_the_dark_sidebar(): void
    {
        $html = $this->get(route('admin.dashboard'))->getContent();

        $this->assertStringContainsString('scrollbar-dark', $html);
        $this->assertStringContainsString('>Utama</p>', $html);
        $this->assertStringNotContainsString('>Menu</p>', $html);
        $this->assertSame(4, preg_match_all('/<p class="[^"]*text-brand-bright[^"]*">(Utama|Konten|Isi Halaman|Sistem)<\/p>/', $html));
        $this->assertStringNotContainsString('text-white/40">Konten', $html);
    }
}
