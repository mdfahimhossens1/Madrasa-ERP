<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        return response()->json(PaymentMethod::all());
    }

    public function store(Request $request)
{
    try {

        \Log::info('PaymentMethod store called', $request->all());

        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'account_type' => 'required|string',
            'account_number' => 'nullable|string|max:255',
            'mobile1' => 'nullable|string|max:20',
            'mobile2' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        $paymentMethod = PaymentMethod::create([

            'name' => $request->name,
            'icon' => $request->icon ?? 'fa-credit-card',
            'user_id' => auth()->id(),
            'account_type' => $request->account_type,
            'account_number' => $request->account_number,
            'mobile1' => $request->mobile1,
            'mobile2' => $request->mobile2,
            'address' => $request->address,
        ]);

        return response()->json([
            'success' => true,
            'id' => $paymentMethod->id,
            'name' => $paymentMethod->name,
            'icon' => $paymentMethod->icon,
        ]);

    } catch (\Exception $e) {

        \Log::error('PaymentMethod store error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    
    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->delete();
        return response()->json(['success' => true]);
    }
}