@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Coupons</h1>
            <p class="text-muted mb-0">Manage discount codes and usage limits</p>
        </div>
        <div>
            @can('coupon.create')
                <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-2"></i>Add Coupon
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
                <form action="{{ route('admin.coupons.index') }}" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Code">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">All Coupons ({{ $coupons->total() }})</h6>
            </div>
            <div class="card-body p-0">
                @if ($coupons->count() === 0)
                    <div class="text-center py-5">
                        <i class="bi bi-ticket-perforated fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No coupons found</h5>
                        @can('coupon.create')
                            <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Create First Coupon
                            </a>
                        @endcan
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Value</th>
                                    <th>Min Order</th>
                                    <th>Usage</th>
                                    <th>Validity</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($coupons as $c)
                                    <tr>
                                        <td class="fw-semibold"><code>{{ $c->code }}</code></td>
                                        <td>
                                            <span class="badge {{ $c->type === 'percentage' ? 'bg-info text-dark' : 'bg-secondary text-white' }}">
                                                {{ $c->type === 'percentage' ? 'Percentage' : 'Fixed' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($c->type === 'percentage')
                                                {{ number_format((float) $c->value, 2) }}%
                                                @if (!is_null($c->max_discount_amount))
                                                    <div class="text-muted small">Cap: {{ number_format((float) $c->max_discount_amount, 2) }}</div>
                                                @endif
                                            @else
                                                {{ number_format((float) $c->value, 2) }}
                                            @endif
                                        </td>
                                        <td>{{ number_format((float) $c->min_order_amount, 2) }}</td>
                                        <td>
                                            <div class="small">
                                                {{ (int) $c->used_count }} / {{ $c->usage_limit ? (int) $c->usage_limit : '∞' }}
                                            </div>
                                            @if ($c->per_user_limit)
                                                <div class="text-muted small">Per user: {{ (int) $c->per_user_limit }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small">{{ $c->start_date?->format('Y-m-d') }} → {{ $c->end_date?->format('Y-m-d') }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input" type="checkbox" id="coupon-status-{{ $c->id }}"
                                                        {{ $c->is_active ? 'checked' : '' }}
                                                        onchange="toggleCouponStatus({{ $c->id }})"
                                                        @cannot('coupon.edit') disabled @endcannot>
                                                </div>
                                                <span class="badge {{ $c->is_active ? 'bg-success' : 'bg-danger' }} text-white ms-2" data-coupon-status-badge="{{ $c->id }}">
                                                    {{ $c->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                @can('coupon.edit')
                                                    <a href="{{ route('admin.coupons.edit', $c) }}" class="btn btn-outline-secondary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                @endcan
                                                @can('coupon.delete')
                                                    <button type="button" class="btn btn-outline-danger" title="Delete"
                                                        data-id="{{ $c->id }}" data-code="{{ $c->code }}"
                                                        onclick="confirmDelete(this.dataset.id, this.dataset.code)">
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

        @if ($coupons->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function toggleCouponStatus(id) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = `{{ route("admin.coupons.toggle-status", 0) }}`.replace(/0$/, id);

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
                    const badge = document.querySelector('[data-coupon-status-badge="' + id + '"]');
                    const sw = document.getElementById('coupon-status-' + id);
                    if (badge) {
                        badge.className = 'badge ' + (data.is_active ? 'bg-success' : 'bg-danger') + ' text-white ms-2';
                        badge.textContent = data.is_active ? 'Active' : 'Inactive';
                    }
                    if (sw) sw.checked = !!data.is_active;
                })
                .catch(() => {
                    const sw = document.getElementById('coupon-status-' + id);
                    if (sw) sw.checked = !sw.checked;
                });
        }

        function confirmDelete(id, code) {
            if (!confirm('Delete coupon ' + code + '?')) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route("admin.coupons.destroy", 0) }}`.replace(/0$/, id);
            form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]').getAttribute('content') +
                '"><input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endpush
