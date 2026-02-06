@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Orders</h1>
            <p class="text-muted mb-0">Manage customer orders</p>
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

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="Order #, name, phone, email">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            @foreach ($statusOptions as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">All Orders ({{ $orders->total() }})</h6>
            </div>
            <div class="card-body p-0">
                @if ($orders->count() === 0)
                    <div class="text-center py-5">
                        <i class="bi bi-receipt fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No orders found</h5>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th class="text-end">Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td class="fw-semibold">{{ $order->order_number }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $order->customer_name }}</div>
                                            <div class="text-muted small">{{ $order->customer_phone }}</div>
                                            @if ($order->user)
                                                <div class="text-muted small">User: {{ $order->user->email }}</div>
                                            @else
                                                <div class="text-muted small">Guest snapshot</div>
                                            @endif
                                        </td>
                                        <td class="text-end fw-semibold">${{ number_format((float) $order->total, 2) }}</td>
                                        <td>
                                            @php($status = (string) $order->status)
                                            <span class="badge
                                                {{ $status === 'pending' ? 'bg-warning text-dark' : '' }}
                                                {{ $status === 'confirmed' ? 'bg-info text-white' : '' }}
                                                {{ $status === 'processing' ? 'bg-primary text-white' : '' }}
                                                {{ $status === 'shipped' ? 'bg-secondary text-white' : '' }}
                                                {{ $status === 'completed' ? 'bg-success text-white' : '' }}
                                                {{ $status === 'cancelled' ? 'bg-danger text-white' : '' }}
                                            ">
                                                {{ ucfirst($status) }}
                                            </span>
                                            <div class="text-muted small">Items: {{ $order->items_count }}</div>
                                        </td>
                                        <td>
                                            @if ($order->payment)
                                                <div class="fw-semibold">{{ $order->payment->payment_method }}</div>
                                                <div class="text-muted small">{{ ucfirst($order->payment->payment_status) }}</div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-muted">{{ $order->created_at?->format('M d, Y H:i') }}</td>
                                        <td class="text-end">
                                            @can('order.view.all')
                                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-eye me-1"></i>View
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        @if ($orders->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
@endsection
