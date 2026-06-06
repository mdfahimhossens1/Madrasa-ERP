<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeCollection;
use App\Models\PaymentMethod;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Http\Request;

class FeeCollectionController extends Controller
{
    public function index(Request $request)
    {
        $madrasaId = auth()->user()->madrasa_id ?? 1;

        $collections = FeeCollection::with(['student.user', 'collector', 'paymentMethod'])
            ->where('madrasa_id', $madrasaId)
            ->when($request->student_id, fn($q) => $q->where('student_id', $request->student_id))
            ->when($request->status,     fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        $paymentMethods = PaymentMethod::where('status', 1)->orderBy('type')->get();
        $years          = AcademicYear::where('status', 1)->get();
        $cashiers       = User::where('role_id', 4)->get();

        $todayTotal = FeeCollection::where('madrasa_id', $madrasaId)
            ->whereDate('created_at', today())->sum('paid_amount');

        $myTotal = FeeCollection::where('madrasa_id', $madrasaId)
            ->whereDate('created_at', today())
            ->where('collected_by', auth()->id())
            ->sum('paid_amount');

        $payments = FeeCollection::with(['student.user', 'collector'])
            ->where('madrasa_id', $madrasaId)
            ->whereDate('created_at', today())
            ->latest()->get()
            ->map(fn($item) => (object)[
                'id'           => $item->id,
                'payment_date' => $item->collection_date,
                'created_at'   => $item->created_at,
                'student_id'   => $item->student->student_id ?? '',
                'student_name' => $item->student->user->name_bn ?? $item->student->user->name ?? '',
                'month'        => $item->month,
                'amount'       => $item->paid_amount,
                'discount'     => $item->discount,
                'method'       => $item->payment_method_id ? 'Mobile/Bank' : 'Cash',
                'cashier_name' => $item->collector->name ?? '',
                'voucher_no'   => $item->receipt_no,
            ]);

        return view('admin.fee-collections.index', compact(
            'collections', 'paymentMethods', 'years', 'cashiers',
            'todayTotal', 'myTotal', 'payments'
        ));
    }

    public function create()
    {
        return view('admin.fee-collections.create');
    }

    public function show($id)
    {
        $collection = FeeCollection::with(['student.user', 'collector', 'paymentMethod'])
            ->findOrFail($id);
        return view('admin.fee-collections.show', compact('collection'));
    }

    public function destroy($id)
    {
        FeeCollection::findOrFail($id)->delete();
        return back()->with('success', 'ফি রেকর্ড মুছে ফেলা হয়েছে');
    }
}