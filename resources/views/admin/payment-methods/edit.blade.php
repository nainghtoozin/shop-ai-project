@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Edit Payment Method</h1>
            <p class="text-muted mb-0">Update payment method details</p>
        </div>
        <div>
            <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-outline-secondary btn-sm">
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
                        <h6 class="m-0 font-weight-bold text-primary">Payment Method Information</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.payment-methods.update', $paymentMethod) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <x-input-label for="type" value="Payment Type" />
                                <input id="type" type="text" name="type" class="form-control" value="{{ old('type', $paymentMethod->type) }}" list="pm-types" required>
                                <datalist id="pm-types">
                                    <option value="COD"></option>
                                    <option value="Bank Transfer"></option>
                                    <option value="Mobile Wallet"></option>
                                    <option value="Online Payment"></option>
                                </datalist>
                                <x-input-error :messages="$errors->get('type')" />
                            </div>

                            <div class="mb-3">
                                <x-input-label for="name" value="Payment Method Name" />
                                <x-text-input id="name" type="text" name="name" value="{{ old('name', $paymentMethod->name) }}" required />
                                <x-input-error :messages="$errors->get('name')" />
                            </div>

                            <div class="mb-3">
                                <x-input-label for="account_number" value="Account / Number" />
                                <x-text-input id="account_number" type="text" name="account_number" value="{{ old('account_number', $paymentMethod->account_number) }}" required />
                                <x-input-error :messages="$errors->get('account_number')" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="description" value="Description" />
                                <textarea id="description" name="description" class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" rows="3" placeholder="optional">{{ old('description', $paymentMethod->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" />
                            </div>

                            <div class="mb-4">
                                <x-input-label value="Status" />
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.payment-methods.index') }}" class="btn btn-outline-secondary">
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
