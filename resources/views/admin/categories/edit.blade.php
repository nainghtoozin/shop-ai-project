@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Edit Category</h1>
            <p class="text-muted mb-0">Update category information</p>
        </div>
        <div>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to Categories
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Current Category Info -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Current Category</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="mb-3">{{ $category->name }}</h5>
                            <p class="text-muted mb-2">{{ Str::limit($category->description, 100) }}</p>
                            <div class="d-flex gap-2">
                                <span class="badge bg-info text-white">{{ $category->code ?? 'No Code' }}</span>
                                <code class="badge bg-light text-dark">{{ $category->slug }}</code>
                                <span class="badge {{ $category->status ? 'bg-success' : 'bg-danger' }} text-white">
                                    {{ $category->status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            @if($category->parent)
                                <p class="mb-0"><small class="text-muted">Parent: {{ $category->parent->name }}</small></p>
                            @endif
                        </div>
                        <div class="col-md-4">
                            @if($imagePreview)
                                <img src="{{ $imagePreview }}" alt="Current image" class="rounded border" style="width: 100%; height: 150px; object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width: 100%; height: 150px;">
                                    <i class="bi bi-image fs-1 text-muted"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Category Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Name Field -->
                        <div class="mb-3">
                            <x-input-label for="name" value="Category Name" />
                            <x-text-input id="name" type="text" name="name" value="{{ old('name', $category->name) }}" placeholder="e.g., Electronics" required />
                            <x-input-error :messages="$errors->get('name')" />
                        </div>

                        <!-- Slug Field -->
                        <div class="mb-3">
                            <x-input-label for="slug" value="Slug" />
                            <x-text-input id="slug" type="text" name="slug" value="{{ old('slug', $category->slug) }}" placeholder="e.g., electronics" />
                            <x-input-error :messages="$errors->get('slug')" />
                            <div class="form-text text-muted">Leave empty to auto-generate from name.</div>
                        </div>

                        <!-- Code Field -->
                        <div class="mb-3">
                            <x-input-label for="code" value="Category Code" />
                            <x-text-input id="code" type="text" name="code" value="{{ old('code', $category->code) }}" placeholder="e.g., ELEC" maxlength="10" />
                            <x-input-error :messages="$errors->get('code')" />
                            <div class="form-text text-muted">Optional. Maximum 10 characters.</div>
                        </div>

                        <!-- Parent Category Field -->
                        <div class="mb-3">
                            <x-input-label for="parent_id" value="Parent Category" />
                            <select class="form-select {{ $errors->has('parent_id') ? 'is-invalid' : '' }}" id="parent_id" name="parent_id">
                                <option value="">Select Parent Category (Root Level)</option>
                                @foreach($parentOptions as $id => $name)
                                    <option value="{{ $id }}" {{ old('parent_id', $category->parent_id) == $id ? 'selected' : '' }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('parent_id')" />
                        </div>

                        <!-- Description Field -->
                        <div class="mb-3">
                            <x-input-label for="description" value="Description" />
                            <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" 
                                      name="description" rows="4" placeholder="Optional description...">{{ old('description', $category->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" />
                            <div class="form-text text-muted">Optional. Maximum 1000 characters.</div>
                        </div>

                        <!-- Image Upload Field -->
                        <div class="mb-3">
                            <x-input-label for="image" value="Category Image" />
                            <input type="file" 
                                   class="form-control {{ $errors->has('image') ? 'is-invalid' : '' }}" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*"
                                   onchange="previewImage(this)">
                            <x-input-error :messages="$errors->get('image')" />
                            <div class="form-text text-muted">
                                <small>Allowed formats: JPEG, PNG, JPG, GIF. Maximum size: 2MB.</small>
                            </div>
                            <div class="form-text text-info">
                                <small>Current: {{ $category->image ?? 'No image uploaded' }}</small>
                            </div>
                        </div>

                        <!-- Image Preview -->
                        <div class="mb-4">
                            <x-input-label value="New Image Preview" />
                            <div class="d-flex">
                                <img id="imagePreview" 
                                     src="{{ $imagePreview }}" 
                                     class="rounded border" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                                <div class="ms-3">
                                    <small class="text-muted" id="imageInfo">{{ $category->image ? basename($category->image) : 'No image selected' }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Status Field -->
                        <div class="mb-4">
                            <x-input-label value="Status" />
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status', $category->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    Active
                                </label>
                            </div>
                            <div class="form-text text-muted">
                                <small>Check if this category should be visible on the frontend.</small>
                            </div>
                            <x-input-error :messages="$errors->get('status')" />
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <x-primary-button>
                                <i class="bi bi-check-circle me-2"></i>Update Category
                            </x-primary-button>
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
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const imagePreview = document.getElementById('imagePreview');
            const imageInfo = document.getElementById('imageInfo');
            
            imagePreview.src = e.target.result;
            imageInfo.textContent = input.files[0].name + ' (' + (input.files[0].size / 1024).toFixed(2) + ' KB)';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush