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

  @include('layouts.sidebar')

  {{-- TOPBAR + MAIN --}}
  <div class="admin-main" id="adminMain">

@include('layouts.topbar')

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