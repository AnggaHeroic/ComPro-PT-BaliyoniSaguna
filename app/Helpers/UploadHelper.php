<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class UploadHelper
{
    /** Aturan validasi standar untuk unggahan gambar. */
    public const IMAGE_RULES = ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];

    private const MAX_WIDTH = 1600;

    /**
     * Simpan gambar ke disk public sebagai WebP yang diperkecil (lebar maksimum $maxWidth, default 1600 px).
     * Bila GD/WebP tidak tersedia atau gambar tidak terbaca, file asli disimpan apa adanya.
     */
    public static function storeImage(UploadedFile $file, string $directory, int $maxWidth = self::MAX_WIDTH): string
    {
        $webp = self::toWebp($file, $maxWidth);

        if ($webp === null) {
            return self::store($file, $directory);
        }

        $path = trim($directory, '/').'/'.Str::random(40).'.webp';

        return Storage::disk('public')->put($path, $webp)
            ? $path
            : throw new RuntimeException('Gagal menyimpan file.');
    }

    public static function store(UploadedFile $file, string $directory): string
    {
        return $file->store($directory, 'public')
            ?: throw new RuntimeException('Gagal menyimpan file.');
    }

    /**
     * Hapus file hasil unggahan. URL penuh (foto placeholder) dan nilai kosong diabaikan.
     */
    public static function delete(?string $path): void
    {
        if (blank($path) || str_starts_with($path, 'http')) {
            return;
        }

        Storage::disk('public')->delete($path);
    }

    private static function toWebp(UploadedFile $file, int $maxWidth): ?string
    {
        if (! function_exists('imagewebp')) {
            return null;
        }

        $source = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if ($source === false) {
            return null;
        }

        $width = imagesx($source);

        if ($width > $maxWidth) {
            $source = imagescale($source, $maxWidth) ?: $source;
        }

        // Pertahankan transparansi PNG.
        imagepalettetotruecolor($source);
        imagealphablending($source, true);
        imagesavealpha($source, true);

        ob_start();
        imagewebp($source, null, 82);
        $binary = ob_get_clean();

        return $binary === false || $binary === '' ? null : $binary;
    }
}
