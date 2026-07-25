<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        string $permission
    ): Response {

        $user = auth()->user();

        if (!$user) {
            abort(401);
        }

        if (!$user->hasPermission($permission)) {

            abort(403, 'Permission denied.');

        }

        return $next($request);
    }
}