<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\SystemPanel;
use Illuminate\Http\Request;

class SystemPanelManagerController extends Controller
{

    public function index(Request $request)
    {
        $roles = Role::orderBy('level')->get();

        $selectedRole = $request->role
            ? Role::findOrFail($request->role)
            : $roles->first();

        $panels = SystemPanel::where('is_active',1)
                    ->orderBy('serial')
                    ->get();

        return view(
            'admin.system-panel-manager.index',
            compact(
                'roles',
                'selectedRole',
                'panels'
            )
        );
    }

    public function update(Request $request)
    {
        $request->validate([
            'role_id'=>'required|exists:roles,id',
            'panels'=>'nullable|array'
        ]);

        $role=Role::findOrFail($request->role_id);

        $role->systemPanels()->sync($request->panels ?? []);

        return back()->with(
            'success',
            'Panel access updated successfully.'
        );
    }
}