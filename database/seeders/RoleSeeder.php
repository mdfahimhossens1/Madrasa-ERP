<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Madrasa;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Roles Create
        |--------------------------------------------------------------------------
        */

        $roles = [
            [
                'role_name' => 'Super Admin',
                'slug' => 'super-admin',
                'level' => 1,
                'is_system' => 1,
            ],
            [
                'role_name' => 'Soft Admin',
                'slug' => 'soft-admin',
                'level' => 2,
                'is_system' => 1,
            ],
            [
                'role_name' => 'Madrasa Admin',
                'slug' => 'madrasa-admin',
                'level' => 3,
                'is_system' => 1,
            ],
            [
                'role_name' => 'Teacher',
                'slug' => 'teacher',
                'level' => 4,
                'is_system' => 1,
            ],
            [
                'role_name' => 'Student',
                'slug' => 'student',
                'level' => 5,
                'is_system' => 1,
            ],
            [
                'role_name' => 'Guardian',
                'slug' => 'guardian',
                'level' => 6,
                'is_system' => 1,
            ],
        ];

        foreach ($roles as $roleData) {

            Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                [
                    'role_name'  => $roleData['role_name'],
                    'level'      => $roleData['level'],
                    'is_system'  => $roleData['is_system'],
                    'description'=> $this->getRoleDescription($roleData['slug']),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Get Role IDs
        |--------------------------------------------------------------------------
        */

        $superAdminRoleId   = Role::where('slug', 'super-admin')->value('id');
        $softAdminRoleId    = Role::where('slug', 'soft-admin')->value('id');
        $madrasaAdminRoleId = Role::where('slug', 'madrasa-admin')->value('id');

        /*
        |--------------------------------------------------------------------------
        | Super Admin Create
        |--------------------------------------------------------------------------
        */

        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'institution_user_id' => '601',

                'name'       => 'Super Admin',
                'name_bn'    => 'সুপার অ্যাডমিন',
                'username'   => 'superadmin',
                'phone'      => '01700000000',
                'password'   => Hash::make('fahim'),

                'role_id'    => $superAdminRoleId,
                'madrasa_id' => null,

                'status'     => 1,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Madrasa 1 Create
        |--------------------------------------------------------------------------
        */

        $madrasa = Madrasa::updateOrCreate(
            ['madrasa_code' => 'ALHUDA001'],
            [
                'name'                  => 'আল-হুদা ইসলামি একাডেমি',
                'name_bn'               => 'আল-হুদা ইসলামি একাডেমি',
                'email'                 => 'info@alhuda.edu.bd',
                'phone'                 => '01700000001',
                'address'               => 'ধানমন্ডি, ঢাকা',
                'city'                  => 'ঢাকা',
                'state'                 => 'ঢাকা',
                'postal_code'           => '1209',
                'country'               => 'Bangladesh',
                'eiin_no'               => '123456',
                'established_year'      => 2020,
                'status'                => 1,
                'subscription_end_date' => now()->addYears(5),
                'created_by'            => $superAdmin->id,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Soft Admin Create
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'softadmin@alhuda.edu.bd'],
            [
                'institution_user_id' => '501',

                'name'       => 'Soft Admin',
                'name_bn'    => 'সফট অ্যাডমিন',
                'username'   => 'softadmin',
                'phone'      => '01700000001',
                'password'   => Hash::make('fahim'),

                'role_id'    => $softAdminRoleId,
                'madrasa_id' => $madrasa->id,

                'status'     => 1,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Madrasa Admin Create
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'admin@alhuda.edu.bd'],
            [
                'institution_user_id' => '401',

                'name'       => 'Madrasa Admin',
                'name_bn'    => 'মাদ্রাসা অ্যাডমিন',
                'username'   => 'madrasaadmin',
                'phone'      => '01700000002',
                'password'   => Hash::make('fahim'),

                'role_id'    => $madrasaAdminRoleId,
                'madrasa_id' => $madrasa->id,

                'status'     => 1,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Second Madrasa Create
        |--------------------------------------------------------------------------
        */

        $madrasa2 = Madrasa::updateOrCreate(
            ['madrasa_code' => 'DARUL001'],
            [
                'name'                  => 'দারুল উলুম ইসলামিয়া মাদ্রাসা',
                'name_bn'               => 'দারুল উলুম ইসলামিয়া মাদ্রাসা',
                'email'                 => 'info@darululum.edu.bd',
                'phone'                 => '01800000001',
                'address'               => 'মিরপুর, ঢাকা',
                'city'                  => 'ঢাকা',
                'state'                 => 'ঢাকা',
                'postal_code'           => '1216',
                'country'               => 'Bangladesh',
                'eiin_no'               => '789012',
                'established_year'      => 2015,
                'status'                => 1,
                'subscription_end_date' => now()->addYears(3),
                'created_by'            => $superAdmin->id,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Second Soft Admin
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            ['email' => 'softadmin@darululum.edu.bd'],
            [
                'institution_user_id' => '502',

                'name'       => 'Soft Admin - Darul Uloom',
                'name_bn'    => 'সফট অ্যাডমিন - দারুল উলুম',
                'username'   => 'softadmin2',
                'phone'      => '01800000001',
                'password'   => Hash::make('fahim'),

                'role_id'    => $softAdminRoleId,
                'madrasa_id' => $madrasa2->id,

                'status'     => 1,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Role Description
    |--------------------------------------------------------------------------
    */

    private function getRoleDescription($slug)
    {
        $descriptions = [

            'super-admin' =>
                'পুরো সিস্টেমের সর্বোচ্চ কর্তৃত্ব।',

            'soft-admin' =>
                'নির্দিষ্ট মাদ্রাসা পরিচালনা করতে পারে।',

            'madrasa-admin' =>
                'মাদ্রাসার দৈনন্দিন কার্যক্রম পরিচালনা করে।',

            'teacher' =>
                'শিক্ষাদান ও একাডেমিক কার্যক্রম পরিচালনা করে।',

            'student' =>
                'নিজের একাডেমিক তথ্য দেখতে পারে।',

            'guardian' =>
                'সন্তানের তথ্য দেখতে পারে।',
        ];

        return $descriptions[$slug] ?? 'Role Description';
    }
}