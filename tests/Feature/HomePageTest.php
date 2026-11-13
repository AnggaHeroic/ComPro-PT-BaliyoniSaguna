<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\TestCase;

class HomePageTest extends TestCase
{
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

        $response->assertSee('Hubungi Kami');
        $response->assertSee('https://wa.me/'.ltrim(config('company.mobile_href'), '+'), false);
        $response->assertSee('tel:'.config('company.phone_href'), false);
        $response->assertSee('mailto:'.config('company.email'), false);
        $response->assertSee(config('company.description'));
        $this->assertSame(2, substr_count($response->getContent(), 'mailto:'.config('company.email')));
    }
}
