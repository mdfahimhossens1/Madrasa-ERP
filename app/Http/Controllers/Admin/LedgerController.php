<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ledger;

class LedgerController extends Controller
{
    public function store(Request $request)
    {
        try {

            $request->validate([
                'name'    => 'required|string|max:255',
                'type'    => 'required|in:income,expense,both',
                'fund_id' => 'required|exists:funds,id'
            ]);

            $ledger = Ledger::create([
                'madrasa_id' => auth()->user()->madrasa_id,
                'user_id'     => auth()->id(),
                'name'        => $request->name,
                'type'        => $request->type,
                'fund_id'     => $request->fund_id,
            ]);

            return response()->json([
                'success' => true,
                'id'      => $ledger->id,
                'name'    => $ledger->name,
                'message' => 'লেজার যোগ করা হয়েছে'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'লেজার যোগ করতে ব্যর্থ: ' . $e->getMessage()
            ], 500);
        }
    }

public function fundLedgers(Request $request, $id)
{
    $type = $request->type;

    return Ledger::where('fund_id', $id)

        ->where(function ($q) use ($type) {

            $q->where('type', $type)
              ->orWhere('type', 'both');

        })

        ->orderBy('name')

        ->get();
}
}