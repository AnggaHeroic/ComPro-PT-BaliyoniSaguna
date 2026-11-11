<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __construct(private CompanyService $companyService) {}

    public function index(): View
    {
        return view('web.about', [
            'about' => $this->companyService->about(),
        ]);
    }
}
