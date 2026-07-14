<?php
// app/Models/Section.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\BelongsToInstitution;
class Section extends Model
{
    use HasFactory, BelongsToInstitution;

    protected $fillable = [
        'name',
        'name_bn',
        'class_id',
        'institution_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'id');
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class, 'section_id');
    }

    public function studentAssignments()
    {
        return $this->hasMany(StudentClassAssignment::class, 'section_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFullNameAttribute()
    {
        return $this->name_bn ?: $this->name;
    }
}