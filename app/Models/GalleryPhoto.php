<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\ResolvesImageUrl;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'description', 'image', 'sort_order', 'is_published'])]
class GalleryPhoto extends Model
{
    use LogsActivity, ResolvesImageUrl;

    /**
     * Ukuran kotak pada kisi bento (layar lebar, 4 kolom), diulang per foto: besar (2x2), kecil, tinggi (1x2),
     * kecil, lebar (2x1), lebar. Blok kedua dicerminkan agar tidak terlihat berulang; setiap kelipatan 6 foto
     * membentuk blok utuh tanpa lubang. Dipakai halaman publik dan pratinjau admin agar tampilannya sama.
     */
    public const BENTO_SPANS = [
        'lg:col-span-2 lg:row-span-2', '', 'lg:row-span-2', '', 'lg:col-span-2', 'lg:col-span-2',
        'lg:row-span-2', '', 'lg:col-span-2 lg:row-span-2', '', 'lg:col-span-2', 'lg:col-span-2',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function activityLabel(): string
    {
        return $this->title;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->resolveImageUrl($this->image));
    }
}
