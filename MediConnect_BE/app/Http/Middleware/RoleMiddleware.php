<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $roleName = optional($user->role)->name;

        if (!$roleName || !in_array($roleName, $roles)) {
            return response()->json([
                'message' => 'Forbidden - role not allowed'
            ], 403);
        }

        return $next($request);
    }
}
