<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Tidak boleh memakai trait LogsActivity (menghindari rekursi).
#[Fillable(['user_id', 'action', 'subject_type', 'subject_id', 'subject_label', 'changes'])]
class ActivityLog extends Model
{
    public const ACTION_CREATED = 'created';

    public const ACTION_UPDATED = 'updated';

    public const ACTION_DELETED = 'deleted';

    /** Sebutan tiap jenis objek di halaman riwayat. */
    public const SUBJECT_LABELS = [
        ServiceCategory::class => 'Kategori Usaha',
        BusinessActivity::class => 'Aktivitas Usaha',
        GalleryPhoto::class => 'Foto Galeri',
    ];

    /** Sebutan kolom yang diubah, agar riwayat mudah dibaca. */
    public const FIELD_LABELS = [
        'name' => 'Nama',
        'slug' => 'Alamat URL',
        'description' => 'Deskripsi',
        'icon' => 'Ikon',
        'icon_image' => 'Ikon kustom',
        'kbli_code' => 'Kode KBLI',
        'title' => 'Judul',
        'image' => 'Gambar',
        'is_published' => 'Status',
        'service_category_id' => 'Kategori',
        'summary' => 'Ringkasan',
        'detail' => 'Detail',
    ];

    protected function casts(): array
    {
        return ['changes' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subjectName(): string
    {
        return self::SUBJECT_LABELS[$this->subject_type] ?? class_basename($this->subject_type);
    }

    /**
     * Daftar nama kolom yang berubah (untuk ditampilkan), kosong bila bukan perubahan.
     *
     * @return list<string>
     */
    public function changedFields(): array
    {
        // getAttribute: properti $changes milik Eloquent sendiri akan terbaca bila ditulis $this->changes.
        return collect(array_keys($this->getAttribute('changes') ?? []))
            ->map(fn (string $field) => self::FIELD_LABELS[$field] ?? $field)
            ->values()
            ->all();
    }

    /**
     * Daftar sebutan menu untuk penyaring riwayat.
     *
     * @return list<string>
     */
    public static function menus(): array
    {
        return array_values(self::SUBJECT_LABELS);
    }

    /**
     * Kelas model untuk satu sebutan menu; null bila tidak dikenal.
     */
    public static function subjectTypeFor(string $menu): ?string
    {
        return array_search($menu, self::SUBJECT_LABELS, true) ?: null;
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            self::ACTION_CREATED => 'Ditambah',
            self::ACTION_UPDATED => 'Diubah',
            self::ACTION_DELETED => 'Dihapus',
            default => $this->action,
        };
    }

    /** Warna lencana aksi di tabel riwayat. */
    public function actionClass(): string
    {
        return match ($this->action) {
            self::ACTION_CREATED => 'bg-green-100 text-green-800',
            self::ACTION_DELETED => 'bg-red-100 text-red-800',
            default => 'bg-amber-100 text-amber-800',
        };
    }

    /** Path ikon garis (viewBox 24) untuk lencana aksi. */
    public function actionIcon(): string
    {
        return match ($this->action) {
            self::ACTION_CREATED => 'M12 5v14M5 12h14',
            self::ACTION_DELETED => 'M5 7h14M10 11v6M14 11v6M6 7l1 12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1l1-12M9 7V4h6v3',
            default => 'M4 20h4L19 9a2.83 2.83 0 0 0-4-4L4 16zM14 6l4 4',
        };
    }

    /**
     * Keterangan dua baris untuk tabel: kalimat utama dan catatan (kolom yang diubah) bila ada.
     *
     * @return array{0: string, 1: ?string}
     */
    public function detailLines(): array
    {
        $verb = match ($this->action) {
            self::ACTION_CREATED => 'Menambah',
            self::ACTION_DELETED => 'Menghapus',
            default => 'Mengubah',
        };

        $fields = $this->changedFields();

        return [
            $verb.' '.mb_strtolower($this->subjectName()).' "'.$this->subject_label.'"',
            $fields === [] ? null : 'Yang diubah: '.implode(', ', $fields),
        ];
    }
}
