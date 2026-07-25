@extends('layouts.admin')

@section('title','System Panels')

@section('page')

<div class="container-fluid">

    <div class="card shadow-sm border-0">

        <div class="card-header bg-white d-flex justify-content-between align-items-center">

            <div>
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-th-large me-2"></i>
                    System Panels
                </h5>

                <small class="text-muted">
                    Manage all system panels.
                </small>
            </div>

            <button
                class="btn btn-primary"
                id="addPanelBtn">

                <i class="fas fa-plus me-1"></i>

                Add Panel

            </button>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">

                    <tr>

                        <th width="60">
                            #
                        </th>

                        <th width="90">
                            Icon
                        </th>

                        <th>
                            Panel Name
                        </th>

                        <th>
                            Slug
                        </th>

                        <th width="100">
                            Order
                        </th>

                        <th width="120">
                            Status
                        </th>

                        <th width="180">
                            Action
                        </th>

                    </tr>

                    </thead>

                    <tbody>

                    @forelse($panels as $panel)

                        <tr>

                            <td>

                                {{ $loop->iteration }}

                            </td>

                            <td>

                                @if($panel->icon)

                                    <i class="{{ $panel->icon }} fa-lg"></i>

                                @else

                                    <i class="fas fa-folder"></i>

                                @endif

                            </td>

                            <td>

                                <strong>

                                    {{ $panel->panel_name }}

                                </strong>

                            </td>

                            <td>

                                <code>

                                    {{ $panel->slug }}

                                </code>

                            </td>

                            <td>

                                {{ $panel->sort_order }}

                            </td>

                            <td>

                                @if($panel->status)

                                    <span class="badge bg-success">

                                        Active

                                    </span>

                                @else

                                    <span class="badge bg-danger">

                                        Inactive

                                    </span>

                                @endif

                            </td>

                            <td>

<button
    class="btn btn-warning btn-sm editPanelBtn"

    data-id="{{ $panel->id }}"

    data-name="{{ $panel->panel_name }}"

    data-slug="{{ $panel->slug }}"

    data-icon="{{ $panel->icon }}"

    data-order="{{ $panel->sort_order }}"

    data-status="{{ $panel->status }}">

    <i class="fas fa-edit"></i>

</button>

                                <form
                                    action="{{ route('dashboard.system-panels.destroy',$panel->id) }}"
                                    method="POST"
                                    class="d-inline">

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this panel?')">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="text-center text-muted py-5">

                                No System Panel Found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>
<!-- Add / Edit Modal -->
<div class="modal fade" id="panelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="panelForm" method="POST">

            @csrf

            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="modalTitle">
                        Add System Panel
                    </h5>

                    <button class="btn-close" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Panel Name
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="panel_name"
                                id="panel_name"
                                required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Slug
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="slug"
                                id="slug">

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                FontAwesome Icon
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                name="icon"
                                id="icon"
                                placeholder="fas fa-users">

                        </div>

                        <div class="col-md-3 mb-3">

                            <label class="form-label">
                                Sort Order
                            </label>

                            <input
                                type="number"
                                class="form-control"
                                name="sort_order"
                                id="sort_order"
                                value="0">

                        </div>

                        <div class="col-md-3 mb-3">

                            <label class="form-label d-block">
                                Status
                            </label>

                            <div class="form-check form-switch mt-2">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="status"
                                    name="status"
                                    checked>

                                <label class="form-check-label">

                                    Active

                                </label>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                        type="button">

                        Close

                    </button>

                    <button
                        class="btn btn-primary"
                        type="submit">

                        Save Panel

                    </button>

                </div>

            </div>

        </form>
    </div>
</div>
@push('scripts')

<script>
document.querySelectorAll('.editPanelBtn').forEach(btn=>{

    btn.onclick=function(){

        form.action="/dashboard/system-panels/"+this.dataset.id;

        document.getElementById('formMethod').value='PUT';

        document.getElementById('modalTitle').innerHTML='Edit System Panel';

        document.getElementById('panel_name').value=this.dataset.name;

        document.getElementById('slug').value=this.dataset.slug;

        document.getElementById('icon').value=this.dataset.icon;

        document.getElementById('sort_order').value=this.dataset.order;

        document.getElementById('status').checked=this.dataset.status==1;

        modal.show();

    }

});
const modal=new bootstrap.Modal(document.getElementById('panelModal'));

const form=document.getElementById('panelForm');

document.getElementById('addPanelBtn').onclick=function(){

    form.reset();

    document.getElementById('modalTitle').innerHTML='Add System Panel';

    document.getElementById('formMethod').value='POST';

    form.action="{{ route('dashboard.system-panels.store') }}";

    document.getElementById('status').checked=true;

    modal.show();

};

</script>

@endpush


@endsection