@extends('layouts.admin')

@section('page')

<div class="row">

    <div class="col-md-3">

        @include('admin.fee-report.partials.filters')

    </div>

    <div class="col-md-9">

        @if(isset($reports))
            @include('admin.fee-report.partials.report')
        @endif

    </div>

</div>

@endsection