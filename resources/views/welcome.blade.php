<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ setting('site_name', config('app.name', 'Shop')) }} - Modern E-Commerce Platform</title>

    @php($favicon = setting('site_favicon'))
    @if ($favicon && \Illuminate\Support\Facades\Storage::disk('public')->exists($favicon))
        <link rel="icon" href="{{ asset('storage/' . $favicon) }}">
    @endif

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                @php($logo = setting('site_logo'))
                @if ($logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($logo))
                    <img src="{{ asset('storage/' . $logo) }}" alt="{{ setting('site_name', config('app.name', 'Shop')) }}" style="height: 28px; width: auto;" class="me-2">
                @else
                    <i class="bi bi-shop me-2"></i>
                @endif
                {{ setting('site_name', config('app.name', 'Shop')) }}
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('home') }}"><i class="bi bi-house me-1"></i> {{ __('messages.home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}"><i class="bi bi-grid me-1"></i> {{ __('messages.products') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categories.index') }}"><i class="bi bi-tag me-1"></i> {{ __('messages.categories') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-percent me-1"></i> {{ __('messages.deals') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-info-circle me-1"></i> {{ __('messages.about') }}</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                    <div class="dropdown me-2">
                        @php($currentLocale = app()->getLocale())
                        <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-translate me-1"></i>{{ strtoupper($currentLocale) }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach (($supportedLocales ?? ['en' => 'English', 'my' => 'Myanmar']) as $code => $label)
                                <li>
                                    <a class="dropdown-item {{ $currentLocale === $code ? 'active' : '' }}" href="{{ route('language.switch', $code) }}">
                                        {{ $label }} ({{ strtoupper($code) }})
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <form action="{{ route('search') }}" method="GET" class="input-group me-2" style="max-width: 320px;">
                        <input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('messages.search_placeholder') }}" aria-label="Search" autocomplete="off">
                        <button class="btn btn-outline-light" type="submit" aria-label="Search">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>

                    <a href="{{ route('cart.index') }}" class="btn btn-outline-light position-relative me-2" aria-label="Cart">
                        <i class="bi bi-cart3"></i>
                        @php($navCartCount = collect(session('cart', []))->sum('quantity'))
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $navCartCount > 0 ? '' : 'd-none' }}" data-cart-badge>
                            <span data-cart-count>{{ $navCartCount }}</span>
                            <span class="visually-hidden">items in cart</span>
                        </span>
                    </a>

                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-light me-2">
                            <i class="bi bi-person me-1"></i> {{ __('auth.login') }}
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-light text-primary">
                            <i class="bi bi-person-plus me-1"></i> {{ __('auth.register') }}
                        </a>
                    @else
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                                    </a></li>
                                <li><a class="dropdown-item" href="{{ route('my-orders.index') }}">
                                        <i class="bi bi-receipt me-2"></i> My Orders
                                    </a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-person me-2"></i> Profile
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right me-2"></i> {{ __('auth.logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-3">
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
    </div>

    <!-- Hero Section (Dynamic Carousel) -->
    <section class="bg-gradient bg-primary text-white py-5 mb-5">
        <div class="container">
            @if (!empty($heroSliders) && $heroSliders->count() > 0)
                <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
                    <div class="carousel-indicators">
                        @foreach ($heroSliders as $i => $slide)
                            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}"
                                class="{{ $i === 0 ? 'active' : '' }}" aria-current="{{ $i === 0 ? 'true' : 'false' }}"
                                aria-label="Slide {{ $i + 1 }}"></button>
                        @endforeach
                    </div>

                    <div class="carousel-inner rounded shadow-lg overflow-hidden">
                        @foreach ($heroSliders as $i => $slide)
                            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                @php($slideUrl = $slide->link)
                                @php($slideTitle = $slide->title ?: setting('site_name', config('app.name', 'Shop')))
                                @php($slideSubtitle = $slide->subtitle)

                                @if ($slideUrl)
                                    <a href="{{ $slideUrl }}" target="_blank" rel="noopener" class="d-block text-decoration-none">
                                @endif

                                <div class="position-relative">
                                    <img src="{{ asset('storage/' . $slide->image) }}" class="d-block w-100" alt="{{ $slideTitle }}" style="height: 420px; object-fit: cover;">
                                    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(90deg, rgba(0,0,0,.55) 0%, rgba(0,0,0,.15) 60%, rgba(0,0,0,0) 100%);"></div>

                                    <div class="carousel-caption text-start" style="left: 8%; right: 8%; bottom: 2.25rem;">
                                        @if ($slide->badge_text)
                                            <div class="mb-2">
                                                <span class="badge bg-warning text-dark">{{ $slide->badge_text }}</span>
                                            </div>
                                        @endif

                                        @if ($slideTitle)
                                            <h1 class="display-5 fw-bold mb-3">{{ $slideTitle }}</h1>
                                        @endif

                                        @if ($slideSubtitle)
                                            <p class="lead mb-0" style="max-width: 50rem;">{{ $slideSubtitle }}</p>
                                        @endif
                                    </div>
                                </div>

                                @if ($slideUrl)
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>

                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start mt-4">
                    <a href="{{ route('products.index') }}" class="btn btn-light btn-lg text-primary">
                        <i class="bi bi-bag me-2"></i> Start Shopping
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-tag me-2"></i> Browse Categories
                    </a>
                </div>
            @else
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h1 class="display-5 fw-bold mb-3">{{ setting('site_name', config('app.name', 'Shop')) }}</h1>
                        <p class="lead mb-0">{{ setting('footer_text', 'Welcome.') }}</p>
                    </div>
                    <div class="col-lg-4 mt-3 mt-lg-0 text-lg-end">
                        <a href="{{ route('products.index') }}" class="btn btn-light btn-lg text-primary">
                            <i class="bi bi-bag me-2"></i> Start Shopping
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-truck text-primary fs-1"></i>
                            </div>
                            <h5 class="card-title">Fast Delivery</h5>
                            <p class="card-text text-muted">
                                Get your orders delivered quickly and reliably to your doorstep.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-shield-check text-primary fs-1"></i>
                            </div>
                            <h5 class="card-title">Secure Payment</h5>
                            <p class="card-text text-muted">
                                Shop with confidence using our secure and encrypted payment methods.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="bi bi-arrow-repeat text-primary fs-1"></i>
                            </div>
                            <h5 class="card-title">Easy Returns</h5>
                            <p class="card-text text-muted">
                                Not satisfied? Return items easily within our flexible return policy.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Shop by Category</h2>
                <p class="lead text-muted">Browse our wide range of product categories</p>
            </div>
            <div class="row g-4">
                @foreach ($categories as $category)
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm hover-shadow">
                            <div class="card-body text-center p-4">
                                <div class="mb-3">
                                    @if ($category->image)
                                        <img src="{{ $category->image_url }}" alt="{{ $category->name }}"
                                            class="img-fluid rounded-circle"
                                            style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <i class="bi bi-tag text-primary fs-1"></i>
                                    @endif
                                </div>
                                <h5 class="card-title">{{ $category->name }}</h5>
                                <p class="card-text text-muted">
                                    {{ Str::limit($category->description, 80) }}
                                </p>
                                <a href="{{ route('category.show', $category->slug) }}" class="btn btn-outline-primary">Shop Now</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Featured Products</h2>
                <p class="lead text-muted">Check out our most popular items</p>
            </div>

            <div class="row g-4">
                @foreach ($products as $product)
                    <div class="col-lg-3 col-md-6">
                        <div class="card product-card h-100">
                            <div class="position-relative overflow-hidden">
                                @if ($product->primaryImage)
                                    <img src="{{ $product->primaryImage->image_url }}" class="card-img-top"
                                        alt="{{ $product->name }}">
                                @else
                                    <img src="https://via.placeholder.com/300x200/6c757d/ffffff?text={{ urlencode($product->name) }}"
                                        class="card-img-top" alt="{{ $product->name }}">
                                @endif
                                @if ($product->featured)
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-2">Featured</span>
                                @elseif ($product->created_at->diffInDays() <= 7)
                                    <span class="badge bg-success position-absolute top-0 start-0 m-2">New</span>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-2">
                                    <a href="{{ route('products.show', $product->slug) }}" class="product-title-link">
                                        {{ Str::limit($product->name, 30) }}
                                    </a>
                                </h5>
                                <p class="card-text text-muted small">
                                    {{ Str::limit($product->description, 60) }}
                                </p>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        @if ($product->stock <= $product->alert_stock)
                                            <i class="bi bi-exclamation-triangle text-danger"></i> Low Stock
                                        @else
                                            <i class="bi bi-check-circle text-success"></i> In Stock
                                        @endif
                                    </small>
                                </div>
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h4 class="mb-0 text-primary">${{ number_format($product->selling_price, 2) }}
                                        </h4>
                                        <small class="text-muted">{{ $product->stock }}
                                            {{ $product->unit->short_name ?? $product->unit->name }}</small>
                                    </div>
                                    <div class="d-grid">
                                        <form method="POST" action="{{ route('cart.add', $product->id) }}" class="m-0 js-add-to-cart-form">
                                            @csrf
                                            <button type="submit" class="btn btn-primary w-100 js-add-to-cart-btn"
                                                {{ ($product->not_for_sale || $product->stock <= 0) ? 'disabled' : '' }}>
                                                <span class="js-add-to-cart-label">
                                                    <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                                </span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg">
                    View All Products <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h3 class="mb-3">Stay Updated!</h3>
                    <p>Subscribe to our newsletter for exclusive offers and new product updates.</p>
                </div>
                <div class="col-lg-6">
                    <form class="d-flex flex-column flex-sm-row gap-2">
                        <input type="email" class="form-control flex-grow-1"
                            placeholder="Enter your email address">
                        <button type="submit" class="btn btn-light text-primary">
                            <i class="bi bi-envelope me-2"></i>Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-ecom text-white">
        @php($contactEmail = setting('contact_email'))
        @php($contactPhone = setting('contact_phone'))
        @php($address = setting('address'))
        @php($facebook = setting('facebook_url'))
        @php($instagram = setting('instagram_url'))
        @php($telegram = setting('telegram_url'))
        @php($footerLogo = setting('site_logo'))

        <div class="footer-ecom__top py-5">
            <div class="container">
                <div class="row gy-5 gx-4">
                    <!-- Brand + Social -->
                    <div class="col-lg-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="footer-ecom__mark">
                                @if ($footerLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($footerLogo))
                                    <img src="{{ asset('storage/' . $footerLogo) }}" alt="{{ setting('site_name', config('app.name', 'Shop')) }}" class="footer-ecom__logo">
                                @else
                                    <div class="footer-ecom__logo-fallback">
                                        <i class="bi bi-shop"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="footer-ecom__brand fw-bold">{{ setting('site_name', config('app.name', 'Shop')) }}</div>
                                    <span class="badge rounded-pill footer-ecom__pill">Online Store</span>
                                </div>
                                <p class="footer-ecom__desc text-white-50 mb-0">
                                    {{ setting('footer_text', 'Your trusted online shopping destination for quality products and great deals.') }}
                                </p>

                                <div class="mt-4">
                                    <div class="text-uppercase text-white-50 small fw-semibold mb-2">Social</div>
                                    <div class="d-flex gap-2">
                                        @php($social = [
                                            ['label' => 'Facebook', 'url' => $facebook, 'icon' => 'bi-facebook'],
                                            ['label' => 'Instagram', 'url' => $instagram, 'icon' => 'bi-instagram'],
                                            ['label' => 'Telegram', 'url' => $telegram, 'icon' => 'bi-telegram'],
                                        ])
                                        @foreach ($social as $s)
                                            @if (!empty($s['url']))
                                                <a href="{{ $s['url'] }}" target="_blank" rel="noopener"
                                                    class="footer-ecom__social" aria-label="{{ $s['label'] }}">
                                                    <i class="bi {{ $s['icon'] }}"></i>
                                                </a>
                                            @else
                                                <span class="footer-ecom__social footer-ecom__social--disabled"
                                                    aria-label="{{ $s['label'] }} (not configured)" title="{{ $s['label'] }} not configured">
                                                    <i class="bi {{ $s['icon'] }}"></i>
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shop Links -->
                    <div class="col-6 col-lg-2">
                        <div class="text-uppercase text-white-50 small fw-semibold mb-3">Shop</div>
                        <ul class="list-unstyled mb-0 footer-ecom__links">
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li><a href="{{ route('products.index') }}">Products</a></li>
                            <li><a href="{{ route('categories.index') }}">Categories</a></li>
                            <li><a href="{{ route('cart.index') }}">Cart</a></li>
                        </ul>
                    </div>

                    <!-- Account Links -->
                    <div class="col-6 col-lg-2">
                        <div class="text-uppercase text-white-50 small fw-semibold mb-3">Account</div>
                        <ul class="list-unstyled mb-0 footer-ecom__links">
                            @guest
                                <li><a href="{{ route('login') }}">Login</a></li>
                                <li><a href="{{ route('register') }}">Register</a></li>
                            @else
                                <li><a href="{{ route('my-orders.index') }}">My Orders</a></li>
                                <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            @endguest
                        </ul>
                    </div>

                    <!-- Contact + Payments -->
                    <div class="col-lg-4">
                        <div class="row gy-4">
                            <div class="col-12">
                                <div class="text-uppercase text-white-50 small fw-semibold mb-3">Contact</div>
                                @if ($contactEmail || $contactPhone || $address)
                                    <div class="footer-ecom__contact text-white-50 small">
                                        @if ($address)
                                            <div class="footer-ecom__contact-row">
                                                <i class="bi bi-geo-alt"></i>
                                                <span>{{ $address }}</span>
                                            </div>
                                        @endif
                                        @if ($contactPhone)
                                            <div class="footer-ecom__contact-row">
                                                <i class="bi bi-telephone"></i>
                                                <a href="tel:{{ preg_replace('/\s+/', '', $contactPhone) }}" class="text-white-50 text-decoration-none">{{ $contactPhone }}</a>
                                            </div>
                                        @endif
                                        @if ($contactEmail)
                                            <div class="footer-ecom__contact-row">
                                                <i class="bi bi-envelope"></i>
                                                <a href="mailto:{{ $contactEmail }}" class="text-white-50 text-decoration-none">{{ $contactEmail }}</a>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-white-50 small">Contact details are not configured yet.</div>
                                @endif
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="text-uppercase text-white-50 small fw-semibold">Accepted Payments</div>
                                    <a href="{{ route('checkout.index') }}" class="footer-ecom__micro-link text-white-50 small text-decoration-none">
                                        Checkout <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>

                                @if (!empty($footerPaymentMethods) && $footerPaymentMethods->count() > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($footerPaymentMethods as $pm)
                                            @php($t = strtolower((string) ($pm->type ?? '')))
                                            @php($pmIcon = str_contains($t, 'cod') ? 'bi-cash-coin' : (str_contains($t, 'bank') ? 'bi-bank' : (str_contains($t, 'wallet') ? 'bi-phone' : 'bi-credit-card')))
                                            <span class="footer-ecom__pay-pill" title="{{ $pm->type ?: '' }}">
                                                <i class="bi {{ $pmIcon }}"></i>
                                                <span>{{ $pm->name }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="footer-ecom__pay-pill footer-ecom__pay-pill--placeholder"><i class="bi bi-cash-coin"></i><span>Cash</span></span>
                                        <span class="footer-ecom__pay-pill footer-ecom__pay-pill--placeholder"><i class="bi bi-bank"></i><span>Bank Transfer</span></span>
                                        <span class="footer-ecom__pay-pill footer-ecom__pay-pill--placeholder"><i class="bi bi-phone"></i><span>Mobile Wallet</span></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-ecom__bottom py-3">
            <div class="container">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                    <div class="text-white-50 small">
                        &copy; {{ date('Y') }} {{ setting('site_name', config('app.name', 'Shop')) }}. All rights reserved.
                    </div>
                    <div class="text-white-50 small">
                        <span class="me-3"><i class="bi bi-shield-lock me-1"></i>Secure payment</span>
                        <span class="me-3"><i class="bi bi-truck me-1"></i>Fast delivery</span>
                        <span><i class="bi bi-arrow-repeat me-1"></i>Easy returns</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <style>
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .rating .bi {
            font-size: 14px;
        }

        .hover-shadow:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .bg-gradient {
            background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
        }

        .navbar-brand {
            font-size: 1.5rem;
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
        }

        .product-title-link {
            color: inherit;
            text-decoration: none;
        }

        .product-title-link:hover {
            text-decoration: none;
            color: #0d6efd;
        }

        /* Footer (modern e-commerce style) */
        .footer-ecom {
            background:
                radial-gradient(1200px 600px at 20% -10%, rgba(13, 110, 253, 0.28) 0%, rgba(13, 110, 253, 0) 60%),
                radial-gradient(900px 500px at 90% 0%, rgba(255, 193, 7, 0.14) 0%, rgba(255, 193, 7, 0) 55%),
                linear-gradient(180deg, #0f172a 0%, #0b1220 100%);
        }

        .footer-ecom__top {
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .footer-ecom__bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(0, 0, 0, 0.18);
            backdrop-filter: blur(6px);
        }

        .footer-ecom__mark {
            width: 52px;
            height: 52px;
            flex: 0 0 52px;
        }

        .footer-ecom__logo {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.06);
        }

        .footer-ecom__logo-fallback {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(13, 110, 253, 0.18);
            border: 1px solid rgba(13, 110, 253, 0.35);
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.2rem;
        }

        .footer-ecom__brand {
            letter-spacing: 0.2px;
        }

        .footer-ecom__pill {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.78);
        }

        .footer-ecom__desc {
            line-height: 1.5;
        }

        .footer-ecom__links li {
            margin-bottom: 0.5rem;
        }

        .footer-ecom__links a {
            color: rgba(255, 255, 255, 0.68);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: color 160ms ease, transform 160ms ease;
        }

        .footer-ecom__links a:hover {
            color: rgba(255, 255, 255, 0.92);
            transform: translateX(2px);
        }

        .footer-ecom__social {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.10);
            color: rgba(255, 255, 255, 0.84);
            text-decoration: none;
            transition: transform 160ms ease, background 160ms ease, border-color 160ms ease;
        }

        .footer-ecom__social:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.10);
            border-color: rgba(255, 255, 255, 0.18);
            color: rgba(255, 255, 255, 0.95);
        }

        .footer-ecom__social--disabled {
            opacity: 0.45;
            cursor: not-allowed;
            transform: none !important;
        }

        .footer-ecom__contact-row {
            display: flex;
            gap: 0.6rem;
            align-items: flex-start;
            margin-bottom: 0.6rem;
        }

        .footer-ecom__contact-row i {
            margin-top: 2px;
            color: rgba(255, 255, 255, 0.65);
        }

        .footer-ecom__micro-link:hover {
            color: rgba(255, 255, 255, 0.88) !important;
        }

        .footer-ecom__pay-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0.55rem 0.8rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.10);
            color: rgba(255, 255, 255, 0.78);
            font-size: 0.875rem;
            line-height: 1;
        }

        .footer-ecom__pay-pill i {
            color: rgba(255, 255, 255, 0.9);
        }

        .footer-ecom__pay-pill--placeholder {
            opacity: 0.7;
        }

        @media (max-width: 575.98px) {
            .footer-ecom__desc {
                font-size: 0.95rem;
            }
        }
    </style>

    <script>
        function formatMoney(value) {
            const n = Number(value || 0);
            return n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function updateCartBadge(count) {
            document.querySelectorAll('[data-cart-badge]').forEach((el) => {
                const c = Number(count || 0);
                const countEl = el.querySelector('[data-cart-count]');
                if (countEl) countEl.textContent = String(c);
                if (c > 0) {
                    el.classList.remove('d-none');
                } else {
                    el.classList.add('d-none');
                }
            });
        }

        function showCartToast(message, type) {
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = '1080';
            toast.innerHTML =
                '<div class="alert alert-' + (type || 'success') + ' shadow-sm mb-0" role="alert">' +
                '<div class="d-flex align-items-center">' +
                '<div class="me-2"><i class="bi ' + ((type || 'success') === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle') + '"></i></div>' +
                '<div class="flex-grow-1">' + message + '</div>' +
                '<button type="button" class="btn-close ms-2" aria-label="Close"></button>' +
                '</div>' +
                '</div>';

            document.body.appendChild(toast);
            const btn = toast.querySelector('.btn-close');
            if (btn) btn.addEventListener('click', () => toast.remove());
            setTimeout(() => toast.remove(), 2200);
        }

        async function postCartForm(form) {
            const btn = form.querySelector('.js-add-to-cart-btn');
            const label = form.querySelector('.js-add-to-cart-label');

            if (btn && btn.disabled) return;

            if (btn) btn.disabled = true;
            if (label) {
                label.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Adding...';
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const url = form.getAttribute('action') || form.action;
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                });

                const data = await res.json();
                if (!res.ok || !data.ok) {
                    throw new Error(data.message || 'Could not add to cart.');
                }

                updateCartBadge(data.cart?.count);
                showCartToast(data.message || 'Added to cart.', 'success');
            } catch (e) {
                showCartToast(e.message || 'Something went wrong.', 'danger');
            } finally {
                // restore button
                const isDisabledByStock = btn?.hasAttribute('disabled') && btn?.getAttribute('disabled') !== null;
                // If stock/not_for_sale disabled in markup, keep disabled
                if (btn && !isDisabledByStock) btn.disabled = false;
                if (label) {
                    label.innerHTML = '<i class="bi bi-cart-plus me-2"></i>Add to Cart';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.js-add-to-cart-form').forEach((form) => {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    postCartForm(form);
                });
            });
        });
    </script>
</body>

</html>
