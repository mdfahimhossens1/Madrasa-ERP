<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubLedger;
use App\Models\Ledger;

class SubLedgerSeeder extends Seeder
{
    public function run(): void
    {
        $studentLedger = Ledger::where(
            'name',
            'শিক্ষার্থীর মাধ্যম'
        )->first();

        if (!$studentLedger) {
            return;
        }

        $subLedgers = [

            'বেতন',
            'ভর্তি ফি',
            'পরীক্ষার ফি',
            'আবাসন ফি',

        ];

        foreach ($subLedgers as $name) {

            SubLedger::firstOrCreate(

                [
                    'ledger_id' => $studentLedger->id,
                    'name'      => $name,
                ],

                [
                    'madrasa_id' => 1,
                ]
            );
        }
    }
}