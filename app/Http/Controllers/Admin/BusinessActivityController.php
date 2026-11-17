<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\RedirectHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListFilterRequest;
use App\Http\Requests\Admin\ReorderActivitiesRequest;
use App\Http\Requests\Admin\StoreBusinessActivityRequest;
use App\Http\Requests\Admin\UpdateBusinessActivityRequest;
use App\Models\BusinessActivity;
use App\Services\ActivityLogService;
use App\Services\BusinessActivityService;
use App\Services\ServiceCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class BusinessActivityController extends Controller
{
    private const FIELDS = ['service_category_id', 'name', 'kbli_code', 'summary', 'detail', 'is_published'];

    public function __construct(
        private BusinessActivityService $activityService,
        private ServiceCategoryService $categoryService,
        private ActivityLogService $logService,
    ) {}

    public function index(ListFilterRequest $request): View
    {
        $search = trim((string) $request->validated('q'));
        $status = $request->validated('status');
        $categoryId = $request->validated('category') ?: null;

        $counts = $this->activityService->statusCounts($categoryId);

        // Mode atur urutan tidak boleh ada pencarian/filter status; daftarnya dipecah per kategori usaha.
        $reorder = $request->boolean('reorder') && $search === '' && $status === null && $counts['total'] > 1;

        return view('admin.activities.index', [
            'activities' => $reorder ? null : $this->activityService->paginate($search, $categoryId, $status)->withQueryString(),
            'groups' => $reorder ? $this->activityService->groupedByCategory($categoryId) : null,
            'categories' => $this->categoryService->options(),
            'counts' => $counts,
            'search' => $search,
            'status' => $status,
            'categoryId' => $categoryId,
            'reorder' => $reorder,
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.activities.create', [
            'activity' => new BusinessActivity([
                'service_category_id' => $request->integer('category') ?: null,
                'is_published' => true,
            ]),
            'categories' => $this->categoryService->options(),
        ]);
    }

    public function store(StoreBusinessActivityRequest $request): RedirectResponse
    {
        try {
            $activity = $this->activityService->store($request->safe()->only(self::FIELDS), $request->file('image'));
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Gagal menyimpan aktivitas.');
        }

        return RedirectHelper::afterSave('admin.activities.index', $activity->is_published, 'Aktivitas berhasil ditambahkan.', ['category' => $activity->service_category_id]);
    }

    public function edit(BusinessActivity $activity): View
    {
        return view('admin.activities.edit', [
            'activity' => $activity,
            'categories' => $this->categoryService->options(),
            'lastChange' => $this->logService->lastChange($activity),
        ]);
    }

    public function update(UpdateBusinessActivityRequest $request, BusinessActivity $activity): RedirectResponse
    {
        try {
            $this->activityService->update($activity, $request->safe()->only(self::FIELDS), $request->file('image'), $request->boolean('remove_image'));
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Gagal memperbarui aktivitas.');
        }

        return RedirectHelper::afterSave('admin.activities.index', $activity->is_published, 'Aktivitas berhasil diperbarui.', ['category' => $activity->service_category_id]);
    }

    public function destroy(BusinessActivity $activity): RedirectResponse
    {
        $categoryId = $activity->service_category_id;

        try {
            $this->activityService->delete($activity);
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal menghapus aktivitas.');
        }

        return redirect()->route('admin.activities.index', ['category' => $categoryId])->with('success', 'Aktivitas berhasil dihapus.');
    }

    public function reorder(ReorderActivitiesRequest $request): RedirectResponse
    {
        try {
            $this->activityService->reorder($request->validated('orders'));
        } catch (ValidationException $e) {
            return redirect()->route('admin.activities.index')->with('error', $e->validator->errors()->first('order'));
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal menyimpan urutan aktivitas.');
        }

        return redirect()->route('admin.activities.index')->with('success', 'Urutan aktivitas disimpan.');
    }
}
