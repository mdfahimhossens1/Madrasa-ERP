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
use Illuminate\Http\Request;

class StudentFeeCollectionController extends Controller
{

    public function index()
    {
        $years = AcademicYear::where('status', 'active')
            ->orderBy('id', 'desc')
            ->get();
        $cashiers = Cashier::latest()->get();
        
        return view('admin.fee-collection.index', compact('years', 'cashiers'));
    }

private function getStudentFeeAmount($student)
{
    $admission = $student->admissions()->latest()->first();

    if (!$admission) {
        return 0;
    }

    $feeSetting = FeeSetting::where('academic_year_id', $admission->academic_year_id)
        ->where(function ($q) use ($admission) {
            $q->where('class_id', $admission->class_id)
              ->orWhereNull('class_id');
        })
        ->first();

    if (!$feeSetting) {
        return 0;
    }

    $gender   = $student->user->gender;
    $resident = $admission->residence_status;
    $type     = $admission->admission_type;

    if ($gender === 'male') {

        if ($resident === 'resident') {
            return $type === 'new'
                ? $feeSetting->chattra_abashik_new
                : $feeSetting->chattra_abashik_old;
        }

        if ($resident === 'non_resident') {
            return $type === 'new'
                ? $feeSetting->chattra_onabashik_new
                : $feeSetting->chattra_onabashik_old;
        }

        if ($resident === 'day_care') {
            return $type === 'new'
                ? $feeSetting->chattra_dekeyr_new
                : $feeSetting->chattra_dekeyr_old;
        }

        if ($resident === 'night_care') {
            return $type === 'new'
                ? $feeSetting->chattra_nightcare_new
                : $feeSetting->chattra_nightcare_old;
        }

    } else {

        if ($resident === 'resident') {
            return $type === 'new'
                ? $feeSetting->chhatri_abashik_new
                : $feeSetting->chhatri_abashik_old;
        }

        if ($resident === 'non_resident') {
            return $type === 'new'
                ? $feeSetting->chhatri_onabashik_new
                : $feeSetting->chhatri_onabashik_old;
        }

        if ($resident === 'day_care') {
            return $type === 'new'
                ? $feeSetting->chhatri_dekeyr_new
                : $feeSetting->chhatri_dekeyr_old;
        }

        if ($resident === 'night_care') {
            return $type === 'new'
                ? $feeSetting->chhatri_nightcare_new
                : $feeSetting->chhatri_nightcare_old;
        }
    }

    return 0;
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

        $lastVoucher = StudentPayment::max('voucher_no');

        $nextVoucher = $lastVoucher ? $lastVoucher + 1 : 4001;
        $payments = StudentPayment::where('student_id', $student->id)->get();

$amount = $this->getStudentFeeAmount($student);

$months = [
    'Jan','Feb','Mar','Apr','May','Jun',
    'Jul','Aug','Sep','Oct','Nov','Dec'
];

$monthList = collect($months)->map(function ($m) use ($payments, $amount) {

    return [
        'name' => $m,
        'fee' => (float) $amount,
        'is_paid' => $payments->where('month', $m)->count() > 0
    ];
});

$total = $amount * 12;
$paid = $payments->sum('amount');
$discount = $payments->sum('discount');
$due = $total - ($paid + $discount);


        $admission = $student->admissions()->latest()->first();

        return response()->json([
            'success' => true,
            'voucher_no' => $nextVoucher,
            'academic_year_id' => $admission?->academic_year_id,
            'student' => [
                'id' => $user->institution_user_id,
                'name' => $user->name_bn,
                'father_name' => $user->father_name,
                'mobile' => $user->phone,
                'class_name' => optional($admission?->class)->name_bn ?? '',
                'photo' => $user->photo,
                'remarks' => $user->guardian_name ?? '',
            ],
            'fee' => [
                'total' => $total,
                'paid' => $paid,
                'discount' => $discount,
                'due' => $due,
            ],
            'monthList' => $monthList
        ]);
    }



    // =========================
    // SAVE PAYMENT
    // =========================

