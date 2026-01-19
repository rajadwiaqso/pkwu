<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureEmailVerified - Middleware untuk memastikan user sudah verifikasi email
 * Sebelum akses halaman tertentu
 */
class EnsureEmailVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user sudah login dan email belum verified
        if ($request->user() && !$request->user()->isVerified()) {
            // Jika AJAX request, return 403
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Silakan verifikasi email Anda terlebih dahulu.'
                ], 403);
            }

            // Redirect ke halaman verifikasi dengan pesan
            return redirect()
                ->route('email.verify-page')
                ->with('warning', 'Silakan verifikasi email Anda untuk melanjutkan.');
        }

        return $next($request);
    }
}
