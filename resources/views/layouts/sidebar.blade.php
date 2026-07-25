{{-- SIDEBAR --}}
<aside class="admin-sidebar bg-dark text-white" id="adminSidebar">

    <div class="sidebar-brand p-2 border-secondary">
        <div class="sidebar_flex d-flex align-items-center">
            <div class="logo">
                <a href="{{ route('dashboard.index') }}" class="text-white text-decoration-none d-flex align-items-center gap-2">
                    <img src="{{asset('contents/admin/images/logo.png')}}" alt="">
                </a>
            </div>
            @php
                $institutionName = optional(auth()->user()->institution)->name ?? 'System';
                $words = explode(' ', $institutionName);
                $firstPart = implode(' ', array_slice($words,0,1));
                $secondPart = implode(' ', array_slice($words,1));
            @endphp
            <div class="content text-secondary small mt-1">
                <h3>{{ $firstPart }}</h3>
                <p>{{ $secondPart }}</p>
            </div>
        </div>
    </div>

    <div class="sidebar-body">
        <ul class="nav flex-column gap-1" id="sidebarNav">

    
            @include('admin.sidebar.dashboard')
            @include('admin.sidebar.student')
            @include('admin.sidebar.teacher')
            @include('admin.sidebar.attendance')
            @include('admin.sidebar.exam')
            @include('admin.sidebar.result')
            @include('admin.sidebar.accounting')
            @include('admin.sidebar.payment')
            @include('admin.sidebar.settings')
            @include('admin.sidebar.help')
            @include('admin.sidebar.system')

        </ul>
    </div>
</aside>