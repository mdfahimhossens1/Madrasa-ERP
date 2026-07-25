@if(auth()->user()->hasPanel('payment'))

<li class="nav-item">

    <a class="nav-link text-white d-flex justify-content-between {{ request()->routeIs('payment.*') ? 'active' : '' }}"
       data-bs-toggle="collapse"
       data-bs-target="#paymentMenu"
       data-parent="payment">

        <span>
            <i class="fas fa-credit-card me-2"></i>
            অনলাইন পেমেন্ট
        </span>

        <i class="fas fa-chevron-down"></i>

    </a>

    <div class="collapse {{ $open('payment.') }}" id="paymentMenu">

        <ul class="nav flex-column ms-3">

            @can('online-payment.create')
            <li>
                <a class="nav-link text-white-50" href="#">
                    সাবস্ক্রিপশন
                </a>
            </li>
            @endcan

            @can('online-payment.process')
            <li>
                <a class="nav-link text-white-50" href="#">
                    স্টুডেন্ট পেমেন্ট
                </a>
            </li>
            @endcan

            @can('online-payment.report')
            <li>
                <a class="nav-link text-white-50" href="#">
                    পেমেন্ট হিস্ট্রি
                </a>
            </li>
            @endcan

        </ul>

    </div>

</li>

@endif