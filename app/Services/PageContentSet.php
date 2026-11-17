<?php

namespace App\Services;

use App\Models\Concerns\ResolvesImageUrl;
use Illuminate\Support\HtmlString;

/**
 * Isi satu halaman publik yang siap dibaca view: $content('kunci').
 * Nilai yang dipakai berurutan: isi tersimpan, setelan default kustom, lalu teks bawaan di config/page_contents.php.
 * Objek ini hanya membaca; dibuat oleh PageContentService::resolve().
 */
class PageContentSet
{
    use ResolvesImageUrl;

    /**
     * @param  array<string, array<string, mixed>>  $fields  kunci => definisi kolom (datar, semua bagian)
     * @param  list<string>  $sections  kunci bagian yang ada di halaman ini
     * @param  array<string, mixed>  $stored  isi yang diubah admin
     * @param  array<string, mixed>  $defaults  setelan default kustom
     */
    public function __construct(
        private readonly array $fields,
        private readonly array $sections,
        private readonly array $stored = [],
        private readonly array $defaults = [],
        private readonly bool $previewing = false,
    ) {}

    public function __invoke(string $key): string
    {
        return $this->get($key);
    }

    public function get(string $key): string
    {
        return (string) $this->value($key);
    }

    /**
     * Teks judul dengan penanda warna: *kata* tampil dengan $class, baris baru menjadi <br>.
     * Teks di-escape lebih dulu, jadi HTML dari admin tidak pernah dieksekusi.
     */
    public function rich(string $key, string $class = 'text-brand'): HtmlString
    {
        $html = e(str_replace(["\r\n", "\r"], "\n", $this->get($key)));
        $html = preg_replace('/\*(.+?)\*/s', '<span class="'.e($class).'">$1</span>', $html);

        return new HtmlString(nl2br($html, false));
    }

    /**
     * @return list<array{title: string, text: string}>
     */
    public function cards(string $key): array
    {
        return $this->cardList($this->stored[$key] ?? null)
            ?? $this->cardList($this->defaults[$key] ?? null)
            ?? $this->cardList($this->fields[$key]['default'] ?? null)
            ?? [];
    }

    /** URL gambar yang dipakai: unggahan admin, lalu default kustom, lalu gambar bawaan. */
    public function image(string $key): string
    {
        return $this->resolveImageUrl($this->customImagePath($key)) ?? $this->defaultImage($key);
    }

    /** URL gambar default: default kustom bila ada, selain itu gambar bawaan. */
    public function defaultImage(string $key): string
    {
        return $this->resolveImageUrl($this->defaults[$key] ?? null)
            ?? $this->bundledImageUrl((string) ($this->fields[$key]['default'] ?? ''))
            ?? '';
    }

    /** Path gambar unggahan admin, atau null bila halaman memakai gambar default. */
    public function customImagePath(string $key): ?string
    {
        return filled($this->stored[$key] ?? null) ? $this->stored[$key] : null;
    }

    /** Nilai default yang berlaku: default kustom bila ada, selain itu bawaan. Teks => string; kartu => array. */
    public function defaultValue(string $key): mixed
    {
        if (($this->fields[$key]['type'] ?? 'text') === 'cards') {
            return $this->cardList($this->defaults[$key] ?? null) ?? $this->cardList($this->fields[$key]['default'] ?? null) ?? [];
        }

        return (string) ($this->defaults[$key] ?? $this->fields[$key]['default'] ?? '');
    }

    public function hasCustomDefaults(): bool
    {
        return $this->defaults !== [];
    }

    /** Penanda bagian untuk pratinjau; kosong di halaman publik biasa. */
    public function mark(string $section): HtmlString
    {
        if (! $this->previewing || ! in_array($section, $this->sections, true)) {
            return new HtmlString('');
        }

        return new HtmlString(' data-cms-section="'.e($section).'"');
    }

    /** Gambar bawaan: URL penuh apa adanya, atau path berawalan / yang menunjuk berkas di folder public. */
    /** Nama bagian untuk atribut data-cms-section komponen; null (atribut tidak dirender) di halaman publik biasa. */
    public function sectionKey(string $section): ?string
    {
        return $this->previewing && in_array($section, $this->sections, true) ? $section : null;
    }

    private function bundledImageUrl(string $default): ?string
    {
        return str_starts_with($default, '/') ? asset(ltrim($default, '/')) : $this->resolveImageUrl($default);
    }

    private function value(string $key): mixed
    {
        // Kolom opsional boleh disimpan kosong (artinya disembunyikan); selebihnya kosong = default.
        if (array_key_exists($key, $this->stored)) {
            return $this->stored[$key];
        }

        return $this->defaults[$key] ?? $this->fields[$key]['default'] ?? '';
    }

    /**
     * @return list<array{title: string, text: string}>|null null bila bukan daftar kartu yang valid
     */
    private function cardList(mixed $items): ?array
    {
        if (! is_array($items)) {
            return null;
        }

        $cards = [];

        foreach ($items as $item) {
            if (is_array($item) && is_string($item['title'] ?? null) && is_string($item['text'] ?? null)) {
                $cards[] = ['title' => $item['title'], 'text' => $item['text']];
            }
        }

        return $cards === [] ? null : $cards;
    }
}
