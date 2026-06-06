<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    public function index()
    {
        $classes = Classes::with(['sections' => function($q) {
            $q->orderBy('name', 'asc');
        }])
        ->where('status', 'active')
        ->orderBy('name', 'asc')
        ->get();
        
        return view('admin.sections.index', compact('classes'));
    }

   public function store(Request $request)
{
    $request->validate([
        'class_id'  => 'required|exists:classes,id',
        'name'      => 'required|string|max:100',
        'name_bn'   => 'nullable|string|max:100',
        'is_active' => 'nullable|in:0,1',
    ]);

    try {
        $section = Section::updateOrCreate(
            [
                'class_id' => $request->class_id,
                'name' => $request->name,
            ],
            [
                'name_bn'   => $request->name_bn,
                'is_active' => $request->is_active == '1' ? true : false,
                'created_by' => auth()->id(),
            ]
        );

        $message = $section->wasRecentlyCreated ? 'সেকশন সফলভাবে তৈরি করা হয়েছে।' : 'সেকশন ইতিমধ্যে ছিল, আপডেট করা হয়েছে।';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => $message,
                'data' => $section
            ]);
        }

        return redirect()->route('dashboard.sections.index')
            ->with('success', $message);

    } catch (\Exception $e) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false, 
                'message' => 'সমস্যা হয়েছে: ' . $e->getMessage()
            ], 500);
        }

        return back()->with('error', 'সমস্যা হয়েছে: ' . $e->getMessage())->withInput();
    }
}

    public function edit($id)
    {
        $section = Section::with('class')->findOrFail($id);
        
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($section);
        }
        
        return view('admin.sections.edit', compact('section'));
    }

    public function update(Request $request, $id)
    {
        $section = Section::findOrFail($id);

        $request->validate([
            'class_id'  => 'required|exists:classes,id',
            'name'      => 'required|string|max:100',
            'name_bn'   => 'nullable|string|max:100',
            'is_active' => 'nullable|in:0,1',
        ]);

        try {
            $section->update([
                'class_id'   => $request->class_id,
                'name'       => $request->name,
                'name_bn'    => $request->name_bn,
                'is_active'  => $request->is_active == '1' ? true : false,
                'updated_by' => auth()->id(),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'সেকশন আপডেট করা হয়েছে।',
                    'data' => $section
                ]);
            }

            return redirect()->route('dashboard.sections.index')
                ->with('success', 'সেকশন আপডেট করা হয়েছে।');

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'সমস্যা হয়েছে: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $section = Section::findOrFail($id);
            
            if ($section->admissions()->count() > 0) {
                if (request()->ajax()) {
                    return response()->json(['success' => false, 'message' => 'এই সেকশনে শিক্ষার্থী ভর্তি থাকায় ডিলিট করা সম্ভব নয়।']);
                }
                return back()->with('error', 'এই সেকশনে শিক্ষার্থী ভর্তি থাকায় ডিলিট করা সম্ভব নয়।');
            }
            
            $section->delete();
            
            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'সেকশন ডিলিট করা হয়েছে।']);
            }
            
            return back()->with('success', 'সেকশন ডিলিট করা হয়েছে।');
            
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'সমস্যা হয়েছে: ' . $e->getMessage()]);
            }
            return back()->with('error', 'সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }
}