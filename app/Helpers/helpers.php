<?php

/*
|--------------------------------------------------------------------------
| Bangla Number
|--------------------------------------------------------------------------
*/

if (! function_exists('bn_num')) {

    /**
     * Convert English numbers to Bangla numbers.
     */
    function bn_num($value)
    {
        $enDigits = ['0','1','2','3','4','5','6','7','8','9'];
        $bnDigits = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];

        return str_replace($enDigits, $bnDigits, (string) $value);
    }
}

/*
|--------------------------------------------------------------------------
| Bangla Date
|--------------------------------------------------------------------------
*/

if (! function_exists('bn_date')) {

    /**
     * Format Carbon date to Bangla.
     */
    function bn_date($date, $format = 'd-m-Y')
    {
        if (! $date) {
            return '';
        }

        return bn_num($date->format($format));
    }
}

/*
|--------------------------------------------------------------------------
| Permission Helpers
|--------------------------------------------------------------------------
*/

if (! function_exists('can_access')) {

    /**
     * Check if authenticated user has a permission.
     */
    function can_access(string $permission): bool
    {
        return auth()->check()
            && auth()->user()->hasPermission($permission);
    }
}

if (! function_exists('cannot_access')) {

    /**
     * Opposite of can_access().
     */
    function cannot_access(string $permission): bool
    {
        return ! can_access($permission);
    }
}

if (! function_exists('has_role')) {

    /**
     * Check if authenticated user has a role.
     */
    function has_role(string $role): bool
    {
        return auth()->check()
            && auth()->user()->hasRole($role);
    }
}