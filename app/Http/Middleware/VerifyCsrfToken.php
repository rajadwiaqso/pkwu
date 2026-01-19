<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Tambahkan endpoint Midtrans di sini, misal:
        'midtrans-notification',
        'tripay-callback',
        // Exclude API routes for Sanctum SPA authentication
        'api/*',
    ];
}