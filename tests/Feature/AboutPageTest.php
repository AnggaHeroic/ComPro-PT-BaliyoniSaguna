<?php

namespace Tests\Feature;

use Tests\TestCase;

class AboutPageTest extends TestCase
{
    public function test_about_page_shows_profile_vision_and_mission(): void
    {
        $this->withoutVite();

        $response = $this->get(route('about'));

        $response->assertOk();
        $response->assertSee('id="profil"', false);
        $response->assertSee('id="visi-misi"', false);
        $response->assertSee(config('company.about.vision'));

        foreach (config('company.about.mission') as $item) {
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
