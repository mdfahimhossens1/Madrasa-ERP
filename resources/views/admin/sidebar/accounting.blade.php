@if(auth()->user()->hasPanel('accounting'))

<li class="nav-item">

    <a class="nav-link text-white d-flex justify-content-between {{ request()->routeIs('dashboard.transactions.*','dashboard.fees.*','dashboard.fee-settings.*','dashboard.fee-collection.*','dashboard.fee-report.*','dashboard.reports.*') ? 'active' : '' }}"
       data-bs-toggle="collapse"
       data-bs-target="#accountMenu"
       data-parent="account">

        <span>
            <i class="fas fa-wallet me-2"></i>
            অ্যাকাউন্টিং
        </span>

        <i class="fas fa-chevron-down"></i>

    </a>

    <div class="collapse {{ $open('account.') }}" id="accountMenu">

        <ul class="nav flex-column ms-3">

            @can('income.view')
            <li>
                <a class="nav-link text-white-50"
                   href="{{ route('dashboard.transactions.index') }}">
                    আয়-ব্যয়
                </a>
            </li>
            @endcan

            @can('accounting-report.view')
            <li>
                <a class="nav-link text-white-50"
                   href="{{ route('dashboard.reports.income-expense') }}">
                    আয়-ব্যয় রিপোর্ট
                </a>
            </li>
            @endcan

            @can('fee-type.view')
            <li>
                <a class="nav-link text-white-50"
                   href="{{ route('dashboard.fees.index') }}">
                    ছাত্র ফি গ্রুপ
                </a>
            </li>
            @endcan

            @can('fee.view')
            <li>
                <a class="nav-link text-white-50"
                   href="{{ route('dashboard.fee-settings.index') }}">
                    ফি সেটিং
                </a>
            </li>
            @endcan

            @can('fee.collect')
            <li>
                <a class="nav-link text-white-50"
                   href="{{ route('dashboard.fee-collection.index') }}">
                    ফি গ্রহণ
                </a>
            </li>
            @endcan

            @can('income.create')
            <li>
                <a class="nav-link text-white-50" href="#">
                    অনুদান
                </a>
            </li>
            @endcan

            @can('fee.report')
            <li>
                <a class="nav-link text-white-50"
                   href="{{ route('dashboard.fee-report.index') }}">
                    ফি রিপোর্ট
                </a>
            </li>
            @endcan

            @can('fee.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    বকেয়া
                </a>
            </li>
            @endcan

            @can('payment.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    হিস্ট্রি
                </a>
            </li>
            @endcan

        </ul>

    </div>

</li>

@endif