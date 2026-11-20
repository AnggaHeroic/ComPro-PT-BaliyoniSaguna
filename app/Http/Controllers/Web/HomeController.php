<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use App\Services\PageContentService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private CompanyService $companyService,
        private PageContentService $pageContentService,
    ) {}

    public function index(): View
    {
        return view('web.home', [
            ...$this->companyService->pageData('home'),
            'content' => $this->pageContentService->resolve('home'),
        ]);
    }
}
