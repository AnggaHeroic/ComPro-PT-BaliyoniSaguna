<?php

namespace App\Services;

use App\Helpers\OrderHelper;
use App\Helpers\SlugHelper;
use App\Helpers\UploadHelper;
use App\Models\ActivityLog;
use App\Models\BusinessActivity;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

class BusinessActivityService
{
    public function __construct(private ActivityLogService $logService) {}

    public function paginate(?string $search, ?int $categoryId, ?string $status, int $perPage = 15): LengthAwarePaginator
    {
        return $this->filtered($search, $categoryId, $status)->paginate($perPage);
    }

    /**
     * Kategori yang memiliki aktivitas, masing-masing beserta semua aktivitasnya (tanpa paginasi), untuk mode
     * atur urutan yang dipecah per kategori. Bisa dibatasi pada satu kategori.
     *
     * @return Collection<int, ServiceCategory>
     */
    public function groupedByCategory(?int $categoryId = null): Collection
    {
        return ServiceCategory::ordered()
            ->when($categoryId, fn (Builder $query) => $query->whereKey($categoryId))
            ->with('activities')
            ->has('activities')
            ->get();
    }

    /**
     * Jumlah per status, bisa dibatasi pada satu kategori.
     *
     * @return array{total: int, published: int, draft: int}
     */
    public function statusCounts(?int $categoryId = null): array
    {
        $scope = fn () => BusinessActivity::query()->when($categoryId, fn (Builder $query) => $query->where('service_category_id', $categoryId));

        $total = $scope()->count();
        $published = $scope()->published()->count();

        return ['total' => $total, 'published' => $published, 'draft' => $total - $published];
    }

    /**
     * @param  array{service_category_id: int, name: string, summary: ?string, detail: ?string, is_published: bool}  $data
     */
    public function store(array $data, ?UploadedFile $image = null): BusinessActivity
    {
        $categoryId = (int) $data['service_category_id'];
        $path = $image ? UploadHelper::storeImage($image, 'activities') : null;

        try {
            return BusinessActivity::create([
                ...$data,
                'slug' => $this->uniqueSlug($data['name'], $categoryId),
                'sort_order' => $this->nextSortOrder($categoryId),
                'image' => $path,
            ]);
        } catch (Throwable $e) {
            UploadHelper::delete($path); // bersihkan file yatim, lalu lempar ulang
            throw $e;
        }
    }

    /**
     * Alamat URL (slug) dipertahankan agar tautan tetap berlaku; hanya dibuat ulang bila aktivitas
     * dipindah ke kategori lain dan alamatnya bentrok di sana.
     *
     * @param  array{service_category_id: int, name: string, summary: ?string, detail: ?string, is_published: bool}  $data
     */
    public function update(BusinessActivity $activity, array $data, ?UploadedFile $image = null, bool $removeImage = false): BusinessActivity
    {
        $oldPath = $activity->image;
        $newPath = $image ? UploadHelper::storeImage($image, 'activities') : null;
        $categoryId = (int) $data['service_category_id'];

        if ($categoryId !== $activity->service_category_id) {
            $data['sort_order'] = $this->nextSortOrder($categoryId);

            if ($this->slugTaken($activity->slug, $categoryId)) {
                $data['slug'] = $this->uniqueSlug($data['name'], $categoryId);
            }
        }

        if ($newPath !== null) {
            $data['image'] = $newPath;
        } elseif ($removeImage) {
            $data['image'] = null;
        }

        try {
            $activity->update($data);
        } catch (Throwable $e) {
            UploadHelper::delete($newPath);
            throw $e;
        }

        if (array_key_exists('image', $data)) {
            UploadHelper::delete($oldPath); // hapus file lama hanya setelah database berhasil
        }

        return $activity;
    }

    public function delete(BusinessActivity $activity): void
    {
        $activity->delete();
        UploadHelper::delete($activity->image);
    }

    /**
     * Terapkan urutan baru untuk beberapa kategori sekaligus. Tiap daftar harus persis semua aktivitas kategorinya,
     * sehingga aktivitas tidak bisa berpindah ke kategori lain lewat pengurutan. Semua-atau-tidak-sama-sekali.
     *
     * @param  array<int|string, array<int, int|string>>  $orders  id kategori => id aktivitas sesuai urutan baru
     */
    public function reorder(array $orders): void
    {
        DB::transaction(function () use ($orders) {
            foreach ($orders as $categoryId => $ids) {
                OrderHelper::apply(BusinessActivity::where('service_category_id', (int) $categoryId), $ids, 'aktivitas');
            }
        });

        // Satu catatan untuk seluruh pengurutan, menyebut kategori yang urutannya berubah.
        $names = ServiceCategory::whereIn('id', array_map('intval', array_keys($orders)))->orderBy('name')->pluck('name')->all();
        $this->logService->record(ActivityLog::ACTION_REORDERED, BusinessActivity::class, 0, $names === [] ? null : 'Kategori: '.implode(', ', $names));
    }

    private function filtered(?string $search, ?int $categoryId, ?string $status): Builder
    {
        return BusinessActivity::query()
            ->with('category:id,name')
            ->when($categoryId, fn (Builder $query) => $query->where('service_category_id', $categoryId))
            ->when($status === 'published', fn (Builder $query) => $query->published())
            ->when($status === 'draft', fn (Builder $query) => $query->where('is_published', false))
            ->when(filled($search), fn (Builder $query) => $query->where(function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('kbli_code', 'like', "%{$search}%");
            }))
            // Kelompok mengikuti urutan kategori (bukan id-nya), jadi mengatur ulang kategori ikut menggeser daftar ini.
            ->orderBy(ServiceCategory::select('sort_order')->whereColumn('service_categories.id', 'business_activities.service_category_id'))
            ->orderBy('service_category_id')
            ->ordered();
    }

    private function nextSortOrder(int $categoryId): int
    {
        return (int) BusinessActivity::where('service_category_id', $categoryId)->max('sort_order') + 1;
    }

    private function slugTaken(string $slug, int $categoryId): bool
    {
        return BusinessActivity::where('service_category_id', $categoryId)->where('slug', $slug)->exists();
    }

    private function uniqueSlug(string $name, int $categoryId): string
    {
        return SlugHelper::unique($name, fn (string $slug) => $this->slugTaken($slug, $categoryId), 150);
    }
}
