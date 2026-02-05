@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Products</h1>
            <p class="text-muted mb-0">Manage your product inventory</p>
        </div>
        <div>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-2"></i>Add Product
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filters -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.products.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search products..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">All</option>
                            @foreach ($categories as $id => $name)
                                <option value="{{ $id }}" {{ request('category_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 font-weight-bold text-primary">All Products ({{ $products->total() }})</h6>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if ($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0  table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">Image</th>
                                    <th scope="col">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}"
                                            class="text-decoration-none">
                                            Name
                                            {{ request('sort') == 'name' ? (request('direction') == 'asc' ? '↑' : '↓') : '' }}
                                        </a>
                                    </th>
                                    <th scope="col">Category</th>
                                    <th scope="col" class="text-end">Cost Price</th>
                                    <th scope="col" class="text-end">Selling Price</th>
                                    <th scope="col">Current Stock</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $product)
                                    <tr>
                                        <td class="text-center">
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $product->name }}</div>
                                            <small
                                                class="text-muted d-block d-md-none">{{ Str::limit($product->description, 50) }}</small>
                                            @if ($product->not_for_sale)
                                                <span class="badge bg-warning text-dark d-inline-block">Not for Sale</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($product->category)
                                                <span
                                                    class="badge bg-info text-white">{{ $product->category->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <span class="text-muted">$</span>{{ number_format($product->cost_price, 2) }}
                                        </td>
                                        <td class="text-end">
                                            <span
                                                class="fw-semibold">$</span>{{ number_format($product->selling_price, 2) }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span
                                                    class="fw-medium text-uppercase {{ $product->stock <= $product->alert_stock ? 'text-danger' : 'text-success' }}">
                                                    {{ $product->stock }}
                                                </span>
                                                <span class="text-dark small ms-2">
                                                    @if ($product->unit)
                                                        {{ $product->unit->short_name ?? $product->unit->name }}
                                                    @else
                                                        pcs
                                                    @endif
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    id="status-{{ $product->id }}"
                                                    {{ $product->status ? 'checked' : '' }}
                                                    onchange="toggleProductStatus({{ $product->id }})">
                                                <label class="form-check-label" for="status-{{ $product->id }}"></label>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.products.show', $product->id) }}"
                                                    class="btn btn-outline-primary" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                                    class="btn btn-outline-secondary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" title="Delete"
                                                    onclick="confirmDelete({{ $product->id }}, '{{ $product->name }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-box-seam fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No products found</h5>
                        <p class="text-muted">Get started by creating your first product.</p>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create First Product
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if ($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function toggleProductStatus(productId) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = '{{ route('admin.products.toggle-status', ':id') }}'.replace(':id', productId);

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message || 'Error updating status', 'error');
                        // Revert checkbox state
                        document.querySelector('#status-' + productId).checked = !document.querySelector('#status-' +
                            productId).checked;
                    }
                })
                .catch(error => {
                    showToast('Error updating status', 'error');
                    // Revert checkbox state
                    document.querySelector('#status-' + productId).checked = !document.querySelector('#status-' +
                        productId).checked;
                });
        }

        function confirmDelete(productId, productName) {
            if (confirm('Are you sure you want to delete ' + productName +
                    '?\n\nThis will also delete all associated images and cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.products.destroy', ':id') }}'.replace(':id', productId);
                form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector(
                        'meta[name="csrf-token"]').getAttribute('content') +
                    '"><input type="hidden" name="_method" value="DELETE">';
                document.body.appendChild(form);
                form.submit();
            }
        }

        function showToast(message, type) {
            type = type || 'info';
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
