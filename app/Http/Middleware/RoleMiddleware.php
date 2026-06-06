<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Normalize role slug
     * Example:
     * super-admin => super_admin
     */
    private function normalize(?string $role): string
    {
        $role = strtolower(trim($role ?? 'user'));

        return str_replace([' ', '-'], '_', $role);
    }

    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        // User logged in?
        if (!$user) {
            return redirect()->route('login');
        }

        // Get user role slug
        $dbRole = optional($user->role)->slug ?? 'user';

        $roleName = $this->normalize($dbRole);

        /*
        |--------------------------------------------------------------------------
        | Role Hierarchy
        |--------------------------------------------------------------------------
        | Higher number = higher power
        */

        $levels = [

            'guardian'       => 1,
            'student'        => 2,
            'teacher'        => 3,

            'madrasa_admin' => 4,

            'soft_admin'    => 5,

            'super_admin'   => 6,
        ];

        $userLevel = $levels[$roleName] ?? 0;

        /*
        |--------------------------------------------------------------------------
        | Check Required Roles
        |--------------------------------------------------------------------------
        */

        foreach ($roles as $role) {

            $requiredRole = $this->normalize($role);

            $requiredLevel = $levels[$requiredRole] ?? 0;

            // User has required role or higher
            if ($userLevel >= $requiredLevel) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized Access.');
    }
}