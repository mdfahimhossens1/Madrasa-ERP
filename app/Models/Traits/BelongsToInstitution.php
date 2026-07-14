<?php

namespace App\Models\Traits;

use App\Models\Scopes\InstitutionScope;

trait BelongsToInstitution
{
    protected static function bootBelongsToInstitution(): void
    {
        static::addGlobalScope(new InstitutionScope);

        static::creating(function ($model) {

            if (!auth()->check()) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Already Assigned
            |--------------------------------------------------------------------------
            */

            if (!empty($model->institution_id)) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Session Institution
            |--------------------------------------------------------------------------
            */

            if (session()->has('institution_id')) {

                $model->institution_id = session('institution_id');

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | User Institution
            |--------------------------------------------------------------------------
            */

            if (auth()->user()->institution_id) {

                $model->institution_id =
                    auth()->user()->institution_id;
            }
        });
    }
}