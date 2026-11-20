<?php

namespace Tests\Feature\Admin;

use App\Models\BusinessActivity;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityGroupedReorderTest extends TestCase
{
    use RefreshDatabase;

    private ServiceCategory $kantor;

    private ServiceCategory $perkakas;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->actingAs(User::factory()->create());
        $this->kantor = $this->category('Peralatan Kantor', 1);
        $this->perkakas = $this->category('Perkakas', 2);
    }

    private function category(string $name, int $order): ServiceCategory
    {
        return ServiceCategory::create(['name' => $name, 'slug' => str($name)->slug()->toString(), 'icon' => 'office', 'sort_order' => $order, 'is_published' => true]);
    }

    private function activity(string $name, ServiceCategory $category, int $order): BusinessActivity
    {
        return BusinessActivity::create(['service_category_id' => $category->id, 'name' => $name, 'slug' => str($name)->slug()->toString(), 'sort_order' => $order]);
    }

    public function test_reorder_mode_is_available_without_choosing_a_category_and_is_split_per_category(): void
    {
        $this->activity('Kursi', $this->kantor, 1);
        $this->activity('Meja', $this->kantor, 2);
        $this->activity('Obeng', $this->perkakas, 1);

        $html = $this->get(route('admin.activities.index', ['reorder' => 1]))->assertOk()->assertSee('x-data="reorderList"', false)->getContent();

        $this->assertStringContainsString('aria-label="Kategori Peralatan Kantor"', $html);
        $this->assertStringContainsString('aria-label="Kategori Perkakas"', $html);
        $this->assertSame(3, substr_count($html, 'data-group='));
        $this->assertStringContainsString('data-group="'.$this->kantor->id.'"', $html);
        $this->assertStringContainsString('data-group="'.$this->perkakas->id.'"', $html);
        $this->assertStringContainsString('Urutan hanya bisa diubah di dalam kategorinya masing-masing', $html);
    }

    public function test_reorder_mode_can_be_limited_to_one_category_and_hides_empty_categories(): void
    {
        $this->activity('Kursi', $this->kantor, 1);
        $this->activity('Meja', $this->kantor, 2);
        $this->category('Kosong', 3);

        $html = $this->get(route('admin.activities.index', ['reorder' => 1, 'category' => $this->kantor->id]))->getContent();

        $this->assertStringContainsString('aria-label="Kategori Peralatan Kantor"', $html);
        $this->assertStringNotContainsString('aria-label="Kategori Kosong"', $html);
        $this->assertStringNotContainsString('aria-label="Kategori Perkakas"', $html);
    }

    public function test_reorder_toggle_needs_at_least_two_activities_and_no_filters(): void
    {
        $this->activity('Satu', $this->kantor, 1);

        $this->get(route('admin.activities.index'))->assertSee('aria-disabled="true"', false);
        $this->get(route('admin.activities.index', ['reorder' => 1]))->assertDontSee('x-data="reorderList"', false);

        $this->activity('Dua', $this->perkakas, 1);

        $this->get(route('admin.activities.index'))->assertSee('role="switch"', false);
        $this->get(route('admin.activities.index', ['reorder' => 1, 'q' => 'Satu']))->assertDontSee('x-data="reorderList"', false);
    }

    public function test_several_categories_are_saved_at_once(): void
    {
        $k1 = $this->activity('K1', $this->kantor, 1);
        $k2 = $this->activity('K2', $this->kantor, 2);
        $p1 = $this->activity('P1', $this->perkakas, 1);
        $p2 = $this->activity('P2', $this->perkakas, 2);

        $this->put(route('admin.activities.reorder'), ['orders' => [
            $this->kantor->id => [$k2->id, $k1->id],
            $this->perkakas->id => [$p2->id, $p1->id],
        ]])->assertRedirect(route('admin.activities.index'))->assertSessionHas('success');

        $this->assertSame([1, 2, 1, 2], [$k2->fresh()->sort_order, $k1->fresh()->sort_order, $p2->fresh()->sort_order, $p1->fresh()->sort_order]);
    }

    public function test_an_activity_cannot_be_moved_into_another_category_by_reordering(): void
    {
        $k1 = $this->activity('K1', $this->kantor, 1);
        $k2 = $this->activity('K2', $this->kantor, 2);
        $p1 = $this->activity('P1', $this->perkakas, 1);

        $this->put(route('admin.activities.reorder'), ['orders' => [$this->kantor->id => [$k1->id, $k2->id, $p1->id]]])->assertSessionHas('error');

        $this->assertSame([1, 2], [$k1->fresh()->sort_order, $k2->fresh()->sort_order]);
        $this->assertSame($this->perkakas->id, $p1->fresh()->service_category_id);
        $this->assertSame(1, $p1->fresh()->sort_order);
    }

    public function test_a_failing_category_leaves_every_category_unchanged(): void
    {
        $k1 = $this->activity('K1', $this->kantor, 1);
        $k2 = $this->activity('K2', $this->kantor, 2);
        $p1 = $this->activity('P1', $this->perkakas, 1);
        $p2 = $this->activity('P2', $this->perkakas, 2);

        $this->put(route('admin.activities.reorder'), ['orders' => [
            $this->kantor->id => [$k2->id, $k1->id],
            $this->perkakas->id => [$p1->id],
        ]])->assertSessionHas('error');

        $this->assertSame([1, 2, 1, 2], [$k1->fresh()->sort_order, $k2->fresh()->sort_order, $p1->fresh()->sort_order, $p2->fresh()->sort_order]);
    }

    public function test_invalid_reorder_payload_is_rejected(): void
    {
        $this->put(route('admin.activities.reorder'), [])->assertSessionHasErrors('orders');
        $this->put(route('admin.activities.reorder'), ['orders' => [$this->kantor->id => ['x']]])->assertSessionHasErrors('orders.'.$this->kantor->id.'.0');
    }
}
