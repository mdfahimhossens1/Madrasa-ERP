<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\SystemPanel;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::orderBy('level')->get();

        $selectedRole = $request->role
            ? Role::findOrFail($request->role)
            : $roles->first();

        $panels = SystemPanel::with([
            'permissions' => function ($query) {
                $query->orderBy('serial');
            }
        ])
        ->where('is_active', true)
        ->orderBy('serial')
        ->get();

        return view(
            'admin.role-permissions.index',
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

            'role_id' => 'required|exists:roles,id',

            'permissions' => 'nullable|array',

        ]);

        $role = Role::findOrFail($request->role_id);

        $role->permissions()->sync(
            $request->permissions ?? []
        );

        return back()->with(
            'success',
            'Role permissions updated successfully.'
        );
    }
}