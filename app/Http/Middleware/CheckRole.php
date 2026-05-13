<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        // Load sekali per request, skip kalau sudah di-load
        $user?->loadMissing(['roles', 'schools']);

        if (!$user?->hasRole($roles)) {
            abort(403, 'Unauthorized.');
        }

        return $next($request);
    }
}