@if(auth()->user()->hasPanel('result'))

<li class="nav-item">

    <a class="nav-link text-white d-flex justify-content-between {{ request()->routeIs('result.*') ? 'active' : '' }}"
       data-bs-toggle="collapse"
       data-bs-target="#resultMenu"
       data-parent="result">

        <span>
            <i class="fas fa-chart-line me-2"></i>
            ফলাফল
        </span>

        <i class="fas fa-chevron-down"></i>

    </a>

    <div class="collapse {{ $open('result.') }}" id="resultMenu">

        <ul class="nav flex-column ms-3">

            @can('result.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    ফলাফল এন্ট্রি
                </a>
            </li>
            @endcan

            @can('result.edit')
            <li>
                <a class="nav-link text-white-50" href="#">
                    Auto Calculation
                </a>
            </li>
            @endcan

            @can('grade.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    GPA System
                </a>
            </li>
            @endcan

            @can('result.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    Subject Result
                </a>
            </li>
            @endcan

            @can('result.publish')
            <li>
                <a class="nav-link text-white-50" href="#">
                    অনলাইন প্রকাশ
                </a>
            </li>
            @endcan

            @can('result.print')
            <li>
                <a class="nav-link text-white-50" href="#">
                    মার্কশিট
                </a>
            </li>
            @endcan

            @can('result.export')
            <li>
                <a class="nav-link text-white-50" href="#">
                    Report
                </a>
            </li>
            @endcan

            @can('result.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    Analytics
                </a>
            </li>
            @endcan

        </ul>

    </div>

</li>

@endif