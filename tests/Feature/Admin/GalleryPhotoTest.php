<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\GalleryPhoto;
use App\Models\User;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryPhotoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
    }

    private function photo(string $title, int $order = 1, bool $published = true): GalleryPhoto
    {
        return GalleryPhoto::create(['title' => $title, 'image' => 'gallery/'.str($title)->slug().'.webp', 'sort_order' => $order, 'is_published' => $published]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        auth()->logout();

        $this->get(route('admin.gallery.index'))->assertRedirect(route('login'));
        $this->post(route('admin.gallery.store'), [])->assertRedirect(route('login'));
    }

    public function test_index_shows_the_upload_form_and_a_bento_preview_with_the_public_layout(): void
    {
        $this->photo('Foto Satu', 1);
        $this->photo('Foto Dua', 2, false);

        $this->get(route('admin.gallery.index'))
            ->assertOk()->assertSee('name="images[]"', false)->assertSee('Foto Satu')->assertDontSee('Foto Dua')
            ->assertSee('lg:auto-rows-[13rem] lg:grid-flow-dense lg:grid-cols-4', false)
            ->assertSee('lg:col-span-2 lg:row-span-2', false);
    }

    public function test_multiple_photos_can_be_uploaded_with_titles_from_filenames_in_the_last_positions(): void
    {
        $this->photo('Lama', 5);

        $this->post(route('admin.gallery.store'), ['images' => [
            UploadedFile::fake()->image('gedung-kantor_baru.jpg', 800, 600),
            UploadedFile::fake()->image('ruang rapat.png', 600, 600),
        ]])->assertRedirect(route('admin.gallery.index'))->assertSessionHas('success');

        $new = GalleryPhoto::orderBy('sort_order')->get();
        $this->assertSame(['Lama', 'Gedung kantor baru', 'Ruang rapat'], $new->pluck('title')->all());
        $this->assertSame([5, 6, 7], $new->pluck('sort_order')->all());
        $this->assertTrue($new->skip(1)->every(fn ($photo) => str_ends_with($photo->image, '.webp') && Storage::disk('public')->exists($photo->image)));
    }

    public function test_upload_validates_files(): void
    {
        $this->post(route('admin.gallery.store'), [])->assertSessionHasErrors('images');
        $this->post(route('admin.gallery.store'), ['images' => [UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf')]])->assertSessionHasErrors('images.0');
        $this->post(route('admin.gallery.store'), ['images' => array_map(fn ($i) => UploadedFile::fake()->image("f{$i}.jpg"), range(1, 11))])->assertSessionHasErrors('images');

        $this->assertSame(0, GalleryPhoto::count());
    }

    public function test_photo_details_can_be_edited_and_the_image_replaced(): void
    {
        $this->post(route('admin.gallery.store'), ['images' => [UploadedFile::fake()->image('asli.jpg', 400, 300)]]);
        $photo = GalleryPhoto::first();
        $old = $photo->image;

        $this->get(route('admin.gallery.edit', $photo))->assertOk()->assertSee('name="status" value="draft"', false);

        $this->put(route('admin.gallery.update', $photo), ['title' => 'Judul Baru', 'description' => 'Keterangan foto', 'status' => 'draft', 'image' => UploadedFile::fake()->image('baru.jpg', 300, 300)])
            ->assertRedirect(route('admin.gallery.index'));

        $photo->refresh();
        $this->assertSame('Judul Baru', $photo->title);
        $this->assertFalse($photo->is_published);
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($photo->image);

        $this->put(route('admin.gallery.update', $photo), ['title' => '', 'status' => 'draft'])->assertSessionHasErrors('title');
    }

    public function test_photo_can_be_deleted_with_its_file(): void
    {
        $this->post(route('admin.gallery.store'), ['images' => [UploadedFile::fake()->image('hapus.jpg', 300, 300)]]);
        $photo = GalleryPhoto::first();

        $this->delete(route('admin.gallery.destroy', $photo))->assertRedirect(route('admin.gallery.index'));

        $this->assertModelMissing($photo);
        Storage::disk('public')->assertMissing($photo->image);
    }

    public function test_reorder_saves_positions_and_a_stale_list_is_rejected(): void
    {
        $a = $this->photo('A', 1);
        $b = $this->photo('B', 2);
        $c = $this->photo('C', 3);
        $logs = ActivityLog::count();

        $this->put(route('admin.gallery.reorder'), ['order' => [$c->id, $a->id, $b->id]])->assertSessionHas('success');
        $this->assertSame([1, 2, 3], [$c->fresh()->sort_order, $a->fresh()->sort_order, $b->fresh()->sort_order]);
        // Satu catatan "Urutan" untuk seluruh pengurutan, bukan satu per baris.
        $this->assertSame($logs + 1, ActivityLog::count());
        $this->assertSame(ActivityLog::ACTION_REORDERED, ActivityLog::latest('id')->first()->action);

        $this->put(route('admin.gallery.reorder'), ['order' => [$a->id]])->assertSessionHas('error');
    }

    public function test_reorder_mode_renders_the_draggable_bento_grid(): void
    {
        $this->photo('A', 1);
        $this->photo('B', 2);

        $this->get(route('admin.gallery.index', ['reorder' => 1]))
            ->assertSee('x-data="reorderList"', false)->assertSee('data-layout="grid"', false)->assertSee('data-spans=', false)->assertSee('data-id=', false);
        $this->get(route('admin.gallery.index'))->assertDontSee('x-data="reorderList"', false);
    }

    public function test_public_gallery_shows_only_published_photos_in_the_admin_order(): void
    {
        $this->photo('Ketiga', 3);
        $this->photo('Pertama', 1);
        $this->photo('Draft Tersembunyi', 2, false);

        $html = $this->get(route('gallery'))->assertOk()->assertDontSee('Draft Tersembunyi')->getContent();

        $this->assertLessThan(strpos($html, 'Ketiga'), strpos($html, 'Pertama'));
    }

    public function test_public_gallery_uses_the_same_bento_spans_and_shows_seeded_photos(): void
    {
        $this->seed(SiteContentSeeder::class);

        $this->get(route('gallery'))->assertOk()->assertSee('lg:col-span-2 lg:row-span-2', false)->assertSee(config('company.gallery')[0]['title']);
        $this->assertSame(count(config('company.gallery')), GalleryPhoto::count());
    }

    public function test_changes_are_recorded_in_the_activity_log_under_the_gallery_menu(): void
    {
        $this->post(route('admin.gallery.store'), ['images' => [UploadedFile::fake()->image('tercatat.jpg', 300, 300)]]);

        $log = ActivityLog::firstWhere('subject_type', GalleryPhoto::class);
        $this->assertSame('Foto Galeri', $log->subjectName());
        $this->assertSame('Tercatat', $log->subject_label);
    }
}
