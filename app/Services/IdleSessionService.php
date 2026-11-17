<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Sesi admin yang tidak aktif: menghitung lama diam, menandai peringatan, dan mengeluarkan admin
 * (dengan mencatatnya di riwayat) setelah melewati batas di config/admin.php.
 */
class IdleSessionService
{
    /** Kunci sesi: waktu (detik epoch) permintaan admin terakhir. */
    public const KEY = 'admin_last_activity';

    /** Kunci sesi: peringatan tidak aktif sedang tampil; permintaan biasa tidak lagi menyegarkan KEY. */
    public const WARNING_KEY = 'admin_idle_warning';

    /** Atribut permintaan: keluar ini karena tidak aktif, jadi catatan "Keluar" biasa tidak perlu menyusul. */
    public const IDLE_LOGOUT_ATTRIBUTE = 'idle_logout';

    public function __construct(private ActivityLogService $logService) {}

    public function timeoutMinutes(): int
    {
        return max(1, (int) config('admin.idle_timeout', 30));
    }

    /** Berapa detik sejak aktivitas admin terakhir yang tercatat di sesi (null bila belum ada). */
    public function idleSeconds(Request $request): ?int
    {
        $last = $request->session()->get(self::KEY);

        return $last === null ? null : max(0, now()->timestamp - (int) $last);
    }

    public function isExpired(Request $request): bool
    {
        $idle = $this->idleSeconds($request);

        return $idle !== null && $idle >= $this->timeoutMinutes() * 60;
    }

    /** Permintaan biasa menyegarkan waktu aktif, kecuali selama peringatan tampil (hanya "Tetap masuk" yang melanjutkan). */
    public function touch(Request $request): void
    {
        if (! $request->session()->get(self::WARNING_KEY)) {
            $request->session()->put(self::KEY, now()->timestamp);
        }
    }

    /** "Tetap masuk" atau aktivitas di tab lain: sesi dilanjutkan dari sekarang. */
    public function keepAlive(Request $request): void
    {
        $request->session()->forget(self::WARNING_KEY);
        $request->session()->put(self::KEY, now()->timestamp);
    }

    /**
     * Peringatan mulai tampil dan admin sudah diam $idleSeconds detik: jam server diselaraskan dengan jam browser,
     * lalu tidak disegarkan permintaan biasa, sehingga pindah halaman atau memuat ulang tidak mengulang hitungan mundur.
     */
    public function markWarning(Request $request, int $idleSeconds): void
    {
        $limit = $this->timeoutMinutes() * 60;
        $idle = max(0, min($limit, $idleSeconds));

        $request->session()->put(self::KEY, now()->timestamp - $idle);
        $request->session()->put(self::WARNING_KEY, true);
    }

    /** Keluar karena tidak aktif sudah (hampir) melewati batas menurut server; toleransi satu menit untuk selisih jam browser. */
    public function reallyIdle(Request $request): bool
    {
        $idle = $this->idleSeconds($request);

        return $idle === null || $idle >= $this->timeoutMinutes() * 60 - 60;
    }

    public function expire(Request $request, User $user): void
    {
        $minutes = $this->timeoutMinutes();

        $this->logService->record(ActivityLog::ACTION_TIMEOUT, User::class, $user->getKey(), $user->name, ['minutes' => $minutes], $user->getKey());

        $request->attributes->set(self::IDLE_LOGOUT_ATTRIBUTE, true);

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->flash('status', "Anda keluar otomatis karena tidak aktif selama {$minutes} menit. Silakan masuk kembali.");
    }
}
