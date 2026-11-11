<?php

namespace Tests\Feature;

use Tests\TestCase;

class ServicePageTest extends TestCase
{
    public function test_service_page_lists_every_service_category_with_its_activities(): void
    {
        $this->withoutVite();

        $response = $this->get(route('services'));

        $response->assertOk();
        $response->assertSee('id="ruang-lingkup"', false);
        $response->assertSee('Pilih salah satu kategori');
        $response->assertSee('role="dialog"', false);
        $response->assertSee('Lihat detail');

        foreach (config('company.services') as $service) {
            $response->assertSee($service['name']);

            foreach ($service['activities'] as $activity) {
                $response->assertSee($activity['name']);
            }
        }
    }

    public function test_every_service_category_has_five_placeholder_activities(): void
    {
        foreach (config('company.services') as $service) {
            $this->assertCount(5, $service['activities'], $service['name']);
        }
    }

    public function test_about_page_no_longer_shows_service_scope(): void
    {
        $this->withoutVite();

        $this->get(route('about'))->assertDontSee('id="ruang-lingkup"', false);
    }
}
