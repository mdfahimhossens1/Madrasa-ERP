<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'madrasa_id',
        'teacher_id',
        'designation',
        'joining_date',
        'qualification',
        'experience',
        'expertise_subjects',
        'salary_scale',
        'basic_salary',
        'is_class_teacher',
        'class_teacher_for',
        'remarks',
    ];

    protected $casts = [
        'joining_date' => 'date',
        'basic_salary' => 'decimal:2',
        'is_class_teacher' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function madrasa()
    {
        return $this->belongsTo(Madrasa::class);
    }

    public function classTeacherFor()
    {
        return $this->belongsTo(Classes::class, 'class_teacher_for');
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return $this->user ? $this->user->name : '';
    }

    public function getPhoneAttribute()
    {
        return $this->user ? $this->user->phone : '';
    }
}