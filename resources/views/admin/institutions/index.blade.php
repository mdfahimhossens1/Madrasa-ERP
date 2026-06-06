@extends('layouts.admin')
@section('title', 'প্রতিষ্ঠান ব্যবস্থাপনা')
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
    --amber:    #d97706;
    --amber-l:  #fffbeb;
    --rose:     #e11d48;
    --rose-l:   #fff1f2;
    --radius:   12px;
    --radius-lg:20px;
    --shadow:   0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
    --shadow-lg:0 8px 32px rgba(0,0,0,.12);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Lexend', sans-serif; background: var(--surface); color: var(--ink); }

.inst-page { padding: 32px 28px; max-width: 1400px; margin: 0 auto; min-height: 100vh; }

/* ── Top bar ── */
.inst-topbar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 28px; gap: 16px; flex-wrap: wrap; }
.inst-topbar__title { display: flex; align-items: center; gap: 14px; }
.inst-topbar__icon { width: 52px; height: 52px; background: linear-gradient(135deg, #0369a1, #0ea5e9); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; box-shadow: 0 4px 14px rgba(3,105,161,.35); flex-shrink: 0; }
.inst-topbar__text h1 { font-size: 1.4rem; font-weight: 700; color: var(--ink); letter-spacing: -.02em; line-height: 1.2; }
.inst-topbar__text p { font-size: 0.82rem; color: var(--ink-3); margin-top: 2px; font-weight: 300; }

.btn-primary-sky { display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #0369a1, #0ea5e9); color: white; border: none; border-radius: var(--radius); padding: 12px 22px; font-size: 0.88rem; font-weight: 600; font-family: inherit; cursor: pointer; white-space: nowrap; box-shadow: 0 3px 10px rgba(3,105,161,.3); transition: transform .15s, box-shadow .15s; }
.btn-primary-sky:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(3,105,161,.4); }

/* ── Stats ── */
.inst-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 28px; }
.stat-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px 20px; display: flex; align-items: center; gap: 14px; box-shadow: var(--shadow); transition: transform .2s; }
.stat-card:hover { transform: translateY(-2px); }
.stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.si-blue   { background: #e0f2fe; color: #0369a1; }
.si-green  { background: var(--success-l); color: var(--success); }
.si-amber  { background: var(--amber-l); color: var(--amber); }
.si-rose   { background: var(--rose-l); color: var(--rose); }
.stat-label { font-size: 0.75rem; color: var(--ink-3); font-weight: 400; }
.stat-value { font-size: 1.55rem; font-weight: 700; color: var(--ink); line-height: 1.1; }

/* ── Card / Table ── */
.inst-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow); overflow: hidden; }

