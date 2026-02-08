@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Create Product</h1>
            <p class="text-muted mb-0">Add a new product to your inventory</p>
        </div>
        <div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to Products
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid p-0">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Product Information</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
                            id="productForm">
                            @csrf

                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3">Basic Information</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Product Name <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sku" class="form-label">SKU</label>
                                        <input type="text" class="form-control" 
                                               id="sku" name="sku" value="Auto-generated" readonly 
                                               style="background-color: #f8f9fa;">
                                        <small class="form-text text-muted">SKU will be automatically generated when you save the product.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('category_id') is-invalid @enderror"
                                            id="category_id" name="category_id" required>
                                            <option value="">Select Category</option>
                                            @foreach ($categories as $id => $name)
                                                <option value="{{ $id }}"
                                                    {{ old('category_id') == $id ? 'selected' : '' }}>{{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="unit_id" class="form-label">Unit <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id"
                                            name="unit_id" required>
                                            <option value="">Select Unit</option>
                                            @foreach ($units as $id => $name)
                                                <option value="{{ $id }}"
                                                    {{ old('unit_id') == $id ? 'selected' : '' }}>{{ $name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('unit_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="delivery_category_id" class="form-label">Delivery Category</label>
                                        <select class="form-select @error('delivery_category_id') is-invalid @enderror" id="delivery_category_id"
                                            name="delivery_category_id">
                                            <option value="">Select Delivery Category (optional)</option>
                                            @foreach ($deliveryCategories as $id => $name)
                                                <option value="{{ $id }}" {{ old('delivery_category_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('delivery_category_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                            rows="3">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3">Pricing</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cost_price" class="form-label">Cost Price <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number"
                                                class="form-control @error('cost_price') is-invalid @enderror"
                                                id="cost_price" name="cost_price" value="{{ old('cost_price') }}"
                                                step="0.01" min="0" required>
                                        </div>
                                        @error('cost_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="selling_price" class="form-label">Selling Price <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number"
                                                class="form-control @error('selling_price') is-invalid @enderror"
                                                id="selling_price" name="selling_price"
                                                value="{{ old('selling_price') }}" step="0.01" min="0"
                                                required>
                                        </div>
                                        @error('selling_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Inventory -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3">Inventory</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="stock" class="form-label">Current Stock <span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('stock') is-invalid @enderror"
                                            id="stock" name="stock" value="{{ old('stock', 0) }}" min="0"
                                            required>
                                        @error('stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="alert_stock" class="form-label">Alert Stock</label>
                                        <input type="number"
                                            class="form-control @error('alert_stock') is-invalid @enderror"
                                            id="alert_stock" name="alert_stock" value="{{ old('alert_stock', 10) }}"
                                            min="0">
                                        <small class="form-text text-muted">You'll be notified when stock reaches this
                                            level</small>
                                        @error('alert_stock')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Product Images -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3">Product Images</h5>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="images" class="form-label">Upload Images</label>
                                        <input type="file" class="form-control @error('images') is-invalid @enderror"
                                            id="images" name="images[]" multiple accept="image/*">
                                        <small class="form-text text-muted">You can upload multiple images. First image
                                            will be set as primary unless you select otherwise.</small>
                                        @error('images')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Image Preview Area -->
                                    <div id="imagePreview" class="row g-3 mb-3"></div>

                                    <!-- Primary Image Selection (shown after images are selected) -->
                                    <div id="primaryImageSelection" class="d-none">
                                        <label class="form-label">Select Primary Image</label>
                                        <div id="primaryImageOptions" class="d-flex flex-wrap gap-2"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="mb-3">Settings</h5>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="status" name="status"
                                            value="1" checked>
                                        <label class="form-check-label" for="status">
                                            Active
                                        </label>
                                        <small class="form-text text-muted d-block">Enable to make product visible</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="featured" value="0"
                                            name="featured">
                                        <label class="form-check-label" for="featured">
                                            Featured
                                        </label>
                                        <small class="form-text text-muted d-block">Show in featured products</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" value="0" type="checkbox" id="not_for_sale"
                                            name="not_for_sale">
                                        <label class="form-check-label" for="not_for_sale">
                                            Not for Sale
                                        </label>
                                        <small class="form-text text-muted d-block">Product cannot be purchased</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Create Product
                                    </button>
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary ms-2">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let imageFiles = [];

        // Handle image selection and preview
        document.getElementById('images').addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            imageFiles = files;

            const previewContainer = document.getElementById('imagePreview');
            const primarySelection = document.getElementById('primaryImageSelection');
            const primaryOptions = document.getElementById('primaryImageOptions');

            // Clear previous previews
            previewContainer.innerHTML = '';
            primaryOptions.innerHTML = '';

            if (files.length > 0) {
                primarySelection.classList.remove('d-none');

                files.forEach((file, index) => {
                    // Create preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewHtml = `
                            <div class="col-md-3">
                                <div class="card">
                                    <img src="${e.target.result}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <small class="text-muted">${file.name}</small>
                                    </div>
                                </div>
                            </div>
                        `;
                        previewContainer.innerHTML += previewHtml;

                        // Create radio button for primary selection
                        const radioHtml = `
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="primary_image" 
                                       id="primary_image_${index}" value="${index}" ${index === 0 ? 'checked' : ''}>
                                <label class="form-check-label" for="primary_image_${index}">
                                    Image ${index + 1}
                                </label>
                            </div>
                        `;
                        primaryOptions.innerHTML += radioHtml;
                    };
                    reader.readAsDataURL(file);
                });
            } else {
                primarySelection.classList.add('d-none');
            }
        });

        // Auto-calculate profit margin
        document.getElementById('cost_price').addEventListener('input', calculateMargin);
        document.getElementById('selling_price').addEventListener('input', calculateMargin);

        function calculateMargin() {
            const costPrice = parseFloat(document.getElementById('cost_price').value) || 0;
            const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;

            if (sellingPrice > 0) {
                const margin = ((sellingPrice - costPrice) / sellingPrice * 100).toFixed(2);

                // Remove existing margin display if any
                const existingMargin = document.getElementById('margin-display');
                if (existingMargin) {
                    existingMargin.remove();
                }

                // Add margin display
                const marginHtml = `<div id="margin-display" class="mt-2">
                    <small class="text-muted">Profit Margin: <strong class="${margin >= 0 ? 'text-success' : 'text-danger'}">${margin}%</strong></small>
                </div>`;

                document.getElementById('selling_price').parentNode.insertAdjacentHTML('afterend', marginHtml);
            }
        }
    </script>
@endpush
