<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Ringkasan untuk dashboard admin. Kategori dan aktivitas dihitung dari database;
 * foto galeri dan menu navigasi masih dari config sampai modulnya dibuat.
 */
class DashboardService
{
    public function __construct(
        private CompanyService $companyService,
        private ServiceCategoryService $categoryService,
        private BusinessActivityService $activityService,
    ) {}

    /**
     * @return array{today: string, stats: list<array{label: string, value: int, hint: string, icon: string}>}
     */
    public function summary(): array
    {
        $categories = $this->categoryService->statusCounts();
        $activities = $this->activityService->statusCounts();

        return [
            'today' => Carbon::now()->locale('id')->translatedFormat('l, j F Y'),
            'stats' => [
                ['label' => 'Kategori Layanan', 'value' => $categories['total'], 'hint' => $this->statusHint($categories), 'icon' => 'layers'],
                ['label' => 'Aktivitas Usaha', 'value' => $activities['total'], 'hint' => $this->statusHint($activities), 'icon' => 'briefcase'],
                ['label' => 'Foto Galeri', 'value' => count($this->companyService->gallery()), 'hint' => 'Dokumentasi tampil di website', 'icon' => 'image'],
                ['label' => 'Halaman Publik', 'value' => count($this->companyService->navigation()), 'hint' => 'Menu navigasi website', 'icon' => 'dashboard'],
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
