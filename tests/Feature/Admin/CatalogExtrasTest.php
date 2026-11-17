<?php

namespace Tests\Feature\Admin;

use App\Models\BusinessActivity;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogExtrasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->actingAs(User::factory()->create());
    }

    private function category(string $name = 'Peralatan Kantor'): ServiceCategory
    {
        return ServiceCategory::create(['name' => $name, 'slug' => str($name)->slug()->toString(), 'icon' => 'office', 'sort_order' => 1, 'is_published' => true]);
    }

    public function test_forms_show_status_cards_icon_picker_custom_icon_and_kbli(): void
    {
        $category = $this->category();

        $this->get(route('admin.categories.create'))
            ->assertSee('name="status" value="published"', false)->assertSee('name="status" value="draft"', false)
            ->assertSee('iconPicker', false)->assertSee('name="icon_image"', false);

        $this->get(route('admin.activities.create', ['category' => $category->id]))
            ->assertSee('name="kbli_code"', false)->assertSee('name="status" value="draft"', false);
    }

    public function test_icon_whitelist_includes_the_new_icons_and_each_has_a_drawing(): void
    {
        foreach (['laptop', 'cctv', 'printer', 'furniture', 'book', 'truck', 'cart', 'building', 'bolt', 'cleaning', 'medical', 'package'] as $key) {
            $this->assertArrayHasKey($key, ServiceCategory::ICONS);
            $this->assertMatchesRegularExpression('/<(path|rect|circle)/', (string) view('components.web.service-icon', ['name' => $key]));
        }

        $this->post(route('admin.categories.store'), ['name' => 'Alat Medis', 'icon' => 'medical', 'status' => 'published']);
        $this->assertDatabaseHas('service_categories', ['name' => 'Alat Medis', 'icon' => 'medical']);
    }

    public function test_custom_icon_is_stored_small_replaced_and_removed(): void
    {
        Storage::fake('public');

        $this->post(route('admin.categories.store'), ['name' => 'Ikonku', 'icon' => 'tools', 'status' => 'published', 'icon_image' => UploadedFile::fake()->image('ikon.png', 900, 900)]);
        $category = ServiceCategory::firstWhere('name', 'Ikonku');

        $this->assertStringStartsWith('category-icons/', $category->icon_image);
        Storage::disk('public')->assertExists($category->icon_image);
        $size = getimagesizefromstring(Storage::disk('public')->get($category->icon_image));
        $this->assertLessThanOrEqual(256, $size[0]);

        $old = $category->icon_image;
        $this->put(route('admin.categories.update', $category), ['name' => 'Ikonku', 'icon' => 'tools', 'status' => 'published', 'icon_image' => UploadedFile::fake()->image('baru.png', 100, 100)]);
        Storage::disk('public')->assertMissing($old);

        $current = $category->fresh()->icon_image;
        $this->put(route('admin.categories.update', $category), ['name' => 'Ikonku', 'icon' => 'tools', 'status' => 'published', 'remove_icon_image' => 1]);
        $this->assertNull($category->fresh()->icon_image);
        Storage::disk('public')->assertMissing($current);
    }

    public function test_public_site_shows_the_custom_icon_instead_of_the_built_in_one(): void
    {
        $category = $this->category('Berikon');
        $category->update(['icon_image' => 'category-icons/ikon-kustom.webp']);

        $this->get(route('services'))->assertSee('category-icons/ikon-kustom.webp', false);
    }

    public function test_kbli_is_saved_validated_searched_and_shown_publicly(): void
    {
        $category = $this->category();
        $payload = ['service_category_id' => $category->id, 'name' => 'Penjualan furnitur', 'status' => 'published'];

        $this->post(route('admin.activities.store'), $payload + ['kbli_code' => '47591']);
        $this->post(route('admin.activities.store'), ['name' => 'Tanpa kode'] + $payload);
        $this->post(route('admin.activities.store'), ['kbli_code' => 'a b!'] + $payload)->assertSessionHasErrors('kbli_code');
        $this->post(route('admin.activities.store'), ['kbli_code' => '123456789012'] + $payload)->assertSessionHasErrors('kbli_code');

        $activity = BusinessActivity::firstWhere('kbli_code', '47591');
        $this->assertNotNull($activity);
        $this->assertNull(BusinessActivity::firstWhere('name', 'Tanpa kode')?->kbli_code ?? null);

        $this->get(route('admin.activities.index', ['q' => '47591']))->assertSee('47591');
        $this->get(route('services.show', ['service' => $category->slug, 'activity' => $activity->slug]))->assertSee('Kode KBLI')->assertSee('47591');
    }

    public function test_kbli_is_hidden_on_the_public_page_when_empty(): void
    {
        $category = $this->category();
        $activity = BusinessActivity::create(['service_category_id' => $category->id, 'name' => 'Tanpa Kode', 'slug' => 'tanpa-kode', 'sort_order' => 1]);

        $this->get(route('services.show', ['service' => $category->slug, 'activity' => $activity->slug]))->assertOk()->assertDontSee('Kode KBLI');
    }

    public function test_success_message_is_a_popup_not_an_inline_banner(): void
    {
        $response = $this->followingRedirects()->post(route('admin.categories.store'), ['name' => 'Notif', 'icon' => 'tools', 'status' => 'published']);

        $response->assertSee('Kategori berhasil ditambahkan.')->assertSee('data-flash-popup="success"', false)->assertSee('toast-progress', false);
        $response->assertDontSee('mb-6 flex items-start gap-3 border-l-4', false);
    }

    public function test_error_message_is_also_a_popup(): void
    {
        $category = $this->category();
        BusinessActivity::create(['service_category_id' => $category->id, 'name' => 'A', 'slug' => 'a', 'sort_order' => 1]);

        $this->from(route('admin.categories.index'))->followingRedirects()->delete(route('admin.categories.destroy', $category))
            ->assertSee('masih memiliki 1 aktivitas')->assertSee('Gagal')->assertSee('data-flash-popup="error"', false);
    }
}
