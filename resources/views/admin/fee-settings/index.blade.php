@extends('layouts.admin')
@section('title', 'ফি সেটিংস')
@section('page')

<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  .page-wrapper {
    background: #fff;
    border-radius: 6px;
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
  }
  h2.page-title {
    font-size: 16px;
    font-weight: 600;
    color: #222;
    margin-bottom: 18px;
    border-bottom: 1px solid #e8e8e8;
    padding-bottom: 10px;
  }

  .filters-row {
    display: flex;
    gap: 16px;
    align-items: flex-end;
    margin-bottom: 20px;
    flex-wrap: wrap;
  }
  .filter-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
    flex: 1;
    min-width: 200px;
  }
  .filter-group label {
    font-size: 12px;
    color: #555;
    font-weight: 500;
  }
  .filter-group label .required { color: red; }
  .input-with-btn {
    display: flex;
    align-items: center;
    border: 1px solid #ccc;
    border-radius: 4px;
    overflow: hidden;
    background: #fff;
  }
  .input-with-btn select {
    flex: 1;
    border: none;
    outline: none;
    padding: 7px 10px;
    font-size: 13px;
    color: #333;
    background: transparent;
    height: 36px;
  }
  .btn-plus {
    background: #2196F3;
    color: white;
    border: none;
    padding: 0 12px;
    cursor: pointer;
    font-size: 18px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .btn-plus:hover { background: #1976D2; }

  .fee-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 18px;
  }
  .fee-half { border-right: 1px solid #ddd; }
  .fee-half:last-child { border-right: none; }

  .fee-half-title {
    background: #f7f7f7;
    text-align: center;
    padding: 7px 0;
    font-weight: 600;
    font-size: 13px;
    border-bottom: 1px solid #ddd;
  }
  .sub-headers {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    border-bottom: 1px solid #ddd;
  }
  .sub-header-cell {
    text-align: center;
    padding: 5px 0;
    font-size: 12px;
    font-weight: 500;
    color: #444;
    border-right: 1px solid #ddd;
  }
  .sub-header-cell:last-child { border-right: none; }

  /* ছাত্রী sub-headers — ৩ কলাম + নাইট-কেয়ার */
  .sub-headers.chhatri {
    grid-template-columns: 1fr 1fr 1fr 1fr;
  }

  .new-old-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    border-bottom: 1px solid #ddd;
  }
  .new-old-cell {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border-right: 1px solid #ddd;
  }
  .new-old-cell:last-child { border-right: none; }
  .new-old-label {
    text-align: center;
    padding: 4px 0;
    font-size: 11px;
    color: #555;
    border-right: 1px solid #eee;
  }
  .new-old-label:last-child { border-right: none; }

  .input-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    border-bottom: 1px solid #eee;
    padding: 6px 0;
  }
  .input-cell {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border-right: 1px solid #eee;
    gap: 4px;
    padding: 0 4px;
  }
  .input-cell:last-child { border-right: none; }
  .input-cell input[type="text"] {
    border: 1px solid #ccc;
    border-radius: 3px;
    padding: 6px 5px;
    font-size: 12px;
    width: 100%;
    outline: none;
  }
  .input-cell input[type="text"]:focus { border-color: #2196F3; }

  .checkbox-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr 1fr;
    border-bottom: 1px solid #eee;
    padding: 5px 0;
  }
  .checkbox-cell {
    display: grid;
    grid-template-columns: 1fr 1fr;
    border-right: 1px solid #eee;
    padding: 0 4px;
  }
  .checkbox-cell:last-child { border-right: none; }
  .checkbox-cell label {
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }

  .taka-row {
    background: #fafafa;
    padding: 6px 8px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .taka-btn {
    background: #fff;
    border: 1px solid #bbb;
    border-radius: 3px;
    padding: 4px 12px;
    font-size: 12px;
    cursor: pointer;
    color: #333;
  }
  .taka-btn:hover { background: #f0f0f0; }
  .taka-checkbox-label {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: #555;
    cursor: pointer;
  }

  .action-row {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 10px;
  }
  .btn-reset {
    background: #f44336; color: white;
    border: none; border-radius: 4px;
    padding: 7px 20px; font-size: 13px; cursor: pointer;
  }
  .btn-reset:hover { background: #d32f2f; }
  .btn-save {
    background: #4CAF50; color: white;
    border: none; border-radius: 4px;
    padding: 7px 20px; font-size: 13px; cursor: pointer;
  }
  .btn-save:hover { background: #388E3C; }

  .taka-modal-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.4);
    z-index: 9999;
    align-items: center;
    justify-content: center;
  }
  .taka-modal-overlay.active { display: flex; }
  .taka-modal-box {
    background: #fff;
    border-radius: 8px;
    padding: 22px 26px;
    width: 320px;
    max-width: 95vw;
    box-shadow: 0 6px 24px rgba(0,0,0,0.15);
  }
  .taka-modal-title {
    font-size: 14px; font-weight: 600;
    color: #333; margin-bottom: 12px;
  }
  .taka-modal-input {
    width: 100%; height: 38px;
    border: 1px solid #ccc; border-radius: 4px;
    padding: 0 12px; font-size: 14px; outline: none;
    margin-bottom: 14px; box-sizing: border-box;
  }
  .taka-modal-input:focus { border-color: #2196F3; }
  .taka-modal-actions { display: flex; justify-content: flex-end; gap: 8px; }
  .btn-taka-cancel {
    background: #e5e7eb; color: #374151;
    border: none; padding: 8px 18px;
    border-radius: 6px; font-size: 13px; cursor: pointer;
  }
  .btn-taka-ok {
    background: #2563eb; color: #fff;
    border: none; padding: 8px 18px;
    border-radius: 6px; font-size: 13px; cursor: pointer;
  }
  .btn-taka-ok:hover { background: #1d4ed8; }

  .loading { opacity: 0.6; pointer-events: none; }

  /* =========================
     SAVED TABLE
  ========================= */
  .saved-table-wrapper {
    margin-top: 25px;
    background: #fff;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  }
  .saved-table-header {
    padding: 14px 18px;
    border-bottom: 1px solid #eee;
    background: #fafafa;
  }
  .saved-table-header h4 {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
    color: #222;
  }
  .custom-fee-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
  }
  .custom-fee-table thead { background: #f3f4f6; }
  .custom-fee-table th {
    padding: 12px 10px;
    border-bottom: 1px solid #ddd;
    text-align: center;
    color: #333;
    font-weight: 600;
  }
  .custom-fee-table td {
    padding: 12px 10px;
    border-bottom: 1px solid #eee;
    text-align: center;
    color: #444;
    vertical-align: middle;
  }
  .custom-fee-table tbody tr:hover { background: #fafafa; }
  .empty-data {
    padding: 30px !important;
    color: #888;
    font-size: 14px;
  }

  /* =========================
     ACTION BUTTONS
  ========================= */
  .action-buttons {
    display: flex;
    gap: 6px;
    justify-content: center;
  }
  .btn-action {
    border: none;
    padding: 6px 12px;
    border-radius: 5px;
    font-size: 12px;
    cursor: pointer;
    transition: .2s;
    font-weight: 500;
  }
  .btn-edit { background: #2563eb; color: #fff; }
  .btn-edit:hover { background: #1d4ed8; }
  .btn-delete { background: #ef4444; color: #fff; }
  .btn-delete:hover { background: #dc2626; }
</style>

{{-- Taka Modal --}}
<div class="taka-modal-overlay" id="takaModal">
  <div class="taka-modal-box">
    <div class="taka-modal-title" id="takaModalTitle">টাকার পরিমাণ লিখুন</div>
    <input type="number" class="taka-modal-input" id="takaModalInput" placeholder="টাকা লিখুন..." min="0" step="0.01"/>
    <div class="taka-modal-actions">
      <button class="btn-taka-cancel" onclick="closeTakaModal()">বাতিল</button>
      <button class="btn-taka-ok" onclick="applyTaka()">ঠিক আছে</button>
    </div>
  </div>
</div>

<div class="page-wrapper">
  <h2 class="page-title">ফি সেটিংস</h2>

  <div class="filters-row">
    <div class="filter-group">
      <label>শিক্ষাবর্ষ<span class="required">*</span> :</label>
      <div class="input-with-btn">
        <select id="academic-year-select" onchange="loadFeeData()">
          <option value="">নির্বাচন করুন</option>
          @foreach($academicYears as $year)
            <option value="{{ $year->id }}">{{ $year->name_bn ?? $year->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="filter-group">
      <label>শ্রেণি:</label>
      <div class="input-with-btn">
        <select id="class-select" onchange="loadFeeData()">
          <option value="">নির্বাচন করুন</option>
          @foreach($classes as $cls)
            <option value="{{ $cls->id }}">{{ $cls->name_bn ?? $cls->name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="filter-group">
      <label>ফি এর নাম<span class="required">*</span> :</label>
      <div class="input-with-btn">
        <select id="fee-group-select" onchange="loadFeeData()">
            <option value="">নির্বাচন করুন</option>
            @foreach($feeGroups as $group)
                <option value="{{ $group->id }}">
                    {{ $group->subLedger?->name ?? $group->name ?? '—' }}
                </option>
            @endforeach
        </select>
      </div>
    </div>
  </div>

  <div class="fee-section">

    {{-- ছাত্র (৪ কলাম: আবাসিক, অনাবাসিক, ডে-কেয়ার, নাইট-কেয়ার) --}}
    <div class="fee-half">
      <div class="fee-half-title">ছাত্র</div>

      <div class="sub-headers">
        <div class="sub-header-cell">আবাসিক</div>
        <div class="sub-header-cell">অনাবাসিক</div>
        <div class="sub-header-cell">ডে-কেয়ার</div>
        <div class="sub-header-cell">নাইট-কেয়ার</div>
      </div>

      <div class="new-old-row">
        <div class="new-old-cell">
          <div class="new-old-label">নতুন</div>
          <div class="new-old-label">পুরাতন</div>
        </div>
        <div class="new-old-cell">
          <div class="new-old-label">নতুন</div>
          <div class="new-old-label">পুরাতন</div>
        </div>
        <div class="new-old-cell">
          <div class="new-old-label">নতুন</div>
          <div class="new-old-label">পুরাতন</div>
        </div>
        <div class="new-old-cell">
          <div class="new-old-label">নতুন</div>
          <div class="new-old-label">পুরাতন</div>
        </div>
      </div>

      <div class="input-row">
        <div class="input-cell">
          <input type="text" id="l-ab-new" placeholder="০.০০"/>
          <input type="text" id="l-ab-old" placeholder="০.০০"/>
        </div>
        <div class="input-cell">
          <input type="text" id="l-an-new" placeholder="০.০০"/>
          <input type="text" id="l-an-old" placeholder="০.০০"/>
        </div>
        <div class="input-cell">
          <input type="text" id="l-dk-new" placeholder="০.০০"/>
          <input type="text" id="l-dk-old" placeholder="০.০০"/>
        </div>
        <div class="input-cell">
          <input type="text" id="l-nk-new" placeholder="০.০০"/>
          <input type="text" id="l-nk-old" placeholder="০.০০"/>
        </div>
      </div>

      <div class="checkbox-row">
        <div class="checkbox-cell">
          <label><input type="checkbox" id="cb-l-ab-new"/></label>
          <label><input type="checkbox" id="cb-l-ab-old"/></label>
        </div>
        <div class="checkbox-cell">
          <label><input type="checkbox" id="cb-l-an-new"/></label>
          <label><input type="checkbox" id="cb-l-an-old"/></label>
        </div>
        <div class="checkbox-cell">
          <label><input type="checkbox" id="cb-l-dk-new"/></label>
          <label><input type="checkbox" id="cb-l-dk-old"/></label>
        </div>
        <div class="checkbox-cell">
          <label><input type="checkbox" id="cb-l-nk-new"/></label>
          <label><input type="checkbox" id="cb-l-nk-old"/></label>
        </div>
      </div>

      <div class="taka-row">
        <button class="taka-btn" onclick="openTakaModal('left')">টাকা লিখুন (ছাত্র)</button>
        <label class="taka-checkbox-label">
          <input type="checkbox" id="cb-taka-l"/> সব চেক করুন
        </label>
      </div>
    </div>

    {{-- ছাত্রী (৪ কলাম: আবাসিক, অনাবাসিক, ডে-কেয়ার, নাইট-কেয়ার) --}}
    <div class="fee-half">
      <div class="fee-half-title">ছাত্রী</div>

      <div class="sub-headers chhatri">
        <div class="sub-header-cell">আবাসিক</div>
        <div class="sub-header-cell">অনাবাসিক</div>
        <div class="sub-header-cell">ডে-কেয়ার</div>
        <div class="sub-header-cell">নাইট-কেয়ার</div>
      </div>

      <div class="new-old-row">
        <div class="new-old-cell">
          <div class="new-old-label">নতুন</div>
          <div class="new-old-label">পুরাতন</div>
        </div>
        <div class="new-old-cell">
          <div class="new-old-label">নতুন</div>
          <div class="new-old-label">পুরাতন</div>
        </div>
        <div class="new-old-cell">
          <div class="new-old-label">নতুন</div>
          <div class="new-old-label">পুরাতন</div>
        </div>
        <div class="new-old-cell">
          <div class="new-old-label">নতুন</div>
          <div class="new-old-label">পুরাতন</div>
        </div>
      </div>

      <div class="input-row">
        <div class="input-cell">
          <input type="text" id="r-ab-new" placeholder="০.০০"/>
          <input type="text" id="r-ab-old" placeholder="০.০০"/>
        </div>
        <div class="input-cell">
          <input type="text" id="r-an-new" placeholder="০.০০"/>
          <input type="text" id="r-an-old" placeholder="০.০০"/>
        </div>
        <div class="input-cell">
          <input type="text" id="r-dk-new" placeholder="০.০০"/>
          <input type="text" id="r-dk-old" placeholder="০.০০"/>
        </div>
        <div class="input-cell">
          <input type="text" id="r-nk-new" placeholder="০.০০"/>
          <input type="text" id="r-nk-old" placeholder="০.০০"/>
        </div>
      </div>

      <div class="checkbox-row">
        <div class="checkbox-cell">
          <label><input type="checkbox" id="cb-r-ab-new"/></label>
          <label><input type="checkbox" id="cb-r-ab-old"/></label>
        </div>
        <div class="checkbox-cell">
          <label><input type="checkbox" id="cb-r-an-new"/></label>
          <label><input type="checkbox" id="cb-r-an-old"/></label>
        </div>
        <div class="checkbox-cell">
          <label><input type="checkbox" id="cb-r-dk-new"/></label>
          <label><input type="checkbox" id="cb-r-dk-old"/></label>
        </div>
        <div class="checkbox-cell">
          <label><input type="checkbox" id="cb-r-nk-new"/></label>
          <label><input type="checkbox" id="cb-r-nk-old"/></label>
        </div>
      </div>

      <div class="taka-row">
        <button class="taka-btn" onclick="openTakaModal('right')">টাকা লিখুন (ছাত্রী)</button>
        <label class="taka-checkbox-label">
          <input type="checkbox" id="cb-taka-r"/> সব চেক করুন
        </label>
      </div>
    </div>

  </div>{{-- end fee-section --}}

  <div class="action-row">
    <button class="btn-reset" onclick="resetForm()">রিসেট</button>
    <button class="btn-save" onclick="saveForm()">সেভ করুন</button>
  </div>
</div>{{-- end page-wrapper --}}

{{-- Add Modal --}}
<div class="taka-modal-overlay" id="addModal">
  <div class="taka-modal-box">
    <div class="taka-modal-title" id="addModalTitle">যোগ করুন</div>
    <input type="text" class="taka-modal-input" id="addModalInput" placeholder="নাম লিখুন..."/>
    <input type="hidden" id="addModalType"/>
    <div class="taka-modal-actions">
      <button class="btn-taka-cancel" onclick="closeAddModal()">বাতিল</button>
      <button class="btn-taka-ok" onclick="submitAdd()">যোগ করুন</button>
    </div>
  </div>
</div>

{{-- Saved Fee Settings Table --}}
<div class="card-body p-0 mt-3">
  <table class="table table-bordered table-striped mb-0">
    <thead class="table-light">
      <tr>
        <th>শিক্ষাবর্ষ</th>
        <th>শ্রেণি</th>
        <th>ফি এর নাম</th>
        {{-- ছাত্র --}}
        <th>ছাত্র আবাসিক</th>
        <th>ছাত্র অনাবাসিক</th>
        <th>ছাত্র ডে-কেয়ার</th>
        <th>ছাত্র নাইট-কেয়ার</th>
        {{-- ছাত্রী --}}
        <th>ছাত্রী আবাসিক</th>
        <th>ছাত্রী অনাবাসিক</th>
        <th>ছাত্রী ডে-কেয়ার</th>
        <th>ছাত্রী নাইট-কেয়ার</th>
        <th>অ্যাকশন</th>
      </tr>
    </thead>
    <tbody>
    @forelse($feeSettings as $item)
      <tr>
        <td>{{ $item->academicYear->name_bn ?? '-' }}</td>
        <td>{{ $item->class->name_bn ?? 'সকল শ্রেণি' }}</td>
        <td>{{ $item->feeGroup->subLedger->name ?? '-' }}</td>

        {{-- ছাত্র --}}
        <td>
          নতুন: {{ $item->chattra_abashik_new ?? 0 }}<br>
          পুরাতন: {{ $item->chattra_abashik_old ?? 0 }}
        </td>
        <td>
          নতুন: {{ $item->chattra_onabashik_new ?? 0 }}<br>
          পুরাতন: {{ $item->chattra_onabashik_old ?? 0 }}
        </td>
        <td>
          নতুন: {{ $item->chattra_dekeyr_new ?? 0 }}<br>
          পুরাতন: {{ $item->chattra_dekeyr_old ?? 0 }}
        </td>
        <td>
          নতুন: {{ $item->chattra_nightcare_new ?? 0 }}<br>
          পুরাতন: {{ $item->chattra_nightcare_old ?? 0 }}
        </td>

        {{-- ছাত্রী --}}
        <td>
          নতুন: {{ $item->chhatri_abashik_new ?? 0 }}<br>
          পুরাতন: {{ $item->chhatri_abashik_old ?? 0 }}
        </td>
        <td>
          নতুন: {{ $item->chhatri_onabashik_new ?? 0 }}<br>
          পুরাতন: {{ $item->chhatri_onabashik_old ?? 0 }}
        </td>
        <td>
          নতুন: {{ $item->chhatri_dekeyr_new ?? 0 }}<br>
          পুরাতন: {{ $item->chhatri_dekeyr_old ?? 0 }}
        </td>
        <td>
          নতুন: {{ $item->chhatri_nightcare_new ?? 0 }}<br>
          পুরাতন: {{ $item->chhatri_nightcare_old ?? 0 }}
        </td>

        <td>
          <div class="action-buttons">
            <button class="btn-action btn-edit" onclick="editFeeSetting({{ $item->id }})">Edit</button>
            <button class="btn-action btn-delete" onclick="deleteFeeSetting({{ $item->id }})">Delete</button>
          </div>
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="12" class="text-center">কোনো ফি সেটিংস পাওয়া যায়নি</td>
      </tr>
    @endforelse
    </tbody>
  </table>
</div>

@push('scripts')
<script>
const CSRF = "{{ csrf_token() }}";

// =========================
// ELEMENT IDS — migration-এর column নাম অনুযায়ী
// =========================
const LEFT_IDS  = ['l-ab-new','l-ab-old','l-an-new','l-an-old','l-dk-new','l-dk-old','l-nk-new','l-nk-old'];
const RIGHT_IDS = ['r-ab-new','r-ab-old','r-an-new','r-an-old','r-dk-new','r-dk-old','r-nk-new','r-nk-old'];
const ALL_INPUT_IDS = [...LEFT_IDS, ...RIGHT_IDS];

// input id → DB column mapping
const FIELD_MAP = {
  'l-ab-new': 'chattra_abashik_new',
  'l-ab-old': 'chattra_abashik_old',
  'l-an-new': 'chattra_onabashik_new',
  'l-an-old': 'chattra_onabashik_old',
  'l-dk-new': 'chattra_dekeyr_new',
  'l-dk-old': 'chattra_dekeyr_old',
  'l-nk-new': 'chattra_nightcare_new',
  'l-nk-old': 'chattra_nightcare_old',

  'r-ab-new': 'chhatri_abashik_new',
  'r-ab-old': 'chhatri_abashik_old',
  'r-an-new': 'chhatri_onabashik_new',
  'r-an-old': 'chhatri_onabashik_old',
  'r-dk-new': 'chhatri_dekeyr_new',
  'r-dk-old': 'chhatri_dekeyr_old',
  'r-nk-new': 'chhatri_nightcare_new',
  'r-nk-old': 'chhatri_nightcare_old',
};

// =========================
// STATE
// =========================
const state = {
  academicYearId: null,
  classId: null,
  subLedgerId: null,
  currentModalSide: null
};

function updateState() {
    state.academicYearId = document.getElementById('academic-year-select')?.value || null;
    state.classId        = document.getElementById('class-select')?.value || null;
    state.feeGroupId     = document.getElementById('fee-group-select')?.value || null; // ✅
}

// =========================
// SAFE FETCH
// =========================
async function safeFetch(url, options = {}) {
  try {
    const res = await fetch(url, options);
    return await res.json();
  } catch (err) {
    console.error("Fetch error:", err);
    return { success: false, message: err.message };
  }
}

// =========================
// LOAD FEE DATA
// =========================
async function loadFeeData() {
    updateState();
    if (!state.academicYearId || !state.feeGroupId) return; // ✅

    const GET_URL = "{{ route('dashboard.fee-settings.get') }}";
    const url = `${GET_URL}?academic_year_id=${state.academicYearId}&class_id=${state.classId || ''}&fee_group_id=${state.feeGroupId}`; // ✅

    const result = await safeFetch(url);

  if (!result.success || !result.data) {
    ALL_INPUT_IDS.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });
    return;
  }

  const data = result.data;
  Object.keys(FIELD_MAP).forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = data[FIELD_MAP[id]] ?? '';
  });
}

// =========================
// SAVE FORM
// =========================
async function saveForm() {
    updateState();
    if (!state.academicYearId) return alert('একাডেমিক ইয়ার নির্বাচন করুন!');
    if (!state.feeGroupId)     return alert('ফি এর নাম নির্বাচন করুন!'); // ✅

    const payload = {
        academic_year_id: state.academicYearId,
        class_id:         state.classId || null,
        fee_group_id:     state.feeGroupId, // ✅ sub_ledger_id → fee_group_id
    };

  Object.keys(FIELD_MAP).forEach(id => {
    const el = document.getElementById(id);
    payload[FIELD_MAP[id]] = el && el.value !== '' ? parseFloat(el.value) || null : null;
  });

  // chattra_checked / chhatri_checked
  payload.chattra_checked = document.getElementById('cb-taka-l')?.checked ? 1 : 0;
  payload.chhatri_checked = document.getElementById('cb-taka-r')?.checked ? 1 : 0;

  const SAVE_URL = "{{ route('dashboard.fee-settings.save') }}";
  const result = await safeFetch(SAVE_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF },
    body: JSON.stringify(payload)
  });

  if (result.success) {
    alert(result.message || "সফলভাবে সেভ হয়েছে!");
    window.location.reload();
  } else {
    alert(result.message || "সেভ করতে ব্যর্থ হয়েছে!");
  }
}

// =========================
// RESET FORM
// =========================
function resetForm() {
  if (confirm('আপনি কি নিশ্চিত? সব ডাটা মুছে যাবে!')) {
    ALL_INPUT_IDS.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    alert('ফর্ম রিসেট করা হয়েছে!');
  }
}

// =========================
// TAKA MODAL
// =========================
function openTakaModal(side) {
  state.currentModalSide = side;
  document.getElementById('takaModalTitle').innerText =
    side === 'left' ? 'ছাত্র - টাকার পরিমাণ লিখুন' : 'ছাত্রী - টাকার পরিমাণ লিখুন';
  document.getElementById('takaModalInput').value = '';
  document.getElementById('takaModal').classList.add('active');
}

function closeTakaModal() {
  document.getElementById('takaModal').classList.remove('active');
  state.currentModalSide = null;
}

function applyTaka() {
  const amount = document.getElementById('takaModalInput').value;
  if (!amount) { alert('টাকার পরিমাণ লিখুন!'); return; }

  const targetIds = state.currentModalSide === 'left' ? LEFT_IDS : RIGHT_IDS;
  targetIds.forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = amount;
  });

  closeTakaModal();
  alert(`${state.currentModalSide === 'left' ? 'ছাত্র' : 'ছাত্রী'} - সব ফিল্ডে ${amount} টাকা বসানো হয়েছে!`);
}

// =========================
// ADD MODAL
// =========================
function openAddModal(type) {
  const titles = {
    academicYear: 'নতুন শিক্ষাবর্ষ যোগ করুন',
    class:        'নতুন ক্লাস যোগ করুন',
    feeType:      'নতুন ফি টাইপ যোগ করুন',
  };
  document.getElementById('addModalType').value    = type;
  document.getElementById('addModalTitle').innerText = titles[type] || 'যোগ করুন';
  document.getElementById('addModalInput').value   = '';
  document.getElementById('addModal').classList.add('active');
}

function closeAddModal() {
  document.getElementById('addModal').classList.remove('active');
}

async function submitAdd() {
  const type = document.getElementById('addModalType').value;
  const name = document.getElementById('addModalInput').value.trim();
  if (!name) return alert('নাম লিখুন!');

  const urlMap = {
    academicYear: '{{ route("dashboard.academic-year.store-ajax") }}',
    class:        '{{ route("dashboard.class.store-ajax") }}',
    feeType:      '{{ route("dashboard.fee-type.store") }}',
  };
  const msgMap = {
    academicYear: 'শিক্ষাবর্ষ যোগ করা হয়েছে!',
    class:        'ক্লাস যোগ করা হয়েছে!',
    feeType:      'ফি টাইপ যোগ করা হয়েছে!',
  };

  const result = await safeFetch(urlMap[type], {
    method: "POST",
    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": CSRF, "Accept": "application/json" },
    body: JSON.stringify({ name })
  });

  if (result.success) {
    alert(msgMap[type]);
    location.reload();
  } else {
    alert(result.message || 'যোগ করা ব্যর্থ হয়েছে!');
  }
  closeAddModal();
}

// =========================
// CHECKBOX — সব চেক করুন
// =========================
document.addEventListener('DOMContentLoaded', function () {

  const LEFT_CB_IDS = [
    'cb-l-ab-new','cb-l-ab-old',
    'cb-l-an-new','cb-l-an-old',
    'cb-l-dk-new','cb-l-dk-old',
    'cb-l-nk-new','cb-l-nk-old',
  ];
  const RIGHT_CB_IDS = [
    'cb-r-ab-new','cb-r-ab-old',
    'cb-r-an-new','cb-r-an-old',
    'cb-r-dk-new','cb-r-dk-old',
    'cb-r-nk-new','cb-r-nk-old',
  ];

  document.getElementById('cb-taka-l')?.addEventListener('change', function () {
    LEFT_CB_IDS.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.checked = this.checked;
    });
  });

  document.getElementById('cb-taka-r')?.addEventListener('change', function () {
    RIGHT_CB_IDS.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.checked = this.checked;
    });
  });
});

// =========================
// GLOBAL EXPORTS
// =========================
window.resetForm     = resetForm;
window.saveForm      = saveForm;
window.openTakaModal = openTakaModal;
window.closeTakaModal= closeTakaModal;
window.applyTaka     = applyTaka;
window.openAddModal  = openAddModal;
window.closeAddModal = closeAddModal;
window.submitAdd     = submitAdd;
window.loadFeeData   = loadFeeData;
</script>
@endpush

@endsection