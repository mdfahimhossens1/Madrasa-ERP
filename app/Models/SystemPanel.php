<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemPanel extends Model
{
    use HasFactory;

    protected $fillable = [

        'panel_name',

        'slug',

        'icon',

        'description',

        'serial',

        'is_system',

        'is_active',

    ];

    protected $casts = [

        'is_system'=>'boolean',

        'is_active'=>'boolean',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

public function roles()
{
    return $this->belongsToMany(
        Role::class,
        'role_system_panel'
    );
}

    /*
    |--------------------------------------------------------------------------
    | Scope
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active',true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('serial');
    }

public function permissions()
{
    return $this->hasMany(
        Permission::class
    );
}
}