<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Login Check
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // User Role
        $userRole = $user->role;

        if (!$userRole) {
            abort(403, 'Role not assigned.');
        }

        /*
        |--------------------------------------------------------------------------
        | Exact Role Match
        |--------------------------------------------------------------------------
        */
        if (in_array($userRole->slug, $roles)) {
            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Role Hierarchy Check
        |--------------------------------------------------------------------------
        */

        $requiredRoles = Role::whereIn('slug', $roles)->get();

        if ($requiredRoles->isEmpty()) {
            abort(403, 'Invalid role configuration.');
        }

        $minimumLevel = $requiredRoles->max('level');

        if ($userRole->level >= $minimumLevel) {
            return $next($request);
        }

        abort(403, 'You do not have permission to access this page.');
    }
}