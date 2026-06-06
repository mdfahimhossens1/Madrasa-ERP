<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_bn',
        'code',
        'semester_count',
        'is_current',
        'status',
        'description',
        'created_by',
        'updated_by',
        'parent_year_id',
    ];

    protected $casts = [
        'is_current' => 'boolean',
    ];

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    // Helpers
    public static function current()
    {
        return static::where('is_current', true)->where('status', 'active')->first();
    }

    public function getFullNameAttribute()
    {
        return $this->name_bn ?: $this->name;
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'active' => 'সক্রিয়',
            'inactive' => 'নিষ্ক্রিয়',
            default => 'অজানা',
        };
    }
}