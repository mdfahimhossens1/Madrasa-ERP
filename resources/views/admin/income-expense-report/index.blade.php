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
    <div class="filter-card">
        <form action="{{ route('dashboard.reports.income-expense') }}" method="GET">

            {{-- Row 1: Report Type --}}
            <div class="form-row">
                <div class="form-group full-width">
                    <label class="form-label required">রিপোর্টের ধরন <span class="req">*</span></label>
                    <select name="report_type" id="report_type" class="form-select" required onchange="toggleVoucherField()">
                        <option value="">— নির্বাচন করুন —</option>
                        <optgroup label="জমা/খরচ ভিত্তিক">
                            <option value="income"        {{ old('report_type', $filters['report_type'] ?? '') == 'income'        ? 'selected' : '' }}>আয় (জমা) রিপোর্ট</option>
                            <option value="expense"       {{ old('report_type', $filters['report_type'] ?? '') == 'expense'       ? 'selected' : '' }}>ব্যয় (খরচ) রিপোর্ট</option>
                            <option value="income_expense"{{ old('report_type', $filters['report_type'] ?? '') == 'income_expense' ? 'selected' : '' }}>আয়-ব্যয় (সম্মিলিত)</option>
                        </optgroup>
                        <optgroup label="খাত ভিত্তিক">
                            <option value="ledger"        {{ old('report_type', $filters['report_type'] ?? '') == 'ledger'        ? 'selected' : '' }}>লেজার ভিত্তিক</option>
                            <option value="subledger"     {{ old('report_type', $filters['report_type'] ?? '') == 'subledger'     ? 'selected' : '' }}>সাব-লেজার ভিত্তিক</option>
                            <option value="payment_method"{{ old('report_type', $filters['report_type'] ?? '') == 'payment_method' ? 'selected' : '' }}>পেমেন্ট মেথড ভিত্তিক</option>
                        </optgroup>
                        <optgroup label="বিশেষ ভিত্তিক">
                            <option value="date_wise"     {{ old('report_type', $filters['report_type'] ?? '') == 'date_wise'     ? 'selected' : '' }}>তারিখ ভিত্তিক</option>
                            <option value="voucher_wise"  {{ old('report_type', $filters['report_type'] ?? '') == 'voucher_wise'  ? 'selected' : '' }}>ভাউচার ভিত্তিক</option>
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
                            <input type="radio" name="filter_mode" value="date" id="mode_date"
                                {{ old('filter_mode', $filters['filter_mode'] ?? 'date') == 'date' ? 'checked' : '' }}
                                onchange="toggleFilterMode('date')">
                            <span class="toggle-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                তারিখ অনুযায়ী
                            </span>
                        </label>
                        <label class="toggle-option">
                            <input type="radio" name="filter_mode" value="voucher" id="mode_voucher"
                                {{ old('filter_mode', $filters['filter_mode'] ?? '') == 'voucher' ? 'checked' : '' }}
                                onchange="toggleFilterMode('voucher')">
                            <span class="toggle-label">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                                ভাউচার নম্বর অনুযায়ী
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
                        <input type="date" name="date_from" class="form-input" value="{{ old('date_from', $filters['date_from'] ?? date('Y-m-01')) }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">শেষ তারিখ</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <input type="date" name="date_to" class="form-input" value="{{ old('date_to', $filters['date_to'] ?? date('Y-m-d')) }}">
                    </div>
                </div>
            </div>

            {{-- Row 3b: Voucher fields (hidden by default) --}}
            <div class="form-row hidden" id="voucher-fields">
                <div class="form-group">
                    <label class="form-label">ভাউচার নং</label>
                    <div class="input-with-icon">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                        <input type="number" name="voucher_from" class="form-input" placeholder="যেমন: 1001" value="{{ old('voucher_from', $filters['voucher_from'] ?? '') }}">
                    </div>
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

</div>

<style>
    .page-wrapper { padding: 24px; max-width: 860px; }
    .page-header { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; }
    .page-header-icon { width: 46px; height: 46px; background: #eef2ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #1a2744; }
    .page-header-text h2 { font-size: 20px; font-weight: 700; color: #1a2744; }
    .page-header-text p { font-size: 13px; color: #888; margin-top: 2px; }

    .filter-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); padding: 28px 32px; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .form-row .full-width { grid-column: 1 / -1; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-label { font-size: 13px; font-weight: 600; color: #374151; }
    .form-label .req { color: #e53935; }
    .form-select, .form-input {
        border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 10px 14px;
        font-size: 13px; color: #1a2744; background: #fff;
        transition: border-color .2s, box-shadow .2s;
        outline: none; width: 100%;
    }
    .form-select:focus, .form-input:focus { border-color: #1a2744; box-shadow: 0 0 0 3px rgba(26,39,68,0.08); }
    .input-with-icon { position: relative; }
    .input-with-icon .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; }
    .input-with-icon .form-input { padding-left: 34px; }

    .toggle-group { display: flex; gap: 0; border: 1.5px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
    .toggle-option input[type="radio"] { display: none; }
    .toggle-label {
        display: flex; align-items: center; gap: 6px;
        padding: 9px 20px; font-size: 13px; font-weight: 500;
        color: #6b7280; cursor: pointer; user-select: none;
        transition: background .2s, color .2s;
        border-right: 1px solid #e5e7eb;
    }
    .toggle-option:last-child .toggle-label { border-right: none; }
    .toggle-option input[type="radio"]:checked + .toggle-label { background: #1a2744; color: #fff; }

    .hidden { display: none !important; }

    .form-actions { display: flex; gap: 12px; margin-top: 8px; }
    .btn-primary {
        display: inline-flex; align-items: center; gap: 8px;
        background: #1a2744; color: #fff; border: none;
        padding: 11px 28px; border-radius: 8px; font-size: 14px; font-weight: 600;
        cursor: pointer; transition: background .2s;
    }
    .btn-primary:hover { background: #243560; }
    .btn-secondary {
        display: inline-flex; align-items: center;
        border: 1.5px solid #e5e7eb; color: #6b7280; background: #fff;
        padding: 11px 20px; border-radius: 8px; font-size: 14px;
        text-decoration: none; font-weight: 500; transition: all .2s;
    }
    .btn-secondary:hover { background: #f9fafb; color: #374151; }
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

    // On page load, apply saved state
    document.addEventListener('DOMContentLoaded', function () {
        const mode = document.querySelector('input[name="filter_mode"]:checked')?.value ?? 'date';
        toggleFilterMode(mode);
    });
</script>
@endsection