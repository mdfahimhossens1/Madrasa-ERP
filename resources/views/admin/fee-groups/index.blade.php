
@extends('layouts.admin')
@section('title', 'ছাত্র ফি গ্রুপ')
@section('page')

<div class="container-fluid">

    {{-- ফিল্টার বার --}}
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">ছাত্র ফি গ্রুপ</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('dashboard.fees.store') }}" class="row g-2">
                @csrf
                {{-- ফান্ড --}}
                <div class="col-md-3">
                    <label class="form-label">ফান্ড</label>
                    <select name="fund_id"
                            id="fund_id"
                            class="form-select form-select-sm">

                        <option value="">নির্বাচন করুন</option>

                        @foreach($funds as $fund)
                            <option value="{{ $fund->id }}">
                                {{ $fund->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- জেনারেল লেজার --}}
                <div class="col-md-3">
                    <label class="form-label">জেনারেল লেজার</label>
                    <select name="ledger_id"
                            id="ledger_id"
                            class="form-select form-select-sm">

                        <option value="">প্রথমে ফান্ড নির্বাচন করুন</option>
                    </select>
                </div>

                {{-- সাব লেজার (যোগ করা হলো) --}}
                <div class="col-md-3">
                    <label class="form-label">সাব লেজার</label>
                    <select name="sub_ledger_id"
                            id="sub_ledger_id"
                            class="form-select form-select-sm">

                        <option value="">প্রথমে লেজার নির্বাচন করুন</option>
                    </select>
                </div>

                {{-- ফি ধরণ --}}
                <div class="col-md-3">
                    <label class="form-label">ধরন</label>

                    <select name="type" class="form-select form-select-sm">
                        <option value="">নির্বাচন করুন</option>

                        <option value="ekkalin"
                            {{ request('type') == 'ekkalin' ? 'selected' : '' }}>
                            এককালীন (ভর্তি সময়)
                        </option>

                        <option value="monthly"
                            {{ request('type') == 'monthly' ? 'selected' : '' }}>
                            মাসিক (১২ মাসের জন্য)
                        </option>

                        <option value="others"
                            {{ request('type') == 'others' ? 'selected' : '' }}>
                            অন্যান্য
                        </option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end mt-3">
                    <button type="submit" class="btn btn-success btn-sm me-3"> <i class="fas fa-save"></i> Save </button>
                    <a href="{{ route('dashboard.fees.index') }}" class="btn btn-secondary btn-sm">রিসেট</a>
                </div>
            </form>
        </div>
    </div>

    {{-- ফি টেবিল --}}
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ফান্ড</th> 
                        <th>জেনারেল লেজার</th> 
                        <th>সাব লেজার</th> 
                        <th>ধরন</th> 
                        <th width="100">অ্যাকশন</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feeGroups as $fee) 
                    <tr> 
                        {{-- Fund --}} 
                        <td> 
                            {{ $fee->fund->name ?? '-' }} 
                        </td> 
                        {{-- Ledger --}} 

                        <td> {{ $fee->ledger->name ?? '-' }} </td> 
                        {{-- Sub Ledger --}}
                         <td> 
                            {{ $fee->subLedger->name ?? '-' }} 
                        </td> 
                        {{-- Type --}} 
                        <td> @if($fee->type == 'ekkalin') এককালীন @elseif($fee->type == 'monthly') মাসিক @else অন্যান্য @endif 

                        </td> {{-- Action --}} <td> 
                            <a href="{{ route('dashboard.fees.edit', $fee) }}" class="btn btn-sm btn-primary"> <i class="fas fa-edit"></i> </a> 
                            <form action="{{ route('dashboard.fees.destroy', $fee) }}" method="POST" class="d-inline" onsubmit="return confirm('মুছে ফেলতে নিশ্চিত?')"> @csrf @method('DELETE') 
                                <button class="btn btn-sm btn-danger"> <i class="fas fa-trash"></i> </button> </form> </td> 
                            </tr> @empty <tr> 
                                <td colspan="5" class="text-center py-4"> কোনো ডাটা পাওয়া যায়নি। 

                                </td> 
                            </tr> @endforelse
                </tbody>
            </table>
        </div>
        @if($feeGroups->hasPages())
        <div class="card-footer">{{ $feeGroups->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>

    // ================================
    // FUND -> LEDGER
    // ================================
    $('#fund_id').on('change', function () {

        let fund_id = $(this).val();

        $('#ledger_id').html(
            '<option value="">লোড হচ্ছে...</option>'
        );

        $('#sub_ledger_id').html(
            '<option value="">প্রথমে লেজার নির্বাচন করুন</option>'
        );

        if (fund_id != '') {

            $.ajax({
                url: '/dashboard/fees/get-ledgers/' + fund_id,
                type: 'GET',

                success: function (data) {

                    let html =
                        '<option value="">নির্বাচন করুন</option>';

                    $.each(data, function (key, item) {

                        html += `
                            <option value="${item.id}">
                                ${item.name}
                            </option>
                        `;
                    });

                    $('#ledger_id').html(html);
                },

                error: function () {

                    $('#ledger_id').html(
                        '<option value="">কোনো লেজার পাওয়া যায়নি</option>'
                    );
                }
            });

        } else {

            $('#ledger_id').html(
                '<option value="">প্রথমে ফান্ড নির্বাচন করুন</option>'
            );
        }
    });





    // ================================
    // LEDGER -> SUB LEDGER
    // ================================
    $('#ledger_id').on('change', function () {

        let ledger_id = $(this).val();

        $('#sub_ledger_id').html(
            '<option value="">লোড হচ্ছে...</option>'
        );

        if (ledger_id != '') {

            $.ajax({
                url: '/dashboard/fees/get-sub-ledgers/' + ledger_id,
                type: 'GET',

                success: function (data) {

                    let html =
                        '<option value="">নির্বাচন করুন</option>';

                    $.each(data, function (key, item) {

                        html += `
                            <option value="${item.id}">
                                ${item.name}
                            </option>
                        `;
                    });

                    $('#sub_ledger_id').html(html);
                },

                error: function () {

                    $('#sub_ledger_id').html(
                        '<option value="">কোনো সাব লেজার পাওয়া যায়নি</option>'
                    );
                }
            });

        } else {

            $('#sub_ledger_id').html(
                '<option value="">প্রথমে লেজার নির্বাচন করুন</option>'
            );
        }
    });

</script>
@endsection