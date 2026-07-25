@if(auth()->user()->hasPanel('exam'))

<li class="nav-item">

    <a class="nav-link text-white d-flex justify-content-between {{ request()->routeIs('exam.*') ? 'active' : '' }}"
       data-bs-toggle="collapse"
       data-bs-target="#examMenu"
       data-parent="exam">

        <span>
            <i class="fas fa-file-alt me-2"></i>
            পরীক্ষা
        </span>

        <i class="fas fa-chevron-down"></i>

    </a>

    <div class="collapse {{ $open('exam.') }}" id="examMenu">

        <ul class="nav flex-column ms-3">

            @if(auth()->user()->hasPermission('exam-type.view'))
            <li>
                <a class="nav-link text-white-50" href="#">
                    পরীক্ষার নাম
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('exam.create'))
            <li>
                <a class="nav-link text-white-50" href="#">
                    পরীক্ষার ফি
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('exam.edit'))
            <li>
                <a class="nav-link text-white-50" href="#">
                    কন্ডিশন
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('exam.edit'))
            <li>
                <a class="nav-link text-white-50" href="#">
                    মেধা কন্ডিশন
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('exam.schedule'))
            <li>
                <a class="nav-link text-white-50" href="#">
                    প্রবেশপত্র
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('exam.routine'))
            <li>
                <a class="nav-link text-white-50" href="#">
                    রুটিন
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('exam.view'))
            <li>
                <a class="nav-link text-white-50" href="#">
                    নিয়ম
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('exam.view'))
            <li>
                <a class="nav-link text-white-50" href="#">
                    রিপোর্ট
                </a>
            </li>
            @endif

            @if(auth()->user()->hasPermission('exam.edit'))
            <li>
                <a class="nav-link text-white-50" href="#">
                    সেটিং
                </a>
            </li>
            @endif

        </ul>

    </div>

</li>

@endif