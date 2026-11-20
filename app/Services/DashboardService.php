<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Ringkasan untuk dashboard admin. Tiap kartu membawa nama rute menu pengelolaannya agar bisa diklik.
 */
class DashboardService
{
    public function __construct(
        private CompanyService $companyService,
        private ServiceCategoryService $categoryService,
        private BusinessActivityService $activityService,
        private PageContentService $pageContentService,
    ) {}

    /**
     * @return array{today: string, stats: list<array{label: string, value: int, hint: string, icon: string, route: string, params: array<string, string>}>}
     */
    public function summary(): array
    {
        $categories = $this->categoryService->statusCounts();
        $activities = $this->activityService->statusCounts();

        return [
            'today' => Carbon::now()->locale('id')->translatedFormat('l, j F Y'),
            'stats' => [
                ['label' => 'Kategori Layanan', 'value' => $categories['total'], 'hint' => $this->statusHint($categories), 'icon' => 'layers', 'route' => 'admin.categories.index', 'params' => []],
                ['label' => 'Aktivitas Usaha', 'value' => $activities['total'], 'hint' => $this->statusHint($activities), 'icon' => 'briefcase', 'route' => 'admin.activities.index', 'params' => []],
                ['label' => 'Foto Galeri', 'value' => count($this->companyService->gallery()), 'hint' => 'Dokumentasi tampil di website', 'icon' => 'image', 'route' => 'admin.gallery.index', 'params' => []],
                ['label' => 'Halaman Publik', 'value' => count($this->pageContentService->pages()), 'hint' => 'Teks dan foto bisa diubah', 'icon' => 'dashboard', 'route' => 'admin.pages.edit', 'params' => ['page' => array_key_first($this->pageContentService->pages())]],
            ],
        ];
    }

    /**
     * @param  array{total: int, published: int, draft: int}  $counts
     */
    private function statusHint(array $counts): string
    {
        return "{$counts['published']} terbit, {$counts['draft']} draft";
    }
}
