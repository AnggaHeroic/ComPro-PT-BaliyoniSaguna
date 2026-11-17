<?php

namespace Tests\Feature\Admin;

use App\Models\Page;
use App\Models\User;
use App\Services\CompanyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GeneralSettingsTest extends TestCase
{
    use RefreshDatabase;

    /** Isian form Umum lengkap dengan teks bawaan; $overrides menimpa kunci tertentu. */
    private function payload(array $overrides = []): array
    {
        $content = [];

        foreach (config('page_contents.general.sections') as $section) {
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
        return $this->put(route('admin.pages.update', 'general'), [...$this->payload($overrides), ...$extra]);
    }

    public function test_guest_is_redirected_and_sidebar_links_to_general(): void
    {
        $this->get(route('admin.pages.edit', 'general'))->assertRedirect(route('login'));

        $this->withoutVite();
        $this->actingAs(User::factory()->create());
        $this->get(route('admin.dashboard'))->assertSee(route('admin.pages.edit', 'general'), false)->assertSee('Umum');
    }

    public function test_edit_page_shows_the_sections(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->get(route('admin.pages.edit', 'general'))
            ->assertOk()->assertSee('Identitas')->assertSee('Kontak')->assertSee('Footer')
            ->assertSee('PT. Bal*i*yoni Saguna', false);
    }

    public function test_defaults_keep_the_original_contact_data_and_wordmark(): void
    {
        $company = app(CompanyService::class)->profile();

        $this->assertSame('PT Baliyoni Saguna', $company['name']);
        $this->assertSame('+62361257055', $company['phone_href']);
        $this->assertSame('https://wa.me/6282272269888', $company['mobile_url']);
        $this->assertSame('info@baliyoni.co.id', $company['email']);
        $this->assertSame('PT. Bal<span class="text-brand-bright">i</span>yoni Saguna', (string) app(CompanyService::class)->brand()['wordmark']);
        $this->assertSame(asset('images/logo.png'), app(CompanyService::class)->brand()['logo_url']);
    }

    public function test_changed_contact_appears_in_footer_cta_and_contact_page(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->save(['email' => 'halo@contoh.id', 'phone' => '0812 3456 789', 'mobile' => '+62 811-222-333', 'address' => 'Jl. Baru No. 5, Denpasar'])
            ->assertSessionHasNoErrors();

        $company = app(CompanyService::class)->profile();
        $this->assertSame('+62812345678'.'9', $company['phone_href']);
        $this->assertSame('https://wa.me/62811222333', $company['mobile_url']);

        $this->get(route('home'))->assertSee('mailto:halo@contoh.id', false)->assertSee('tel:+628123456789', false)->assertSee('https://wa.me/62811222333', false)->assertSee('Jl. Baru No. 5, Denpasar');
        $this->get(route('contact'))->assertSee('halo@contoh.id')->assertSee('Jl. Baru No. 5, Denpasar');
    }

    public function test_blank_contact_fields_are_hidden(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->save(['email' => '', 'phone' => '', 'mobile' => '', 'address' => ''])->assertSessionHasNoErrors();

        $company = app(CompanyService::class)->profile();
        $this->assertFalse($company['has_contact']);
        $this->assertNull($company['phone_href']);
        $this->assertNull($company['mobile_url']);
        $this->get(route('home'))->assertOk()->assertDontSee('mailto:info@baliyoni.co.id', false)->assertDontSee('wa.me/6282272269888', false);
    }

    public function test_contact_fields_are_validated(): void
    {
        $this->actingAs(User::factory()->create());

        $this->save(['email' => 'bukan-email'])->assertSessionHasErrors('content.email');
        $this->save(['phone' => 'telepon kantor'])->assertSessionHasErrors('content.phone');
        $this->save(['site_name' => ''])->assertSessionHasErrors('content.site_name');
        $this->save(['brand_text' => 'PT *Baliyoni'])->assertSessionHasErrors('content.brand_text');
        $this->assertNull(Page::where('slug', 'general')->first()?->content);
    }

    public function test_site_name_and_tagline_reach_the_page_title_and_description(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->save(['site_name' => 'PT Nama Baru', 'tagline' => 'Slogan baru kami.']);

        $this->get(route('about'))->assertSee('<title>Tentang Kami — PT Nama Baru</title>', false);
        $this->post(route('logout'));
        $this->get(route('login'))->assertDontSee('PT Baliyoni Saguna</title>', false)->assertSee('| PT Nama Baru</title>', false);
    }

    public function test_wordmark_and_logo_are_used_in_navbar_footer_login_and_sidebar(): void
    {
        Storage::fake('public');
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->save(['brand_text' => '*Baliyoni* Group'], ['image_logo' => UploadedFile::fake()->image('logo.png', 400, 400)])->assertSessionHasNoErrors();

        $logo = Storage::disk('public')->url(Page::where('slug', 'general')->first()->content['logo']);

        foreach ([route('home'), route('admin.dashboard')] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertStringContainsString('<span class="text-brand-bright">Baliyoni</span> Group', $html);
            $this->assertStringContainsString($logo, $html);
        }

        $this->post(route('logout'));
        $this->get(route('login'))->assertSee($logo, false)->assertSee('<span class="text-brand-bright">Baliyoni</span> Group', false);
    }

    public function test_logo_can_be_reverted_to_the_bundled_one(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());

        $this->save([], ['image_logo' => UploadedFile::fake()->image('logo.png', 300, 300)]);
        $path = Page::where('slug', 'general')->first()->content['logo'];
        Storage::disk('public')->assertExists($path);

        $this->save([], ['remove_image_logo' => '1']);
        Storage::disk('public')->assertMissing($path);
        $this->assertSame(asset('images/logo.png'), app(CompanyService::class)->brand()['logo_url']);
    }

    public function test_each_cta_variant_has_its_own_text(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->save([
            'cta_card_eyebrow' => 'Label Kartu', 'cta_card_title' => 'Judul kartu *merah*', 'cta_card_button' => 'Tombol Kartu',
            'cta_banner_eyebrow' => 'Label Banner', 'cta_banner_title' => 'Judul banner *merah*', 'cta_banner_button_phone' => 'Telepon Banner',
        ])->assertSessionHasNoErrors();

        // Beranda memakai kartu, Tentang Kami memakai banner.
        $this->get(route('home'))->assertSee('Label Kartu')->assertSee('Judul kartu <span class="text-brand">merah</span>', false)->assertSee('Tombol Kartu')
            ->assertDontSee('Label Banner')->assertSee('Telepon Kami');
        $this->get(route('about'))->assertSee('Label Banner')->assertSee('Judul banner <span class="whitespace-nowrap text-brand-bright">merah</span>', false)
            ->assertSee('Telepon Banner')->assertDontSee('Label Kartu')->assertSee('Kontak Kami');
    }

    public function test_cta_summary_adapts_when_only_one_phone_number_is_filled(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        // Hanya WhatsApp: tombol telepon hilang dan labelnya menjadi WhatsApp.
        $this->save(['phone' => ''])->assertSessionHasNoErrors();
        $this->get(route('home'))->assertSee('lg:grid-cols-3', false)->assertSee('WA. 0822 7226 9888')->assertDontSee('Telp.')->assertDontSee('Telepon Kami')
            ->assertSee('>WhatsApp</p>', false);
        $this->get(route('contact'))->assertSee('>WhatsApp</p>', false)->assertDontSee('Telepon dan WhatsApp');

        // Kelompok kontak yang kosong tidak menyisakan kolom: tersisa dua, lalu satu, selalu mengisi lebar penuh.
        $this->save(['phone' => '', 'email' => ''])->assertSessionHasNoErrors();
        $this->get(route('home'))->assertSee('lg:grid-cols-2', false)->assertDontSee('lg:grid-cols-3', false);

        $this->save(['phone' => '', 'email' => '', 'address' => ''])->assertSessionHasNoErrors();
        $this->get(route('home'))->assertSee('lg:grid-cols-1', false)->assertSee('WA. 0822 7226 9888');

        // Hanya telepon: label tetap Telepon, tanpa baris WA.
        $this->save(['mobile' => ''])->assertSessionHasNoErrors();
        $this->get(route('contact'))->assertSee('>Telepon</p>', false)->assertDontSee('Telepon dan WhatsApp');
    }

    public function test_banner_cta_background_photo_can_be_changed_and_reverted(): void
    {
        Storage::fake('public');
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $this->get(route('about'))->assertSee('photo-1512403754473-27835f7b9984', false);

        $this->save([], ['image_cta_banner_image' => UploadedFile::fake()->image('latar.jpg', 1600, 600)])->assertSessionHasNoErrors();
        $path = Page::where('slug', 'general')->first()->content['cta_banner_image'];
        Storage::disk('public')->assertExists($path);

        $this->get(route('about'))->assertSee(Storage::disk('public')->url($path), false)->assertDontSee('photo-1512403754473-27835f7b9984', false);
        // Kartu tidak berlatar foto, jadi Beranda tidak ikut berubah.
        $this->get(route('home'))->assertDontSee(Storage::disk('public')->url($path), false);

        $this->save([], ['remove_image_cta_banner_image' => '1'])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($path);
        $this->get(route('about'))->assertSee('photo-1512403754473-27835f7b9984', false);
    }

    public function test_cta_is_one_section_split_into_two_variations_with_a_preview_each(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $html = $this->get(route('admin.pages.edit', 'general'))->assertOk()->assertSee('Ajakan menghubungi')->assertSee('Variasi 1: Kartu')->assertSee('Variasi 2: Banner')->getContent();

        $html = str_replace('\/', '/', $html); // URL di x-data ditulis dengan garis miring ter-escape
        $this->assertStringNotContainsString('Ajakan: kartu', $html);
        $this->assertSame(2, substr_count($html, 'Pratinjau di website'));
        $this->assertStringContainsString(route('admin.pages.preview', ['page' => 'general', 'section' => 'cta_card']), $html);
        $this->assertStringContainsString(route('admin.pages.preview', ['page' => 'general', 'section' => 'cta_banner']), $html);
    }

    public function test_cta_preview_shows_one_variation_with_unsaved_text_and_saves_nothing(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create());

        $card = $this->post(route('admin.pages.preview', ['page' => 'general', 'section' => 'cta_card']), $this->payload(['cta_card_eyebrow' => 'Kartu Sementara', 'cta_banner_eyebrow' => 'Banner Sementara', 'email' => 'sementara@contoh.id']));
        $card->assertOk()->assertSee('Kartu Sementara')->assertSee('Banner Sementara')->assertSee('sementara@contoh.id')
            ->assertSee('data-cms-section="cta_card"', false)->assertSee('data-cms-section="cta_banner"', false);
        $this->assertNull(Page::where('slug', 'general')->first());

        // Setelah pratinjau, halaman publik kembali memakai isi yang tersimpan.
        $this->get(route('home'))->assertDontSee('Kartu Sementara')->assertDontSee('sementara@contoh.id');

        $this->post(route('admin.pages.preview', ['page' => 'general', 'section' => 'cta']))->assertNotFound();
    }

    public function test_general_has_no_preview(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.pages.preview', ['page' => 'general', 'section' => 'contact']))->assertNotFound();
    }
}
