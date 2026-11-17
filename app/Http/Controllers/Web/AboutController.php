<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\PageContentService;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __construct(private PageContentService $pageContentService) {}

    public function index(): View
    {
        return view('web.about', ['content' => $this->pageContentService->resolve('about')]);
    }
}
