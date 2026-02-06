<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Shop') }} - Order Success</title>

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
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $navCartCount > 0 ? '' : 'd-none' }}">
                            {{ $navCartCount }}
                            <span class="visually-hidden">items in cart</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                </div>
                                <h1 class="h3 fw-bold mb-1">Order Placed Successfully</h1>
                                <p class="text-muted mb-0">Thank you. Your order has been received.</p>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded">
                                        <div class="text-muted small">Order Number</div>
                                        <div class="fw-bold">{{ $order->order_number }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-light rounded">
                                        <div class="text-muted small">Total Amount</div>
                                        <div class="fw-bold text-primary">${{ number_format((float) $order->total, 2) }}</div>
                                    </div>
                                </div>
                            </div>

                            <h5 class="fw-bold mb-3">Order Summary</h5>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->items as $item)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $item->product_name }}</div>
                                                    <div class="text-muted small">{{ $item->quantity }} x ${{ number_format((float) $item->price, 2) }}</div>
                                                </td>
                                                <td class="text-end fw-semibold">${{ number_format((float) $item->total, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="text-muted">Subtotal</td>
                                            <td class="text-end">${{ number_format((float) $order->subtotal, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Tax</td>
                                            <td class="text-end">${{ number_format((float) $order->tax, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Shipping</td>
                                            <td class="text-end">${{ number_format((float) $order->shipping_cost, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Discount</td>
                                            <td class="text-end">-${{ number_format((float) $order->discount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Total</td>
                                            <td class="text-end fw-bold text-primary">${{ number_format((float) $order->total, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                                <a href="{{ route('products.index') }}" class="btn btn-primary">
                                    <i class="bi bi-grid me-2"></i>Continue Shopping
                                </a>
                                <a href="{{ route('home') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-house me-2"></i>Home
                                </a>
                            </div>

                            <small class="text-muted d-block mt-4">
                                Payment method: {{ strtoupper($order->payment?->payment_method ?? 'COD') }}
                                &middot; Status: {{ ucfirst($order->payment?->payment_status ?? 'pending') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
