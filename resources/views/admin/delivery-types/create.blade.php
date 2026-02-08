@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Create Delivery Type</h1>
            <p class="text-muted mb-0">Add normal/express delivery rules</p>
        </div>
        <div>
            <a href="{{ route('admin.delivery-types.index') }}" class="btn btn-outline-secondary btn-sm">
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
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Delivery Type Information</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.delivery-types.store') }}">
                            @csrf

                            <div class="mb-3">
                                <x-input-label for="name" value="Name" />
                                <x-text-input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="e.g., Normal Delivery" required />
                                <x-input-error :messages="$errors->get('name')" />
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <x-input-label for="charge_type" value="Charge Type" />
                                    <select id="charge_type" name="charge_type" class="form-select" required>
                                        <option value="fixed" {{ old('charge_type', 'fixed') === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                        <option value="percent" {{ old('charge_type') === 'percent' ? 'selected' : '' }}>Percent</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('charge_type')" />
                                </div>
                                <div class="col-md-8">
                                    <x-input-label for="extra_charge" value="Extra Charge" />
                                    <x-text-input id="extra_charge" type="number" step="0.01" min="0" name="extra_charge" value="{{ old('extra_charge', '0') }}" required />
                                    <div class="form-text">Fixed adds $ amount. Percent adds % on top of base+category charges.</div>
                                    <x-input-error :messages="$errors->get('extra_charge')" />
                                </div>
                            </div>

                            <div class="mt-3">
                                <x-input-label for="description" value="Description" />
                                <textarea id="description" name="description" class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" rows="3" placeholder="optional">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" />
                            </div>

                            <div class="mt-3 mb-4">
                                <x-input-label value="Status" />
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.delivery-types.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Create
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
