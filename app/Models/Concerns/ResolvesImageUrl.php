<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

trait ResolvesImageUrl
{
    /**
     * URL penuh dipakai apa adanya (foto placeholder bawaan); selain itu dianggap path di disk public.
     */
    protected function resolveImageUrl(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        return str_starts_with($value, 'http') ? $value : Storage::disk('public')->url($value);
    }
}
