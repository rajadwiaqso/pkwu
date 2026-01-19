<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class UnderDevelopmentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if request expects JSON (API requests)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Fitur ini sedang dalam pengembangan',
                'status' => 'under_development',
                'estimated_completion' => 'Segera hadir'
            ], 503);
        }

        // For web requests, return Inertia response with under development page
        return Inertia::render('UnderDevelopment', [
            'title' => 'Fitur Dalam Pengembangan',
            'message' => 'Halaman ini sedang dalam pengembangan dan akan segera tersedia.',
            'description' => 'Kami sedang bekerja keras untuk memberikan pengalaman terbaik bagi Anda. Silakan kembali lagi nanti!',
            'estimated_time' => 'Segera Hadir',
            'features' => [
                '🚀 Performa yang lebih cepat',
                '✨ Fitur-fitur baru yang menarik',
                '🔒 Keamanan yang lebih baik',
                '📱 Pengalaman mobile yang lebih baik'
            ],
            'contact_info' => [
                'email' => 'support@marketraja.com',
                'phone' => '+62 812-3456-7890'
            ],
            'current_route' => $request->route() ? $request->route()->getName() : $request->path(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString()
        ])->toResponse($request);
    }
}
