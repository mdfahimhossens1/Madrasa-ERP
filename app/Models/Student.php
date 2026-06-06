<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_id',
        'madrasa_id',
        'academic_year_id',
        'class_id',
        'section_id',
        'guardian_user_id',
        'is_hostel',
        'is_transport',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_hostel'    => 'boolean',
        'is_transport' => 'boolean',
        'status'       => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function madrasa()
    {
        return $this->belongsTo(Madrasa::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function guardian()
    {
        return $this->belongsTo(User::class, 'guardian_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function feeGroups()
    {
        return $this->belongsToMany(
            FeeGroup::class,
            'fee_group_student',
            'student_id',
            'fee_group_id'
        )->withPivot('academic_year_id')->withTimestamps();
    }

    public function currentFeeGroup()
    {
        $academicYearId = AcademicYear::where('is_current', 1)->value('id') ?? 1;

        return $this->belongsToMany(
            FeeGroup::class,
            'fee_group_student',
            'student_id',
            'fee_group_id'
        )->wherePivot('academic_year_id', $academicYearId)->first();
    }

   
}