<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubLedger;
use Illuminate\Support\Facades\Log;

class SubLedgerController extends Controller
{
    public function store(Request $request)
    {
        try {
            Log::info('SubLedger store called', ['request' => $request->all()]);
            
            $request->validate([
                'ledger_id' => 'required|exists:ledgers,id',
                'name' => 'required|string|max:255'
            ]);

            $madrashaId = auth()->user()->madrasa_id;

            Log::info('Creating sub ledger with:', [
                'ledger_id' => $request->ledger_id,
                'madrasha_id' => $madrashaId,
                'name' => $request->name
            ]);

            $subLedger = SubLedger::create([
                'ledger_id' => $request->ledger_id,
                'madrasa_id' => $madrashaId,
                'name' => $request->name
                // fee_type is nullable, so not required
            ]);

            Log::info('SubLedger created', ['id' => $subLedger->id]);

            return response()->json([
                'success' => true, 
                'id' => $subLedger->id, 
                'name' => $subLedger->name
            ]);
            
        } catch (\Exception $e) {
            Log::error('SubLedger store error: ' . $e->getMessage() . ' Line: ' . $e->getLine());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getByLedger($id)
    {
        try {
            $subLedgers = SubLedger::where('ledger_id', $id)
                ->where('madrasa_id', auth()->user()->madrasa_id ?? 1)
                ->get(['id', 'name']);
            return response()->json($subLedgers);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
}