<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Ringkasan untuk dashboard admin. Saat ini dihitung dari data company (config);
 * ketika konten pindah ke database, cukup ubah isi service ini.
 */
class DashboardService
{
    public function __construct(private CompanyService $companyService) {}

    /**
     * @return array{today: string, stats: list<array{label: string, value: int, hint: string, icon: string}>}
     */
    public function summary(): array
    {
        $services = $this->companyService->services();

        return [
            'today' => Carbon::now()->locale('id')->translatedFormat('l, j F Y'),
            'stats' => [
                ['label' => 'Kategori Layanan', 'value' => count($services), 'hint' => 'Produk dan layanan', 'icon' => 'layers'],
                ['label' => 'Aktivitas Usaha', 'value' => (int) collect($services)->sum(fn (array $service) => count($service['activities'])), 'hint' => 'Di seluruh kategori', 'icon' => 'briefcase'],
                ['label' => 'Foto Galeri', 'value' => count($this->companyService->gallery()), 'hint' => 'Dokumentasi tampil di website', 'icon' => 'image'],
                ['label' => 'Halaman Publik', 'value' => count($this->companyService->navigation()), 'hint' => 'Menu navigasi website', 'icon' => 'dashboard'],
            ],
        ];
    }
}
