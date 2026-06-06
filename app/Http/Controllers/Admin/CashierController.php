<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cashier;
use Illuminate\Http\Request;

class CashierController extends Controller
{
    public function index()
    {
        return response()->json(Cashier::all());
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Cashier store called', $request->all());
            
            $request->validate([
                'name' => 'required|string|max:255'
            ]);
            
            $cashier = Cashier::create([
                'name' => $request->name,
                'user_id' => auth()->id(),
                'status' => true
            ]);
            
            return response()->json([
                'success' => true,
                'id' => $cashier->id,
                'name' => $cashier->name
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Cashier store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy($id)
    {
        $cashier = Cashier::findOrFail($id);
        $cashier->delete();
        return response()->json(['success' => true]);
    }
}