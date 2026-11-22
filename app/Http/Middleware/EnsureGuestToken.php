<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memberi setiap pengunjung token anonim (disimpan di cookie terenkripsi)
 * agar mood & jurnal bisa dipakai tanpa login (mode tamu sesuai PRD).
 */
class EnsureGuestToken
{
    public const COOKIE = 'selateduh_guest';

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie(self::COOKIE);
        $isNew = false;

        if (! is_string($token) || ! preg_match('/^[A-Za-z0-9]{40}$/', $token)) {
            $token = Str::random(40);
            $isNew = true;
        }

        $request->attributes->set('guest_token', $token);

        $response = $next($request);

        if ($isNew) {
            // Berlaku 1 tahun, hanya untuk HTTP (tidak bisa dibaca JavaScript)
            $response->headers->setCookie(cookie(self::COOKIE, $token, 60 * 24 * 365));
        }

        return $response;
    }
}
