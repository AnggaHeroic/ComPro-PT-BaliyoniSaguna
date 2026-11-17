<?php

namespace App\Http\Middleware;

use App\Services\IdleSessionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin yang tidak membuka atau mengirim apa pun ke dashboard selama batas waktu
 * (config admin.idle_timeout) dikeluarkan dan harus masuk kembali.
 */
class AdminIdleTimeout
{
    public function __construct(private IdleSessionService $idleSession) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            if ($this->idleSession->isExpired($request)) {
                $this->idleSession->expire($request, $user);

                return redirect()->route('login');
            }

            $this->idleSession->touch($request);
        }

        return $next($request);
    }
}
