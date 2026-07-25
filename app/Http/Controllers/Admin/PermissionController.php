<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\SystemPanel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Display Permission List
     */
public function index()
{
    $permissions = Permission::with('panel')
        ->orderBy('system_panel_id')
        ->orderBy('module')
        ->orderBy('serial')
        ->get();

    $panels = $permissions->groupBy('system_panel_id')->map(function ($items) {
        $first = $items->first();

        return (object) [
            'id' => $first->panel->id,
            'panel_name' => $first->panel->panel_name,
            'icon' => $first->panel->icon,
            'permissions' => $items,
        ];
    })->values();

    return view('admin.permissions.index', compact('panels'));
}
    /**
     * Store Permission
     */
public function store(Request $request)
{
    $request->validate([
        'system_panel_id' => 'required|exists:system_panels,id',
        'module' => 'required|string|max:100',
        'permission_name' => 'required|string|max:150',
        'slug' => 'required|string|max:150|unique:permissions,slug',
        'description' => 'nullable|string',
        'serial' => 'nullable|integer|min:0',
        'is_active' => 'nullable|boolean',
    ]);

    Permission::create([
        'system_panel_id' => $request->system_panel_id,
        'module' => $request->module,
        'permission_name' => $request->permission_name,
        'slug' => $request->slug,
        'description' => $request->description,
        'serial' => $request->serial ?? 0,
        'is_system' => true,
        'is_active' => $request->has('is_active') ? 1 : 0,
    ]);

    return redirect()
        ->route('dashboard.permissions.index')
        ->with('success', 'Permission created successfully.');
}

    /**
     * Edit
     */
    public function edit(Permission $permission)
    {
        return response()->json($permission);
    }

    /**
     * Update
     */

public function update(Request $request, Permission $permission)
{
    $request->validate([
        'system_panel_id' => 'required|exists:system_panels,id',
        'permission_name' => 'required|string|max:150',
        'slug' => 'required|string|max:150|unique:permissions,slug,' . $permission->id,
        'module' => 'required|string|max:100',
        'description' => 'nullable|string',
        'serial' => 'nullable|integer|min:0',
        'is_active' => 'nullable|boolean',
    ]);

    $permission->update([
        'system_panel_id' => $request->system_panel_id,
        'permission_name' => $request->permission_name,
        'slug' => $request->slug,
        'module' => $request->module,
        'description' => $request->description,
        'serial' => $request->serial ?? 0,
        'is_active' => $request->has('is_active') ? 1 : 0,
    ]);

    return redirect()
        ->route('dashboard.permissions.index')
        ->with('success', 'Permission updated successfully.');
}

    /**
     * Delete
     */
public function destroy(Permission $permission)
{
    if ($permission->roles()->exists()) {
        return back()->with('error', 'This permission is assigned to one or more roles.');
    }

    $permission->delete();

    return back()->with('success', 'Permission deleted successfully.');
}
}