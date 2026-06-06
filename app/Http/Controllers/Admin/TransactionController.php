<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Ledger, SubLedger, Fund, PaymentMethod, Cashier, Transaction, TransactionItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'expense');
        $madrashaId = auth()->user()->madrasa_id ?? 1;
        
        // Use the correct method name
        $voucherNo = $this->generateUniqueVoucherNumber($type);
        
        $ledgers = Ledger::where('madrasa_id', $madrashaId)
            ->where(function($q) use ($type) {
                $q->where('type', $type)->orWhere('type', 'both');
            })
            ->get();
            
        $funds = Fund::where('madrasa_id', $madrashaId)->get();
        $paymentMethods = PaymentMethod::all();
        $cashiers = Cashier::all();
        
        $transactions = Transaction::with(['items.ledger', 'items.subLedger', 'fund', 'paymentMethod', 'cashier'])
            ->where('type', $type)
            ->latest()
            ->get();
        
        $balance = $this->getBalance();
        
        return view('admin.transactions.index', compact(
            'type', 'voucherNo', 'ledgers', 'funds', 
            'paymentMethods', 'cashiers', 'transactions', 'balance'
        ));
    }

    public function store(Request $request)
    {
        try {
            Log::info('Transaction store started', ['request_type' => $request->type, 'all_data' => $request->all()]);
            
            DB::beginTransaction();
            
            $validated = $request->validate([
                'type' => 'required|in:income,expense',
                'date' => 'required|date',
                'fund_id' => 'required|exists:funds,id',
                'payment_method_id' => 'required|exists:payment_methods,id',
                'cashier_id' => 'required|exists:cashiers,id',
                'note' => 'nullable|string',
                'items' => 'required|json'
            ]);
            
            Log::info('Validation passed', ['type' => $request->type]);
            
            $items = json_decode($request->items, true);
            if (empty($items)) {
                throw new \Exception('কোন আইটেম নেই');
            }
            
            Log::info('Items decoded', ['items_count' => count($items), 'items' => $items]);
            
            // Generate unique voucher number
            $voucherNo = $this->generateUniqueVoucherNumber($request->type);
            $totalAmount = array_sum(array_column($items, 'amount'));
            
            Log::info('Creating transaction', [
                'voucher_no' => $voucherNo,
                'type' => $request->type,
                'fund_id' => $request->fund_id,
                'payment_method_id' => $request->payment_method_id,
                'cashier_id' => $request->cashier_id,
                'total_amount' => $totalAmount,
                'date' => $request->date
            ]);
            
            $transaction = Transaction::create([
                'voucher_no' => $voucherNo,
                'type' => $request->type,
                'fund_id' => $request->fund_id,
                'payment_method_id' => $request->payment_method_id,
                'cashier_id' => $request->cashier_id,
                'total_amount' => $totalAmount,
                'date' => $request->date,
                'note' => $request->note
            ]);
            
            Log::info('Transaction created', ['transaction_id' => $transaction->id]);
            
            foreach ($items as $index => $item) {
                Log::info('Creating transaction item', [
                    'index' => $index,
                    'transaction_id' => $transaction->id,
                    'ledger_id' => $item['ledger_id'],
                    'sub_ledger_id' => $item['sub_ledger_id'] ?? null,
                    'amount' => $item['amount'],
                    'description' => $item['description'] ?? null
                ]);
                
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'ledger_id' => $item['ledger_id'],
                    'sub_ledger_id' => $item['sub_ledger_id'] ?? null,
                    'amount' => $item['amount'],
                    'description' => $item['description'] ?? null
                ]);
            }

            DB::commit();
            
            Log::info('Transaction committed successfully', ['type' => $request->type, 'voucher_no' => $voucherNo]);
            
            return redirect()->route('dashboard.transactions.index', ['type' => $request->type])
                ->with('success', "লেনদেন সফলভাবে সংরক্ষণ করা হয়েছে (ভাউচার: {$voucherNo})");
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error: ' . json_encode($e->errors()));
            return back()->with('error', 'ভ্যালিডেশন Error: ' . json_encode($e->errors()))->withInput();
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction store error: ' . $e->getMessage() . ' Line: ' . $e->getLine());
            
            return back()->with('error', 'লেনদেন সংরক্ষণ করতে ব্যর্থ: ' . $e->getMessage())
                         ->withInput();
        }
    }

    /**
     * Generate unique voucher number
     */
private function generateUniqueVoucherNumber($type)
{
    $lastTransaction = Transaction::where('type', $type)
        ->latest('id')
        ->first();

    if ($lastTransaction) {

        $lastNumber = (int) $lastTransaction->voucher_no;

        $newNumber = $lastNumber + 1;

    } else {

        $newNumber = 1;
    }

    return str_pad($newNumber, 2, '0', STR_PAD_LEFT);
}
    
    public function destroy($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);
            $type = $transaction->type;
            
            // Delete transaction items first
            $transaction->items()->delete();
            $transaction->delete();
            
            return response()->json(['success' => true, 'type' => $type]);
        } catch (\Exception $e) {
            Log::error('Delete error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function getBalance(): array
    {
        $balance = [];
        $paymentMethods = PaymentMethod::all();
        
        foreach ($paymentMethods as $pm) {
            $incomeTotal = Transaction::where('payment_method_id', $pm->id)
                ->where('type', 'income')
                ->sum('total_amount');
            
            $expenseTotal = Transaction::where('payment_method_id', $pm->id)
                ->where('type', 'expense')
                ->sum('total_amount');
            
            $netBalance = $incomeTotal - $expenseTotal;
            
            $balance[$pm->id] = [
                'name' => $pm->name,
                'icon' => $pm->icon ?? 'fa-credit-card',
                'total' => $netBalance,
                'total_display' => abs($netBalance),
                'is_negative' => $netBalance < 0,
                'income' => $incomeTotal,
                'expense' => $expenseTotal,
                'members' => []
            ];
            
            $cashiers = Cashier::all();
            foreach ($cashiers as $cashier) {
                $cashierIncome = Transaction::where('payment_method_id', $pm->id)
                    ->where('cashier_id', $cashier->id)
                    ->where('type', 'income')
                    ->sum('total_amount');
                    
                $cashierExpense = Transaction::where('payment_method_id', $pm->id)
                    ->where('cashier_id', $cashier->id)
                    ->where('type', 'expense')
                    ->sum('total_amount');
                    
                $cashierNet = $cashierIncome - $cashierExpense;
                
                if ($cashierNet != 0) {
                    $balance[$pm->id]['members'][] = [
                        'name' => $cashier->name,
                        'amount' => abs($cashierNet),
                        'is_negative' => $cashierNet < 0
                    ];
                }
            }
        }
        
        return $balance;
    }

}