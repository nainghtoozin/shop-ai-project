@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Payment Methods</h1>
            <p class="text-muted mb-0">Manage checkout payment options</p>
        </div>
        <div>
            @can('setting.edit')
                <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-2"></i>Add Payment Method
                </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid p-0">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.payment-methods.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Name, type, account">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-5 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">All Payment Methods ({{ $paymentMethods->total() }})</h6>
            </div>
            <div class="card-body p-0">
                @if ($paymentMethods->count() === 0)
                    <div class="text-center py-5">
                        <i class="bi bi-credit-card fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No payment methods found</h5>
                        @can('setting.edit')
                            <a href="{{ route('admin.payment-methods.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Create First Payment Method
                            </a>
                        @endcan
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Account</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($paymentMethods as $pm)
                                    <tr>
                                        <td>{{ $pm->id }}</td>
                                        <td>
                                            <span class="badge bg-secondary text-white">{{ $pm->type }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $pm->name }}</div>
                                            @if ($pm->description)
                                                <div class="text-muted small">{{ Str::limit($pm->description, 80) }}</div>
                                            @endif
                                        </td>
                                        <td><code class="text-muted">{{ $pm->account_number }}</code></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input" type="checkbox" id="pm-status-{{ $pm->id }}"
                                                        {{ $pm->is_active ? 'checked' : '' }}
                                                        onchange="togglePaymentMethodStatus({{ $pm->id }})"
                                                        @cannot('setting.edit') disabled @endcannot>
                                                </div>
                                                <span class="badge {{ $pm->is_active ? 'bg-success' : 'bg-danger' }} text-white ms-2" data-pm-status-badge="{{ $pm->id }}">
                                                    {{ $pm->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                @can('setting.edit')
                                                    <a href="{{ route('admin.payment-methods.edit', $pm) }}" class="btn btn-outline-secondary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" title="Delete"
                                                        data-id="{{ $pm->id }}" data-name="{{ $pm->name }}"
                                                        onclick="confirmDelete(this.dataset.id, this.dataset.name)">
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
                @endif
            </div>
        </div>

        @if ($paymentMethods->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $paymentMethods->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function togglePaymentMethodStatus(id) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = `{{ route("admin.payment-methods.toggle-status", 0) }}`.replace(/0$/, id);

            fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (!data || !data.success) return;

                    const badge = document.querySelector('[data-pm-status-badge="' + id + '"]');
                    const sw = document.getElementById('pm-status-' + id);

                    if (badge) {
                        badge.className = 'badge ' + (data.is_active ? 'bg-success' : 'bg-danger') + ' text-white ms-2';
                        badge.textContent = data.is_active ? 'Active' : 'Inactive';
                    }
                    if (sw) sw.checked = !!data.is_active;
                })
                .catch(() => {
                    // revert switch if request fails
                    const sw = document.getElementById('pm-status-' + id);
                    if (sw) sw.checked = !sw.checked;
                });
        }

        function confirmDelete(id, name) {
            if (!confirm('Are you sure you want to delete ' + name + '?')) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route("admin.payment-methods.destroy", 0) }}`.replace(/0$/, id);
            form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector(
                    'meta[name="csrf-token"]').getAttribute('content') +
                '"><input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endpush
