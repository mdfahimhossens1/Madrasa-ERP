@extends('layouts.admin')
@section('title', 'আয়-ব্যয় রিপোর্ট জেনারেটর')
@section('page')

@php
    $madrasa   = auth()->user()?->madrasa;
    $madrasaName    = $madrasa?->name    ?? auth()->user()?->name ?? 'মাদরাসার নাম';
    $madrasaAddress = $madrasa?->address ?? '';
    $hasReport = isset($reports) && $reports->isNotEmpty() && request()->has('report_type') && request('report_type') != '';
@endphp

{{-- ===========================
     SCREEN-ONLY: Filter UI
     =========================== --}}
<div class="no-print">
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
    <div class="filter-card">
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

            {{-- Row 4: Payment Method + Cashier --}}
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

    {{-- Report Action Buttons (screen only, shown only when report exists) --}}
    @if($hasReport)
    <div class="report-actions">
        <button onclick="printReport()" class="btn-action btn-print">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            প্রিন্ট করুন
        </button>
        <button onclick="printReport()" class="btn-action btn-download">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            ডাউনলোড PDF
        </button>
    </div>
    @endif

    {{-- Empty state --}}
    @if(request()->has('report_type') && request('report_type') != '' && !$hasReport)
    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6M3 21h18M3 10l9-7 9 7"/></svg>
        <p>নির্বাচিত ফিল্টারে কোনো লেনদেন পাওয়া যায়নি।</p>
    </div>
    @endif

</div>{{-- /.page-wrapper --}}
</div>{{-- /.no-print --}}


{{-- =============================================
     PRINTABLE REPORT — shown on screen & print
     Only rendered if button was clicked with data
     ============================================= --}}
@if($hasReport)

@php
    $reportTypeLabel = match($filters['report_type'] ?? '') {
        'income'  => 'আয় (জমা) রিপোর্ট',
        'expense' => 'ব্যয় (খরচ) রিপোর্ট',
        default   => 'আয়-ব্যয় সম্মিলিত রিপোর্ট',
    };
    $filterModeLabel = ($filters['filter_mode'] ?? 'date') == 'voucher' ? 'ভাউচার ভিত্তিক' : 'তারিখ ভিত্তিক';
    $reportTitleBanner = match($filters['report_type'] ?? '') {
        'income'  => 'জমা পেমেন্ট স্টেটমেন্ট ' . $filterModeLabel,
        'expense' => 'খরচ পেমেন্ট স্টেটমেন্ট ' . $filterModeLabel,
        default   => 'জমা-খরচ স্টেটমেন্ট ' . $filterModeLabel,
    };
    $selectedFund = isset($filters['fund_id']) && $filters['fund_id']
        ? ($funds->find($filters['fund_id'])?->name ?? 'সকল ফান্ড')
        : 'সকল ফান্ড';
    $filterMode = $filters['filter_mode'] ?? 'date';
    $dateFrom   = $filters['date_from'] ?? '—';
    $dateTo     = $filters['date_to']   ?? '—';
    $grouped    = $reports->groupBy(function($r) {
        return $r->items->first()?->ledger?->name ?? 'অন্যান্য';
    });
    $isCombo = in_array($filters['report_type'] ?? '', ['income_expense', '']);
    $colSpanTotal = $isCombo ? 6 : 5;
@endphp

