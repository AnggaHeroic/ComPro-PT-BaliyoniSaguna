<?php

namespace Tests\Feature;

use App\Services\CompanyService;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    // Halaman publik membaca kategori dan aktivitas dari database.
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SiteContentSeeder::class);
    }

    public function test_home_page_shows_company_sections(): void
    {
        $this->withoutVite();

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('One Stop Solution');
        $response->assertSee('id="tentang"', false);
        $response->assertSee('id="layanan"', false);
        $response->assertSee('id="mengapa-kami"', false);
        $response->assertSee('Kontak Kami');
    }

    public function test_home_page_lists_every_service_category(): void
    {
        $this->withoutVite();

        $response = $this->get(route('home'));

        foreach (config('company.services') as $service) {
            $response->assertSee($service['name']);
        }
    }

    public function test_home_carousel_cards_link_to_their_category_on_services_page(): void
    {
        $this->withoutVite();

        $response = $this->get(route('home'));

        foreach (config('company.services') as $service) {
            $response->assertSee(route('services').'#ruang-lingkup-'.Str::slug($service['name']), false);
        }
    }

    public function test_home_page_shows_contact_information_in_cta_and_footer(): void
    {
        $this->withoutVite();

        $response = $this->get(route('home'));

        $company = app(CompanyService::class)->profile();

        $response->assertSee('Hubungi Kami');
        $response->assertSee($company['mobile_url'], false);
        $response->assertSee('tel:'.$company['phone_href'], false);
        $response->assertSee('mailto:'.$company['email'], false);
        $response->assertSee($company['description']);
        $this->assertSame(2, substr_count($response->getContent(), 'mailto:'.$company['email']));
    }
}
