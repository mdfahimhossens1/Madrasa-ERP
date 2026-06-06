<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_bn',
        'code',
        'academic_year_id',
        'class_id',
        'section_id',
        'type',
        'start_date',
        'end_date',
        'marks_distribution',
        'passing_marks',
        'total_marks',
        'is_active',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'marks_distribution' => 'array',
        'total_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByAcademicYear($query, $yearId)
    {
        return $query->where('academic_year_id', $yearId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('end_date', '<', now());
    }

    // ── Accessors ─────────────────────────────────────────

    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'weekly' => 'সাপ্তাহিক',
            'monthly' => 'মাসিক',
            'quarterly' => 'ত্রৈমাসিক',
            'half_yearly' => 'অর্ধ-বার্ষিক',
            'annual' => 'বার্ষিক',
            'test' => 'টেস্ট',
            default => 'অন্যান্য',
        };
    }

    public function getStatusAttribute()
    {
        if ($this->end_date < now()) {
            return 'completed';
        } elseif ($this->start_date <= now() && $this->end_date >= now()) {
            return 'ongoing';
        } else {
            return 'upcoming';
        }
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'completed' => 'সমাপ্ত',
            'ongoing' => 'চলমান',
            'upcoming' => 'আসন্ন',
            default => 'অজানা',
        };
    }

    // ── Helpers ────────────────────────────────────────────

    public function getTotalStudents()
    {
        return $this->class->admissions()
            ->where('academic_year_id', $this->academic_year_id)
            ->where('status', 'active')
            ->count();
    }

    public function getResultPublishedCount()
    {
        return $this->results()->count();
    }
}