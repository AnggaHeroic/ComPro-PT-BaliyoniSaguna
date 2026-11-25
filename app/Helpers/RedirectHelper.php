<?php

namespace App\Helpers;

use Illuminate\Http\RedirectResponse;

/**
 * Satu pintu untuk semua pengalihan beserta pesan flash (success/error) di panel admin,
 * sehingga controller cukup memanggil helper ini dan hanya mengisi teks pesannya.
 */
class RedirectHelper
{
    /**
     * Ke halaman lain dengan pesan sukses.
     *
     * @param  array<string, mixed>  $params  parameter route tujuan
     */
    public static function success(string $route, string $message, array $params = []): RedirectResponse
    {
        return redirect()->route($route, $params)->with('success', $message);
    }

    /**
     * Kembali ke halaman sebelumnya dengan pesan galat. Isian form dipertahankan bila $keepInput true.
     */
    public static function failure(string $message, bool $keepInput = false): RedirectResponse
    {
        $response = back();

        if ($keepInput) {
            $response->withInput();
        }

        return $response->with('error', $message);
    }

    /**
     * Ke halaman tertentu (bukan kembali) dengan pesan galat.
     *
     * @param  array<string, mixed>  $params
     */
    public static function failureTo(string $route, string $message, array $params = []): RedirectResponse
    {
        return redirect()->route($route, $params)->with('error', $message);
    }

    /**
     * Kembali ke daftar setelah menyimpan. Draft diarahkan ke tab Draft agar langsung terlihat,
     * dengan catatan bahwa belum tampil di website.
     *
     * @param  array<string, mixed>  $params  parameter route daftar, mis. kategori yang sedang dibuka
     */
    public static function afterSave(string $route, bool $published, string $message, array $params = []): RedirectResponse
    {
        if ($published) {
            return self::success($route, $message, $params);
        }

        return self::success($route, rtrim($message, '.').' sebagai draft. Belum tampil di website.', $params + ['status' => 'draft']);
    }
}