/* ── Search bar ── */
.inst-toolbar { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.search-wrap { position: relative; flex: 1; min-width: 200px; }
.search-wrap i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--ink-3); font-size: 14px; }
.search-input { width: 100%; border: 1.5px solid var(--border-2); border-radius: 9px; padding: 9px 13px 9px 36px; font-size: 0.875rem; font-family: inherit; color: var(--ink); background: var(--card); transition: border-color .15s, box-shadow .15s; }
.search-input:focus { outline: none; border-color: #0369a1; box-shadow: 0 0 0 3px rgba(3,105,161,.1); }
.filter-select { border: 1.5px solid var(--border-2); border-radius: 9px; padding: 9px 34px 9px 12px; font-size: 0.85rem; font-family: inherit; color: var(--ink-2); appearance: none; background: var(--card) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2364748b' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") no-repeat right 10px center; cursor: pointer; transition: border-color .15s; }
.filter-select:focus { outline: none; border-color: #0369a1; }

/* ── Table ── */
.inst-table-wrap { overflow-x: auto; }
table.inst-table { width: 100%; border-collapse: collapse; }
table.inst-table thead th { background: #f1f5f9; padding: 12px 16px; text-align: left; font-size: 0.72rem; font-weight: 600; color: var(--ink-3); text-transform: uppercase; letter-spacing: .06em; border-bottom: 1px solid var(--border); white-space: nowrap; }
table.inst-table tbody tr { transition: background .12s; }
table.inst-table tbody tr:hover { background: #f8fafc; }
table.inst-table tbody td { padding: 13px 16px; font-size: 0.85rem; color: var(--ink-2); border-bottom: 1px solid var(--border); vertical-align: middle; }
table.inst-table tbody tr:last-child td { border-bottom: none; }

/* ── Institution logo/name cell ── */
.inst-name-cell { display: flex; align-items: center; gap: 12px; }
.inst-logo { width: 38px; height: 38px; border-radius: 10px; object-fit: cover; border: 1px solid var(--border); flex-shrink: 0; }
.inst-logo-placeholder { width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #0369a1, #0ea5e9); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: 700; flex-shrink: 0; }
.inst-name { font-weight: 600; color: var(--ink); font-size: 0.875rem; }
.inst-name-bn { font-size: 0.75rem; color: var(--ink-3); margin-top: 1px; }

.serial { width: 28px; height: 28px; background: #f1f5f9; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.72rem; font-weight: 700; color: var(--ink-3); }
.code-pill { background: #f1f5f9; border-radius: 6px; padding: 3px 9px; font-size: 0.75rem; font-family: monospace; color: var(--ink-2); letter-spacing: .04em; }

/* Type badges */
.type-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 11px; border-radius: 20px; font-size: 0.72rem; font-weight: 600; }
.type-school     { background: #e0f2fe; color: #0369a1; }
.type-college    { background: #f3e8ff; color: #7c3aed; }
.type-madrasa    { background: #fef3c7; color: #b45309; }
.type-university { background: #d1fae5; color: #065f46; }
.type-other      { background: #f1f5f9; color: var(--ink-3); }

.badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 11px; border-radius: 20px; font-size: 0.72rem; font-weight: 600; }
.badge-active   { background: var(--success-l); color: var(--success); }
.badge-inactive { background: #f1f5f9; color: var(--ink-3); }

.contact-text { font-size: 0.8rem; color: var(--ink-3); }
.contact-text a { color: var(--ink-3); text-decoration: none; }
.contact-text a:hover { color: var(--primary); }

/* Action buttons */
.action-cell { display: flex; gap: 6px; align-items: center; }
.btn-icon { width: 32px; height: 32px; border: 1px solid var(--border); background: var(--card); border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; font-size: 13px; transition: all .15s; }
.btn-icon.edit { color: var(--primary); }
.btn-icon.edit:hover { background: var(--primary-l); border-color: var(--primary-m); }
.btn-icon.del  { color: var(--danger); }
.btn-icon.del:hover  { background: var(--danger-l); border-color: #fecaca; }

/* Empty */
.empty-state { text-align: center; padding: 60px 24px; color: var(--ink-3); }
.empty-state i { font-size: 40px; opacity: .2; margin-bottom: 14px; display: block; }

/* ── Modal ── */
.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); z-index: 99999 !important; align-items: flex-start; justify-content: center; padding: 40px 20px; overflow-y: auto; }
.modal-overlay.open { display: flex !important; }
.modal-box { background: var(--card); border-radius: var(--radius-lg); width: 100%; max-width: 680px; box-shadow: var(--shadow-lg); animation: popIn .25s cubic-bezier(.34,1.56,.64,1) both; overflow: hidden; margin: auto; }
@keyframes popIn { from { opacity: 0; transform: scale(.93) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }

.modal-head { background: linear-gradient(135deg, #0369a1, #0ea5e9); padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; }
.modal-head h5 { color: white; font-size: 1rem; font-weight: 600; }
.modal-close { width: 32px; height: 32px; background: rgba(255,255,255,.15); border: none; border-radius: 8px; color: white; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; transition: background .15s; }
.modal-close:hover { background: rgba(255,255,255,.3); }
.modal-body { padding: 26px 24px; }
.modal-foot { padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; background: #f8fafc; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
.form-group { margin-bottom: 16px; }
.form-label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--ink-2); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 7px; }
.req { color: var(--danger); }
.form-input, .form-select, .form-textarea { width: 100%; border: 1.5px solid var(--border-2); border-radius: 9px; padding: 10px 13px; font-size: 0.875rem; font-family: inherit; color: var(--ink); background: var(--card); transition: border-color .15s, box-shadow .15s; appearance: none; }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: #0369a1; box-shadow: 0 0 0 3px rgba(3,105,161,.12); }
.form-select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2364748b' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px; }
.form-textarea { resize: vertical; min-height: 80px; }
.form-file-wrap { border: 1.5px dashed var(--border-2); border-radius: 9px; padding: 14px; text-align: center; cursor: pointer; transition: border-color .15s; }
.form-file-wrap:hover { border-color: #0369a1; background: #f0f9ff; }
.form-file-wrap input { display: none; }
.form-file-wrap label { cursor: pointer; font-size: 0.82rem; color: var(--ink-3); display: flex; flex-direction: column; align-items: center; gap: 6px; }
.form-file-wrap label i { font-size: 22px; color: #0369a1; opacity: .6; }

.section-divider { font-size: 0.72rem; font-weight: 700; color: var(--ink-3); text-transform: uppercase; letter-spacing: .08em; margin: 4px 0 16px; padding-bottom: 8px; border-bottom: 1px solid var(--border); }

.btn-ghost { background: none; border: 1.5px solid var(--border-2); border-radius: 9px; padding: 10px 22px; font-size: 0.875rem; font-family: inherit; font-weight: 500; color: var(--ink-3); cursor: pointer; transition: background .15s; }
.btn-ghost:hover { background: var(--border); }
.btn-save-sky { background: linear-gradient(135deg, #0369a1, #0ea5e9); color: white; border: none; border-radius: 9px; padding: 10px 26px; font-size: 0.875rem; font-family: inherit; font-weight: 600; cursor: pointer; box-shadow: 0 3px 10px rgba(3,105,161,.28); transition: transform .15s, box-shadow .15s; }
.btn-save-sky:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(3,105,161,.38); }

@media (max-width: 640px) {
    .inst-page { padding: 20px 14px; }
    .form-row, .form-row-3 { grid-template-columns: 1fr; }
    .inst-topbar { flex-direction: column; align-items: flex-start; }
}
</style>

<div class="inst-page">

    {{-- Top bar --}}
    <div class="inst-topbar">
        <div class="inst-topbar__title">
            <div class="inst-topbar__icon"><i class="fas fa-university"></i></div>
            <div class="inst-topbar__text">
                @if(
                    auth()->user()->role &&
                    auth()->user()->role->slug == 'madrasa-admin'
                )
                    <h1>মাদ্রাসার তথ্য</h1>
                    <p>আপনার প্রতিষ্ঠানের তথ্য পরিচালনা করুন</p>
                @else
                    <h1>Institution Management</h1>
                    <p>Manage all registered institutions</p>
                @endif
            </div>
        </div>
        @if( auth()->user()->role && in_array(auth()->user()->role->slug, ['super-admin', 'soft-admin']))
            <button class="btn-primary-sky" onclick="openCreateModal()">
                <i class="fas fa-plus"></i> Add Institution
            </button>
        @endif
    </div>

    {{-- Stats --}}
    <div class="inst-stats">
        <div class="stat-card">
            <div class="stat-icon si-blue"><i class="fas fa-university"></i></div>
            <div><div class="stat-label">Total</div><div class="stat-value">{{ $institutions->count() }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon si-green"><i class="fas fa-check-circle"></i></div>
            <div><div class="stat-label">Active</div><div class="stat-value">{{ $institutions->where('status','active')->count() }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon si-amber"><i class="fas fa-school"></i></div>
            <div><div class="stat-label">Schools</div><div class="stat-value">{{ $institutions->where('type','school')->count() }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon si-rose"><i class="fas fa-ban"></i></div>
            <div><div class="stat-label">Inactive</div><div class="stat-value">{{ $institutions->where('status','inactive')->count() }}</div></div>
        </div>
    </div>

    {{-- Table card --}}
    <div class="inst-card">
        {{-- Toolbar --}}
        <div class="inst-toolbar">
            <div class="search-wrap">
                <i class="fas fa-search"></i>
                <input type="text" class="search-input" id="instSearch" placeholder="Search by name, code, email...">
            </div>
            <select class="filter-select" id="typeFilter">
                <option value="">All Types</option>
                <option value="school">School</option>
                <option value="college">College</option>
                <option value="madrasa">Madrasa</option>
                <option value="university">University</option>
            </select>
            <select class="filter-select" id="statusFilter">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="inst-table-wrap">
            <table class="inst-table" id="instTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Institution</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($institutions as $institution)
                    <tr data-name="{{ strtolower($institution->name) }} {{ strtolower($institution->name_bn) }} {{ strtolower($institution->code) }} {{ strtolower($institution->email) }}"
                        data-type="{{ $institution->type }}"
                        data-status="{{ $institution->status }}">
                        <td><span class="serial">{{ $loop->iteration }}</span></td>
                        <td>
                            <div class="inst-name-cell">
                                @if($institution->logo)
                                    <img src="{{ asset('storage/'.$institution->logo) }}" class="inst-logo" alt="">
                                @else
                                    <div class="inst-logo-placeholder">{{ strtoupper(substr($institution->name,0,1)) }}</div>
                                @endif
                                <div>
                                    <div class="inst-name">{{ $institution->name }}</div>
                                    @if($institution->name_bn)
                                    <div class="inst-name-bn">{{ $institution->name_bn }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td><span class="code-pill">{{ $institution->code ?? '—' }}</span></td>
                        <td>
                            @php $t = $institution->type; @endphp
                            <span class="type-badge type-{{ $t }}">
                                @if($t=='school') <i class="fas fa-school" style="font-size:10px;"></i> School
                                @elseif($t=='college') <i class="fas fa-building" style="font-size:10px;"></i> College
                                @elseif($t=='madrasa') <i class="fas fa-moon" style="font-size:10px;"></i> Madrasa
                                @elseif($t=='university') <i class="fas fa-graduation-cap" style="font-size:10px;"></i> University
                                @else {{ ucfirst($t) }} @endif
                            </span>
                        </td>
                        <td>
                            @if($institution->status == 1)
                                <span class="badge badge-active" value="1"><i class="fas fa-circle" style="font-size:7px;"></i> Active</span>
                            @else
                                <span class="badge badge-inactive" value="0"><i class="fas fa-circle" style="font-size:7px;"></i> Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-cell">
                                <button class="btn-icon edit editBtn" data-id="{{ $institution->id }}" title="Edit"><i class="fas fa-pen"></i></button>
                                <form action="{{ route('dashboard.institutions.destroy', $institution->id) }}" method="POST" style="display:contents;" onsubmit="return confirm('Delete this institution?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-icon del" type="submit" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-university"></i>
                                <p>No institutions found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ───── Create Modal ───── --}}
<div id="createModal" class="modal-overlay">
    <div class="modal-box">
        <form action="{{ route('dashboard.institutions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-head">
                <h5><i class="fas fa-plus-circle" style="margin-right:8px;"></i>Add Institution</h5>
                <button type="button" class="modal-close" onclick="closeCreateModal()">&#215;</button>
            </div>
            <div class="modal-body">
                <p class="section-divider">Basic Information</p>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Institution Name <span class="req">*</span></label>
                        <input type="text" name="name" class="form-input" placeholder="Institution name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">প্রতিষ্ঠানের নাম</label>
                        <input type="text" name="name_bn" class="form-input" placeholder="প্রতিষ্ঠানের নাম">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">মাদ্রাসা কোড</label>
                        <input type="text" name="code" class="form-input" placeholder="INST-001">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">ইমেইল</label>
                        <input type="email" name="email" class="form-input" placeholder="email@institution.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">যোগাযোগ নাম্বার</label>
                        <input type="text" name="phone" class="form-input" placeholder="+880...">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Short Address</label>
                    <textarea name="address" class="form-textarea" placeholder="Full address..."></textarea>
                </div>

                <p class="section-divider">Settings</p>
                <div class="form-row">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">✅ Active</option>
                            <option value="inactive">🚫 Inactive</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Logo</label>
                        <div class="form-file-wrap">
                            <label for="create_logo">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span id="create_logo_label">Click to upload logo</span>
                            </label>
                            <input type="file" name="logo" id="create_logo" accept="image/*" onchange="updateLabel(this,'create_logo_label')">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-ghost" onclick="closeCreateModal()">Cancel</button>
                <button type="submit" class="btn-save-sky"><i class="fas fa-save" style="margin-right:6px;"></i>Save Institution</button>
            </div>
        </form>
    </div>
</div>

{{-- ───── Edit Modal ───── --}}
<div id="editModal" class="modal-overlay">
    <div class="modal-box">
        <form id="editInstitutionForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-head">
                <h5><i class="fas fa-pen" style="margin-right:8px;"></i>Edit Institution</h5>
                <button type="button" class="modal-close" onclick="closeEditModal()">&#215;</button>
            </div>
            <div class="modal-body" id="editModalBody">
                <div style="text-align:center; padding: 40px; color: var(--ink-3);">
                    <i class="fas fa-spinner fa-spin" style="font-size:24px; margin-bottom:12px; display:block;"></i>
                    Loading...
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-ghost" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-save-sky"><i class="fas fa-save" style="margin-right:6px;"></i>Update Institution</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Inline scripts (runs after DOM is ready) ── --}}

<script>
// ── Modal helpers ──
function openCreateModal() { document.getElementById('createModal').classList.add('open'); }
function closeCreateModal() { document.getElementById('createModal').classList.remove('open'); }
function openEditModal()   { document.getElementById('editModal').classList.add('open'); }
function closeEditModal()  { document.getElementById('editModal').classList.remove('open'); }

document.getElementById('createModal').addEventListener('click', function(e) { if(e.target===this) closeCreateModal(); });
document.getElementById('editModal').addEventListener('click',   function(e) { if(e.target===this) closeEditModal(); });

// ── Logo label update ──
function updateLabel(input, labelId) {
    document.getElementById(labelId).textContent = input.files[0] ? input.files[0].name : 'Click to upload logo';
}

// ── Edit button ──
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.getElementById('editModalBody').innerHTML = '<div style="text-align:center;padding:40px;color:#64748b;"><i class="fas fa-spinner fa-spin" style="font-size:24px;margin-bottom:12px;display:block;"></i>Loading...</div>';
        openEditModal();
        fetch(`/dashboard/institutions/${id}/edit`)
            .then(r => r.text())
            .then(html => {
                document.getElementById('editModalBody').innerHTML = html;
                document.getElementById('editInstitutionForm').action = `/dashboard/institutions/${id}`;
            });
    });
});

// ── Search & filter ──
function filterTable() {
    const q      = document.getElementById('instSearch').value.toLowerCase();
    const type   = document.getElementById('typeFilter').value;
    const status = document.getElementById('statusFilter').value;
    document.querySelectorAll('#instTable tbody tr[data-name]').forEach(row => {
        const matchQ      = !q      || row.dataset.name.includes(q);
        const matchType   = !type   || row.dataset.type   === type;
        const matchStatus = !status || row.dataset.status === status;
        row.style.display = (matchQ && matchType && matchStatus) ? '' : 'none';
    });
}
document.getElementById('instSearch').addEventListener('input', filterTable);
document.getElementById('typeFilter').addEventListener('change', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);
</script>
@endsection

