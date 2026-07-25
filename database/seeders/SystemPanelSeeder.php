<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemPanel;

class SystemPanelSeeder extends Seeder
{
    public function run(): void
    {
        $panels = [

            [
                'panel_name' => 'Dashboard',
                'slug' => 'dashboard',
                'icon' => 'fas fa-home',
                'description' => 'Dashboard Module',
                'serial' => 1,
                'is_system' => true,
                'is_active' => true,
            ],

            [
                'panel_name' => 'Student',
                'slug' => 'student',
                'icon' => 'fas fa-user-graduate',
                'description' => 'Student Management Module',
                'serial' => 2,
                'is_system' => true,
                'is_active' => true,
            ],

            [
                'panel_name' => 'Teacher',
                'slug' => 'teacher',
                'icon' => 'fas fa-chalkboard-teacher',
                'description' => 'Teacher Management Module',
                'serial' => 3,
                'is_system' => true,
                'is_active' => true,
            ],

            [
                'panel_name' => 'Attendance',
                'slug' => 'attendance',
                'icon' => 'fas fa-calendar-check',
                'description' => 'Attendance Module',
                'serial' => 4,
                'is_system' => true,
                'is_active' => true,
            ],

            [
                'panel_name' => 'Exam',
                'slug' => 'exam',
                'icon' => 'fas fa-file-alt',
                'description' => 'Exam Management Module',
                'serial' => 5,
                'is_system' => true,
                'is_active' => true,
            ],

            [
                'panel_name' => 'Result',
                'slug' => 'result',
                'icon' => 'fas fa-chart-line',
                'description' => 'Result Management Module',
                'serial' => 6,
                'is_system' => true,
                'is_active' => true,
            ],

            [
                'panel_name' => 'Accounting',
                'slug' => 'accounting',
                'icon' => 'fas fa-wallet',
                'description' => 'Accounting Module',
                'serial' => 7,
                'is_system' => true,
                'is_active' => true,
            ],

            [
                'panel_name' => 'Payment',
                'slug' => 'payment',
                'icon' => 'fas fa-credit-card',
                'description' => 'Online Payment Module',
                'serial' => 8,
                'is_system' => true,
                'is_active' => true,
            ],

            [
                'panel_name' => 'Settings',
                'slug' => 'settings',
                'icon' => 'fas fa-cogs',
                'description' => 'Settings Module',
                'serial' => 9,
                'is_system' => true,
                'is_active' => true,
            ],

            [
                'panel_name' => 'Help',
                'slug' => 'help',
                'icon' => 'fas fa-life-ring',
                'description' => 'Help & Support Module',
                'serial' => 10,
                'is_system' => true,
                'is_active' => true,
            ],

            [
                'panel_name' => 'System',
                'slug' => 'system',
                'icon' => 'fas fa-server',
                'description' => 'System Administration Module',
                'serial' => 11,
                'is_system' => true,
                'is_active' => true,
            ],

        ];

        foreach ($panels as $panel) {

            SystemPanel::updateOrCreate(

                [
                    'slug' => $panel['slug']
                ],

                $panel

            );
        }
    }
}