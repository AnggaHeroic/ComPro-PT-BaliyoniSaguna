<?php

namespace Tests\Feature\Admin;

use App\Models\BusinessActivity;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Daftar aktivitas mengikuti urutan kategori; ikon kustom menggantikan ikon daftar (yang menjadi kosong). */
class CategoryOrderAndCustomIconTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->actingAs(User::factory()->create());
    }

    private function category(string $name, int $order, array $extra = []): ServiceCategory
    {
        return ServiceCategory::create(['name' => $name, 'slug' => str($name)->slug()->toString(), 'icon' => 'office', 'sort_order' => $order, 'is_published' => true, ...$extra]);
    }

    public function test_activity_list_is_grouped_in_the_order_of_the_categories(): void
    {
        $first = $this->category('Kategori Pertama', 2);
        $second = $this->category('Kategori Kedua', 1);
        BusinessActivity::create(['service_category_id' => $first->id, 'name' => 'Aktivitas A', 'slug' => 'a', 'sort_order' => 1]);
        BusinessActivity::create(['service_category_id' => $second->id, 'name' => 'Aktivitas B', 'slug' => 'b', 'sort_order' => 1]);

        $this->get(route('admin.activities.index'))->assertSeeInOrder(['Aktivitas B', 'Aktivitas A']);

        // Kategori dipindah: daftar aktivitas ikut berubah.
        $this->put(route('admin.categories.reorder'), ['order' => [$first->id, $second->id]]);
        $this->get(route('admin.activities.index'))->assertSeeInOrder(['Aktivitas A', 'Aktivitas B']);
    }

    public function test_activity_search_also_matches_the_category_name(): void
    {
        $kantor = $this->category('Peralatan Kantor', 1);
        $dapur = $this->category('Peralatan Dapur', 2);
        BusinessActivity::create(['service_category_id' => $kantor->id, 'name' => 'Kursi', 'slug' => 'kursi', 'sort_order' => 1]);
        BusinessActivity::create(['service_category_id' => $dapur->id, 'name' => 'Panci', 'slug' => 'panci', 'sort_order' => 1]);

        $this->get(route('admin.activities.index', ['q' => 'Kantor']))->assertSee('Kursi')->assertDontSee('Panci');
    }

    public function test_custom_icon_makes_the_list_icon_optional_but_one_of_them_is_required(): void
    {
        Storage::fake('public');
        $payload = ['name' => 'Baru', 'status' => 'published'];

        $this->post(route('admin.categories.store'), $payload)->assertSessionHasErrors('icon');

        $this->post(route('admin.categories.store'), [...$payload, 'icon' => '', 'icon_image' => UploadedFile::fake()->image('ikon.png', 120, 120)])
            ->assertSessionHasNoErrors();

        $category = ServiceCategory::where('name', 'Baru')->first();
        $this->assertNull($category->icon);
        $this->assertNotNull($category->icon_image);

        // Ikon lama tetap ada, jadi mengubah tanpa memilih ikon daftar diperbolehkan; menghapusnya tanpa pengganti tidak.
        $this->put(route('admin.categories.update', $category), [...$payload, 'icon' => ''])->assertSessionHasNoErrors();
        $this->put(route('admin.categories.update', $category), [...$payload, 'icon' => '', 'remove_icon_image' => '1'])->assertSessionHasErrors('icon');
        $this->put(route('admin.categories.update', $category), [...$payload, 'icon' => 'cart', 'remove_icon_image' => '1'])->assertSessionHasNoErrors();
        $this->assertNull($category->fresh()->icon_image);
        $this->assertSame('cart', $category->fresh()->icon);
    }

    public function test_edit_form_selects_no_list_icon_when_a_custom_icon_exists(): void
    {
        $category = $this->category('Berikon', 1, ['icon' => null, 'icon_image' => 'category-icons/x.webp']);

        $this->get(route('admin.categories.edit', $category))->assertOk()
            ->assertSee("pairedWith: 'icon_image'", false)
            ->assertSee('iconPicker({ value: null', false);
    }

    public function test_public_pages_show_a_custom_icon_without_a_list_icon_and_not_as_a_white_silhouette(): void
    {
        $category = $this->category('Berikon', 1, ['icon' => null, 'icon_image' => 'category-icons/x.webp']);
        BusinessActivity::create(['service_category_id' => $category->id, 'name' => 'Aktivitas X', 'slug' => 'aktivitas-x', 'sort_order' => 1]);

        $url = Storage::disk('public')->url('category-icons/x.webp');

        foreach ([route('home'), route('services'), route('services.show', ['service' => 'berikon', 'activity' => 'aktivitas-x'])] as $page) {
            $html = $this->get($page)->assertOk()->getContent();
            $this->assertStringContainsString($url, $html);
            $this->assertStringNotContainsString('brightness-0', $html);
        }
    }
}
