<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SlugHelper
{
    /**
     * Buat slug dari nama dan pastikan unik; bila sudah dipakai ditambah -2, -3, dst.
     *
     * @param  callable(string): bool  $exists  mengembalikan true bila slug sudah terpakai
     */
    public static function unique(string $name, callable $exists, int $maxLength = 120): string
    {
        $base = rtrim(Str::limit(Str::slug($name), $maxLength - 6, ''), '-');
        $base = $base !== '' ? $base : 'item';

        $slug = $base;
        $counter = 2;

        while ($exists($slug)) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }
}
