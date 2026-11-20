<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Konten awal website yang dikelola di CMS (kategori, aktivitas usaha, galeri) dari config/company.php.
 */
class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([ServiceCatalogSeeder::class, GalleryPhotoSeeder::class]);
    }
}
