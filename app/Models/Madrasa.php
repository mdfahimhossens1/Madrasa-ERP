<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Madrasa extends Model
{
    use HasFactory;

    protected $fillable = [
        'madrasa_code',
        'name',
        'name_bn',
        'email',
        'phone',
        'address',
        'logo',
        'banner',
        'eiin_no',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('madrasa_code', $code);
    }

}