    public function savePayment(Request $req)
{


dd($req->all());
    try {

        $student = Student::with('user')
            ->whereHas('user', function ($q) use ($req) {
                $q->where('institution_user_id', $req->student_id);
            })
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ]);
        }

        $lastVoucher = StudentPayment::max('voucher_no');

        if ($lastVoucher) {
            $voucher = $lastVoucher + 1;
        } else {
            $voucher = 4001;
        }

        $cashierId = null;

        if (!empty($req->cashier_id)) {

            $cashier = Cashier::find($req->cashier_id);

            if ($cashier) {
                $cashierId = $cashier->id;
            }
        }

        $amount = $this->getStudentFeeAmount($student);

$months = $req->months ?? [];

if ($req->pay_type === 'monthly') {

    foreach ($months as $m) {

        StudentPayment::create([
            'madrasa_id'         => auth()->user()->madrasa_id ?? 1,
            'student_id'         => $student->id,
            'user_id'            => $student->user_id,
            'month'              => $m,
            'pay_type'           => 'monthly',
            'amount'             => $amount,
            'discount'           => $req->discount ?? 0,
            'method'             => $req->pay_method,
            'pay_method_label'   => $req->pay_method_label,
            'pay_account_number' => $req->pay_account_number,
            'cashier_id'         => $cashierId,
            'payment_date'       => $req->payment_date,
            'voucher_no'         => $voucher,
            'note'               => $req->note,
        ]);
    }

} else {

    StudentPayment::create([
        'madrasa_id'         => auth()->user()->madrasa_id ?? 1,
        'student_id'         => $student->id,
        'user_id'            => $student->user_id,
        'pay_type'           => 'admission',
        'amount'             => $amount,
        'discount'           => $req->discount ?? 0,
        'method'             => $req->pay_method,
        'pay_method_label'   => $req->pay_method_label,
        'pay_account_number' => $req->pay_account_number,
        'cashier_id'         => $cashierId,
        'payment_date'       => $req->payment_date,
        'voucher_no'         => $voucher,
        'note'               => $req->note,
    ]);
}

return response()->json([
    'success' => true,
    'voucher_no' => $voucher,
    'paidMonths' => $months,
    'fee' => [
        'total' => $amount * 12,
        'paid' => StudentPayment::where('student_id', $student->id)->sum('amount'),
        'discount' => StudentPayment::where('student_id', $student->id)->sum('discount'),
        'due' => ($amount * 12)
            - StudentPayment::where('student_id', $student->id)->sum('amount')
            - StudentPayment::where('student_id', $student->id)->sum('discount'),
    ]
]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ], 500);
    }
}
    // =========================
    // TODAY PAYMENTS
    // =========================
    public function todayPayments(Request $req)
    {
        $query = StudentPayment::with(['user','cashier']);

        if ($req->search) {

            $query->where(function($q) use ($req){

                $q->where('voucher_no', 'like', '%' . $req->search . '%')
                  ->orWhere('student_id', 'like', '%' . $req->search . '%');

            });
        }

        $payments = $query->latest()
            ->take(50)
            ->get();

        return response()->json([
            'success' => true,
            'todayTotal' => 0,
            'myTotal' => 0,

            'payments' => $payments->map(function ($p) {

                return [
                    'id' => $p->id,
                    'student_id' => optional($p->user)->institution_user_id,
                    'student_name' => $p->user->name_bn ?? '',
                    'month' => $p->month,
                    'amount' => 0,
                    'discount' => 0,
                    'method' => $p->method,
                    'cashier_name' => optional($p->cashier)->name ?? '',
                    'voucher_no' => $p->voucher_no,
                    'payment_date' => $p->payment_date,
                    'created_at' => $p->created_at,
                ];
            })
        ]);
    }

    // =========================
    // STATEMENT
    // =========================
    public function statement(Request $req)
    {
        $student = Student::with('user')
            ->whereHas('user', function ($q) use ($req) {
                $q->where('institution_user_id', $req->student_id);
            })
            ->first();

        if (!$student) {

            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ]);
        }

        $payments = StudentPayment::with(['cashier','user'])
            ->where('student_id', $student->id)
            ->get();

        return response()->json([
            'success' => true,
            'student_name' => optional($payments->first()?->user)->name_bn,
            'student_id'   => $req->student_id,

            'payments' => $payments->map(function ($p) {

                return [
                    'date' => date('d/m/Y', strtotime($p->payment_date)),
                    'time' => date('h:i A', strtotime($p->created_at)),
                    'month' => $p->month,
                    'amount' => 0,
                    'method' => $p->method,
                    'cashier' => optional($p->cashier)->name ?? '',
                    'voucher_no' => $p->voucher_no,
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
}