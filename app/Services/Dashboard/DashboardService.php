<?php

namespace App\Services\Dashboard;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Attendance;
use App\Models\FeeCollection;
use App\Models\User;
use Carbon\Carbon;

class DashboardService
{
    public function getStatistics(User $user): array
    {
        $institutionId = $user->institution_id;

        return [

            'students' => Student::where('institution_id', $institutionId)->count(),

            'teachers' => Teacher::where('institution_id', $institutionId)->count(),

            'todayAttendance' => Attendance::whereDate('date', today())
                ->whereHas('student', function ($q) use ($institutionId) {
                    $q->where('institution_id', $institutionId);
                })
                ->where('status', 'present')
                ->count(),

            'todayCollection' => FeeCollection::where('institution_id', $institutionId)
                ->whereDate('collection_date', today())
                ->sum('paid_amount'),

            'totalDue' => FeeCollection::where('institution_id', $institutionId)
                ->sum('due_amount'),

        ];
    }

    public function recentAdmissions(User $user)
    {
        return Student::with('user')
            ->where('institution_id', $user->institution_id)
            ->latest()
            ->take(10)
            ->get();
    }

    public function attendanceChart(User $user): array
    {
        $labels = [];
        $values = [];

        for ($i = 1; $i <= now()->daysInMonth; $i++) {

            $date = Carbon::create(
                now()->year,
                now()->month,
                $i
            );

            $labels[] = $i;

            $values[] = Attendance::whereDate('date', $date)
                ->whereHas('student', function ($q) use ($user) {
                    $q->where('institution_id', $user->institution_id);
                })
                ->where('status', 'present')
                ->count();
        }

        return [

            'labels' => $labels,

            'values' => $values,

        ];
    }
}