<div class="print-report" id="printable-area">

    {{-- ── HEADER ── --}}
    <div class="rpt-header">
        <div class="rpt-logo">
            @if($madrasa?->logo)
                <img src="{{ asset('storage/' . $madrasa->logo) }}" alt="Logo">
            @else
                <div class="logo-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="#1a2744" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                </div>
            @endif
        </div>
        <div class="rpt-org-info">
            <h1 class="rpt-org-name">{{ $madrasaName }}</h1>
            @if($madrasaAddress)
                <p class="rpt-org-address">{{ $madrasaAddress }}</p>
            @endif
        </div>
    </div>

    {{-- ── TITLE BANNER ── --}}
    <div class="rpt-title-bar">
        <span class="rpt-title-text">{{ $reportTitleBanner }}</span>
    </div>

    {{-- ── META ROW (left date | center fund | right date) ── --}}
    <div class="rpt-meta-row">
        <div class="rpt-meta-left">
            @if($filterMode == 'voucher')
                <span class="meta-date">ভাউচার নং: {{ $filters['voucher_no'] ?? '—' }}</span>
            @else
                <span class="meta-date">{{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }} হতে {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }} পর্যন্ত</span>
            @endif
        </div>
        <div class="rpt-meta-center">
            <div class="fund-badge">{{ $selectedFund }}</div>
        </div>
        <div class="rpt-meta-right">
            <span class="meta-date">মুদ্রণের তারিখ: {{ now()->format('d/m/Y h:i A') }}</span>
        </div>
    </div>

    {{-- ── TABLE ── --}}
    @php $globalSl = 1; @endphp

    <table class="rpt-table">
        <thead>
            <tr class="thead-main">
                <th class="col-sl">ক্র:</th>
                <th class="col-date">তারিখ</th>
                <th class="col-voucher">ভাউচার</th>
                <th class="col-subledger">সাব লেজার</th>
                <th class="col-desc">বিবরণ</th>
                @if($isCombo)<th class="col-type">ধরন</th>@endif
                <th class="col-amount">পরিমাণ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grouped as $ledgerName => $groupItems)
                {{-- Ledger group header --}}
                <tr class="group-header-tr">
                    <td colspan="{{ $isCombo ? 7 : 6 }}" class="group-header-td">{{ $ledgerName }}</td>
                </tr>
                @foreach($groupItems as $transaction)
                    @foreach($transaction->items as $item)
                    <tr class="data-row">
                        <td class="col-sl tc">{{ $globalSl++ }}</td>
                        <td class="col-date">{{ \Carbon\Carbon::parse($transaction->date)->format('Y-m-d') }}</td>
                        <td class="col-voucher tc">{{ $transaction->voucher_no }}</td>
                        <td class="col-subledger">{{ $item->subLedger?->name ?? 'সেল' }}</td>
                        <td class="col-desc">{{ $item->description ?? ($transaction->note ?? '—') }}</td>
                        @if($isCombo)
                        <td class="col-type tc">
                            <span class="type-badge {{ $transaction->type == 'income' ? 'type-in' : 'type-ex' }}">
                                {{ $transaction->type == 'income' ? 'আয়' : 'ব্যয়' }}
                            </span>
                        </td>
                        @endif
                        <td class="col-amount tr">{{ number_format($item->amount, 0) }}</td>
                    </tr>
                    @endforeach
                @endforeach
                {{-- Group subtotal --}}
                <tr class="group-total-tr">
                    <td colspan="{{ $colSpanTotal }}" class="tr group-total-label">{{ $ledgerName }} — মোট :</td>
                    <td class="tr group-total-val">
                        {{ number_format($groupItems->sum(fn($t) => $t->items->sum('amount')), 0) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            @if(($filters['report_type'] ?? '') != 'expense')
            <tr class="grand-tr income-tr">
                <td colspan="{{ $colSpanTotal }}" class="tr">সর্বমোট আয় :</td>
                <td class="tr grand-val">{{ number_format($totalIncome, 0) }}</td>
            </tr>
            @endif
            @if(($filters['report_type'] ?? '') != 'income')
            <tr class="grand-tr expense-tr">
                <td colspan="{{ $colSpanTotal }}" class="tr">সর্বমোট ব্যয় :</td>
                <td class="tr grand-val">{{ number_format($totalExpense, 0) }}</td>
            </tr>
            @endif
            @if($isCombo)
            <tr class="grand-tr balance-tr">
                <td colspan="{{ $colSpanTotal }}" class="tr">সাধারণ ব্যালান্স :</td>
                <td class="tr grand-val {{ $balance >= 0 ? 'clr-green' : 'clr-red' }}">{{ number_format(abs($balance), 0) }}</td>
            </tr>
            @endif
        </tfoot>
    </table>

    {{-- ── FOOTER ── --}}
    <div class="rpt-footer">
        <div class="rpt-footer-sig">
            <div class="sig-line"></div>
            <p>প্রস্তুতকারী</p>
        </div>
        <div class="rpt-footer-center">
            <p class="powered-by">© {{ now()->year }} Developed by SAHARA IT. All rights reserved.</p>
        </div>
        <div class="rpt-footer-sig">
            <div class="sig-line"></div>
            <p>অনুমোদনকারী</p>
        </div>
    </div>

</div>{{-- /#printable-area --}}

@endif

{{-- =====================
     STYLES
     ===================== --}}
<style>
/* ─── SCREEN WRAPPER ─── */
.page-wrapper { padding: 24px; max-width: 1060px; }
.page-header  { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; }
.page-header-icon { width: 46px; height: 46px; background: #eef2ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #1a2744; flex-shrink: 0; }
.page-header-text h2 { font-size: 20px; font-weight: 700; color: #1a2744; }
.page-header-text p  { font-size: 13px; color: #888; margin-top: 2px; }

/* ─── FILTER CARD ─── */
.filter-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); padding: 28px 32px; margin-bottom: 20px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
.form-row .full-width { grid-column: 1 / -1; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-label  { font-size: 13px; font-weight: 600; color: #374151; }
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
.form-actions { display: flex; gap: 12px; margin-top: 4px; }
.btn-primary { display: inline-flex; align-items: center; gap: 8px; background: #1a2744; color: #fff; border: none; padding: 11px 28px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background .2s; }
.btn-primary:hover { background: #243560; }
.btn-secondary { display: inline-flex; align-items: center; border: 1.5px solid #e5e7eb; color: #6b7280; background: #fff; padding: 11px 20px; border-radius: 8px; font-size: 14px; text-decoration: none; font-weight: 500; transition: all .2s; }
.btn-secondary:hover { background: #f9fafb; color: #374151; }

/* ─── REPORT ACTION BUTTONS ─── */
.report-actions { display: flex; gap: 10px; margin-bottom: 14px; justify-content: flex-end; }
.btn-action  { display: inline-flex; align-items: center; gap: 7px; padding: 9px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: all .2s; }
.btn-print   { background: #1a2744; color: #fff; }
.btn-print:hover  { background: #243560; }
.btn-download     { background: #0d9488; color: #fff; }
.btn-download:hover { background: #0f766e; }

/* ─── EMPTY STATE ─── */
.empty-state { text-align: center; padding: 48px; color: #94a3b8; }
.empty-state p { margin-top: 12px; font-size: 14px; }

/* ══════════════════════════════
   PRINT REPORT  (screen + print)
   ══════════════════════════════ */
.print-report {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 36px 40px 28px;
    max-width: 1060px;
    margin: 0 auto;
    font-family: 'SolaimanLipi', 'Kalpurush', Arial, sans-serif;
    font-size: 13px;
    color: #1a2744;
}

/* ── Org Header ── */
.rpt-header { display: flex; align-items: center; gap: 16px; justify-content: center; padding-bottom: 10px; border-bottom: 2.5px solid #1a2744; margin-bottom: 8px; }
.rpt-logo img, .logo-placeholder { width: 64px; height: 64px; object-fit: contain; }
.logo-placeholder { display: flex; align-items: center; justify-content: center; border: 1.5px dashed #c7d2fe; border-radius: 8px; }
.rpt-org-info { text-align: center; }
.rpt-org-name    { font-size: 24px; font-weight: 800; color: #1a2744; line-height: 1.2; }
.rpt-org-address { font-size: 13px; color: #555; margin-top: 3px; }

/* ── Title Banner ── */
.rpt-title-bar { text-align: center; margin: 10px 0 10px; }
.rpt-title-text {
    display: inline-block;
    border: 1.5px solid #1a2744;
    color: #1a2744;
    font-size: 14px; font-weight: 700;
    padding: 5px 32px; border-radius: 4px;
    letter-spacing: 0.3px;
}

/* ── Meta Row ── */
.rpt-meta-row {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 10px; font-size: 12px; color: #555;
}
.rpt-meta-left, .rpt-meta-right { flex: 1; }
.rpt-meta-right { text-align: right; }
.rpt-meta-center { text-align: center; }
.meta-date { font-size: 12px; color: #444; }
.fund-badge {
    display: inline-block;
    border: 1.5px solid #1a2744;
    padding: 4px 22px;
    border-radius: 4px;
    font-size: 13px; font-weight: 600;
    color: #1a2744;
}

/* ── Table ── */
.rpt-table { width: 100%; border-collapse: collapse; font-size: 13px; }

.rpt-table .thead-main th {
    background: #1a2744; color: #fff;
    padding: 8px 10px; text-align: left;
    font-size: 12.5px; font-weight: 600;
    border: 1px solid #2d3f6b;
}

.rpt-table .group-header-tr .group-header-td {
    background: #f0f4ff;
    color: #1a2744; font-weight: 700; font-size: 13px;
    padding: 6px 10px;
    border: 1px solid #c7d2fe;
    border-left: 3px solid #1a2744;
}

.rpt-table .data-row td {
    padding: 6px 10px;
    border: 1px solid #e5e7eb;
    color: #374151; vertical-align: middle;
}
.rpt-table .data-row:nth-child(even) td { background: #f9fafb; }

.rpt-table .group-total-tr td {
    background: #eef2ff;
    font-weight: 700; font-size: 13px;
    padding: 7px 10px;
    border: 1px solid #c7d2fe;
    color: #1a2744;
}
.group-total-val { font-weight: 800; }

.rpt-table tfoot .grand-tr td {
    padding: 7px 10px;
    border: 1px solid #e2e8f0;
    font-size: 13px; font-weight: 700;
}
.grand-tr.income-tr  td { background: #f0fdf4; }
.grand-tr.expense-tr td { background: #fef2f2; }
.grand-tr.balance-tr td { background: #eff6ff; font-size: 14px; }
.grand-val { font-weight: 800; }

/* ── Type badge ── */
.type-badge { display: inline-block; padding: 2px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.type-in  { background: #dcfce7; color: #166534; }
.type-ex  { background: #fee2e2; color: #991b1b; }

/* ── Column widths ── */
.col-sl       { width: 40px; }
.col-date     { width: 100px; }
.col-voucher  { width: 80px; }
.col-subledger{ width: 150px; }
.col-type     { width: 68px; }
.col-amount   { width: 90px; }

/* ── Utilities ── */
.tc { text-align: center; }
.tr { text-align: right; }
.clr-green { color: #15803d; }
.clr-red   { color: #dc2626; }

/* ── Footer ── */
.rpt-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 36px; padding-top: 14px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #666; }
.rpt-footer-sig { text-align: center; }
.sig-line { width: 140px; border-bottom: 1.5px solid #555; margin: 0 auto 6px; }
.rpt-footer-center { text-align: center; }
.powered-by { font-size: 11px; color: #aaa; }

/* ══════════════════════════════
   PRINT MEDIA — A4, only report
   ══════════════════════════════ */
@media print {
    .no-print { display: none !important; }

    html, body { background: #fff !important; margin: 0; padding: 0; }

    .print-report {
        border: none !important;
        border-radius: 0 !important;
        padding: 0 !important;
        max-width: 100% !important;
        box-shadow: none !important;
        margin: 0 !important;
    }

    @page {
        size: A4 portrait;
        margin: 14mm 12mm 14mm 12mm;
    }

    .rpt-table { page-break-inside: auto; }
    .rpt-table tr { page-break-inside: avoid; page-break-after: auto; }
    .rpt-table thead { display: table-header-group; }
    .rpt-table tfoot { display: table-footer-group; }

    .rpt-footer { margin-top: 24px; }
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

function printReport() {
    window.print();
}
</script>
@endsection