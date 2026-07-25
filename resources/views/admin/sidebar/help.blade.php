@if(auth()->user()->hasPanel('help'))

<li class="nav-item">

    <a class="nav-link text-white d-flex justify-content-between {{ request()->routeIs('help.*') ? 'active' : '' }}"
       data-bs-toggle="collapse"
       data-bs-target="#helpsMenu"
       data-parent="help">

        <span>
            <i class="fas fa-life-ring me-2"></i>
            হেল্প
        </span>

        <i class="fas fa-chevron-down"></i>

    </a>

    <div class="collapse {{ $open('help.') }}" id="helpsMenu">

        <ul class="nav flex-column ms-3">

            @can('faq.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    সফটওয়্যার ভিডিও
                </a>
            </li>
            @endcan

            @can('ticket.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    সাপোর্ট / টিকেট
                </a>
            </li>
            @endcan

        </ul>

    </div>

</li>

@endif