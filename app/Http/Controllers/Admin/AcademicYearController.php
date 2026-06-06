<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderBy('id', 'desc')->get();
        return view('admin.academic-years.index', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:academic_years',
            'name_bn' => 'nullable|string|max:50',
        ]);

        if ($request->is_current) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
        }

        AcademicYear::create($request->all());
        return redirect()->route('dashboard.academic-years.index')->with('success', 'শিক্ষাবর্ষ তৈরি হয়েছে');
    }

    // AJAX এর জন্য store মেথড
    public function storeAjax(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:academic_years'
        ]);

        try {
            $academicYear = AcademicYear::create([
                'name' => $request->name,
                'name_bn' => $request->name,
                'status' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'শিক্ষাবর্ষ যোগ করা হয়েছে!',
                'data' => $academicYear
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'যোগ করা ব্যর্থ হয়েছে: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        return response()->json(AcademicYear::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $year = AcademicYear::findOrFail($id);

        if ($request->is_current && !$year->is_current) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
        }

        $year->update($request->all());
        return redirect()->route('dashboard.academic-years.index')->with('success', 'আপডেট হয়েছে');
    }

    public function destroy($id)
    {
        AcademicYear::findOrFail($id)->delete();
        return back()->with('success', 'ডিলিট হয়েছে');
    }
}