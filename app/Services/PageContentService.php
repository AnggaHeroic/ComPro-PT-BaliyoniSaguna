<?php

namespace App\Services;

use App\Helpers\UploadHelper;
use App\Models\ActivityLog;
use App\Models\Page;
use Illuminate\Http\UploadedFile;
use Throwable;

/**
 * Isi halaman publik yang bisa diubah admin (menu Isi Halaman).
 *
 * Definisi kolom dan teks bawaan ada di config/page_contents.php. Hanya isi yang berbeda dari
 * acuan (setelan default kustom, atau teks bawaan) yang disimpan di tabel pages.
 */
class PageContentService
{
    private const IMAGE_DIRECTORY = 'pages';

    /** Isi halaman yang sudah dibaca pada permintaan ini (service ini singleton), supaya logo dan kontak tidak mengulang query. @var array<string, PageContentSet> */
    private array $resolved = [];

    /** Isian form yang sedang dipratinjau per halaman (hanya terisi selama previewing() berjalan). @var array<string, array<string, mixed>> */
    private array $preview = [];

    public function __construct(private ActivityLogService $logService) {}

    /** @return array<string, array<string, mixed>> */
    public function definitions(): array
    {
        return config('page_contents');
    }

    /**
     * Halaman yang bisa diubah admin (kunci => label), tanpa data bersama "Umum"; urutannya sama dengan navbar website.
     *
     * @return array<string, string>
     */
    public function pages(): array
    {
        $labels = array_map(fn (array $page) => $page['label'], $this->definitions());

        return array_diff_key($labels, ['general' => true]);
    }

    /**
     * Semua halaman beserta "Umum" untuk daftar pintasan pengelolaan (kunci => label).
     *
     * @return array<string, string>
     */
    public function menu(): array
    {
        return array_map(fn (array $page) => $page['label'], $this->definitions());
    }

    public function has(string $slug): bool
    {
        return array_key_exists($slug, $this->definitions());
    }

    /** @return array<string, mixed> */
    public function definition(string $slug): array
    {
        return $this->definitions()[$slug] ?? [];
    }

    /**
     * Semua kolom satu halaman dalam bentuk datar: kunci => definisi.
     *
     * @return array<string, array<string, mixed>>
     */
    public function fields(string $slug): array
    {
        $fields = [];

        foreach ($this->definition($slug)['sections'] ?? [] as $section) {
            $fields += $section['fields'];
        }

        return $fields;
    }

    /** Baris halaman untuk admin; dibuat bila belum ada (tanpa dicatat di riwayat). */
    public function find(string $slug): Page
    {
        return Page::withoutEvents(fn () => Page::firstOrCreate(['slug' => $slug]));
    }

    /**
     * Isi halaman untuk view publik. Selama pratinjau (lihat previewing()), isian form yang belum
     * disimpan ikut dipakai sementara dan tidak pernah ditulis ke database.
     */
    public function resolve(string $slug): PageContentSet
    {
        $preview = $this->preview[$slug] ?? null;

        if ($preview === null && isset($this->resolved[$slug])) {
            return $this->resolved[$slug];
        }

        $page = Page::where('slug', $slug)->first();

        $set = new PageContentSet(
            $this->fields($slug),
            array_keys($this->definition($slug)['sections'] ?? []),
            [...($page?->content ?? []), ...($preview ?? [])],
            $page?->defaults ?? [],
            $preview !== null,
        );

        return $preview === null ? $this->resolved[$slug] = $set : $set;
    }

    /**
     * Jalankan $render dengan isian form yang belum disimpan berlaku untuk SEMUA pembaca isi halaman
     * (resolve() dan view komponen seperti logo dan CTA), lalu pulihkan seperti semula.
     *
     * @param  array<string, mixed>  $submitted  isian content[...] dari form admin
     */
    public function previewing(string $slug, array $submitted, callable $render): mixed
    {
        $this->preview[$slug] = $this->previewValues($slug, $submitted);
        $this->resolved = [];

        try {
            return $render();
        } finally {
            unset($this->preview[$slug]);
            $this->resolved = [];
        }
    }

