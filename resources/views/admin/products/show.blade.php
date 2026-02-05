@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Product Details</h1>
            <p class="text-muted mb-0">View complete product information</p>
        </div>
        <div>
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm me-2">
                <i class="bi bi-pencil me-2"></i>Edit Product
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to Products
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid p-0">
        <div class="row">
            <!-- Product Information -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Product Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Name:</strong> {{ $product->name }}</p>
                                <p class="mb-2"><strong>SKU:</strong> <code>{{ $product->sku }}</code></p>
                                <p class="mb-2"><strong>Slug:</strong> <code>{{ $product->slug }}</code></p>
                                <p class="mb-2"><strong>Category:</strong> 
                                    @if ($product->category)
                                        <span class="badge bg-info text-white">{{ $product->category->name }}</span>
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </p>
                                <p class="mb-2"><strong>Unit:</strong> 
                                    @if ($product->unit)
                                        {{ $product->unit->name }}
                                    @else
                                        <span class="text-muted">Not assigned</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Cost Price:</strong> ${{ number_format($product->cost_price, 2) }}</p>
                                <p class="mb-2"><strong>Selling Price:</strong> ${{ number_format($product->selling_price, 2) }}</p>
                                <p class="mb-2"><strong>Profit Margin:</strong> 
                                    <span class="badge {{ $product->profit_margin >= 0 ? 'bg-success' : 'bg-danger' }} text-white">
                                        {{ number_format($product->profit_margin, 2) }}%
                                    </span>
                                </p>
                                <p class="mb-2"><strong>Current Stock:</strong> 
                                    <span class="badge {{ $product->is_low_stock ? 'bg-warning' : ($product->is_in_stock ? 'bg-success' : 'bg-danger') }} text-white">
                                        {{ $product->stock }}
                                        @if ($product->is_low_stock)
                                            <i class="bi bi-exclamation-triangle"></i> Low Stock
                                        @endif
                                    </span>
                                </p>
                                <p class="mb-2"><strong>Alert Stock:</strong> {{ $product->alert_stock ?: 'Not set' }}</p>
                            </div>
                        </div>

                        @if ($product->description)
                            <div class="mt-3">
                                <strong>Description:</strong>
                                <p class="mt-2">{{ $product->description }}</p>
                            </div>
                        @endif

                        <!-- Status Badges -->
                        <div class="mt-3">
                            <strong>Status:</strong>
                            <div class="mt-2">
                                <span class="badge {{ $product->status ? 'bg-success' : 'bg-danger' }} text-white me-2">
                                    {{ $product->status ? 'Active' : 'Inactive' }}
                                </span>
                                @if ($product->featured)
                                    <span class="badge bg-warning text-dark me-2">Featured</span>
                                @endif
                                @if ($product->not_for_sale)
                                    <span class="badge bg-secondary text-white">Not for Sale</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Images -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Product Images ({{ $product->images->count() }})</h6>
                    </div>
                    <div class="card-body">
                        @if ($product->images->count() > 0)
                            <div class="row g-3">
                                @foreach ($product->images as $image)
                                    <div class="col-md-4">
                                        <div class="card">
                                            <img src="{{ $image->image_url }}" class="card-img-top" style="height: 250px; object-fit: cover;" alt="Product Image">
                                            <div class="card-body p-2">
                                                <p class="mb-1">
                                                    @if ($image->is_primary)
                                                        <span class="badge bg-primary">Primary Image</span>
                                                    @else
                                                        <span class="badge bg-secondary">Image {{ $loop->index + 1 }}</span>
                                                    @endif
                                                </p>
                                                <small class="text-muted">{{ $image->image }}</small>
                                                <br>
                                                <small class="text-muted">Order: {{ $image->sort_order }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-image fs-1 text-muted mb-3"></i>
                                <h5 class="text-muted">No Images</h5>
                                <p class="text-muted">This product doesn't have any images yet.</p>
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-upload me-2"></i>Add Images
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Stats -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-2"></i>Edit Product
                            </a>
                            <button type="button" class="btn btn-outline-warning" onclick="toggleStatus()">
                                <i class="bi bi-toggle-on me-2"></i>{{ $product->status ? 'Deactivate' : 'Activate' }}
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="toggleFeatured()">
                                <i class="bi bi-star me-2"></i>{{ $product->featured ? 'Remove Featured' : 'Make Featured' }}
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                <i class="bi bi-trash me-2"></i>Delete Product
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Product Stats -->
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Product Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="border-end">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Stock Value</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($product->stock * $product->cost_price, 2) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Potential Revenue</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ${{ number_format($product->stock * $product->selling_price, 2) }}
                                </div>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Profit per Unit</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format($product->selling_price - $product->cost_price, 2) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Images</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $product->images->count() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Information -->
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">System Information</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>ID:</strong> {{ $product->id }}</p>
                        <p class="mb-2"><strong>Created:</strong> {{ $product->created_at->format('M d, Y H:i') }}</p>
                        <p class="mb-2"><strong>Updated:</strong> {{ $product->updated_at->format('M d, Y H:i') }}</p>
                        <p class="mb-0"><strong>Updated:</strong> {{ $product->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleStatus() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = '{{ route('admin.products.toggle-status', $product->id) }}';
            
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
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showToast(data.message || 'Error updating status', 'error');
                    }
                })
                .catch(error => {
                    showToast('Error updating status', 'error');
                });
        }

        function toggleFeatured() {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = '{{ route('admin.products.toggle-featured', $product->id) }}';
            
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
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showToast(data.message || 'Error updating featured status', 'error');
                    }
                })
                .catch(error => {
                    showToast('Error updating featured status', 'error');
                });
        }

        function confirmDelete() {
            if (confirm('Are you sure you want to delete this product?\n\nThis will also delete all associated images and cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.products.destroy', $product->id) }}';
                form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]').getAttribute('content') + '"><input type="hidden" name="_method" value="DELETE">';
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