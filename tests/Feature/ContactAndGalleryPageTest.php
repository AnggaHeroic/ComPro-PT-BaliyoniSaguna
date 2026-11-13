<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\TestCase;

class ContactAndGalleryPageTest extends TestCase
{
    public function test_contact_page_shows_company_contact_details_and_form(): void
    {
        $this->withoutVite();

        $response = $this->get(route('contact'));

        $response->assertOk();
        $response->assertSee(config('company.email'));
        $response->assertSee(config('company.address'));
        $response->assertSee('Kirim lewat WhatsApp');
        $response->assertSee('Kirim lewat Email');
        $response->assertSee('Isi salah satu: email atau nomor telepon');
        $response->assertSee('id="kontak-email"', false);
        $response->assertSee('id="kontak-telepon"', false);
        $response->assertSee('Buka di Google Maps');
    }

    public function test_contact_page_prefills_subject_from_query(): void
    {
        $this->withoutVite();

        $this->get(route('contact', ['perihal' => 'AC Split Wall']))
            ->assertOk()
            ->assertSee('AC Split Wall');
    }

    public function test_gallery_page_lists_every_photo(): void
    {
        $this->withoutVite();

        $response = $this->get(route('gallery'));

        $response->assertOk();
        $response->assertSee('role="dialog"', false);

        $response->assertSee('Pratinjau foto galeri');

        foreach (config('company.gallery') as $photo) {
            $response->assertSee($photo['title']);
            $response->assertSee($photo['description']);
        }
    }

    public function test_activity_cards_link_to_their_detail_pages(): void
    {
        $this->withoutVite();

        $service = config('company.services')[0];
        $url = route('services.show', ['service' => Str::slug($service['name']), 'activity' => Str::slug($service['activities'][0]['name'])]);

        $this->get(route('services'))->assertSee($url, false);
    }

    public function test_activity_detail_page_shows_activity_and_contact_link(): void
    {
        $this->withoutVite();

        $service = config('company.services')[0];
        $activity = $service['activities'][1];

        $this->get(route('services.show', ['service' => Str::slug($service['name']), 'activity' => Str::slug($activity['name'])]))
            ->assertOk()
            ->assertSee($activity['name'])
            ->assertSee($service['activities'][0]['name'])
            ->assertSee($service['activities'][2]['name'])
            ->assertSee(route('contact', ['perihal' => $activity['name']]), false)
            ->assertSee(route('services', ['kategori' => Str::slug($service['name'])]).'#ruang-lingkup', false);
    }

    public function test_activity_detail_page_returns_404_for_unknown_activity(): void
    {
        $service = config('company.services')[0];

        $this->get(route('services.show', ['service' => Str::slug($service['name']), 'activity' => 'tidak-ada']))->assertNotFound();
        $this->get(route('services.show', ['service' => 'tidak-ada', 'activity' => 'tidak-ada']))->assertNotFound();
    }

    public function test_every_activity_detail_page_renders_three_description_paragraphs(): void
    {
        $this->withoutVite();

        foreach (config('company.services') as $service) {
            foreach ($service['activities'] as $activity) {
                $response = $this->get(route('services.show', ['service' => Str::slug($service['name']), 'activity' => Str::slug($activity['name'])]));

                $response->assertOk();
                $response->assertSee($activity['name'].' merupakan salah satu aktivitas usaha', false);
                $response->assertSee(config('company.activity_placeholder')[2]);
            }
        }
    }
}
