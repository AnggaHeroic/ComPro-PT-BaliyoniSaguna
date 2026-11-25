<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ActionBlockedException;
use App\Helpers\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListFilterRequest;
use App\Http\Requests\Admin\ReorderRequest;
use App\Http\Requests\Admin\StoreServiceCategoryRequest;
use App\Http\Requests\Admin\UpdateServiceCategoryRequest;
use App\Models\ServiceCategory;
use App\Services\ActivityLogService;
use App\Services\ServiceCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ServiceCategoryController extends Controller
{
    public function __construct(
        private ServiceCategoryService $categoryService,
        private ActivityLogService $logService,
    ) {}

    public function index(ListFilterRequest $request): View
    {
        $search = trim((string) $request->validated('q'));
        $status = $request->validated('status');

        // Mode atur urutan hanya tanpa pencarian/filter, dan memuat semua baris agar urutan utuh.
        $reorder = $request->boolean('reorder') && $search === '' && $status === null;

        return view('admin.categories.index', [
            'categories' => $reorder
                ? $this->categoryService->all()
                : $this->categoryService->paginate($search, $status)->withQueryString(),
            'counts' => $this->categoryService->statusCounts(),
            'search' => $search,
            'status' => $status,
            'reorder' => $reorder,
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.create', ['category' => new ServiceCategory(['is_published' => true])]);
    }

    public function store(StoreServiceCategoryRequest $request): RedirectResponse
    {
        try {
            $category = $this->categoryService->store(
                $request->safe()->only(['name', 'description', 'icon', 'is_published']),
                $request->file('image'),
                $request->file('icon_image'),
            );
        } catch (Throwable $e) {
            report($e);

            return RedirectHelper::failure('Gagal menyimpan kategori.', true);
        }

        return RedirectHelper::afterSave('admin.categories.index', $category->is_published, 'Kategori berhasil ditambahkan.');
    }

    public function edit(ServiceCategory $category): View
    {
        return view('admin.categories.edit', [
            'category' => $category,
            'lastChange' => $this->logService->lastChange($category),
        ]);
    }

    public function update(UpdateServiceCategoryRequest $request, ServiceCategory $category): RedirectResponse
    {
        try {
            $this->categoryService->update(
                $category,
                $request->safe()->only(['name', 'description', 'icon', 'is_published']),
                $request->file('image'),
                $request->boolean('remove_image'),
                $request->file('icon_image'),
                $request->boolean('remove_icon_image'),
            );
        } catch (Throwable $e) {
            report($e);

            return RedirectHelper::failure('Gagal memperbarui kategori.', true);
        }

        return RedirectHelper::afterSave('admin.categories.index', $category->is_published, 'Kategori berhasil diperbarui.');
    }

    public function destroy(ServiceCategory $category): RedirectResponse
    {
        try {
            $this->categoryService->delete($category);
        } catch (ActionBlockedException $e) {
            // Pesan ini sengaja ditulis untuk admin (kategori masih berisi aktivitas), jadi aman ditampilkan.
            return RedirectHelper::failure($e->getMessage());
        } catch (Throwable $e) {
            report($e);

            return RedirectHelper::failure('Gagal menghapus kategori.');
        }

        return RedirectHelper::success('admin.categories.index', 'Kategori berhasil dihapus.');
    }

    public function reorder(ReorderRequest $request): RedirectResponse
    {
        try {
            $this->categoryService->reorder($request->validated('order'));
        } catch (ValidationException $e) {
            return RedirectHelper::failureTo('admin.categories.index', $e->validator->errors()->first('order'));
        } catch (Throwable $e) {
            report($e);

            return RedirectHelper::failure('Gagal menyimpan urutan kategori.');
        }

        return RedirectHelper::success('admin.categories.index', 'Urutan kategori disimpan.');
    }
}
