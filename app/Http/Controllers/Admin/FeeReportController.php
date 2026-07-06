<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Models\Cashier;
use App\Models\Classes;
use App\Models\PaymentMethod;
use App\Models\FeeCollection;
use App\Models\Madrasa;
use App\Exports\FeeReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class FeeReportController extends Controller
{

    public function index(Request $request)
    {
        $dropdowns = $this->dropdownData();

        if (! $request->filled('report_type')) {
            return view('admin.fee-report.index', array_merge($dropdowns, [
                'filters' => $request->all(),
            ]));
        }

        $data = $this->buildReportData($request);

        return view('admin.fee-report.index', array_merge($dropdowns, $data));
    }

    public function print(Request $request)
    {
        $data = $this->buildReportData($request);

        return view('admin.fee-report.print', $data);
    }


    public function pdf(Request $request)
    {
        $data = $this->buildReportData($request);

        $html = view('admin.fee-report.partials.report', $data)->render();

        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'orientation'   => 'P',
            'margin_top'    => 12,
            'margin_bottom' => 12,
            'margin_left'   => 12,
            'margin_right'  => 12,
            // resources/fonts ফোল্ডার থেকে বাংলা ফন্ট খুঁজে নেবে
            'fontDir' => array_merge($fontDirs, [resource_path('fonts')]),
            'fontdata' => $fontData + [
                'notosansbengali' => [
                    'R' => 'NotoSansBengali-Regular.ttf',
                    'B' => 'NotoSansBengali-Bold.ttf',
                ],
            ],
            'default_font' => 'notosansbengali',
        ]);

        $mpdf->WriteHTML($html);

        return response(
            $mpdf->Output('fee-report-'.now()->format('Y-m-d').'.pdf', 'S')
        )->header('Content-Type', 'application/pdf');
    }

    /**
     * Excel Download — একই partials/report.blade.php ব্যবহার করে
     */
    public function excel(Request $request)
    {
        $data = $this->buildReportData($request);

        return Excel::download(
            new FeeReportExport($data),
            'fee-report-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Dropdown Data (Filter Sidebar)
    |--------------------------------------------------------------------------
    */
    private function dropdownData(): array
    {
        return [
            'madrasa'        => Madrasa::active()->first(),
            'academicYears'  => AcademicYear::orderByDesc('id')->get(),
            'cashiers'       => Cashier::orderBy('name')->get(),
            'classes'        => Classes::orderBy('name')->get(),
            'paymentMethods' => PaymentMethod::orderBy('name')->get(),
            'reportTypes'    => [
                'user'           => 'ব্যবহারকারী ভিত্তিক পেমেন্ট তালিকা',
                'class'          => 'ক্লাস ভিত্তিক পেমেন্ট তালিকা',
                'payment_method' => 'পেমেন্ট মাধ্যম ভিত্তিক তালিকা',
                'receipt'        => 'রশিদ ভিত্তিক পেমেন্ট তালিকা',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Query + Summary — index/print/pdf/excel সবাই এখান থেকেই ডেটা নেয়
    |--------------------------------------------------------------------------
    */
    private function buildReportData(Request $request): array
    {
        $academicYears  = AcademicYear::orderByDesc('id')->get();
        $cashiers       = Cashier::orderBy('name')->get();
        $classes        = Classes::orderBy('name')->get();
        $paymentMethods = PaymentMethod::orderBy('name')->get();

        $query = FeeCollection::with([
            'student.user',
            'student.class',
            'paymentMethod',
            'collector',
        ]);

        if ($request->filled('academic_year_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('academic_year_id', $request->academic_year_id);
            });
        }

        if ($request->report_type == 'user' && $request->filled('filter_id')) {
            $query->where('collected_by', $request->filter_id);
        }

        if ($request->report_type == 'class' && $request->filled('filter_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class_id', $request->filter_id);
            });
        }

        if ($request->report_type == 'payment_method' && $request->filled('filter_id')) {
            $query->where('payment_method_id', $request->filter_id);
        }

        // রশিদ নং ভিত্তিক (আংশিক মিল দিয়ে সার্চ)
        if ($request->report_type == 'receipt' && $request->filled('filter_id')) {
            $query->where('receipt_no', 'like', '%'.$request->filter_id.'%');
        }

        if ($request->filled('from_date')) {
            $query->whereDate('collection_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('collection_date', '<=', $request->to_date);
        }

        $reports = $query
            ->orderBy('collection_date')
            ->orderBy('receipt_no')
            ->get();

        $totalPayable  = $reports->sum('total_amount');
        $totalDiscount = $reports->sum('discount');
        $totalPaid     = $reports->sum('paid_amount');
        $totalDue      = $reports->sum('due_amount');
        $totalPrevious = $reports->sum('previous_paid');

        $reportTitle = match ($request->report_type) {
            'user'           => 'ব্যবহারকারী ভিত্তিক পেমেন্ট তালিকা',
            'class'          => 'ক্লাস ভিত্তিক পেমেন্ট তালিকা',
            'payment_method' => 'পেমেন্ট মাধ্যম ভিত্তিক তালিকা',
            'receipt'        => 'রশিদ ভিত্তিক পেমেন্ট তালিকা',
            default          => 'ফি রিপোর্ট',
        };

        $filterName = '';

        if ($request->report_type == 'user') {
            $filterName = optional($cashiers->firstWhere('id', $request->filter_id))->name;
        }

        if ($request->report_type == 'class') {
            $filterName = optional($classes->firstWhere('id', $request->filter_id))->full_name;
        }

        if ($request->report_type == 'payment_method') {
            $filterName = optional($paymentMethods->firstWhere('id', $request->filter_id))->name;
        }

        if ($request->report_type == 'receipt') {
            $filterName = $request->filter_id;
        }

        $selectedYear = optional(
            $academicYears->firstWhere('id', $request->academic_year_id)
        )->year;

        return [
            'reports'       => $reports,
            'totalPayable'  => $totalPayable,
            'totalDiscount' => $totalDiscount,
            'totalPaid'     => $totalPaid,
            'totalDue'      => $totalDue,
            'totalPrevious' => $totalPrevious,
            'reportTitle'   => $reportTitle,
            'filterName'    => $filterName,
            'selectedYear'  => $selectedYear,
            'madrasa'       => Madrasa::active()->first(),
            'filters'       => $request->all(),
            'academicYears' => $academicYears,
            'cashiers'      => $cashiers,
            'classes'       => $classes,
            'paymentMethods'=> $paymentMethods,
        ];
    }
}