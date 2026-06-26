@extends('layouts.admin')
@section('title', 'আয়-ব্যয় রিপোর্ট জেনারেটর')
@section('page')

<div class="page-wrapper">

    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6M3 21h18M3 10l9-7 9 7"/>
            </svg>
        </div>
        <div class="page-header-text">
            <h2>আয়-ব্যয় রিপোর্ট জেনারেটর</h2>
            <p>ফিল্টার প্রয়োগ করে কাস্টম রিপোর্ট তৈরি করুন</p>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="filter-card no-print">
        <form action="{{ route('dashboard.reports.income-expense') }}" method="GET">

            {{-- Row 1: Report Type --}}
            <div class="form-row">
                <div class="form-group full-width">
                    <label class="form-label">রিপোর্টের ধরন <span class="req">*</span></label>
                    <select name="report_type" id="report_type" class="form-select" required>
                        <option value="">— নির্বাচন করুন —</option>
                        <optgroup label="জমা/খরচ ভিত্তিক">
                            <option value="income"         {{ ($filters['report_type'] ?? '') == 'income'         ? 'selected' : '' }}>আয় (জমা) রিপোর্ট</option>
                            <option value="expense"        {{ ($filters['report_type'] ?? '') == 'expense'        ? 'selected' : '' }}>ব্যয় (খরচ) রিপোর্ট</option>
                            <option value="income_expense" {{ ($filters['report_type'] ?? '') == 'income_expense' ? 'selected' : '' }}>আয়-ব্যয় (সম্মিলিত)</option>
                        </optgroup>
                    </select>
                </div>
            </div>

            {{-- Row 2: Filter Mode Toggle --}}
            <div class="form-row">
                <div class="form-group full-width">
                    <label class="form-label">ফিল্টার পদ্ধতি</label>
                    <div class="toggle-group">
                        <label class="toggle-option">
                            <input type="radio" name="filter_mode" value="date"
                                {{ ($filters['filter_mode'] ?? 'date') == 'date' ? 'checked' : '' }}
                                onchange="toggleFilterMode('date')">
                            <span class="toggle-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                তারিখ অনুযায়ী
                            </span>
                        </label>
                        <label class="toggle-option">
                            <input type="radio" name="filter_mode" value="voucher"
                                {{ ($filters['filter_mode'] ?? '') == 'voucher' ? 'checked' : '' }}
                                onchange="toggleFilterMode('voucher')">
                            <span class="toggle-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                                ভাউচার নং অনুযায়ী
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Row 3a: Date fields --}}
            <div class="form-row" id="date-fields">
                <div class="form-group">
                    <label class="form-label">শুরুর তারিখ</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <input type="date" name="date_from" class="form-input" value="{{ $filters['date_from'] ?? date('Y-m-01') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">শেষ তারিখ</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <input type="date" name="date_to" class="form-input" value="{{ $filters['date_to'] ?? date('Y-m-d') }}">
                    </div>
                </div>
            </div>

            {{-- Row 3b: Voucher fields --}}
            <div class="form-row hidden" id="voucher-fields">
                <div class="form-group">
                    <label class="form-label">ভাউচার নং</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                        <input type="number" name="voucher_no" class="form-input" placeholder="যেমন: 1001" value="{{ $filters['voucher_no'] ?? '' }}">
                    </div>
                </div>
            </div>

            {{-- Row 4: Extra Filters --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">পেমেন্ট মেথড</label>
                    <select name="payment_method_id" class="form-select">
                        <option value="">— সব —</option>
                        @foreach($paymentMethods as $pm)
                            <option value="{{ $pm->id }}" {{ ($filters['payment_method_id'] ?? '') == $pm->id ? 'selected' : '' }}>{{ $pm->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">ক্যাশিয়ার</label>
                    <select name="cashier_id" class="form-select">
                        <option value="">— সব —</option>
                        @foreach($cashiers as $c)
                            <option value="{{ $c->id }}" {{ ($filters['cashier_id'] ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Row 5: Fund + Sort --}}
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">ফান্ড</label>
                    <select name="fund_id" class="form-select">
                        <option value="">— সব —</option>
                        @foreach($funds as $f)
                            <option value="{{ $f->id }}" {{ ($filters['fund_id'] ?? '') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">সর্টিং</label>
                    <select name="sort_by" class="form-select">
                        <option value="newest"  {{ ($filters['sort_by'] ?? 'newest') == 'newest'  ? 'selected' : '' }}>সর্বশেষ তারিখ</option>
                        <option value="oldest"  {{ ($filters['sort_by'] ?? '') == 'oldest'  ? 'selected' : '' }}>পুরনো তারিখ</option>
                        <option value="highest" {{ ($filters['sort_by'] ?? '') == 'highest' ? 'selected' : '' }}>সর্বোচ্চ পরিমাণ</option>
                        <option value="lowest"  {{ ($filters['sort_by'] ?? '') == 'lowest'  ? 'selected' : '' }}>সর্বনিম্ন পরিমাণ</option>
                    </select>
                </div>
            </div>

            {{-- Submit --}}
            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    রিপোর্ট দেখুন
                </button>
                <a href="{{ route('dashboard.reports.income-expense') }}" class="btn-secondary">রিসেট</a>
            </div>

        </form>
    </div>

    {{-- ============================
         PRINTABLE REPORT SECTION
         ============================ --}}
    @if(isset($reports) && $reports->isNotEmpty())
    <div class="report-section" id="report-area">

        {{-- Action Buttons --}}
        <div class="report-actions no-print">
            <button onclick="window.print()" class="btn-action btn-print">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                প্রিন্ট করুন
            </button>
            <button onclick="downloadPDF()" class="btn-action btn-download">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                ডাউনলোড PDF
            </button>
        </div>

        {{-- ===== PRINTABLE REPORT ===== --}}
        <div class="print-report">

            {{-- Madrasha Header --}}
            <div class="rpt-header">
                <div class="rpt-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" onerror="this.style.display='none'">
                </div>
                <div class="rpt-org-info">
                    <h1 class="rpt-org-name">আবরারুল হক মহিলা মাদরাসা</h1>
                    <p class="rpt-org-address">আবরারপুর, পেকুয়া, কক্সবাজার</p>
                </div>
            </div>

            <div class="rpt-title-bar">
                <span class="rpt-title-text">
                    @if(($filters['report_type'] ?? '') == 'income')
                        জমা পেমেন্ট ভাউচার রিপোর্ট
                    @elseif(($filters['report_type'] ?? '') == 'expense')
                        খরচ পেমেন্ট ভাউচার রিপোর্ট
                    @else
                        জমা-খরচ পেমেন্ট ভাউচার রিপোর্ট
                    @endif
                </span>
            </div>

            {{-- Meta Info Row --}}
            <div class="rpt-meta">
                <div class="rpt-meta-item">
                    <span class="rpt-meta-label">রিপোর্টের ধরন :</span>
                    <span class="rpt-meta-value">
                        @if(($filters['report_type'] ?? '') == 'income') আয় (জমা) রিপোর্ট
                        @elseif(($filters['report_type'] ?? '') == 'expense') ব্যয় (খরচ) রিপোর্ট
                        @else আয়-ব্যয় সম্মিলিত @endif
                    </span>
                </div>
                <div class="rpt-meta-item">
                    <span class="rpt-meta-label">ফান্ড :</span>
                    <span class="rpt-meta-value">
                        {{ isset($filters['fund_id']) && $filters['fund_id'] ? ($funds->find($filters['fund_id'])?->name ?? '—') : 'সকল ফান্ড' }}
                    </span>
                </div>
                <div class="rpt-meta-item">
                    <span class="rpt-meta-label">Report Mode :</span>
                    <span class="rpt-meta-value">Date</span>
                </div>
                <div class="rpt-meta-item">
                    <span class="rpt-meta-label">Start Date :</span>
                    <span class="rpt-meta-value">{{ $filters['date_from'] ?? '—' }}</span>
                </div>
                <div class="rpt-meta-item">
                    <span class="rpt-meta-label">End Date :</span>
                    <span class="rpt-meta-value">{{ $filters['date_to'] ?? '—' }}</span>
                </div>
            </div>

            {{-- Summary Box --}}
            <div class="rpt-summary-bar">
                <div class="rpt-summary-item income">
                    <span class="s-label">মোট আয়</span>
                    <span class="s-value">৳ {{ number_format($totalIncome, 2) }}</span>
                </div>
                <div class="rpt-summary-item expense">
                    <span class="s-label">মোট ব্যয়</span>
                    <span class="s-value">৳ {{ number_format($totalExpense, 2) }}</span>
                </div>
                <div class="rpt-summary-item balance {{ $balance >= 0 ? 'positive' : 'negative' }}">
                    <span class="s-label">সাধারণ ব্যালান্স</span>
                    <span class="s-value">৳ {{ number_format(abs($balance), 2) }}</span>
                </div>
                <div class="rpt-summary-item voucher-count">
                    <span class="s-label">মোট ভাউচার</span>
                    <span class="s-value">{{ $totalVoucher }} টি</span>
                </div>
            </div>

            {{-- Report Table --}}
            @php
                $grouped = $reports->groupBy(function($r) { return $r->items->first()?->ledger?->name ?? 'অন্যান্য'; });
            @endphp

            @foreach($grouped as $ledgerName => $groupItems)
            <table class="rpt-table">
                <thead>
                    <tr>
                        <th class="col-sl">ক্র:</th>
                        <th class="col-date">তারিখ</th>
                        <th class="col-voucher">ভাউচার</th>
                        <th class="col-subledger">সাব লেজার</th>
                        <th class="col-desc">বিবরণ</th>
                        @if(($filters['report_type'] ?? '') == 'income_expense' || ($filters['report_type'] ?? '') == '')
                        <th class="col-type">ধরন</th>
                        @endif
                        <th class="col-amount">পরিমাণ</th>
                    </tr>
                    <tr class="group-header-row">
                        <td colspan="{{ (($filters['report_type'] ?? '') == 'income_expense' || ($filters['report_type'] ?? '') == '') ? 7 : 6 }}" class="group-label">{{ $ledgerName }}</td>
                    </tr>
                </thead>
                <tbody>
                    @php $sl = 1; @endphp
                    @foreach($groupItems as $transaction)
                        @foreach($transaction->items as $item)
                        <tr>
                            <td class="col-sl text-center">{{ $sl++ }}</td>
                            <td class="col-date">{{ \Carbon\Carbon::parse($transaction->date)->format('Y-m-d') }}</td>
                            <td class="col-voucher text-center">{{ $transaction->voucher_no }}</td>
                            <td class="col-subledger">{{ $item->subLedger?->name ?? 'সেল' }}</td>
                            <td class="col-desc">{{ $item->description ?? ($transaction->note ?? '—') }}</td>
                            @if(($filters['report_type'] ?? '') == 'income_expense' || ($filters['report_type'] ?? '') == '')
                            <td class="col-type text-center">
                                <span class="badge {{ $transaction->type == 'income' ? 'badge-income' : 'badge-expense' }}">
                                    {{ $transaction->type == 'income' ? 'আয়' : 'ব্যয়' }}
                                </span>
                            </td>
                            @endif
                            <td class="col-amount text-right">{{ number_format($item->amount, 0) }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="group-total-row">
                        <td colspan="{{ (($filters['report_type'] ?? '') == 'income_expense' || ($filters['report_type'] ?? '') == '') ? 6 : 5 }}" class="text-right group-total-label">{{ $ledgerName }} মোট :</td>
                        <td class="text-right group-total-value">
                            {{ number_format($groupItems->sum(fn($t) => $t->items->sum('amount')), 0) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
            @endforeach

            {{-- Grand Total --}}
            <table class="rpt-table grand-total-table">
                <tbody>
                    @if(($filters['report_type'] ?? '') != 'expense')
                    <tr class="grand-row income-row">
                        <td colspan="{{ (($filters['report_type'] ?? '') == 'income_expense' || ($filters['report_type'] ?? '') == '') ? 6 : 5 }}" class="text-right">সর্বমোট আয় :</td>
                        <td class="text-right fw-bold">{{ number_format($totalIncome, 0) }}</td>
                    </tr>
                    @endif
                    @if(($filters['report_type'] ?? '') != 'income')
                    <tr class="grand-row expense-row">
                        <td colspan="{{ (($filters['report_type'] ?? '') == 'income_expense' || ($filters['report_type'] ?? '') == '') ? 6 : 5 }}" class="text-right">সর্বমোট ব্যয় :</td>
                        <td class="text-right fw-bold">{{ number_format($totalExpense, 0) }}</td>
                    </tr>
                    @endif
                    @if(($filters['report_type'] ?? '') == 'income_expense' || ($filters['report_type'] ?? '') == '')
                    <tr class="grand-row balance-row">
                        <td colspan="6" class="text-right">সাধারণ ব্যালান্স :</td>
                        <td class="text-right fw-bold {{ $balance >= 0 ? 'text-green' : 'text-red' }}">{{ number_format(abs($balance), 0) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>

            {{-- Footer --}}
            <div class="rpt-footer">
                <div class="rpt-footer-col">
                    <div class="sig-line"></div>
                    <p>প্রস্তুতকারী</p>
                </div>
                <div class="rpt-footer-col">
                    <p class="rpt-generated">মুদ্রণের তারিখ: {{ now()->format('d/m/Y h:i A') }}</p>
                    <p class="rpt-powered">© {{ now()->year }} Developed by SAHARA IT</p>
                </div>
                <div class="rpt-footer-col">
                    <div class="sig-line"></div>
                    <p>অনুমোদনকারী</p>
                </div>
            </div>

        </div>{{-- /.print-report --}}
    </div>{{-- /.report-section --}}

    @elseif(request()->has('report_type'))
    <div class="empty-state no-print">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6M3 21h18M3 10l9-7 9 7"/></svg>
        <p>নির্বাচিত ফিল্টারে কোনো লেনদেন পাওয়া যায়নি।</p>
    </div>
    @endif

</div>{{-- /.page-wrapper --}}

<style>
/* ========== SCREEN STYLES ========== */
.page-wrapper { padding: 24px; max-width: 1000px; }
.page-header { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; }
.page-header-icon { width: 46px; height: 46px; background: #eef2ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #1a2744; flex-shrink: 0; }
.page-header-text h2 { font-size: 20px; font-weight: 700; color: #1a2744; }
.page-header-text p  { font-size: 13px; color: #888; margin-top: 2px; }

/* Filter Card */
.filter-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); padding: 28px 32px; margin-bottom: 24px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
.form-row .full-width { grid-column: 1 / -1; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-label { font-size: 13px; font-weight: 600; color: #374151; }
.form-label .req { color: #e53935; }
.form-select, .form-input {
    border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 10px 14px;
    font-size: 13px; color: #1a2744; background: #fff;
    transition: border-color .2s, box-shadow .2s; outline: none; width: 100%;
}
.form-select:focus, .form-input:focus { border-color: #1a2744; box-shadow: 0 0 0 3px rgba(26,39,68,0.08); }
.input-with-icon { position: relative; }
.input-with-icon .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; }
.input-with-icon .form-input { padding-left: 34px; }
.toggle-group { display: flex; border: 1.5px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
.toggle-option input[type="radio"] { display: none; }
.toggle-label { display: flex; align-items: center; gap: 6px; padding: 9px 20px; font-size: 13px; font-weight: 500; color: #6b7280; cursor: pointer; user-select: none; transition: background .2s, color .2s; border-right: 1px solid #e5e7eb; }
.toggle-option:last-child .toggle-label { border-right: none; }
.toggle-option input[type="radio"]:checked + .toggle-label { background: #1a2744; color: #fff; }
.hidden { display: none !important; }
.form-actions { display: flex; gap: 12px; margin-top: 8px; }
.btn-primary { display: inline-flex; align-items: center; gap: 8px; background: #1a2744; color: #fff; border: none; padding: 11px 28px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background .2s; }
.btn-primary:hover { background: #243560; }
.btn-secondary { display: inline-flex; align-items: center; border: 1.5px solid #e5e7eb; color: #6b7280; background: #fff; padding: 11px 20px; border-radius: 8px; font-size: 14px; text-decoration: none; font-weight: 500; transition: all .2s; }
.btn-secondary:hover { background: #f9fafb; color: #374151; }

/* Report Actions */
.report-actions { display: flex; gap: 10px; margin-bottom: 16px; justify-content: flex-end; }
.btn-action { display: inline-flex; align-items: center; gap: 7px; padding: 9px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: all .2s; }
.btn-print    { background: #1a2744; color: #fff; }
.btn-print:hover { background: #243560; }
.btn-download { background: #0d9488; color: #fff; }
.btn-download:hover { background: #0f766e; }

/* Empty State */
.empty-state { text-align: center; padding: 48px; color: #94a3b8; }
.empty-state p { margin-top: 12px; font-size: 14px; }

/* ========== PRINT REPORT STYLES ========== */
.print-report {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 32px;
    font-family: 'SolaimanLipi', 'Kalpurush', Arial, sans-serif;
}

/* Org Header */
.rpt-header { display: flex; align-items: center; gap: 16px; justify-content: center; padding-bottom: 12px; border-bottom: 2px solid #1a2744; margin-bottom: 6px; }
.rpt-logo img { width: 60px; height: 60px; object-fit: contain; }
.rpt-org-info { text-align: center; }
.rpt-org-name { font-size: 22px; font-weight: 800; color: #1a2744; line-height: 1.2; }
.rpt-org-address { font-size: 13px; color: #555; margin-top: 3px; }

/* Title Bar */
.rpt-title-bar { text-align: center; margin: 8px 0 16px; }
.rpt-title-text { display: inline-block; background: #1a2744; color: #fff; font-size: 14px; font-weight: 700; padding: 6px 28px; border-radius: 4px; letter-spacing: 0.5px; }

/* Meta */
.rpt-meta { display: flex; flex-wrap: wrap; gap: 8px 32px; margin-bottom: 16px; padding: 12px 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; }
.rpt-meta-item { font-size: 12.5px; color: #374151; }
.rpt-meta-label { font-weight: 600; color: #1a2744; margin-right: 4px; }
.rpt-meta-value { color: #555; }

/* Summary Bar */
.rpt-summary-bar { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
.rpt-summary-item { padding: 12px 16px; border-radius: 8px; text-align: center; }
.rpt-summary-item.income   { background: #ecfdf5; border: 1px solid #a7f3d0; }
.rpt-summary-item.expense  { background: #fef2f2; border: 1px solid #fca5a5; }
.rpt-summary-item.balance.positive  { background: #eff6ff; border: 1px solid #93c5fd; }
.rpt-summary-item.balance.negative  { background: #fef2f2; border: 1px solid #fca5a5; }
.rpt-summary-item.voucher-count { background: #f5f3ff; border: 1px solid #c4b5fd; }
.s-label { display: block; font-size: 11px; font-weight: 600; color: #555; margin-bottom: 4px; }
.s-value { display: block; font-size: 16px; font-weight: 800; color: #1a2744; }

/* Table */
.rpt-table { width: 100%; border-collapse: collapse; margin-bottom: 0; font-size: 13px; }
.rpt-table + .rpt-table { margin-top: 20px; }
.rpt-table thead th {
    background: #1a2744; color: #fff; padding: 8px 10px;
    text-align: left; font-size: 12.5px; font-weight: 600;
    border: 1px solid #243560;
}
.rpt-table .group-header-row td {
    background: #eef2ff; color: #1a2744; font-weight: 700;
    font-size: 13px; padding: 6px 10px;
    border: 1px solid #c7d2fe;
}
.rpt-table tbody tr td {
    padding: 7px 10px; border: 1px solid #e5e7eb;
    color: #374151; vertical-align: middle;
}
.rpt-table tbody tr:nth-child(even) td { background: #f9fafb; }
.rpt-table tfoot .group-total-row td {
    background: #f1f5f9; font-weight: 700; font-size: 13px;
    padding: 7px 10px; border: 1px solid #cbd5e1;
    color: #1a2744;
}
.grand-total-table { margin-top: 4px; }
.grand-total-table .grand-row td { padding: 7px 10px; border: 1px solid #e2e8f0; font-size: 13px; }
.grand-total-table .income-row td  { background: #f0fdf4; }
.grand-total-table .expense-row td { background: #fef2f2; }
.grand-total-table .balance-row td { background: #eff6ff; font-size: 14px; }

/* Badge */
.badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.badge-income  { background: #dcfce7; color: #166534; }
.badge-expense { background: #fee2e2; color: #991b1b; }

/* Column widths */
.col-sl       { width: 42px; }
.col-date     { width: 100px; }
.col-voucher  { width: 80px; }
.col-subledger{ width: 160px; }
.col-desc     { }
.col-type     { width: 70px; }
.col-amount   { width: 90px; }

/* Utilities */
.text-center { text-align: center; }
.text-right  { text-align: right; }
.fw-bold     { font-weight: 700; }
.text-green  { color: #15803d; }
.text-red    { color: #dc2626; }
.group-total-label { }
.group-total-value { font-weight: 700; color: #1a2744; }

/* Footer */
.rpt-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 32px; padding-top: 16px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #666; }
.rpt-footer-col { text-align: center; }
.sig-line { width: 140px; border-bottom: 1.5px solid #333; margin: 0 auto 6px; }
.rpt-generated { font-size: 11px; color: #888; }
.rpt-powered { font-size: 11px; color: #aaa; margin-top: 2px; }

/* ========== PRINT MEDIA ========== */
@media print {
    .no-print { display: none !important; }
    body { background: #fff; }
    .page-wrapper { padding: 0; max-width: 100%; }
    .print-report { border: none; border-radius: 0; padding: 20px; box-shadow: none; }
    .rpt-table { page-break-inside: auto; }
    .rpt-table tr { page-break-inside: avoid; }
    .rpt-summary-bar { grid-template-columns: repeat(4,1fr); }
    @page { margin: 15mm 12mm; size: A4; }
}
</style>

<script>
function toggleFilterMode(mode) {
    const dateFields    = document.getElementById('date-fields');
    const voucherFields = document.getElementById('voucher-fields');
    if (mode === 'date') {
        dateFields.classList.remove('hidden');
        voucherFields.classList.add('hidden');
    } else {
        dateFields.classList.add('hidden');
        voucherFields.classList.remove('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const mode = document.querySelector('input[name="filter_mode"]:checked')?.value ?? 'date';
    toggleFilterMode(mode);
});

function downloadPDF() {
    // Uses browser print dialog with PDF option
    // Or integrate html2pdf.js / jsPDF if available in your project
    window.print();
}
</script>
@endsection