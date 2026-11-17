<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ListFilterRequest;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __construct(private ActivityLogService $logService) {}

    public function index(ListFilterRequest $request): View
    {
        $filters = $request->safe()->only(['jenis', 'menu', 'action', 'q', 'dari', 'sampai']);
        $group = $filters['jenis'] ?? ActivityLog::DEFAULT_GROUP;
        $groups = ActivityLog::groups();

        return view('admin.activity-logs.index', [
            'logs' => $this->logService->paginate($filters)->withQueryString(),
            'filters' => $filters,
            'group' => $group,
            'defaultGroup' => ActivityLog::DEFAULT_GROUP,
            'groups' => $groups,
            'counts' => $this->logService->groupCounts($filters),
            'menus' => $groups[$group]['menus'],
            // Pilihan aksi hanya yang berlaku untuk jenis yang dibuka (label dari ActivityLog::actions()).
            'actions' => collect(ActivityLog::actions())
                ->filter(fn (array $item, string $action) => $groups[$group]['actions'] === null || in_array($action, $groups[$group]['actions'], true))
                ->map(fn (array $item) => $item[0])
                ->all(),
            'datePresets' => $this->logService->datePresets(),
            'rangeLabel' => $this->rangeLabel($filters['dari'] ?? null, $filters['sampai'] ?? null),
        ]);
    }

    /**
     * Teks singkat rentang tanggal aktif untuk tombol filter, mis. "01 Nov 2026 – 07 Nov 2026".
     */
    private function rangeLabel(?string $from, ?string $to): ?string
    {
        $format = fn (?string $date) => $date ? Carbon::parse($date)->locale('id')->translatedFormat('d M Y') : null;

        return match (true) {
            $from && $to && $from === $to => $format($from),
            (bool) ($from && $to) => $format($from).' – '.$format($to),
            (bool) $from => 'Sejak '.$format($from),
            (bool) $to => 'Sampai '.$format($to),
            default => null,
        };
    }
}
