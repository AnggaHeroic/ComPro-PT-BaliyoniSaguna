<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use App\Services\PageContentService;
use Illuminate\View\View;

class GalleryController extends Controller
{
    public function __construct(
        private CompanyService $companyService,
        private PageContentService $pageContentService,
    ) {}

    public function index(): View
    {
        return view('web.gallery', [
            ...$this->companyService->pageData('gallery'),
            'content' => $this->pageContentService->resolve('gallery'),
        ]);
    }
}
