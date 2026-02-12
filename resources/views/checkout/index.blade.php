<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Shop') }} - Checkout</title>

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
                </div>
            </div>
        </div>
    </nav>

    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h1 class="display-6 fw-bold mb-1">{{ __('checkout.title') }}</h1>
                    <p class="text-muted mb-0">{{ __('checkout.confirm_details') }}</p>
                </div>
                <div class="text-muted small">{{ $items->sum('quantity') }} items</div>
            </div>

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

            @if ($items->count() === 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-5 text-center">
                        <i class="bi bi-cart3 text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 mb-2">Your cart is empty</h5>
                        <p class="text-muted mb-4">Add products to your cart before checking out.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            <i class="bi bi-grid me-2"></i>Browse Products
                        </a>
                    </div>
                </div>
            @else
                <form method="POST" action="{{ route('checkout.place') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-3">Customer Details</h5>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', auth()->user()->name ?? '') }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                                            <input type="text" name="customer_phone" class="form-control" value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="customer_email" class="form-control" value="{{ old('customer_email', auth()->user()->email ?? '') }}" placeholder="optional">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-3">Shipping Address</h5>

                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">City <span class="text-danger">*</span></label>
                                            <select name="city_id" id="city_id" class="form-select" required {{ ($cities ?? collect())->isEmpty() ? 'disabled' : '' }}>
                                                <option value="">Select City</option>
                                                @foreach (($cities ?? collect()) as $c)
                                                    <option value="{{ $c->id }}" data-base-charge="{{ $c->base_charge }}" {{ old('city_id') == $c->id ? 'selected' : '' }}>
                                                        {{ $c->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if (($cities ?? collect())->isEmpty())
                                                <div class="form-text text-danger">No active cities available. Please contact support.</div>
                                            @endif
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Delivery Type <span class="text-danger">*</span></label>
                                            <select name="delivery_type_id" id="delivery_type_id" class="form-select" required {{ ($deliveryTypes ?? collect())->isEmpty() ? 'disabled' : '' }}>
                                                <option value="">Select Delivery Type</option>
                                                @foreach (($deliveryTypes ?? collect()) as $dt)
                                                    <option value="{{ $dt->id }}" data-charge-type="{{ $dt->charge_type }}" data-extra-charge="{{ $dt->extra_charge }}" {{ old('delivery_type_id') == $dt->id ? 'selected' : '' }}>
                                                        {{ $dt->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if (($deliveryTypes ?? collect())->isEmpty())
                                                <div class="form-text text-danger">No active delivery types available. Please contact support.</div>
                                            @endif
                                        </div>
                                        <div class="col-12">
                                            <div class="form-text" id="shippingQuoteHelp">Select city and delivery type to calculate shipping.</div>
                                        </div>
                                    </div>

                                    <textarea name="shipping_address" class="form-control" rows="4" required>{{ old('shipping_address') }}</textarea>

                                    <div class="mt-3">
                                        <label class="form-label">Billing Address</label>
                                        <textarea name="billing_address" class="form-control" rows="3" placeholder="optional">{{ old('billing_address') }}</textarea>
                                        <div class="form-text">Leave blank if same as shipping.</div>
                                    </div>

                                    <div class="mt-3">
                                        <label class="form-label">Order Note</label>
                                        <textarea name="note" class="form-control" rows="2" placeholder="optional">{{ old('note') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-3">Payment Method</h5>

                                    <div class="mb-3">
                                        <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                        <select name="payment_method_id" id="payment_method" class="form-select" required {{ ($paymentMethods ?? collect())->isEmpty() ? 'disabled' : '' }}>
                                            @php($oldPm = old('payment_method_id'))
                                            @foreach (($paymentMethods ?? collect()) as $pm)
                                                <option value="{{ $pm->id }}"
                                                    data-type="{{ $pm->type }}"
                                                    data-account="{{ $pm->account_number }}"
                                                    data-description="{{ $pm->description }}"
                                                    {{ (string) $oldPm === (string) $pm->id ? 'selected' : '' }}>
                                                    {{ $pm->name }}
                                                </option>
                                            @endforeach

                                            @if (($paymentMethods ?? collect())->isEmpty())
                                                <option value="" selected>No payment methods available</option>
                                            @endif
                                        </select>

                                        <div class="form-text" id="paymentMethodHelp">Select a payment method to continue.</div>
                                    </div>

                                    <div class="mb-0" id="paymentProofWrap">
                                        <label class="form-label">Payment Proof <span class="text-danger" id="paymentProofRequired">*</span></label>
                                        <input type="file" name="payment_proof" id="payment_proof" class="form-control" accept="image/png,image/jpeg,image/webp">
                                        <div class="form-text">Required for non-COD methods. Max 4MB.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-3">Your Items</h5>

                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Product</th>
                                                    <th class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($items as $item)
                                                    <tr>
                                                        <td>
                                                            <div class="fw-semibold">{{ $item['name'] }}</div>
                                                            <div class="text-muted small">
                                                                {{ (int) $item['quantity'] }} x ${{ number_format((float) $item['price'], 2) }}
                                                            </div>
                                                        </td>
                                                        <td class="text-end fw-semibold">
                                                            ${{ number_format((float) $item['subtotal'], 2) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="fw-bold mb-3">Order Summary</h5>

                                    <div id="js-order-base" data-base-total="{{ number_format(($subtotal + $tax) - $discount, 2, '.', '') }}" class="d-none"></div>

                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal</span>
                                        <span>${{ number_format($subtotal, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Tax</span>
                                        <span>${{ number_format($tax, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">{{ __('checkout.discount') }}</span>
                                        <span class="text-success">- $<span id="js-discount">{{ number_format($discount, 2) }}</span></span>
                                    </div>
                                    @if (!empty($appliedCoupon))
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">{{ __('checkout.coupon') }}</span>
                                            <span class="text-muted"><code>{{ $appliedCoupon->code }}</code></span>
                                        </div>
                                    @endif
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Shipping</span>
                                        <span>$<span id="js-shipping-cost">{{ number_format($shippingCost, 2) }}</span></span>
                                    </div>
                                    <hr>

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fw-bold">Total</span>
                                        <span class="h4 mb-0 text-primary fw-bold">$<span id="js-grand-total">{{ number_format($total, 2) }}</span></span>
                                    </div>

                                    <button type="submit" id="placeOrderBtn" class="btn btn-primary w-100 btn-lg" {{ ($paymentMethods ?? collect())->isEmpty() ? 'disabled' : '' }}>
                                        <i class="bi bi-check2-circle me-2"></i>{{ __('checkout.place_order') }}
                                    </button>

                                    <a href="{{ route('cart.index') }}" class="btn btn-outline-primary w-100 mt-2">
                                        <i class="bi bi-arrow-left me-2"></i>Back to Cart
                                    </a>

                                    <small class="text-muted d-block mt-3">
                                        By placing your order, you confirm your details are correct.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </section>
</body>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const method = document.getElementById('payment_method');
        const proof = document.getElementById('payment_proof');
        const req = document.getElementById('paymentProofRequired');
        const help = document.getElementById('paymentMethodHelp');

        const city = document.getElementById('city_id');
        const deliveryType = document.getElementById('delivery_type_id');
        const shippingHelp = document.getElementById('shippingQuoteHelp');
        const shippingEl = document.getElementById('js-shipping-cost');
        const grandTotalEl = document.getElementById('js-grand-total');
        const baseEl = document.getElementById('js-order-base');
        const placeBtn = document.getElementById('placeOrderBtn');

        const formatMoney = (n) => {
            const v = Number(n || 0);
            return v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };

        const getSelected = () => {
            const opt = method?.options?.[method.selectedIndex];
            if (!opt) return null;
            return {
                type: (opt.dataset.type || '').toString(),
                account: (opt.dataset.account || '').toString(),
                description: (opt.dataset.description || '').toString(),
            };
        };

        const sync = () => {
            const sel = getSelected();
            const isCOD = (sel?.type || '').trim().toUpperCase() === 'COD';
            if (proof) proof.required = !isCOD;
            if (req) req.classList.toggle('d-none', isCOD);

            if (help && sel) {
                let text = sel.type ? ('Type: ' + sel.type) : '';
                if (sel.account) text += (text ? ' | ' : '') + ('Account: ' + sel.account);
                if (sel.description) text += (text ? ' | ' : '') + sel.description;
                help.textContent = text || 'Select a payment method to continue.';
            }
        };

        if (method) method.addEventListener('change', sync);
        sync();

        async function fetchShippingQuote() {
            const cityId = city?.value;
            const dtId = deliveryType?.value;

            if (!cityId || !dtId) {
                if (shippingHelp) shippingHelp.textContent = 'Select city and delivery type to calculate shipping.';
                if (shippingEl) shippingEl.textContent = formatMoney(0);

                const baseTotal = Number(baseEl?.dataset?.baseTotal || 0);
                if (grandTotalEl) grandTotalEl.textContent = formatMoney(baseTotal);
                if (placeBtn && !placeBtn.hasAttribute('data-payment-disabled')) placeBtn.disabled = true;
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const url = `{{ route('checkout.shipping-quote') }}`;

            if (shippingHelp) shippingHelp.textContent = 'Calculating shipping...';

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({ city_id: cityId, delivery_type_id: dtId }),
                });

                const data = await res.json();
                if (!res.ok || !data.ok) {
                    throw new Error(data?.message || 'Could not calculate shipping.');
                }

                const shipping = Number(data.shipping_cost || 0);
                const baseTotal = Number(baseEl?.dataset?.baseTotal || 0);
                const grand = baseTotal + shipping;

                if (shippingEl) shippingEl.textContent = formatMoney(shipping);
                if (grandTotalEl) grandTotalEl.textContent = formatMoney(grand);

                if (shippingHelp) {
                    const b = data.breakdown || {};
                    const detail = `Base: $${formatMoney(b.city_base)} | Items: $${formatMoney(b.product_extra)} | ${b.delivery_type || 'Delivery'}: $${formatMoney(b.delivery_extra)}`;
                    shippingHelp.textContent = detail;
                }

                // Allow submit (other validation may still block)
                if (placeBtn && !placeBtn.hasAttribute('data-payment-disabled')) {
                    placeBtn.disabled = false;
                }
            } catch (e) {
                if (shippingHelp) shippingHelp.textContent = e.message || 'Could not calculate shipping.';
                if (placeBtn) placeBtn.disabled = true;
            }
        }

        // If payment methods are missing we keep the existing disabled state.
        if (placeBtn && placeBtn.disabled) {
            placeBtn.setAttribute('data-payment-disabled', '1');
        }

        if (city) city.addEventListener('change', fetchShippingQuote);
        if (deliveryType) deliveryType.addEventListener('change', fetchShippingQuote);
        fetchShippingQuote();

        // expose for external updates
        window.__refreshShippingQuote = fetchShippingQuote;
    });
</script>

</html>
