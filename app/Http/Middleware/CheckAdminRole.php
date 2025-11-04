<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // 1️⃣ If user has a 'role' column (normal admin or teacher)
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 2️⃣ If user is linked to a super_admin or admin table
        if ($user->relationLoaded('admin') || method_exists($user, 'admin')) {
            $admin = $user->admin;
            if ($admin && in_array('school_admin', $roles)) {
                return $next($request);
            }
        }

        if ($user->relationLoaded('superAdmin') || method_exists($user, 'superAdmin')) {
            $superAdmin = $user->superAdmin;
            if ($superAdmin && in_array('super_admin', $roles)) {
                return $next($request);
            }
        }

        return response()->json(['error' => 'Forbidden'], 403);
    }
}
