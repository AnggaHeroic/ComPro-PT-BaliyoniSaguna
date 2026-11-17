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
        $filters = $request->safe()->only(['menu', 'action', 'q', 'dari', 'sampai']);

        return view('admin.activity-logs.index', [
            'logs' => $this->logService->paginate($filters)->withQueryString(),
            'filters' => $filters,
            'menus' => ActivityLog::menus(),
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
