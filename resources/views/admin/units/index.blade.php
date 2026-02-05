@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Units</h1>
            <p class="text-muted mb-0">Manage product units and measurements</p>
        </div>
        <div>
            <a href="{{ route('admin.units.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-2"></i>Add Unit
            </a>
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
                                               disabled>
                                        <label class="form-check-label" for="status-{{ $unit->id }}"></label>
                                    </div>
                                    <span class="badge {{ $unit->status ? 'bg-success' : 'bg-danger' }} text-white ms-2">
                                        {{ $unit->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.units.show', $unit->id) }}" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.units.edit', $unit->id) }}" class="btn btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger" title="Delete" onclick="confirmDelete({{ $unit->id }}, '({{ $unit->name }}')">
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
                    <i class="bi bi-box fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No units found</h5>
                    <p class="text-muted">Get started by creating your first unit.</p>
                    <a href="{{ route('admin.units.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create First Unit
                    </a>
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
@endpush