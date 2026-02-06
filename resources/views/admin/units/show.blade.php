@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Unit Details</h1>
            <p class="text-muted mb-0">View unit information</p>
        </div>
        <div>
            <a href="{{ route('admin.units.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to Units
            </a>
            @can('unit.edit')
                <a href="{{ route('admin.units.edit', $unit->id) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-pencil me-2"></i>Edit Unit
                </a>
            @endcan
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Unit Details Card -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Unit Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-input-label value="Unit Name" />
                                <div class="form-control-plaintext fw-semibold">{{ $unit->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-input-label value="Short Name" />
                                <div class="form-control-plaintext fw-semibold">{{ $unit->short_name }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <x-input-label value="Description" />
                        <div class="form-control-plaintext">{{ $unit->description ?? 'No description provided.' }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <x-input-label value="Status" />
                        <div>
                            <span class="badge {{ $unit->status ? 'bg-success' : 'bg-danger' }} text-white fs-6">
                                {{ $unit->status ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Additional Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-input-label value="Created At" />
                                <div class="form-control-plaintext">{{ $unit->created_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-input-label value="Last Updated" />
                                <div class="form-control-plaintext">{{ $unit->updated_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <x-input-label value="Products Using This Unit" />
                        <div class="form-control-plaintext">{{ $unit->products_count ?? 0 }} products</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
