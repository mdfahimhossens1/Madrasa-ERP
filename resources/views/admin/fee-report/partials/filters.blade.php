<form action="{{ route('dashboard.fee-report.index') }}" method="GET">

    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white">
            <strong>Fee Report</strong>
        </div>

        <div class="card-body">

            {{-- Report Type --}}
            <div class="mb-3">
                <label class="form-label fw-bold">রিপোর্ট :</label>

                <select
                    name="report_type"
                    id="report_type"
                    class="form-select"
                    required>

                    <option value="">-- রিপোর্ট নির্বাচন করুন --</option>

                    @foreach($reportTypes as $key => $value)
                        <option
                            value="{{ $key }}"
                            {{ ($filters['report_type'] ?? '') == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach

                </select>
            </div>

            {{-- Academic Year --}}
            <div class="mb-3">

                <label class="form-label fw-bold">
                    শিক্ষা বর্ষ :
                </label>

                <select
                    name="academic_year_id"
                    class="form-select"
                    required>

                    <option value="">-- শিক্ষা বর্ষ নির্বাচন করুন --</option>

                    @foreach($academicYears as $year)
                        <option
                            value="{{ $year->id }}"
                            {{ ($filters['academic_year_id'] ?? '') == $year->id ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                    @endforeach

                </select>

            </div>

            {{-- Dynamic Filter --}}
            <div class="mb-3">

                <label
                    class="form-label fw-bold"
                    id="filter_label">

                    নির্বাচন করুন

                </label>

                <select
                    name="filter_id"
                    id="filter_id"
                    class="form-select"
                    required>

                </select>

            </div>

            <button
                type="submit"
                class="btn btn-primary w-100">

                <i class="fas fa-search"></i> Preview

            </button>

        </div>

    </div>

</form>

@push('scripts')

<script>

const users = @json($cashiers->map(fn($c) => [
    'id' => $c->id,
    'name' => $c->name
]));

const classes = @json($classes->map(fn($c) => [
    'id' => $c->id,
    'name' => $c->full_name
]));

const methods = @json($paymentMethods->map(fn($m) => [
    'id' => $m->id,
    'name' => $m->name
]));

const selectedFilter = "{{ $filters['filter_id'] ?? '' }}";

function loadFilter() {

    let type = $('#report_type').val();

    let data = [];
    let label = "নির্বাচন করুন";

    if (type === 'user') {

        data = users;
        label = "ব্যবহারকারী";

    } else if (type === 'class') {

        data = classes;
        label = "ক্লাস";

    } else if (type === 'payment_method') {

        data = methods;
        label = "পেমেন্ট মাধ্যম";

    }

    $('#filter_label').text(label);

    let html = '<option value="">-- নির্বাচন করুন --</option>';

    data.forEach(function(item){

        html += `
            <option value="${item.id}" ${item.id == selectedFilter ? 'selected' : ''}>
                ${item.name}
            </option>
        `;

    });

    $('#filter_id').html(html);

}

$(document).ready(function () {

    loadFilter();

    $('#report_type').on('change', function () {

        $('#filter_id').val('');

        loadFilter();

    });

});

</script>

@endpush