<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\User;
use App\Models\Cashier;
use App\Models\StudentPayment;
use App\Models\FeeSetting;
use App\Models\Admission;
use App\Models\Fund;
use App\Models\Ledger;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;

class StudentFeeCollectionController extends Controller
{

public function index()
{
    $madrasaId = auth()->user()->madrasa_id ?? 1;

    $years    = AcademicYear::where('status', 'active')->orderBy('id', 'desc')->get();
    $cashiers = Cashier::latest()->get();

    $todayTotal = \App\Models\FeeCollection::where('madrasa_id', $madrasaId)
        ->whereDate('collection_date', today())
        ->sum('paid_amount');

    $myTotal = \App\Models\FeeCollection::where('madrasa_id', $madrasaId)
        ->whereDate('collection_date', today())
        ->where('collected_by', auth()->id())
        ->sum('paid_amount');

    $payments = \App\Models\FeeCollection::with(['student.user', 'collector'])
        ->where('madrasa_id', $madrasaId)
        ->whereDate('collection_date', today())
        ->latest()
        ->get()
        ->map(fn($p) => (object)[
            'id'           => $p->id,
            'payment_date' => $p->collection_date,
            'created_at'   => $p->created_at,
            'student_id'   => optional($p->student?->user)->institution_user_id ?? '—',
            'student_name' => optional($p->student?->user)->name_bn ?? '—',
            'month'        => $p->month ?? '—',
            'amount'       => $p->paid_amount,
            'discount'     => $p->discount,
            'method'       => $p->payment_method_id ? 'Mobile' : 'Cash',
            'cashier_name' => optional($p->collector)->name ?? '—',
            'voucher_no'   => $p->receipt_no,
        ]);

    return view('admin.fee-collection.index', compact(
        'years', 'cashiers', 'todayTotal', 'myTotal', 'payments'
    ));
}

private function getStudentFeeAmount($student, $payType = 'monthly')
{
    $admission = $student->admissions()->latest()->first();

    if (!$admission) {
        return ['total' => 0, 'items' => []];
    }

    $madrasaId = auth()->user()->madrasa_id ?? 1;

    // ✅ FeeGroup type দিয়ে filter করো — এটাই মূল fix
    // monthly → type = 'monthly'
    // admission → type = 'ekkalin'
    $feeGroupType = $payType === 'monthly' ? 'monthly' : 'ekkalin';

    // ✅ FeeSetting গুলো নিয়ে আসো FeeGroup এর সাথে join করে
    // শুধু সেই FeeGroup গুলো যার type match করে
    $feeSettings = FeeSetting::with(['feeGroup.subLedger'])
        ->where('madrasa_id', $madrasaId)
        ->where('academic_year_id', $admission->academic_year_id)
        ->where('class_id', $admission->class_id)
        ->whereHas('feeGroup', function ($q) use ($feeGroupType) {
            $q->where('type', $feeGroupType)
              ->where('is_active', 1);
        })
        ->get();

    // class specific না পেলে global try করো
    if ($feeSettings->isEmpty()) {
        $feeSettings = FeeSetting::with(['feeGroup.subLedger'])
            ->where('madrasa_id', $madrasaId)
            ->where('academic_year_id', $admission->academic_year_id)
            ->whereNull('class_id')
            ->whereHas('feeGroup', function ($q) use ($feeGroupType) {
                $q->where('type', $feeGroupType)
                  ->where('is_active', 1);
            })
            ->get();
    }

    if ($feeSettings->isEmpty()) {
        return ['total' => 0, 'items' => []];
    }

    $gender   = $student->user->gender;
    $resident = $admission->residence_status; // resident/non-resident/daycare/nightcare
    $type     = $admission->admission_type;   // new/old

    $total = 0;
    $items = [];

    foreach ($feeSettings as $feeSetting) {

        $amount = $this->extractAmount($feeSetting, $gender, $resident, $type);

        if ($amount <= 0) continue;

        $items[] = [
            'fee_setting_id' => $feeSetting->id,
            'fee_group_id'   => $feeSetting->feeGroup?->id,
            'fee_name'       => $feeSetting->feeGroup?->subLedger?->name
                                ?? $feeSetting->feeGroup?->name
                                ?? 'ফি',
            'fee_type'       => $feeSetting->feeGroup?->type,
            'amount'         => $amount,
        ];

        $total += $amount;
    }

    return ['total' => $total, 'items' => $items];
}

private function getFeeFundAndLedger(): array
{
    $madrasaId = auth()->user()->madrasa_id ?? 1;

    // ফান্ড — না থাকলে প্রথমটা নাও, একেবারেই না থাকলে তৈরি করো
    $fund = Fund::where('madrasa_id', $madrasaId)->first();

    if (!$fund) {
        $fund = Fund::create([
            'madrasa_id' => $madrasaId,
            'name'       => 'সাধারণ ফান্ড',
        ]);
    }

    // লেজার — fee income এর জন্য 'ছাত্র বেতন' নামে খুঁজবে, না থাকলে তৈরি করবে
    $ledger = Ledger::where('madrasa_id', $madrasaId)
        ->where('fund_id', $fund->id)
        ->where('name', 'ছাত্র বেতন')
        ->first();

    if (!$ledger) {
        $ledger = Ledger::create([
            'madrasa_id' => $madrasaId,
            'user_id'    => auth()->id(),
            'name'       => 'ছাত্র বেতন',
            'type'       => 'income',
            'fund_id'    => $fund->id,
        ]);
    }

    return ['fund' => $fund, 'ledger' => $ledger];
}

private function extractAmount($feeSetting, $gender, $resident, $type)
{
    $amount = 0;

    if ($gender === 'male') {
        if ($resident === 'resident') {
            $amount = $type === 'new'
                ? $feeSetting->chattra_abashik_new
                : $feeSetting->chattra_abashik_old;

        } elseif ($resident === 'non-resident') {
            $amount = $type === 'new'
                ? $feeSetting->chattra_onabashik_new
                : $feeSetting->chattra_onabashik_old;

        } elseif ($resident === 'daycare') {
            $amount = $type === 'new'
                ? $feeSetting->chattra_dekeyr_new
                : $feeSetting->chattra_dekeyr_old;

        } elseif ($resident === 'nightcare') {
            $amount = $type === 'new'
                ? $feeSetting->chattra_nightcare_new
                : $feeSetting->chattra_nightcare_old;
        }

    } else {

        if ($resident === 'resident') {
            $amount = $type === 'new'
                ? $feeSetting->chhatri_abashik_new
                : $feeSetting->chhatri_abashik_old;

        } elseif ($resident === 'non-resident') {
            $amount = $type === 'new'
                ? $feeSetting->chhatri_onabashik_new
                : $feeSetting->chhatri_onabashik_old;

        } elseif ($resident === 'daycare') {
            $amount = $type === 'new'
                ? $feeSetting->chhatri_dekeyr_new
                : $feeSetting->chhatri_dekeyr_old;

        } elseif ($resident === 'nightcare') {
            $amount = $type === 'new'
                ? $feeSetting->chhatri_nightcare_new
                : $feeSetting->chhatri_nightcare_old;
        }
    }

    return (float)($amount ?? 0);
}
    // =========================
    // STUDENT INFO
    // =========================
public function studentInfo(Request $req)
{
    $student = Student::with([
            'user',
            'admissions.class'
        ])
        ->whereHas('user', function ($q) use ($req) {
            $q->where('institution_user_id', $req->id);
        })
        ->first();

    if (!$student) {
        return response()->json([
            'success' => false,
            'message' => 'শিক্ষার্থী পাওয়া যায়নি'
        ]);
    }

    $user = $student->user;
    $admission = $student->admissions()->latest()->first();

    // ✅ আগে $amount define করো
    $monthlyFees   = $this->getStudentFeeAmount($student, 'monthly');
    $admissionFees = $this->getStudentFeeAmount($student, 'admission');

    // ✅ তারপর voucher
    $lastVoucher = \App\Models\FeeCollection::max('receipt_no');
    $nextVoucher = $lastVoucher ? $lastVoucher + 1 : 4001;

    // ✅ তারপর payments ও monthList
    $payments = \App\Models\FeeCollection::where('student_id', $student->id)->get();

    $months = [
        'Jan','Feb','Mar','Apr','May','Jun',
        'Jul','Aug','Sep','Oct','Nov','Dec'
    ];

    $monthList = collect($months)->map(function ($m) use ($payments, $monthlyFees) {
        return [
            'name'      => $m,
            'fee' => (float) $monthlyFees['total'],
            'is_paid'   => $payments
                            ->where('month', $m)
                            ->where('pay_type', 'monthly')
                            ->where('due_amount', 0)
                            ->count() > 0,
            'prev_paid' => $payments->where('month', $m)->sum('paid_amount'),
        ];
    });

    $total = ($monthlyFees['total'] * 12)
       + $admissionFees['total'];
    $paid     = $payments->sum('paid_amount');
    $discount = $payments->sum('discount');
    $due      = $total - ($paid + $discount);

return response()->json([
    'success'          => true,
    'voucher_no'       => $nextVoucher,
    'academic_year_id' => $admission?->academic_year_id,
    'student' => [
        'id'          => $user->institution_user_id,
        'name'        => $user->name_bn,
        'father_name' => $user->father_name,
        'mobile'      => $user->phone,
        'class_name'  => optional($admission?->class)->name_bn ?? '',
        'photo'       => $user->photo,
        'remarks'     => $user->guardian_name ?? '',
    ],
    'fee' => [
        'total'    => $total,
        'paid'     => $paid,
        'discount' => $discount,
        'due'      => $due,
    ],
    // ✅ JS এ data.admission_fees check করে তাই key একই রাখো
    'admission_fees' => collect($admissionFees['items'])->map(fn($i) => [
        'name' => $i['fee_name'],
        'fee'  => $i['amount'],
    ])->values()->all(),
    'monthList' => $monthList,
    '_debug' => [
    'gender'    => $student->user->gender,
    'resident'  => $admission?->residence_status,
    'adm_type'  => $admission?->admission_type,
    'year_id'   => $admission?->academic_year_id,
    'class_id'  => $admission?->class_id,
    'monthly_total'   => $monthlyFees['total'],
    'admission_total' => $admissionFees['total'],
    'fee_count' => FeeSetting::where('academic_year_id', $admission?->academic_year_id)
                    ->where('class_id', $admission?->class_id)
                    ->count(),
],
]);
}



