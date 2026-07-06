<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FeeReportExport implements FromView, ShouldAutoSize
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * একই partials/report.blade.php ফাইল Excel তৈরির জন্যও ব্যবহার হচ্ছে —
     * আলাদা কোনো Excel টেমপ্লেট বানাতে হচ্ছে না।
     */
    public function view(): View
    {
        return view('admin.fee-report.partials.report', $this->data);
    }
}