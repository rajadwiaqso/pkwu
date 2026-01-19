<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class RedirectMethodNotAllowed
{
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (MethodNotAllowedHttpException $e) {
            return redirect()->route('home'); // Ganti 'home' sesuai route index kamu
        }
    }
}