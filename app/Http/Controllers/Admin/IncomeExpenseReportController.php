<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class IncomeExpenseReportController extends Controller
{
    public function incomeExpense(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Start Query
        |--------------------------------------------------------------------------
        */

        $query = Transaction::with([
            'paymentMethod',
            'cashier',
            'items'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Filter Mode
        |--------------------------------------------------------------------------
        */

        if ($request->filter_mode == 'voucher') {

            /*
            |--------------------------------------------------------------------------
            | Voucher Filter
            |--------------------------------------------------------------------------
            */

            if ($request->filled('voucher_no')) {

                $query->where(
                    'voucher_no',
                    $request->voucher_no
                );
            }

        } else {

            /*
            |--------------------------------------------------------------------------
            | Date Filter
            |--------------------------------------------------------------------------
            */

            if ($request->filled('date_from')) {

                $query->whereDate(
                    'date',
                    '>=',
                    $request->date_from
                );
            }

            if ($request->filled('date_to')) {

                $query->whereDate(
                    'date',
                    '<=',
                    $request->date_to
                );
            }
        }

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

        /*
        |--------------------------------------------------------------------------
        | Get Reports
        |--------------------------------------------------------------------------
        */

        $reports = $query
            ->latest()
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $totalIncome = $reports
            ->where('type', 'income')
            ->sum('total_amount');

        $totalExpense = $reports
            ->where('type', 'expense')
            ->sum('total_amount');

        $balance = $totalIncome - $totalExpense;

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */
        $filters = $request->all();
        
        return view(
            'admin.income-expense-report.index',
            compact(
                'reports',
                'totalIncome',
                'totalExpense',
                'balance'
            )
        );
    }
}