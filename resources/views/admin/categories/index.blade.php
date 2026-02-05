@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Categories</h1>
            <p class="text-muted mb-0">Manage product categories and hierarchies</p>
        </div>
        <div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-2"></i>Add Category
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
                <form action="{{ route('admin.categories.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search categories..."
                            value="{{ request('search') }}">
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
                        <label class="form-label">Parent</label>
                        <select name="parent_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach (\App\Models\Category::all() as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('parent_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="card shadow">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 font-weight-bold text-primary">All Categories ({{ $categories->total() }})</h6>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if ($categories->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Image</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Code</th>
                                    <th scope="col">Slug</th>
                                    <th scope="col">Parent</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>
                                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}"
                                                class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $category->name }}</div>
                                            @if ($category->children()->exists())
                                                <span class="badge bg-info text-white ms-2">
                                                    <i class="bi bi-diagram-3"></i> {{ $category->children()->count() }}
                                                    children
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($category->code)
                                                <span class="badge bg-secondary text-white">{{ $category->code }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <code class="text-muted">{{ $category->slug }}</code>
                                        </td>
                                        <td>
                                            @if ($category->parent)
                                                <span class="text-muted">{{ $category->parent->name }}</span>
                                            @else
                                                <span class="badge bg-light text-dark">Root</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    id="status-{{ $category->id }}"
                                                    {{ $category->status ? 'checked' : '' }}
                                                    onchange="toggleCategoryStatus({{ $category->id }})" disabled>
                                                <label class="form-check-label" for="status-{{ $category->id }}"></label>
                                            </div>
                                            <span
                                                class="badge {{ $category->status ? 'bg-success' : 'bg-danger' }} text-white ms-2">
                                                {{ $category->status ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('admin.categories.show', $category->id) }}"
                                                    class="btn btn-outline-primary" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.categories.edit', $category->id) }}"
                                                    class="btn btn-outline-secondary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" title="Delete"
                                                    onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}')">
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
                        <i class="bi bi-folder fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No categories found</h5>
                        <p class="text-muted">Get started by creating your first category.</p>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create First Category
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if ($categories->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function toggleCategoryStatus(categoryId) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = '{{ route('admin.categories.toggle-status', ':id') }}'.replace(':id', categoryId);

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
                        const statusBadge = document.querySelector('#status-' + categoryId).closest('td').querySelector(
                            '.badge');
                        const statusSwitch = document.querySelector('#status-' + categoryId);

                        if (data.status) {
                            statusBadge.className = 'badge bg-success text-white ms-2';
                            statusBadge.textContent = 'Active';
                            statusSwitch.checked = true;
                        } else {
                            statusBadge.className = 'badge bg-danger text-white ms-2';
                            statusBadge.textContent = 'Inactive';
                            statusSwitch.checked = false;
                        }

                        showToast(data.message, 'success');
                    }
                })
                .catch(error => {
                    showToast('Error updating status', 'error');
                });
        }

        function confirmDelete(categoryId, categoryName) {
            if (confirm('Are you sure you want to delete ' + categoryName + '?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.categories.destroy', ':id') }}'.replace(':id', categoryId);
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
