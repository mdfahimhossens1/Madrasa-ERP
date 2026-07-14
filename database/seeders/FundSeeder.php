<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fund;

class FundSeeder extends Seeder
{
    public function run(): void
    {
        Fund::firstOrCreate(

            [
                'name' => 'জেনারেল ফান্ড',
            ],

            [
                'institution_id' => 1,
                'user_id'    => 1,
                'balance'    => 0,
            ]
        );
    }
}