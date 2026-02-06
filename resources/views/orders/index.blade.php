<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Shop') }} - My Orders</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="bi bi-shop me-2"></i>{{ config('app.name', 'Shop') }}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}"><i class="bi bi-house me-1"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}"><i class="bi bi-grid me-1"></i> Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categories.index') }}"><i class="bi bi-tag me-1"></i> Categories</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-light position-relative" aria-label="Cart">
                        <i class="bi bi-cart3"></i>
                        @php($navCartCount = collect(session('cart', []))->sum('quantity'))
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $navCartCount > 0 ? '' : 'd-none' }}" data-cart-badge>
                            <span data-cart-count>{{ $navCartCount }}</span>
                            <span class="visually-hidden">items in cart</span>
                        </span>
                    </a>

                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light">
                        <i class="bi bi-person-circle me-1"></i> Account
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h1 class="display-6 fw-bold mb-1">My Orders</h1>
                    <p class="text-muted mb-0">Your order history</p>
                </div>
                <div class="text-muted small">{{ $orders->total() }} orders</div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if ($orders->count() === 0)
                        <div class="p-5 text-center">
                            <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                            <h5 class="mt-3 mb-2">No orders yet</h5>
                            <p class="text-muted mb-4">When you place an order, it will show up here.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary">
                                <i class="bi bi-grid me-2"></i>Browse Products
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th class="text-end">Total</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td class="fw-semibold">{{ $order->order_number }}</td>
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
                                            </td>
                                            <td class="text-muted">{{ $order->created_at?->format('M d, Y') }}</td>
                                            <td class="text-end">
                                                <a class="btn btn-outline-primary btn-sm" href="{{ route('checkout.success', $order) }}">
                                                    View
                                                </a>
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
    </section>
</body>

</html>
