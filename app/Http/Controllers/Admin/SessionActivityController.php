<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IdleWarningRequest;
use App\Services\IdleSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SessionActivityController extends Controller
{
    public function __construct(private IdleSessionService $idleSession) {}

    /**
     * Dipanggil berkala oleh dashboard selama admin masih aktif di layar (mengetik, menggulir, dst.) dan oleh tombol
     * "Tetap masuk", agar sesi tidak dianggap diam walau tidak ada halaman yang dibuka.
     */
    public function keepAlive(Request $request): Response
    {
        $this->idleSession->keepAlive($request);

        return response()->noContent();
    }

    /** Dashboard memberi tahu bahwa peringatan tampil dan sudah berapa detik admin diam. */
    public function idleWarning(IdleWarningRequest $request): Response
    {
        $this->idleSession->markWarning($request, (int) $request->validated('idle'));

        return response()->noContent();
    }

    /**
     * Dikirim dashboard saat pewaktu di browser menyatakan admin diam. Server memastikan dulu (mis. tab lain mungkin masih
     * aktif); hanya bila benar-benar sudah (hampir) melewati batas, admin keluar dan dicatat sebagai keluar otomatis.
     */
    public function idleLogout(Request $request): RedirectResponse
    {
        if (! $this->idleSession->reallyIdle($request)) {
            return redirect()->back(fallback: route('admin.dashboard'));
        }

        $this->idleSession->expire($request, $request->user());

        return redirect()->route('login');
    }
}
