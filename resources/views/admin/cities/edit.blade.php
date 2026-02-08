@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Edit City</h1>
            <p class="text-muted mb-0">Update base delivery charge and status</p>
        </div>
        <div>
            <a href="{{ route('admin.cities.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid p-0">
        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">Please fix the errors below.</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">City Information</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.cities.update', $city) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <x-input-label for="name" value="City Name" />
                                <x-text-input id="name" type="text" name="name" value="{{ old('name', $city->name) }}" required />
                                <x-input-error :messages="$errors->get('name')" />
                            </div>

                            <div class="mb-3">
                                <x-input-label for="base_charge" value="Base Delivery Charge" />
                                <x-text-input id="base_charge" type="number" step="0.01" min="0" name="base_charge" value="{{ old('base_charge', $city->base_charge) }}" required />
                                <x-input-error :messages="$errors->get('base_charge')" />
                            </div>

                            <div class="mb-4">
                                <x-input-label value="Status" />
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $city->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.cities.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
