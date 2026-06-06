<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeeType;

class FeeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $feeTypes = [
            [
                'name' => 'Admission Fee',
                'is_active' => 1,
            ],
            [
                'name' => 'Monthly Fee',
                'is_active' => 1,
            ],
            [
                'name' => 'Exam Fee',
                'is_active' => 1,
            ],
            [
                'name' => 'Hostel Fee',
                'is_active' => 1,
            ],
            [
                'name' => 'Library Fee',
                'is_active' => 1,
            ],
        ];

        foreach ($feeTypes as $type) {
            FeeType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}