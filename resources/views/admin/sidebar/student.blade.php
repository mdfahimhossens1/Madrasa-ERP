@if(auth()->user()->hasPanel('student'))

<li class="nav-item">

    <a class="nav-link text-white d-flex justify-content-between {{ request()->routeIs('student.*') ? 'active' : '' }}"
       data-bs-toggle="collapse"
       data-bs-target="#studentMenu"
       data-parent="student">

        <span>
            <i class="fas fa-user-graduate me-2"></i>
            শিক্ষার্থী
        </span>

        <i class="fas fa-chevron-down"></i>

    </a>

    <div class="collapse {{ $open('student.') }}" id="studentMenu">

        <ul class="nav flex-column ms-3">

            @if(auth()->user()->hasPermission('academic.view'))
            <li>
                <a class="nav-link text-white-50"
                   href="{{ route('dashboard.academic-years.index') }}">
                    শিক্ষাবর্ষ
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('class.view'))
            <li>
                <a class="nav-link text-white-50"
                   href="{{ route('dashboard.classes.index') }}">
                    ক্লাস
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('section.view'))
            <li>
                <a class="nav-link text-white-50"
                   href="{{ route('dashboard.sections.index') }}">
                    ক্লাস গ্রুপ
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('admission.view'))
            <li>
                <a class="nav-link text-white-50"
                   href="{{ route('dashboard.admissions.index') }}">
                    শিক্ষার্থী ভর্তি
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('subject.view'))
            <li>
                <a class="nav-link text-white-50" href="#">
                    কিতাব / সাবজেক্ট
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('student-group.view'))
            <li>
                <a class="nav-link text-white-50" href="#">
                    শিক্ষার্থী গ্রুপ
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('student-card.view'))
            <li>
                <a class="nav-link text-white-50" href="#">
                    আইডি কার্ড
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('student.export'))
            <li>
                <a class="nav-link text-white-50" href="#">
                    ডাটা এক্সপোর্ট
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('student.report'))
            <li>
                <a class="nav-link text-white-50" href="#">
                    শিক্ষার্থী রিপোর্ট
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('certificate.view'))
            <li>
                <a class="nav-link text-white-50" href="#">
                    সার্টিফিকেট
                </a>
            </li>
            @endif

        </ul>

    </div>

</li>

@endif