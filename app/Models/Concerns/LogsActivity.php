<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(fn (Model $model) => static::logActivity($model, ActivityLog::ACTION_CREATED));
        static::updated(fn (Model $model) => static::logActivity($model, ActivityLog::ACTION_UPDATED));
        static::deleted(fn (Model $model) => static::logActivity($model, ActivityLog::ACTION_DELETED));
    }

    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    /**
     * Nama objek yang ditampilkan di riwayat. Model boleh menimpa method ini.
     */
    public function activityLabel(): string
    {
        return (string) ($this->name ?? class_basename($this).' #'.$this->getKey());
    }

    /**
     * Isi kolom 'changes' pada riwayat. Model boleh menimpa untuk meringkas perubahan (mis. nama bagian yang berubah).
     *
     * @param  array<string, mixed>  $changes
     * @return array<string, mixed>
     */
    public function activityChanges(array $changes): array
    {
        return $changes;
    }

    protected static function logActivity(Model $model, string $action): void
    {
        $changes = null;
        $previous = null;

        if ($action === ActivityLog::ACTION_UPDATED) {
            $changes = $model->activityChanges(Arr::except($model->getChanges(), ['updated_at']));

            // Nilai lama hanya untuk kolom biasa bernilai sederhana (bukan array/json), agar "Detail" di riwayat bisa menampilkan dari-menjadi.
            $previous = array_filter(
                array_intersect_key($model->getOriginal(), $changes),
                fn ($value) => is_scalar($value) || $value === null,
            ) ?: null;

            // Perubahan urutan (seret-dan-lepas) menyentuh banyak baris sekaligus; tidak dicatat satu per satu.
            if (array_keys($changes) === [] || array_keys($changes) === ['sort_order']) {
                return;
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $model::class,
            'subject_id' => $model->getKey(),
            'subject_label' => mb_substr($model->activityLabel(), 0, 255),
            'changes' => $changes,
            'previous' => $previous,
        ]);
    }
}
