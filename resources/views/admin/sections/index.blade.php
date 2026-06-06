@extends('layouts.admin')
@section('title', 'ক্লাস গ্রুপ/সেকশন ব্যবস্থাপনা')
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
    --teal:     #0d9488;
    --teal-l:   #f0fdfa;
    --teal-m:   #99f6e4;
    --radius:   12px;
    --radius-lg:20px;
    --shadow:   0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
    --shadow-lg:0 8px 32px rgba(0,0,0,.12);
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'Lexend', 'Tiro Bangla', sans-serif; background: var(--surface); color: var(--ink); }

.sc-page { padding: 32px 28px; max-width: 1300px; margin: 0 auto; min-height: 100vh; }

/* ── Top bar ── */
.sc-topbar { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 28px; gap: 16px; flex-wrap: wrap; }
.sc-topbar__title { display: flex; align-items: center; gap: 14px; }
.sc-topbar__icon { width: 52px; height: 52px; background: linear-gradient(135deg, #0d9488, #14b8a6); border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; box-shadow: 0 4px 14px rgba(13,148,136,.35); flex-shrink: 0; }
.sc-topbar__text h1 { font-size: 1.4rem; font-weight: 700; color: var(--ink); letter-spacing: -.02em; line-height: 1.2; }
.sc-topbar__text p { font-size: 0.82rem; color: var(--ink-3); margin-top: 2px; font-weight: 300; }

/* ── Stats ── */
.sc-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 28px; }
.stat-card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: 18px 20px; display: flex; align-items: center; gap: 14px; box-shadow: var(--shadow); transition: transform .2s; }
.stat-card:hover { transform: translateY(-2px); }
.stat-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.stat-icon.teal   { background: var(--teal-l);    color: var(--teal); }
.stat-icon.green  { background: var(--success-l); color: var(--success); }
.stat-icon.blue   { background: var(--primary-l); color: var(--primary); }
.stat-label { font-size: 0.75rem; color: var(--ink-3); font-weight: 400; }
.stat-value { font-size: 1.55rem; font-weight: 700; color: var(--ink); line-height: 1.1; }

/* ── Accordion ── */
.sc-accordion { display: flex; flex-direction: column; gap: 16px; }

.sc-block { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow); transition: box-shadow .2s; }
.sc-block:hover { box-shadow: var(--shadow-lg); }

