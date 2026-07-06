<style>
    .report-container{
        background:#fff;
        border:1px solid #dcdcdc;
        padding:25px;
        font-family: 'SolaimanLipi', 'Kalpurush', sans-serif;
    }

    .report-header{
        text-align:center;
        margin-bottom:10px;
    }

    .report-header h2{
        margin:0;
        font-size:26px;
        font-weight:700;
    }

    .report-header p{
        margin:2px 0;
        font-size:14px;
    }

    .report-meta{
        width:100%;
        margin-bottom:12px;
        border-collapse:collapse;
    }

    .report-meta td{
        padding:4px 6px;
        font-size:14px;
        vertical-align:middle;
    }

    .report-meta .title-box{
        text-align:center;
        font-weight:700;
        font-size:16px;
        border:1px solid #000;
        padding:6px 14px;
    }

    .report-table{
        width:100%;
        border-collapse:collapse;
    }

    .report-table th,
    .report-table td{
        border:1px solid #000;
        padding:6px;
        font-size:13px;
    }

    .report-table th{
        background:#efefef;
        text-align:center;
        font-weight:bold;
    }

    .text-center{ text-align:center; }
    .text-right{ text-align:right; }
    .fw-bold{ font-weight:bold; }

    .report-footer-total td{
        font-weight:bold;
        background:#f7f7f7;
    }

    .signature-row{
        width:100%;
        margin-top:60px;
    }

    .signature-row td{
        text-align:center;
        font-size:13px;
        padding-top:30px;
        border-top:1px solid #000;
        width:30%;
    }

    @media print{
        body{ background:#fff; }
        .no-print{ display:none !important; }
        .report-container{ border:none; padding:0; }
        @page{ size:A4 portrait; margin:12mm; }
    }
</style>

{{-- Action Buttons: Print / PDF / Excel — তিনটাই এই একই ভিউ থেকে জেনারেট হয় --}}
<div class="d-flex justify-content-end gap-2 mb-3 no-print">

    <button type="button" onclick="window.print();" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-print"></i> Print
    </button>

    <a href="{{ route('dashboard.fee-report.pdf', request()->query()) }}"
       target="_blank"
       class="btn btn-outline-danger btn-sm">
        <i class="fas fa-file-pdf"></i> PDF
    </a>

    <a href="{{ route('dashboard.fee-report.excel', request()->query()) }}"
       class="btn btn-outline-success btn-sm">
        <i class="fas fa-file-excel"></i> Excel
    </a>

</div>

<div class="report-container">

    {{-- Header: প্রতিষ্ঠানের নাম, ঠিকানা, ফোন --}}
    <div class="report-header">
        <h2>{{ $madrasa->name ?? (auth()->user()->institution->name ?? 'বাংলাদেশ মাদ্রাসা ERP') }}</h2>
        @if(!empty($madrasa->address))
            <p>{{ $madrasa->address }}</p>
        @endif
        @if(!empty($madrasa->phone) || !empty($madrasa->phone_2))
            <p>{{ $madrasa->phone ?? '' }}{{ !empty($madrasa->phone_2) ? ' - '.$madrasa->phone_2 : '' }}</p>
        @endif
    </div>

    {{-- Meta Row: Date Range | Report Title | Print Date --}}
    <table class="report-meta">
        <tr>
            <td width="25%">
                @if(!empty($filters['from_date']) && !empty($filters['to_date']))
                    {{ bn_date(\Carbon\Carbon::parse($filters['from_date'])) }} হতে {{ bn_date(\Carbon\Carbon::parse($filters['to_date'])) }} পর্যন্ত
                @endif
            </td>
            <td width="50%" class="title-box">
                {{ bn_num($selectedYear) }} এর {{ $reportTitle }}
            </td>
            <td width="25%" class="text-right">
                প্রিন্ট তারিখঃ {{ bn_date(now()) }}
            </td>
        </tr>
    </table>

    {{-- Report Table --}}
    <table class="report-table">
        <thead>
            <tr>
                <th width="35">ক্র</th>
                <th>রশিদ</th>
                <th>আইডি</th>
                <th>শিক্ষার্থীর নাম</th>
                <th>ক্লাস</th>
                <th>ব্যবহারকারী</th>
                <th>পেমেন্ট মাধ্যম</th>
                <th>তারিখ</th>
                <th>নির্ধারিত</th>
                <th>পূর্ব জমা</th>
                <th>কর্তন</th>
                <th>জমা</th>
                <th>বকেয়া</th>
            </tr>
        </thead>
        <tbody>
        @forelse($reports as $key => $report)
            <tr>
                <td class="text-center">{{ bn_num($key + 1) }}</td>
                <td class="text-center">{{ bn_num($report->receipt_no) }}</td>
                <td class="text-center">{{ bn_num(optional($report->student)->student_id) }}</td>
                <td>{{ optional(optional($report->student)->user)->name }}</td>
                <td class="text-center">{{ optional(optional($report->student)->class)->full_name }}</td>
                <td class="text-center">{{ optional($report->collector)->name }}</td>
                <td class="text-center">{{ optional($report->paymentMethod)->name }}</td>
                <td class="text-center">{{ bn_date(optional($report->collection_date)) }}</td>
                <td class="text-right">{{ bn_num(number_format($report->total_amount, 2)) }}</td>
                <td class="text-right">{{ bn_num(number_format($report->previous_paid ?? 0, 2)) }}</td>
                <td class="text-right">{{ bn_num(number_format($report->discount, 2)) }}</td>
                <td class="text-right">{{ bn_num(number_format($report->paid_amount, 2)) }}</td>
                <td class="text-right">{{ bn_num(number_format($report->due_amount, 2)) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="13" class="text-center"><strong>কোনো তথ্য পাওয়া যায়নি</strong></td>
            </tr>
        @endforelse
        </tbody>

        @if($reports->count())
        <tfoot>
            <tr class="report-footer-total">
                <td colspan="8" class="text-right">মোট =</td>
                <td class="text-right">{{ bn_num(number_format($totalPayable, 2)) }}</td>
                <td class="text-right">{{ bn_num(number_format($totalPrevious ?? 0, 2)) }}</td>
                <td class="text-right">{{ bn_num(number_format($totalDiscount, 2)) }}</td>
                <td class="text-right">{{ bn_num(number_format($totalPaid, 2)) }}</td>
                <td class="text-right">{{ bn_num(number_format($totalDue, 2)) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- Signature --}}
    <table class="signature-row">
        <tr>
            <td>প্রস্তুতকারী</td>
            <td>যাচাইকারী</td>
            <td>অনুমোদনকারী</td>
        </tr>
    </table>

</div>