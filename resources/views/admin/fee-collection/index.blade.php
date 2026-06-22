@extends('layouts.admin')
@section('title', 'ফি গ্রহণ')
@section('page')

<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Hind Siliguri', sans-serif; background: #f0f2f5; color: #333; font-size: 14px; }

    .container { max-width: 1400px; margin: 20px auto; padding: 0 16px; }

    /* ===== TOP CARD ===== */
    .top-card {
        background: #fff;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        display: grid;
        grid-template-columns: 320px 1fr 280px;
        gap: 24px;
        align-items: start;
    }

    /* LEFT PANEL */
    .tab-nav { display: flex; gap: 4px; margin-bottom: 16px; border-bottom: 2px solid #e5e7eb; }
    .tab-btn {
        padding: 8px 16px; border: none; background: none; cursor: pointer;
        font-family: 'Hind Siliguri', sans-serif; font-size: 14px; font-weight: 500;
        color: #6b7280; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s;
    }
    .tab-btn.active { color: #2563eb; border-bottom-color: #2563eb; }
    .tab-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .filter-btn { background: none; border: 1px solid #d1d5db; border-radius: 6px; padding: 5px 10px; cursor: pointer; color: #6b7280; }
    .radio-group { display: flex; gap: 20px; margin-bottom: 16px; }
    .radio-label { display: flex; align-items: center; gap: 6px; cursor: pointer; font-weight: 500; }
    .radio-label input[type="radio"] { accent-color: #2563eb; width: 16px; height: 16px; }
    .search-row { display: flex; gap: 8px; margin-bottom: 14px; }
    .search-input {
        flex: 1; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px;
        font-family: 'Hind Siliguri', sans-serif; font-size: 14px; outline: none; transition: border-color 0.2s;
    }
    .search-input:focus { border-color: #2563eb; }
    .search-btn {
        background: #2563eb; border: none; border-radius: 8px; padding: 8px 14px;
        cursor: pointer; color: #fff; transition: background 0.2s;
    }
    .search-btn:hover { background: #1d4ed8; }
    .form-row { margin-bottom: 12px; }
    .form-label { display: block; font-weight: 500; color: #374151; margin-bottom: 4px; }
    .form-control {
        width: 100%; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px;
        font-family: 'Hind Siliguri', sans-serif; font-size: 14px; outline: none; transition: border-color 0.2s;
    }
    .form-control:focus { border-color: #2563eb; }
    .inline-label { display: flex; align-items: center; gap: 8px; }
    .inline-label .form-label { margin-bottom: 0; white-space: nowrap; min-width: 110px; }
    .receipt-row { display: flex; align-items: center; gap: 8px; }
    .receipt-value { font-weight: 600; color: #2563eb; }
    .receipt-settings-btn {
        background: none; border: 1px solid #d1d5db; border-radius: 6px;
        padding: 4px 8px; cursor: pointer; color: #6b7280; font-size: 12px;
    }

    /* MIDDLE PANEL — uses CSS grid internally */
    .middle-panel {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0;
    }

    .student-info-card { display: flex; gap: 16px; align-items: flex-start; }
    .student-photo { width: 100px; height: 110px; border-radius: 8px; object-fit: cover; flex-shrink: 0; }
    .student-photo-placeholder {
        width: 100px; height: 110px; border-radius: 8px; background: #e5e7eb;
        display: flex; align-items: center; justify-content: center;
        color: #9ca3af; font-size: 32px; flex-shrink: 0;
    }
    .student-details { flex: 1; }
    .student-detail-row { display: flex; align-items: center; gap: 6px; margin-bottom: 5px; font-size: 13.5px; }
    .detail-label { color: #6b7280; font-weight: 500; min-width: 90px; }
    .detail-value { font-weight: 600; color: #111827; }
    .student-name-value { font-size: 16px; font-weight: 700; color: #111827; }
    .badge-active { display: inline-block; background: #dcfce7; color: #16a34a; border-radius: 20px; padding: 3px 12px; font-size: 12px; font-weight: 600; margin-top: 4px; }

    /* Fee Summary */
    .fee-summary { margin-top: 16px; display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; text-align: center; }
    .fee-col-label { font-size: 12px; color: #6b7280; margin-bottom: 4px; font-weight: 500; }
    .fee-col-value { border-radius: 6px; padding: 7px 4px; font-weight: 700; font-size: 14px; transition: background .25s; }
    .fee-total    { background: #f3f4f6; color: #111827; }
    .fee-discount { background: #fef9c3; color: #854d0e; }
    .fee-paid     { background: #dcfce7; color: #15803d; }
    .fee-due      { background: #fee2e2; color: #dc2626; }
    .fee-receipt  { background: #f0fdf4; color: #166534; border: 1px dashed #86efac; }
    .fee-col-value.flash { background: #bfdbfe !important; }

    /* Remarks row */
    .remarks-row { margin-top: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .remarks-label { font-size: 12px; color: #6b7280; margin-bottom: 4px; }
    .remarks-box  { border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px 10px; font-family: 'Hind Siliguri', sans-serif; font-size: 13px; background: #f9fafb; color: #374151; }
    .remarks-textarea { width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px 10px; font-family: 'Hind Siliguri', sans-serif; font-size: 13px; outline: none; resize: none; transition: border-color 0.2s; }
    .remarks-textarea:focus { border-color: #2563eb; }

    /* ===== MONTH LIST SECTION — full width spanning entire middle panel ===== */
    .month-list-section {
        display: none;
        width: 100%;
        margin-top: 0;
    }
    .month-section-divider { border: none; border-top: 1.5px solid #e5e7eb; margin: 14px 0 12px 0; }
    .month-list-title { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 10px; }

    /* ---- HORIZONTAL MONTH SCROLL ---- */
    .month-scroll-wrap {
    display: flex;
    gap: 19px;
    padding-bottom: 15px;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
    }
    .month-scroll-wrap::-webkit-scrollbar { height: 4px; }
    .month-scroll-wrap::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .month-chip {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        padding: 8px 12px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-family: 'Hind Siliguri', sans-serif;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        flex-shrink: 0;
        transition: all 0.18s;
        min-width: 64px;
        text-align: center;
    }
    .month-chip .mc-name { font-size: 13px; }
    .month-chip .mc-fee  { font-size: 11px; opacity: 0.85; }
    .month-chip.due      { background: #fee2e2; color: #dc2626; border: 1.5px solid #fca5a5; }
    .month-chip.due:hover{ background: #fecaca; transform: translateY(-2px); box-shadow: 0 3px 8px rgba(220,38,38,0.2); }
    .month-chip.paid     { background: #dcfce7; color: #16a34a; border: 1.5px solid #86efac; cursor: default; opacity: 0.75; }
    .month-chip.partial  { background: #fef9c3; color: #92400e; border: 1.5px solid #fde047; }
    .month-chip.selected { background: #2563eb; color: #fff; border: 1.5px solid #1d4ed8; transform: translateY(-2px); box-shadow: 0 3px 8px rgba(37,99,235,0.3); }

    /* ---- ADDED MONTHS TABLE ---- */
    .added-months-section {
        display: none;
        margin-top: 12px;
    }
    .added-months-section.show { display: block; width: 980px}
    .added-months-label { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px; display: flex; align-items: center; justify-content: space-between; }
    .btn-clear-all { background: none; border: none; cursor: pointer; color: #dc2626; font-size: 12px; font-weight: 600; font-family: 'Hind Siliguri', sans-serif; }
    .added-months-table { width: 100%; border-collapse: collapse; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; }
    .added-months-table thead tr { background: #f8fafc; }
    .added-months-table th { padding: 8px 10px; font-size: 11px; font-weight: 600; color: #6b7280; text-align: left; border-bottom: 1px solid #e5e7eb; }
    .added-months-table tbody tr { border-bottom: 1px solid #f1f5f9; background: #fff; transition: background 0.12s; }
    .added-months-table tbody tr:hover { background: #f8fafc; }
    .added-months-table tbody tr:last-child { border-bottom: none; }
    .added-months-table td { padding: 8px 10px; font-size: 12px; color: #374151; }
    .amt-col { font-weight: 700; }
    .amt-paid { color: #16a34a; }
    .amt-disc { color: #b45309; }
    .btn-row-month { display: flex; gap: 6px; align-items: center; }
    .btn-edit-month, .btn-del-month {
        background: none; border: none; cursor: pointer; padding: 3px 6px;
        border-radius: 5px; font-size: 13px; transition: background 0.15s;
    }
    .btn-edit-month { color: #2563eb; }
    .btn-edit-month:hover { background: #eff6ff; }
    .btn-del-month  { color: #dc2626; }
    .btn-del-month:hover  { background: #fee2e2; }
    .added-months-total-row { background: #f0fdf4 !important; }
    .added-months-total-row td { font-weight: 700; color: #15803d; font-size: 12px; }

    /* ===== OTHER (অনন্য) FEE SUMMARY BOX — popup এর মতো, ভর্তি ফি বিবরণ স্টাইলে ===== */
    .other-summary-box {
        display: none;
        background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 10px;
        padding: 12px 14px; margin-bottom: 14px;
    }
    .other-summary-box.show { display: block; }
    .other-sum-title { font-size: 12px; font-weight: 700; color: #4338ca; margin-bottom: 8px; }
    .other-sum-row { display: flex; justify-content: space-between; font-size: 12px; color: #374151; margin-bottom: 4px; }
    .other-sum-total { font-weight: 700; color: #4338ca; }
    .other-sum-edit-btn {
        margin-top: 8px; background: none; border: 1px solid #c7d2fe; border-radius: 6px;
        padding: 4px 10px; cursor: pointer; font-size: 12px; color: #4338ca; font-weight: 600;
        font-family: 'Hind Siliguri', sans-serif; transition: all 0.15s; width: 100%;
    }
    .other-sum-edit-btn:hover { background: #e0e7ff; }

    /* RIGHT PANEL */
    .payment-type-row { display: flex; gap: 8px; margin-bottom: 16px; }
    .pay-type-btn {
        flex: 1; padding: 9px 6px; border-radius: 8px; border: 1px solid #d1d5db;
        background: #f9fafb; cursor: pointer; font-family: 'Hind Siliguri', sans-serif;
        font-size: 13px; font-weight: 600; color: #374151;
        display: flex; align-items: center; justify-content: center; gap: 5px; transition: all 0.2s;
    }
    .pay-type-btn.active-type { background: #1e3a5f; color: #fff; border-color: #1e3a5f; }

    /* ===== PAYMENT METHOD SECTION — pay-card style (transaction form এর মতো) ===== */
    .pay-method-label {
        font-size: 12px; color: #6b7280; margin-bottom: 8px; font-weight: 500;
    }

    .payment-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; margin-bottom: 12px; }
    .pay-card { border: 2px solid #e5e7eb; border-radius: 12px; padding: 12px 6px; text-align: center; cursor: pointer; transition: all .2s; }
    .pay-card.active { border-color: #00897b; background: #e0f2f1; }
    .pay-card i { font-size: 20px; margin-bottom: 5px; display: block; color: #777; }
    .pay-card.active i { color: #00897b; }
    .pay-card span { font-size: 11px; font-weight: 700; color: #666; display: block; }
    .pay-card.active span { color: #00695c; }

    /* Account select */
    .pay-account-section { margin-bottom: 10px; }
    .pay-account-label { font-size: 11px; color: #6b7280; margin-bottom: 6px; font-weight: 500; }

    .selected-account-bar {
        display: none;
        background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px;
        padding: 8px 12px; margin-bottom: 12px; font-size: 12px;
        color: #166534; font-weight: 600; align-items: center; gap: 6px;
    }
    .selected-account-bar.show { display: flex; }

    .add-method-btn-row { display: flex; justify-content: flex-end; margin-bottom: 12px; }
    .add-method-btn {
        display: flex; align-items: center; gap: 6px; padding: 6px 14px;
        background: #1976d2; color: #fff; border: none; border-radius: 8px;
        cursor: pointer; font-size: 12px; font-family: inherit; font-weight: 600;
    }
    .add-method-btn:hover { background: #1565c0; }

    /* Admission summary box */
    .admission-summary-box {
        display: none;
        background: #f0fdf4; border: 1px solid #86efac; border-radius: 10px;
        padding: 12px 14px; margin-bottom: 14px;
    }
    .admission-summary-box.show { display: block; }
    .adm-sum-title { font-size: 12px; font-weight: 700; color: #15803d; margin-bottom: 8px; }
    .adm-sum-row { display: flex; justify-content: space-between; font-size: 12px; color: #374151; margin-bottom: 4px; }
    .adm-sum-total { font-weight: 700; color: #16a34a; }
    .adm-sum-edit-btn {
        margin-top: 8px; background: none; border: 1px solid #86efac; border-radius: 6px;
        padding: 4px 10px; cursor: pointer; font-size: 12px; color: #15803d; font-weight: 600;
        font-family: 'Hind Siliguri', sans-serif; transition: all 0.15s; width: 100%;
    }
    .adm-sum-edit-btn:hover { background: #dcfce7; }

    .cashier-label { font-size: 12px; color: #6b7280; margin-bottom: 6px; font-weight: 500; }
    .cashier-row { display: flex; gap: 6px; align-items: center; margin-bottom: 16px; }
    .cashier-select {
        flex: 1; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 12px;
        font-family: 'Hind Siliguri', sans-serif; font-size: 13px; outline: none; background: #fff;
    }
    .cashier-select:focus { border-color: #2563eb; }

    .btn-row { display: flex; gap: 10px; }
    .btn-reset {
        flex: 1; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;
        background: #fff; cursor: pointer; font-family: 'Hind Siliguri', sans-serif;
        font-size: 14px; font-weight: 600; color: #374151; transition: background 0.2s;
    }
    .btn-reset:hover { background: #f3f4f6; }
    .btn-save {
        flex: 2; padding: 10px; border: none; border-radius: 8px;
        background: #16a34a; cursor: pointer; font-family: 'Hind Siliguri', sans-serif;
        font-size: 14px; font-weight: 700; color: #fff; transition: background 0.2s;
    }
    .btn-save:hover { background: #15803d; }
    .btn-save:disabled { background: #9ca3af; cursor: not-allowed; }

    /* ===== MONTH FEE ACCEPT POPUP ===== */
    .month-popup-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(15,23,42,0.55); z-index: 9997;
        align-items: center; justify-content: center;
    }
    .month-popup-overlay.show { display: flex; }
    .month-popup-box {
        background: #fff; border-radius: 14px; padding: 0;
        width: 100%; max-width: 460px;
        box-shadow: 0 20px 60px rgba(15,23,42,0.25);
        animation: popupIn 0.2s ease;
        overflow: hidden;
    }
    .month-popup-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid #e5e7eb; background: #f8fafc;
    }
    .month-popup-title { font-size: 15px; font-weight: 700; color: #1e293b; }
    .month-popup-close {
        background: none; border: none; cursor: pointer; color: #9ca3af;
        font-size: 18px; padding: 4px; border-radius: 6px; transition: color 0.2s;
    }
    .month-popup-close:hover { color: #374151; }
    .month-popup-body { padding: 20px; }
    .month-popup-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
    .mp-field-label { font-size: 12px; color: #6b7280; font-weight: 600; margin-bottom: 5px; }
    .mp-field-value {
        width: 100%; border: 1.5px solid #e2e8f0; border-radius: 8px;
        padding: 9px 12px; font-size: 14px; font-family: 'Hind Siliguri', sans-serif;
        color: #1e293b; background: #f8fafc; outline: none; transition: border-color 0.2s;
    }
    .mp-field-value:focus { border-color: #2563eb; background: #fff; }
    .mp-field-value[readonly] { background: #f1f5f9; color: #64748b; cursor: default; }
    .mp-fee-table { width: 100%; border-collapse: collapse; margin-top: 10px; border-radius: 8px; overflow: hidden; }
    .mp-fee-table thead tr { background: #f1f5f9; }
    .mp-fee-table th { padding: 9px 12px; font-size: 12px; font-weight: 600; color: #64748b; text-align: left; }
    .mp-fee-table tbody tr { border-top: 1px solid #f1f5f9; }
    .mp-fee-table td { padding: 10px 12px; font-size: 13px; color: #374151; }
    .mp-fee-table td.td-paid { color: #16a34a; font-weight: 700; }
    .mp-fee-table td.td-due  { color: #dc2626; font-weight: 700; }
    .month-popup-footer {
        display: flex; gap: 10px; padding: 16px 20px; border-top: 1px solid #e5e7eb; background: #f8fafc;
    }
    .mp-btn-cancel {
        flex: 1; padding: 10px; border-radius: 8px; border: 1.5px solid #e2e8f0;
        background: #fff; color: #475569; font-size: 14px; font-weight: 600;
        cursor: pointer; font-family: 'Hind Siliguri', sans-serif; transition: all 0.15s;
    }
    .mp-btn-cancel:hover { background: #f1f5f9; }
    .mp-btn-add {
        flex: 2; padding: 10px; border-radius: 8px; border: none;
        background: #2563eb; color: #fff; font-size: 14px; font-weight: 700;
        cursor: pointer; font-family: 'Hind Siliguri', sans-serif; transition: all 0.15s;
    }
    .mp-btn-add:hover { background: #1d4ed8; }
    .mp-btn-add:disabled { background: #cbd5e1; cursor: not-allowed; }

    /* ===== ADMISSION FEE POPUP (ভর্তি ফি ও অনন্য ফি দুটোতেই ব্যবহৃত — generic) ===== */
    .admission-popup-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(15,23,42,0.55); z-index: 9997;
        align-items: center; justify-content: center;
    }
    .admission-popup-overlay.show { display: flex; }
    .admission-popup-box {
        background: #fff; border-radius: 14px;
        width: 100%; max-width: 520px;
        box-shadow: 0 20px 60px rgba(15,23,42,0.25);
        animation: popupIn 0.2s ease;
        overflow: hidden;
    }
    .adm-popup-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 16px 20px; border-bottom: 1px solid #e5e7eb; background: #f8fafc;
    }
    .adm-popup-title { font-size: 15px; font-weight: 700; color: #1e293b; }
    .adm-popup-close {
        background: none; border: none; cursor: pointer; color: #9ca3af;
        font-size: 18px; padding: 4px; border-radius: 6px; transition: color 0.2s;
    }
    .adm-popup-close:hover { color: #374151; }
    .adm-popup-body { padding: 20px; }
    .adm-student-strip {
        display: flex; align-items: center; gap: 12px;
        background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 12px 14px; margin-bottom: 16px;
    }
    .adm-student-avatar {
        width: 44px; height: 44px; border-radius: 50%; background: #dbeafe;
        display: flex; align-items: center; justify-content: center;
        color: #2563eb; font-size: 18px; flex-shrink: 0;
    }
    .adm-student-name { font-weight: 700; color: #111827; font-size: 14px; }
    .adm-student-sub  { font-size: 12px; color: #6b7280; margin-top: 2px; }
    .adm-fee-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
    .adm-field-label { font-size: 12px; color: #6b7280; font-weight: 600; margin-bottom: 5px; }
    .adm-field-value {
        width: 100%; border: 1.5px solid #e2e8f0; border-radius: 8px;
        padding: 9px 12px; font-size: 14px; font-family: 'Hind Siliguri', sans-serif;
        color: #1e293b; background: #f8fafc; outline: none; transition: border-color 0.2s;
    }
    .adm-field-value:focus { border-color: #2563eb; background: #fff; }
    .adm-field-value[readonly] { background: #f1f5f9; color: #64748b; cursor: default; }
    .adm-fee-items { border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; margin-bottom: 16px; max-height: 320px; overflow-y: auto; }
    .adm-fee-items-header {
        background: #f1f5f9; display: grid;
        grid-template-columns: 1fr 80px 80px 80px;
        padding: 8px 12px; font-size: 11px; font-weight: 600; color: #64748b;
        position: sticky; top: 0;
    }
    .adm-fee-item-row {
        display: grid; grid-template-columns: 1fr 80px 80px 80px;
        padding: 10px 12px; border-top: 1px solid #f1f5f9;
        font-size: 13px; align-items: center;
    }
    .adm-fee-item-row:first-of-type { border-top: none; }
    .adm-fee-name { color: #374151; font-weight: 500; }
    .adm-fee-amount { color: #16a34a; font-weight: 700; text-align: center; }
    .adm-fee-discount-input {
        border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 6px;
        font-size: 12px; font-family: 'Hind Siliguri', sans-serif; width: 68px;
        text-align: center; outline: none; transition: border-color .15s;
    }
    .adm-fee-discount-input:focus { border-color: #2563eb; }
    .adm-fee-deposit-input {
        border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px 6px;
        font-size: 12px; font-family: 'Hind Siliguri', sans-serif; width: 68px;
        text-align: center; outline: none; transition: border-color .15s;
    }
    .adm-fee-deposit-input:focus { border-color: #2563eb; }
    .adm-fee-item-row.row-paid { background: #f0fdf4; opacity: .65; }
    .adm-fee-paid-tag { font-size: 9px; background: #dcfce7; color: #16a34a; border-radius: 8px; padding: 1px 6px; font-weight: 700; margin-left: 6px; }
    .adm-total-strip {
        background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px;
        padding: 10px 14px; display: flex; justify-content: space-between; align-items: center;
    }
    .adm-total-label { font-size: 13px; color: #374151; font-weight: 600; }
    .adm-total-value { font-size: 16px; font-weight: 800; color: #16a34a; }
    .adm-popup-footer {
        display: flex; gap: 10px; padding: 16px 20px; border-top: 1px solid #e5e7eb; background: #f8fafc;
    }
    .adm-btn-cancel {
        flex: 1; padding: 10px; border-radius: 8px; border: 1.5px solid #e2e8f0;
        background: #fff; color: #475569; font-size: 14px; font-weight: 600;
        cursor: pointer; font-family: 'Hind Siliguri', sans-serif; transition: all 0.15s;
    }
    .adm-btn-cancel:hover { background: #f1f5f9; }
    .adm-btn-save {
        flex: 2; padding: 10px; border-radius: 8px; border: none;
        background: #16a34a; color: #fff; font-size: 14px; font-weight: 700;
        cursor: pointer; font-family: 'Hind Siliguri', sans-serif; transition: all 0.15s;
    }
    .adm-btn-save:hover { background: #15803d; }
    .adm-btn-save:disabled { background: #cbd5e1; cursor: not-allowed; }

    /* ADD PAYMENT METHOD POPUP */
    .popup-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(15,23,42,0.5); z-index: 9998;
        align-items: center; justify-content: center;
    }
    .popup-overlay.show { display: flex; }
    .popup-box {
        background: #fff; border-radius: 16px; padding: 26px 24px 22px;
        width: 100%; max-width: 360px;
        box-shadow: 0 20px 60px rgba(15,23,42,0.2);
        animation: popupIn 0.2s ease;
    }
    @keyframes popupIn {
        from { transform: translateY(16px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
    .popup-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 18px; }
    .popup-type-row { display: flex; gap: 10px; margin-bottom: 16px; }
    .popup-type-card {
        flex: 1; padding: 11px 8px; border-radius: 10px; border: 2px solid #e2e8f0;
        background: #f8fafc; text-align: center; cursor: pointer;
        font-size: 13px; font-weight: 600; color: #475569;
        transition: all 0.15s; font-family: 'Hind Siliguri', sans-serif;
    }
    .popup-type-card:hover { border-color: #2563eb; color: #2563eb; }
    .popup-type-card.selected { border-color: #2563eb; background: #eff6ff; color: #1d4ed8; }
    .popup-label { font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px; display: block; }
    .popup-input {
        width: 100%; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px;
        font-size: 13px; font-family: 'Hind Siliguri', sans-serif; color: #1e293b;
        background: #f8fafc; outline: none; margin-bottom: 12px; transition: border-color 0.15s;
    }
    .popup-input:focus { border-color: #2563eb; background: #fff; }
    .popup-btn-row { display: flex; gap: 8px; margin-top: 6px; }
    .popup-btn-cancel {
        flex: 1; padding: 10px; border-radius: 8px; border: 1.5px solid #e2e8f0;
        background: #f8fafc; color: #475569; font-size: 13px; font-weight: 600;
        cursor: pointer; font-family: 'Hind Siliguri', sans-serif; transition: all 0.15s;
    }
    .popup-btn-cancel:hover { background: #f1f5f9; }
    .popup-btn-save {
        flex: 2; padding: 10px; border-radius: 8px; border: none;
        background: #2563eb; color: #fff; font-size: 13px; font-weight: 700;
        cursor: pointer; font-family: 'Hind Siliguri', sans-serif; transition: all 0.15s;
    }
    .popup-btn-save:hover    { background: #1d4ed8; }
    .popup-btn-save:disabled { background: #cbd5e1; cursor: not-allowed; }

    /* BOTTOM TABLE */
    .table-card { background: #1e293b; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .table-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 20px; gap: 16px; flex-wrap: wrap;
    }
    .table-title { color: #fff; font-size: 15px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
    .table-title i { color: #94a3b8; }
    .summary-badges { display: flex; gap: 10px; flex-wrap: wrap; }
    .summary-badge { background: rgba(255,255,255,0.1); border-radius: 20px; padding: 5px 14px; color: #fff; font-size: 13px; font-weight: 600; transition: background .3s; }
    .summary-badge span { color: #4ade80; }
    .summary-badge.flash { background: rgba(74,222,128,0.25); }
    .table-search-row { display: flex; gap: 8px; align-items: center; }
    .table-search-input {
        background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);
        border-radius: 8px; padding: 7px 14px; font-family: 'Hind Siliguri', sans-serif;
        font-size: 13px; color: #fff; outline: none; width: 130px; transition: width 0.3s;
    }
    .table-search-input::placeholder { color: rgba(255,255,255,0.5); }
    .table-search-input:focus { width: 180px; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead tr { background: #0f172a; }
    .data-table th { padding: 11px 14px; color: #94a3b8; font-size: 12px; font-weight: 600; text-align: left; white-space: nowrap; }
    .data-table tbody tr { background: #fff; border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
    .data-table tbody tr:hover { background: #f8fafc; }
    .data-table td { padding: 11px 14px; color: #374151; font-size: 13px; vertical-align: middle; }
    .td-date { color: #1e293b; font-weight: 600; font-size: 12px; }
    .td-time { color: #9ca3af; font-size: 11px; }
    .td-name { font-weight: 700; color: #111827; }
    .td-amount { font-weight: 700; color: #16a34a; }
    .td-discount { color: #374151; }
    .badge-method { display: inline-block; border-radius: 20px; padding: 3px 10px; font-size: 11px; font-weight: 600; }
    .badge-cash   { background: #dcfce7; color: #15803d; }
    .badge-mobile { background: #fef9c3; color: #92400e; }
    .badge-bank   { background: #dbeafe; color: #1d4ed8; }
    .btn-print { background: none; border: none; cursor: pointer; color: #6b7280; font-size: 16px; padding: 4px 8px; border-radius: 6px; transition: color 0.2s; }
    .btn-print:hover { color: #111827; }

    /* STATEMENT MODAL */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; }
    .modal-overlay.show { display: flex; }
    .modal-box { background: #fff; border-radius: 16px; width: 90%; max-width: 750px; max-height: 85vh; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
    .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 18px 24px; border-bottom: 1px solid #e5e7eb; }
    .modal-header-left { display: flex; align-items: center; gap: 12px; }
    .modal-icon { width: 40px; height: 40px; background: #f3f4f6; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #374151; font-size: 16px; }
    .modal-title { font-size: 16px; font-weight: 700; color: #111827; }
    .modal-subtitle { font-size: 13px; color: #6b7280; margin-top: 2px; }
    .modal-close { background: none; border: none; cursor: pointer; color: #9ca3af; font-size: 20px; padding: 4px; border-radius: 6px; transition: color 0.2s; }
    .modal-close:hover { color: #374151; }
    .btn-print-all { background: #1e3a5f; color: #fff; border: none; border-radius: 8px; padding: 8px 18px; cursor: pointer; font-family: 'Hind Siliguri', sans-serif; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; }
    .modal-body { overflow-y: auto; padding: 0; }
    .statement-table { width: 100%; border-collapse: collapse; }
    .statement-table thead tr { background: #f8fafc; }
    .statement-table th { padding: 12px 16px; color: #6b7280; font-size: 12px; font-weight: 600; text-align: left; border-bottom: 2px solid #e5e7eb; white-space: nowrap; }
    .statement-table tbody tr { border-bottom: 1px solid #f1f5f9; transition: background 0.15s; }
    .statement-table tbody tr:hover { background: #f8fafc; }
    .statement-table td { padding: 13px 16px; font-size: 13px; color: #374151; vertical-align: middle; }
    .st-amount { font-weight: 700; color: #16a34a; }
    .st-voucher { color: #6b7280; font-size: 12px; }
    .no-data { text-align: center; padding: 40px; color: #9ca3af; font-size: 14px; }

    /* Toast */
    .toast { position: fixed; bottom: 24px; right: 24px; background: #1e293b; color: #fff; padding: 12px 20px; border-radius: 10px; font-size: 14px; font-weight: 500; z-index: 99999; display: none; align-items: center; gap: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.2); }
    .toast.show    { display: flex; }
    .toast.success { border-left: 4px solid #16a34a; }
    .toast.error   { border-left: 4px solid #dc2626; }

    @media (max-width: 1100px) { .top-card { grid-template-columns: 1fr; } }
</style>

<div class="container">

    {{-- ===== TOP CARD ===== --}}
    <div class="top-card">

        {{-- LEFT PANEL --}}
        <div class="left-panel">
            <div class="tab-header">
                <div class="tab-nav">
                    <button class="tab-btn active" id="tabFee" onclick="switchTab('fee')">ফি গ্রহণ</button>
                    <button class="tab-btn" id="tabStatement" onclick="switchTab('statement')">স্টেটমেন্ট</button>
                </div>
                <button class="filter-btn"><i class="fas fa-sliders-h"></i></button>
            </div>

            <div class="radio-group">
                <label class="radio-label"><input type="radio" name="search_type" value="id" checked> ID</label>
                <label class="radio-label"><input type="radio" name="search_type" value="card"> Card</label>
            </div>

            <div class="search-row">
                <input type="text" class="search-input" id="studentIdInput" placeholder="101">
                <button class="search-btn" onclick="searchStudent()"><i class="fas fa-search"></i></button>
            </div>

            <div class="form-row">
                <div class="inline-label">
                    <label class="form-label">পেমেন্ট তারিখ :</label>
                    <input type="date" class="form-control" id="paymentDate" value="{{ date('Y-m-d') }}">
                </div>
            </div>

            <div class="form-row">
                <div class="inline-label">
                    <label class="form-label">শিক্ষাবর্ষ :</label>
                    <select class="form-control" id="academicYear">
                        @foreach($years as $year)
                            <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>
                                {{ $year->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="inline-label">
                    <label class="form-label">রিসিট সিরিয়াল :</label>
                    <div class="receipt-row">
                        <span class="receipt-value" id="receiptSerial">অটো</span>
                        <button class="receipt-settings-btn"><i class="fas fa-cog"></i></button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MIDDLE PANEL --}}
        <div class="middle-panel" id="studentInfoPanel">

            {{-- Student info + fee summary + remarks (top block) --}}
            <div>
                <div class="student-info-card">
                    <div class="student-photo-placeholder" id="studentPhotoWrap">
                        <i class="fas fa-user" id="photoIcon"></i>
                    </div>
                    <div class="student-details">
                        <div class="student-detail-row">
                            <span class="detail-label">নাম :</span>
                            <span class="detail-value student-name-value" id="stdName">—</span>
                        </div>
                        <div class="student-detail-row">
                            <span class="detail-label">পিতার নাম :</span>
                            <span class="detail-value" id="stdFather">—</span>
                        </div>
                        <div class="student-detail-row">
                            <span class="detail-label">মোবাইল :</span>
                            <span class="detail-value" id="stdMobile">—</span>
                        </div>
                        <div class="student-detail-row">
                            <span class="detail-label">ক্লাস :</span>
                            <span class="detail-value" id="stdClass">—</span>
                        </div>
                        <div class="student-detail-row">
                            <span class="detail-label">আইডি :</span>
                            <span class="detail-value" id="stdId">—</span>
                        </div>
                        <span class="badge-active" id="stdStatus" style="display:none;">সক্রিয় শিক্ষার্থী</span>
                    </div>
                </div>

                <div class="fee-summary">
                    <div>
                        <div class="fee-col-label">মোট :</div>
                        <div class="fee-col-value fee-total" id="feeTotal">0</div>
                    </div>
                    <div>
                        <div class="fee-col-label">কর্তন :</div>
                        <div class="fee-col-value fee-discount" id="feeDiscount">0</div>
                    </div>
                    <div>
                        <div class="fee-col-label">জমা :</div>
                        <div class="fee-col-value fee-paid" id="feePaid">0</div>
                    </div>
                    <div>
                        <div class="fee-col-label">বকেয়া :</div>
                        <div class="fee-col-value fee-due" id="feeDue">0</div>
                    </div>
                    <div>
                        <div class="fee-col-label">রিসিড নং :</div>
                        <div class="fee-col-value fee-receipt" id="feeReceipt">—</div>
                    </div>
                </div>

                <hr style="border:none;border-top:1.5px solid #e5e7eb;margin:14px 0 12px 0;">

                <div class="remarks-row">
                    <div>
                        <div class="remarks-label">কথা :</div>
                        <div class="remarks-box" id="stdRemarks">—</div>
                    </div>
                    <div>
                        <div class="remarks-label">মন্তব্য :</div>
                        <textarea class="remarks-textarea" id="paymentNote" rows="2" placeholder="মন্তব্য লিখুন..."></textarea>
                    </div>
                </div>
            </div>

        </div>{{-- end middle-panel --}}

        {{-- RIGHT PANEL --}}
        <div class="right-panel">

            {{-- ভর্তি / মাসিক / অনন্য ফি type --}}
            <div class="payment-type-row">
                <button class="pay-type-btn active-type" id="btnAdmission" onclick="setPayType('admission', this)">
                    <i class="fas fa-check-circle"></i> ভর্তি ফি
                </button>
                <button class="pay-type-btn" id="btnMonthly" onclick="setPayType('monthly', this)">
                    <i class="fas fa-calendar"></i> মাসিক ফি
                </button>
                <button class="pay-type-btn" id="btnOther" onclick="setPayType('other', this)">
                    <i class="fas fa-receipt"></i> অনন্য
                </button>
            </div>

            {{-- Admission summary box --}}
            <div class="admission-summary-box" id="admissionSummaryBox">
                <div class="adm-sum-title"><i class="fas fa-check-circle" style="margin-right:5px;"></i> ভর্তি ফি বিবরণ</div>
                <div id="admissionSummaryRows"></div>
                <button class="adm-sum-edit-btn" onclick="openAdmissionPopup()">
                    <i class="fas fa-edit"></i> পরিবর্তন করুন
                </button>
            </div>

            {{-- ✅ অনন্য ফি summary box — ভর্তি ফি বক্সের মতোই, popup থেকে confirm হওয়ার পর দেখাবে --}}
            <div class="other-summary-box" id="otherSummaryBox">
                <div class="other-sum-title"><i class="fas fa-receipt" style="margin-right:5px;"></i> অনন্য ফি বিবরণ</div>
                <div id="otherSummaryRows"></div>
                <button class="other-sum-edit-btn" onclick="openOtherPopup()">
                    <i class="fas fa-edit"></i> পরিবর্তন করুন
                </button>
            </div>

            {{-- Payment Method label --}}
            <div class="pay-method-label">পেমেন্ট মেথড</div>

            {{-- ক্যাশ / মোবাইল ব্যাংকিং / ব্যাংক — pay-card style, transaction form এর মতো --}}
            <div class="payment-grid" id="paymentTypeGrid">
                <div class="pay-card active" onclick="selectMethodType(this, 'cash')">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>ক্যাশ</span>
                </div>
                <div class="pay-card" onclick="selectMethodType(this, 'mobile')">
                    <i class="fas fa-mobile-alt"></i>
                    <span>মোবাইল ব্যাংকিং</span>
                </div>
                <div class="pay-card" onclick="selectMethodType(this, 'bank')">
                    <i class="fas fa-university"></i>
                    <span>ব্যাংক</span>
                </div>
            </div>

            <div class="pay-account-section" id="payAccountSection">
                <div class="pay-account-label" id="payAccountLabel">একাউন্ট সিলেক্ট করুন:</div>
                <select class="cashier-select" id="paymentMethodSelect">
                    <option value="">সিলেক্ট করুন</option>
                </select>
            </div>

            <div class="selected-account-bar" id="selectedAccountBar">
                <i class="fas fa-check-circle"></i>
                <span id="selectedAccountText"></span>
            </div>

            <div class="add-method-btn-row">
                <button type="button" class="add-method-btn" onclick="openPmModal()">
                    <i class="fas fa-plus"></i> মেথড যোগ
                </button>
            </div>

            <div class="cashier-label">কাশিয়ার সিলেক্ট করুন :</div>
            <div class="cashier-row">
                <select class="cashier-select" id="cashierSelect">
                    <option value="">সিলেক্ট করুন</option>
                    @isset($cashiers)
                    @foreach($cashiers as $cashier)
                        <option value="{{ $cashier->id }}">{{ $cashier->name }}</option>
                    @endforeach
                    @endisset
                </select>
            </div>

            <div class="btn-row">
                <button class="btn-reset" onclick="resetForm()"><i class="fas fa-undo"></i> Reset</button>
                <button class="btn-save" id="btnSave" onclick="savePayment()">
                    <i class="fas fa-save"></i> Save Payment
                </button>
            </div>
        </div>

        {{-- মাসের তালিকা --}}
        <div class="month-list-section" id="monthListSection">
            <hr class="month-section-divider">
            <div class="month-list-title">
                <i class="fas fa-calendar-alt" style="color:#2563eb;margin-right:6px;"></i>
                মাসের তালিকা (ফি গ্রহণের জন্য ক্লিক করুন)
            </div>

            {{-- Horizontal scroll chips --}}
            <div class="month-scroll-wrap" id="monthScrollWrap"></div>

            {{-- Added months table --}}
            <div class="added-months-section" id="addedMonthsSection">
                <div class="added-months-label">
                    <span><i class="fas fa-list" style="margin-right:5px;color:#2563eb;"></i> এড করা ফি বিস্তারিত</span>
                    <button class="btn-clear-all" onclick="clearAllMonths()">সব মুছে ফেলুন</button>
                </div>
                <table class="added-months-table" id="addedMonthsTable">
                    <thead>
                        <tr>
                            <th>ক্রমিক</th>
                            <th>ফি-এর নাম</th>
                            <th>বিবরণ</th>
                            <th>নির্ধারিত ফি</th>
                            <th>কর্তন</th>
                            <th>পূর্ব জমা</th>
                            <th>জমা</th>
                            <th>বাকি</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="addedMonthsTbody"></tbody>
                    <tfoot id="addedMonthsTfoot"></tfoot>
                </table>
            </div>
        </div>

    </div>{{-- end top-card --}}

    {{-- আজকের পেমেন্ট তালিকা --}}
    <div class="table-card">
        <div class="table-header">
            <div class="table-title"><i class="fas fa-clock"></i> আজকের পেমেন্ট তালিকা</div>
            <div class="summary-badges">
                <div class="summary-badge" id="summaryTotalBadge">আজকের মোট সংগ্রহ: <span id="summaryTotal">৳ {{ number_format($todayTotal ?? 0) }}</span></div>
                <div class="summary-badge" id="summaryMyBadge">আমার সংগ্রহ: <span id="summaryMy">৳ {{ number_format($myTotal ?? 0) }}</span></div>
            </div>
            <div class="table-search-row">
                <input type="text" class="table-search-input" placeholder="🔍 ID" id="searchById" oninput="liveSearch()">
                <input type="text" class="table-search-input" placeholder="🔍 ভাউচার" id="searchByVoucher" oninput="liveSearch()">
            </div>
        </div>
        <table class="data-table" id="paymentTable">
            <thead>
            <tr>
                <th>আকশন</th>
                <th>আইডি</th>
                <th>শিক্ষার্থীর নাম</th>
                <th>পরিমাণ</th>
                <th>ছাড়</th>
                <th>মাধ্যম</th>
                <th>গ্রহণকারী</th>
                <th>ভাউচার</th>
                <th>তারিখ ও সময়</th>
            </tr>
            </thead>
            <tbody id="paymentTableBody">
                @isset($payments)
                @foreach($payments as $payment)
                <tr>
<td><button class="btn-print" onclick="printReceipt({{ $payment->id }})" title="প্রিন্ট"><i class="fas fa-print"></i></button></td>
                    <td>{{ $payment->student_id }}</td>
                    <td class="td-name">{{ $payment->student_name }}</td>
                    <td class="td-amount">৳ {{ number_format($payment->amount) }}</td>
                    <td class="td-discount">৳ {{ number_format($payment->discount ?? 0) }}</td>
                    <td><span class="badge-method badge-{{ strtolower($payment->method) }}">{{ $payment->method }}</span></td>
                    <td>{{ $payment->cashier_name }}</td>
                    <td style="color:#6b7280;font-size:12px;">{{ $payment->voucher_no ?? '—' }}</td>
                    
                                        <td>
                        <div class="td-date">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</div>
                        <div class="td-time">{{ \Carbon\Carbon::parse($payment->created_at)->format('h:i A') }}</div>
                    </td>
                </tr>
                @endforeach
                @endisset
            </tbody>
        </table>
    </div>

</div>

{{-- ===== STATEMENT MODAL ===== --}}
<div class="modal-overlay" id="statementModal">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-header-left">
                <div class="modal-icon"><i class="fas fa-history"></i></div>
                <div>
                    <div class="modal-title">পেমেন্ট স্টেটমেন্ট</div>
                    <div class="modal-subtitle" id="modalStudentTitle">—</div>
                </div>
            </div>
            <div style="display:flex;gap:10px;align-items:center;">
                <button class="btn-print-all" onclick="printStatement()"><i class="fas fa-print"></i> প্রিন্ট</button>
                <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <div class="modal-body">
            <table class="statement-table" id="statementTable">
                <thead>
                    <tr>
                        <th>তারিখ ও সময়</th><th>মাস</th><th>পরিমাণ</th>
                        <th>মেথড</th><th>কাশিয়ার</th><th>ইনভয়েস</th>
                    </tr>
                </thead>
                <tbody id="statementBody">
                    <tr><td colspan="6" class="no-data"><i class="fas fa-spinner fa-spin"></i> লোড হচ্ছে...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ===== MONTH FEE ACCEPT POPUP ===== --}}
<div class="month-popup-overlay" id="monthFeePopup">
    <div class="month-popup-box">
        <div class="month-popup-header">
            <div class="month-popup-title">Student Month Fee Accept Form</div>
            <button class="month-popup-close" onclick="closeMonthFeePopup()"><i class="fas fa-times"></i></button>
        </div>
        <div class="month-popup-body">
            <div class="month-popup-grid">
                <div>
                    <div class="mp-field-label">শিক্ষার্থীর কোড :</div>
                    <input type="text" class="mp-field-value" id="mpStudentId" readonly>
                </div>
                <div>
                    <div class="mp-field-label">মাসের নাম :</div>
                    <input type="text" class="mp-field-value" id="mpMonthName" readonly>
                </div>
                <div>
                    <div class="mp-field-label">নির্ধারিত ফি :</div>
                    <input type="number" class="mp-field-value" id="mpSetFee" readonly>
                </div>
                <div>
                    <div class="mp-field-label">পূর্ব জমা :</div>
                    <input type="number" class="mp-field-value" id="mpPrevPaid" readonly>
                </div>
                <div>
                    <div class="mp-field-label">কর্তন (Discount) :</div>
                    <input type="number" class="mp-field-value" id="mpDiscount" value="0" min="0" oninput="updateMonthPopupCalc('discount')">                
            </div>
                <div>
                    <div class="mp-field-label">জমা (Deposit) :</div>
                    <input type="number" class="mp-field-value" id="mpDeposit" value="0" min="0" oninput="updateMonthPopupCalc('deposit')">
                </div>
            </div>
            <table class="mp-fee-table">
                <thead>
                    <tr><th>ফি-এর নাম</th><th>নির্ধারিত</th><th>জমা</th><th>বকেয়া</th></tr>
                </thead>
                <tbody id="mpFeeTableBody"></tbody>
            </table>
        </div>
        <div class="month-popup-footer">
            <button class="mp-btn-cancel" onclick="closeMonthFeePopup()">Cancel</button>
            <button class="mp-btn-add" id="mpBtnAddToList" onclick="addMonthToList()">Add to List</button>
        </div>
    </div>
</div>

{{-- ===== ADMISSION FEE POPUP ===== --}}
<div class="admission-popup-overlay" id="admissionFeePopup">
    <div class="admission-popup-box">
        <div class="adm-popup-header">
            <div class="adm-popup-title"><i class="fas fa-user-graduate" style="margin-right:8px;color:#2563eb;"></i> ভর্তি ফি গ্রহণ ফর্ম</div>
            <button class="adm-popup-close" onclick="closeAdmissionPopup()"><i class="fas fa-times"></i></button>
        </div>
        <div class="adm-popup-body">
            <div class="adm-student-strip">
                <div class="adm-student-avatar"><i class="fas fa-user-graduate"></i></div>
                <div>
                    <div class="adm-student-name" id="admPopupStudentName">—</div>
                    <div class="adm-student-sub" id="admPopupStudentSub">—</div>
                </div>
            </div>

            <div class="adm-fee-grid">
                <div>
                    <div class="adm-field-label">মোট নির্ধারিত ফি :</div>
                    <input type="text" class="adm-field-value" id="admTotalFee" readonly>
                </div>
                <div>
                    <div class="adm-field-label">পূর্বে জমা :</div>
                    <input type="text" class="adm-field-value" id="admPrevPaid" readonly>
                </div>
            </div>

            <div class="adm-fee-items">
                <div class="adm-fee-items-header">
                    <span>ফি-এর নাম</span>
                    <span style="text-align:center;">নির্ধারিত</span>
                    <span style="text-align:center;">কর্তন</span>
                    <span style="text-align:center;">জমা</span>
                </div>
                <div id="admFeeItemsBody"></div>
            </div>

            <div class="adm-total-strip">
                <span class="adm-total-label">মোট জমার পরিমাণ :</span>
                <span class="adm-total-value" id="admTotalDeposit">৳ 0</span>
            </div>
        </div>
        <div class="adm-popup-footer">
            <button class="adm-btn-cancel" onclick="closeAdmissionPopup()">বাতিল</button>
            <button class="adm-btn-save" id="admBtnSave" onclick="saveAdmissionFee()">
                <i class="fas fa-check"></i> ভর্তি ফি নিশ্চিত করুন
            </button>
        </div>
    </div>
</div>

{{-- ===== ✅ OTHER (অনন্য) FEE POPUP — ভর্তি ফি পপআপের মতোই গঠন ===== --}}
<div class="admission-popup-overlay" id="otherFeePopup">
    <div class="admission-popup-box">
        <div class="adm-popup-header">
            <div class="adm-popup-title"><i class="fas fa-receipt" style="margin-right:8px;color:#4338ca;"></i> অনন্য ফি গ্রহণ ফর্ম</div>
            <button class="adm-popup-close" onclick="closeOtherPopup()"><i class="fas fa-times"></i></button>
        </div>
        <div class="adm-popup-body">
            <div class="adm-student-strip">
                <div class="adm-student-avatar"><i class="fas fa-user-graduate"></i></div>
                <div>
                    <div class="adm-student-name" id="otherPopupStudentName">—</div>
                    <div class="adm-student-sub" id="otherPopupStudentSub">—</div>
                </div>
            </div>

            <div class="adm-fee-grid">
                <div>
                    <div class="adm-field-label">মোট নির্ধারিত ফি :</div>
                    <input type="text" class="adm-field-value" id="otherTotalFee" readonly>
                </div>
                <div>
                    <div class="adm-field-label">পূর্বে জমা :</div>
                    <input type="text" class="adm-field-value" id="otherPrevPaid" readonly>
                </div>
            </div>

            <div class="adm-fee-items">
                <div class="adm-fee-items-header">
                    <span>ফি-এর নাম</span>
                    <span style="text-align:center;">নির্ধারিত</span>
                    <span style="text-align:center;">কর্তন</span>
                    <span style="text-align:center;">জমা</span>
                </div>
                <div id="otherFeeItemsBody"></div>
            </div>

            <div class="adm-total-strip">
                <span class="adm-total-label">মোট জমার পরিমাণ :</span>
                <span class="adm-total-value" id="otherTotalDeposit">৳ 0</span>
            </div>
        </div>
        <div class="adm-popup-footer">
            <button class="adm-btn-cancel" onclick="closeOtherPopup()">বাতিল</button>
            <button class="adm-btn-save" id="otherBtnSave" onclick="saveOtherFee()">
                <i class="fas fa-check"></i> অনন্য ফি নিশ্চিত করুন
            </button>
        </div>
    </div>
</div>


{{-- ADD CASHIER POPUP --}}
<div class="popup-overlay" id="cashierPopup">
    <div class="popup-box">
        <div class="popup-title">👤 কাশিয়ার যোগ করুন</div>
        <label class="popup-label">কাশিয়ারের নাম</label>
        <input type="text" class="popup-input" id="newCashierName" placeholder="কাশিয়ারের নাম লিখুন..."
               oninput="document.getElementById('btnSaveCashier').disabled = !this.value.trim()"
               onkeydown="if(event.key==='Enter') saveCashier()">
        <div class="popup-btn-row">
            <button class="popup-btn-cancel" onclick="closeCashierPopup()">বাতিল</button>
            <button class="popup-btn-save" id="btnSaveCashier" onclick="saveCashier()" disabled>✅ সেভ করুন</button>
        </div>
    </div>
</div>

{{-- ADD PAYMENT METHOD (ব্যাংক তথ্য) POPUP — আয়-ব্যয় ফরমের মতোই --}}
<div class="popup-overlay" id="pmPopupOverlay">
    <div class="popup-box" style="max-width:400px;">
        <div class="popup-title">🏦 পেমেন্ট মেথড যোগ করুন</div>

        <label class="popup-label">একাউন্ট ধরন</label>
        <select class="popup-input" id="pmType">
            <option value="cash">ক্যাশ</option>
            <option value="mobile">মোবাইল ব্যাংকিং</option>
            <option value="bank">ব্যাংক</option>
        </select>

        <label class="popup-label">একাউন্টের নাম</label>
        <input type="text" class="popup-input" id="pmName" placeholder="নাম লিখুন">

        <label class="popup-label">একাউন্ট নাম্বার</label>
        <input type="text" class="popup-input" id="pmNumber" placeholder="একাউন্ট নাম্বার (ঐচ্ছিক)">

        <label class="popup-label">মোবাইল ১</label>
        <input type="text" class="popup-input" id="pmMobile1" placeholder="মোবাইল নং (ঐচ্ছিক)">

        <div class="popup-btn-row">
            <button class="popup-btn-cancel" onclick="closePmModal()">বাতিল</button>
            <button class="popup-btn-save" id="btnSavePm" onclick="pmSave()">✅ সেভ করুন</button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div class="toast" id="toast">
    <i class="fas fa-check-circle"></i>
    <span id="toastMsg">সফল হয়েছে</span>
</div>

<script>
// ================================================================
// STATE
// ================================================================
let currentPayType    = 'admission';
let currentMethodType = 'cash';
let selectedAccountId = null;
let currentStudentId  = null;
let currentStudentData = null;
let currentMonthList  = [];
let selectedMonths    = [];

// Admission fee data
let admissionFeeItems  = [];
let admissionConfirmed = false;

// ✅ 'অনন্য' (Others) fee data — admission-এর মতোই popup ব্যবহার করে
let otherFeeItems  = [];   // popup-এ এডিটযোগ্য: {name, fee, prev_paid, is_paid, discount, deposit}
let otherConfirmed = false;

let currentPopupMonth = null;

// Payment accounts (loaded from server) — আয়-ব্যয় ফরমের মতো same endpoint থেকে
let cashAccounts = [];
let mobileAccounts = [];
let bankAccounts   = [];

const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// ================================================================
// INIT — আয়-ব্যয় ফরমের /dashboard/payment-method/list এর মতো same endpoint
// ================================================================
function loadPaymentMethods() {

    fetch('/dashboard/fee-collection/payment-methods', {
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {

        if (data.success) {

            mobileAccounts = data.mobile ?? [];
            bankAccounts   = data.bank ?? [];
            cashAccounts   = data.cash ?? [];

            // বর্তমানে active type এর account dropdown রিফ্রেশ করো
            renderAccountOptions(currentMethodType);
        }
    })
    .catch(err => {
        console.log(err);
    });
}

// ================================================================
// PAYMENT METHOD TYPE SELECTION — pay-card style, transaction form এর মতো
// ================================================================
function selectMethodType(el, type) {

    currentMethodType = type;
    selectedAccountId = null;

    hideAccountBar();

    document.querySelectorAll('#paymentTypeGrid .pay-card')
        .forEach(card => card.classList.remove('active'));
    el.classList.add('active');

    renderAccountOptions(type);
}

function renderAccountOptions(type) {

    const label  = document.getElementById('payAccountLabel');
    const select = document.getElementById('paymentMethodSelect');

    select.innerHTML = `<option value="">সিলেক্ট করুন</option>`;

    let accounts = [];

    if (type === 'cash') {
        accounts = cashAccounts ?? [];
        label.textContent = 'ক্যাশ অ্যাকাউন্ট সিলেক্ট করুন:';
    }
    if (type === 'mobile') {
        accounts = mobileAccounts ?? [];
        label.textContent = 'মোবাইল অ্যাকাউন্ট সিলেক্ট করুন:';
    }
    if (type === 'bank') {
        accounts = bankAccounts ?? [];
        label.textContent = 'ব্যাংক অ্যাকাউন্ট সিলেক্ট করুন:';
    }

    if (!accounts.length) {
        select.innerHTML = `<option value="">কোনো অ্যাকাউন্ট পাওয়া যায়নি</option>`;
        return;
    }

    accounts.forEach(acc => {
        const option = document.createElement('option');
        option.value = acc.id;
        option.textContent = `${acc.name} (${acc.account_number ?? ''})`;
        option.dataset.name = acc.name;
        option.dataset.number = acc.account_number ?? '';
        select.appendChild(option);
    });
}

document.getElementById('paymentMethodSelect').addEventListener('change', function () {

    selectedAccountId = this.value;
    const selectedOption = this.options[this.selectedIndex];

    if (!selectedAccountId) {
        hideAccountBar();
        return;
    }

    showAccountBar(selectedOption.dataset.name, selectedOption.dataset.number);
});

function showAccountBar(name, number) {
    const bar = document.getElementById('selectedAccountBar');
    document.getElementById('selectedAccountText').textContent = `${name} — ${number}`;
    bar.classList.add('show');
}
function hideAccountBar() {
    document.getElementById('selectedAccountBar').classList.remove('show');
    document.getElementById('selectedAccountText').textContent = '';
}

// ================================================================
// ADD PAYMENT METHOD POPUP (ব্যাংক তথ্য) — আয়-ব্যয় ফরমের pmSave() এর মতো
// ================================================================
function openPmModal() {
    document.getElementById('pmType').value = currentMethodType;
    document.getElementById('pmName').value = '';
    document.getElementById('pmNumber').value = '';
    document.getElementById('pmMobile1').value = '';
    document.getElementById('pmPopupOverlay').classList.add('show');
}
function closePmModal() {
    document.getElementById('pmPopupOverlay').classList.remove('show');
}
document.getElementById('pmPopupOverlay').addEventListener('click', function (e) {
    if (e.target === this) closePmModal();
});

function pmSave() {
    const name   = document.getElementById('pmName').value.trim();
    const type   = document.getElementById('pmType').value;
    const number = document.getElementById('pmNumber').value.trim();
    const mob1   = document.getElementById('pmMobile1').value.trim();

    if (!name) { showToast('একাউন্টের নাম দিন', 'error'); return; }

    const iconMap = { cash: 'fa-money-bill-wave', mobile: 'fa-mobile-alt', bank: 'fa-university' };

    const btn = document.getElementById('btnSavePm');
    btn.disabled = true;

    fetch('/dashboard/payment-method/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            name,
            icon: iconMap[type] || 'fa-money-bill-wave',
            account_type: type,
            account_number: number,
            mobile1: mob1,
        })
    })
    .then(async r => {
        const data = await r.json();
        if (!r.ok) throw new Error(data.message || 'Save failed');
        return data;
    })
    .then(data => {
        if (data.success || data.status || data.id) {
            showToast(name + ' সেভ হয়েছে', 'success');
            closePmModal();
            loadPaymentMethods();
        } else {
            showToast(data.message || 'সেভ হয়নি', 'error');
        }
    })
    .catch(err => {
        console.log(err);
        showToast('সেভ হয়নি', 'error');
    })
    .finally(() => { btn.disabled = false; });
}

// ================================================================
// ✅ FEE SUMMARY — student search korar shathe shathe sob 0 dekhabe.
// Jokhon monthly/admission/other theke kichu add/confirm hobe, tokhoni
// shei session-er deposit/discount jog kore box update hobe.
// ================================================================
function flashFeeBoxes() {
    ['feeTotal','feeDiscount','feePaid','feeDue'].forEach(id => {
        const el = document.getElementById(id);
        el.classList.add('flash');
        setTimeout(() => el.classList.remove('flash'), 350);
    });
}

function updateFeeSummary() {
    if (!currentStudentData && !currentStudentId) return;

    // মাসিক থেকে
    const monthlyDeposit  = selectedMonths.reduce((s, m) => s + m.deposit,  0);
    const monthlyDiscount = selectedMonths.reduce((s, m) => s + m.discount, 0);

    // ভর্তি ফি থেকে (confirm করা হলে)
    let admDeposit = 0, admDiscount = 0;
    if (admissionConfirmed) {
        admDeposit  = admissionFeeItems.reduce((s, i) => s + (Number(i.deposit)  || 0), 0);
        admDiscount = admissionFeeItems.reduce((s, i) => s + (Number(i.discount) || 0), 0);
    }

    // অনন্য ফি থেকে (confirm করা হলে)
    let otherDeposit = 0, otherDiscount = 0;
    if (otherConfirmed) {
        otherDeposit  = otherFeeItems.reduce((s, i) => s + (Number(i.deposit)  || 0), 0);
        otherDiscount = otherFeeItems.reduce((s, i) => s + (Number(i.discount) || 0), 0);
    }

    // ✅ সার্চ করার সাথে সাথে সব ০, যা যা session-এ অ্যাড হয়েছে তাই দেখাবে
    const sessionTotal    = monthlyDeposit + admDeposit + otherDeposit
                           + monthlyDiscount + admDiscount + otherDiscount;
    const sessionDeposit  = monthlyDeposit + admDeposit + otherDeposit;
    const sessionDiscount = monthlyDiscount + admDiscount + otherDiscount;
    const sessionDue      = 0; // যা অ্যাড করা হয়েছে তার বিপরীতে deposit+discount = fee, তাই due শূন্য থাকবে এই সেশনে

    document.getElementById('feeTotal').textContent    = sessionTotal.toLocaleString();
    document.getElementById('feePaid').textContent     = sessionDeposit.toLocaleString();
    document.getElementById('feeDiscount').textContent = sessionDiscount.toLocaleString();
    document.getElementById('feeDue').textContent      = sessionDue.toLocaleString();

    flashFeeBoxes();
}

function numberToBanglaWords(amount) {
    const ones = ['', 'এক', 'দুই', 'তিন', 'চার', 'পাঁচ', 'ছয়', 'সাত', 'আট', 'নয়',
                  'দশ', 'এগারো', 'বারো', 'তেরো', 'চৌদ্দ', 'পনেরো', 'ষোলো', 'সতেরো', 'আঠারো', 'উনিশ'];
    const tens = ['', '', 'বিশ', 'ত্রিশ', 'চল্লিশ', 'পঞ্চাশ', 'ষাট', 'সত্তর', 'আশি', 'নব্বই'];

    if (amount === 0) return 'শূন্য টাকা';

    function convertBelow100(n) {
        if (n < 20) return ones[n];
        const ten = tens[Math.floor(n / 10)];
        const one = ones[n % 10];
        return one ? ten + ' ' + one : ten;
    }

    function convertBelow1000(n) {
        if (n < 100) return convertBelow100(n);
        const hundred = ones[Math.floor(n / 100)] + ' শত';
        const rest = n % 100;
        return rest ? hundred + ' ' + convertBelow100(rest) : hundred;
    }

    function convertBelow100000(n) {
        if (n < 1000) return convertBelow1000(n);
        const thousand = convertBelow100(Math.floor(n / 1000)) + ' হাজার';
        const rest = n % 1000;
        return rest ? thousand + ' ' + convertBelow1000(rest) : thousand;
    }

    function convertBelow10000000(n) {
        if (n < 100000) return convertBelow100000(n);
        const lakh = convertBelow100(Math.floor(n / 100000)) + ' লক্ষ';
        const rest = n % 100000;
        return rest ? lakh + ' ' + convertBelow100000(rest) : lakh;
    }

    let result = '';
    if (amount >= 10000000) {
        result += ones[Math.floor(amount / 10000000)] + ' কোটি ';
        amount %= 10000000;
    }
    result += convertBelow10000000(amount);

    return result.trim() + ' টাকা';
}

function updateKothaBox() {
    const monthlyDeposit = selectedMonths.reduce((s, m) => s + m.deposit, 0);
    let otherDeposit = 0;
    if (otherConfirmed) otherDeposit = otherFeeItems.reduce((s, i) => s + (Number(i.deposit) || 0), 0);

    const totalDeposit = monthlyDeposit + otherDeposit;
    const kothaEl = document.getElementById('stdRemarks');

    if (!totalDeposit) {
        kothaEl.textContent = currentStudentData?.remarks ?? '—';
        return;
    }

    kothaEl.textContent = numberToBanglaWords(totalDeposit);
}

// ================================================================
// MONTH CHIPS
// ================================================================
function renderMonthChips() {
    const wrap = document.getElementById('monthScrollWrap');
    if (!currentMonthList.length) {
        wrap.innerHTML = '<div style="color:#9ca3af;font-size:13px;padding:8px 0;">শিক্ষার্থী সার্চ করুন</div>';
        renderAddedMonthsTable();
        updateKothaBox();
        return;
    }
    wrap.innerHTML = '';
    currentMonthList.forEach(m => {
        const chip = document.createElement('button');
        chip.type = 'button';
        const isSelected = selectedMonths.some(s => s.name === m.name);
        let cls = 'month-chip ';
        const partialPaid =
    Number(m.prev_paid) > 0 &&
    !m.is_paid;

if (m.is_paid)
    cls += 'paid';

else if (partialPaid)
    cls += 'partial';

else if (isSelected)
    cls += 'selected';

else
    cls += 'due';

        chip.className = cls;
        chip.title     = m.is_paid ? 'পরিশোধ হয়েছে' : '';
        chip.innerHTML = `<span class="mc-name">${m.name}</span><span class="mc-fee">৳ ${m.fee}</span>`;

        if (!m.is_paid) {
            chip.onclick = () => openMonthFeePopup(m);
        }
        wrap.appendChild(chip);
    });

    renderAddedMonthsTable();
}

// ================================================================
// ADDED MONTHS TABLE
// ================================================================
function renderAddedMonthsTable() {
    const section = document.getElementById('addedMonthsSection');
    const tbody   = document.getElementById('addedMonthsTbody');
    const tfoot   = document.getElementById('addedMonthsTfoot');

    if (!selectedMonths.length) {
        section.classList.remove('show');
        return;
    }
    section.classList.add('show');

    let totalFee = 0, totalDisc = 0, totalDeposit = 0;
    let rows = '';
    selectedMonths.forEach((m, idx) => {
        const due = Math.max(0, m.fee - m.discount - m.deposit);
        totalFee     += m.fee;
        totalDisc    += m.discount;
        totalDeposit += m.deposit;
        rows += `<tr>
            <td style="color:#2563eb;font-weight:700;">${idx + 1}</td>
            <td>মাসিক বেতন</td>
            <td>${m.name} (${currentYear()})</td>
            <td>${m.fee.toLocaleString()}</td>
            <td class="amt-disc">${m.discount > 0 ? m.discount.toLocaleString() : '—'}</td>
            <td>${m.prev_paid > 0 ? m.prev_paid.toLocaleString() : '0'}</td>
            <td class="amt-col amt-paid">৳ ${m.deposit.toLocaleString()}</td>
            <td style="color:${due > 0 ? '#dc2626' : '#16a34a'};font-weight:700;">৳ ${due.toLocaleString()}</td>
            <td>
                <div class="btn-row-month">
                    <button class="btn-edit-month" onclick="editMonth(${idx})" title="সম্পাদনা"><i class="fas fa-pen"></i></button>
                    <button class="btn-del-month"  onclick="removeMonth(${idx})" title="মুছুন"><i class="fas fa-times"></i></button>
                </div>
            </td>
        </tr>`;
    });
    tbody.innerHTML = rows;
    tfoot.innerHTML = `<tr class="added-months-total-row">
        <td colspan="3" style="text-align:right;font-weight:700;color:#374151;">মোট জমা:</td>
        <td style="font-weight:700;">৳ ${totalFee.toLocaleString()}</td>
        <td style="color:#b45309;font-weight:700;">${totalDisc > 0 ? '৳ '+totalDisc.toLocaleString() : '—'}</td>
        <td>—</td>
        <td style="color:#16a34a;font-weight:800;">৳ ${totalDeposit.toLocaleString()}</td>
        <td>৳ 0</td>
        <td></td>
    </tr>`;
}

function currentYear() {
    const sel = document.getElementById('academicYear');
    return sel.options[sel.selectedIndex]?.text ?? '';
}

function editMonth(idx) {
    const m = selectedMonths[idx];
    const monthData = currentMonthList.find(ml => ml.name === m.name);
    if (monthData) openMonthFeePopup(monthData);
}

function removeMonth(idx) {
    selectedMonths.splice(idx, 1);
    renderMonthChips();
    updateFeeSummary();
    updateKothaBox()
}

function clearAllMonths() {
    selectedMonths = [];
    searchStudent();
    renderMonthChips();
    updateFeeSummary();
    updateKothaBox();
}

// ================================================================
// MONTH FEE POPUP
// ================================================================
function openMonthFeePopup(month) {
    currentPopupMonth = month;
    const existing = selectedMonths.find(s => s.name === month.name);
    const discount = existing ? existing.discount : 0;
    const deposit  = existing ? existing.deposit  : month.fee;

    document.getElementById('mpStudentId').value  = currentStudentId ?? '';
    document.getElementById('mpMonthName').value  = month.name;
    document.getElementById('mpSetFee').value     = month.fee;
    document.getElementById('mpPrevPaid').value   = month.prev_paid ?? 0;
    document.getElementById('mpDiscount').value   = discount;
    document.getElementById('mpDeposit').value    = deposit;

    updateMonthPopupCalc();
    document.getElementById('monthFeePopup').classList.add('show');
    setTimeout(() => document.getElementById('mpDeposit').focus(), 100);
}

function closeMonthFeePopup() {
    document.getElementById('monthFeePopup').classList.remove('show');
    currentPopupMonth = null;
}

function updateMonthPopupCalc(changedField) {
    const setFee      = Number(document.getElementById('mpSetFee').value)    || 0;
    const discountEl  = document.getElementById('mpDiscount');
    const depositEl   = document.getElementById('mpDeposit');

    let discount = Number(discountEl.value) || 0;
    let deposit  = Number(depositEl.value)  || 0;

    if (changedField === 'discount') {
        discount = Math.min(discount, setFee);
        discountEl.value = discount;
        deposit = Math.max(0, setFee - discount);
        depositEl.value = deposit;
    }

    const due = Math.max(0, setFee - discount - deposit);

    document.getElementById('mpFeeTableBody').innerHTML = `
        <tr>
            <td>মাসিক বেতন</td>
            <td>৳ ${setFee.toLocaleString()}</td>
            <td class="td-paid">৳ ${deposit.toLocaleString()}</td>
            <td class="td-due">৳ ${due.toLocaleString()}</td>
        </tr>`;

    document.getElementById('mpBtnAddToList').disabled = deposit <= 0;
}

function addMonthToList() {
    if (!currentPopupMonth) return;
    const discount = Number(document.getElementById('mpDiscount').value) || 0;
    const deposit  = Number(document.getElementById('mpDeposit').value)  || 0;

    if (deposit <= 0) { showToast('জমার পরিমাণ দিন', 'error'); return; }

    selectedMonths = selectedMonths.filter(s => s.name !== currentPopupMonth.name);
    selectedMonths.push({
        name     : currentPopupMonth.name,
        fee      : currentPopupMonth.fee,
        prev_paid: currentPopupMonth.prev_paid ?? 0,
        discount : discount,
        deposit  : deposit,
    });

    closeMonthFeePopup();
    renderMonthChips();
    updateFeeSummary();
    updateKothaBox();
    showToast(`${currentPopupMonth.name} মাস লিস্টে যোগ হয়েছে`, 'success');
}

document.getElementById('monthFeePopup').addEventListener('click', function(e) {
    if (e.target === this) closeMonthFeePopup();
});

// ================================================================
// ADMISSION FEE POPUP
// — শুধুমাত্র "ভর্তি ফি" বোতাম ক্লিক করলে বা "পরিবর্তন করুন" বোতাম থেকে খুলবে
// — student search এ auto-open হবে না
// ================================================================
function openAdmissionPopup() {
    if (!currentStudentId) {
        showToast('প্রথমে একজন শিক্ষার্থী সার্চ করুন', 'error');
        return;
    }

    const name = document.getElementById('stdName').textContent;
    const cls  = document.getElementById('stdClass').textContent;
    const sid  = document.getElementById('stdId').textContent;
    document.getElementById('admPopupStudentName').textContent = name;
    document.getElementById('admPopupStudentSub').textContent  = `ID: ${sid} | ক্লাস: ${cls}`;

if (!admissionFeeItems.length) {

    showToast(
        'ভর্তি ফি সেটিং পাওয়া যায়নি',
        'error'
    );

    return;
}

    const totalFee = admissionFeeItems.reduce((s, i) => s + i.fee, 0);
    document.getElementById('admTotalFee').value  = totalFee;
    document.getElementById('admPrevPaid').value  = 0;

    renderAdmFeeItems();
    document.getElementById('admissionFeePopup').classList.add('show');
}

function renderAdmFeeItems() {
    const body = document.getElementById('admFeeItemsBody');
    body.innerHTML = '';
    admissionFeeItems.forEach((item, idx) => {
        const row = document.createElement('div');
        row.className = 'adm-fee-item-row';
        row.innerHTML = `
            <span class="adm-fee-name">${item.name}</span>
            <span class="adm-fee-amount" style="text-align:center;">৳ ${item.fee.toLocaleString()}</span>
            <input type="number" class="adm-fee-discount-input" value="${item.discount}" min="0" max="${item.fee}"
                   onchange="updateAdmItem(${idx}, 'discount', this.value)" style="text-align:center;">
            <input type="number" class="adm-fee-deposit-input" value="${item.deposit}" min="0"
                   onchange="updateAdmItem(${idx}, 'deposit', this.value)" style="text-align:center;">
        `;
        body.appendChild(row);
    });
    updateAdmTotal();
}

function updateAdmItem(idx, field, val) {
    val = Number(val) || 0;
    const item = admissionFeeItems[idx];

    if (field === 'discount') {
        val = Math.min(val, item.fee);
        item.discount = val;
        item.deposit = Math.max(0, item.fee - val);
    } else if (field === 'deposit') {
        val = Math.min(val, item.fee - item.discount);
        item.deposit = Math.max(0, val);
    }

    renderAdmFeeItems();
}

function updateAdmTotal() {
    const total = admissionFeeItems.reduce((s, i) => s + (Number(i.deposit) || 0), 0);
    document.getElementById('admTotalDeposit').textContent = '৳ ' + total.toLocaleString();
    document.getElementById('admBtnSave').disabled = total <= 0;
}

function closeAdmissionPopup() {
    document.getElementById('admissionFeePopup').classList.remove('show');
}

function saveAdmissionFee() {
    const total = admissionFeeItems.reduce((s, i) => s + (Number(i.deposit) || 0), 0);
    if (total <= 0) { showToast('জমার পরিমাণ দিন', 'error'); return; }

    admissionConfirmed = true;
    closeAdmissionPopup();

    const summaryBox  = document.getElementById('admissionSummaryBox');
    const summaryRows = document.getElementById('admissionSummaryRows');
    let html = '';
    admissionFeeItems.forEach(item => {
        html += `<div class="adm-sum-row">
            <span>
                ৳ ${item.fee}
            </span>
            <span class="adm-sum-total">৳ ${Number(item.deposit).toLocaleString()}</span>
        </div>`;
    });
    html += `<div class="adm-sum-row" style="border-top:1px solid #86efac;margin-top:6px;padding-top:6px;">
        <span style="font-weight:700;">মোট</span>
        <span class="adm-sum-total" style="font-size:15px;">৳ ${total.toLocaleString()}</span>
    </div>`;
    summaryRows.innerHTML = html;
    summaryBox.classList.add('show');

    // ✅ ভর্তি ফি কনফার্ম হওয়ার সাথে সাথে fee summary বক্সে (মোট/কর্তন/জমা/বকেয়া) আপডেট
    updateFeeSummary();

    showToast('ভর্তি ফি নিশ্চিত হয়েছে', 'success');
}

document.getElementById('admissionFeePopup').addEventListener('click', function(e) {
    if (e.target === this) closeAdmissionPopup();
});

// ================================================================
// ✅ OTHER (অনন্য) FEE POPUP — ভর্তি ফি পপআপের মতোই কাজ করে
// ================================================================
function openOtherPopup() {
    if (!currentStudentId) {
        showToast('প্রথমে একজন শিক্ষার্থী সার্চ করুন', 'error');
        return;
    }

    const name = document.getElementById('stdName').textContent;
    const cls  = document.getElementById('stdClass').textContent;
    const sid  = document.getElementById('stdId').textContent;
    document.getElementById('otherPopupStudentName').textContent = name;
    document.getElementById('otherPopupStudentSub').textContent  = `ID: ${sid} | ক্লাস: ${cls}`;

    if (!otherFeeItems.length) {
        showToast('কোনো অনন্য ফি সেটিং পাওয়া যায়নি', 'error');
        return;
    }

    const totalFee  = otherFeeItems.reduce((s, i) => s + i.fee, 0);
    const prevPaid  = otherFeeItems.reduce((s, i) => s + (Number(i.prev_paid) || 0), 0);
    document.getElementById('otherTotalFee').value = totalFee;
    document.getElementById('otherPrevPaid').value = prevPaid;

    renderOtherFeeItems();
    document.getElementById('otherFeePopup').classList.add('show');
}

function closeOtherPopup() {
    document.getElementById('otherFeePopup').classList.remove('show');
}

function renderOtherFeeItems() {
    const body = document.getElementById('otherFeeItemsBody');
    body.innerHTML = '';
    otherFeeItems.forEach((item, idx) => {
        const row = document.createElement('div');
        row.className = 'adm-fee-item-row' + (item.is_paid ? ' row-paid' : '');
        row.innerHTML = `
            <span class="adm-fee-name">${item.name} ${item.is_paid ? '<span class="adm-fee-paid-tag">পরিশোধিত</span>' : ''}</span>
            <span class="adm-fee-amount" style="text-align:center;">৳ ${Number(item.fee).toLocaleString()}</span>
            <input type="number" class="adm-fee-discount-input" value="${item.discount}" min="0" max="${item.fee}"
                   ${item.is_paid ? 'disabled' : ''}
                   onchange="updateOtherItem(${idx}, 'discount', this.value)" style="text-align:center;">
            <input type="number" class="adm-fee-deposit-input" value="${item.deposit}" min="0"
                   ${item.is_paid ? 'disabled' : ''}
                   onchange="updateOtherItem(${idx}, 'deposit', this.value)" style="text-align:center;">
        `;
        body.appendChild(row);
    });
    updateOtherTotal();
}

function updateOtherItem(idx, field, val) {
    val = Number(val) || 0;
    const item = otherFeeItems[idx];

    if (field === 'discount') {
        val = Math.min(val, item.fee);
        item.discount = val;
        item.deposit = Math.max(0, item.fee - val);
    } else if (field === 'deposit') {
        val = Math.min(val, item.fee - item.discount);
        item.deposit = Math.max(0, val);
    }

    renderOtherFeeItems();
}

function updateOtherTotal() {
    const total = otherFeeItems.reduce((s, i) => s + (Number(i.deposit) || 0), 0);
    document.getElementById('otherTotalDeposit').textContent = '৳ ' + total.toLocaleString();
    document.getElementById('otherBtnSave').disabled = total <= 0;
}

function saveOtherFee() {
    const total = otherFeeItems.reduce((s, i) => s + (Number(i.deposit) || 0), 0);
    if (total <= 0) { showToast('জমার পরিমাণ দিন', 'error'); return; }

    otherConfirmed = true;
    closeOtherPopup();

    const summaryBox  = document.getElementById('otherSummaryBox');
    const summaryRows = document.getElementById('otherSummaryRows');
    let html = '';
    otherFeeItems.filter(i => i.deposit > 0 || i.discount > 0).forEach(item => {
        html += `<div class="other-sum-row">
            <span>${item.name} — ৳ ${item.fee}</span>
            <span class="other-sum-total">৳ ${Number(item.deposit).toLocaleString()}</span>
        </div>`;
    });
    html += `<div class="other-sum-row" style="border-top:1px solid #c7d2fe;margin-top:6px;padding-top:6px;">
        <span style="font-weight:700;">মোট</span>
        <span class="other-sum-total" style="font-size:15px;">৳ ${total.toLocaleString()}</span>
    </div>`;
    summaryRows.innerHTML = html;
    summaryBox.classList.add('show');

    updateFeeSummary();
    updateKothaBox();

    showToast('অনন্য ফি নিশ্চিত হয়েছে', 'success');
}

document.getElementById('otherFeePopup').addEventListener('click', function(e) {
    if (e.target === this) closeOtherPopup();
});

// ================================================================
// PAYMENT TYPE (ভর্তি / মাসিক / অনন্য)
// ================================================================
function setPayType(type, btn) {
    currentPayType = type;
    document.querySelectorAll('.pay-type-btn').forEach(b => b.classList.remove('active-type'));
    btn.classList.add('active-type');

    const monthSection = document.getElementById('monthListSection');
    const admissionBox = document.getElementById('admissionSummaryBox');
    const otherBox      = document.getElementById('otherSummaryBox');

    monthSection.style.display = 'none';

    if (type === 'monthly') {
        monthSection.style.display = 'block';
        admissionBox.classList.remove('show');
        otherBox.classList.remove('show');
        selectedMonths = [];
        renderMonthChips();

    } else if (type === 'other') {
        admissionBox.classList.remove('show');
        // অনন্য বক্স আগে confirm করা থাকলে দেখাবে, না হলে popup খুলবে
        if (currentStudentId) {
            openOtherPopup();
        }

    } else {
        // admission — শুধু section বন্ধ করি, popup auto-open করি না
        otherBox.classList.remove('show');
        selectedMonths = [];
        if (currentStudentId) {
            openAdmissionPopup();
        }
    }

    updateFeeSummary();
}

// ================================================================
// SEARCH STUDENT
// ================================================================
function searchStudent() {
    const id   = document.getElementById('studentIdInput').value.trim();
    const year = document.getElementById('academicYear').value;
    if (!id) return;

    fetch(`/dashboard/fee-collection/student-info?id=${encodeURIComponent(id)}&year=${year}`, {
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            showToast(data.message ?? 'শিক্ষার্থী পাওয়া যায়নি', 'error');
            return;
        }

        const s = data.student;

        currentStudentId   = s.id;
        currentStudentData = s;
        currentMonthList   = data.monthList ?? [];
        selectedMonths     = [];
        admissionFeeItems  = [];
        admissionConfirmed = false;
        otherConfirmed      = false;

        document.getElementById('admissionSummaryBox').classList.remove('show');
        document.getElementById('otherSummaryBox').classList.remove('show');

        if (data.academic_year_id) document.getElementById('academicYear').value = data.academic_year_id;
        if (data.voucher_no) {
            document.getElementById('feeReceipt').textContent = data.voucher_no;
        }

        const photoWrap = document.getElementById('studentPhotoWrap');
        photoWrap.innerHTML = s.photo
            ? `<img src="${s.photo}" class="student-photo" alt="ছবি">`
            : `<i class="fas fa-user" style="font-size:32px;color:#9ca3af;"></i>`;

        document.getElementById('stdName').textContent    = s.name;
        document.getElementById('stdFather').textContent  = s.father_name;
        document.getElementById('stdMobile').textContent  = s.mobile;
        document.getElementById('stdClass').textContent   = s.class_name;
        document.getElementById('stdId').textContent      = s.id;
        document.getElementById('stdRemarks').textContent = s.remarks ?? 'শূন্য টাকা';
        document.getElementById('stdStatus').style.display = 'inline-block';

        // ✅ সার্চ করার সাথে সাথে fee summary বক্স সব ০ দেখাবে।
        // নতুন কিছু (মাসিক/ভর্তি/অনন্য) অ্যাড না করা পর্যন্ত এই ০ অবস্থাই থাকবে।
        document.getElementById('feeTotal').textContent       = '0';
        document.getElementById('feeDiscount').textContent    = '0';
        document.getElementById('feePaid').textContent        = '0';
        document.getElementById('feeDue').textContent         = '0';

        // Admission fee items
        admissionFeeItems = (data.admission_fees ?? []).map(af => ({
            name: af.name, fee: af.fee, discount: 0, deposit: af.fee
        }));

        // ✅ Other(অনন্য) fee items — popup-এ এডিট করার জন্য state বানাচ্ছি
        otherFeeItems = (data.other_fees ?? []).map(of => ({
            name      : of.name,
            fee       : of.fee,
            prev_paid : of.prev_paid ?? 0,
            is_paid   : !!of.is_paid,
            discount  : 0,
            deposit   : of.is_paid ? 0 : of.fee,
        }));

        // বর্তমান payType অনুযায়ী relevant section দেখাও
        if (currentPayType === 'monthly') {
            document.getElementById('monthListSection').style.display = 'block';
            renderMonthChips();
        }
        // admission/other মোডে search এর পরে popup auto-open হবে না — ইউজার বাটনে ক্লিক করলে খুলবে
    })
    .catch(err => {
        console.error(err);
        showToast('সার্ভার ত্রুটি', 'error');
    });
}

document.getElementById('studentIdInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') searchStudent();
});

// ================================================================
// SAVE PAYMENT
// ================================================================
function savePayment() {
    if (!currentStudentId) { showToast('প্রথমে একজন শিক্ষার্থী সার্চ করুন', 'error'); return; }
    if (!document.getElementById('cashierSelect').value) { showToast('কাশিয়ার সিলেক্ট করুন', 'error'); return; }

    if (currentPayType === 'monthly' && selectedMonths.length === 0) {
        showToast('অন্তত একটি মাস সিলেক্ট করুন', 'error'); return;
    }
    if (currentPayType === 'admission' && !admissionConfirmed) {
        showToast('ভর্তি ফি নিশ্চিত করুন', 'error');
        openAdmissionPopup(); return;
    }
    if (currentPayType === 'other' && !otherConfirmed) {
        showToast('অনন্য ফি নিশ্চিত করুন', 'error');
        openOtherPopup(); return;
    }

    let otherItemsPayload = [];
    if (currentPayType === 'other') {
        otherItemsPayload = otherFeeItems
            .filter(it => it.deposit > 0 || it.discount > 0)
            .map(it => ({
                name: it.name, fee: it.fee, discount: it.discount, deposit: it.deposit,
            }));

        if (!otherItemsPayload.length) {
            showToast('অন্তত একটি অনন্য ফি-তে জমা/কর্তন দিন', 'error');
            return;
        }
    }

    const btn = document.getElementById('btnSave');

    let paymentMethodId = selectedAccountId || null;

    if (currentMethodType !== 'cash' && !paymentMethodId) {
        showToast('পেমেন্ট মেথড সিলেক্ট করুন', 'error');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> সংরক্ষণ হচ্ছে...';

    const payload = {
        student_id       : currentStudentId,
        collection_date  : document.getElementById('paymentDate').value,
        academic_year    : document.getElementById('academicYear').value,
        pay_type         : currentPayType,
        payment_method_id: paymentMethodId,
        cashier_id       : document.getElementById('cashierSelect').value,
        note             : document.getElementById('paymentNote').value,
        months           : currentPayType === 'monthly'   ? selectedMonths      : [],
        admission_items  : currentPayType === 'admission' ? admissionFeeItems   : [],
        other_items      : currentPayType === 'other'      ? otherItemsPayload   : [],
    };

    fetch('/dashboard/fee-collection/save-payment', {
        method : 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body   : JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Save Payment';

        if (data.success) {
            showToast(`পেমেন্ট সংরক্ষিত! ভাউচার: ${data.voucher_no}`, 'success');

            // ✅ Save হওয়ার পর session-এর hisab (selectedMonths/admission/other) রিসেট হয়ে
            // যাবে, তাই fee summary বক্স আবার ০-তে ফিরে যাবে — যেটা স্বাভাবিক, কারণ
            // ইতিমধ্যে যা যোগ হয়েছিল তা সার্ভারে সেভ হয়ে গেছে।
            if (data.paidMonths) {
                currentMonthList.forEach(m => {
                    if (data.paidMonths.includes(m.name)) m.is_paid = true;
                });
            }

            // ✅ Other fee items-এ is_paid mark করো যেগুলো এই payment-এ পরিশোধ হলো
            if (currentPayType === 'other' && data.paidMonths) {
                otherFeeItems.forEach(it => {
                    if (data.paidMonths.includes(it.name)) {
                        it.is_paid = true;
                        it.deposit = 0;
                        it.discount = 0;
                    }
                });
            }

            // ✅ আজকের মোট সংগ্রহ / আমার সংগ্রহ — savePayment() রেসপন্স থেকে সরাসরি আপডেট
            if (typeof data.todayTotal !== 'undefined') {
                document.getElementById('summaryTotal').textContent = '৳ ' + Number(data.todayTotal).toLocaleString();
                flashSummaryBadge('summaryTotalBadge');
            }
            if (typeof data.myTotal !== 'undefined') {
                document.getElementById('summaryMy').textContent = '৳ ' + Number(data.myTotal).toLocaleString();
                flashSummaryBadge('summaryMyBadge');
            }

            selectedMonths     = [];
            admissionConfirmed = false;
            otherConfirmed      = false;
            document.getElementById('admissionSummaryBox').classList.remove('show');
            document.getElementById('otherSummaryBox').classList.remove('show');
            document.getElementById('feeTotal').textContent    = '0';
            document.getElementById('feeDiscount').textContent = '0';
            document.getElementById('feePaid').textContent     = '0';
            document.getElementById('feeDue').textContent      = '0';
            renderMonthChips();
            refreshTodayTable();
        } else {
            showToast(data.message ?? 'কিছু একটা সমস্যা হয়েছে', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Save Payment';
        showToast('সার্ভার ত্রুটি', 'error');
    });
}

function flashSummaryBadge(id) {
    const el = document.getElementById(id);
    el.classList.add('flash');
    setTimeout(() => el.classList.remove('flash'), 500);
}

// ================================================================
// RESET
// ================================================================
function resetForm() {
    currentStudentId   = null;
    currentStudentData = null;
    currentMonthList   = [];
    selectedMonths     = [];
    admissionFeeItems  = [];
    admissionConfirmed = false;
    otherFeeItems       = [];
    otherConfirmed       = false;

    document.getElementById('studentIdInput').value = '';
    document.getElementById('paymentNote').value    = '';
    document.getElementById('cashierSelect').value  = '';
    document.getElementById('monthListSection').style.display = 'none';
    document.getElementById('stdStatus').style.display        = 'none';
    document.getElementById('admissionSummaryBox').classList.remove('show');
    document.getElementById('otherSummaryBox').classList.remove('show');
    document.getElementById('addedMonthsSection').classList.remove('show');

    document.getElementById('studentPhotoWrap').innerHTML =
        '<i class="fas fa-user" style="font-size:32px;color:#9ca3af;"></i>';
    ['stdName','stdFather','stdMobile','stdClass','stdId','stdRemarks'].forEach(id => {
        document.getElementById(id).textContent = '—';
    });
    ['feeTotal','feeDiscount','feePaid','feeDue'].forEach(id => {
        document.getElementById(id).textContent = '0';
    });
    document.getElementById('feeReceipt').textContent = '—';

    // Reset method buttons to cash
    const firstCard = document.querySelector('#paymentTypeGrid .pay-card');
    if (firstCard) selectMethodType(firstCard, 'cash');
}


// ================================================================
// CASHIER POPUP
// ================================================================
function openCashierPopup() {
    document.getElementById('newCashierName').value = '';
    document.getElementById('btnSaveCashier').disabled = true;
    document.getElementById('cashierPopup').classList.add('show');
    setTimeout(() => document.getElementById('newCashierName').focus(), 100);
}

function closeCashierPopup() {
    document.getElementById('cashierPopup').classList.remove('show');
}

function saveCashier() {
    const name = document.getElementById('newCashierName').value.trim();
    if (!name) return;

    const btn = document.getElementById('btnSaveCashier');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> সংরক্ষণ...';

    fetch('/dashboard/fee-collection/add-cashier', {
        method : 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body   : JSON.stringify({ name })
    })
    .then(r => r.json())
    .then(data => {
        btn.innerHTML = '✅ সেভ করুন';
        if (data.success) {
            const sel = document.getElementById('cashierSelect');
            const opt = document.createElement('option');
            opt.value       = data.cashier.id;
            opt.textContent = data.cashier.name;
            sel.appendChild(opt);
            sel.value = data.cashier.id;
            closeCashierPopup();
            showToast(`"${name}" কাশিয়ার যোগ হয়েছে`, 'success');
            refreshTodayTable();
        } else {
            btn.disabled = false;
            showToast(data.message ?? 'সমস্যা হয়েছে', 'error');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '✅ সেভ করুন';
        showToast('সার্ভার ত্রুটি', 'error');
    });
}

document.getElementById('cashierPopup').addEventListener('click', function(e) {
    if (e.target === this) closeCashierPopup();
});

// ✅ কাশিয়ার ড্রপডাউন পরিবর্তন হলে 'আমার সংগ্রহ' সাথে সাথে আপডেট হবে
document.getElementById('cashierSelect').addEventListener('change', function () {
    refreshTodayTable();
});

// ================================================================
// TAB SWITCH
// ================================================================
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    if (tab === 'fee') {
        document.getElementById('tabFee').classList.add('active');
    } else {
        document.getElementById('tabStatement').classList.add('active');
        if (currentStudentId) {
            openStatementModal(currentStudentId);
        } else {
            showToast('প্রথমে একজন শিক্ষার্থী সার্চ করুন', 'error');
            setTimeout(() => document.getElementById('tabFee').classList.add('active'), 300);
        }
    }
}

// ================================================================
// STATEMENT MODAL
// ================================================================
function openStatementModal(studentId) {
    const year = document.getElementById('academicYear').value;
    document.getElementById('statementModal').classList.add('show');
    document.getElementById('statementBody').innerHTML =
        '<tr><td colspan="6" class="no-data"><i class="fas fa-spinner fa-spin"></i> লোড হচ্ছে...</td></tr>';

    fetch(`/dashboard/fee-collection/statement?student_id=${studentId}&year=${year}`, {
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            document.getElementById('statementBody').innerHTML =
                '<tr><td colspan="6" class="no-data">তথ্য পাওয়া যায়নি</td></tr>';
            return;
        }
        document.getElementById('modalStudentTitle').textContent =
            `${data.student_name} (ID: ${data.student_id})`;
        if (!data.payments.length) {
            document.getElementById('statementBody').innerHTML =
                '<tr><td colspan="6" class="no-data">কোনো পেমেন্ট নেই</td></tr>';
            return;
        }
        let rows = '';
        data.payments.forEach(p => {
            rows += `<tr>
                <td><div class="td-date">${p.date}</div><div class="td-time">${p.time}</div></td>
                <td>${p.month}</td>
                <td class="st-amount">৳ ${Number(p.amount).toLocaleString()}</td>
                <td>${p.method}</td>
                <td>${p.cashier ?? '—'}</td>
                <td class="st-voucher">${p.voucher_no}</td>
            </tr>`;
        });
        document.getElementById('statementBody').innerHTML = rows;
    })
    .catch(() => {
        document.getElementById('statementBody').innerHTML =
            '<tr><td colspan="6" class="no-data">সার্ভার ত্রুটি</td></tr>';
    });
}

function closeModal() {
    document.getElementById('statementModal').classList.remove('show');
    document.getElementById('tabFee').classList.add('active');
    document.getElementById('tabStatement').classList.remove('active');
}
document.getElementById('statementModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

function printStatement() {
    const content = document.getElementById('statementTable').outerHTML;
    const title   = document.getElementById('modalStudentTitle').textContent;
    const w = window.open('', '_blank');
    w.document.write(`<html><head><title>স্টেটমেন্ট - ${title}</title>
        <style>body{font-family:'Hind Siliguri',sans-serif;}table{width:100%;border-collapse:collapse;}
        th,td{border:1px solid #ccc;padding:10px;text-align:left;font-size:13px;}th{background:#f3f4f6;}</style>
        </head><body><h3 style="margin-bottom:16px;">পেমেন্ট স্টেটমেন্ট — ${title}</h3>${content}</body></html>`);
    w.document.close();
    w.print();
}

// ================================================================
// TODAY TABLE REFRESH + LIVE SEARCH
// ================================================================
function refreshTodayTable() {
    const sid = document.getElementById('searchById').value.trim();
    const vou = document.getElementById('searchByVoucher').value.trim();

    // ✅ ফর্মে এখন যে cashier সিলেক্ট করা আছে, তার ভিত্তিতেই 'আমার সংগ্রহ' হিসাব হবে
    const cashierId = document.getElementById('cashierSelect').value;

    let url = '/dashboard/fee-collection/today-payments?';
    const parts = [];
    if (sid) parts.push(`search=${encodeURIComponent(sid)}`);
    else if (vou) parts.push(`search=${encodeURIComponent(vou)}`);
    if (cashierId) parts.push(`cashier_id=${encodeURIComponent(cashierId)}`);
    url += parts.join('&');

    fetch(url, { headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;
        document.getElementById('summaryTotal').textContent = '৳ ' + Number(data.todayTotal).toLocaleString();
        document.getElementById('summaryMy').textContent    = '৳ ' + Number(data.myTotal).toLocaleString();
        let rows = '';
        data.payments.forEach(p => {
            const mc = (p.method ?? 'cash').toLowerCase();
            rows += `<tr>
                <td><button class="btn-print" onclick="printReceipt(${p.id})" title="প্রিন্ট">
                    <i class="fas fa-print"></i></button></td>
                <td>${p.student_id}</td>
                <td class="td-name">${p.student_name}</td>
                <td class="td-amount">৳ ${Number(p.amount).toLocaleString()}</td>
                <td class="td-discount">৳ ${Number(p.discount ?? 0).toLocaleString()}</td>
                <td><span class="badge-method badge-${mc}">${p.method}</span></td>
                <td>${p.cashier_name ?? '—'}</td>
                <td style="color:#6b7280;font-size:12px;">${p.voucher_no ?? '—'}</td>
                <td><div class="td-date">${formatDate(p.payment_date)}</div>
                    <div class="td-time">${formatTime(p.created_at)}</div></td>
            </tr>`;
        });
        document.getElementById('paymentTableBody').innerHTML =
            rows || '<tr><td colspan="9" class="no-data">কোনো পেমেন্ট নেই</td></tr>';
    })
    .catch(err => console.error(err));
}

let searchTimer = null;
function liveSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(refreshTodayTable, 300);
}

function printReceipt(id) {
    window.open(`/dashboard/fee-collection/print-receipt/${id}`, '_blank');
}

function formatDate(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleDateString('bn-BD');
}
function formatTime(dateStr) {
    if (!dateStr) return '—';
    return new Date(dateStr).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.className = `toast show ${type}`;
    t.querySelector('i').className =
        type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    setTimeout(() => t.classList.remove('show'), 3500);
}

// ================================================================
// INIT
// ================================================================
document.addEventListener('DOMContentLoaded', () => {
    loadPaymentMethods();
    // ✅ পেজ লোড হওয়ার সাথে সাথে আজকের সংগ্রহ তথ্য রিফ্রেশ করো
    // (blade থেকে আসা initial value ব্যাকআপ হিসেবে আগে থেকেই বসানো আছে)
    refreshTodayTable();
});
</script>

@endsection