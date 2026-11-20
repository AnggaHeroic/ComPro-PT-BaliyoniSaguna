<?php

namespace App\Services;

use App\Helpers\OrderHelper;
use App\Helpers\UploadHelper;
use App\Models\ActivityLog;
use App\Models\GalleryPhoto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Throwable;

class GalleryPhotoService
{
    public function __construct(private ActivityLogService $logService) {}

    /**
     * Foto draft (belum tampil di website), yang terbaru di atas.
     *
     * @return Collection<int, GalleryPhoto>
     */
    public function drafts(): Collection
    {
        return GalleryPhoto::where('is_published', false)->latest('updated_at')->latest('id')->get();
    }

    /**
     * Foto terbit untuk halaman publik.
     *
     * @return Collection<int, GalleryPhoto>
     */
    public function published(): Collection
    {
        return GalleryPhoto::published()->ordered()->get();
    }

    /**
     * @return array{total: int, published: int, draft: int}
     */
    public function statusCounts(): array
    {
        $total = GalleryPhoto::count();
        $published = GalleryPhoto::published()->count();

        return ['total' => $total, 'published' => $published, 'draft' => $total - $published];
    }

    /**
     * Simpan beberapa foto sekaligus ke urutan paling akhir. Judul awal diambil dari nama file
     * (bisa diubah kemudian). Semua-atau-tidak-sama-sekali: bila ada yang gagal, file yang sudah
     * tersimpan dibersihkan.
     *
     * @param  list<UploadedFile>  $files
     * @return Collection<int, GalleryPhoto>
     */
    public function storeMany(array $files): Collection
    {
        $paths = [];

        try {
            $order = (int) GalleryPhoto::max('sort_order');

            return new Collection(array_map(function (UploadedFile $file) use (&$paths, &$order) {
                $paths[] = $path = UploadHelper::storeImage($file, 'gallery');

                return GalleryPhoto::create([
                    'title' => $this->titleFromFilename($file),
                    'image' => $path,
                    'sort_order' => ++$order,
                    'is_published' => true,
                ]);
            }, $files));
        } catch (Throwable $e) {
            array_map(UploadHelper::delete(...), $paths); // bersihkan file yatim, lalu lempar ulang
            throw $e;
        }
    }

    /**
     * @param  array{title: string, description: ?string, is_published: bool}  $data
     */
    public function update(GalleryPhoto $photo, array $data, ?UploadedFile $image = null): GalleryPhoto
    {
        $oldPath = $photo->image;
        $newPath = $image ? UploadHelper::storeImage($image, 'gallery') : null;

        if ($newPath !== null) {
            $data['image'] = $newPath;
        }

        try {
            $photo->update($data);
        } catch (Throwable $e) {
            UploadHelper::delete($newPath);
            throw $e;
        }

        if ($newPath !== null) {
            UploadHelper::delete($oldPath); // hapus file lama hanya setelah database berhasil
        }

        return $photo;
    }

    public function delete(GalleryPhoto $photo): void
    {
        $photo->delete();
        UploadHelper::delete($photo->image);
    }

    /**
     * Urutan hanya berlaku untuk foto terbit (yang tampil di kisi); foto draft tidak ikut diurutkan.
     *
     * @param  array<int, int|string>  $ids  id foto terbit sesuai urutan baru (harus semua foto terbit)
     */
    public function reorder(array $ids): void
    {
        OrderHelper::apply(GalleryPhoto::published(), $ids, 'foto');

        $this->logService->record(ActivityLog::ACTION_REORDERED, GalleryPhoto::class);
    }

    private function titleFromFilename(UploadedFile $file): string
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $title = Str::of($name)->replaceMatches('/[-_.]+/', ' ')->squish()->limit(120, '')->toString();

        return $title !== '' ? Str::ucfirst($title) : 'Foto galeri';
    }
}
