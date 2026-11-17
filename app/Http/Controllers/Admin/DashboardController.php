<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboardService,
        private CompanyService $companyService,
    ) {}

    public function index(): View
    {
        return view('admin.dashboard', [
            'summary' => $this->dashboardService->summary(),
            'company' => $this->companyService->profile(),
        ]);
    }
}
