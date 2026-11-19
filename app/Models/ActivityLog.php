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

    public const ACTION_REORDERED = 'reordered';

    public const ACTION_RESET = 'reset';

    public const ACTION_DEFAULT = 'default';

    public const ACTION_DEFAULT_CLEARED = 'default_cleared';

    public const ACTION_LOGIN = 'login';

    public const ACTION_LOGOUT = 'logout';

    public const ACTION_TIMEOUT = 'timeout';

    /** Jenis (tab) yang dibuka saat halaman riwayat dibuka tanpa pilihan. */
    public const DEFAULT_GROUP = 'semua';

    /** Sebutan tiap jenis objek di halaman riwayat. */
    public const SUBJECT_LABELS = [
        ServiceCategory::class => 'Kategori Usaha',
        BusinessActivity::class => 'Aktivitas Usaha',
        GalleryPhoto::class => 'Foto Galeri',
        Page::class => 'Isi Halaman',
        User::class => 'Akun',
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

    /**
     * Aksi yang mengubah isi website; dipakai tab "Perubahan konten" dan kartu "Aktivitas Terbaru" di dashboard.
     *
     * @return list<string>
     */
    public static function contentActions(): array
    {
        return [
            self::ACTION_CREATED,
            self::ACTION_UPDATED,
            self::ACTION_DELETED,
            self::ACTION_REORDERED,
            self::ACTION_RESET,
            self::ACTION_DEFAULT,
            self::ACTION_DEFAULT_CLEARED,
        ];
    }

    /**
     * Aksi akses akun admin.
     *
     * @return list<string>
     */
    public static function accessActions(): array
    {
        return [self::ACTION_LOGIN, self::ACTION_LOGOUT, self::ACTION_TIMEOUT];
    }

    /**
     * Jenis catatan di halaman Riwayat Aktivitas, urut dari kiri ke kanan seperti tab. 'actions' null = semua aksi;
     * 'menus' = sebutan menu untuk penyaring (kosong = penyaring menu disembunyikan).
     *
     * @return array<string, array{label: string, actions: list<string>|null, menus: list<string>}>
     */
    public static function groups(): array
    {
        $contentMenus = array_values(array_filter(self::SUBJECT_LABELS, fn (string $label) => $label !== self::SUBJECT_LABELS[User::class]));

        return [
            'semua' => ['label' => 'Semua', 'actions' => null, 'menus' => self::menus()],
            'konten' => ['label' => 'Perubahan konten', 'actions' => self::contentActions(), 'menus' => $contentMenus],
            'akses' => ['label' => 'Akses akun', 'actions' => self::accessActions(), 'menus' => []],
        ];
    }

    /**
     * Label aksi untuk tampilan: [teks lencana, kelas warna, path ikon garis (viewBox 24)].
     *
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function actions(): array
    {
        return [
            self::ACTION_CREATED => ['Ditambah', 'bg-green-100 text-green-800', 'M12 5v14M5 12h14'],
            self::ACTION_UPDATED => ['Diubah', 'bg-amber-100 text-amber-800', 'M4 20h4L19 9a2.83 2.83 0 0 0-4-4L4 16zM14 6l4 4'],
            self::ACTION_DELETED => ['Dihapus', 'bg-red-100 text-red-800', 'M5 7h14M10 11v6M14 11v6M6 7l1 12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1l1-12M9 7V4h6v3'],
            self::ACTION_REORDERED => ['Urutan', 'bg-gray-800 text-white', 'M8 4v16m0 0-3-3m3 3 3-3M16 20V4m0 0-3 3m3-3 3 3'],
            self::ACTION_RESET => ['Reset default', 'bg-fuchsia-100 text-fuchsia-800', 'M3 12a9 9 0 1 0 3-6.7M3 4v5h5'],
            self::ACTION_DEFAULT => ['Setel default', 'bg-indigo-100 text-indigo-800', 'M12 3l2.7 5.5 6 .9-4.4 4.2 1 6L12 16.8 6.7 19.6l1-6L3.3 9.4l6-.9L12 3Z'],
            self::ACTION_DEFAULT_CLEARED => ['Hapus default', 'bg-teal-100 text-teal-800', 'M3 12a9 9 0 1 0 3-6.7M3 4v5h5'],
            self::ACTION_LOGIN => ['Masuk', 'bg-sky-100 text-sky-800', 'M15 3h4a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-4M10 17l5-5-5-5M15 12H3'],
            self::ACTION_LOGOUT => ['Keluar', 'bg-gray-100 text-gray-700', 'M9 21H5a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h4M16 17l5-5-5-5M21 12H9'],
            self::ACTION_TIMEOUT => ['Keluar otomatis', 'bg-orange-200 text-orange-900', 'M12 7v5l3 2M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        ];
    }

    public function subjectName(): string
    {
        return self::SUBJECT_LABELS[$this->subject_type] ?? class_basename($this->subject_type);
    }

    /**
     * Daftar nama kolom yang berubah (untuk ditampilkan), kosong bila bukan perubahan data.
     *
     * @return list<string>
     */
    public function changedFields(): array
    {
        if ($this->action !== self::ACTION_UPDATED) {
            return [];
        }

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
        return self::actions()[$this->action][0] ?? $this->action;
    }

    /** Warna lencana aksi di tabel riwayat. */
    public function actionClass(): string
    {
        return self::actions()[$this->action][1] ?? 'bg-amber-100 text-amber-800';
    }

    /** Path ikon garis (viewBox 24) untuk lencana aksi. */
    public function actionIcon(): string
    {
        return self::actions()[$this->action][2] ?? self::actions()[self::ACTION_UPDATED][2];
    }

    /**
     * Keterangan dua baris untuk tabel: kalimat utama dan catatan (kolom yang diubah) bila ada.
     *
     * @return array{0: string, 1: ?string}
     */
    public function detailLines(): array
    {
        $menu = mb_strtolower($this->subjectName());
        $label = '"'.$this->subject_label.'"';

        return match ($this->action) {
            self::ACTION_LOGIN => ['Masuk ke dashboard', null],
            self::ACTION_LOGOUT => ['Keluar dari dashboard', null],
            self::ACTION_TIMEOUT => ['Keluar otomatis karena tidak aktif selama '.(($this->getAttribute('changes') ?? [])['minutes'] ?? '?').' menit', null],
            self::ACTION_REORDERED => ['Mengubah urutan '.$menu, filled($this->subject_label) ? $this->subject_label : null],
            self::ACTION_RESET => ["Mengembalikan seluruh {$menu} {$label} ke setelan default", null],
            self::ACTION_DEFAULT => ["Menetapkan {$menu} {$label} yang tersimpan sebagai setelan default baru", null],
            self::ACTION_DEFAULT_CLEARED => ["Menghapus setelan default kustom {$menu} {$label}", 'Kembali ke default bawaan sistem'],
            default => [
                match ($this->action) {
                    self::ACTION_CREATED => 'Menambah',
                    self::ACTION_DELETED => 'Menghapus',
                    default => 'Mengubah',
                }.' '.$menu.' '.$label,
                $this->changedFields() === [] ? null : 'Yang diubah: '.implode(', ', $this->changedFields()),
            ],
        };
    }
}
