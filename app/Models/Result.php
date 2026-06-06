<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Result extends Model
{
    use HasFactory; 

    protected $fillable = [
        'exam_id',
        'student_id',
        'admission_id',
        'subject',
        'subject_bn',
        'subject_code',
        'theory_marks',
        'practical_marks',
        'total_marks',
        'obtained_marks',
        'percentage',
        'grade',
        'grade_point',
        'is_passed',
        'remarks',
        'published_by',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'theory_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'obtained_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'grade_point' => 'decimal:2',
        'is_passed' => 'boolean',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }

    public function publishedBy()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeByExam($query, $examId)
    {
        return $query->where('exam_id', $examId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopePassed($query)
    {
        return $query->where('is_passed', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('is_passed', false);
    }

    // Accessors
    public function getGradeLabelAttribute()
    {
        $grades = [
            'A+' => 'A+ (80%+)',
            'A' => 'A (70-79%)',
            'A-' => 'A- (60-69%)',
            'B' => 'B (50-59%)',
            'C' => 'C (40-49%)',
            'D' => 'D (33-39%)',
            'F' => 'F (0-32%)',
        ];
        return $grades[$this->grade] ?? $this->grade;
    }

    public function getStatusLabelAttribute()
    {
        return $this->is_passed ? 'পাস' : 'ফেল';
    }

    public function getStatusColorAttribute()
    {
        return $this->is_passed ? 'success' : 'danger';
    }

    // Helpers
    public static function calculateGrade($percentage)
    {
        if ($percentage >= 80) {
            return ['grade' => 'A+', 'grade_point' => 5.00];
        } elseif ($percentage >= 70) {
            return ['grade' => 'A', 'grade_point' => 4.00];
        } elseif ($percentage >= 60) {
            return ['grade' => 'A-', 'grade_point' => 3.50];
        } elseif ($percentage >= 50) {
            return ['grade' => 'B', 'grade_point' => 3.00];
        } elseif ($percentage >= 40) {
            return ['grade' => 'C', 'grade_point' => 2.00];
        } elseif ($percentage >= 33) {
            return ['grade' => 'D', 'grade_point' => 1.00];
        } else {
            return ['grade' => 'F', 'grade_point' => 0.00];
        }
    }

    public function calculate()
    {
        $this->total_marks = ($this->theory_marks ?? 0) + ($this->practical_marks ?? 0);
        $this->percentage = ($this->obtained_marks / $this->total_marks) * 100;
        
        $gradeInfo = self::calculateGrade($this->percentage);
        $this->grade = $gradeInfo['grade'];
        $this->grade_point = $gradeInfo['grade_point'];
        $this->is_passed = $this->grade !== 'F';
        
        $this->save();
    }
}