<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Madrasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InstitutionController extends Controller
{
    /**
     * Display listing
     */
public function index()
{
    $authUser = auth()->user();

    if ($authUser->is_super_admin) {

        $institutions = Madrasa::orderBy('name', 'asc')->get();

    } else {

        $institutions = Madrasa::where('id', $authUser->madrasa_id)->get();
    }

    return view('admin.institutions.index', compact('institutions')
    );
}

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.institutions.create');
    }

    /**
     * Store new madrasa
     */
    public function store(Request $request)
    {
        $request->validate([
            'madrasa_code'        => 'nullable|string|max:100|unique:madrasas,madrasa_code',
            'name'                => 'required|string|max:255',
            'name_bn'             => 'nullable|string|max:255',
            'email'               => 'nullable|email|max:255',
            'phone'               => 'nullable|string|max:30',
            'address'             => 'nullable|string|max:1000',
            'eiin_no'             => 'nullable|string|max:100',
            'status'              => 'required|in:active,inactive',
            'logo'                => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        DB::beginTransaction();

        try {

            $data = $request->except(['logo', 'banner']);

            $data['created_by'] = auth()->id();

            /**
             * Logo Upload
             */
            if ($request->hasFile('logo')) {

                $data['logo'] = $request
                    ->file('logo')
                    ->store('madrasas/logo', 'public');
            }

            /**
             * Banner Upload
             */
            if ($request->hasFile('banner')) {

                $data['banner'] = $request
                    ->file('banner')
                    ->store('madrasas/banner', 'public');
            }

            Madrasa::create($data);

            DB::commit();

            return redirect()
                ->route('dashboard.institutions.index')
                ->with('success', 'মাদরাসা সফলভাবে তৈরি করা হয়েছে।');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Edit form
     */
    public function edit($id)
    {
        $institution = Madrasa::findOrFail($id);

        return view('admin.institutions.edit', compact('institution'));
    }

    /**
     * Update madrasa
     */
    public function update(Request $request, $id)
    {
        $institution = Madrasa::findOrFail($id);

        $request->validate([
            'madrasa_code'        => 'nullable|string|max:100|unique:madrasas,madrasa_code,' . $id,
            'name'                => 'required|string|max:255',
            'name_bn'             => 'nullable|string|max:255',
            'email'               => 'nullable|email|max:255',
            'phone'               => 'nullable|string|max:30',
            'address'             => 'nullable|string|max:1000',
            'eiin_no'             => 'nullable|string|max:100',
            'status'              => 'required|in:active,inactive',
            'logo'                => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        DB::beginTransaction();

        try {

            $data = $request->except(['logo', 'banner']);

            /**
             * Update Logo
             */
            if ($request->hasFile('logo')) {

                if (
                    $institution->logo &&
                    Storage::disk('public')->exists($institution->logo)
                ) {
                    Storage::disk('public')->delete($institution->logo);
                }

                $data['logo'] = $request
                    ->file('logo')
                    ->store('madrasas/logo', 'public');
            }

            /**
             * Update Banner
             */
            if ($request->hasFile('banner')) {

                if (
                    $institution->banner &&
                    Storage::disk('public')->exists($institution->banner)
                ) {
                    Storage::disk('public')->delete($institution->banner);
                }

                $data['banner'] = $request
                    ->file('banner')
                    ->store('madrasas/banner', 'public');
            }

            $institution->update($data);

            DB::commit();

            return redirect()
                ->route('dashboard.institutions.index')
                ->with('success', 'মাদরাসার তথ্য আপডেট করা হয়েছে।');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete madrasa
     */
    public function destroy($id)
    {
        try {

            $institution = Madrasa::findOrFail($id);

            /**
             * Delete logo
             */
            if (
                $institution->logo &&
                Storage::disk('public')->exists($institution->logo)
            ) {
                Storage::disk('public')->delete($institution->logo);
            }

            /**
             * Delete banner
             */
            if (
                $institution->banner &&
                Storage::disk('public')->exists($institution->banner)
            ) {
                Storage::disk('public')->delete($institution->banner);
            }

            $institution->delete();

            return back()
                ->with('success', 'মাদরাসা সফলভাবে ডিলিট করা হয়েছে।');

        } catch (\Exception $e) {

            return back()
                ->with('error', $e->getMessage());
        }
    }
}