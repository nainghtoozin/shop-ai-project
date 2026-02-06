@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Order Details</h1>
            <p class="text-muted mb-0">{{ $order->order_number }}</p>
        </div>
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back to Orders
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid p-0">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

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

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Items</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $item->product_name }}</div>
                                                <div class="text-muted small">SKU: <code>{{ $item->sku }}</code></div>
                                            </td>
                                            <td class="text-end">${{ number_format((float) $item->price, 2) }}</td>
                                            <td class="text-center">{{ $item->quantity }}</td>
                                            <td class="text-end fw-semibold">${{ number_format((float) $item->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end text-muted">Subtotal</td>
                                        <td class="text-end">${{ number_format((float) $order->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end text-muted">Tax</td>
                                        <td class="text-end">${{ number_format((float) $order->tax, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end text-muted">Shipping</td>
                                        <td class="text-end">${{ number_format((float) $order->shipping_cost, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end text-muted">Discount</td>
                                        <td class="text-end">-${{ number_format((float) $order->discount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total</td>
                                        <td class="text-end fw-bold text-primary">${{ number_format((float) $order->total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Customer & Address</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="text-muted small">Customer</div>
                                    <div class="fw-semibold">{{ $order->customer_name }}</div>
                                    <div class="text-muted">{{ $order->customer_phone }}</div>
                                    @if ($order->customer_email)
                                        <div class="text-muted">{{ $order->customer_email }}</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="text-muted small">Account</div>
                                    @if ($order->user)
                                        <div class="fw-semibold">{{ $order->user->name }}</div>
                                        <div class="text-muted">{{ $order->user->email }}</div>
                                    @else
                                        <div class="text-muted">No user attached</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <div class="text-muted small mb-1">Shipping Address</div>
                                    <div style="white-space: pre-wrap;">{{ $order->shipping_address }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <div class="text-muted small mb-1">Billing Address</div>
                                    <div style="white-space: pre-wrap;">{{ $order->billing_address ?: $order->shipping_address }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Payment</h6>
                    </div>
                    <div class="card-body">
                        @if ($order->payment)
                            <div class="mb-2"><span class="text-muted">Method:</span> <span class="fw-semibold">{{ $order->payment->payment_method }}</span></div>
                            <div class="mb-2"><span class="text-muted">Status:</span> <span class="fw-semibold">{{ ucfirst($order->payment->payment_status) }}</span></div>
                            <div class="mb-3"><span class="text-muted">Amount:</span> <span class="fw-semibold">${{ number_format((float) $order->payment->amount, 2) }}</span></div>

                            @if ($order->payment->payment_proof)
                                <a href="{{ asset('storage/' . $order->payment->payment_proof) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="bi bi-image me-2"></i>View Payment Proof
                                </a>
                            @else
                                <div class="text-muted small">No payment proof uploaded.</div>
                            @endif
                        @else
                            <div class="text-muted">No payment record.</div>
                        @endif
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-primary">Update Order</h6>
                    </div>
                    <div class="card-body">
                        @can('order.edit')
                            <form method="POST" action="{{ route('admin.orders.update', $order) }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" required>
                                        @foreach ($statusOptions as $status)
                                            <option value="{{ $status }}" {{ old('status', $order->status) === $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Admin Note</label>
                                    <textarea name="note" class="form-control" rows="4" placeholder="Internal note">{{ old('note', $order->note) }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-save me-2"></i>Save Changes
                                </button>
                            </form>
                        @else
                            <div class="alert alert-warning mb-0" role="alert">
                                <i class="bi bi-shield-lock me-2"></i>You don't have permission to update orders.
                            </div>
                        @endcan

                        <div class="text-muted small mt-3">
                            Created: {{ $order->created_at?->format('M d, Y H:i') }}
                            <br>
                            Updated: {{ $order->updated_at?->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
