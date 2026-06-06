<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentClassAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'class_id',
        'section_id',
        'academic_year_id',
        'roll_no',
        'assignment_date',
        'is_promoted',
        'promotion_date',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'promotion_date' => 'date',
        'is_promoted' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}