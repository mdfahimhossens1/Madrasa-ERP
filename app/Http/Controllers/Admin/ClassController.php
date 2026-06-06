<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Madrasa; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function index()
    {
        $madrasas = Madrasa::where('status', 'active')->orderBy('name', 'asc')->get();
        
        if ($madrasas->isEmpty()) {
            $madrasas = collect([
                (object)['id' => 0, 'name' => 'কোনো প্রতিষ্ঠান নেই']
            ]);
        }

        $classes = Classes::withCount(['sections' => function($query) {
            $query->where('is_active', true);
        }])
        ->orderBy('name', 'asc')
        ->get();
        
        return view('admin.classes.index', compact('classes', 'madrasas'));
    }

    public function create()
    {
        $madrasas = Madrasa::where('status', 'active')->orderBy('name', 'asc')->get();
        return view('admin.classes.create', compact('madrasas'));
    }

public function store(Request $request)
{
    $request->validate([
        'name'   => 'required|string|max:100|unique:classes,name',
        'name_bn'=> 'required|string|max:100',
        'status' => 'in:active,inactive',
    ]);

    Classes::create([
        'madrasa_id' => auth()->user()->madrasa_id, // 🔥 FIXED HERE
        'name'       => $request->name,
        'name_bn'    => $request->name_bn,
        'status'     => $request->status ?? 'active',
        'created_by' => auth()->id(),
    ]);

    return redirect()->route('dashboard.classes.index')
        ->with('success', 'ক্লাস সফলভাবে তৈরি করা হয়েছে।');
}

    // AJAX এর জন্য store মেথড
    public function storeAjax(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:classes,name'
        ]);

        try {
            $defaultMadrasas = Madrasa::where('status', 'active')->first();
            
            if (!$defaultMadrasas) {
                
                $defaultMadrasas = Madrasa::create([
                    'name' => 'ডিফল্ট প্রতিষ্ঠান',
                    'name_bn' => 'ডিফল্ট প্রতিষ্ঠান',
                    'status' => 'active'
                ]);
            }

            $class = Classes::create([
                'name' => $request->name,
                'name_bn' => $request->name,
                'status' => 'active',
                'madrasa_id' => $defaultMadrasas->id,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'ক্লাস সফলভাবে যোগ করা হয়েছে!',
                'data' => $class
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
        $class = Classes::findOrFail($id);
        $madrasas = Madrasa::where('status', 'active')->orderBy('name', 'asc')->get();

        if (request()->ajax()) {
            return response()->json($class);
        }
        
        return view('admin.classes.edit', compact('class', 'madrasas'));
    }

public function update(Request $request, $id)
{
    $class = Classes::findOrFail($id);

    $request->validate([
        'name'   => 'required|string|max:100|unique:classes,name,' . $id,
        'name_bn'=> 'required|string|max:100',
        'status' => 'in:active,inactive',
    ]);

    $class->update([
        'madrasa_id' => auth()->user()->madrasa_id, // 🔥 FIXED
        'name'       => $request->name,
        'name_bn'    => $request->name_bn,
        'status'     => $request->status ?? 'active',
        'updated_by' => auth()->id(),
    ]);

    return back()->with('success', 'ক্লাস আপডেট করা হয়েছে।');
}

    public function destroy($id)
    {
        try {
            $class = Classes::findOrFail($id);
            
            if ($class->sections()->count() > 0) {
                return back()->with('error', 'এই ক্লাসে সেকশন থাকায় ডিলিট করা সম্ভব নয়। আগে সেকশন ডিলিট করুন।');
            }
            
            $class->delete();
            
            return back()->with('success', 'ক্লাস ডিলিট করা হয়েছে।');
            
        } catch (\Exception $e) {
            return back()->with('error', 'সমস্যা হয়েছে: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $class = Classes::findOrFail($id);
        $class->status = $class->status == 'active' ? 'inactive' : 'active';
        $class->save();
        
        return back()->with('success', 'স্ট্যাটাস পরিবর্তন করা হয়েছে।');
    }
}