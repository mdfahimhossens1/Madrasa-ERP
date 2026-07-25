@if(auth()->user()->hasPanel('settings'))

<li class="nav-item">

    <a class="nav-link text-white d-flex justify-content-between {{ request()->routeIs('settings.*') ? 'active' : '' }}"
       data-bs-toggle="collapse"
       data-bs-target="#settingsMenu"
       data-parent="settings">

        <span>
            <i class="fas fa-cog me-2"></i>
            সেটিংস
        </span>

        <i class="fas fa-chevron-down"></i>

    </a>

    <div class="collapse {{ $open('settings.') }}" id="settingsMenu">

        <ul class="nav flex-column ms-3">

            @can('general.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    General Settings
                </a>
            </li>
            @endcan

            @can('academic.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    Academic Settings
                </a>
            </li>
            @endcan

            @can('institution.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    Institution Settings
                </a>
            </li>
            @endcan

            @can('month.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    Month Settings
                </a>
            </li>
            @endcan

            @can('language.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    Language Settings
                </a>
            </li>
            @endcan

            @can('theme.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    Theme Settings
                </a>
            </li>
            @endcan

            @can('backup.view')
            <li>
                <a class="nav-link text-white-50" href="#">
                    Backup & Restore
                </a>
            </li>
            @endcan

        </ul>

    </div>

</li>

@endif