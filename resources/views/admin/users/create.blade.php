@extends('layouts.admin')
@section('title', 'নতুন ইউজার তৈরি')
@section('page')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap');
    .reg-page * { font-family: 'Hind Siliguri', sans-serif; box-sizing: border-box; }
    .reg-page { background: #f0f4ff; min-height: 100vh; padding: 24px; }
    .reg-card { background: #fff; border-radius: 16px; box-shadow: 0 2px 16px rgba(30,58,138,0.08); overflow: hidden; }
    .reg-card-header { display: flex; align-items: center; gap: 12px; padding: 18px 28px; border-bottom: 1.5px solid #eef2fb; }
    .reg-card-header .header-icon { width: 38px; height: 38px; background: #eef2fb; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #3b5bdb; font-size: 1rem; }
    .reg-card-header h5 { margin: 0; font-size: 1.05rem; font-weight: 700; color: #1a1a2e; }
    .reg-card-body { padding: 28px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr 320px; gap: 24px; align-items: start; }
    .form-label { display: block; font-size: 0.82rem; font-weight: 600; color: #374151; margin-bottom: 6px; }
    .form-label .req { color: #ef4444; margin-left: 2px; }
    .form-control, .form-select { width: 100%; border: 1.5px solid #e5e9f5; border-radius: 10px; padding: 10px 14px; font-size: 0.88rem; color: #374151; background: #f8faff; outline: none; transition: border-color 0.2s, background 0.2s; font-family: 'Hind Siliguri', sans-serif; appearance: none; -webkit-appearance: none; }
    .form-select { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 36px; }
    .form-control:focus, .form-select:focus { border-color: #6b8cde; background: #fff; box-shadow: 0 0 0 3px rgba(107,140,222,0.12); }
    .form-control::placeholder { color: #b0bac9; }
    .field-group { margin-bottom: 0; }
    .mini-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .gender-row { display: flex; align-items: center; gap: 18px; padding-top: 8px; height: 42px; }
    .radio-label { display: flex; align-items: center; gap: 6px; font-size: 0.88rem; color: #374151; cursor: pointer; }
    .radio-label input[type="radio"] { accent-color: #3b5bdb; width: 16px; height: 16px; cursor: pointer; }
    .col-fields { display: flex; flex-direction: column; gap: 14px; }
    .phone-group { display: flex; gap: 8px; }
    .phone-group .form-control { flex: 1; }
    .phone-group .form-select { width: 100px; flex-shrink: 0; }
    .address-col { display: flex; flex-direction: column; gap: 16px; }
    .address-card { background: #f8faff; border: 1.5px solid #e5e9f5; border-radius: 14px; padding: 18px; }
    .address-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
    .address-card-title { display: flex; align-items: center; gap: 7px; font-size: 0.9rem; font-weight: 700; color: #1e3a8a; }
    .address-card-title i { color: #3b5bdb; font-size: 0.9rem; }
    .same-check-label { display: flex; align-items: center; gap: 6px; font-size: 0.78rem; color: #6b7280; cursor: pointer; background: #eef2fb; padding: 4px 10px; border-radius: 20px; white-space: nowrap; }
    .same-check-label input { accent-color: #3b5bdb; cursor: pointer; }
    .addr-fields { display: flex; flex-direction: column; gap: 10px; }
    .addr-mini-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .reg-card-footer { display: flex; justify-content: flex-end; align-items: center; gap: 12px; padding: 18px 28px; border-top: 1.5px solid #eef2fb; background: #fafbff; margin-top: 24px; border-radius: 0 0 16px 16px; }
    .btn-cancel { background: transparent; border: 1.5px solid #e5e9f5; border-radius: 10px; padding: 10px 24px; font-size: 0.9rem; font-weight: 600; color: #6b7280; cursor: pointer; transition: background 0.15s; font-family: 'Hind Siliguri', sans-serif; text-decoration: none; }
    .btn-cancel:hover { background: #f0f4ff; color: #374151; }
    .btn-save { background: #1e3a8a; border: none; border-radius: 10px; padding: 10px 26px; font-size: 0.9rem; font-weight: 600; color: #fff; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: background 0.15s; font-family: 'Hind Siliguri', sans-serif; }
    .btn-save:hover { background: #1e40af; }
    .alert-danger-custom { background: #fef2f2; border: 1.5px solid #fca5a5; border-radius: 10px; padding: 14px 18px; margin-bottom: 20px; font-size: 0.88rem; color: #b91c1c; }
    .preview-id-box { background: linear-gradient(135deg, #eef2ff, #e0e7ff); border: 2px solid #3b5bdb; border-radius: 12px; padding: 12px 16px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    .preview-id-label { font-size: 0.85rem; font-weight: 600; color: #1e3a8a; display: flex; align-items: center; gap: 6px; }
    .preview-id-value { font-size: 1.2rem; font-weight: 700; color: #1e3a8a; background: white; padding: 6px 16px; border-radius: 30px; letter-spacing: 1px; font-family: monospace; }
    .preview-id-note { font-size: 0.7rem; color: #4b5563; }
    @media (max-width: 992px) { .form-grid { grid-template-columns: 1fr 1fr; } .address-col { grid-column: 1 / -1; flex-direction: row; flex-wrap: wrap; } .address-card { flex: 1; min-width: 260px; } }
    @media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } .mini-grid-2, .addr-mini-grid { grid-template-columns: 1fr; } }
</style>

<div class="reg-page">
    <div class="reg-card">
        <div class="reg-card-header">
            <div class="header-icon"><i class="fas fa-user-plus"></i></div>
            <h5>ইউজার রেজিস্ট্রেশন ফরম</h5>
        </div>

        <div class="reg-card-body">
            @if ($errors->any())
            <div class="alert-danger-custom">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('dashboard.user.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="role_id" id="role_id" value="">
                <input type="hidden" name="madrasa_id" id="madrasa_id" value="{{ auth()->user()->madrasa_id ?? 1 }}">

                <div class="form-grid">
                    {{-- LEFT COLUMN --}}
                    <div class="col-fields">
                        <div class="field-group">
                            <label class="form-label">ব্যবহারকারীর ধরণ <span class="req">*</span></label>
                            <select name="user_type" id="user_type" class="form-select" required>
                                <option value="">নির্বাচন করুন</option>
                                @foreach($userTypes as $key => $type)
                                    <option value="{{ $key }}" {{ old('user_type') == $key ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mini-grid-2">
                        <div class="field-group">
                            <label class="form-label">আইডি <span class="req">*</span></label>
                            <input type="text" name="custom_id" id="custom_id" class="form-control" 
                                value="{{ old('custom_id') }}" placeholder="স্বয়ংক্রিয়ভাবে জেনারেট হবে" readonly 
                                style="background:#eef2fb; font-weight:600; color:#1e3a8a;">
                        </div>
                            <div class="field-group">
                                <label class="form-label">লিঙ্গ <span class="req">*</span></label>
                                <div class="gender-row">
                                    <label class="radio-label"><input type="radio" name="gender" value="male" {{ old('gender', 'male') == 'male' ? 'checked' : '' }}> পুরুষ</label>
                                    <label class="radio-label"><input type="radio" name="gender" value="female" {{ old('gender') == 'female' ? 'checked' : '' }}> মহিলা</label>
                                </div>
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="form-label">নাম <span class="req">*</span></label>
                            <input type="text" name="name_bn" class="form-control" value="{{ old('name_bn') }}" placeholder="পুরো নাম লিখুন">
                        </div>

                        <div class="field-group">
                            <label class="form-label">পিতার নাম</label>
                            <input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}" placeholder="পিতার নাম লিখুন">
                        </div>

                        <div class="field-group">
                            <label class="form-label">মাতার নাম</label>
                            <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name') }}" placeholder="মাতার নাম লিখুন">
                        </div>
                    </div>

                    {{-- MIDDLE COLUMN --}}
                    <div class="col-fields">
                        <div class="field-group">
                            <label class="form-label">রক্তের গ্রুপ</label>
                            <select name="blood_group" class="form-select">
                                <option value="">নির্বাচন করুন</option>
                                @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                                    <option value="{{ $bg }}" {{ old('blood_group') == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mini-grid-2">
                            <div class="field-group">
                                <label class="form-label">জন্ম তারিখ</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                            </div>
                            <div class="field-group">
                                <label class="form-label">বয়স</label>
                                <input type="number" name="age" id="age" class="form-control" readonly style="background:#eef2fb;" placeholder="Auto">
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="form-label">NID/জন্ম নিবন্ধন নং</label>
                            <input type="text" name="nid" class="form-control" value="{{ old('nid') }}" placeholder="নম্বর লিখুন">
                        </div>

                        <div class="field-group">
                            <label class="form-label">মোবাইল 1 <span class="req">*</span></label>
                            <div class="phone-group">
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="০১XXXXXXXXX" required>
                                <select name="phone_relation" class="form-select">
                                    <option value="">সম্পর্ক</option>
                                    <option value="self"     {{ old('phone_relation') == 'self'     ? 'selected' : '' }}>নিজ</option>
                                    <option value="father"   {{ old('phone_relation') == 'father'   ? 'selected' : '' }}>পিতা</option>
                                    <option value="mother"   {{ old('phone_relation') == 'mother'   ? 'selected' : '' }}>মাতা</option>
                                    <option value="guardian" {{ old('phone_relation') == 'guardian' ? 'selected' : '' }}>অভিভাবক</option>
                                </select>
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="form-label">মোবাইল 2</label>
                            <div class="phone-group">
                                <input type="text" name="phone2" class="form-control" value="{{ old('phone2') }}" placeholder="০১XXXXXXXXX">
                                <select name="phone2_relation" class="form-select">
                                    <option value="">সম্পর্ক</option>
                                    <option value="self">নিজ</option>
                                    <option value="father">পিতা</option>
                                    <option value="mother">মাতা</option>
                                    <option value="guardian">অভিভাবক</option>
                                </select>
                            </div>
                        </div>

                        <div class="field-group">
                            <label class="form-label">ই-মেইল</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="example@mail.com">
                        </div>
                    </div>

                    {{-- RIGHT COLUMN (Address) --}}
                    <div class="address-col">
                        <div class="address-card">
                            <div class="address-card-header">
                                <div class="address-card-title"><i class="fas fa-map-marker-alt"></i> স্থায়ী ঠিকানা</div>
                            </div>
                            <div class="addr-fields">
                                <div class="addr-mini-grid">
                                    <div class="field-group">
                                        <select name="permanent_division_id" id="permanent_division" class="form-select">
                                            <option value="">বিভাগ</option>
                                            @foreach($divisions as $div)
                                                <option value="{{ $div->id }}" {{ old('permanent_division_id') == $div->id ? 'selected' : '' }}>{{ $div->name_bn }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field-group">
                                        <select name="permanent_district_id" id="permanent_district" class="form-select">
                                            <option value="">জেলা</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="addr-mini-grid">
                                    <div class="field-group">
                                        <select name="permanent_upazila_id" id="permanent_upazila" class="form-select">
                                            <option value="">থানা</option>
                                        </select>
                                    </div>
                                    <div class="field-group">
                                        <input type="text" name="permanent_post_office" class="form-control" value="{{ old('permanent_post_office') }}" placeholder="ডাকঘর">
                                    </div>
                                </div>
                                <div class="field-group">
                                    <input type="text" name="permanent_village_road" class="form-control" value="{{ old('permanent_village_road') }}" placeholder="গ্রাম/রাস্তা/বাসা নং">
                                </div>
                            </div>
                        </div>

                        <div class="address-card">
                            <div class="address-card-header">
                                <div class="address-card-title"><i class="fas fa-map-marker-alt"></i> অস্থায়ী ঠিকানা</div>
                                <label class="same-check-label"><input type="checkbox" id="same_as_permanent"> স্থায়ী ঠিকানার মতই</label>
                            </div>
                            <div class="addr-fields">
                                <div class="addr-mini-grid">
                                    <div class="field-group">
                                        <select name="present_division_id" id="present_division" class="form-select">
                                            <option value="">বিভাগ</option>
                                            @foreach($divisions as $div)
                                                <option value="{{ $div->id }}" {{ old('present_division_id') == $div->id ? 'selected' : '' }}>{{ $div->name_bn }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field-group">
                                        <select name="present_district_id" id="present_district" class="form-select">
                                            <option value="">জেলা</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="addr-mini-grid">
                                    <div class="field-group">
                                        <select name="present_upazila_id" id="present_upazila" class="form-select">
                                            <option value="">থানা</option>
                                        </select>
                                    </div>
                                    <div class="field-group">
                                        <input type="text" name="present_post_office" class="form-control" value="{{ old('present_post_office') }}" placeholder="ডাকঘর">
                                    </div>
                                </div>
                                <div class="field-group">
                                    <input type="text" name="present_village_road" class="form-control" value="{{ old('present_village_road') }}" placeholder="গ্রাম/রাস্তা/বাসা নং">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="reg-card-footer">
                    <a href="{{ route('dashboard.user.index') }}" class="btn-cancel">বাতিল করুন</a>
                    <button type="submit" class="btn-save"><i class="fas fa-save"></i> সেভ করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {

    // ─── Age calculate ───────────────────────────────────────────────────────
    function calculateAge() {
        var birthDate = $('#date_of_birth').val();
        if (!birthDate) { $('#age').val(''); return; }

        var today = new Date();
        var birth = new Date(birthDate);

        if (birth > today) {
            $('#age').val('');
            alert('জন্ম তারিখ ভবিষ্যতের হতে পারে না!');
            $('#date_of_birth').val('');
            return;
        }

        var age = today.getFullYear() - birth.getFullYear();
        var m   = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
        if (age < 0) age = 0;
        $('#age').val(age);
    }

    $('#date_of_birth').on('change', calculateAge);
    if ($('#date_of_birth').val()) calculateAge();

    // ─── User type → role_id set & Auto fill ID ─────────────────────────────

var roleMap = {
    'student': 5,
    'teacher': 4,
    'guardian': 6,
    'madrasa-admin': 3,
    'soft-admin': 2,
    'super-admin': 1
};


    // আইডি অটো ফিল করার ফাংশন
    function autoFillId(userType, institutionId) {
        if (!userType) {
            $('#custom_id').val('');
            $('#custom_id').attr('placeholder', 'প্রথমে ইউজার ধরণ নির্বাচন করুন');
            $('#id_hint').text('');
            return;
        }
        
        // লোডিং দেখান
        $('#custom_id').val('Loading...');
        
        $.ajax({
            url: '/dashboard/user/preview-id',
            type: 'POST',
            data: {
                user_type: userType,
                madrasa_id: institutionId,
                _token: $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
            },
            dataType: 'json',
            success: function(response) {
                console.log('AJAX Success:', response);
                if (response.success && response.preview_id) {
                    $('#custom_id').val(response.preview_id);
                } else if (response.preview_id) {
                    $('#custom_id').val(response.preview_id);
                } else {
                    // ডিফল্ট মান
                    let defaultId = '101';
                    if (userType === 'student') defaultId = '101';
                    else if (userType === 'teacher') defaultId = '201';
                    else if (userType === 'guardian') defaultId = '301';
                    else if (userType === 'madrasa-admin') defaultId = '401';
                    $('#custom_id').val(defaultId);
                }
                
                // হিন্ট টেক্সট দেখান
                let hintText = '';
                switch(userType) {
                    case 'student': hintText = 'শিক্ষার্থী আইডি রেঞ্জ: 101-199'; break;
                    case 'teacher': hintText = 'শিক্ষক আইডি রেঞ্জ: 201-299'; break;
                    case 'guardian': hintText = 'অভিভাবক আইডি রেঞ্জ: 301-399'; break;
                    case 'madrasa-admin': hintText = 'অ্যাডমিন আইডি রেঞ্জ: 401-499'; break;
                    default: hintText = '';
                }
                $('#id_hint').text(hintText);
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr, status, error);
                
                // এরর হলেও ডিফল্ট মান দেখান
                let defaultId = '101';
                if (userType === 'student') defaultId = '101';
                else if (userType === 'teacher') defaultId = '201';
                else if (userType === 'guardian') defaultId = '301';
                else if (userType === 'madrasa-admin') defaultId = '401';
                $('#custom_id').val(defaultId);
                $('#id_hint').text('ডিফল্ট ফরম্যাট ব্যবহার করা হচ্ছে');
            }
        });
    }

    // ইউজার টাইপ পরিবর্তন ইভেন্ট
    $('#user_type').on('change', function () {
        var userType = $(this).val();
        var institutionId = $('#madrasa_id').val() || 1;
        
        $('#role_id').val(roleMap[userType] || '');
        
        if (userType) {
            autoFillId(userType, institutionId);
        } else {
            $('#custom_id').val('');
            $('#custom_id').attr('placeholder', 'প্রথমে ইউজার ধরণ নির্বাচন করুন');
            $('#id_hint').text('');
        }
    });
    
    // পেজ লোড হলে ট্রিগার করুন (যদি ইতিমধ্যে সিলেক্ট করা থাকে)
    if ($('#user_type').val()) {
        $('#user_type').trigger('change');
    }

    // প্রতিষ্ঠান পরিবর্তন হলে আইডি আপডেট
    $('#madrasa_id').on('change', function() {
        var userType = $('#user_type').val();
        if (userType) {
            autoFillId(userType, $(this).val());
        }
    });

    // ─── Address AJAX ────────────────────────────────────────────────────────
    function loadDistricts(divisionId, districtSelect, upazilaSelect) {
        $(districtSelect).html('<option value="">জেলা নির্বাচন করুন</option>');
        $(upazilaSelect).html('<option value="">থানা নির্বাচন করুন</option>');
        if (!divisionId) return;
        $.getJSON('/get-districts/' + divisionId, function (data) {
            $.each(data, function (k, v) {
                $(districtSelect).append('<option value="' + v.id + '">' + v.name_bn + '</option>');
            });
        });
    }

    function loadUpazilas(districtId, upazilaSelect) {
        $(upazilaSelect).html('<option value="">থানা নির্বাচন করুন</option>');
        if (!districtId) return;
        $.getJSON('/get-upazilas/' + districtId, function (data) {
            $.each(data, function (k, v) {
                $(upazilaSelect).append('<option value="' + v.id + '">' + v.name_bn + '</option>');
            });
        });
    }

    $('#permanent_division').on('change', function () {
        loadDistricts($(this).val(), '#permanent_district', '#permanent_upazila');
    });
    $('#permanent_district').on('change', function () {
        loadUpazilas($(this).val(), '#permanent_upazila');
    });
    $('#present_division').on('change', function () {
        loadDistricts($(this).val(), '#present_district', '#present_upazila');
    });
    $('#present_district').on('change', function () {
        loadUpazilas($(this).val(), '#present_upazila');
    });

    // ─── Same address checkbox ───────────────────────────────────────────────
    $('#same_as_permanent').on('change', function () {
        if (!$(this).is(':checked')) return;

        var pDiv = $('#permanent_division').val();
        if (pDiv) {
            $('#present_division').val(pDiv).trigger('change');
            setTimeout(function () {
                $('#present_district').val($('#permanent_district').val()).trigger('change');
                setTimeout(function () {
                    $('#present_upazila').val($('#permanent_upazila').val());
                }, 500);
            }, 500);
        }
        $('input[name="present_post_office"]').val($('input[name="permanent_post_office"]').val());
        $('input[name="present_village_road"]').val($('input[name="permanent_village_road"]').val());
    });
    
    if ($('#permanent_division').val()) {
        $('#permanent_division').trigger('change');
    }
    if ($('#present_division').val()) {
        $('#present_division').trigger('change');
    }
});
</script>
@endpush

@endsection