.sc-block__head {
    padding: 16px 22px;
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
    cursor: pointer;
    background: var(--card);
    border-bottom: 1px solid transparent;
    transition: background .15s, border-color .15s;
    user-select: none;
}
.sc-block__head:hover { background: #f8fafc; }
.sc-block__head.expanded { border-bottom-color: var(--border); background: #f8fafc; }

.sc-block__left { display: flex; align-items: center; gap: 14px; }
.sc-block__chevron { width: 30px; height: 30px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--ink-3); font-size: 12px; transition: transform .25s, background .15s; flex-shrink: 0; }
.sc-block__head.expanded .sc-block__chevron { transform: rotate(180deg); background: var(--teal-l); color: var(--teal); }

.sc-block__class { display: flex; align-items: center; gap: 10px; }
.sc-block__num { width: 38px; height: 38px; background: linear-gradient(135deg, #0d9488, #14b8a6); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1rem; font-weight: 700; flex-shrink: 0; box-shadow: 0 2px 8px rgba(13,148,136,.25); }
.sc-block__name { font-weight: 700; font-size: 1rem; color: var(--ink); }
.sc-block__name-en { font-size: 0.78rem; color: var(--ink-3); font-weight: 300; margin-top: 1px; }

.sc-block__right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.count-chip { background: var(--teal-l); border: 1px solid var(--teal-m); color: var(--teal); border-radius: 20px; padding: 4px 12px; font-size: 0.75rem; font-weight: 600; white-space: nowrap; }

.btn-add-sec { display: inline-flex; align-items: center; gap: 6px; background: linear-gradient(135deg, #0d9488, #14b8a6); color: white; border: none; border-radius: 9px; padding: 8px 16px; font-size: 0.8rem; font-family: inherit; font-weight: 600; cursor: pointer; box-shadow: 0 2px 8px rgba(13,148,136,.25); transition: transform .15s, box-shadow .15s; white-space: nowrap; }
.btn-add-sec:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(13,148,136,.35); }

/* ── Section body ── */
.sc-block__body { padding: 20px 22px; }

.sc-items { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px; }

.sc-item { background: #f8fafc; border: 1.5px solid var(--border); border-radius: var(--radius); padding: 14px 16px; display: flex; align-items: center; justify-content: space-between; gap: 10px; transition: border-color .15s, background .15s, transform .15s; }
.sc-item:hover { border-color: var(--teal); background: white; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(13,148,136,.1); }

.sc-item__info h6 { font-size: 0.92rem; font-weight: 700; color: var(--ink); margin-bottom: 3px; }
.sc-item__info .meta { font-size: 0.72rem; color: var(--ink-3); display: flex; gap: 8px; flex-wrap: wrap; }
.sc-item__info .meta span { display: flex; align-items: center; gap: 3px; }

.sc-item__actions { display: flex; gap: 6px; flex-shrink: 0; }
.btn-sm { width: 30px; height: 30px; border-radius: 7px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px; border: 1px solid var(--border); background: var(--card); transition: all .15s; }
.btn-sm.edit { color: var(--primary); }
.btn-sm.edit:hover { background: var(--primary-l); border-color: var(--primary-m); }
.btn-sm.del  { color: var(--danger); }
.btn-sm.del:hover  { background: var(--danger-l); border-color: #fecaca; }

/* ── Empty ── */
.empty-sec { text-align: center; padding: 30px; color: var(--ink-3); font-size: 0.85rem; background: #f8fafc; border-radius: var(--radius); border: 1.5px dashed var(--border-2); }
.empty-sec i { display: block; font-size: 24px; opacity: .25; margin-bottom: 8px; }

/* ── Slide toggle ── */
.sc-block__body-wrap { overflow: hidden; transition: max-height .35s cubic-bezier(.4,0,.2,1); }

/* ── Modal ── */
.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,.55); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 20px; }
.modal-overlay.open { display: flex; }
.modal-box { background: var(--card); border-radius: var(--radius-lg); width: 100%; max-width: 480px; box-shadow: var(--shadow-lg); animation: popIn .25s cubic-bezier(.34,1.56,.64,1) both; overflow: hidden; }
@keyframes popIn { from { opacity: 0; transform: scale(.92) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }

.modal-head { background: linear-gradient(135deg, #0d9488, #14b8a6); padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; }
.modal-head h5 { color: white; font-size: 1rem; font-weight: 600; }
.modal-close { width: 32px; height: 32px; background: rgba(255,255,255,.15); border: none; border-radius: 8px; color: white; cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; transition: background .15s; }
.modal-close:hover { background: rgba(255,255,255,.3); }
.modal-body { padding: 24px; }
.modal-foot { padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; background: #f8fafc; }

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { margin-bottom: 16px; }
.form-label { display: block; font-size: 0.75rem; font-weight: 600; color: var(--ink-2); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 7px; }
.req { color: var(--danger); }
.form-input, .form-select { width: 100%; border: 1.5px solid var(--border-2); border-radius: 9px; padding: 10px 13px; font-size: 0.875rem; font-family: inherit; color: var(--ink); background: var(--card); transition: border-color .15s, box-shadow .15s; appearance: none; }
.form-input:focus, .form-select:focus { outline: none; border-color: var(--teal); box-shadow: 0 0 0 3px rgba(13,148,136,.12); }
.form-select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2364748b' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px; }

.btn-ghost { background: none; border: 1.5px solid var(--border-2); border-radius: 9px; padding: 10px 22px; font-size: 0.875rem; font-family: inherit; font-weight: 500; color: var(--ink-3); cursor: pointer; transition: background .15s; }
.btn-ghost:hover { background: var(--border); }
.btn-save { background: linear-gradient(135deg, #0d9488, #14b8a6); color: white; border: none; border-radius: 9px; padding: 10px 26px; font-size: 0.875rem; font-family: inherit; font-weight: 600; cursor: pointer; box-shadow: 0 3px 10px rgba(13,148,136,.28); transition: transform .15s, box-shadow .15s; }
.btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(13,148,136,.38); }

@media (max-width: 640px) { .sc-page { padding: 20px 14px; } .form-row { grid-template-columns: 1fr; } .sc-block__right { flex-wrap: wrap; } }
</style>

<div class="sc-page">

    {{-- Top bar --}}
    <div class="sc-topbar">
        <div class="sc-topbar__title">
            <div class="sc-topbar__icon"><i class="fas fa-layer-group"></i></div>
            <div class="sc-topbar__text">
                <h1>সেকশন ব্যবস্থাপনা</h1>
                <p>প্রতিটি ক্লাসের গ্রুপ ও সেকশন পরিচালনা করুন</p>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    @php
        $totalSections = $classes->sum(fn($c) => $c->sections->count());
        $activeSections = $classes->sum(fn($c) => $c->sections->where('is_active', true)->count());
    @endphp
    <div class="sc-stats">
        <div class="stat-card">
            <div class="stat-icon teal"><i class="fas fa-chalkboard"></i></div>
            <div><div class="stat-label">মোট ক্লাস</div><div class="stat-value">{{ $classes->count() }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-layer-group"></i></div>
            <div><div class="stat-label">মোট সেকশন</div><div class="stat-value">{{ $totalSections }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div><div class="stat-label">সক্রিয় সেকশন</div><div class="stat-value">{{ $activeSections }}</div></div>
        </div>
    </div>

    {{-- Accordion --}}
    <div class="sc-accordion">
        @foreach($classes as $class)
        <div class="sc-block" id="block-{{ $class->id }}">
            <div class="sc-block__head expanded" onclick="toggleClass({{ $class->id }})">
                <div class="sc-block__left">
                    <div class="sc-block__chevron"><i class="fas fa-chevron-down"></i></div>
                    <div class="sc-block__class">
                        <div class="sc-block__num">{{ $loop->iteration }}</div>
                        <div>
                            <div class="sc-block__name">{{ $class->name_bn }}</div>
                            <div class="sc-block__name-en">{{ $class->name }}</div>
                        </div>
                    </div>
                </div>
                <div class="sc-block__right" onclick="event.stopPropagation()">
                    <span class="count-chip"><i class="fas fa-layer-group" style="font-size:10px;"></i> {{ $class->sections->count() }} টি সেকশন</span>
                    <button class="btn-add-sec" onclick="openSectionModal({{ $class->id }})">
                        <i class="fas fa-plus"></i> নতুন সেকশন
                    </button>
                </div>
            </div>
            <div class="sc-block__body-wrap" id="body-{{ $class->id }}">
                <div class="sc-block__body">
                    <div class="sc-items">
                        @forelse($class->sections as $section)
                        <div class="sc-item">
                            <div class="sc-item__info">
                                <h6>{{ $section->name_bn }} <span style="font-weight:400; color:var(--ink-3);">({{ $section->name }})</span></h6>
                                <div class="meta">
                                    @if($section->is_active)
                                        <span style="color:#059669;"><i class="fas fa-circle" style="font-size:7px;"></i> সক্রিয়</span>
                                    @else
                                        <span style="color:#94a3b8;"><i class="fas fa-circle" style="font-size:7px;"></i> নিষ্ক্রিয়</span>
                                    @endif
                                </div>
                            </div>
                            <div class="sc-item__actions">
                                <button class="btn-sm edit" onclick="editSection({{ $section->id }})" title="এডিট"><i class="fas fa-pen"></i></button>
                                <form action="{{ route('dashboard.sections.destroy', $section->id) }}" method="POST" style="display:contents;" onsubmit="return confirm('এই সেকশনটি মুছে ফেলতে চান?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-sm del" type="submit" title="ডিলিট"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <div class="empty-sec" style="grid-column:1/-1;">
                            <i class="fas fa-inbox"></i>
                            এই ক্লাসে কোনো সেকশন যোগ করা হয়নি।
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>

{{-- Modal --}}
<div id="sectionModal" class="modal-overlay">
    <div class="modal-box">
        <form id="sectionForm" action="{{ route('dashboard.sections.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="sectionFormMethod" value="POST">
            <input type="hidden" name="id" id="sectionId">
            <input type="hidden" name="class_id" id="sectionClassId">
            <div class="modal-head">
                <h5 id="sectionModalTitle"><i class="fas fa-layer-group" style="margin-right:8px;"></i>নতুন সেকশন যোগ করুন</h5>
                <button type="button" class="modal-close" onclick="closeSectionModal()">&#215;</button>
            </div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">নাম (ইংরেজি) <span class="req">*</span></label>
                        <input type="text" name="name" id="section_name" class="form-input" placeholder="Section A" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">নাম (বাংলা) <span class="req">*</span></label>
                        <input type="text" name="name_bn" id="section_name_bn" class="form-input" placeholder="এ বিভাগ" required>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">স্ট্যাটাস</label>
                    <select name="is_active" id="section_status" class="form-select">
                        <option value="1">✅ সক্রিয়</option>
                        <option value="0">🚫 নিষ্ক্রিয়</option>
                    </select>
                </div>
            </div>
            <div class="modal-foot">
                <button type="button" class="btn-ghost" onclick="closeSectionModal()">বাতিল</button>
                <button type="submit" class="btn-save"><i class="fas fa-save" style="margin-right:6px;"></i>সংরক্ষণ করুন</button>
            </div>
        </form>
    </div>
</div>

<script>
// Accordion toggle
function toggleClass(classId) {
    const block = document.getElementById(`block-${classId}`);
    const head = block.querySelector('.sc-block__head');
    const bodyWrap = document.getElementById(`body-${classId}`);

    head.classList.toggle('expanded');

    if (bodyWrap.style.maxHeight) {
        bodyWrap.style.maxHeight = null;
    } else {
        bodyWrap.style.maxHeight = bodyWrap.scrollHeight + "px";
    }
}

// Modal functions
function openSectionModal(classId) {
    document.getElementById('sectionForm').reset();
    document.getElementById('sectionId').value = '';
    document.getElementById('sectionClassId').value = classId;
    document.getElementById('sectionFormMethod').value = 'POST';
    document.getElementById('sectionModalTitle').innerHTML = '<i class="fas fa-layer-group" style="margin-right:8px;"></i>নতুন সেকশন যোগ করুন';
    document.getElementById('sectionModal').classList.add('open');
}

function closeSectionModal() {
    document.getElementById('sectionModal').classList.remove('open');
}

// Edit Section
function editSection(sectionId) {
    fetch(`/dashboard/sections/${sectionId}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('section_name').value = data.name;
            document.getElementById('section_name_bn').value = data.name_bn;
            document.getElementById('section_status').value = data.is_active ? '1' : '0';
            document.getElementById('sectionId').value = sectionId;
            document.getElementById('sectionFormMethod').value = 'PUT';
            document.getElementById('sectionClassId').value = data.class_id;
            document.getElementById('sectionModalTitle').innerHTML = '<i class="fas fa-pen" style="margin-right:8px;"></i>সেকশন এডিট করুন';
            openSectionModal();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('ডাটা লোড করতে সমস্যা হয়েছে।');
        });
}

// Submit Section Form via AJAX - সঠিকভাবে কাজ করবে
document.getElementById('sectionForm').addEventListener('submit', function(e){
    e.preventDefault();

    let form = e.target;
    let formData = new FormData(form);
    let method = form.querySelector('#sectionFormMethod').value;
    let sectionId = form.querySelector('#sectionId').value;
    let url = method === 'POST' ? form.action : `/dashboard/sections/${sectionId}`;

    // লোডিং ইন্ডিকেটর দেখান
    let submitBtn = form.querySelector('.btn-save');
    let originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> সংরক্ষণ হচ্ছে...';
    submitBtn.disabled = true;

    fetch(url, {
        method: 'POST', // সবসময় POST দিয়ে যান, _method টোকেন কাজ করবে
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if(data.success){
            // সফল হলে পেজ রিলোড করুন
            location.reload();
        } else {
            alert(data.message || 'কিছু ভুল হয়েছে।');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(error.message || 'সার্ভার এ সমস্যা হয়েছে।');
    })
    .finally(() => {
        // বাটন রিস্টোর করুন
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// মডালের বাইরে ক্লিক করলে বন্ধ হবে
document.getElementById('sectionModal').addEventListener('click', function(e) {
    if (e.target === this) closeSectionModal();
});
</script>
@endsection