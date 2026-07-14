<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstitutionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        | সব Institution Access করতে পারবে।
        | Future-এ Session Institution ব্যবহার করবে।
        */

        if ($user->is_super_admin) {

            if (session()->has('institution_id')) {
                app()->instance(
                    'institution_id',
                    session('institution_id')
                );
            }

            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Soft Admin
        |--------------------------------------------------------------------------
        */

        if ($user->is_soft_admin) {

            if (session()->has('institution_id')) {
                app()->instance(
                    'institution_id',
                    session('institution_id')
                );
            }

            return $next($request);
        }

        /*
        |--------------------------------------------------------------------------
        | Institution User
        |--------------------------------------------------------------------------
        */

        if (empty($user->institution_id)) {
            abort(403, 'Institution not assigned.');
        }

        app()->instance(
            'institution_id',
            $user->institution_id
        );

        return $next($request);
    }
}