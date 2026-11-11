<?php

namespace Tests\Feature;

use Tests\TestCase;

class ServicePageTest extends TestCase
{
    public function test_service_page_lists_every_service_category(): void
    {
        $this->withoutVite();

        $response = $this->get(route('services'));

        $response->assertOk();
        $response->assertSee('id="ruang-lingkup"', false);

        foreach (config('company.services') as $service) {
            $response->assertSee($service['name']);
        }
    }

    public function test_about_page_no_longer_shows_service_scope(): void
    {
        $this->withoutVite();

        $this->get(route('about'))->assertDontSee('id="ruang-lingkup"', false);
    }
}