    /**
     * Nama target pratinjau yang sah: bagian yang tampil di halaman, atau kelompok variasi bila bagian dibagi kelompok.
     *
     * @return list<string>
     */
    public function previewTargets(string $slug): array
    {
        $targets = [];

        foreach ($this->definition($slug)['sections'] ?? [] as $key => $section) {
            if (isset($section['groups'])) {
                array_push($targets, ...array_column($section['groups'], 'preview'));
            } elseif (is_string($section['preview'] ?? null)) {
                $targets[] = $section['preview'];
            } elseif ($section['preview'] ?? true) {
                $targets[] = $key;
            }
        }

        return $targets;
    }

    /**
     * Rapikan isian form yang belum disimpan menjadi nilai yang aman dirender untuk pratinjau.
     * Isian yang tidak lolos (mis. kolom wajib yang sedang dikosongkan) dilewati agar pratinjau tidak rusak.
     *
     * @param  array<string, mixed>  $submitted
     * @return array<string, mixed>
     */
    public function previewValues(string $slug, array $submitted): array
    {
        $values = [];

        foreach ($this->fields($slug) as $key => $field) {
            $type = $field['type'] ?? 'text';

            if ($type === 'image' || ! array_key_exists($key, $submitted)) {
                continue;
            }

            if ($type === 'cards') {
                $cards = $this->normalizeCards($submitted[$key], $field);

                if ($cards !== []) {
                    $values[$key] = $cards;
                }

                continue;
            }

            $text = mb_substr($this->normalizeText($submitted[$key]), 0, (int) ($field['max'] ?? 200));

            if ($text !== '' || ($field['optional'] ?? false)) {
                $values[$key] = $text;
            }
        }

        return $values;
    }

    /**
     * Simpan isian form. Kolom yang sama dengan acuan tidak disimpan (barisnya dihapus), sehingga
     * perubahan teks bawaan atau default kustom otomatis ikut ke halaman yang belum pernah diubah.
     *
     * @param  array<string, mixed>  $submitted  kunci => isian teks atau daftar kartu
     * @param  array<string, UploadedFile>  $images  kunci => file gambar baru
     * @param  list<string>  $revertImages  kunci gambar yang dikembalikan ke gambar default
     */
    public function update(Page $page, array $submitted, array $images = [], array $revertImages = []): void
    {
        $content = $page->content ?? [];
        $before = $this->imagePaths($page);
        $uploaded = [];

        try {
            foreach ($this->fields($page->slug) as $key => $field) {
                $type = $field['type'] ?? 'text';

                if ($type === 'image') {
                    if (isset($images[$key])) {
                        $content[$key] = $uploaded[] = UploadHelper::storeImage($images[$key], self::IMAGE_DIRECTORY);
                    } elseif (in_array($key, $revertImages, true)) {
                        unset($content[$key]);
                    }

                    continue;
                }

                if (! array_key_exists($key, $submitted)) {
                    continue;
                }

                $value = $type === 'cards'
                    ? $this->normalizeCards($submitted[$key], $field)
                    : $this->normalizeText($submitted[$key]);

                if ($value === $this->defaultFor($page, $key)) {
                    unset($content[$key]);
                } else {
                    $content[$key] = $value;
                }
            }

            $page->update(['content' => $content === [] ? null : $content]);
            $this->resolved = [];
        } catch (Throwable $e) {
            // Gambar yang baru diunggah tapi gagal tercatat tidak boleh tertinggal.
            array_map(UploadHelper::delete(...), $uploaded);

            throw $e;
        }

        $this->deleteUnusedImages($page, $before);
    }

    /** Kembalikan seluruh isi halaman ke setelan default (kustom bila ada, selain itu bawaan). */
    public function reset(Page $page): void
    {
        $before = $this->imagePaths($page);

        // Dicatat sebagai "Reset default", bukan perubahan biasa, jadi catatan otomatis model dilewati.
        Page::withoutEvents(fn () => $page->update(['content' => null]));
        $this->resolved = [];
        $this->logService->record(ActivityLog::ACTION_RESET, Page::class, $page->getKey(), $page->activityLabel());

        $this->deleteUnusedImages($page, $before);
    }

