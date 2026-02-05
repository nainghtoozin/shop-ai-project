@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Category Details</h1>
            <p class="text-muted mb-0">View category information and manage settings</p>
        </div>
        <div>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to Categories
            </a>
            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil me-2"></i>Edit Category
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="row">
        <!-- Main Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Category Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-input-label value="Category Name" />
                                <div class="form-control-plaintext fw-semibold">{{ $category->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-input-label value="Category Code" />
                                <div class="form-control-plaintext">
                                    @if($category->code)
                                        <span class="badge bg-secondary text-white">{{ $category->code }}</span>
                                    @else
                                        <span class="text-muted">No Code</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-input-label value="URL Slug" />
                                <div class="form-control-plaintext">
                                    <code>{{ $category->slug }}</code>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-input-label value="Status" />
                                <div class="form-control-plaintext">
                                    <span class="badge {{ $category->status ? 'bg-success' : 'bg-danger' }} text-white">
                                        {{ $category->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <x-input-label value="Description" />
                        <div class="form-control-plaintext">{{ $category->description ?? 'No description provided.' }}</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-input-label value="Parent Category" />
                                <div class="form-control-plaintext">
                                    @if($category->parent)
                                        {{ $category->parent->name }}
                                        <small class="text-muted d-block">ID: {{ $category->parent->id }}</small>
                                    @else
                                        <span class="badge bg-light text-dark">Root Category</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-input-label value="Total Products" />
                                <div class="form-control-plaintext">{{ $category->products_count ?? 0 }} products</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <!-- Category Image -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Category Image</h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ $category->image_url }}" 
                         alt="{{ $category->name }}" 
                         class="rounded border" 
                         style="width: 100%; height: 200px; object-fit: cover;">
                </div>
            </div>

            <!-- Subcategories -->
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Subcategories</h6>
                    <span class="badge bg-info text-white ms-2">{{ $category->children_count ?? 0 }}</span>
                </div>
                <div class="card-body">
                    @if($category->children->exists())
                        <div class="list-group list-group-flush">
                            @foreach($category->children as $child)
                                <a href="{{ route('admin.categories.show', $child->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-semibold">{{ $child->name }}</div>
                                        @if($child->code)
                                            <span class="badge bg-secondary text-white ms-2">{{ $child->code }}</span>
                                        @endif
                                    </div>
                                        <small class="text-muted">{{ $child->products_count ?? 0 }} products</small>
                                </div>
                                <div class="badge {{ $child->status ? 'bg-success' : 'bg-danger' }} text-white">
                                    {{ $child->status ? 'Active' : 'Inactive' }}
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">No subcategories</p>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h4 class="text-primary">{{ $category->products_count ?? 0 }}</h4>
                            <p class="text-muted mb-0">Products</p>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-info">{{ $category->children_count ?? 0 }}</h4>
                            <p class="text-muted mb-0">Subcategories</p>
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col-6">
                            <small class="text-muted">Created</small>
                            <p class="fw-semibold mb-0">{{ $category->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Last Updated</small>
                            <p class="fw-semibold mb-0">{{ $category->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection