@if(auth()->user()->hasPanel('dashboard'))

<li class="nav-item">

    <a class="nav-link text-white d-flex justify-content-between {{ request()->routeIs('dashboard') ? 'active' : '' }}"
       href="{{ route('dashboard.index') }}"
       data-menu="dashboard"
       data-bs-toggle="collapse"
       data-bs-target="#dashboardMenu"
       data-parent="dashboard">

        <span>
            <i class="fas fa-home me-2"></i>
            ড্যাশবোর্ড
        </span>

        <i class="fas fa-chevron-down"></i>

    </a>

    <div class="collapse {{ $open('dashboard.') }}" id="dashboardMenu">

        <ul class="nav flex-column ms-3">

            @permission('user.create')
            <li>
                <a class="nav-link text-white-50"
                   href="{{ route('dashboard.user.create') }}">
                    নতুন ব্যবহারকারী তৈরি
                </a>
            </li>
            @endpermission

            @permission('institution.view')
            <li>
                <a class="nav-link text-white-50"
                   href="{{ route('dashboard.institutions.index') }}">
                    প্রতিষ্ঠানের তথ্য
                </a>
            </li>
            @endpermission

            @permission('month.view')
            <li>
                <a class="nav-link text-white-50"
                   href="{{ route('dashboard.month.index') }}">
                    মাস সেটিং
                </a>
            </li>
            @endpermission

            @permission('user.photo')
            <li>
                <a class="nav-link text-white-50" href="#">
                    ব্যবহারকারী ছবি
                </a>
            </li>
            @endpermission

            @permission('user.report')
            <li>
                <a class="nav-link text-white-50" href="#">
                    ব্যবহারকারী রিপোর্ট
                </a>
            </li>
            @endpermission

            @permission('role.permission')
            <li>
                <a class="nav-link text-white-50" href="#">
                    ব্যবহারকারী পারমিশন
                </a>
            </li>
            @endpermission
            @permission('role.permission')
            @if(auth()->user()->is_super_admin)
            <li>
                <a class="nav-link text-white-50"
                   href="{{ route('dashboard.role-permissions.index') }}">
                    Role Permission Manager
                </a>
            </li>
            @endif
            @endpermission
        </ul>

    </div>

</li>

@endif