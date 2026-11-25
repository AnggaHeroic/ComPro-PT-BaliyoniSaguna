<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PageContentTest extends TestCase
{
    use RefreshDatabase;

    /** Isian form lengkap dengan teks bawaan; $overrides menimpa kunci tertentu. */
    private function payload(array $overrides = []): array
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

    private function save(array $overrides = [], array $extra = [])
    {
        return $this->put(route('admin.pages.update', 'home'), [...$this->payload($overrides), ...$extra]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.pages.edit', 'home'))->assertRedirect(route('login'));
        $this->put(route('admin.pages.update', 'home'))->assertRedirect(route('login'));
    }

    public function test_unknown_page_is_not_found(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.pages.edit', 'tidak-ada'))->assertNotFound();
    }

    public function test_edit_page_shows_sections_with_default_text(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.pages.edit', 'home'))
            ->assertOk()->assertSee('Bagian utama')->assertSee('Profil singkat')->assertSee('Mesin pencari')
            ->assertSee('One Stop Solution')->assertSee('Setelan default');
    }

    public function test_saved_text_appears_on_the_public_page_and_unchanged_text_is_not_stored(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->save(['hero_title' => 'Solusi Satu Pintu'])->assertRedirect()->assertSessionHas('success');

        $this->assertSame(['hero_title' => 'Solusi Satu Pintu'], Page::where('slug', 'home')->first()->content);
        $this->get(route('home'))->assertOk()->assertSee('Solusi Satu Pintu');

        // Dikembalikan ke teks bawaan: barisnya hilang.
        $this->save()->assertSessionHas('success');
        $this->assertNull(Page::where('slug', 'home')->first()->content);
    }

    public function test_title_color_markers_render_as_coloured_text_and_are_escaped(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->save(['hero_eyebrow' => 'Butuh <b>solusi</b> *cepat?*'])->assertSessionHasNoErrors();

        $this->get(route('home'))->assertSee('Butuh &lt;b&gt;solusi&lt;/b&gt; <span class="text-brand-bright">cepat?</span>', false);
    }

    public function test_unbalanced_colour_marker_and_blank_required_text_are_rejected(): void
    {
        $this->actingAs(User::factory()->create());

        $this->save(['hero_eyebrow' => 'Butuh *solusi'])->assertSessionHasErrors('content.hero_eyebrow');
        $this->save(['hero_title' => ''])->assertSessionHasErrors('content.hero_title');
        $this->save(['hero_title' => str_repeat('a', 61)])->assertSessionHasErrors('content.hero_title');
        $this->assertNull(Page::where('slug', 'home')->first());
    }

    public function test_optional_text_can_be_blanked_to_hide_it(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->save(['about_paragraph_2' => ''])->assertSessionHasNoErrors();

        $this->get(route('home'))->assertOk()->assertDontSee('Dengan semboyan One Stop Solution');
    }

    public function test_cards_can_be_changed_and_keep_icons_by_position(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->save(['advantages_items' => [
            ['title' => 'Garansi Resmi', 'text' => 'Semua produk bergaransi.'],
            ['title' => '', 'text' => ''],
            ['title' => 'Layanan Cepat', 'text' => 'Respons dalam sehari.'],
        ]])->assertSessionHasNoErrors();

        $this->assertCount(2, Page::where('slug', 'home')->first()->content['advantages_items']);
        $this->get(route('home'))->assertSee('Garansi Resmi')->assertSee('Layanan Cepat')->assertDontSee('Transaksi Aman dan Jelas');
    }

    public function test_cards_need_at_least_one_filled_card_and_respect_limits(): void
    {
        $this->actingAs(User::factory()->create());

        $this->save(['advantages_items' => [['title' => '', 'text' => '']]])->assertSessionHasErrors('content.advantages_items');
        $this->save(['advantages_items' => [['title' => 'Hanya judul', 'text' => '']]])->assertSessionHasErrors('content.advantages_items.0.text');
        $this->save(['advantages_items' => array_fill(0, 6, ['title' => 'A', 'text' => 'B'])])->assertSessionHasErrors('content.advantages_items');
    }

    public function test_image_upload_replaces_the_default_and_can_be_reverted(): void
    {
        Storage::fake('public');
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->save([], ['image_hero_image' => UploadedFile::fake()->image('hero.jpg', 1600, 900)])->assertSessionHasNoErrors();

        $path = Page::where('slug', 'home')->first()->content['hero_image'];
        Storage::disk('public')->assertExists($path);
        $this->get(route('home'))->assertSee(Storage::disk('public')->url($path), false);

        // Unggahan kedua mengganti file lama.
        $this->save([], ['image_hero_image' => UploadedFile::fake()->image('baru.jpg', 1600, 900)]);
        Storage::disk('public')->assertMissing($path);
        $second = Page::where('slug', 'home')->first()->content['hero_image'];
        Storage::disk('public')->assertExists($second);

        $this->save([], ['remove_image_hero_image' => '1'])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($second);
        $this->assertNull(Page::where('slug', 'home')->first()->content);
    }

    public function test_non_image_upload_is_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        $this->save([], ['image_hero_image' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf')])->assertSessionHasErrors('image_hero_image');
    }

    public function test_reset_returns_everything_to_the_defaults(): void
    {
        $this->actingAs(User::factory()->create());
        $this->save(['hero_title' => 'Judul Baru', 'about_button' => 'Hubungi']);

        $this->delete(route('admin.pages.reset', 'home'))->assertRedirect(route('admin.pages.edit', 'home'))->assertSessionHas('success');

        $this->assertNull(Page::where('slug', 'home')->first()->content);
    }

    public function test_clear_default_log_puts_the_fallback_note_under_the_sentence(): void
    {
        $this->actingAs(User::factory()->create());
        $this->save(['hero_title' => 'Judul Kustom']);
        $this->post(route('admin.pages.default.set', 'home'));
        $this->delete(route('admin.pages.default.clear', 'home'));

        $this->assertSame(
            ['Menghapus setelan default kustom isi halaman "Beranda"', 'Kembali ke default bawaan sistem', null],
            ActivityLog::latest('id')->first()->detailLines(),
        );
    }

    public function test_set_default_then_reset_and_clear_default(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());
        $this->save(['hero_title' => 'Judul Kustom']);

        $this->post(route('admin.pages.default.set', 'home'))->assertSessionHas('success');
        $page = Page::where('slug', 'home')->first();
        $this->assertNull($page->content);
        $this->assertSame(['hero_title' => 'Judul Kustom'], $page->defaults);
        $this->get(route('home'))->assertSee('Judul Kustom');

        // Perubahan baru lalu reset: kembali ke default kustom, bukan teks bawaan.
        $this->save(['hero_title' => 'Sementara']);
        $this->delete(route('admin.pages.reset', 'home'));
        $this->get(route('home'))->assertSee('Judul Kustom')->assertDontSee('Sementara');

        // Menghapus default kustom tidak mengubah tampilan website.
        $this->delete(route('admin.pages.default.clear', 'home'))->assertSessionHas('success');
        $page->refresh();
        $this->assertNull($page->defaults);
        $this->assertSame(['hero_title' => 'Judul Kustom'], $page->content);
        $this->get(route('home'))->assertSee('Judul Kustom');
    }

    public function test_default_images_are_kept_until_no_longer_referenced(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        $this->save([], ['image_hero_image' => UploadedFile::fake()->image('hero.jpg', 800, 400)]);
        $path = Page::where('slug', 'home')->first()->content['hero_image'];

        // Dijadikan default: file masih dipakai walau isi tersimpan dikosongkan.
        $this->post(route('admin.pages.default.set', 'home'));
        Storage::disk('public')->assertExists($path);
        $this->delete(route('admin.pages.reset', 'home'));
        Storage::disk('public')->assertExists($path);

        // Default kustom dihapus tanpa isi tersimpan: gambar dipindah ke isi tersimpan, file tetap ada.
        $this->delete(route('admin.pages.default.clear', 'home'));
        Storage::disk('public')->assertExists($path);

        $this->delete(route('admin.pages.reset', 'home'));
        Storage::disk('public')->assertMissing($path);
    }

    public function test_preview_shows_only_the_requested_section_with_unsaved_text_and_saves_nothing(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $response = $this->post(route('admin.pages.preview', ['page' => 'home', 'section' => 'about']), $this->payload(['about_eyebrow' => 'Pratinjau Sementara']));

        $response->assertOk()->assertSee('Pratinjau Sementara')->assertSee('data-cms-section="about"', false)->assertSee('data-cms-section="hero"', false);
        $this->assertNull(Page::where('slug', 'home')->first());

        // Tanpa pratinjau, penanda bagian tidak ada di halaman publik.
        $this->get(route('home'))->assertDontSee('data-cms-section', false)->assertDontSee('Pratinjau Sementara');
    }

    public function test_preview_of_unknown_section_is_not_found(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.pages.preview', ['page' => 'home', 'section' => 'tidak-ada']))->assertNotFound();
    }

    public function test_seo_description_goes_into_the_meta_tag(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->save(['seo_description' => 'Deskripsi khusus mesin pencari.']);

        $this->get(route('home'))->assertSee('<meta name="description" content="Deskripsi khusus mesin pencari.">', false);
    }

    public function test_changes_are_recorded_in_the_activity_log_by_section(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create(['name' => 'Budi Admin']));

        $this->save(['hero_title' => 'Judul Baru', 'about_button' => 'Hubungi']);

        $log = ActivityLog::latest('id')->first();
        $this->assertSame(Page::class, $log->subject_type);
        $this->assertSame('Beranda', $log->subject_label);
        $this->assertEqualsCanonicalizing(['Bagian utama', 'Profil singkat'], array_keys($log->changes));

        $this->get(route('admin.activity-logs.index', ['menu' => 'Isi Halaman']))
            ->assertOk()->assertSee('Mengubah isi halaman &quot;Beranda&quot;', false)->assertSee('Yang diubah: Bagian utama, Profil singkat');

        // Menyimpan tanpa perubahan tidak mencatat apa pun.
        $count = ActivityLog::count();
        $this->save(['hero_title' => 'Judul Baru', 'about_button' => 'Hubungi']);
        $this->assertSame($count, ActivityLog::count());
    }

    public function test_sidebar_links_to_the_home_page_editor(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.dashboard'))->assertSee(route('admin.pages.edit', 'home'), false);
    }
}
