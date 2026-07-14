<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fund;
use App\Models\Ledger;

class LedgerSeeder extends Seeder
{
    public function run(): void
    {
        $generalFund = Fund::where(
            'name',
            'জেনারেল ফান্ড'
        )->first();

        if (!$generalFund) {
            return;
        }

        $ledgers = [

            [
                'name' => 'শিক্ষার্থীর মাধ্যম',
                'type' => 'income',
            ],

            [
                'name' => 'শিক্ষক বেতন',
                'type' => 'expense',
            ],

        ];

        foreach ($ledgers as $ledger) {

            Ledger::firstOrCreate(

                [
                    'name'    => $ledger['name'],
                    'fund_id' => $generalFund->id,
                ],

                [
                    'institution_id' => 1,
                    'user_id'    => 1,
                    'type'       => $ledger['type'],
                ]
            );
        }
    }
}