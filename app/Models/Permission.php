<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [

    'system_panel_id',
    'permission_name',
    'slug',
    'description',
    'serial',
    'is_system',
    'is_active',

];

protected $casts = [

    'is_system' => 'boolean',
    'is_active' => 'boolean',

];

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */
public function systemPanel()
{
    return $this->belongsTo(SystemPanel::class);
}

public function roles()
{
    return $this->belongsToMany(
        Role::class,
        'role_permissions'
    )->withTimestamps();
}

public function users()
{
    return $this->belongsToMany(
        User::class,
        'user_permissions'
    )
    ->withPivot('allow')
    ->withTimestamps();
}

    /*
    |--------------------------------------------------------------------------
    | Scope
    |--------------------------------------------------------------------------
    */

public function scopeActive($query)
{
    return $query->where('is_active',1);
}

public function scopeOrdered($query)
{
    return $query->orderBy('serial');
}
    public function panel()
{
    return $this->belongsTo(SystemPanel::class, 'system_panel_id');
}
}