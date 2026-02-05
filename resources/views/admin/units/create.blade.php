@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Create Unit</h1>
            <p class="text-muted mb-0">Add a new product measurement unit</p>
        </div>
        <div>
            <a href="{{ route('admin.units.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to Units
            </a>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Unit Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.units.store') }}" method="POST">
                        @csrf

                        <!-- Name Field -->
                        <div class="mb-3">
                            <x-input-label for="name" value="Unit Name" />
                            <x-text-input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="e.g., Kilogram" required />
                            <x-input-error :messages="$errors->get('name')" />
                        </div>

                        <!-- Short Name Field -->
                        <div class="mb-3">
                            <x-input-label for="short_name" value="Short Name" />
                            <x-text-input id="short_name" type="text" name="short_name" value="{{ old('short_name') }}" placeholder="e.g., kg" required maxlength="10" />
                            <x-input-error :messages="$errors->get('short_name')" />
                            <div class="form-text text-muted">Maximum 10 characters.</div>
                        </div>

                        <!-- Description Field -->
                        <div class="mb-4">
                            <x-input-label for="description" value="Description" />
                            <textarea class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" name="description" rows="4" placeholder="Optional description...">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" />
                            <div class="form-text text-muted">Optional. Maximum 1000 characters.</div>
                        </div>

                        <!-- Status Field -->
                        <div class="mb-4">
                            <x-input-label value="Status" />
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="status" id="status" value="1" {{ old('status') ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    Active
                                </label>
                            </div>
                            <div class="form-text text-muted">
                                <small>Check if this unit should be available for use.</small>
                            </div>
                            <x-input-error :messages="$errors->get('status')" />
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.units.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <x-primary-button>
                                <i class="bi bi-check-circle me-2"></i>Create Unit
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection