<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Exam;
use App\Models\Result;
use App\Models\FeeGroup;
use App\Models\FeeCollection;
use App\Models\Madrasa;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $studentRole = Role::where('role_name', 'student')->first();
        $teacherRole = Role::where('role_name', 'teacher')->first();

        $studentsCount = $studentRole ? User::where('role_id', $studentRole->id)->count() : 0;
        $teachersCount = $teacherRole ? User::where('role_id', $teacherRole->id)->count() : 0;

        $madrasasCount = Madrasa::count();
        $studentsTableCount = Student::count();
        $teachersTableCount = Teacher::count();
        $examsCount = Exam::count();
        $resultsCount = Result::count();

        // 🔥 FIXED LOGIC
        $totalFees = FeeGroup::sum('amount');
        $totalPaid = FeeCollection::sum('paid_amount');
        $totalDue = FeeCollection::sum('due_amount');

        return view('admin.dashboard.home', compact(
            'studentsCount',
            'teachersCount',
            'madrasasCount',
            'studentsTableCount',
            'teachersTableCount',
            'examsCount',
            'resultsCount',
            'totalFees',
            'totalPaid',
            'totalDue'
        ));
    }
}