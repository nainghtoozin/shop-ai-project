<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Shop') }} - Products</title>

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
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-light">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
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

    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    @if (!empty($searchQuery))
                        <h1 class="display-6 fw-bold mb-1">Search Results</h1>
                        <p class="text-muted mb-0">Search results for: <span class="fw-semibold">{{ $searchQuery }}</span></p>
                    @else
                        <h1 class="display-6 fw-bold mb-1">Products</h1>
                        <p class="text-muted mb-0">Browse all available products</p>
                    @endif
                </div>
                <div class="text-muted small">{{ $products->total() }} items</div>
            </div>

            @if ($products->count() > 0)
                <div class="row g-4">
                    @foreach ($products as $product)
                        <div class="col-lg-3 col-md-6">
                            <div class="card product-card h-100">
                                <div class="position-relative overflow-hidden">
                                    <img src="{{ $product->image_url }}" class="card-img-top" alt="{{ $product->name }}">
                                    @if ($product->featured)
                                        <span class="badge bg-danger position-absolute top-0 start-0 m-2">Featured</span>
                                    @endif
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title mb-2">
                                        <a href="{{ route('products.show', $product->slug) }}" class="product-title-link">
                                            {{ Str::limit($product->name, 30) }}
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted small">{{ Str::limit($product->description, 60) }}</p>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h4 class="mb-0 text-primary">${{ number_format($product->selling_price, 2) }}</h4>
                                            <small class="text-muted">{{ $product->stock }} {{ $product->unit->short_name ?? $product->unit->name }}</small>
                                        </div>
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
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-5">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-box-seam fs-1 text-muted mb-3"></i>
                    @if (!empty($searchQuery))
                        <h5 class="text-muted">No results found</h5>
                        <p class="text-muted mb-0">We couldn't find any products matching <span class="fw-semibold">{{ $searchQuery }}</span>.</p>
                    @else
                        <h5 class="text-muted">No products available</h5>
                        <p class="text-muted">Please check back later.</p>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <style>
        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
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
        function updateCartBadge(count) {
            document.querySelectorAll('[data-cart-badge]').forEach((el) => {
                const c = Number(count || 0);
                const countEl = el.querySelector('[data-cart-count]');
                if (countEl) countEl.textContent = String(c);
                if (c > 0) el.classList.remove('d-none');
                else el.classList.add('d-none');
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
                label.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Adding...';
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
                if (!res.ok || !data.ok) throw new Error(data.message || 'Could not add to cart.');

                updateCartBadge(data.cart?.count);
                showCartToast(data.message || 'Added to cart.', 'success');
            } catch (e) {
                showCartToast(e.message || 'Something went wrong.', 'danger');
            } finally {
                const isDisabledByStock = btn?.hasAttribute('disabled') && btn?.getAttribute('disabled') !== null;
                if (btn && !isDisabledByStock) btn.disabled = false;
                if (label) label.innerHTML = '<i class="bi bi-cart-plus me-2"></i>Add to Cart';
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
