@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Edit Delivery Category</h1>
            <p class="text-muted mb-0">Update extra delivery charge per item</p>
        </div>
        <div>
            <a href="{{ route('admin.delivery-categories.index') }}" class="btn btn-outline-secondary btn-sm">
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
                        <h6 class="m-0 font-weight-bold text-primary">Category Information</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.delivery-categories.update', $deliveryCategory) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <x-input-label for="name" value="Category Name" />
                                <x-text-input id="name" type="text" name="name" value="{{ old('name', $deliveryCategory->name) }}" required />
                                <x-input-error :messages="$errors->get('name')" />
                            </div>

                            <div class="mb-4">
                                <x-input-label for="extra_charge" value="Extra Charge (per item)" />
                                <x-text-input id="extra_charge" type="number" step="0.01" min="0" name="extra_charge" value="{{ old('extra_charge', $deliveryCategory->extra_charge) }}" required />
                                <x-input-error :messages="$errors->get('extra_charge')" />
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.delivery-categories.index') }}" class="btn btn-outline-secondary">
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
