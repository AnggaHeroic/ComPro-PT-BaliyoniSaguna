<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\IdleSessionService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

/**
 * Mencatat masuk dan keluar admin di riwayat aktivitas. Keluar karena tidak aktif sudah dicatat sendiri
 * ("Keluar otomatis"), jadi catatan "Keluar" biasa dilewati.
 */
class RecordAdminAccess
{
    public function __construct(private ActivityLogService $logService) {}

    public function login(Login $event): void
    {
        $this->record(ActivityLog::ACTION_LOGIN, $event->user);
    }

    public function logout(Logout $event): void
    {
        if (request()->attributes->get(IdleSessionService::IDLE_LOGOUT_ATTRIBUTE)) {
            return;
        }

        $this->record(ActivityLog::ACTION_LOGOUT, $event->user);
    }

    private function record(string $action, mixed $user): void
    {
        if (! $user instanceof User) {
            return;
        }

        $this->logService->record($action, User::class, $user->getKey(), $user->name, null, $user->getKey());
    }
}
