<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        $this->assignAllPermissions('super-admin');


        $this->assignAllPermissions('soft-admin');
        /*
        |--------------------------------------------------------------------------
        | Support Admin
        |--------------------------------------------------------------------------
        */

        $this->assignModules(
            'support-admin',
            [
                'Institution',
                'User',
                'Role',
                'Permission',
                'Audit Log',
                'Package',
                'Subscription',
                'Software Control',
                'Settings',
                'Notification',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Madrasa Admin
        |--------------------------------------------------------------------------
        */

$this->assignPermissions('madrasa-admin',[

'dashboard.view',

'student.view',
'student.create',
'student.edit',
'student.delete',
'student.photo',
'student.report',
'student.export',
'student.import',

'guardian.view',
'guardian.create',
'guardian.edit',
'guardian.delete',

'admission.view',
'admission.create',
'admission.edit',
'admission.delete',
'admission.approve',
'admission.reject',

'class.view',
'class.create',
'class.edit',
'class.delete',

'section.view',
'section.create',
'section.edit',
'section.delete',

'attendance.view',
'attendance.take',
'attendance.edit',
'attendance.report',

'exam.view',
'exam.create',
'exam.edit',
'exam.delete',

'result.view',
'result.publish',
'result.edit',

'fee.view',
'fee.create',
'fee.edit',
'fee.collect',
'fee.report',

'fee-type.view',
'fee-type.create',
'fee-type.edit',

'income.view',
'income.create',

'expense.view',
'expense.create',

'invoice.view',
'invoice.print',

'payment.view',

'accounting-report.view',

]);

        /*
        |--------------------------------------------------------------------------
        | Teacher
        |--------------------------------------------------------------------------
        */

$this->assignPermissions('teacher',[

'dashboard.view',

'student.view',

'attendance.view',
'attendance.take',
'attendance.edit',

'exam.view',

'result.view',
'result.edit',

]);

        /*
        |--------------------------------------------------------------------------
        | Student
        |--------------------------------------------------------------------------
        */

$this->assignPermissions('student',[

'dashboard.view',

'result.view',

]);

        /*
        |--------------------------------------------------------------------------
        | Guardian
        |--------------------------------------------------------------------------
        */

$this->assignPermissions('guardian',[

'dashboard.view',

'student.view',

'result.view',

]);
    }

    protected function assignAllPermissions(string $roleSlug): void
    {
        $role = Role::where('slug', $roleSlug)->first();

        if (!$role) {
            return;
        }

        $role->permissions()->sync(
            Permission::pluck('id')->toArray()
        );
    }

    protected function assignModules(string $roleSlug, array $modules): void
    {
        $role = Role::where('slug', $roleSlug)->first();

        if (!$role) {
            return;
        }

        $permissionIds = Permission::whereIn('module', $modules)
            ->pluck('id')
            ->toArray();

        $role->permissions()->syncWithoutDetaching($permissionIds);
    }

    protected function assignPermissions(
    string $roleSlug,
    array $permissions
): void {

    $role = Role::where('slug', $roleSlug)->first();

    if (!$role) {
        return;
    }

    $permissionIds = Permission::whereIn(
        'slug',
        $permissions
    )->pluck('id');

    $role->permissions()->sync($permissionIds);
}
}