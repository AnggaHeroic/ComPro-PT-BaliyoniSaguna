<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class ActivityLogService
{
    /**
     * Riwayat terbaru di atas, bisa disaring menurut menu, aksi, kata kunci (keterangan, nama objek, atau admin), dan rentang tanggal.
     * Tanggal terbalik (dari lebih besar dari sampai) otomatis ditukar.
     *
     * @param  array{menu?: ?string, action?: ?string, q?: ?string, dari?: ?string, sampai?: ?string}  $filters
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $from = $this->date($filters['dari'] ?? null);
        $to = $this->date($filters['sampai'] ?? null);

        if ($from && $to && $from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        $search = trim((string) ($filters['q'] ?? ''));
        $subjectType = filled($filters['menu'] ?? null) ? ActivityLog::subjectTypeFor($filters['menu']) : null;

        return ActivityLog::query()
            ->with('user:id,name')
            ->when($subjectType, fn (Builder $query) => $query->where('subject_type', $subjectType))
            ->when($filters['action'] ?? null, fn (Builder $query, string $action) => $query->where('action', $action))
            ->when($from, fn (Builder $query) => $query->where('created_at', '>=', $from->copy()->startOfDay()))
            ->when($to, fn (Builder $query) => $query->where('created_at', '<=', $to->copy()->endOfDay()))
            ->when($search !== '', fn (Builder $query) => $this->matchSearch($query, $search))
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Cocokkan kata kunci dengan nama objek, nama admin, dan isi kolom Keterangan di tabel
     * (kata kerja "Menambah/Mengubah/Menghapus", sebutan menu, serta nama kolom yang diubah).
     */
    private function matchSearch(Builder $query, string $search): Builder
    {
        $term = mb_strtolower($search);

        $actions = collect([
            ActivityLog::ACTION_CREATED => ['menambah', 'ditambah'],
            ActivityLog::ACTION_UPDATED => ['mengubah', 'diubah'],
            ActivityLog::ACTION_DELETED => ['menghapus', 'dihapus'],
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
     * Pintasan tanggal untuk filter, dihitung dari hari ini (zona waktu aplikasi).
     *
     * @return list<array{label: string, from: string, to: string}>
     */
    public function datePresets(): array
    {
        $today = now()->toDateString();

        return [
            ['label' => 'Hari ini', 'from' => $today, 'to' => $today],
            ['label' => '7 hari terakhir', 'from' => now()->subDays(6)->toDateString(), 'to' => $today],
            ['label' => '30 hari terakhir', 'from' => now()->subDays(29)->toDateString(), 'to' => $today],
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
        return filled($value) ? Carbon::parse($value, config('app.timezone')) : null;
    }
}
