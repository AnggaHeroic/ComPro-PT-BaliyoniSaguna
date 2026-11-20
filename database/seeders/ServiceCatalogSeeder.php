<?php

namespace Database\Seeders;

use App\Models\BusinessActivity;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Mengisi kategori dan aktivitas usaha awal dari config/company.php (data PLACEHOLDER).
 * Aman dijalankan ulang: baris yang sudah ada (dicocokkan lewat slug) tidak digandakan
 * dan perubahan yang dibuat admin tidak ditimpa.
 */
class ServiceCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Tanpa event model agar pengisian awal tidak masuk riwayat aktivitas admin.
        ServiceCategory::withoutEvents(fn () => BusinessActivity::withoutEvents(function () {
            foreach (config('company.services') as $index => $service) {
                $category = ServiceCategory::firstOrCreate(
                    ['slug' => Str::slug($service['name'])],
                    [
                        'name' => $service['name'],
                        'description' => $service['description'] ?? null,
                        'icon' => $service['icon'],
                        // Foto placeholder dari Unsplash; admin bisa menggantinya dengan unggahan sendiri.
                        'image' => filled($service['image'] ?? null)
                            ? 'https://images.unsplash.com/photo-'.$service['image'].'?auto=format&fit=crop&w=1400&q=75'
                            : null,
                        'sort_order' => $index + 1,
                        'is_published' => true,
                    ],
                );

                foreach (array_values($service['activities']) as $position => $activity) {
                    BusinessActivity::firstOrCreate(
                        ['service_category_id' => $category->id, 'slug' => Str::slug($activity['name'])],
                        [
                            'name' => $activity['name'],
                            'summary' => $activity['summary'] ?? null,
                            'detail' => filled($activity['detail'] ?? null) ? implode("\n\n", (array) $activity['detail']) : null,
                            'sort_order' => $position + 1,
                            'is_published' => true,
                        ],
                    );
                }
            }
        }));
    }
}
