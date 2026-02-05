<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Shop') }} - {{ $product->name }}</title>

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
                        <a class="nav-link active" href="{{ route('products.index') }}"><i class="bi bi-grid me-1"></i> Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categories.index') }}"><i class="bi bi-tag me-1"></i> Categories</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-light position-relative" aria-label="Cart">
                        <i class="bi bi-cart3"></i>
                        @php($navCartCount = collect(session('cart', []))->sum('quantity'))
                        @if ($navCartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <span data-cart-count>{{ $navCartCount }}</span>
                                <span class="visually-hidden">items in cart</span>
                            </span>
                        @endif
                    </a>

                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-light me-2">
                            <i class="bi bi-person me-1"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-light text-primary">
                            <i class="bi bi-person-plus me-1"></i> Register
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-light">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <section class="py-5">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-decoration-none">Products</a></li>
                    @if ($product->category)
                        <li class="breadcrumb-item"><a href="{{ route('category.show', $product->category->slug) }}" class="text-decoration-none">{{ $product->category->name }}</a></li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($product->name, 40) }}</li>
                </ol>
            </nav>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <img src="{{ $product->image_url }}" class="img-fluid rounded" alt="{{ $product->name }}">
                    </div>

                    @if ($product->images->count() > 1)
                        <div class="row g-2 mt-3">
                            @foreach ($product->images as $image)
                                <div class="col-3">
                                    <img src="{{ $image->image_url }}" class="img-fluid rounded" alt="{{ $product->name }}" style="height: 80px; object-fit: cover; width: 100%;">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="col-lg-6">
                    <h1 class="h2 fw-bold mb-2">{{ $product->name }}</h1>

                    <div class="d-flex align-items-center gap-2 mb-3">
                        @if ($product->featured)
                            <span class="badge bg-danger">Featured</span>
                        @endif
                        @if ($product->category)
                            <a href="{{ route('category.show', $product->category->slug) }}" class="badge bg-info text-white text-decoration-none">{{ $product->category->name }}</a>
                        @endif
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="text-muted">Price</div>
                                <div class="h3 mb-0 text-primary fw-bold">${{ number_format($product->selling_price, 2) }}</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">Availability</div>
                                <div>
                                    <span class="badge {{ $product->stock <= $product->alert_stock ? 'bg-danger' : 'bg-success' }}">
                                        {{ $product->stock }} {{ $product->unit->short_name ?? $product->unit->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($product->description)
                        <div class="mb-4">
                            <h5 class="fw-bold">Description</h5>
                            <p class="text-muted mb-0">{{ $product->description }}</p>
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('cart.add', $product->id) }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-cart-plus me-2"></i>Add to Cart
                            </button>
                        </form>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i>Back
                        </a>
                    </div>

                    <small class="text-muted d-block mt-3">SKU: <code>{{ $product->sku }}</code></small>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
