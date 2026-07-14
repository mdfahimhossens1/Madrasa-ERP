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

        $this->assignModules(
            'madrasa-admin',
            [
                'Student',
                'Teacher',
                'Guardian',
                'Admission',

                'Academic Year',
                'Class',
                'Section',

                'Attendance',
                'Exam',
                'Result',

                'Fee',
                'Fee Type',
                'Fee Setting',
                'Fee Collection',

                'Fund',
                'Ledger',
                'Sub Ledger',

                'Transaction',

                'Cashier',
                'Payment Method',

                'Income Expense Report',
                'Student Report',

                'Notification',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Teacher
        |--------------------------------------------------------------------------
        */

        $this->assignPermissions(
            'teacher',
            [
                'student.view',

                'attendance.view',
                'attendance.create',
                'attendance.edit',

                'result.view',
                'result.create',
                'result.edit',

                'exam.view',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Student
        |--------------------------------------------------------------------------
        */

        $this->assignPermissions(
            'student',
            [
                'result.view',
                'student.view',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Guardian
        |--------------------------------------------------------------------------
        */

        $this->assignPermissions(
            'guardian',
            [
                'student.view',
                'result.view',
            ]
        );
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

    protected function assignPermissions(string $roleSlug, array $slugs): void
    {
        $role = Role::where('slug', $roleSlug)->first();

        if (!$role) {
            return;
        }

        $permissionIds = Permission::whereIn('slug', $slugs)
            ->pluck('id')
            ->toArray();

        $role->permissions()->syncWithoutDetaching($permissionIds);
    }
}