<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\SystemPanel;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // এখানে শুধু module এবং তার actions সংরক্ষণ করব
        $modules = [

            /*
            |--------------------------------------------------------------------------
            | Dashboard Module
            |--------------------------------------------------------------------------
            */
            'dashboard' => [
                'Dashboard' => ['view', 'analytics', 'export'],
            ],

            /*
            |--------------------------------------------------------------------------
            | Student Module
            |--------------------------------------------------------------------------
            */
            'student' => [
                'Student' => ['view', 'create', 'edit', 'delete', 'photo', 'report', 'export', 'import'],
                'Guardian' => ['view', 'create', 'edit', 'delete'],
                'Admission' => ['view', 'create', 'edit', 'delete', 'approve', 'reject'],
                'Class' => ['view', 'create', 'edit', 'delete'],
                'Section' => ['view', 'create', 'edit', 'delete'],
            ],

            /*
            |--------------------------------------------------------------------------
            | Teacher Module
            |--------------------------------------------------------------------------
            */
            'teacher' => [
                'Teacher' => ['view', 'create', 'edit', 'delete', 'photo', 'report', 'export', 'import'],
            ],

            /*
            |--------------------------------------------------------------------------
            | Attendance Module
            |--------------------------------------------------------------------------
            */
            'attendance' => [
                'Attendance' => ['view', 'take', 'edit', 'delete', 'report', 'export'],
            ],

            /*
            |--------------------------------------------------------------------------
            | Exam Module
            |--------------------------------------------------------------------------
            */
            'exam' => [
                'Exam' => ['view', 'create', 'edit', 'delete', 'schedule', 'routine'],
                'Exam Type' => ['view', 'create', 'edit', 'delete'],
            ],

            /*
            |--------------------------------------------------------------------------
            | Result Module
            |--------------------------------------------------------------------------
            */
            'result' => [
                'Result' => ['view', 'publish', 'edit', 'delete', 'export', 'print'],
                'Grade' => ['view', 'create', 'edit', 'delete'],
            ],

            /*
            |--------------------------------------------------------------------------
            | Accounting Module
            |--------------------------------------------------------------------------
            */
            'accounting' => [
                'Fee' => ['view', 'create', 'edit', 'delete', 'collect', 'report', 'export'],
                'Fee Type' => ['view', 'create', 'edit', 'delete'],
                'Income' => ['view', 'create', 'edit', 'delete'],
                'Expense' => ['view', 'create', 'edit', 'delete'],
                'Invoice' => ['view', 'create', 'edit', 'delete', 'print'],
                'Payment' => ['view', 'create', 'edit', 'delete'],
                'Report' => ['view', 'export', 'print'],
            ],

            /*
            |--------------------------------------------------------------------------
            | Payment Module
            |--------------------------------------------------------------------------
            */
            'payment' => [
                'Online Payment' => ['view', 'create', 'process', 'verify', 'report'],
                'Payment Gateway' => ['view', 'create', 'edit', 'delete'],
            ],

            /*
            |--------------------------------------------------------------------------
            | Settings Module
            |--------------------------------------------------------------------------
            */
            'settings' => [
                'General' => ['view', 'edit'],
                'Academic' => ['view', 'create', 'edit', 'delete'],
                'Institution' => ['view', 'create', 'edit', 'delete'],
                'Month' => ['view', 'create', 'edit', 'delete'],
                'Language' => ['view', 'edit'],
                'Theme' => ['view', 'edit'],
                'Backup' => ['view', 'create', 'restore', 'delete'],
            ],

            /*
            |--------------------------------------------------------------------------
            | Help Module
            |--------------------------------------------------------------------------
            */
            'help' => [
                'Support' => ['view', 'create', 'edit', 'delete'],
                'FAQ' => ['view', 'create', 'edit', 'delete'],
                'Ticket' => ['view', 'create', 'edit', 'delete'],
            ],

            /*
            |--------------------------------------------------------------------------
            | System Module
            |--------------------------------------------------------------------------
            */
            'system' => [
                'User' => ['view', 'create', 'edit', 'delete', 'photo', 'report'],
                'Role' => ['view', 'create', 'edit', 'delete', 'permission'],
                'Permission' => ['view', 'create', 'edit', 'delete'],
                'System Panel' => ['view', 'create', 'edit', 'delete'],
                'Activity Log' => ['view', 'export', 'delete'],
                'System Setting' => ['view', 'edit'],
            ],

        ];

        $serial = 1; // Serial counter

        foreach ($modules as $panelSlug => $modulesData) {
            
            // Panel খুঁজে বের করি
            $panel = SystemPanel::where('slug', $panelSlug)->first();

            // Panel না থাকলে skip করি
            if (!$panel) {
                continue;
            }

            foreach ($modulesData as $module => $actions) {

                foreach ($actions as $action) {

                    // Slug বানাই: panel.module.action
                    $slug = strtolower(
                        str_replace(' ', '-', $module)
                        . '.'
                        . $action
                    );
                    // অথবা চাইলে এভাবেও করতে পার: panel.action (যেমন student.view)
                    // $slug = strtolower($panelSlug . '.' . $action);

                    // Permission name তৈরি করি (যথাযথ formatting সহ)
                    $permissionName = ucwords(str_replace('.', ' ', $slug));

                    Permission::updateOrCreate(
                        [
                            'slug' => $slug,
                        ],
                        [
                            'system_panel_id' => $panel->id,
                            'module' => $module,
                            'permission_name' => $permissionName,
                            'description' => null,
                            'is_system' => true,
                            'is_active' => true,  // ✅ status → is_active
                            'serial' => $serial++, // ✅ Serial যোগ করলাম
                        ]
                    );

                }

            }
        }
    }
}