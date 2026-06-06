
@extends('layouts.admin')
@section('title', isset($fee) ? 'ফি সম্পাদনা' : 'নতুন ফি সংযোজন')
@section('page')

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ isset($fee) ? 'ফি সম্পাদনা' : 'নতুন ফি সংযোজন' }}</h5>
        </div>
        <div class="card-body">
            <form method="POST"
                  action="{{ isset($fee) ? route('dashboard.fees.update', $fee) : route('dashboard.fees.store') }}">
                @csrf
                @if(isset($fee)) @method('PUT') @endif

                <div class="row g-3">

                    {{-- ফান্ড --}}
                    <div class="col-md-3">
                        <label class="form-label">ফান্ড <span class="text-danger">*</span></label>
                        <select name="fund_id" class="form-select @error('fund_id') is-invalid @enderror" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach($funds as $fund)
                                <option value="{{ $fund->id }}"
                                    {{ old('fund_id', $fee->fund_id ?? '') == $fund->id ? 'selected' : '' }}>
                                    {{ $fund->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('fund_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- জেনারেল লেজার --}}
                    <div class="col-md-3">
                        <label class="form-label">জেনারেল লেজার <span class="text-danger">*</span></label>
                        <select name="general_ledger_id" id="general_ledger_id"
                                class="form-select @error('general_ledger_id') is-invalid @enderror" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach($ledgers as $ledger)
                                <option value="{{ $ledger->id }}"
                                    {{ old('general_ledger_id', $fee->general_ledger_id ?? '') == $ledger->id ? 'selected' : '' }}>
                                    {{ $ledger->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('general_ledger_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- সাব লেজার --}}
                    <div class="col-md-3">
                        <label class="form-label">সাব লেজার</label>
                        <select name="sub_ledger_id" id="sub_ledger_id" class="form-select">
                            <option value="">নির্বাচন করুন</option>
                            @isset($subLedgers)
                                @foreach($subLedgers as $sl)
                                    <option value="{{ $sl->id }}"
                                        {{ old('sub_ledger_id', $fee->sub_ledger_id ?? '') == $sl->id ? 'selected' : '' }}>
                                        {{ $sl->name }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    {{-- ধরন --}}
                    <div class="col-md-3">
                        <label class="form-label">ধরন <span class="text-danger">*</span></label>
                        <select name="fee_type_id" class="form-select @error('fee_type_id') is-invalid @enderror" required>
                            <option value="">নির্বাচন করুন</option>
                            @foreach($feeTypes as $type)
                                <option value="{{ $type->id }}"
                                    {{ old('fee_type_id', $fee->fee_type_id ?? '') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('fee_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- ফি-এর নাম --}}
                    <div class="col-md-4">
                        <label class="form-label">ফি-এর নাম <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               value="{{ old('name', $fee->name ?? '') }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="যেমন: ভর্তি ফি, বেতন" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- পরিমাণ --}}
                    <div class="col-md-3">
                        <label class="form-label">ডিফল্ট পরিমাণ (টাকা)</label>
                        <input type="number" name="amount" step="0.01" min="0"
                               value="{{ old('amount', $fee->amount ?? 0) }}"
                               class="form-control @error('amount') is-invalid @enderror">
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    @isset($fee)
                    {{-- স্ট্যাটাস (শুধু edit এ) --}}
                    <div class="col-md-2">
                        <label class="form-label">স্ট্যাটাস</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', $fee->is_active) == 1 ? 'selected' : '' }}>সক্রিয়</option>
                            <option value="0" {{ old('is_active', $fee->is_active) == 0 ? 'selected' : '' }}>নিষ্ক্রিয়</option>
                        </select>
                    </div>
                    @endisset

                    {{-- বিবরণ --}}
                    <div class="col-12">
                        <label class="form-label">বিবরণ</label>
                        <textarea name="description" rows="2" class="form-control"
                                  placeholder="ঐচ্ছিক বিবরণ">{{ old('description', $fee->description ?? '') }}</textarea>
                    </div>

                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> সংরক্ষণ করুন
                    </button>
                    <a href="{{ route('dashboard.fees.index') }}" class="btn btn-secondary ms-2">বাতিল</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// জেনারেল লেজার পরিবর্তন হলে সাব লেজার লোড
document.getElementById('general_ledger_id').addEventListener('change', function () {
    const ledgerId = this.value;
    const subLedgerSelect = document.getElementById('sub_ledger_id');

    subLedgerSelect.innerHTML = '<option value="">নির্বাচন করুন</option>';

    if (!ledgerId) return;

    fetch(`/fees/sub-ledgers?general_ledger_id=${ledgerId}`)
        .then(r => r.json())
        .then(data => {
            data.forEach(sl => {
                const opt = document.createElement('option');
                opt.value = sl.id;
                opt.textContent = sl.name;
                subLedgerSelect.appendChild(opt);
            });
        });
});
</script>
@endpush