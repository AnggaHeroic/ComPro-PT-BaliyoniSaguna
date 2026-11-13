<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct(private CompanyService $companyService) {}

    // Daftar layanan ($services) dan profil ($company) sudah tersedia di semua view publik.
    public function index(): View
    {
        return view('web.services');
    }

    // Halaman detail satu aktivitas usaha; 404 bila kategori atau aktivitas tidak ada.
    public function show(string $service, string $activity): View
    {
        $detail = $this->companyService->findActivity($service, $activity) ?? abort(404);

        return view('web.service-activity', $detail);
    }
}
