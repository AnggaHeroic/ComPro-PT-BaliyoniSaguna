<?php

namespace App\Helpers;

use Illuminate\Http\RedirectResponse;

class RedirectHelper
{
    /**
     * Kembali ke daftar setelah menyimpan. Draft diarahkan ke tab Draft agar langsung terlihat,
     * dengan catatan bahwa belum tampil di website.
     *
     * @param  array<string, mixed>  $params  parameter route daftar, mis. kategori yang sedang dibuka
     */
    public static function afterSave(string $route, bool $published, string $message, array $params = []): RedirectResponse
    {
        if ($published) {
            return redirect()->route($route, $params)->with('success', $message);
        }

        return redirect()
            ->route($route, $params + ['status' => 'draft'])
            ->with('success', rtrim($message, '.').' sebagai draft. Belum tampil di website.');
    }
}
