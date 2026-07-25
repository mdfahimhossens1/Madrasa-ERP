<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\SystemPanel;

class RolePanelSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        $this->assignPanels('super-admin', [
            'dashboard',
            'student',
            'teacher',
            'attendance',
            'exam',
            'result',
            'accounting',
            'payment',
            'settings',
            'help',
            'system',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Soft Admin
        |--------------------------------------------------------------------------
        */

        $this->assignPanels('soft-admin', [
            'dashboard',
            'student',
            'teacher',
            'attendance',
            'exam',
            'result',
            'accounting',
            'payment',
            'settings',
            'help',
            'system',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Madrasa Admin
        |--------------------------------------------------------------------------
        */

        $this->assignPanels('madrasa-admin', [
            'dashboard',
            'student',
            'teacher',
            'attendance',
            'exam',
            'result',
            'accounting',
            'payment',
            'settings',
            'help',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Teacher
        |--------------------------------------------------------------------------
        */

        $this->assignPanels('teacher', [
            'dashboard',
            'student',
            'attendance',
            'exam',
            'result',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Student
        |--------------------------------------------------------------------------
        */

        $this->assignPanels('student', [
            'dashboard',
            'result',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Guardian
        |--------------------------------------------------------------------------
        */

        $this->assignPanels('guardian', [
            'dashboard',
            'result',
        ]);
    }

    protected function assignPanels(string $roleSlug, array $panelSlugs): void
    {
        $role = Role::where('slug', $roleSlug)->first();

        if (!$role) {
            return;
        }

        $panelIds = SystemPanel::whereIn('slug', $panelSlugs)
            ->pluck('id')
            ->toArray();

        $role->systemPanels()->sync($panelIds);
    }
}