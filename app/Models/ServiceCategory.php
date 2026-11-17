<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\ResolvesImageUrl;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'description', 'icon', 'icon_image', 'image', 'sort_order', 'is_published'])]
class ServiceCategory extends Model
{
    use LogsActivity, ResolvesImageUrl;

    /** Ikon yang tersedia (kunci pada components/web/service-icon => label di formulir). */
    public const ICONS = [
        'electronics' => 'Elektronik',
        'stationery' => 'Alat Tulis',
        'sports' => 'Olahraga',
        'office' => 'Kantor',
        'education' => 'Pendidikan',
        'gift' => 'Hadiah',
        'food' => 'Makanan',
        'tools' => 'Perkakas',
        'apparel' => 'Pakaian',
        'ac-wall' => 'AC Split',
        'cassette' => 'AC Kaset',
        'rental' => 'Penyewaan',
        'vrf' => 'AC VRF',
        'lab' => 'Laboratorium',
        'software' => 'Perangkat Lunak',
        'parking' => 'Parkir',
        'laptop' => 'Laptop',
        'cctv' => 'CCTV',
        'printer' => 'Printer',
        'furniture' => 'Furnitur',
        'book' => 'Buku',
        'truck' => 'Pengiriman',
        'cart' => 'Perdagangan',
        'building' => 'Gedung',
        'bolt' => 'Listrik',
        'cleaning' => 'Kebersihan',
        'medical' => 'Kesehatan',
        'package' => 'Paket Barang',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function activities(): HasMany
    {
        return $this->hasMany(BusinessActivity::class)->ordered();
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

    protected function iconImageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->resolveImageUrl($this->icon_image));
    }
}
