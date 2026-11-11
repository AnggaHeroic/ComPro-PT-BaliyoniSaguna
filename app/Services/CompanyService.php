<?php

namespace App\Services;

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

    // Tautan pencarian Google Maps dari alamat; null jika alamat kosong.
    private function mapsUrl(?string $address): ?string
    {
        return filled($address)
            ? 'https://www.google.com/maps/search/?api=1&query='.urlencode($address)
            : null;
    }
}
