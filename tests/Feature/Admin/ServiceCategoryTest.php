<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\BusinessActivity;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->actingAs(User::factory()->create());
    }

    private function category(string $name, int $order = 1, bool $published = true): ServiceCategory
    {
        return ServiceCategory::create([
            'name' => $name, 'slug' => str($name)->slug()->toString(), 'icon' => 'office',
            'sort_order' => $order, 'is_published' => $published,
        ]);
    }

    public function test_guest_cannot_open_category_pages(): void
    {
        auth()->logout();

        $this->get(route('admin.categories.index'))->assertRedirect(route('login'));
        $this->post(route('admin.categories.store'), [])->assertRedirect(route('login'));
    }

    public function test_index_lists_filters_and_searches_categories(): void
    {
        $this->category('Peralatan Kantor', 1);
        $this->category('Perkakas', 2, false);

        $this->get(route('admin.categories.index'))->assertOk()->assertSee('Peralatan Kantor')->assertSee('Perkakas');
        $this->get(route('admin.categories.index', ['status' => 'draft']))->assertSee('Perkakas')->assertDontSee('Peralatan Kantor');
        $this->get(route('admin.categories.index', ['q' => 'kantor']))->assertSee('Peralatan Kantor')->assertDontSee('Perkakas');
    }

    public function test_store_creates_category_with_slug_and_next_order(): void
    {
        $this->category('Satu', 4);

        $this->post(route('admin.categories.store'), ['name' => 'Peralatan Baru', 'icon' => 'tools', 'status' => 'published'])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('service_categories', ['name' => 'Peralatan Baru', 'slug' => 'peralatan-baru', 'sort_order' => 5, 'is_published' => true]);
    }

    public function test_slug_stays_unique_for_the_same_name(): void
    {
        foreach (range(1, 2) as $ignored) {
            $this->post(route('admin.categories.store'), ['name' => 'Sama', 'icon' => 'tools', 'status' => 'published']);
        }

        $this->assertEqualsCanonicalizing(['sama', 'sama-2'], ServiceCategory::pluck('slug')->all());
    }

    public function test_draft_is_saved_and_redirects_to_the_draft_tab(): void
    {
        $this->post(route('admin.categories.store'), ['name' => 'Rahasia', 'icon' => 'tools', 'status' => 'draft'])
            ->assertRedirect(route('admin.categories.index', ['status' => 'draft']));

        $this->assertFalse(ServiceCategory::firstWhere('name', 'Rahasia')->is_published);
    }

    public function test_store_validates_input(): void
    {
        $this->post(route('admin.categories.store'), ['name' => '', 'icon' => 'tidak-ada', 'status' => 'x'])
            ->assertSessionHasErrors(['name', 'icon', 'status']);
    }

    public function test_image_is_stored_as_webp_and_replaced_on_update(): void
    {
        Storage::fake('public');

        $this->post(route('admin.categories.store'), ['name' => 'Berfoto', 'icon' => 'tools', 'status' => 'published', 'image' => UploadedFile::fake()->image('a.jpg', 800, 600)]);

        $category = ServiceCategory::firstWhere('name', 'Berfoto');
        $this->assertStringEndsWith('.webp', $category->image);
        Storage::disk('public')->assertExists($category->image);

        $old = $category->image;
        $this->put(route('admin.categories.update', $category), ['name' => 'Berfoto', 'icon' => 'tools', 'status' => 'published', 'image' => UploadedFile::fake()->image('b.png', 400, 400)]);

        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($category->fresh()->image);
    }

    public function test_update_keeps_the_slug_when_the_name_changes(): void
    {
        $category = $this->category('Nama Lama');

        $this->put(route('admin.categories.update', $category), ['name' => 'Nama Baru', 'icon' => 'office', 'status' => 'published'])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('service_categories', ['id' => $category->id, 'name' => 'Nama Baru', 'slug' => 'nama-lama']);
    }

    public function test_category_with_activities_cannot_be_deleted(): void
    {
        $category = $this->category('Berisi');
        BusinessActivity::create(['service_category_id' => $category->id, 'name' => 'A', 'slug' => 'a', 'sort_order' => 1]);

        $this->delete(route('admin.categories.destroy', $category))->assertSessionHas('error');

        $this->assertModelExists($category);
    }

    public function test_empty_category_can_be_deleted(): void
    {
        $category = $this->category('Kosong');

        $this->delete(route('admin.categories.destroy', $category))->assertRedirect(route('admin.categories.index'));

        $this->assertModelMissing($category);
    }

    public function test_reorder_saves_the_new_order_without_flooding_the_log(): void
    {
        $a = $this->category('A', 1);
        $b = $this->category('B', 2);
        $c = $this->category('C', 3);
        $logs = ActivityLog::count();

        $this->put(route('admin.categories.reorder'), ['order' => [$c->id, $a->id, $b->id]])
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success');

        $this->assertSame([1, 2, 3], [$c->fresh()->sort_order, $a->fresh()->sort_order, $b->fresh()->sort_order]);
        // Satu catatan "Urutan" untuk seluruh pengurutan, bukan satu per baris.
        $this->assertSame($logs + 1, ActivityLog::count());
        $this->assertSame(ActivityLog::ACTION_REORDERED, ActivityLog::latest('id')->first()->action);
    }

    public function test_reorder_with_a_stale_list_is_rejected(): void
    {
        $a = $this->category('A', 1);
        $this->category('B', 2);

        $this->put(route('admin.categories.reorder'), ['order' => [$a->id]])->assertSessionHas('error');

        $this->assertSame(1, $a->fresh()->sort_order);
    }

    public function test_reorder_mode_renders_the_drag_list_only_without_filters(): void
    {
        $this->category('A', 1);
        $this->category('B', 2);

        $this->get(route('admin.categories.index', ['reorder' => 1]))->assertSee('x-data="reorderList"', false);
        $this->get(route('admin.categories.index', ['reorder' => 1, 'q' => 'A']))->assertDontSee('x-data="reorderList"', false);
    }

    public function test_changes_are_recorded_in_the_activity_log(): void
    {
        $this->post(route('admin.categories.store'), ['name' => 'Tercatat', 'icon' => 'tools', 'status' => 'published']);
        $category = ServiceCategory::firstWhere('name', 'Tercatat');
        $this->put(route('admin.categories.update', $category), ['name' => 'Tercatat', 'icon' => 'tools', 'status' => 'draft']);
        $this->delete(route('admin.categories.destroy', $category));

        $this->assertSame(['created', 'updated', 'deleted'], ActivityLog::orderBy('id')->pluck('action')->all());
        $this->assertSame(['is_published'], array_keys(ActivityLog::firstWhere('action', 'updated')->changes));
    }

    public function test_draft_category_is_hidden_from_the_public_site(): void
    {
        $this->category('Terlihat', 1);
        $this->category('Tersembunyi', 2, false);

        $this->get(route('services'))->assertSee('Terlihat')->assertDontSee('Tersembunyi');
    }
}
