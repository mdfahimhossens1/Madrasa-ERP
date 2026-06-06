<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassTeacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'section_id',
        'teacher_id',
        'academic_year_id',
        'is_class_teacher',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_class_teacher' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}