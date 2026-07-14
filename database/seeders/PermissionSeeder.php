<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [

            'User',
            'Institution',
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

            'Settings',

            'Role',
            'Permission',

            'Audit Log',

            'Package',

            'Subscription',

            'Software Control',
        ];

        $actions = [

            'view',
            'create',
            'edit',
            'delete',
            'export',
            'approve',
        ];

        foreach ($modules as $module) {

            foreach ($actions as $action) {

                Permission::firstOrCreate(

                    [
                        'slug' => strtolower(
                            str_replace(' ', '-', $module)
                        ) . '.' . $action,
                    ],

                    [
                        'module' => $module,

                        'permission_name' =>
                            ucfirst($action) . ' ' . $module,

                        'description' =>
                            ucfirst($action) .
                            ' permission for ' .
                            $module,

                        'status' => 1,
                    ]

                );
            }
        }
    }
}