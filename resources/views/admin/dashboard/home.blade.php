@extends('layouts.admin')
@section('page')

<div class="row g-3">

  {{-- মোট শিক্ষার্থী --}}
  <div class="col-md-4 col-6">
    <div class="kpi kpi-green">
      <div class="kpi-body">
        <p class="kpi-title">মোট শিক্ষার্থী</p>
        <div class="kpi-value"></div>
      </div>
    </div>
  </div>

  {{-- আজকের উপস্থিতি --}}
  <div class="col-md-4 col-6">
    <div class="kpi kpi-blue">
      <div class="kpi-body">
        <p class="kpi-title">আজকের উপস্থিতি</p>
        <div class="kpi-value"></div>
      </div>
    </div>
  </div>

  {{-- বকেয়া ফি --}}
  <div class="col-md-4 col-6">
    <div class="kpi kpi-orange">
      <div class="kpi-body">
        <p class="kpi-title">বকেয়া ফি</p>
        <div class="kpi-value"></div>
      </div>
    </div>
  </div>

</div>

{{-- ===================
  Recent Admissions (Quick Actions)
=================== --}}
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5>সাম্প্রতিক ভর্তি শিক্ষার্থী</h5>
      </div>
      <div class="card-body">

      </div>
    </div>
  </div>
</div>

{{-- ===================
  Monthly Attendance Chart
=================== --}}
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header">মাসিক উপস্থিতি</div>
      <div class="card-body">
        <canvas id="attendanceChart" height="100"></canvas>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ,
        datasets: [{
            label: 'উপস্থিতি',
            data:,
            fill: true,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59,130,246,0.1)',
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
@endsection