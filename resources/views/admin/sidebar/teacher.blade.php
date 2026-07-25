@if(auth()->user()->hasPanel('teacher'))

<li class="nav-item">

    <a class="nav-link text-white d-flex justify-content-between {{ request()->routeIs('teacher.*') ? 'active' : '' }}"
       data-bs-toggle="collapse"
       data-bs-target="#teacherMenu"
       data-parent="teacher">

        <span>
            <i class="fas fa-chalkboard-teacher me-2"></i>
            শিক্ষক
        </span>

        <i class="fas fa-chevron-down"></i>

    </a>

    <div class="collapse {{ $open('teacher.') }}" id="teacherMenu">

        <ul class="nav flex-column ms-3">

            @can('teacher.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    শিক্ষক তথ্য
                </a>
            </li>
            @endcan

            @can('teacher.create')
            <li>
                <a class="nav-link text-white-50" href="#">
                    যোগদান তথ্য
                </a>
            </li>
            @endcan

            @can('teacher.edit')
            <li>
                <a class="nav-link text-white-50" href="#">
                    ক্লাস এসাইন
                </a>
            </li>
            @endcan

            @can('teacher.create')
            <li>
                <a class="nav-link text-white-50" href="#">
                    বেতন সেটিং
                </a>
            </li>
            @endcan

            @can('teacher.edit')
            <li>
                <a class="nav-link text-white-50" href="#">
                    বেতন প্রদান
                </a>
            </li>
            @endcan

            @can('teacher.report')
            <li>
                <a class="nav-link text-white-50" href="#">
                    পারফরমেন্স
                </a>
            </li>
            @endcan

        </ul>

    </div>

</li>

@endif