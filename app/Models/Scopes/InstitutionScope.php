<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class InstitutionScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!Auth::check()) {
            return;
        }

        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Active Institution
        |--------------------------------------------------------------------------
        |
        | Priority:
        | 1. Session Institution (Software Team)
        | 2. User Institution
        |
        */

        $institutionId = session('institution_id');

        if (!$institutionId) {
            $institutionId = $user->institution_id;
        }

        /*
        |--------------------------------------------------------------------------
        | Software Users
        |--------------------------------------------------------------------------
        */

        if (
            $user->is_super_admin ||
            $user->is_soft_admin
        ) {

            if (!$institutionId) {
                return;
            }

            $builder->where(
                $model->getTable() . '.institution_id',
                $institutionId
            );

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Institution Users
        |--------------------------------------------------------------------------
        */

        if (!$institutionId) {
            return;
        }

        $builder->where(
            $model->getTable() . '.institution_id',
            $institutionId
        );
    }
}