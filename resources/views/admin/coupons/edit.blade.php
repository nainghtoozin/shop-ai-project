@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Edit Coupon</h1>
            <p class="text-muted mb-0">Update discount rules and validity</p>
        </div>
        <div>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary btn-sm">
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
            <div class="col-lg-9">
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Coupon Details</h6>
                        <div class="small text-muted">Used: {{ (int) $coupon->used_count }}</div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control" value="{{ old('code', $coupon->code) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Type <span class="text-danger">*</span></label>
                                    <select name="type" class="form-select" required>
                                        <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                        <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Value <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0" name="value" class="form-control" value="{{ old('value', $coupon->value) }}" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Minimum Order Amount</label>
                                    <input type="number" step="0.01" min="0" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', $coupon->min_order_amount) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Max Discount Amount (optional)</label>
                                    <input type="number" step="0.01" min="0" name="max_discount_amount" class="form-control" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Global Usage Limit (optional)</label>
                                    <input type="number" step="1" min="1" name="usage_limit" class="form-control" value="{{ old('usage_limit', $coupon->usage_limit) }}">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Per-User Limit (optional)</label>
                                    <input type="number" step="1" min="1" name="per_user_limit" class="form-control" value="{{ old('per_user_limit', $coupon->per_user_limit) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date', optional($coupon->start_date)->format('Y-m-d\\TH:i')) }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date', optional($coupon->end_date)->format('Y-m-d\\TH:i')) }}" required>
                                </div>

                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Active</label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
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
