@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Delivery Categories</h1>
            <p class="text-muted mb-0">Extra delivery charges per product category</p>
        </div>
        <div>
            @can('setting.edit')
                <a href="{{ route('admin.delivery-categories.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-2"></i>Add Category
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

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.delivery-categories.index') }}" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="Category name">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.delivery-categories.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">All Categories ({{ $deliveryCategories->total() }})</h6>
            </div>
            <div class="card-body p-0">
                @if ($deliveryCategories->count() === 0)
                    <div class="text-center py-5">
                        <i class="bi bi-boxes fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No delivery categories found</h5>
                        @can('setting.edit')
                            <a href="{{ route('admin.delivery-categories.create') }}" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Create First Category
                            </a>
                        @endcan
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th class="text-end">Extra Charge (per item)</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deliveryCategories as $dc)
                                    <tr>
                                        <td class="fw-semibold">{{ $dc->name }}</td>
                                        <td class="text-end">${{ number_format((float) $dc->extra_charge, 2) }}</td>
                                        <td class="text-end">
                                            @can('setting.edit')
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.delivery-categories.edit', $dc) }}" class="btn btn-outline-secondary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" title="Delete"
                                                        data-id="{{ $dc->id }}" data-name="{{ $dc->name }}"
                                                        onclick="confirmDelete(this.dataset.id, this.dataset.name)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        @if ($deliveryCategories->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $deliveryCategories->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(id, name) {
            if (!confirm('Are you sure you want to delete ' + name + '?')) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route("admin.delivery-categories.destroy", 0) }}`.replace(/0$/, id);
            form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]').getAttribute('content') +
                '"><input type="hidden" name="_method" value="DELETE">';
            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endpush
