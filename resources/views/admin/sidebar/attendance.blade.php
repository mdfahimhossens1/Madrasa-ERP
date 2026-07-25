@if(auth()->user()->hasPanel('attendance'))

<li class="nav-item">

    <a class="nav-link text-white d-flex justify-content-between {{ request()->routeIs('attendance.*') ? 'active' : '' }}"
       data-bs-toggle="collapse"
       data-bs-target="#attendanceMenu"
       data-parent="attendance">

        <span>
            <i class="fas fa-calendar-check me-2"></i>
            হাজিরা
        </span>

        <i class="fas fa-chevron-down"></i>

    </a>

    <div class="collapse {{ $open('attendance.') }}" id="attendanceMenu">

        <ul class="nav flex-column ms-3">

            @can('attendance.take')
            <li>
                <a class="nav-link text-white-50" href="#">
                    ম্যানুয়াল হাজিরা
                </a>
            </li>
            @endcan

            @can('attendance.take')
            <li>
                <a class="nav-link text-white-50" href="#">
                    অনলাইন হাজিরা
                </a>
            </li>
            @endcan

            @can('attendance.take')
            <li>
                <a class="nav-link text-white-50" href="#">
                    বায়োমেট্রিক
                </a>
            </li>
            @endcan

            @can('attendance.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    লাইভ ড্যাশবোর্ড
                </a>
            </li>
            @endcan

            @can('attendance.edit')
            <li>
                <a class="nav-link text-white-50" href="#">
                    SMS সেটিং
                </a>
            </li>
            @endcan

            @can('attendance.report')
            <li>
                <a class="nav-link text-white-50" href="#">
                    হাজিরা রিপোর্ট
                </a>
            </li>
            @endcan

        </ul>

    </div>

</li>

@endif