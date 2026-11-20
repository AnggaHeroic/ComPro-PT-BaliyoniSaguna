<?php

namespace App\Services;

use App\Helpers\DateHelper;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class ActivityLogService
{
    /**
     * Catat aksi yang bukan simpan data satu model (masuk, keluar, ubah urutan, setel atau reset default).
     * Perubahan data biasa dicatat otomatis oleh trait LogsActivity, jadi tidak lewat sini.
     *
     * @param  array<string, mixed>|null  $changes  keterangan tambahan (mis. ['minutes' => 30])
     */
    public function record(string $action, string $subjectType, int $subjectId = 0, ?string $label = null, ?array $changes = null, ?int $userId = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'subject_label' => $label === null ? null : mb_substr($label, 0, 255),
            'changes' => $changes,
        ]);
    }

    /**
     * Riwayat terbaru di atas, bisa disaring menurut jenis (tab), menu, aksi, kata kunci (keterangan, nama objek, atau admin),
     * dan rentang tanggal. Tanggal terbalik (dari lebih besar dari sampai) otomatis ditukar.
     *
     * @param  array{jenis?: ?string, menu?: ?string, action?: ?string, q?: ?string, dari?: ?string, sampai?: ?string}  $filters
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->filtered($filters)->latest('id')->paginate($perPage);
    }

    /**
     * Jumlah catatan per jenis (tab) dengan pencarian dan rentang tanggal yang sama, supaya angka di tab ikut menyesuaikan.
     *
     * @param  array{q?: ?string, dari?: ?string, sampai?: ?string}  $filters
     * @return array<string, int>
     */
    public function groupCounts(array $filters = []): array
    {
        $base = ['q' => $filters['q'] ?? null, 'dari' => $filters['dari'] ?? null, 'sampai' => $filters['sampai'] ?? null];
        $counts = [];

        foreach (array_keys(ActivityLog::groups()) as $group) {
            $counts[$group] = $this->filtered([...$base, 'jenis' => $group])->count();
        }

        return $counts;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<ActivityLog>
     */
    private function filtered(array $filters): Builder
    {
        $from = $this->date($filters['dari'] ?? null);
        $to = $this->date($filters['sampai'] ?? null);

        if ($from && $to && $from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        $search = trim((string) ($filters['q'] ?? ''));
        $subjectType = filled($filters['menu'] ?? null) ? ActivityLog::subjectTypeFor($filters['menu']) : null;
        $groupActions = ActivityLog::groups()[$filters['jenis'] ?? ActivityLog::DEFAULT_GROUP]['actions'] ?? null;

        return ActivityLog::query()
            ->with('user:id,name')
            ->when($groupActions, fn (Builder $query) => $query->whereIn('action', $groupActions))
            ->when($subjectType, fn (Builder $query) => $query->where('subject_type', $subjectType))
            ->when($filters['action'] ?? null, fn (Builder $query, string $action) => $query->where('action', $action))
            ->when($from, fn (Builder $query) => $query->where('created_at', '>=', DateHelper::dayBoundary($from->toDateString())))
            ->when($to, fn (Builder $query) => $query->where('created_at', '<=', DateHelper::dayBoundary($to->toDateString(), true)))
            ->when($search !== '', fn (Builder $query) => $this->matchSearch($query, $search));
    }

    /**
     * Cocokkan kata kunci dengan nama objek, nama admin, dan isi kolom Keterangan di tabel
     * (kata kerja, sebutan menu, serta nama kolom yang diubah).
     */
    private function matchSearch(Builder $query, string $search): Builder
    {
        $term = mb_strtolower($search);

        $actions = collect([
            ActivityLog::ACTION_CREATED => ['menambah', 'ditambah'],
            ActivityLog::ACTION_UPDATED => ['mengubah', 'diubah'],
            ActivityLog::ACTION_DELETED => ['menghapus', 'dihapus'],
            ActivityLog::ACTION_REORDERED => ['urutan', 'mengubah urutan'],
            ActivityLog::ACTION_RESET => ['reset', 'mengembalikan seluruh', 'setelan default', 'ke setelan default'],
            ActivityLog::ACTION_DEFAULT => ['setel default', 'menetapkan', 'setelan default baru', 'yang tersimpan'],
            ActivityLog::ACTION_DEFAULT_CLEARED => ['hapus default', 'menghapus setelan default kustom', 'default kustom', 'kembali ke default bawaan sistem'],
            ActivityLog::ACTION_LOGIN => ['masuk', 'login', 'masuk ke dashboard'],
            ActivityLog::ACTION_LOGOUT => ['keluar', 'logout', 'keluar dari dashboard'],
            ActivityLog::ACTION_TIMEOUT => ['keluar otomatis', 'tidak aktif', 'keluar otomatis karena tidak aktif'],
        ])->filter(fn (array $words) => collect($words)->contains(fn (string $word) => str_contains($word, $term)))->keys();

        $subjectTypes = collect(ActivityLog::SUBJECT_LABELS)
            ->filter(fn (string $label) => str_contains(mb_strtolower($label), $term))->keys();

        $fields = collect(ActivityLog::FIELD_LABELS)
            ->filter(fn (string $label) => str_contains(mb_strtolower($label), $term))->keys();

        return $query->where(function (Builder $query) use ($search, $actions, $subjectTypes, $fields) {
            $query->where('subject_label', 'like', "%{$search}%")
                ->orWhereHas('user', fn (Builder $user) => $user->where('name', 'like', "%{$search}%"));

            if ($actions->isNotEmpty()) {
                $query->orWhereIn('action', $actions);
            }

            if ($subjectTypes->isNotEmpty()) {
                $query->orWhereIn('subject_type', $subjectTypes);
            }

            foreach ($fields as $field) {
                $query->orWhereJsonContainsKey('changes->'.$field);
            }
        });
    }

    /**
     * Catatan perubahan konten terbaru (paling baru di atas) untuk kartu "Aktivitas Terbaru" di dashboard;
     * catatan masuk dan keluar tidak ikut.
     *
     * @return Collection<int, ActivityLog>
     */
    public function latest(int $limit = 5): Collection
    {
        return ActivityLog::query()->with('user:id,name')->whereIn('action', ActivityLog::contentActions())->latest('id')->limit($limit)->get();
    }

    /**
     * Pintasan tanggal untuk filter, dihitung dari hari ini (zona waktu tampilan).
     *
     * @return list<array{label: string, from: string, to: string}>
     */
    public function datePresets(): array
    {
        $today = DateHelper::today()->toDateString();

        return [
            ['label' => 'Hari ini', 'from' => $today, 'to' => $today],
            ['label' => '7 hari terakhir', 'from' => DateHelper::today()->subDays(6)->toDateString(), 'to' => $today],
            ['label' => '30 hari terakhir', 'from' => DateHelper::today()->subDays(29)->toDateString(), 'to' => $today],
        ];
    }

    /**
     * Perubahan terakhir pada satu objek, untuk keterangan "Terakhir diubah oleh ...".
     * Model harus memakai trait LogsActivity.
     */
    public function lastChange(Model $subject): ?ActivityLog
    {
        return $subject->activityLogs()->with('user:id,name')->latest('id')->first();
    }

    private function date(?string $value): ?Carbon
    {
        return filled($value) ? Carbon::parse($value, config('app.display_timezone')) : null;
    }
}
