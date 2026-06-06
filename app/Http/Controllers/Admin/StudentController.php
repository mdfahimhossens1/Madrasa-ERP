<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Models\Classes;
use App\Models\AcademicYear;
use App\Models\FeeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'class']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_bn', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })->orWhere('student_id', 'like', "%{$search}%");
        }

        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(15);
        $classes = Classes::where('status', 'active')->get();

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes = Classes::where('status', 'active')->get();
        $feeGroups = FeeGroup::where('is_active', 1)->get();
        return view('admin.students.create', compact('classes', 'feeGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_bn' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'gender' => 'required|in:male,female,other',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'blood_group' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'class_id' => 'required|exists:classes,id',
            'fee_group_id' => 'nullable|exists:fee_groups,id',
        ]);

        try {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('students/photos', 'public');
            }

            // Create User
            $user = User::create([
                'username' => $this->generateUsername($request->name_bn),
                'password' => Hash::make($request->password ?? 'password123'),
                'name' => $request->name_bn,
                'name_bn' => $request->name_bn,
                'phone' => $request->phone,
                'phone2' => $request->phone2,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'blood_group' => $request->blood_group,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'photo' => $photoPath,
                'role_id' => 5,
                'status' => 1,
                'created_by' => auth()->id(),
            ]);

            // Generate Student ID
            $latestStudent = Student::orderBy('id', 'desc')->first();
            $newId = $latestStudent ? $latestStudent->id + 1 : 1;
            $studentId = 'STU-' . date('Y') . '-' . str_pad($newId, 5, '0', STR_PAD_LEFT);

            // Academic Year
            $academicYearId = $request->academic_year_id ?? AcademicYear::where('status', 1)->latest()->first()->id ?? 1;

            // Create Student
            $student = Student::create([
                'user_id'         => $user->id,
                'student_id'      => $studentId,
                'class_id'        => $request->class_id,
                'madrasa_id'      => $request->madrasa_id ?? auth()->user()->madrasa_id ?? 1,
                'status'          => 1,
                'academic_year_id'=> $academicYearId,
                'created_by'      => auth()->id(),
            ]);

            // ============================================================
            // NEW: Assign Fee Group to Student
            // ============================================================
            $feeGroupId = $request->fee_group_id ?? FeeGroup::where('is_active', 1)->first()?->id;
            
            if ($feeGroupId) {
                DB::table('fee_group_student')->insert([
                    'fee_group_id' => $feeGroupId,
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYearId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect()->route('dashboard.students.index')
                ->with('success', 'শিক্ষার্থী তৈরি করা হয়েছে। আইডি: ' . $studentId);

        } catch (\Exception $e) {
            return back()->with('error', 'শিক্ষার্থী তৈরি করতে সমস্যা হয়েছে: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $student = Student::with(['user', 'class', 'admissions' => function($q) {
            $q->with('academicYear', 'class')->latest();
        }, 'feeGroups'])->findOrFail($id);
        
        return response()->json([
            'id' => $student->id,
            'student_id' => $student->student_id,
            'user' => $student->user,
            'class_id' => $student->class_id,
            'status' => $student->status,
            'fee_groups' => $student->feeGroups,
        ]);
    }

    public function edit($id)
    {
        $student = Student::with('user', 'feeGroups')->findOrFail($id);
        $classes = Classes::where('status', 'active')->get();
        $feeGroups = FeeGroup::where('is_active', 1)->get();
        
        return view('admin.students.edit', compact('student', 'classes', 'feeGroups'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::with('user')->findOrFail($id);

        $request->validate([
            'name_bn' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $student->user_id,
            'gender' => 'required|in:male,female,other',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'blood_group' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive,transferred,graduated',
            'fee_group_id' => 'nullable|exists:fee_groups,id',
        ]);

        try {
            if ($request->hasFile('photo')) {
                if ($student->user->photo) {
                    Storage::disk('public')->delete($student->user->photo);
                }
                $photoPath = $request->file('photo')->store('students/photos', 'public');
                $student->user->photo = $photoPath;
            }

            $student->user->update([
                'name' => $request->name_bn,
                'name_bn' => $request->name_bn,
                'phone' => $request->phone,
                'phone2' => $request->phone2,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'blood_group' => $request->blood_group,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'updated_by' => auth()->id(),
            ]);

            $student->update([
                'class_id' => $request->class_id,
                'status' => $request->status,
                'updated_by' => auth()->id(),
            ]);

            if ($request->filled('password')) {
                $student->user->update(['password' => Hash::make($request->password)]);
            }

            // Update fee group assignment
            if ($request->has('fee_group_id')) {
                $academicYearId = AcademicYear::where('status', 1)->latest()->first()->id ?? 1;
                
                DB::table('fee_group_student')
                    ->where('student_id', $student->id)
                    ->where('academic_year_id', $academicYearId)
                    ->delete();
                
                if ($request->fee_group_id) {
                    DB::table('fee_group_student')->insert([
                        'fee_group_id' => $request->fee_group_id,
                        'student_id' => $student->id,
                        'academic_year_id' => $academicYearId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            return redirect()->route('dashboard.students.index')
                ->with('success', 'শিক্ষার্থীর তথ্য আপডেট করা হয়েছে।');

        } catch (\Exception $e) {
            return back()->with('error', 'আপডেট করতে সমস্যা হয়েছে: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        
        if ($student->admissions()->exists()) {
            return back()->with('error', 'এই শিক্ষার্থীর ভর্তি তথ্য রয়েছে। প্রথমে সেগুলো ডিলিট করুন।');
        }
        
        // Delete fee group assignments
        DB::table('fee_group_student')->where('student_id', $student->id)->delete();
        
        if ($student->user->photo) {
            Storage::disk('public')->delete($student->user->photo);
        }
        
        $student->user->delete();
        $student->delete();
        
        return redirect()->route('dashboard.students.index')
            ->with('success', 'শিক্ষার্থী ডিলিট করা হয়েছে।');
    }

    public function search(Request $request)
    {
        $q = $request->get('q');
        
        $students = Student::with(['user', 'class'])
            ->where('madrasa_id', auth()->user()->madrasa_id ?? 1)
            ->where(function ($query) use ($q) {
                $query->where('student_id', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($userQuery) use ($q) {
                        $userQuery->where('name', 'like', "%{$q}%")
                            ->orWhere('name_bn', 'like', "%{$q}%")
                            ->orWhere('phone', 'like', "%{$q}%");
                    });
            })
            ->limit(20)
            ->get();
        
        return response()->json($students->map(function($student) {
            return [
                'id' => $student->id,
                'student_id' => $student->student_id,
                'name' => $student->user->name_bn ?? $student->user->name,
                'phone' => $student->user->phone,
                'father_name' => $student->user->father_name,
                'photo' => $student->user->photo ? asset('storage/' . $student->user->photo) : null,
            ];
        }));
    }

    private function generateUsername($name)
    {
        $cleanName = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($name));
        if (empty($cleanName)) {
            $cleanName = 'student_' . rand(1000, 9999);
        }
        
        $username = $cleanName;
        $counter = 1;
        
        while (User::where('username', $username)->exists()) {
            $username = $cleanName . $counter;
            $counter++;
        }
        
        return $username;
    }
}