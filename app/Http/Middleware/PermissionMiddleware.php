<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth()->user();

        if (!$user) {
            abort(401);
        }

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        if ($user->is_super_admin) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Permission
        |--------------------------------------------------------------------------
        */

        if (!$user->hasPermission($permission)) {

            abort(403, 'You do not have permission to access this page.');

        }

        return $next($request);
    }
}