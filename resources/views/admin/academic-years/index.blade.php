@extends('layouts.admin')
@section('title', 'শিক্ষাবর্ষ ব্যবস্থাপনা')
@section('page')

<link href="https://fonts.googleapis.com/css2?family=Tiro+Bangla&family=Lexend:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
:root {
    --ink:      #0f172a;
    --ink-2:    #334155;
    --ink-3:    #64748b;
    --surface:  #f8fafc;
    --card:     #ffffff;
    --border:   #e2e8f0;
    --border-2: #cbd5e1;
    --primary:  #2563eb;
    --primary-d:#1d4ed8;
    --primary-l:#eff6ff;
    --primary-m:#bfdbfe;
    --success:  #059669;
    --success-l:#ecfdf5;
    --danger:   #dc2626;
    --danger-l: #fef2f2;
    --warn:     #d97706;
    --warn-l:   #fffbeb;
    --radius:   12px;
    --radius-lg:20px;
    --shadow:   0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
    --shadow-lg:0 8px 32px rgba(0,0,0,.12);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body { font-family: 'Lexend', 'Tiro Bangla', sans-serif; background: var(--surface); color: var(--ink); }

/* ── Page shell ── */
.ay-page {
    padding: 32px 28px;
    max-width: 1200px;
    margin: 0 auto;
    min-height: 100vh;
}

/* ── Top bar ── */
.ay-topbar {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 28px;
    gap: 16px;
    flex-wrap: wrap;
}
.ay-topbar__title {
    display: flex;
    align-items: center;
    gap: 14px;
}
.ay-topbar__icon {
    width: 52px; height: 52px;
    background: linear-gradient(135deg, #1d4ed8, #3b82f6);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 22px;
    box-shadow: 0 4px 14px rgba(37,99,235,.35);
    flex-shrink: 0;
}
.ay-topbar__text h1 {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--ink);
    letter-spacing: -.02em;
    line-height: 1.2;
}
.ay-topbar__text p {
    font-size: 0.82rem;
    color: var(--ink-3);
    margin-top: 2px;
    font-weight: 300;
}

.btn-primary {
    display: inline-flex; align-items: center; gap: 8px;
    background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
    color: white;
    border: none;
    border-radius: var(--radius);
    padding: 12px 22px;
    font-size: 0.88rem;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    white-space: nowrap;
    box-shadow: 0 3px 10px rgba(37,99,235,.3);
    transition: transform .15s, box-shadow .15s;
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(37,99,235,.4); }
.btn-primary:active { transform: translateY(0); }
.btn-primary .icon { font-size: 14px; }

/* ── Stats row ── */
.ay-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 16px;
    margin-bottom: 28px;
}
.stat-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    display: flex; align-items: center; gap: 14px;
    box-shadow: var(--shadow);
    transition: transform .2s;
}
.stat-card:hover { transform: translateY(-2px); }
.stat-icon {
    width: 42px; height: 42px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.stat-icon.blue  { background: var(--primary-l); color: var(--primary); }
.stat-icon.green { background: var(--success-l); color: var(--success); }
.stat-icon.red   { background: var(--danger-l);  color: var(--danger); }
.stat-icon.amber { background: var(--warn-l);    color: var(--warn); }
.stat-label { font-size: 0.75rem; color: var(--ink-3); font-weight: 400; }
.stat-value { font-size: 1.55rem; font-weight: 700; color: var(--ink); line-height: 1.1; }

/* ── Card ── */
.ay-card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    overflow: hidden;
}

