<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use App\Models\Cashier;
use App\Models\Fund;

class IncomeExpenseReportController extends Controller
{
    public function incomeExpense(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Dropdown Data
        |--------------------------------------------------------------------------
        */
        $paymentMethods = PaymentMethod::orderBy('name')->get();
        $cashiers       = Cashier::orderBy('name')->get();
        $funds          = Fund::orderBy('name')->get();

        /*
        |--------------------------------------------------------------------------
        | Filter Values
        |--------------------------------------------------------------------------
        */
        $filters = $request->all();

        /*
        |--------------------------------------------------------------------------
        | Only run the query if the button was actually clicked
        | (i.e. report_type is present and not empty)
        |--------------------------------------------------------------------------
        */
        if (!$request->filled('report_type')) {
            return view('admin.income-expense-report.index', compact(
                'filters', 'paymentMethods', 'cashiers', 'funds'
            ));
        }

        /*
        |--------------------------------------------------------------------------
        | Start Query
        |--------------------------------------------------------------------------
        */
        $query = Transaction::with([
            'fund',
            'paymentMethod',
            'cashier',
            'items.ledger',
            'items.subLedger',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Report Type
        |--------------------------------------------------------------------------
        */
        if ($request->report_type == 'income') {
            $query->where('type', 'income');
        } elseif ($request->report_type == 'expense') {
            $query->where('type', 'expense');
        }
        // income_expense → no type filter

        /*
        |--------------------------------------------------------------------------
        | Filter Mode
        |--------------------------------------------------------------------------
        */
        if ($request->filter_mode == 'voucher') {
            if ($request->filled('voucher_no')) {
                $query->where('voucher_no', $request->voucher_no);
            }
        } else {
            if ($request->filled('date_from')) {
                $query->whereDate('date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('date', '<=', $request->date_to);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Extra Filters
        |--------------------------------------------------------------------------
        */
        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }
        if ($request->filled('cashier_id')) {
            $query->where('cashier_id', $request->cashier_id);
        }
        if ($request->filled('fund_id')) {
            $query->where('fund_id', $request->fund_id);
        }

        /*
        |--------------------------------------------------------------------------
        | Sorting
        |--------------------------------------------------------------------------
        */
        switch ($request->sort_by) {
            case 'oldest':
                $query->orderBy('date', 'asc');
                break;
            case 'highest':
                $query->orderBy('total_amount', 'desc');
                break;
            case 'lowest':
                $query->orderBy('total_amount', 'asc');
                break;
            default:
                $query->orderBy('date', 'desc')->orderBy('id', 'desc');
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Get Results
        |--------------------------------------------------------------------------
        */
        $reports = $query->get();

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */
        $totalIncome  = $reports->where('type', 'income')->sum('total_amount');
        $totalExpense = $reports->where('type', 'expense')->sum('total_amount');
        $balance      = $totalIncome - $totalExpense;
        $totalVoucher = $reports->count();

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */
        return view('admin.income-expense-report.index', compact(
            'reports',
            'filters',
            'paymentMethods',
            'cashiers',
            'funds',
            'totalIncome',
            'totalExpense',
            'balance',
            'totalVoucher'
        ));
    }
}