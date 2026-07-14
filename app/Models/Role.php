<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_name',
        'slug',
        'description',
        'level',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'level' => 'integer',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Accessors
    public function getIsSuperAdminAttribute()
    {
        return $this->slug === 'super-admin';
    }

    public function getIsSoftAdminAttribute()
    {
        return $this->slug === 'soft-admin';
    }

    public function getIsMadrasaAdminAttribute()
    {
        return $this->slug === 'madrasa-admin';
    }

    public function getIsTeacherAttribute()
    {
        return $this->slug === 'teacher';
    }

    public function getIsStudentAttribute()
    {
        return $this->slug === 'student';
    }

    public function getIsGuardianAttribute()
    {
        return $this->slug === 'guardian';
    }

    // Scopes
    public function scopeSystemRoles($query)
    {
        return $query->where('is_system', 1);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

public function permissions()
{
    return $this->belongsToMany(
        Permission::class,
        'role_permissions'
    )->withTimestamps();
}

}