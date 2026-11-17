<?php

namespace App\Services;

use App\Exceptions\ActionBlockedException;
use App\Helpers\OrderHelper;
use App\Helpers\SlugHelper;
use App\Helpers\UploadHelper;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Throwable;

class ServiceCategoryService
{
    /** Lebar maksimum ikon kustom (px); ikon tampil kecil, jadi tidak perlu besar. */
    private const ICON_MAX_WIDTH = 256;

    public function paginate(?string $search, ?string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->filtered($search, $status)->paginate($perPage);
    }

    /**
     * Semua kategori tanpa paginasi, untuk mode atur urutan (urutan harus utuh).
     *
     * @return Collection<int, ServiceCategory>
     */
    public function all(): Collection
    {
        return $this->filtered(null, null)->get();
    }

    /**
     * Pilihan id dan nama untuk dropdown.
     *
     * @return Collection<int, ServiceCategory>
     */
    public function options(): Collection
    {
        return ServiceCategory::ordered()->get(['id', 'name']);
    }

    /**
     * Jumlah per status (tanpa filter pencarian).
     *
     * @return array{total: int, published: int, draft: int}
     */
    public function statusCounts(): array
    {
        $total = ServiceCategory::count();
        $published = ServiceCategory::published()->count();

        return ['total' => $total, 'published' => $published, 'draft' => $total - $published];
    }

    /**
     * Kategori terbit beserta aktivitas terbitnya, untuk halaman publik.
     *
     * @return Collection<int, ServiceCategory>
     */
    public function publishedWithActivities(): Collection
    {
        return ServiceCategory::published()
            ->ordered()
            ->with(['activities' => fn ($query) => $query->published()])
            ->get();
    }

    /**
     * @param  array{name: string, description: ?string, icon: string, is_published: bool}  $data
     */
    public function store(array $data, ?UploadedFile $image = null, ?UploadedFile $iconImage = null): ServiceCategory
    {
        $imagePath = $image ? UploadHelper::storeImage($image, 'categories') : null;

        try {
            $iconPath = $iconImage ? UploadHelper::storeImage($iconImage, 'category-icons', self::ICON_MAX_WIDTH) : null;
        } catch (Throwable $e) {
            UploadHelper::delete($imagePath);
            throw $e;
        }

        try {
            return ServiceCategory::create([
                ...$data,
                'slug' => SlugHelper::unique($data['name'], fn (string $slug) => ServiceCategory::where('slug', $slug)->exists()),
                'sort_order' => (int) ServiceCategory::max('sort_order') + 1,
                'image' => $imagePath,
                'icon_image' => $iconPath,
            ]);
        } catch (Throwable $e) {
            UploadHelper::delete($imagePath); // bersihkan file yatim, lalu lempar ulang
            UploadHelper::delete($iconPath);
            throw $e;
        }
    }

    /**
     * Alamat URL (slug) sengaja tidak berubah saat nama diubah agar tautan yang sudah beredar tetap berlaku.
     *
     * @param  array{name: string, description: ?string, icon: string, is_published: bool}  $data
     */
    public function update(
        ServiceCategory $category,
        array $data,
        ?UploadedFile $image = null,
        bool $removeImage = false,
        ?UploadedFile $iconImage = null,
        bool $removeIconImage = false,
    ): ServiceCategory {
        $oldImage = $category->image;
        $oldIcon = $category->icon_image;
        $newImage = $image ? UploadHelper::storeImage($image, 'categories') : null;

        try {
            $newIcon = $iconImage ? UploadHelper::storeImage($iconImage, 'category-icons', self::ICON_MAX_WIDTH) : null;
        } catch (Throwable $e) {
            UploadHelper::delete($newImage);
            throw $e;
        }

        if ($newImage !== null) {
            $data['image'] = $newImage;
        } elseif ($removeImage) {
            $data['image'] = null;
        }

        if ($newIcon !== null) {
            $data['icon_image'] = $newIcon;
        } elseif ($removeIconImage) {
            $data['icon_image'] = null;
        }

        try {
            $category->update($data);
        } catch (Throwable $e) {
            UploadHelper::delete($newImage);
            UploadHelper::delete($newIcon);
            throw $e;
        }

        // Hapus file lama hanya setelah database berhasil.
        if (array_key_exists('image', $data)) {
            UploadHelper::delete($oldImage);
        }

        if (array_key_exists('icon_image', $data)) {
            UploadHelper::delete($oldIcon);
        }

        return $category;
    }

    /**
     * @throws ActionBlockedException bila kategori masih memiliki aktivitas
     */
    public function delete(ServiceCategory $category): void
    {
        $count = $category->activities()->count();

        if ($count > 0) {
            throw new ActionBlockedException("Kategori \"{$category->name}\" masih memiliki {$count} aktivitas. Pindahkan atau hapus aktivitasnya terlebih dahulu.");
        }

        $category->delete();
        UploadHelper::delete($category->image);
        UploadHelper::delete($category->icon_image);
    }

    /**
     * @param  array<int, int|string>  $ids  id kategori sesuai urutan baru (harus semua kategori)
     */
    public function reorder(array $ids): void
    {
        OrderHelper::apply(ServiceCategory::query(), $ids, 'kategori');
    }

    private function filtered(?string $search, ?string $status): Builder
    {
        return ServiceCategory::query()
            ->withCount('activities')
            ->when($status === 'published', fn (Builder $query) => $query->published())
            ->when($status === 'draft', fn (Builder $query) => $query->where('is_published', false))
            ->when(filled($search), fn (Builder $query) => $query->where(function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%");
            }))
            ->ordered();
    }
}
