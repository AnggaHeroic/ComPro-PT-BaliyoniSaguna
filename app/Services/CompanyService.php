<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Sumber data company profile untuk halaman publik.
 *
 * Saat ini data dibaca dari config/company.php. Ketika data dipindah ke database (CMS),
 * cukup ubah isi service ini; controller dan view tidak perlu berubah.
 */
class CompanyService
{
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
     * @return list<array{name: string, icon: string, image: ?int, description: string}>
     */
    public function services(): array
    {
        return config('company.services');
    }

    /**
     * Satu aktivitas usaha beserta kategorinya, nomor urut, dan aktivitas sebelum/sesudahnya; null bila tidak ditemukan.
     * Pencarian memakai slug nama (sama dengan yang dipakai di URL).
     *
     * @return array{service: array<string, mixed>, activity: array<string, mixed>, paragraphs: list<string>, number: int, activities: list<array<string, mixed>>, previous: ?array<string, mixed>, next: ?array<string, mixed>}|null
     */
    public function findActivity(string $serviceSlug, string $activitySlug): ?array
    {
        $service = collect($this->services())->first(fn (array $item) => Str::slug($item['name']) === $serviceSlug);

        if ($service === null) {
            return null;
        }

        $activities = array_values($service['activities']);

        foreach ($activities as $index => $activity) {
            if (Str::slug($activity['name']) === $activitySlug) {
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
     * Foto galeri (sementara dari config; nantinya dari database CMS).
     *
     * @return list<array{title: string, description: string, image: string}>
     */
    public function gallery(): array
    {
        return config('company.gallery');
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
