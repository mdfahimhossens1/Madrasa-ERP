@extends('layouts.admin')
@section('title', 'Role Permission Manager')
@section('page')

<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h4 class="mb-0">
                <i class="fas fa-user-lock me-2"></i>
                Role Permission Manager
                <span class="badge bg-primary ms-2">{{ $selectedRole->role_name }}</span>
            </h4>
        </div>

        <div class="card-body">

            {{-- Role Selector + Search + Global Select All --}}
            <form method="GET" class="mb-3">
                <div class="row align-items-end g-2">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            <i class="fas fa-user-tag me-1"></i> Select Role
                        </label>
                        <select class="form-select" name="role" onchange="this.form.submit()">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ $selectedRole->id == $role->id ? 'selected' : '' }}>
                                    {{ $role->role_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label fw-bold">
                            <i class="fas fa-search me-1"></i> Search Permission
                        </label>
                        <input type="text" id="permissionSearch" class="form-control" placeholder="Type to filter Power Name...">
                    </div>

                    <div class="col-md-3 text-end">
                        <label class="form-label d-block">&nbsp;</label>
                        <div class="form-check form-check-inline m-0">
                            <input class="form-check-input" type="checkbox" id="selectAllPermissions">
                            <label class="form-check-label fw-bold" for="selectAllPermissions">
                                <i class="fas fa-check-double me-1"></i> Select All
                            </label>
                        </div>
                    </div>
                </div>
            </form>

            <hr>

            {{-- Permission Matrix Form --}}
            <form method="POST" action="{{ route('dashboard.role-permissions.update') }}" id="permissionForm">
                @csrf
                <input type="hidden" name="role_id" value="{{ $selectedRole->id }}">

                @foreach($panels as $panel)

                    @php
                        // Group this panel's permissions by module ("Power Name"),
                        // then by the action suffix in the slug: base.action
                        $matrix = [];
                        foreach ($panel->permissions as $permission) {
                            $parts  = explode('.', $permission->slug);
                            $action = strtolower(trim(end($parts)));

                            // normalize common synonyms to the 4 standard columns
                            $action = match($action) {
                                'create', 'add', 'insert' => 'insert',
                                'update', 'edit'          => 'edit',
                                'delete', 'destroy', 'remove' => 'delete',
                                'view', 'index', 'show'   => 'view',
                                default => $action,
                            };

                            $matrix[$permission->module][$action] = $permission;
                        }
                    @endphp

                    @if(count($matrix))
                        <div class="card border mb-4 panel-card">
                            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                <strong>
                                    @if($panel->icon)<i class="{{ $panel->icon }} me-2"></i>@endif
                                    {{ $panel->panel_name }}
                                </strong>
                                <span class="badge bg-primary">{{ count($matrix) }} items</span>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0 matrix-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="60" class="text-center">Serial</th>
                                            <th>Power Name</th>
                                            <th width="120" class="text-center">
                                                View
                                                <input type="checkbox" class="form-check-input ms-1 column-check" data-action="view">
                                            </th>
                                            <th width="120" class="text-center">
                                                Insert
                                                <input type="checkbox" class="form-check-input ms-1 column-check" data-action="insert">
                                            </th>
                                            <th width="120" class="text-center">
                                                Edit
                                                <input type="checkbox" class="form-check-input ms-1 column-check" data-action="edit">
                                            </th>
                                            <th width="120" class="text-center">
                                                Delete
                                                <input type="checkbox" class="form-check-input ms-1 column-check" data-action="delete">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($matrix as $moduleName => $actions)
                                            <tr class="matrix-row" data-name="{{ strtolower($moduleName) }}">
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td class="fw-semibold">{{ $moduleName }}</td>

                                                @foreach(['view', 'insert', 'edit', 'delete'] as $col)
                                                    <td class="text-center">
                                                        @if(isset($actions[$col]))
                                                            @php
                                                                $perm = $actions[$col];
                                                                $isChecked = $selectedRole->permissions->contains($perm->id);
                                                            @endphp
                                                            <input
                                                                type="checkbox"
                                                                class="d-none permission-check"
                                                                name="permissions[]"
                                                                value="{{ $perm->id }}"
                                                                id="perm{{ $perm->id }}"
                                                                data-action="{{ $col }}"
                                                                {{ $isChecked ? 'checked' : '' }}>

                                                            <span
                                                                class="toggle-badge {{ $isChecked ? 'badge-active' : 'badge-inactive' }}"
                                                                data-target="perm{{ $perm->id }}"
                                                                data-action="{{ $col }}"
                                                                title="{{ $perm->slug }}"
                                                                onclick="togglePermission(this)">
                                                                {{ $isChecked ? 'Active' : 'InActive' }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">&mdash;</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                @endforeach

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-primary btn-lg shadow-sm px-5">
                        <i class="fas fa-save me-2"></i>
                        Save Permissions
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .toggle-badge {
        display: inline-block;
        min-width: 80px;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        color: #fff;
        cursor: pointer;
        user-select: none;
        transition: background-color .15s ease, transform .1s ease;
    }
    .toggle-badge:active {
        transform: scale(0.96);
    }
    .badge-active {
        background-color: #28a745;
    }
    .badge-inactive {
        background-color: #dc3545;
    }
    .matrix-table thead th {
        vertical-align: middle;
    }
    .column-check {
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const selectAll = document.getElementById('selectAllPermissions');
    const searchBox = document.getElementById('permissionSearch');

    function setBadgeState(badge, isActive) {
        const checkbox = document.getElementById(badge.dataset.target);
        checkbox.checked = isActive;
        badge.classList.toggle('badge-active', isActive);
        badge.classList.toggle('badge-inactive', !isActive);
        badge.textContent = isActive ? 'Active' : 'InActive';
    }

    function syncColumnHeader(table, action) {
        const badges = table.querySelectorAll('.toggle-badge[data-action="' + action + '"]');
        const header = table.querySelector('.column-check[data-action="' + action + '"]');
        if (!header || badges.length === 0) return;
        header.checked = Array.from(badges).every(b => b.classList.contains('badge-active'));
    }

    function syncGlobalState() {
        const allBadges = document.querySelectorAll('.toggle-badge');
        if (!selectAll) return;
        selectAll.checked = allBadges.length > 0
            && Array.from(allBadges).every(b => b.classList.contains('badge-active'));
    }

    // Click a single permission badge
    window.togglePermission = function (badge) {
        const isActive = badge.classList.contains('badge-inactive');
        setBadgeState(badge, isActive);

        const table = badge.closest('.matrix-table');
        syncColumnHeader(table, badge.dataset.action);
        syncGlobalState();
    };

    // Column-level "check all" (per table, per action column)
    document.querySelectorAll('.column-check').forEach(function (colCheck) {
        colCheck.addEventListener('change', function () {
            const table = this.closest('.matrix-table');
            const action = this.dataset.action;
            const isChecked = this.checked;

            table.querySelectorAll('.toggle-badge[data-action="' + action + '"]').forEach(function (badge) {
                setBadgeState(badge, isChecked);
            });

            syncGlobalState();
        });
    });

    // Global "Select All"
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            const isChecked = this.checked;

            document.querySelectorAll('.toggle-badge').forEach(function (badge) {
                setBadgeState(badge, isChecked);
            });

            document.querySelectorAll('.column-check').forEach(function (cb) {
                cb.checked = isChecked;
            });
        });
    }

    // Live search by Power Name
    if (searchBox) {
        searchBox.addEventListener('input', function () {
            const keyword = this.value.toLowerCase().trim();

            document.querySelectorAll('.matrix-row').forEach(function (row) {
                const name = row.dataset.name || '';
                row.style.display = name.includes(keyword) ? '' : 'none';
            });

            document.querySelectorAll('.panel-card').forEach(function (panel) {
                const anyVisible = Array.from(panel.querySelectorAll('.matrix-row'))
                    .some(row => row.style.display !== 'none');
                panel.style.display = anyVisible ? '' : 'none';
            });
        });
    }

    // Initial sync on load (column headers + global toggle reflect saved state)
    document.querySelectorAll('.matrix-table').forEach(function (table) {
        ['view', 'insert', 'edit', 'delete'].forEach(function (action) {
            syncColumnHeader(table, action);
        });
    });
    syncGlobalState();
});
</script>
@endpush