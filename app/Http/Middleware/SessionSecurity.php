<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Session Security Middleware
 * 
 * Protects against session hijacking and fixation attacks
 */
class SessionSecurity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Check for IP address change (potential session hijacking)
            if ($this->hasIpChanged($request)) {
                $this->handleSuspiciousActivity($request);
                return redirect()->route('signin.view')
                    ->with('warning', 'Sesi Anda telah diakhiri karena aktivitas mencurigakan. Silakan login kembali.');
            }

            // Check for user agent change (potential session hijacking)
            if ($this->hasUserAgentChanged($request)) {
                $this->handleSuspiciousActivity($request);
                return redirect()->route('signin.view')
                    ->with('warning', 'Sesi Anda telah diakhiri karena perubahan perangkat. Silakan login kembali.');
            }

            // Regenerate session ID periodically (prevents session fixation)
            if ($this->shouldRegenerateSession($request)) {
                $request->session()->regenerate();
                session(['last_session_regeneration' => now()]);
            }

            // Update last activity timestamp
            session(['last_activity' => now()]);
        }

        return $next($request);
    }

    /**
     * Check if IP address has changed
     */
    private function hasIpChanged(Request $request): bool
    {
        $currentIp = $request->ip();
        $sessionIp = session('user_ip');

        if (!$sessionIp) {
            session(['user_ip' => $currentIp]);
            return false;
        }

        return $currentIp !== $sessionIp;
    }

    /**
     * Check if user agent has changed
     */
    private function hasUserAgentChanged(Request $request): bool
    {
        $currentUserAgent = $request->header('User-Agent');
        $sessionUserAgent = session('user_agent');

        if (!$sessionUserAgent) {
            session(['user_agent' => $currentUserAgent]);
            return false;
        }

        return $currentUserAgent !== $sessionUserAgent;
    }

    /**
     * Check if session should be regenerated
     */
    private function shouldRegenerateSession(Request $request): bool
    {
        $lastRegeneration = session('last_session_regeneration');

        if (!$lastRegeneration) {
            return true;
        }

        // Regenerate session every 30 minutes
        return now()->diffInMinutes($lastRegeneration) >= 30;
    }

    /**
     * Handle suspicious activity
     */
    private function handleSuspiciousActivity(Request $request): void
    {
        // Log suspicious activity
        Log::warning('Suspicious session activity detected', [
            'user_id' => Auth::id(),
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'session_ip' => session('user_ip'),
            'session_user_agent' => session('user_agent'),
        ]);

        // Logout user
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
