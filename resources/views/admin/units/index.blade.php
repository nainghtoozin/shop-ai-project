@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Units</h1>
            <p class="text-muted mb-0">Manage product units and measurements</p>
        </div>
        <div>
            @can('unit.create')
                <a href="{{ route('admin.units.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-2"></i>Add Unit
                </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid p-0">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Units Table -->
    <div class="card shadow">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">All Units</h6>
                </div>
                <div class="col-auto">
                    <form action="{{ route('admin.units.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search units..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-secondary btn-sm ms-2">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($units->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Name</th>
                                <th scope="col">Short Name</th>
                                <th scope="col">Description</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($units as $unit)
                            <tr>
                                <td>{{ $unit->id }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $unit->name }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-white">{{ $unit->short_name }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">
                                        {{ Str::limit($unit->description, 50) }}
                                        @if($unit->description && strlen($unit->description) > 50)
                                            <span title="{{ $unit->description }}">...</span>
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="status-{{ $unit->id }}" 
                                               {{ $unit->status ? 'checked' : '' }}
                                               onchange="toggleUnitStatus({{ $unit->id }})"
                                               {{ auth()->user()->can('unit.edit') ? '' : 'disabled' }}>
                                        <label class="form-check-label" for="status-{{ $unit->id }}"></label>
                                    </div>
                                    <span class="badge {{ $unit->status ? 'bg-success' : 'bg-danger' }} text-white ms-2">
                                        {{ $unit->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @can('unit.view')
                                            <a href="{{ route('admin.units.show', $unit->id) }}" class="btn btn-outline-primary" title="View">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endcan
                                        @can('unit.edit')
                                            <a href="{{ route('admin.units.edit', $unit->id) }}" class="btn btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endcan
                                        @can('unit.delete')
                                            <button type="button" class="btn btn-outline-danger" title="Delete"
                                                    data-unit-id="{{ $unit->id }}"
                                                    data-unit-name="{{ $unit->name }}"
                                                    onclick="confirmDelete(this.dataset.unitId, this.dataset.unitName)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-box fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No units found</h5>
                    <p class="text-muted">Get started by creating your first unit.</p>
                    @can('unit.create')
                        <a href="{{ route('admin.units.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create First Unit
                        </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    @if($units->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $units->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
    <script>
        function toggleUnitStatus(unitId) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = `{{ route("admin.units.toggle-status", 0) }}`.replace('/0/', '/' + unitId + '/');

            fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (!data || !data.success) return;

                    const statusSwitch = document.querySelector('#status-' + unitId);
                    const statusBadge = statusSwitch.closest('td').querySelector('.badge');

                    if (data.status) {
                        statusBadge.className = 'badge bg-success text-white ms-2';
                        statusBadge.textContent = 'Active';
                        statusSwitch.checked = true;
                    } else {
                        statusBadge.className = 'badge bg-danger text-white ms-2';
                        statusBadge.textContent = 'Inactive';
                        statusSwitch.checked = false;
                    }

                    showToast(data.message || 'Unit status updated.', 'success');
                })
                .catch(() => {
                    showToast('Error updating status', 'error');
                });
        }

        function confirmDelete(unitId, unitName) {
            if (!confirm('Are you sure you want to delete ' + unitName + '?')) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route("admin.units.destroy", 0) }}`.replace(/0$/, unitId);
            form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector(
                    'meta[name="csrf-token"]').getAttribute('content') +
                '"><input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        }

        function showToast(message, type) {
            type = type || 'info';
            if (type === 'error') type = 'danger';

            const toastHtml = '<div class="toast align-items-center text-white bg-' + type + ' border-0" role="alert">' +
                '<div class="d-flex">' +
                '<div class="toast-body">' + message + '</div>' +
                '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
                '</div>' +
                '</div>';

            const toastContainer = document.createElement('div');
            toastContainer.innerHTML = toastHtml;
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(toastContainer);

            const toast = new bootstrap.Toast(toastContainer.querySelector('.toast'));
            toast.show();
        }
    </script>
@endpush
