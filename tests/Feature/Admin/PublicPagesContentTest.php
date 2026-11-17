<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/** Halaman publik selain Beranda yang isinya diatur lewat Isi Halaman: Tentang Kami, Layanan, Galeri, dan Kontak. */
class PublicPagesContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SiteContentSeeder::class);
    }

    /** @return array<string, array{0: string, 1: string, 2: string, 3: string, 4: string}> slug, route publik, kunci teks, bagian, kunci deskripsi SEO */
    public static function pages(): array
    {
        return [
            'Tentang Kami' => ['about', 'about', 'hero_title', 'hero', 'seo_description'],
            'Layanan' => ['services', 'services', 'scope_title', 'scope', 'seo_description'],
            'Galeri' => ['gallery', 'gallery', 'photos_title', 'photos', 'seo_description'],
            'Kontak' => ['contact', 'contact', 'info_title', 'info', 'seo_description'],
        ];
    }

    /** Isian form lengkap dengan teks bawaan halaman; $overrides menimpa kunci tertentu. */
    private function payload(string $slug, array $overrides = []): array
    {
        $content = [];

        foreach (config("page_contents.{$slug}.sections") as $section) {
            foreach ($section['fields'] as $key => $field) {
                if (($field['type'] ?? 'text') !== 'image') {
                    $content[$key] = $field['default'];
                }
            }
        }

        return ['content' => [...$content, ...$overrides]];
    }

    private function save(string $slug, array $overrides = [], array $extra = [])
    {
        return $this->put(route('admin.pages.update', $slug), [...$this->payload($slug, $overrides), ...$extra]);
    }

    #[DataProvider('pages')]
    public function test_defaults_render_and_the_editor_opens(string $slug, string $route): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->get(route($route))->assertOk()->assertDontSee('data-cms-section', false);
        $this->get(route('admin.pages.edit', $slug))->assertOk()->assertSee('Pratinjau di website');
    }

    #[DataProvider('pages')]
    public function test_changed_text_and_seo_description_reach_the_public_page(string $slug, string $route, string $key, string $section, string $seoKey): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->save($slug, [$key => 'Teks Baru Uji', $seoKey => 'Deskripsi mesin pencari baru.'])->assertSessionHasNoErrors();

        $this->get(route($route))->assertOk()->assertSee('Teks Baru Uji')
            ->assertSee('<meta name="description" content="Deskripsi mesin pencari baru.">', false);
        $this->assertSame('Teks Baru Uji', Page::where('slug', $slug)->first()->content[$key]);
    }

    #[DataProvider('pages')]
    public function test_preview_shows_the_section_with_unsaved_text_and_saves_nothing(string $slug, string $route, string $key, string $section): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.pages.preview', ['page' => $slug, 'section' => $section]), $this->payload($slug, [$key => 'Pratinjau Sementara']))
            ->assertOk()->assertSee('Pratinjau Sementara')->assertSee('data-cms-section="'.$section.'"', false);

        $this->assertNull(Page::where('slug', $slug)->first());
        $this->get(route($route))->assertDontSee('Pratinjau Sementara');
        $this->post(route('admin.pages.preview', ['page' => $slug, 'section' => 'seo']))->assertNotFound();
    }

    public function test_about_page_cards_and_optional_paragraphs(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->save('about', [
            'mission_items' => [['title' => 'Misi Satu', 'text' => 'Uraian misi satu.']],
            'facts_items' => [['title' => 'Berdiri', 'text' => '2000'], ['title' => 'Kota', 'text' => 'Denpasar']],
            'profile_paragraph_2' => '',
            'profile_title' => 'Perusahaan *Terpercaya* sejak lama',
        ])->assertSessionHasNoErrors();

        $html = $this->get(route('about'))->assertOk()->assertSee('Misi Satu')->assertSee('Uraian misi satu.')->assertSee('Denpasar')
            ->assertSee('Perusahaan <span class="text-brand">Terpercaya</span> sejak lama', false)->getContent();

        // Paragraf 2 dikosongkan: teks bawaannya tidak tampil, paragraf 1 dan 3 tetap.
        $this->assertStringNotContainsString(config('page_contents.about.sections.profile.fields.profile_paragraph_2.default'), $html);
        $this->assertStringContainsString(config('page_contents.about.sections.profile.fields.profile_paragraph_3.default'), $html);

        $this->save('about', ['values_items' => array_fill(0, 6, ['title' => 'A', 'text' => 'B'])])->assertSessionHasErrors('content.values_items');
    }

    public function test_hero_headings_use_one_coloured_title_field_with_a_red_second_line_by_default(): void
    {
        $this->withoutVite();

        foreach ([
            'about' => ['about', 'Mengenal', 'PT. Baliyoni Saguna'],
            'services' => ['services', 'Solusi Lengkap untuk', 'Setiap Kebutuhan'],
            'gallery' => ['gallery', 'Jejak Visual', 'Baliyoni Saguna'],
            'contact' => ['contact', 'Wujudkan Kebutuhan', 'Bersama Kami'],
        ] as [$route, $white, $red]) {
            $html = $this->get(route($route))->assertOk()->getContent();

            $this->assertStringContainsString($white, $html);
            $this->assertStringContainsString('<span class="text-brand-bright">'.$red.'</span>', $html);
        }

        $this->assertArrayNotHasKey('hero_highlight', config('page_contents.about.sections.hero.fields'));
    }

    public function test_admin_can_recolour_a_hero_heading_and_the_motto(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->save('about', ['hero_title' => "*Kenali* kami\nlebih dekat", 'motto_text' => "Moto *baru*\nsatu baris"])->assertSessionHasNoErrors();

        $html = $this->get(route('about'))->getContent();
        $this->assertStringContainsString('<span class="text-brand-bright">Kenali</span> kami<br', $html);
        $this->assertStringContainsString('Moto <span class="text-brand">baru</span><br', $html);

        // Tanda * harus berpasangan, juga pada judul hero.
        $this->save('contact', ['hero_title' => "Wujudkan *Kebutuhan\nBersama Kami"])->assertSessionHasErrors('content.hero_title');
    }

    public function test_services_hero_label_keeps_its_coloured_ampersand_and_can_be_changed(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->get(route('services'))->assertSee('Layanan <span class="text-brand-bright">&amp;</span> Produk', false);

        $this->save('services', ['hero_label' => '*Layanan* Kami', 'scope_hint' => 'Silakan pilih kategori.']);
        $this->get(route('services'))->assertSee('<span class="text-brand-bright">Layanan</span> Kami', false)->assertSee('Silakan pilih kategori.');
    }

    public function test_hero_photo_can_be_replaced_and_reverted_on_each_page_with_one(): void
    {
        Storage::fake('public');
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        foreach ([['about', 'about'], ['services', 'services'], ['contact', 'contact']] as [$slug, $route]) {
            $this->save($slug, [], ['image_hero_image' => UploadedFile::fake()->image('latar.jpg', 1600, 800)])->assertSessionHasNoErrors();
            $path = Page::where('slug', $slug)->first()->content['hero_image'];
            Storage::disk('public')->assertExists($path);
            $this->get(route($route))->assertSee(Storage::disk('public')->url($path), false);

            $this->save($slug, [], ['remove_image_hero_image' => '1']);
            Storage::disk('public')->assertMissing($path);
        }
    }

    public function test_contact_map_button_and_section_follow_the_address(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->get(route('contact'))->assertSee('Lihat Lokasi')->assertSee('Buka di Google Maps');

        $this->save('general', ['address' => ''])->assertSessionHasNoErrors();
        $this->get(route('contact'))->assertDontSee('Lihat Lokasi')->assertDontSee('Buka di Google Maps');
    }

    public function test_sidebar_links_to_every_page_editor(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $html = $this->get(route('admin.dashboard'))->getContent();

        foreach (['home', 'about', 'services', 'gallery', 'contact', 'general'] as $slug) {
            $this->assertStringContainsString(route('admin.pages.edit', $slug), $html);
        }
    }
}
