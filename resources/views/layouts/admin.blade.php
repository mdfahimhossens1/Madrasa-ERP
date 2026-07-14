<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Admin Panel')</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css">
  <link rel="stylesheet" href="{{ asset('contents/admin/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('contents/admin/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('contents/admin/css/datatables.min.css') }}">
  <link rel="stylesheet" href="{{ asset('contents/admin/css/style.css') }}">

  <style>
    :root{
      --sidebar-w: 260px;
      --topbar-h: 68px;
      --border: rgba(0,0,0,.06);
      --bg: #f6f7fb;
    }

    body{ background: var(--bg); }

    .admin-layout{ min-height:100vh; }

    .admin-sidebar{
      width: var(--sidebar-w);
      height: 100vh;
      position: fixed !important;
      top: 0;
      left: 0;
      z-index: 1000;
      display:flex;
      flex-direction:column;
      transition: transform .25s ease;
      border-right: 1px solid #0000001a;
    }

    .sidebar-brand{
      height: 84px;
      flex: 0 0 auto;
    }

    .sidebar-body{
      flex: 1 1 auto;
      overflow-y:auto;
      padding: .5rem;
    }
    .sidebar-body::-webkit-scrollbar{ width:8px; }
    .sidebar-body::-webkit-scrollbar-thumb{ background: rgba(255,255,255,.18); border-radius: 999px; }

    /* Main area offset */
    .admin-main{
      margin-left: var(--sidebar-w);
      min-height: 100vh;
      transition: margin-left .25s ease;
    }

    /* Topbar */
    .admin-topbar{
      height: var(--topbar-h);
      position: fixed !important;
      top: 0;
      left: var(--sidebar-w);
      right: 0;
      z-index: 900;
      border-bottom:1px solid var(--border);
      background:#fff;
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding: 0 16px;
      transition: left .25s ease;
    }

    .page-wrap{
      padding: calc(var(--topbar-h) + 25px) 18px 0px 18px;
    }

    /* Sidebar link base */
    .admin-sidebar .nav-link {
        transition: 0.3s;
        border-radius: 6px;
    }

    /* Hover effect */
    .admin-sidebar .nav-link:hover {
        background-color: #1A3375;
        color: #fff !important;
    }

    /* Active menu */
    .admin-sidebar .nav-link.active {
        background-color: #1A3375;
        color: #fff !important;
    }

    /* Sub menu hover */
    .admin-sidebar .nav .nav-link.text-white-50:hover {
        background-color: #1A3375;
        color: #fff !important;
    }

    /* Sub menu active */
    .admin-sidebar .nav .nav-link.text-white-50.active {
        background-color: #1A3375;
        color: #fff !important;
    }

    .btn-icon{
      width:40px; height:40px;
      display:inline-flex;
      align-items:center; justify-content:center;
      border-radius:10px;
      border:1px solid var(--border);
      background:#fff;
    }

    .user-dd-btn{
      border:0;
      background: transparent;
      display:flex;
      align-items:center;
      gap:10px;
      padding:6px 10px;
      border-radius: 12px;
    }
    .user-dd-btn:hover{ background: rgba(0,0,0,.04); }

    .avatar{
      width:38px; height:38px;
      border-radius: 999px;
      display:inline-flex;
      align-items:center; justify-content:center;
      background:#111827;
      color:#fff;
      flex: 0 0 auto;
    }
    .user-meta{ line-height:1.1; text-align:left; }
    .user-meta .name{ font-weight:700; font-size:13px; color:#111827; }
    .user-meta .role{ font-size:12px; color:#6b7280; }

    /* Desktop collapsed */
    body.sidebar-collapsed .admin-sidebar{
      transform: translateX(-100%) !important;
    }
    body.sidebar-collapsed .admin-main{ margin-left: 0 !important; }
    body.sidebar-collapsed .admin-topbar{ left: 0 !important; }

    /* Backdrop (mobile overlay) */
    #sidebarBackdrop{
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.35);
      z-index: 950;
      display: none;
    }
    body.sidebar-open #sidebarBackdrop{ display:block; }

    /* Mobile overlay behavior */
    @media (max-width: 991.98px){
      .admin-main{ margin-left: 0 !important; }
      .admin-topbar{ left: 0 !important; }

      .admin-sidebar{ transform: translateX(-100%);     position: fixed !important;
    top: 66px !important;
    left: 0 !important;}
      body.sidebar-open .admin-sidebar{ transform: translateX(0); }
    }
    /* Light (default) */
