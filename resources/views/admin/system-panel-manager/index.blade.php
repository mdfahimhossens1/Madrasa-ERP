@extends('layouts.admin')

@section('title', 'Panel Manager')

@section('page')

<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    <div class="card shadow-sm border-0">

        <div class="card-header bg-white">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h4 class="mb-1 fw-bold">

                        <i class="fas fa-th-large me-2 text-primary"></i>

                        System Panel Manager

                    </h4>

                    <small class="text-muted">

                        Assign System Panels to Roles

                    </small>

                </div>

            </div>

        </div>

        <div class="card-body">

            {{-- Role Select --}}

            <form method="GET">

                <div class="row mb-4">

                    <div class="col-lg-4">

                        <label class="form-label fw-semibold">

                            Select Role

                        </label>

                        <select
                            class="form-select"
                            name="role"
                            onchange="this.form.submit()">

                            @foreach($roles as $role)

                                <option
                                    value="{{ $role->id }}"
                                    {{ $selectedRole->id==$role->id ? 'selected' : '' }}>

                                    {{ $role->role_name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </form>

            <hr>

            {{-- Save Form --}}

            <form
                method="POST"
                action="{{ route('dashboard.system-panel-manager.update') }}">

                @csrf

                <input
                    type="hidden"
                    name="role_id"
                    value="{{ $selectedRole->id }}">

                <div class="row">

                    @foreach($panels as $panel)

                        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">

                            <div class="card h-100 border">

                                <div class="card-body">

                                    <div class="d-flex justify-content-between align-items-center">

                                        <div>

                                            <h6 class="mb-1">

                                                @if($panel->icon)

                                                    <i class="{{ $panel->icon }} me-2 text-primary"></i>

                                                @endif

                                                {{ $panel->panel_name }}

                                            </h6>

                                            <small class="text-muted">

                                                {{ $panel->slug }}

                                            </small>

                                        </div>

                                        <div class="form-check form-switch">

                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                name="panels[]"
                                                value="{{ $panel->id }}"

                                                {{ $selectedRole->systemPanels->contains($panel->id) ? 'checked' : '' }}>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

                <hr>

                <div class="text-end">

                    <button
                        class="btn btn-primary px-4">

                        <i class="fas fa-save me-2"></i>

                        Save Changes

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection