<?php

namespace Tests\Feature;

use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    // Halaman publik membaca kategori dan aktivitas dari database.
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SiteContentSeeder::class);
    }

    public function test_about_page_shows_profile_vision_and_mission(): void
    {
        $this->withoutVite();

        $response = $this->get(route('about'));

        $response->assertOk();
        $response->assertSee('id="profil"', false);
        $response->assertSee('id="visi-misi"', false);
        $response->assertSee(config('page_contents.about.sections.direction.fields.vision_text.default'));

        foreach (config('page_contents.about.sections.direction.fields.mission_items.default') as $item) {
            $response->assertSee($item['title']);
            $response->assertSee($item['text']);
        }
    }

    public function test_navigation_links_to_about_page(): void
    {
        $this->withoutVite();

        $this->get(route('home'))->assertSee(route('about', absolute: false), false);
    }
}