/* ── Table ── */
.ay-table-wrap { overflow-x: auto; }
table.ay-table { width: 100%; border-collapse: collapse; }
table.ay-table thead th {
    background: #f1f5f9;
    padding: 13px 18px;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--ink-3);
    text-transform: uppercase;
    letter-spacing: .06em;
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}
table.ay-table tbody tr {
    transition: background .15s;
}
table.ay-table tbody tr:hover { background: #f8fafc; }
table.ay-table tbody td {
    padding: 14px 18px;
    font-size: 0.875rem;
    color: var(--ink-2);
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
}
table.ay-table tbody tr:last-child td { border-bottom: none; }

.year-name { font-weight: 600; color: var(--ink); }
.year-name-bn { color: var(--ink-3); font-size: 0.82rem; margin-top: 1px; }

.date-chip {
    display: inline-flex; align-items: center; gap: 5px;
    background: #f1f5f9;
    border-radius: 6px;
    padding: 4px 9px;
    font-size: 0.78rem;
    color: var(--ink-3);
    font-weight: 500;
}

/* ── Badges ── */
.badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    white-space: nowrap;
}
.badge-current { background: var(--success-l); color: var(--success); }
.badge-active   { background: var(--primary-l); color: var(--primary); }
.badge-inactive { background: #f1f5f9; color: var(--ink-3); }

/* ── Action buttons ── */
.btn-icon {
    width: 34px; height: 34px;
    border: 1px solid var(--border);
    background: var(--card);
    border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer;
    font-size: 14px;
    transition: all .15s;
}
.btn-icon.edit  { color: var(--primary); }
.btn-icon.edit:hover  { background: var(--primary-l); border-color: var(--primary-m); }
.btn-icon.del   { color: var(--danger); }
.btn-icon.del:hover   { background: var(--danger-l); border-color: #fecaca; }

.actions-cell { display: flex; gap: 6px; align-items: center; }

/* ── Empty state ── */
.empty-state {
    text-align: center;
    padding: 60px 24px;
    color: var(--ink-3);
}
.empty-state i { font-size: 42px; opacity: .25; margin-bottom: 14px; display: block; }
.empty-state p { font-size: 0.9rem; }

/* ── Serial number ── */
.serial {
    width: 28px; height: 28px;
    background: #f1f5f9;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--ink-3);
}

/* ── Modal overlay ── */
.modal-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(15, 23, 42, .55);
    backdrop-filter: blur(4px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.modal-overlay.open { display: flex; }

/* ── Modal box ── */
.modal-box {
    background: var(--card);
    border-radius: var(--radius-lg);
    width: 100%;
    max-width: 490px;
    box-shadow: var(--shadow-lg);
    animation: popIn .25s cubic-bezier(.34,1.56,.64,1) both;
    overflow: hidden;
}
@keyframes popIn {
    from { opacity: 0; transform: scale(.92) translateY(20px); }
    to   { opacity: 1; transform: scale(1)  translateY(0); }
}

.modal-head {
    background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    padding: 20px 24px;
    display: flex; align-items: center; justify-content: space-between;
}
.modal-head h5 { color: white; font-size: 1rem; font-weight: 600; }
.modal-close {
    width: 32px; height: 32px;
    background: rgba(255,255,255,.15);
    border: none; border-radius: 8px;
    color: white; cursor: pointer; font-size: 18px; line-height: 1;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.modal-close:hover { background: rgba(255,255,255,.3); }

.modal-body { padding: 26px 24px; }
.modal-foot {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    display: flex; justify-content: flex-end; gap: 10px;
    background: #f8fafc;
}

/* ── Form grid ── */
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { margin-bottom: 16px; }
.form-label {
    display: block;
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--ink-2);
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 7px;
}
.req { color: var(--danger); }
.form-input, .form-select {
    width: 100%;
    border: 1.5px solid var(--border-2);
    border-radius: 9px;
    padding: 10px 13px;
    font-size: 0.875rem;
    font-family: inherit;
    color: var(--ink);
    background: var(--card);
    transition: border-color .15s, box-shadow .15s;
    appearance: none;
}
.form-input:focus, .form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37,99,235,.12);
}
.form-select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2364748b' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px; }

.checkbox-row {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 14px;
    background: var(--primary-l);
    border: 1.5px solid var(--primary-m);
    border-radius: 9px;
    cursor: pointer;
}
.checkbox-row input[type="checkbox"] {
    width: 17px; height: 17px;
    accent-color: var(--primary);
    cursor: pointer;
}
.checkbox-row span { font-size: 0.85rem; font-weight: 500; color: var(--primary-d); }

/* ── Modal action buttons ── */
.btn-ghost {
    background: none;
    border: 1.5px solid var(--border-2);
    border-radius: 9px;
    padding: 10px 22px;
    font-size: 0.875rem;
    font-family: inherit;
    font-weight: 500;
    color: var(--ink-3);
    cursor: pointer;
    transition: background .15s;
}
.btn-ghost:hover { background: var(--border); }

.btn-save {
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
    color: white;
    border: none;
    border-radius: 9px;
    padding: 10px 26px;
    font-size: 0.875rem;
    font-family: inherit;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 3px 10px rgba(37,99,235,.28);
    transition: transform .15s, box-shadow .15s;
}
.btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(37,99,235,.38); }

