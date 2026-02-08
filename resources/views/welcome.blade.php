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
                        <a class="nav-link active" href="{{ route('home') }}"><i class="bi bi-house me-1"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}"><i class="bi bi-grid me-1"></i> Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('categories.index') }}"><i class="bi bi-tag me-1"></i> Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-percent me-1"></i> Deals</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-info-circle me-1"></i> About</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                    <div class="input-group me-2" style="max-width: 300px;">
                        <input class="form-control" type="search" placeholder="Search products..." aria-label="Search">
                        <button class="btn btn-outline-light" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

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
                            <i class="bi bi-person me-1"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-light text-primary">
                            <i class="bi bi-person-plus me-1"></i> Register
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
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
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
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="mb-3">{{ setting('site_name', config('app.name', 'Shop')) }}</h5>
                    <p class="text-muted">{{ setting('footer_text', 'Your trusted online shopping destination for quality products and great deals.') }}</p>
                    @php($contactEmail = setting('contact_email'))
                    @php($contactPhone = setting('contact_phone'))
                    @php($address = setting('address'))
                    @if ($contactEmail || $contactPhone || $address)
                        <div class="small text-muted">
                            @if ($contactEmail)
                                <div>Email: <a class="text-muted" href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></div>
                            @endif
                            @if ($contactPhone)
                                <div>Phone: <a class="text-muted" href="tel:{{ preg_replace('/\s+/', '', $contactPhone) }}">{{ $contactPhone }}</a></div>
                            @endif
                            @if ($address)
                                <div>Address: {{ $address }}</div>
                            @endif
                        </div>
                    @endif
                    <div class="d-flex gap-3">
                        @if (setting('facebook_url'))
                            <a href="{{ setting('facebook_url') }}" class="text-muted" target="_blank" rel="noopener"><i class="bi bi-facebook fs-5"></i></a>
                        @endif
                        @if (setting('twitter_url'))
                            <a href="{{ setting('twitter_url') }}" class="text-muted" target="_blank" rel="noopener"><i class="bi bi-twitter fs-5"></i></a>
                        @endif
                        @if (setting('instagram_url'))
                            <a href="{{ setting('instagram_url') }}" class="text-muted" target="_blank" rel="noopener"><i class="bi bi-instagram fs-5"></i></a>
                        @endif
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6 class="mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">About Us</a>
                        </li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Contact</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">FAQs</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Terms &
                                Conditions</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6 class="mb-3">Categories</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Electronics</a>
                        </li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Fashion</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Home &
                                Garden</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Sports</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6 class="mb-3">Customer Service</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Shipping
                                Info</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Returns</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Track Order</a>
                        </li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none">Support</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h6 class="mb-3">Payment Methods</h6>
                    <div class="d-flex gap-2 mb-3">
                        <i class="bi bi-credit-card fs-4 text-muted"></i>
                        <i class="bi bi-paypal fs-4 text-muted"></i>
                        <i class="bi bi-apple fs-4 text-muted"></i>
                        <i class="bi bi-google fs-4 text-muted"></i>
                    </div>
                    <p class="small text-muted">Secure payment options available</p>
                </div>
            </div>

            <hr class="my-4 border-secondary">

            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">&copy; {{ date('Y') }} {{ setting('site_name', config('app.name', 'Shop')) }}. All
                        rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-muted text-decoration-none me-3">Privacy Policy</a>
                    <a href="#" class="text-muted text-decoration-none">Terms of Service</a>
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
