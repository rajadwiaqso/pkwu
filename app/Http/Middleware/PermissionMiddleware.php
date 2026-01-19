<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!Auth::check()) {
            return redirect()->route('signin.view');
        }

        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('signin.view')->with('error', 'Your account has been deactivated.');
        }

        // Check if user has any of the required permissions
        if (!$user->hasAnyPermission($permissions)) {
            // Log unauthorized access attempt
            $user->logPermission(
                'access_denied',
                'middleware',
                null,
                "Attempted to access route requiring permissions: " . implode(', ', $permissions)
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Insufficient permissions.',
                    'required_permissions' => $permissions
                ], 403);
            }

            return redirect()->back()->with('error', 'You do not have sufficient permissions to access this resource.');
        }

        return $next($request);
    }
}
