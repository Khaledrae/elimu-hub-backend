<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePremiumUser
{
    public function handle($request, Closure $next)
    {
        if (!auth()->user()?->is_premium) {
            return response()->json([
                'message' => 'Premium subscription required'
            ], 403);
        }

        return $next($request);
    }
}

