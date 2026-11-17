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

    protected static function logActivity(Model $model, string $action): void
    {
        $changes = null;

        if ($action === ActivityLog::ACTION_UPDATED) {
            $changes = Arr::except($model->getChanges(), ['updated_at']);

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
        ]);
    }
}