:root{
  --bg: #f6f7fb;
  --card: #ffffff;
  --text: #111827;
  --muted: #6b7280;
  --border: rgba(0,0,0,.06);
  --topbar: #ffffff;
  --sidebar: #111827;
}

/* Dark mode overrides */
body.dark-mode{
  --bg: #0b1220;
  --card: #0f172a;
  --text: #e5e7eb;
  --muted: #9ca3af;
  --border: rgba(255,255,255,.08);
  --topbar: #0f172a;
  --sidebar: #0b1220;
}

/* Apply variables */
body{ background: var(--bg); color: var(--text); }

.admin-topbar{ background: var(--topbar) !important; border-bottom: 1px solid var(--border) !important; }
.admin-sidebar{ background: #F6F7FB !important;}

.card{ background: var(--card) !important; border-color: var(--border) !important; }
.card-header{ border-bottom: 1px solid var(--border) !important; }

.text-dark{ color: var(--text) !important; }
.text-muted{ color: var(--muted) !important; }

  </style>
</head>

<body>
@php
  $authUser = Auth::user();
  $role = optional($authUser->role)->slug;
  $routeName = request()->route()?->getName() ?? '';

  $active = fn($name) => $routeName === $name ? 'active' : '';
  $open   = fn($prefix) => str_starts_with($routeName, $prefix) ? 'show' : '';
  $aria   = fn($prefix) => str_starts_with($routeName, $prefix) ? 'true' : 'false';
@endphp

<div class="admin-layout">

  {{-- Backdrop for mobile --}}
  <div id="sidebarBackdrop"></div>

  {{-- SIDEBAR --}}
  <aside class="admin-sidebar bg-dark text-white" id="adminSidebar">

    <div class="sidebar-brand p-2 border-secondary">
      <div class="sidebar_flex d-flex align-items-center">
          <div class="logo">
            <a href="{{ route('dashboard') }}" class="text-white text-decoration-none d-flex align-items-center gap-2">
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

    {{-- Dashboard --}}

    <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" data-menu="dashboard"
           data-bs-toggle="collapse" 
           data-bs-target="#dashboardMenu"
           data-parent="dashboard">
            <span><i class="fas fa-home me-2"></i> ড্যাশবোর্ড</span>
            <i class="fas fa-chevron-down"></i>
        </a>

        <div class="collapse {{ $open('dashboard.') }}" id="dashboardMenu">
            <ul class="nav flex-column ms-3">
                <li><a class="nav-link text-white-50" href="{{route('dashboard.user.create')}}" data-submenu="dashboard">নতুন ব্যবহারকারী তৈরি</a></li>
                <li><a class="nav-link text-white-50" href="{{route('dashboard.institutions.index')}}" data-submenu="dashboard">প্রতিষ্ঠানের তথ্য</a></li>
                <li><a class="nav-link text-white-50" href="{{route('dashboard.month.index')}}" data-submenu="dashboard">মাস সেটিং</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="dashboard">ব্যাবহার কারী ছবি</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="dashboard">ব্যাবহার কারী রিপোর্ট</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="student">ব্যাবহার কারী পারমিশন</a></li>

            </ul>
        </div>
    </li>

    {{-- ================= STUDENT ================= --}}
    <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between {{ request()->routeIs('student.*') ? 'active' : '' }}" 
           data-bs-toggle="collapse" 
           data-bs-target="#studentMenu"
           data-parent="student">
            <span><i class="fas fa-user-graduate me-2"></i> শিক্ষার্থী</span>
            <i class="fas fa-chevron-down"></i>
        </a>

        <div class="collapse {{ $open('student.') }}" id="studentMenu">
            <ul class="nav flex-column ms-3">
                <li><a class="nav-link text-white-50" href="{{route('dashboard.academic-years.index')}}" data-submenu="student">শিক্ষাবর্ষ</a></li>
                <li><a class="nav-link text-white-50" href="{{route('dashboard.classes.index')}}" data-submenu="student">ক্লাস</a></li>
                <li><a class="nav-link text-white-50" href="{{route('dashboard.sections.index')}}" data-submenu="student">ক্লাস গ্রুপ</a></li>
                <li><a class="nav-link text-white-50" href="{{route('dashboard.admissions.index')}}" data-submenu="student">শিক্ষার্থী ভর্তি</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="student">কিতাব/সাবজেক্ট</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="student">শিক্ষার্থী গ্রুপ</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="student">আইডি কার্ড</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="student">ডাটা এক্সপোর্ট</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="student">শিক্ষার্থী রিপোর্ট</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="student">সার্টিফিকেট</a></li>
            </ul>
        </div>
    </li>

    {{-- ================= TEACHER ================= --}}
    <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between"
           data-bs-toggle="collapse"
           data-bs-target="#teacherMenu"
           data-parent="teacher">
            <span><i class="fas fa-chalkboard-teacher me-2"></i> শিক্ষক</span>
            <i class="fas fa-chevron-down"></i>
        </a>

        <div class="collapse {{ $open('teacher.') }}" id="teacherMenu">
            <ul class="nav flex-column ms-3">
                <li><a class="nav-link text-white-50" href="#" data-submenu="teacher">শিক্ষক তথ্য</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="teacher">যোগদান তথ্য</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="teacher">ক্লাস এসাইন</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="teacher">বেতন সেটিং</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="teacher">বেতন প্রদান</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="teacher">পারফরমেন্স</a></li>
            </ul>
        </div>
    </li>

    {{-- ================= ATTENDANCE ================= --}}
    <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between"
           data-bs-toggle="collapse"
           data-bs-target="#attendanceMenu"
           data-parent="attendance">
            <span><i class="fas fa-calendar-check me-2"></i> হাজিরা</span>
            <i class="fas fa-chevron-down"></i>
        </a>

        <div class="collapse {{ $open('attendance.') }}" id="attendanceMenu">
            <ul class="nav flex-column ms-3">
                <li><a class="nav-link text-white-50" href="#" data-submenu="attendance">ম্যানুয়াল হাজিরা</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="attendance">অনলাইন হাজিরা</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="attendance">বায়োমেট্রিক</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="attendance">লাইভ ড্যাশবোর্ড</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="attendance">SMS সেটিং</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="attendance">হাজিরা রিপোর্ট</a></li>
            </ul>
        </div>
    </li>

    {{-- ================= EXAM ================= --}}
    <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between"
           data-bs-toggle="collapse"
           data-bs-target="#examMenu"
           data-parent="exam">
            <span><i class="fas fa-file-alt me-2"></i> পরীক্ষা</span>
            <i class="fas fa-chevron-down"></i>
        </a>

        <div class="collapse {{ $open('exam.') }}" id="examMenu">
            <ul class="nav flex-column ms-3">
                <li><a class="nav-link text-white-50" href="#" data-submenu="exam">পরীক্ষার নাম</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="exam">পরীক্ষার ফি</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="exam">কন্ডিশন</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="exam">মেধা কন্ডিশন</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="exam">প্রবেশপত্র</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="exam">রুটিন</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="exam">নিয়ম</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="exam">রিপোর্ট</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="exam">সেটিং</a></li>
            </ul>
        </div>
    </li>

    {{-- ================= RESULT ================= --}}
    <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between"
           data-bs-toggle="collapse"
           data-bs-target="#resultMenu"
           data-parent="result">
            <span><i class="fas fa-chart-line me-2"></i> ফলাফল</span>
            <i class="fas fa-chevron-down"></i>
        </a>

        <div class="collapse {{ $open('result.') }}" id="resultMenu">
            <ul class="nav flex-column ms-3">
                <li><a class="nav-link text-white-50" href="#" data-submenu="result">ফলাফল এন্ট্রি</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="result">Auto Calculation</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="result">GPA System</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="result">Subject Result</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="result">অনলাইন প্রকাশ</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="result">মার্কশিট</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="result">Report</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="result">Analytics</a></li>
            </ul>
        </div>
    </li>

    {{-- ================= ACCOUNTING ================= --}}
    <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between"
           data-bs-toggle="collapse"
           data-bs-target="#accountMenu"
           data-parent="account">
            <span><i class="fas fa-wallet me-2"></i> অ্যাকাউন্টিং</span>
            <i class="fas fa-chevron-down"></i>
        </a>

        <div class="collapse {{ $open('account.') }}" id="accountMenu">
            <ul class="nav flex-column ms-3">
                <li><a class="nav-link text-white-50" href="{{route('dashboard.transactions.index')}}" data-submenu="account">আয়-ব্যয়</a></li>
                <li><a class="nav-link text-white-50" href="{{route('dashboard.reports.income-expense')}}" data-submenu="account">আয়-ব্যয় রিপোর্ট</a></li>
                <li><a class="nav-link text-white-50" href="{{route('dashboard.fees.index')}}" data-submenu="account">ছাত্র ফি গ্রুপ</a></li>
                <li><a class="nav-link text-white-50" href="{{route('dashboard.fee-settings.index')}}" data-submenu="account">ফি সেটিং</a></li>
                <li><a class="nav-link text-white-50" href="{{route('dashboard.fee-collection.index')}}" data-submenu="account">ফি গ্রহণ</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="account">অনুদান</a></li>
                <li><a href="{{ route('dashboard.fee-report.index') }}" class="nav-link text-white-50">ফি রিপোর্ট</li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="account">বকেয়া</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="account">হিস্ট্রি</a></li>
            </ul>
        </div>
    </li>

    {{-- ================= PAYMENT ================= --}}
    <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between"
           data-bs-toggle="collapse"
           data-bs-target="#paymentMenu"
           data-parent="payment">
            <span><i class="fas fa-credit-card me-2"></i> অনলাইন পেমেন্ট</span>
            <i class="fas fa-chevron-down"></i>
        </a>

        <div class="collapse {{ $open('payment.') }}" id="paymentMenu">
            <ul class="nav flex-column ms-3">
                <li><a class="nav-link text-white-50" href="#" data-submenu="payment">সাবস্ক্রিপশন</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="payment">স্টুডেন্ট পেমেন্ট</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="payment">পেমেন্ট হিস্ট্রি</a></li>
            </ul>
        </div>
    </li>

    {{-- ================= SETTINGS ================= --}}
    <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between"
           data-bs-toggle="collapse"
           data-bs-target="#settingsMenu"
           data-parent="settings">
            <span><i class="fas fa-cog me-2"></i> সেটিংস</span>
            <i class="fas fa-chevron-down"></i>
        </a>

        <div class="collapse {{ $open('settings.') }}" id="settingsMenu">
            <ul class="nav flex-column ms-3">
                <li><a class="nav-link text-white-50" href="#" data-submenu="settings">General Settings</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="settings">SMS API</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="settings">Bulk SMS</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="settings">Notification</a></li>
            </ul>
        </div>
    </li>

    {{-- ================= HELPS ================= --}}
    <li class="nav-item">
        <a class="nav-link text-white d-flex justify-content-between"
           data-bs-toggle="collapse"
           data-bs-target="#helpsMenu"
           data-parent="help">
            <span><i class="fas fa-life-ring me-2"></i> হেল্প</span>
            <i class="fas fa-chevron-down"></i>
        </a>

        <div class="collapse {{ $open('help.') }}" id="helpsMenu">
            <ul class="nav flex-column ms-3">
                <li><a class="nav-link text-white-50" href="#" data-submenu="help">সফটওয়ার ভিডিও</a></li>
                <li><a class="nav-link text-white-50" href="#" data-submenu="help">সাপোর্ট / টিকেট</a></li>
            </ul>
        </div>
    </li>

</ul>
    </div>
  </aside>

  {{-- TOPBAR + MAIN --}}
  <div class="admin-main" id="adminMain">

    <div class="admin-topbar">
      <div class="d-flex align-items-center gap-2">
        <button class="btn-icon" type="button" id="sidebarToggle" title="Toggle menu">
          <i class="fas fa-bars"></i>
        </button>
        @php
    $defaultTitle = optional(auth()->user()->institution)->name ?? 'Dashboard';
    $pageTitle = trim($__env->yieldContent('title'));
    $finalTitle = $pageTitle ?: $defaultTitle;
      @endphp

      <h6 class="mb-0 fw-bold text-dark">
          {{ $finalTitle }}
      </h6>
      </div>

      <div class="d-flex align-items-center gap-2">
        
        {{-- Notifications --}}
        <div class="dropdown">
          <button class="btn-icon position-relative" id="notiBtn" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="far fa-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                  id="notiBadge" style="display:none;">0</span>
          </button>

          <ul class="dropdown-menu dropdown-menu-end p-0" style="width:320px;" id="notiMenu">
            <li class="notification_topbar d-flex justify-content-between align-items-center px-3 py-2">
              <span class="fw-bold">Notification</span>
              <button class="btn btn-link p-0 text-decoration-none" type="button" id="clearAllBtn">Clear All</button>
            </li>
            <li><div class="px-3 py-3 text-muted">No notifications</div></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-center small" href="{{ url('dashboard/notifications') }}">View all</a></li>
          </ul>
        </div>

        {{-- Theme Toggle --}}
<button class="btn-icon me-2" id="themeToggle" type="button" title="Toggle theme">
  <i class="fas fa-moon" id="themeMoon"></i>
  <i class="fas fa-sun d-none" id="themeSun"></i>
</button>

{{-- User Dropdown --}}
<div class="dropdown">

  <button class="user-dd-btn" data-bs-toggle="dropdown" aria-expanded="false">

    <span class="avatar p-0 overflow-hidden">
      @if(!empty($authUser->photo))
        <img src="{{ asset('uploads/users/'.$authUser->photo) }}"
             alt="User"
             style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
      @else
        <i class="fas fa-user"></i>
      @endif
    </span>

    <span class="user-meta d-none d-md-block">
      <span class="name">{{ $authUser->name ?? 'User' }}</span><br>
      <span class="role">
    {{ optional($authUser->role)->role_name }}
</span>
    </span>

    <i class="fas fa-chevron-down text-muted ms-1"></i>
  </button>

  <ul class="dropdown-menu dropdown-menu-end">
    <li>
      <a class="dropdown-item" href="{{ route('dashboard.profile') }}">
        <i class="fas fa-user me-2"></i> Profile
      </a>
    </li>
    <li>
      <a class="dropdown-item" href="{{ route('dashboard.settings.index') }}">
        <i class="fas fa-cog me-2"></i> Manage Settings
      </a>
    </li>
    <li><hr class="dropdown-divider"></li>
    <li>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="dropdown-item">
          <i class="fas fa-sign-out-alt me-2"></i> Logout
        </button>
      </form>
    </li>
  </ul>

</div>


      </div>
    </div>

    <div class="page-wrap">
      @yield('page')
    </div>

  </div>
</div>


{{-- Core --}}
<script src="{{ asset('contents/admin/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('contents/admin/js/bootstrap.bundle.min.js') }}"></script>

{{-- Plugins --}}
<script src="{{ asset('contents/admin/js/datatables.min.js') }}"></script>
<script src="{{ asset('contents/admin/js/chart.js') }}"></script>

{{-- Map --}}
<script src="{{ asset('contents/admin/js/jsvectormap.js') }}"></script>
<script src="{{ asset('contents/admin/js/world-merc.js') }}"></script>

{{-- App (clean custom.js) --}}
<script src="{{ asset('contents/admin/js/custom.js') }}"></script>

{{-- Global layout JS (sidebar + notifications) --}}
<script>
(function () {

  const body = document.body;

  function ready(fn){
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      fn();
    }
  }

  ready(function () {

    /* ===============================
       SIDEBAR TOGGLE
    =============================== */

    const btn = document.getElementById('sidebarToggle');
    const backdrop = document.getElementById('sidebarBackdrop');

    const isMobile = () => window.matchMedia('(max-width: 991.98px)').matches;

    if (localStorage.getItem('sidebarCollapsed') === '1') {
      body.classList.add('sidebar-collapsed');
    }

    btn?.addEventListener('click', function (e) {
      e.preventDefault();

      if (isMobile()) {
        body.classList.toggle('sidebar-open');
      } else {
        body.classList.toggle('sidebar-collapsed');
        localStorage.setItem(
          'sidebarCollapsed',
          body.classList.contains('sidebar-collapsed') ? '1' : '0'
        );
      }
    });

    backdrop?.addEventListener('click', function () {
      body.classList.remove('sidebar-open');
    });

    /* ===============================
       THEME TOGGLE
    =============================== */

    const themeBtn  = document.getElementById('themeToggle');
    const moonIcon  = document.getElementById('themeMoon');
    const sunIcon   = document.getElementById('themeSun');

    function applyTheme(mode){
      if (mode === 'dark') {
        body.classList.add('dark-mode');
        moonIcon?.classList.add('d-none');
        sunIcon?.classList.remove('d-none');
        localStorage.setItem('theme', 'dark');
      } else {
        body.classList.remove('dark-mode');
        moonIcon?.classList.remove('d-none');
        sunIcon?.classList.add('d-none');
        localStorage.setItem('theme', 'light');
      }
    }

    // Load saved theme
    const savedTheme = localStorage.getItem('theme');
    applyTheme(savedTheme === 'dark' ? 'dark' : 'light');

    themeBtn?.addEventListener('click', function(){
      const isDark = body.classList.contains('dark-mode');
      applyTheme(isDark ? 'light' : 'dark');
    });

    /* ===============================
       ACTIVE MENU STATE - Only one menu active at a time
    =============================== */

    // Function to remove active class from all menu items
    function removeAllActiveClasses() {
      document.querySelectorAll('.admin-sidebar .nav-link').forEach(link => {
        link.classList.remove('active');
      });
    }

    // Function to set active menu
    function setActiveMenu(activeLink) {
      removeAllActiveClasses();
      if (activeLink) {
        activeLink.classList.add('active');
      }
    }

    // Handle parent menu clicks (Dashboard, Student, Teacher, etc.)
    const allParentMenus = document.querySelectorAll('.admin-sidebar .nav-item > a.nav-link[data-bs-toggle="collapse"], .admin-sidebar .nav-item > a.nav-link[data-menu="dashboard"]');
    
    allParentMenus.forEach(menu => {
      menu.addEventListener('click', function(e) {
        // For dashboard (no collapse)
        if (!this.hasAttribute('data-bs-toggle')) {
          setActiveMenu(this);
        } else {
          // For menus with submenus, set this as active
          setActiveMenu(this);
        }
      });
    });

    // Handle submenu items clicks
    const allSubmenuItems = document.querySelectorAll('.admin-sidebar .nav .nav-link.text-white-50');
    
    allSubmenuItems.forEach(submenu => {
      submenu.addEventListener('click', function(e) {
      if (this.getAttribute('href') === '#') {
          e.preventDefault(); 
      }
        
        // Find the parent menu item (the main menu of this submenu)
        const parentMenuItem = this.closest('.nav-item').querySelector('a.nav-link[data-bs-toggle="collapse"]');
        
        // Set the parent menu as active
        if (parentMenuItem) {
          setActiveMenu(parentMenuItem);
        } else {
          // If no parent found, set this submenu as active
          setActiveMenu(this);
        }
        
        // You can add your navigation logic here
        console.log('Submenu clicked:', this.textContent);
      });
    });

    /* ===============================
       COLLAPSE BEHAVIOR - Only one open at a time
    =============================== */

    // Get all collapse triggers
    const allCollapseTriggers = document.querySelectorAll('.admin-sidebar .nav-link[data-bs-toggle="collapse"]');
    
    // Store all collapse elements
    const allCollapseElements = {};
    allCollapseTriggers.forEach(trigger => {
      const targetId = trigger.getAttribute('data-bs-target');
      if (targetId) {
        const collapseElement = document.querySelector(targetId);
        if (collapseElement) {
          allCollapseElements[targetId] = collapseElement;
        }
      }
    });

    // Add click handler to each collapse trigger
    allCollapseTriggers.forEach(trigger => {
      trigger.addEventListener('click', function(e) {
        const targetId = this.getAttribute('data-bs-target');
        const currentCollapse = document.querySelector(targetId);
        const isCurrentlyShowing = currentCollapse?.classList.contains('show');
        
        // If we're trying to close the current one, let Bootstrap handle it
        if (isCurrentlyShowing) {
          return;
        }
        
        // Otherwise, close all other collapses first
        Object.keys(allCollapseElements).forEach(id => {
          const collapseEl = allCollapseElements[id];
          if (collapseEl && collapseEl.id !== currentCollapse?.id && collapseEl.classList.contains('show')) {
            // Use Bootstrap's collapse API to hide
            const bsCollapse = bootstrap.Collapse.getInstance(collapseEl);
            if (bsCollapse) {
              bsCollapse.hide();
            } else {
              collapseEl.classList.remove('show');
            }
          }
        });
      });
    });

    /* ===============================
       Handle initial active state from server
    =============================== */
    
    // Set active class based on current route
    const currentRoute = '{{ $routeName }}';
    const currentRoutePrefix = currentRoute.split('.')[0];
    
    if (currentRoute === 'dashboard') {
      // Dashboard active
      const dashboardLink = document.querySelector('a[data-menu="dashboard"]');
      if (dashboardLink) setActiveMenu(dashboardLink);
    } else if (currentRoutePrefix) {
      // Check if any parent menu matches the route prefix
      const matchingParent = document.querySelector(`.admin-sidebar .nav-link[data-parent="${currentRoutePrefix}"]`);
      if (matchingParent) {
        setActiveMenu(matchingParent);
      }
    }

  });

})();
</script>


{{-- Page-wise scripts --}}
@stack('scripts')

</body>
</html>