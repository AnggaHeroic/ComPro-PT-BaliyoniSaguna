<?php

namespace App\Services;

use App\Models\BusinessActivity;
use App\Models\GalleryPhoto;
use App\Models\ServiceCategory;

/**
 * Sumber data company profile untuk halaman publik.
 *
 * Kategori dan aktivitas usaha dibaca dari database (dikelola di CMS admin). Bagian lain
 * (profil, navigasi, galeri, tentang kami) masih dari config/company.php sampai modulnya dibuat.
 */
class CompanyService
{
    /** @var list<array<string, mixed>>|null */
    private ?array $services = null;

    public function __construct(
        private ServiceCategoryService $categoryService,
        private GalleryPhotoService $galleryService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function profile(): array
    {
        return [
            'name' => config('company.name'),
            'short_name' => config('company.short_name'),
            'tagline' => config('company.tagline'),
            'description' => config('company.description'),
            'city' => config('company.city'),
            'email' => config('company.email'),
            'phone' => config('company.phone'),
            'phone_href' => config('company.phone_href'),
            'mobile' => config('company.mobile'),
            // Nomor HP dibuka lewat WhatsApp (wa.me), jadi hanya digit tanpa tanda +.
            'mobile_url' => filled(config('company.mobile_href')) ? 'https://wa.me/'.ltrim(config('company.mobile_href'), '+') : null,
            'address' => config('company.address'),
            'maps_url' => $this->mapsUrl(config('company.address')),
            // Footer hanya menampilkan kolom kontak jika minimal satu data kontak terisi.
            'has_contact' => filled(config('company.email')) || filled(config('company.phone')) || filled(config('company.mobile')) || filled(config('company.address')),
        ];
    }

    /**
     * @return list<array{label: string, href: string, match: ?string, mega?: bool}>
     */
    public function navigation(): array
    {
        return config('company.nav');
    }

    /**
     * Kategori terbit beserta aktivitas terbitnya (dari database), urut sesuai pengaturan admin.
     * 'image' kategori/aktivitas berupa URL penuh atau null; aktivitas tanpa foto memakai foto kategorinya.
     *
     * @return list<array{name: string, slug: string, icon: string, icon_image: ?string, image: ?string, description: string, activities: list<array{name: string, slug: string, kbli: ?string, summary: ?string, detail: ?list<string>, image: ?string}>}>
     */
    public function services(): array
    {
        // Dipanggil beberapa kali per permintaan (navbar, footer, halaman), jadi hasilnya disimpan.
        return $this->services ??= $this->categoryService->publishedWithActivities()
            ->map(fn (ServiceCategory $category) => [
                'name' => $category->name,
                'slug' => $category->slug,
                'icon' => $category->icon,
                'icon_image' => $category->icon_image_url,
                'image' => $category->image_url,
                'description' => (string) $category->description,
                'activities' => $category->activities->map(fn (BusinessActivity $activity) => [
                    'name' => $activity->name,
                    'slug' => $activity->slug,
                    'kbli' => $activity->kbli_code,
                    'summary' => $activity->summary,
                    'detail' => $this->paragraphs($activity->detail),
                    'image' => $activity->image_url ?? $category->image_url,
                ])->values()->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * Satu aktivitas usaha beserta kategorinya, nomor urut, dan aktivitas sebelum/sesudahnya; null bila tidak ditemukan.
     * Pencarian memakai slug nama (sama dengan yang dipakai di URL).
     *
     * @return array{service: array<string, mixed>, activity: array<string, mixed>, paragraphs: list<string>, number: int, activities: list<array<string, mixed>>, previous: ?array<string, mixed>, next: ?array<string, mixed>}|null
     */
    public function findActivity(string $serviceSlug, string $activitySlug): ?array
    {
        $service = collect($this->services())->first(fn (array $item) => $item['slug'] === $serviceSlug);

        if ($service === null) {
            return null;
        }

        $activities = array_values($service['activities']);

        foreach ($activities as $index => $activity) {
            if ($activity['slug'] === $activitySlug) {
                return [
                    'service' => $service,
                    'activity' => $activity,
                    'paragraphs' => $this->activityParagraphs($service, $activity),
                    'number' => $index + 1,
                    'activities' => $activities,
                    'previous' => $activities[$index - 1] ?? null,
                    'next' => $activities[$index + 1] ?? null,
                ];
            }
        }

        return null;
    }

    /**
     * Paragraf penjelasan aktivitas: dari 'detail' bila terisi (teks atau daftar paragraf), selain itu paragraf contoh dari config.
     *
     * @param  array<string, mixed>  $service
     * @param  array<string, mixed>  $activity
     * @return list<string>
     */
    private function activityParagraphs(array $service, array $activity): array
    {
        if (filled($activity['detail'] ?? null)) {
            return array_values((array) $activity['detail']);
        }

        return array_map(
            fn (string $text) => strtr($text, [':activity' => $activity['name'], ':category' => $service['name']]),
            config('company.activity_placeholder'),
        );
    }

    /**
     * Pecah teks detail menjadi paragraf (dipisah baris kosong); null bila kosong.
     *
     * @return ?list<string>
     */
    private function paragraphs(?string $text): ?array
    {
        if (blank($text)) {
            return null;
        }

        return array_values(array_filter(array_map('trim', preg_split('/\R{2,}/', trim($text)))));
    }

    /**
     * Foto galeri terbit (dari database) sesuai urutan yang diatur admin. 'image' berupa URL penuh.
     *
     * @return list<array{title: string, description: ?string, image: string}>
     */
    public function gallery(): array
    {
        return $this->galleryService->published()
            ->map(fn (GalleryPhoto $photo) => [
                'title' => $photo->title,
                'description' => $photo->description,
                'image' => $photo->image_url,
            ])
            ->values()
            ->all();
    }

    public function serviceCount(): int
    {
        return count($this->services());
    }

    /**
     * @return list<array{icon: string, title: string, text: string}>
     */
    public function advantages(): array
    {
        return config('company.advantages');
    }

    /**
     * Konten halaman Tentang Kami (profil, visi, misi, nilai).
     *
     * @return array{profile_image: ?int, profile: list<string>, facts: list<array{title: string, text: string}>, direction_tagline: string, vision: string, mission: list<array{title: string, text: string}>, motto: list<string>, values: list<array{title: string, text: string}>}
     */
    public function about(): array
    {
        return config('company.about');
    }

    // Tautan pencarian Google Maps dari alamat; null jika alamat kosong.
    private function mapsUrl(?string $address): ?string
    {
        return filled($address)
            ? 'https://www.google.com/maps/search/?api=1&query='.urlencode($address)
            : null;
    }
}
