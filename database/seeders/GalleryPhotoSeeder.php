<?php

namespace Database\Seeders;

use App\Models\GalleryPhoto;
use Illuminate\Database\Seeder;

/**
 * Mengisi galeri awal dari config/company.php (foto PLACEHOLDER Unsplash). Hanya berjalan bila tabel
 * masih kosong, sehingga foto yang diunggah atau diatur admin tidak tertimpa saat seeder dijalankan ulang.
 */
class GalleryPhotoSeeder extends Seeder
{
    public function run(): void
    {
        if (GalleryPhoto::query()->exists()) {
            return;
        }

        // Tanpa event model agar pengisian awal tidak masuk riwayat aktivitas admin.
        GalleryPhoto::withoutEvents(function () {
            foreach (config('company.gallery') as $index => $photo) {
                GalleryPhoto::create([
                    'title' => $photo['title'],
                    'description' => $photo['description'] ?? null,
                    'image' => 'https://images.unsplash.com/photo-'.$photo['image'].'?auto=format&fit=crop&w=1400&q=75',
                    'sort_order' => $index + 1,
                    'is_published' => true,
                ]);
            }
        });
    }
}
