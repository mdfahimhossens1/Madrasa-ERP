<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Fund;
use Illuminate\Support\Facades\Log;

class FundController extends Controller
{
    public function store(Request $request)
    {
        try {
            Log::info('Fund store called', ['request' => $request->all()]);
            
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $madrasaId = auth()->user()->madrasa_id ?? 1;
            $userId = auth()->id();

            Log::info('Creating fund with:', [
                'madrasa_id' => $madrasaId,
                'user_id' => $userId,
                'name' => $request->name
            ]);

            $fund = Fund::create([
                'madrasa_id' => $madrasaId,
                'user_id' => $userId,
                'name' => $request->name,
                'balance' => 0
            ]);

            Log::info('Fund created', ['id' => $fund->id]);

            return response()->json([
                'success' => true, 
                'id' => $fund->id, 
                'name' => $fund->name
            ]);
            
        } catch (\Exception $e) {
            Log::error('Fund store error: ' . $e->getMessage() . ' Line: ' . $e->getLine());
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    }
}