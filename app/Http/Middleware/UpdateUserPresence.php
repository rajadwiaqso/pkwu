<?php

namespace App\Http\Middleware;

use App\Events\UserPresenceUpdated;
use App\Services\ChatService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserPresence
{
    protected $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Update user presence if authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Update online status
            $settings = $this->chatService->updateUserPresence($user->id, true);
            
            // Broadcast presence update
            broadcast(new UserPresenceUpdated($user, $settings->status, true))->toOthers();
        }

        return $response;
    }
}
