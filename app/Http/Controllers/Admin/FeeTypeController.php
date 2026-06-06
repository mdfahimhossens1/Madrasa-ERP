<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:fee_types,name'
        ]);

        try {
            $feeType = FeeType::create([
                'name' => $request->name,
                'is_active' => 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'ফি টাইপ সফলভাবে যোগ করা হয়েছে!',
                'data' => $feeType
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'যোগ করা ব্যর্থ হয়েছে: ' . $e->getMessage()
            ], 500);
        }
    }

    public function fundLedgers($id)
{
    return Ledger::where('fund_id', $id)->get();
}
}