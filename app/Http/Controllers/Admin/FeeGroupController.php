<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeGroup;
use App\Models\Fund;
use App\Models\Ledger;
use App\Models\SubLedger;
use Illuminate\Http\Request;

class FeeGroupController extends Controller
{
    public function index(Request $request)
    {
        $institutionId = auth()->user()->institution_id ?? 1;

        $query = FeeGroup::with(['fund', 'ledger', 'subLedger'])
            ->where('institution_id', $institutionId);

        // Filters
        if ($request->filled('fund_id')) {
            $query->where('fund_id', $request->fund_id);
        }

        if ($request->filled('general_ledger_id')) {
            $query->where('ledger_id', $request->general_ledger_id);
        }

        if ($request->filled('sub_ledger_id')) {
            $query->where('sub_ledger_id', $request->sub_ledger_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $feeGroups = $query->latest()->paginate(15);

        // Fund List
        $funds = Fund::where('institution_id', $institutionId)->get();

        // Ledger List
        $generalLedgers = Ledger::where('institution_id', $institutionId)
            ->when($request->fund_id, function ($q) use ($request) {
                $q->where('fund_id', $request->fund_id);
            })
            ->get();

        // Sub Ledger List
        $subLedgers = SubLedger::where('institution_id', $institutionId)
            ->when($request->general_ledger_id, function ($q) use ($request) {
                $q->where('ledger_id', $request->general_ledger_id);
            })
            ->get();

        return view('admin.fee-groups.index', compact(
            'feeGroups',
            'funds',
            'generalLedgers',
            'subLedgers'
        ));
    }

    public function store(Request $request)
{
    $request->validate([
        'fund_id'       => 'required|exists:funds,id',
        'ledger_id'     => 'required|exists:ledgers,id',
        'sub_ledger_id' => 'required|exists:sub_ledgers,id',
        'type'          => 'required|in:ekkalin,monthly,others',
    ]);

    $institutionId = auth()->user()->institution_id ?? 1;

    /*
    |--------------------------------------------------------------------------
    | Duplicate Check
    |--------------------------------------------------------------------------
    | একই fund + ledger + sub ledger + type
    | আবার save হতে পারবে না
    */

    $exists = FeeGroup::where('institution_id', $institutionId)
        ->where('fund_id', $request->fund_id)
        ->where('ledger_id', $request->ledger_id)
        ->where('sub_ledger_id', $request->sub_ledger_id)
        ->where('type', $request->type)
        ->exists();

    if ($exists) {

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'এই ফি গ্রুপ আগে থেকেই আছে');
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Number
    |--------------------------------------------------------------------------
    */

    $lastFee = FeeGroup::latest('id')->first();

    $number = $lastFee
        ? $lastFee->number + 1
        : 1001;

    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */

    FeeGroup::create([

        'institution_id'    => $institutionId,

        'fund_id'       => $request->fund_id,

        'ledger_id'     => $request->ledger_id,

        'sub_ledger_id' => $request->sub_ledger_id,

        'type'          => $request->type,

        'number'        => $number,

        'is_active'     => 1,
    ]);

    /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

    return redirect()
        ->route('dashboard.fees.index')
        ->with('success', 'সফলভাবে Save হয়েছে');
}

    public function edit(FeeGroup $fee)
    {
        $institutionId = auth()->user()->institution_id ?? 1;

        $funds = Fund::where('institution_id', $institutionId)->get();

        $generalLedgers = Ledger::where('institution_id', $institutionId)->get();

        $subLedgers = SubLedger::where('institution_id', $institutionId)->get();

        return view('admin.fee-groups.edit', compact(
            'fee',
            'funds',
            'generalLedgers',
            'subLedgers'
        ));
    }

    public function update(Request $request, FeeGroup $fee)
    {
        $request->validate([
            'fund_id'       => 'required|exists:funds,id',
            'ledger_id'     => 'required|exists:ledgers,id',
            'sub_ledger_id' => 'required|exists:sub_ledgers,id',
            'type'          => 'required|in:ekkalin,monthly,others',
        ]);

        $fee->update([
            'fund_id'       => $request->fund_id,
            'ledger_id'     => $request->ledger_id,
            'sub_ledger_id' => $request->sub_ledger_id,
            'type'          => $request->type,
        ]);

        return redirect()
            ->route('dashboard.fees.index')
            ->with('success', 'ফি আপডেট করা হয়েছে!');
    }

    public function destroy(FeeGroup $fee)
    {
        $fee->delete();

        return back()->with('success', 'ফি মুছে ফেলা হয়েছে!');
    }

    public function getLedgers($fund_id)
    {
        $institutionId = auth()->user()->institution_id ?? 1;

        $ledgers = Ledger::where('institution_id', $institutionId)
            ->where('fund_id', $fund_id)
            ->get();

        return response()->json($ledgers);
    }

    public function getSubLedgers($ledger_id)
    {
        $institutionId = auth()->user()->institution_id ?? 1;

        $subLedgers = SubLedger::where('institution_id', $institutionId)
            ->where('ledger_id', $ledger_id)
            ->get();

        return response()->json($subLedgers);
    }
}