    // =========================
    // SAVE PAYMENT
    // =========================

public function savePayment(Request $req)
{
    try {
        $student = Student::with('user')
            ->whereHas('user', function ($q) use ($req) {
                $q->where('institution_user_id', $req->student_id);
            })
            ->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found']);
        }

        $lastReceipt = \App\Models\FeeCollection::max('receipt_no');
        $receiptNo   = $lastReceipt ? $lastReceipt + 1 : 4001;

        $cashierId = null;
        if (!empty($req->cashier_id)) {
            $cashier = Cashier::find($req->cashier_id);
            if ($cashier) $cashierId = $cashier->id;
        }

        $pmId = $req->payment_method_id ?? null;

if ($req->pay_type === 'monthly') {

            $months     = $req->months ?? [];
            $paidMonths = [];

            foreach ($months as $m) {
                $deposit  = (float)($m['deposit']  ?? 0);
                $discount = (float)($m['discount'] ?? 0);
                $fee      = (float)($m['fee']      ?? $deposit);
                $due      = max(0, $fee - $discount - $deposit);

                $feeCollection = \App\Models\FeeCollection::create([
                    'madrasa_id'        => auth()->user()->madrasa_id ?? 1,
                    'student_id'        => $student->id,
                    'receipt_no'        => $receiptNo,
                    'collection_date'   => $req->collection_date,
                    'total_amount'      => $fee,
                    'discount'          => $discount,
                    'paid_amount'       => $deposit,
                    'due_amount'        => $due,
                    'payment_method_id' => $pmId,
                    'month'             => $m['name'],
                    'pay_type'          => 'monthly',
                    'status'            => $due > 0 ? 'partial' : 'paid',
                    'note'              => $req->note,
                    'collected_by'      => $cashierId ?? auth()->id(),
                ]);

                if ($deposit > 0) {
                    $description = sprintf(
                        'ছাত্র: %s (ID: %s) — %s মাসের বেতন%s',
                        $student->user->name_bn ?? $student->user->name ?? '',
                        $student->user->institution_user_id ?? '',
                        $m['name'],
                        $discount > 0 ? " — কর্তন: ৳{$discount}" : ''
                    );
                    $this->createFeeTransaction($feeCollection, $req, $description);
                }

                $paidMonths[] = $m['name'];
                $receiptNo++;
            }

        } else {

            $admItems      = $req->admission_items ?? [];
            $totalDeposit  = collect($admItems)->sum('deposit');
            $totalDiscount = collect($admItems)->sum('discount');
            $totalFee      = collect($admItems)->sum('fee');
            $due           = max(0, $totalFee - $totalDiscount - $totalDeposit);

            $feeCollection = \App\Models\FeeCollection::create([
                'madrasa_id'        => auth()->user()->madrasa_id ?? 1,
                'student_id'        => $student->id,
                'receipt_no'        => $receiptNo,
                'collection_date'   => $req->collection_date,
                'total_amount'      => $totalFee,
                'discount'          => $totalDiscount,
                'paid_amount'       => $totalDeposit,
                'due_amount'        => $due,
                'payment_method_id' => $pmId,
                'month'             => null,
                'pay_type'          => 'admission',
                'status'            => $due > 0 ? 'partial' : 'paid',
                'note'              => $req->note,
                'collected_by'      => $cashierId ?? auth()->id(),
            ]);

            if ($totalDeposit > 0) {
                $description = sprintf(
                    'ছাত্র: %s (ID: %s) — ভর্তি ফি%s',
                    $student->user->name_bn ?? $student->user->name ?? '',
                    $student->user->institution_user_id ?? '',
                    $totalDiscount > 0 ? " — কর্তন: ৳{$totalDiscount}" : ''
                );
                $this->createFeeTransaction($feeCollection, $req, $description);
            }

            $paidMonths = [];
        }

        // ✅ updated fee summary — getStudentFeeAmount এখন array তাই সঠিকভাবে নাও
        $allPayments    = \App\Models\FeeCollection::where('student_id', $student->id)->get();
        $monthlyFees    = $this->getStudentFeeAmount($student, 'monthly');
        $admissionFees  = $this->getStudentFeeAmount($student, 'admission');
        $totalFeeYear   = ($monthlyFees['total'] * 12) + $admissionFees['total'];
        $totalPaid      = $allPayments->sum('paid_amount');
        $totalDiscount  = $allPayments->sum('discount');

        return response()->json([
            'success'    => true,
            'voucher_no' => \App\Models\FeeCollection::max('receipt_no'),
            'paidMonths' => $paidMonths,
            'fee' => [
                'total'    => $totalFeeYear,
                'paid'     => $totalPaid,
                'discount' => $totalDiscount,
                'due'      => max(0, $totalFeeYear - $totalPaid - $totalDiscount),
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'line'    => $e->getLine(),
            'file'    => $e->getFile(),
        ], 500);
    }
}
    // =========================
    // TODAY PAYMENTS
    // =========================

public function todayPayments(Request $req)
{
    $madrasaId = auth()->user()->madrasa_id ?? 1;

    $query = \App\Models\FeeCollection::with(['student.user', 'collector'])
        ->where('madrasa_id', $madrasaId);

    if ($req->search) {
        $query->where(function ($q) use ($req) {
            $q->where('receipt_no', 'like', '%' . $req->search . '%')
              ->orWhereHas('student.user', function ($q2) use ($req) {
                  $q2->where('institution_user_id', 'like', '%' . $req->search . '%');
              });
        });
    }

    $payments = $query->latest()->take(50)->get();

    $todayTotal = \App\Models\FeeCollection::where('madrasa_id', $madrasaId)
        ->whereDate('collection_date', today())
        ->sum('paid_amount');

    $myTotal = \App\Models\FeeCollection::where('madrasa_id', $madrasaId)
        ->whereDate('collection_date', today())
        ->where('collected_by', auth()->id())
        ->sum('paid_amount');

    return response()->json([
        'success'    => true,
        'todayTotal' => $todayTotal,
        'myTotal'    => $myTotal,
        'payments'   => $payments->map(function ($p) {
            return [
                'id'           => $p->id,
                'student_id'   => optional($p->student?->user)->institution_user_id ?? '—',
                'student_name' => optional($p->student?->user)->name_bn ?? '—',
                'month'        => $p->month ?? '—',
                'amount'       => $p->paid_amount,
                'discount'     => $p->discount,
                'method'       => $p->payment_method_id ? 'Mobile' : 'Cash',
                'cashier_name' => optional($p->collector)->name ?? '—',
                'voucher_no'   => $p->receipt_no,
                'payment_date' => $p->collection_date,
                'created_at'   => $p->created_at,
            ];
        })
    ]);
}

public function statement(Request $req)
{
    $student = Student::with('user')
        ->whereHas('user', function ($q) use ($req) {
            $q->where('institution_user_id', $req->student_id);
        })
        ->first();

    if (!$student) {
        return response()->json(['success' => false, 'message' => 'Student not found']);
    }

    $payments = \App\Models\FeeCollection::with(['collector'])
        ->where('student_id', $student->id)
        ->latest()
        ->get();

    return response()->json([
        'success'      => true,
        'student_name' => $student->user->name_bn ?? '',
        'student_id'   => $req->student_id,
        'payments'     => $payments->map(function ($p) {
            return [
                'date'       => \Carbon\Carbon::parse($p->collection_date)->format('d/m/Y'),
                'time'       => \Carbon\Carbon::parse($p->created_at)->format('h:i A'),
                'month'      => $p->month ?? '—',
                'amount'     => $p->paid_amount,
                'method'     => $p->payment_method_id ? 'Mobile/Bank' : 'Cash',
                'cashier'    => optional($p->collector)->name ?? '—',
                'voucher_no' => $p->receipt_no,
            ];
        })
    ]);
}

public function addCashier(Request $request)
{
    try {

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $cashier = Cashier::create([
            'name' => $request->name,
            'madrasa_id' => auth()->user()->madrasa_id
        ]);

        return response()->json([
            'success' => true,
            'cashier' => [
                'id'   => $cashier->id,
                'name' => $cashier->name,
            ]
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    public function paymentMethods()
    {
        $userId = auth()->id();
        return response()->json([
            'success' => true,
            'mobile'  => \App\Models\PaymentMethod::where('user_id', $userId)
                            ->where('account_type', 'mobile')->get(),
            'cash'    => \App\Models\PaymentMethod::where('user_id', $userId)
                            ->where('account_type', 'cash')->get(),
            'bank'    => \App\Models\PaymentMethod::where('user_id', $userId)
                            ->where('account_type', 'bank')->get(),
        ]);
    }

    public function printReceipt($id)
    {
        $payment = \App\Models\FeeCollection::with(['student.user', 'collector'])->findOrFail($id);
        return view('admin.fee-collection.receipt', compact('payment'));
    }

    private function createFeeTransaction($feeCollection, Request $req, string $description): void
{
    [$fund, $ledger] = array_values($this->getFeeFundAndLedger());

    $cashierId = $feeCollection->collected_by;

    $transaction = Transaction::create([
        'voucher_no'        => $feeCollection->receipt_no,
        'type'              => 'income',
        'fund_id'           => $fund->id,
        'payment_method_id' => $feeCollection->payment_method_id,
        'cashier_id'        => $cashierId,
        'total_amount'      => $feeCollection->paid_amount,
        'date'              => $feeCollection->collection_date,
        'note'              => $req->note,
    ]);

    TransactionItem::create([
        'transaction_id' => $transaction->id,
        'ledger_id'      => $ledger->id,
        'sub_ledger_id'  => null,
        'amount'         => $feeCollection->paid_amount,
        'description'    => $description,
    ]);
}
}