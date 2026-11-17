<?php

namespace Tests\Feature\Admin;

use App\Models\BusinessActivity;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessActivityTest extends TestCase
{
    use RefreshDatabase;

    private ServiceCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->actingAs(User::factory()->create());
        $this->category = $this->category('Peralatan Kantor');
    }

    private function category(string $name): ServiceCategory
    {
        return ServiceCategory::create(['name' => $name, 'slug' => str($name)->slug()->toString(), 'icon' => 'office', 'sort_order' => 1, 'is_published' => true]);
    }

    private function activity(string $name, ?ServiceCategory $category = null, int $order = 1, bool $published = true): BusinessActivity
    {
        return BusinessActivity::create([
            'service_category_id' => ($category ?? $this->category)->id,
            'name' => $name, 'slug' => str($name)->slug()->toString(), 'sort_order' => $order, 'is_published' => $published,
        ]);
    }

    private function payload(array $override = []): array
    {
        return ['service_category_id' => $this->category->id, 'name' => 'Penjualan furnitur', 'status' => 'published', ...$override];
    }

    public function test_guest_is_redirected_to_login(): void
    {
        auth()->logout();

        $this->get(route('admin.activities.index'))->assertRedirect(route('login'));
    }

    public function test_index_filters_by_category_status_and_search(): void
    {
        $other = $this->category('Perkakas');
        $this->activity('Kursi kantor');
        $this->activity('Obeng', $other);
        $this->activity('Draft saja', null, 2, false);

        $this->get(route('admin.activities.index', ['category' => $this->category->id]))->assertSee('Kursi kantor')->assertDontSee('Obeng');
        $this->get(route('admin.activities.index', ['status' => 'draft']))->assertSee('Draft saja')->assertDontSee('Kursi kantor');
        $this->get(route('admin.activities.index', ['q' => 'obeng']))->assertSee('Obeng')->assertDontSee('Kursi kantor');
    }

    public function test_store_creates_activity_with_slug_and_next_order_in_its_category(): void
    {
        $this->activity('Satu', null, 3);

        $this->post(route('admin.activities.store'), $this->payload(['summary' => 'Ringkas', 'detail' => "Paragraf satu.\n\nParagraf dua."]))
            ->assertRedirect(route('admin.activities.index', ['category' => $this->category->id]));

        $this->assertDatabaseHas('business_activities', ['name' => 'Penjualan furnitur', 'slug' => 'penjualan-furnitur', 'sort_order' => 4, 'summary' => 'Ringkas']);
    }

    public function test_the_same_name_gets_a_unique_slug_within_a_category_only(): void
    {
        $other = $this->category('Perkakas');

        $this->post(route('admin.activities.store'), $this->payload());
        $this->post(route('admin.activities.store'), $this->payload());
        $this->post(route('admin.activities.store'), $this->payload(['service_category_id' => $other->id]));

        $this->assertEqualsCanonicalizing(['penjualan-furnitur', 'penjualan-furnitur-2'], $this->category->activities()->pluck('slug')->all());
        $this->assertSame(['penjualan-furnitur'], $other->activities()->pluck('slug')->all());
    }

    public function test_store_validates_input(): void
    {
        $this->post(route('admin.activities.store'), ['service_category_id' => 999, 'name' => '', 'status' => 'x'])
            ->assertSessionHasErrors(['service_category_id', 'name', 'status']);
    }

    public function test_moving_to_another_category_appends_and_resolves_slug_clash(): void
    {
        $other = $this->category('Perkakas');
        $this->activity('Obeng', $other, 1);
        $moving = $this->activity('Obeng', $this->category, 1);

        $this->put(route('admin.activities.update', $moving), $this->payload(['name' => 'Obeng', 'service_category_id' => $other->id]));

        $moving->refresh();
        $this->assertSame($other->id, $moving->service_category_id);
        $this->assertSame(2, $moving->sort_order);
        $this->assertSame('obeng-2', $moving->slug);
    }

    public function test_activity_can_be_deleted(): void
    {
        $activity = $this->activity('Hapus aku');

        $this->delete(route('admin.activities.destroy', $activity))->assertRedirect(route('admin.activities.index', ['category' => $this->category->id]));

        $this->assertModelMissing($activity);
    }

    public function test_reorder_applies_within_one_category(): void
    {
        $a = $this->activity('A', null, 1);
        $b = $this->activity('B', null, 2);

        $this->put(route('admin.activities.reorder'), ['orders' => [$this->category->id => [$b->id, $a->id]]])->assertSessionHas('success');

        $this->assertSame([1, 2], [$b->fresh()->sort_order, $a->fresh()->sort_order]);
    }

    public function test_public_detail_page_shows_paragraphs_and_hides_drafts(): void
    {
        $shown = $this->activity('Terbit', null, 1);
        $shown->update(['detail' => "Isi paragraf pertama.\n\nIsi paragraf kedua."]);
        $this->activity('Rahasia', null, 2, false);

        $this->get(route('services.show', ['service' => $this->category->slug, 'activity' => 'terbit']))
            ->assertOk()->assertSee('Isi paragraf pertama.')->assertSee('Isi paragraf kedua.');
        $this->get(route('services.show', ['service' => $this->category->slug, 'activity' => 'rahasia']))->assertNotFound();
    }
}
