<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only count public GET requests, skip admin/api/assets
        if (
            $request->isMethod('GET') &&
            ! $request->is('admin*', 'api*', 'livewire*', '_debugbar*') &&
            ! $request->expectsJson()
        ) {
            $sessionKey = 'visited:' . $request->session()->getId();

            // Count once per session (new session = new visitor)
            if (! Cache::has($sessionKey)) {
                Cache::put($sessionKey, true, now()->addHours(24));

                // Increment total visitor count atomically
                Cache::increment('site.visitor_count');
            }
        }

        return $next($request);
    }
}