/* ── Responsive ── */
@media (max-width: 640px) {
    .ay-page { padding: 20px 14px; }
    .form-row { grid-template-columns: 1fr; }
    .ay-topbar { flex-direction: column; align-items: flex-start; }
}
</style>

<div class="ay-page">

    {{-- Top bar --}}
    <div class="ay-topbar">
        <div class="ay-topbar__title">
            <div class="ay-topbar__icon"><i class="fas fa-calendar-alt"></i></div>
            <div class="ay-topbar__text">
                <h1>শিক্ষাবর্ষ ব্যবস্থাপনা</h1>
                <p>সকল শিক্ষাবর্ষ পরিচালনা ও আপডেট করুন</p>
            </div>
        </div>
        <button class="btn-primary" onclick="openModal()">
            <span class="icon"><i class="fas fa-plus"></i></span> নতুন শিক্ষাবর্ষ যোগ করুন
        </button>
    </div>

    {{-- Stats --}}
    <div class="ay-stats">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-layer-group"></i></div>
            <div>
                <div class="stat-label">মোট শিক্ষাবর্ষ</div>
                <div class="stat-value">{{ $academicYears->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="stat-label">সক্রিয়</div>
                <div class="stat-value">{{ $academicYears->where('status','active')->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber"><i class="fas fa-star"></i></div>
            <div>
                <div class="stat-label">বর্তমান বছর</div>
                <div class="stat-value">{{ $academicYears->where('is_current',1)->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-ban"></i></div>
            <div>
                <div class="stat-label">নিষ্ক্রিয়</div>
                <div class="stat-value">{{ $academicYears->where('status','inactive')->count() }}</div>
            </div>
        </div>
    </div>

    {{-- Table card --}}
    <div class="ay-card">
        <div class="ay-table-wrap">
            <table class="ay-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>শিক্ষাবর্ষ</th>
                        <th>বর্তমান</th>
                        <th>স্ট্যাটাস</th>
                        <th>অ্যাকশন</th>
                    </tr>
                </thead>
<tbody>
    @forelse($academicYears as $key => $year)
    <tr>
        <td><span class="serial">{{ $key + 1 }}</span></td>
        <td>
            <div class="year-name">{{ $year->name }}</div>
            <div class="year-name-bn">{{ $year->name_bn }}</div>
         </td>
        <td>
            @if($year->is_current)
                <span class="badge badge-current"><i class="fas fa-check-circle"></i> বর্তমান</span>
            @else
                <span style="color:#cbd5e1; font-size:18px;">—</span>
            @endif
         </td>
        <td>
            @if($year->status == 'active')
                <span class="badge badge-active"><i class="fas fa-circle" style="font-size:7px;"></i> সক্রিয়</span>
            @else
                <span class="badge badge-inactive"><i class="fas fa-circle" style="font-size:7px;"></i> নিষ্ক্রিয়</span>
            @endif
         </td>
        <td>
            <div class="actions-cell">
                <button class="btn-icon edit" onclick="editYear({{ $year->id }})" title="এডিট">
                    <i class="fas fa-pen"></i>
                </button>
                <form action="{{ route('dashboard.academic-years.destroy', $year->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('এই শিক্ষাবর্ষটি মুছে ফেলতে চান?')">
                    @csrf @method('DELETE')
                    <button class="btn-icon del" type="submit" title="ডিলিট">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </div>
         </td>
    </tr>
    @empty
    <tr>
        <td colspan="5">  {{-- colspan 5 হবে (7 থেকে কমেছে) --}}
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <p>কোনো শিক্ষাবর্ষ এখনো যোগ করা হয়নি।</p>
            </div>
         </td>
    </tr>
    @endforelse
</tbody>
            </table>
        </div>
    </div>

</div>

{{-- ───────── Modal ───────── --}}
<div id="yearModal" class="modal-overlay">
    <div class="modal-box">
        <form id="yearForm" action="{{ route('dashboard.academic-years.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="id" id="yearId">

            <div class="modal-head">
                <h5 id="modalTitle"><i class="fas fa-calendar-plus" style="margin-right:8px;"></i>নতুন শিক্ষাবর্ষ যোগ করুন</h5>
                <button type="button" class="modal-close" onclick="closeModal()">&#215;</button>
            </div>

            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">শিক্ষাবর্ষ (ইংরেজি) <span class="req">*</span></label>
                        <input type="text" name="name" id="name" class="form-input" placeholder="2024-2025" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">শিক্ষাবর্ষ (বাংলা) <span class="req">*</span></label>
                        <input type="text" name="name_bn" id="name_bn" class="form-input" placeholder="২০২৪-২০২৫" required>
                    </div>
                </div>


                <div class="form-group">
                    <label class="form-label">স্ট্যাটাস</label>
                    <select name="status" id="status" class="form-select">
                        <option value="active">✅ সক্রিয়</option>
                        <option value="inactive">🚫 নিষ্ক্রিয়</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="checkbox-row">
                        <input type="checkbox" name="is_current" id="is_current" value="1">
                        <span>⭐ এটিকে বর্তমান শিক্ষাবর্ষ হিসেবে সেট করুন</span>
                    </label>
                </div>
            </div>

            <div class="modal-foot">
                <button type="button" class="btn-ghost" onclick="closeModal()">বাতিল</button>
                <button type="submit" class="btn-save"><i class="fas fa-save" style="margin-right:6px;"></i>সংরক্ষণ করুন</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    const modal = document.getElementById('yearModal');
    modal.classList.add('open');
    document.getElementById('yearForm').reset();
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-calendar-plus" style="margin-right:8px;"></i>নতুন শিক্ষাবর্ষ যোগ করুন';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('yearForm').action = '{{ route("dashboard.academic-years.store") }}';
}

function closeModal() {
    document.getElementById('yearModal').classList.remove('open');
}

// Close on backdrop click
document.getElementById('yearModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

function editYear(id) {
    fetch(`/dashboard/academic-years/${id}/edit`)
        .then(r => r.json())
        .then(data => {
            document.getElementById('yearId').value        = data.id;
            document.getElementById('name').value          = data.name;
            document.getElementById('name_bn').value       = data.name_bn;
            document.getElementById('start_date').value    = data.start_date;
            document.getElementById('end_date').value      = data.end_date;
            document.getElementById('status').value        = data.status;
            document.getElementById('is_current').checked  = data.is_current == 1;
            document.getElementById('modalTitle').innerHTML = '<i class="fas fa-calendar-edit" style="margin-right:8px;"></i>শিক্ষাবর্ষ এডিট করুন';
            document.getElementById('formMethod').value    = 'PUT';
            document.getElementById('yearForm').action     = `/dashboard/academic-years/${id}`;
            openModal();
        });
}
</script>

@endsection