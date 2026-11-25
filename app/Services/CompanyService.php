<?php

namespace App\Services;

use App\Models\BusinessActivity;
use App\Models\GalleryPhoto;
use App\Models\ServiceCategory;
use Illuminate\Support\HtmlString;

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
        private PageContentService $pageContentService,
    ) {}

    /**
     * Profil perusahaan untuk navbar, footer, CTA, dan halaman Kontak. Diatur admin lewat Isi Halaman > Umum.
     *
     * @return array<string, mixed>
     */
    public function profile(): array
    {
        $general = $this->pageContentService->resolve('general');
        $email = trim($general('email'));
        $phone = trim($general('phone'));
        $mobile = trim($general('mobile'));
        $address = trim($general('address'));

        return [
            'name' => $general('site_name'),
            'tagline' => $general('tagline'),
            'description' => $general('footer_description'),
            'city' => $general('city'),
            'email' => $email,
            'phone' => $phone,
            'phone_href' => $phone === '' ? null : '+'.$this->internationalDigits($phone),
            'mobile' => $mobile,
            // Nomor HP dibuka lewat WhatsApp (wa.me), jadi hanya digit tanpa tanda +.
            'mobile_url' => $mobile === '' ? null : 'https://wa.me/'.$this->internationalDigits($mobile),
            'address' => $address,
            'maps_url' => $this->mapsUrl($address),
            // Footer hanya menampilkan kolom kontak jika minimal satu data kontak terisi.
            'has_contact' => $email !== '' || $phone !== '' || $mobile !== '' || $address !== '',
        ];
    }

    /**
     * Teks ajakan menghubungi untuk kedua variasinya (kartu dan banner), diatur lewat Isi Halaman > Umum.
     * Judul berwarna: kata bertanda * memakai warna aksen yang sesuai latar variasinya.
     *
     * @return array<string, array{eyebrow: string, title: HtmlString, description: string, button: string, phone_button: string, image?: string}>
     */
    public function cta(): array
    {
        $general = $this->pageContentService->resolve('general');
        $variants = ['card' => 'text-brand', 'banner' => 'whitespace-nowrap text-brand-bright'];
        $cta = [];

        foreach ($variants as $variant => $accent) {
            $cta[$variant] = [
                'eyebrow' => $general("cta_{$variant}_eyebrow"),
                'title' => $general->rich("cta_{$variant}_title", $accent),
                'description' => $general("cta_{$variant}_description"),
                'button' => $general("cta_{$variant}_button"),
                'phone_button' => $general("cta_{$variant}_button_phone"),
            ];
        }

        // Hanya banner yang berlatar foto.
        $cta['banner']['image'] = $general->image('cta_banner_image');

        return $cta;
    }

    /**
     * Logo dan tulisan di sampingnya (wordmark) yang dipakai navbar, footer, halaman login, dan menu admin.
     *
     * @return array{logo_url: string, wordmark: HtmlString}
     */
    public function brand(): array
    {
        $general = $this->pageContentService->resolve('general');

        return [
            'logo_url' => $general->image('logo'),
            'wordmark' => $general->rich('brand_text', 'text-brand-bright'),
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
     * @return list<array{name: string, slug: string, icon: string, icon_image: ?string, image: ?string, description: string, activities: list<array{name: string, slug: string, summary: ?string, detail: ?list<string>, image: ?string}>}>
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
     * Data halaman publik di luar isi teks yang diatur admin (dipakai controller publik dan pratinjau admin).
     *
     * @return array<string, mixed>
     */
    public function pageData(string $slug): array
    {
        return match ($slug) {
            'home' => $this->homePage(),
            'gallery' => ['gallery' => $this->gallery()],
            'contact' => ['subject' => ''],
            default => [],
        };
    }

    /**
     * Data khusus halaman Beranda (selain isi teks yang diatur admin).
     *
     * @return array{serviceCount: int, advantageIcons: list<string>}
     */
    public function homePage(): array
    {
        return [
            'serviceCount' => $this->serviceCount(),
            'advantageIcons' => config('company.advantage_icons'),
        ];
    }

    /** Nomor telepon dalam format internasional (hanya digit): awalan 0 menjadi 62. */
    private function internationalDigits(string $number): string
    {
        $digits = preg_replace('/\D/', '', $number);

        return str_starts_with($digits, '0') ? '62'.substr($digits, 1) : $digits;
    }

    // Tautan pencarian Google Maps dari alamat; null jika alamat kosong.
    private function mapsUrl(?string $address): ?string
    {
        return filled($address)
            ? 'https://www.google.com/maps/search/?api=1&query='.urlencode($address)
            : null;
    }
}