    /**
     * Jadikan isi tersimpan saat ini sebagai setelan default baru. Tampilan website tidak berubah;
     * hanya acuan "Reset default" yang berganti.
     */
    public function setDefault(Page $page): void
    {
        $before = $this->imagePaths($page);
        $defaults = [];

        foreach ($this->fields($page->slug) as $key => $field) {
            $value = $page->content[$key] ?? $page->defaults[$key] ?? null;

            // Yang sama dengan teks bawaan sistem tidak perlu disimpan sebagai default kustom.
            if ($value !== null && $value !== ($field['default'] ?? null)) {
                $defaults[$key] = $value;
            }
        }

        Page::withoutEvents(fn () => $page->update([
            'content' => null,
            'defaults' => $defaults === [] ? null : $defaults,
            'defaults_set_at' => $defaults === [] ? null : now(),
        ]));
        $this->resolved = [];
        $this->logService->record(ActivityLog::ACTION_DEFAULT, Page::class, $page->getKey(), $page->activityLabel());

        $this->deleteUnusedImages($page, $before);
    }

    /**
     * Hapus setelan default kustom (acuan kembali ke teks bawaan) tanpa mengubah tampilan website:
     * isi yang selama ini hanya mengikuti default kustom dipindahkan menjadi isi tersimpan.
     */
    public function clearDefault(Page $page): void
    {
        Page::withoutEvents(fn () => $page->update([
            'content' => [...($page->defaults ?? []), ...($page->content ?? [])] ?: null,
            'defaults' => null,
            'defaults_set_at' => null,
        ]));
        $this->resolved = [];
        $this->logService->record(ActivityLog::ACTION_DEFAULT_CLEARED, Page::class, $page->getKey(), $page->activityLabel());
    }

    /**
     * Bagian pertama yang memuat galat validasi, supaya form langsung membuka bagian itu.
     *
     * @param  list<string>  $errorKeys  kunci galat validasi (mis. content.hero_title)
     */
    public function sectionWithErrors(string $slug, array $errorKeys): ?string
    {
        foreach ($this->definition($slug)['sections'] ?? [] as $sectionKey => $section) {
            foreach (array_keys($section['fields']) as $key) {
                foreach ($errorKeys as $errorKey) {
                    if (in_array($errorKey, ["content.{$key}", "image_{$key}"], true) || str_starts_with($errorKey, "content.{$key}.")) {
                        return $sectionKey;
                    }
                }
            }
        }

        return null;
    }

    /** Nilai acuan sebuah kolom: default kustom bila ada, selain itu teks bawaan. */
    private function defaultFor(Page $page, string $key): mixed
    {
        $field = $this->fields($page->slug)[$key];
        $value = $page->defaults[$key] ?? $field['default'] ?? '';

        return ($field['type'] ?? 'text') === 'cards' ? $this->normalizeCards($value, $field) : (string) $value;
    }

    private function normalizeText(mixed $value): string
    {
        // Baris baru dari browser (\r\n) disamakan agar bisa dibandingkan dengan teks bawaan.
        return trim(str_replace(["\r\n", "\r"], "\n", (string) $value));
    }

    /**
     * @param  array<string, mixed>  $field
     * @return list<array{title: string, text: string}>
     */
    private function normalizeCards(mixed $items, array $field): array
    {
        $cards = [];

        foreach ((array) $items as $item) {
            $card = [
                'title' => is_array($item) ? $this->normalizeText($item['title'] ?? '') : '',
                'text' => is_array($item) ? $this->normalizeText($item['text'] ?? '') : '',
            ];

            if ($card['title'] !== '' || $card['text'] !== '') {
                $cards[] = $card;
            }
        }

        return array_slice($cards, 0, (int) ($field['max_items'] ?? 10));
    }

    /**
     * Path gambar unggahan yang sedang dipakai halaman (isi tersimpan dan default kustom).
     *
     * @return list<string>
     */
    private function imagePaths(Page $page): array
    {
        $paths = [];

        foreach ($this->fields($page->slug) as $key => $field) {
            if (($field['type'] ?? 'text') !== 'image') {
                continue;
            }

            foreach ([$page->content[$key] ?? null, $page->defaults[$key] ?? null] as $path) {
                if (filled($path)) {
                    $paths[] = $path;
                }
            }
        }

        return $paths;
    }

    /** Hapus file gambar yang tidak lagi dipakai oleh isi tersimpan maupun default kustom. @param list<string> $candidates */
    private function deleteUnusedImages(Page $page, array $candidates): void
    {
        $inUse = $this->imagePaths($page);

        foreach (array_unique($candidates) as $path) {
            if (! in_array($path, $inUse, true)) {
                UploadHelper::delete($path);
            }
        }
    }
}
