<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemPanel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SystemPanelController extends Controller
{

    public function index()
    {
        $panels = SystemPanel::orderBy('serial')->get();

        return view('admin.system-panels.index', compact('panels'));
    }

    public function create()
    {
        return view('admin.system-panels.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'panel_name' => 'required|max:100',
            'slug' => 'nullable|max:100|unique:system_panels,slug',
            'icon' => 'nullable|max:100',
            'serial' => 'nullable|integer',
        ]);

        SystemPanel::create([
            'panel_name' => $request->panel_name,
            'slug' => $request->slug
                ? Str::slug($request->slug)
                : Str::slug($request->panel_name),

            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->status ? 1 : 0,
        ]);

        return redirect()
            ->route('dashboard.system-panels.index')
            ->with('success', 'System Panel created successfully.');
    }

    public function edit(SystemPanel $system_panel)
    {
        return view('admin.system-panels.edit', [
            'panel' => $system_panel
        ]);
    }

    public function update(Request $request, SystemPanel $system_panel)
    {
        $request->validate([
            'panel_name' => 'required|max:100',
            'slug' => 'nullable|max:100|unique:system_panels,slug,' . $system_panel->id,
            'icon' => 'nullable|max:100',
            'serial' => 'nullable|integer',
        ]);

        $system_panel->update([
            'panel_name' => $request->panel_name,
            'slug' => $request->slug
                ? Str::slug($request->slug)
                : Str::slug($request->panel_name),

            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->status ? 1 : 0,
        ]);

        return redirect()
            ->route('dashboard.system-panels.index')
            ->with('success', 'System Panel updated successfully.');
    }

public function destroy(SystemPanel $system_panel)
{
    if ($system_panel->roles()->exists()) {

        return back()->with('error','This panel is assigned to one or more roles.');
    }

    $system_panel->delete();

    return back()->with('success','System Panel deleted successfully.');
}
}