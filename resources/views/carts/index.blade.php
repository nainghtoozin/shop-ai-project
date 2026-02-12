<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Shop') }} - Cart</title>

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
                    <div class="dropdown">
                        @php($currentLocale = app()->getLocale())
                        <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ strtoupper($currentLocale) }}
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
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-light position-relative" aria-label="Cart">
                        <i class="bi bi-cart3"></i>
                        @php($navCartCount = collect(session('cart', []))->sum('quantity'))
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $navCartCount > 0 ? '' : 'd-none' }}" data-cart-badge>
                            <span data-cart-count>{{ $navCartCount }}</span>
                            <span class="visually-hidden">items in cart</span>
                        </span>
                    </a>

                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-light">Login</a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-light">Dashboard</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h1 class="display-6 fw-bold mb-1">{{ __('cart.title') }}</h1>
                    <p class="text-muted mb-0">{{ __('cart.review_items') }}</p>
                </div>
                <div class="text-muted small"><span data-cart-count-display>{{ $count }}</span> items</div>
            </div>

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

            <div id="cartEmpty" class="{{ $items->count() === 0 ? '' : 'd-none' }}">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5 text-center">
                        <i class="bi bi-cart3 text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 mb-2">Your cart is empty</h5>
                        <p class="text-muted mb-4">Add a few products to get started.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            <i class="bi bi-grid me-2"></i>Browse Products
                        </a>
                    </div>
                </div>
            </div>

            <div id="cartContent" class="{{ $items->count() === 0 ? 'd-none' : '' }}">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Product</th>
                                                <th class="text-end">Price</th>
                                                <th class="text-center" style="width: 160px;">Quantity</th>
                                                <th class="text-end">Subtotal</th>
                                                <th class="text-end">Remove</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items as $item)
                                                <tr data-product-id="{{ (int) $item['product_id'] }}">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $item['image'] ?? 'https://via.placeholder.com/80x80/6c757d/ffffff?text=Product' }}"
                                                                alt="{{ $item['name'] }}" class="rounded me-3"
                                                                style="width: 64px; height: 64px; object-fit: cover;">
                                                            <div>
                                                                <div class="fw-semibold">{{ $item['name'] }}</div>
                                                                @if (!empty($item['unit']))
                                                                    <small class="text-muted">Unit: {{ $item['unit'] }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">${{ number_format((float) $item['price'], 2) }}</td>
                                                    <td class="text-center">
                                                        <div class="d-inline-flex align-items-center gap-1">
                                                            <form method="POST" action="{{ route('cart.update', $item['product_id']) }}" data-ajax-cart="update">
                                                                @csrf
                                                                <input type="hidden" name="action" value="dec">
                                                                <button type="submit" class="btn btn-outline-secondary btn-sm" aria-label="Decrease">
                                                                    <i class="bi bi-dash"></i>
                                                                </button>
                                                            </form>

                                                            <span class="px-2" style="min-width: 32px; display: inline-block; text-align: center;" data-item-qty>
                                                                {{ (int) $item['quantity'] }}
                                                            </span>

                                                            <form method="POST" action="{{ route('cart.update', $item['product_id']) }}" data-ajax-cart="update">
                                                                @csrf
                                                                <input type="hidden" name="action" value="inc">
                                                                <button type="submit" class="btn btn-outline-secondary btn-sm" aria-label="Increase">
                                                                    <i class="bi bi-plus"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                    <td class="text-end fw-semibold">
                                                        $<span data-item-subtotal>{{ number_format((float) $item['subtotal'], 2) }}</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <form method="POST" action="{{ route('cart.remove', $item['product_id']) }}" data-ajax-cart="remove"
                                                            onsubmit="return confirm('Remove this item from cart?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm" aria-label="Remove">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                            </a>

                            <form method="POST" action="{{ route('cart.clear') }}" data-ajax-cart="clear" onsubmit="return confirm('Clear all items from cart?')">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-2"></i>Clear Cart
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        @if (!empty($couponMessage))
                            <div class="alert alert-warning" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>{{ $couponMessage }}
                            </div>
                        @endif

                        <div class="card border-0 shadow-sm mb-4" id="couponCard"
                             data-apply-url="{{ route('cart.coupon.apply') }}"
                             data-remove-url="{{ route('cart.coupon.remove') }}">
                            <div class="card-body" id="couponCardBody" data-applied="{{ !empty($couponCode) ? 1 : 0 }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="fw-bold mb-0">Coupon</h5>
                                    @if (!empty($couponCode))
                                        <span class="badge bg-success">Applied</span>
                                    @endif
                                </div>

                                <div id="couponFeedback" class="mt-3"></div>

                                @if (!empty($couponCode))
                                    <div class="mt-3 p-3 rounded border border-success-subtle bg-success bg-opacity-10" data-coupon-applied>
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="fw-semibold">{{ $couponCode }}</div>
                                                <div class="text-muted small">Discount: <span class="fw-semibold text-success">${{ number_format((float) $discount, 2) }}</span></div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-success" data-coupon-remove-btn>
                                                <span class="js-remove-label"><i class="bi bi-x-circle me-1"></i>Remove</span>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="mt-3">
                                        <div class="text-muted small mb-2">Have a coupon?</div>
                                        <div class="d-flex gap-2">
                                            <input type="text" class="form-control" name="code" id="couponCodeInput" placeholder="Enter coupon code" autocomplete="off">
                                            <button type="button" class="btn btn-outline-primary" data-coupon-apply-btn style="white-space: nowrap;">
                                                <span class="js-apply-label">Apply</span>
                                            </button>
                                        </div>
                                        <div class="form-text">Coupon is validated now; shipping is calculated at checkout.</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="fw-bold mb-3">{{ __('cart.summary') }}</h5>

                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">{{ __('cart.items') }}</span>
                                    <span class="fw-semibold" data-cart-count-display>{{ $count }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">{{ __('cart.subtotal') }}</span>
                                    <span class="fw-semibold">$<span data-cart-subtotal>{{ number_format((float) $subtotal, 2) }}</span></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">{{ __('cart.discount') }}</span>
                                    <span class="text-success">- $<span data-cart-discount>{{ number_format((float) $discount, 2) }}</span></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">{{ __('cart.shipping') }}</span>
                                    <span class="text-muted">{{ __('cart.calculated_at_checkout') }}</span>
                                </div>

                                <hr>

                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted">{{ __('cart.total') }}</span>
                                    <span class="h5 mb-0 text-primary fw-bold">$<span data-cart-grand-total>{{ number_format((float) $grandTotal, 2) }}</span></span>
                                </div>

                                <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">
                                    <i class="bi bi-lock me-2"></i>{{ __('cart.proceed_to_checkout') }}
                                </a>
                                <small class="text-muted d-block mt-2">
                                    You will be asked to login before checkout.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                if (c > 0) el.classList.remove('d-none');
                else el.classList.add('d-none');
            });
        }

        function updateCartTotals(count, total) {
            document.querySelectorAll('[data-cart-count-display]').forEach((el) => {
                el.textContent = Number(count || 0);
            });

            // subtotal (pre-discount)
            document.querySelectorAll('[data-cart-subtotal]').forEach((el) => {
                el.textContent = formatMoney(total);
            });
        }

        function updatePricing(pricing) {
            const p = pricing || {};
            const subtotal = Number(p.subtotal || 0);
            const discount = Number(p.discount || 0);
            const grand = Number(p.grand_total || 0);

            document.querySelectorAll('[data-cart-subtotal]').forEach((el) => {
                el.textContent = formatMoney(subtotal);
            });
            document.querySelectorAll('[data-cart-discount]').forEach((el) => {
                el.textContent = formatMoney(discount);
            });
            document.querySelectorAll('[data-cart-grand-total]').forEach((el) => {
                el.textContent = formatMoney(grand);
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

        async function cartFetch(url, method, formData) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const opts = {
                method,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
            };

            if (formData && method !== 'GET' && method !== 'DELETE') {
                opts.body = formData;
            }

            const res = await fetch(url, opts);
            const data = await res.json();
            if (!res.ok || !data.ok) {
                throw new Error(data.message || 'Cart request failed.');
            }
            return data;
        }

        function setCartEmptyState(isEmpty) {
            const emptyEl = document.getElementById('cartEmpty');
            const contentEl = document.getElementById('cartContent');
            if (!emptyEl || !contentEl) return;

            if (isEmpty) {
                emptyEl.classList.remove('d-none');
                contentEl.classList.add('d-none');
            } else {
                emptyEl.classList.add('d-none');
                contentEl.classList.remove('d-none');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('form[data-ajax-cart]').forEach((form) => {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const kind = form.getAttribute('data-ajax-cart');
                    const methodOverride = (form.querySelector('input[name="_method"]')?.value || '').toUpperCase();
                    const method = methodOverride || 'POST';

                    // avoid double clicks
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) submitBtn.disabled = true;

                    try {
                        const formData = new FormData(form);
                        const url = form.getAttribute('action') || form.action;
                        const data = await cartFetch(url, method, formData);

                        updateCartBadge(data.cart?.count);
                        updateCartTotals(data.cart?.count, data.cart?.total);
                        if (data.pricing) updatePricing(data.pricing);

                        if (data.coupon_message) {
                            showCartToast(data.coupon_message, 'danger');
                        }

                        if (kind === 'update' && data.item) {
                            const row = document.querySelector('tr[data-product-id="' + data.item.product_id + '"]');
                            if (row) {
                                const qtyEl = row.querySelector('[data-item-qty]');
                                const subEl = row.querySelector('[data-item-subtotal]');
                                if (qtyEl) qtyEl.textContent = data.item.quantity;
                                if (subEl) subEl.textContent = formatMoney(data.item.subtotal);
                            }
                        }

                        if (kind === 'remove' && data.removed) {
                            const row = document.querySelector('tr[data-product-id="' + data.removed + '"]');
                            if (row) row.remove();
                        }

                        if (kind === 'clear') {
                            document.querySelectorAll('tr[data-product-id]').forEach((row) => row.remove());
                        }

                        const newCount = Number(data.cart?.count || 0);
                        setCartEmptyState(newCount === 0);

                        showCartToast(data.message || 'Done.', 'success');
                    } catch (err) {
                        showCartToast(err.message || 'Something went wrong.', 'danger');
                    } finally {
                        if (submitBtn) submitBtn.disabled = false;
                    }
                });
            });

            // Coupon apply/remove (AJAX)
            const couponCard = document.getElementById('couponCard');
            const feedback = document.getElementById('couponFeedback');
            const couponBody = document.getElementById('couponCardBody');

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const applyUrl = couponCard?.dataset?.applyUrl;
            const removeUrl = couponCard?.dataset?.removeUrl;

            const setFeedback = (type, message) => {
                if (!feedback) return;
                if (!message) {
                    feedback.innerHTML = '';
                    return;
                }
                const cls = type === 'success' ? 'alert-success' : 'alert-danger';
                feedback.innerHTML = `
                    <div class="alert ${cls} py-2 mb-0" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle'} me-2"></i>
                            <div class="small">${message}</div>
                        </div>
                    </div>
                `;
            };

            const renderApplied = (code, discount) => {
                if (!couponBody) return;
                couponBody.dataset.applied = '1';

                const header = couponBody.querySelector('.d-flex.justify-content-between');
                if (header) {
                    header.innerHTML = `<h5 class="fw-bold mb-0">Coupon</h5><span class="badge bg-success">Applied</span>`;
                }

                // remove old content except header + feedback
                Array.from(couponBody.children).forEach((el) => {
                    if (el === header || el === feedback) return;
                    el.remove();
                });

                couponBody.insertAdjacentHTML('beforeend', `
                    <div class="mt-3 p-3 rounded border border-success-subtle bg-success bg-opacity-10" data-coupon-applied>
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">${code}</div>
                                <div class="text-muted small">Discount: <span class="fw-semibold text-success">$${formatMoney(discount)}</span></div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success" data-coupon-remove-btn>
                                <span class="js-remove-label"><i class="bi bi-x-circle me-1"></i>Remove</span>
                            </button>
                        </div>
                    </div>
                `);

                bindCouponHandlers();
            };

            const renderEntry = () => {
                if (!couponBody) return;
                couponBody.dataset.applied = '0';

                const header = couponBody.querySelector('.d-flex.justify-content-between');
                if (header) header.innerHTML = `<h5 class="fw-bold mb-0">Coupon</h5>`;
                setFeedback(null, null);

                Array.from(couponBody.children).forEach((el) => {
                    if (el === header || el === feedback) return;
                    el.remove();
                });

                couponBody.insertAdjacentHTML('beforeend', `
                    <div class="mt-3">
                        <div class="text-muted small mb-2">Have a coupon?</div>
                        <div class="d-flex gap-2">
                            <input type="text" class="form-control" name="code" id="couponCodeInput" placeholder="Enter coupon code" autocomplete="off">
                            <button type="button" class="btn btn-outline-primary" data-coupon-apply-btn style="white-space: nowrap;">
                                <span class="js-apply-label">Apply</span>
                            </button>
                        </div>
                        <div class="form-text">Coupon is validated now; shipping is calculated at checkout.</div>
                    </div>
                `);

                bindCouponHandlers();
            };

            const bindCouponHandlers = () => {
                const applyBtn = couponBody?.querySelector('[data-coupon-apply-btn]');
                const removeBtn = couponBody?.querySelector('[data-coupon-remove-btn]');
                const codeInput = couponBody?.querySelector('#couponCodeInput');

                if (applyBtn && applyUrl && applyBtn.dataset.bound !== '1') {
                    applyBtn.dataset.bound = '1';
                    applyBtn.addEventListener('click', async () => {
                        setFeedback(null, null);
                        const code = (codeInput?.value || '').trim();
                        if (!code) {
                            setFeedback('error', 'Please enter a coupon code.');
                            return;
                        }

                        applyBtn.disabled = true;
                        const label = applyBtn.querySelector('.js-apply-label');
                        if (label) label.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Applying...';

                        try {
                            const res = await fetch(applyUrl, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                },
                                body: JSON.stringify({ code }),
                            });
                            const data = await res.json();
                            if (!res.ok || !data.ok) throw new Error(data.message || 'Invalid coupon.');

                            setFeedback('success', data.message || 'Coupon applied.');
                            if (data.pricing) updatePricing(data.pricing);
                            renderApplied(data.pricing?.coupon_code || code, Number(data.pricing?.discount || 0));
                        } catch (e) {
                            setFeedback('error', e.message || 'Invalid coupon.');
                        } finally {
                            applyBtn.disabled = false;
                            if (label) label.textContent = 'Apply';
                        }
                    });
                }

                if (removeBtn && removeUrl && removeBtn.dataset.bound !== '1') {
                    removeBtn.dataset.bound = '1';
                    removeBtn.addEventListener('click', async () => {
                        setFeedback(null, null);
                        removeBtn.disabled = true;
                        const label = removeBtn.querySelector('.js-remove-label');
                        if (label) label.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Removing...';

                        try {
                            const res = await fetch(removeUrl, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                },
                            });
                            const data = await res.json();
                            if (!res.ok || !data.ok) throw new Error(data.message || 'Could not remove coupon.');

                            setFeedback('success', data.message || 'Coupon removed.');
                            if (data.pricing) updatePricing(data.pricing);
                            renderEntry();
                        } catch (e) {
                            setFeedback('error', e.message || 'Could not remove coupon.');
                        } finally {
                            removeBtn.disabled = false;
                            if (label) label.textContent = 'Remove';
                        }
                    });
                }
            };

            bindCouponHandlers();
        });
    </script>
</body>

</html>
