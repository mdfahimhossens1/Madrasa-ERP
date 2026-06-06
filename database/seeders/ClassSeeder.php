<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classes;
use App\Models\Section;

class ClassSeeder extends Seeder
{
    // public function run(): void
    // {
    //     $institution = \App\Models\Madrasa::first();
        
    //     if (!$institution) {
    //         $institution = \App\Models\Madrasa::create([
    //             'name' => 'প্রধান প্রতিষ্ঠান',
    //             'name_bn' => 'প্রধান প্রতিষ্ঠান',
    //             'code' => 'INST-001',
    //             'status' => 'active',
    //         ]);
    //     }

    //     $classes = [
    //         [
    //             'madrasa_id' => $institution->id,
    //             'name' => 'Play',
    //             'name_bn' => 'প্লে',
    //             'level' => 'preschool',
    //             'description' => 'Play group',
    //             'status' => 'active'
    //         ],
    //         [
    //             'madrasa_id' => $institution->id,
    //             'name' => 'Nursery',
    //             'name_bn' => 'নার্সারি',
    //             'level' => 'preschool',
    //             'description' => 'Nursery',
    //             'status' => 'active'
    //         ],
    //         [
    //             'madrasa_id' => $institution->id,
    //             'name' => 'KG',
    //             'name_bn' => 'কেজি',
    //             'level' => 'preschool',
    //             'description' => 'Kindergarten',
    //             'status' => 'active'
    //         ],
    //         [
    //             'madrasa_id' => $institution->id,
    //             'name' => 'Class 1',
    //             'name_bn' => '১ম শ্রেণী',
    //             'level' => 'primary',
    //             'description' => 'First Grade',
    //             'status' => 'active'
    //         ],
    //         [
    //             'madrasa_id' => $institution->id,
    //             'name' => 'Class 2',
    //             'name_bn' => '২য় শ্রেণী',
    //             'level' => 'primary',
    //             'description' => 'Second Grade',
    //             'status' => 'active'
    //         ],
    //         [
    //             'madrasa_id' => $institution->id,
    //             'name' => 'Class 3',
    //             'name_bn' => '৩য় শ্রেণী',
    //             'level' => 'primary',
    //             'description' => 'Third Grade',
    //             'status' => 'active'
    //         ],
    //         [
    //             'madrasa_id' => $institution->id,
    //             'name' => 'Class 4',
    //             'name_bn' => '৪র্থ শ্রেণী',
    //             'level' => 'primary',
    //             'description' => 'Fourth Grade',
    //             'status' => 'active'
    //         ],
    //         [
    //             'madrasa_id' => $institution->id,
    //             'name' => 'Class 5',
    //             'name_bn' => '৫ম শ্রেণী',
    //             'level' => 'primary',
    //             'description' => 'Fifth Grade',
    //             'status' => 'active'
    //         ],
    //     ];

    //     foreach ($classes as $classData) {

    //         $created = Classes::updateOrCreate(
    //             [
    //                 'madrasa_id' => $classData['madrasa_id'],
    //                 'name' => $classData['name'],
    //             ],
    //             $classData
    //         );

    //         $primaryClasses = ['Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5'];
    //         $hasTwoSections = in_array($created->name, $primaryClasses);
            
    //         if ($hasTwoSections) {
    //             $sections = [
    //                 ['name' => 'A', 'name_bn' => 'ক'],
    //                 ['name' => 'B', 'name_bn' => 'খ'],
    //             ];
    //         } else {
    //             $sections = [
    //                 ['name' => 'A', 'name_bn' => 'ক'],
    //             ];
    //         }

    //         foreach ($sections as $section) {
    //             Section::updateOrCreate(
    //                 [
    //                     'class_id' => $created->id,
    //                     'name' => $section['name'],  
    //                 ],
    //                 [
    //                     'name_bn' => $section['name_bn'],
    //                     'madrasa_id' => $institution->id,
    //                     'is_active' => true,
    //                 ]
    //             );
    //         }
    //     }

    //     $this->command->info('✅ ক্লাস ও সেকশন সফলভাবে তৈরি হয়েছে!');
    // }
}