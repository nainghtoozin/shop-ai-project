<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\City;
use App\Models\DeliveryType;
use App\Models\DeliveryCategory;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        $items = collect($cart)->map(function ($item) {
            $item['quantity'] = (int) ($item['quantity'] ?? 0);
            $item['price'] = (float) ($item['price'] ?? 0);
            $item['subtotal'] = $item['quantity'] * $item['price'];
            return $item;
        })->values();

        $subtotal = round($items->sum('subtotal'), 2);
        $tax = 0.00;
        $shippingCost = 0.00;
        $discount = 0.00;

        $appliedCoupon = null;
        $couponCode = (string) session()->get('coupon.code', '');
        if ($couponCode !== '') {
            $svc = new CouponService();
            $result = $svc->validateAndCalculate($couponCode, $subtotal, Auth::id());
            if (!($result['ok'] ?? false)) {
                session()->forget('coupon');
                session()->flash('error', $result['message'] ?? 'Coupon is not valid.');
            } else {
                $discount = (float) $result['discount'];
                $appliedCoupon = $result['coupon'];
            }
        }

        $total = round(($subtotal + $tax + $shippingCost) - $discount, 2);

        $paymentMethods = PaymentMethod::query()
            ->active()
            ->orderBy('name')
            ->get(['id', 'type', 'name', 'account_number', 'description']);

        $cities = City::query()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'base_charge']);

        $deliveryTypes = DeliveryType::query()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'charge_type', 'extra_charge', 'description']);

        return view('checkout.index', compact('items', 'subtotal', 'tax', 'shippingCost', 'discount', 'total', 'paymentMethods', 'cities', 'deliveryTypes', 'appliedCoupon'));
    }

    public function applyCoupon(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        $items = collect($cart)->map(function ($item) {
            $qty = (int) ($item['quantity'] ?? 0);
            $price = (float) ($item['price'] ?? 0);
            return $qty * $price;
        });

        $subtotal = round((float) $items->sum(), 2);

        $svc = new CouponService();
        $result = $svc->validateAndCalculate($validated['code'], $subtotal, Auth::id());

        if (!($result['ok'] ?? false)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => $result['message'] ?? 'Invalid coupon.',
                ], 422);
            }

            return back()->withInput()->with('error', $result['message'] ?? 'Invalid coupon.');
        }

        /** @var \App\Models\Coupon $coupon */
        $coupon = $result['coupon'];
        session()->put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
        ]);

        if ($request->expectsJson()) {
            $discount = (float) ($result['discount'] ?? 0);
            $baseTotal = round(($subtotal + 0.0) - $discount, 2);
            return response()->json([
                'ok' => true,
                'message' => 'Coupon applied successfully.',
                'coupon' => [
                    'code' => $coupon->code,
                    'discount' => $discount,
                    'base_total' => $baseTotal,
                ],
            ]);
        }

        return back()->with('success', 'Coupon applied successfully.');
    }

    public function removeCoupon(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        session()->forget('coupon');

        if ($request->expectsJson()) {
            // Recompute base_total without discount
            $cart = session()->get('cart', []);
            $subtotal = collect($cart)->sum(function ($item) {
                return ((int) ($item['quantity'] ?? 0)) * ((float) ($item['price'] ?? 0));
            });
            $subtotal = round((float) $subtotal, 2);

            return response()->json([
                'ok' => true,
                'message' => 'Coupon removed.',
                'coupon' => null,
                'base_total' => round($subtotal, 2),
                'discount' => 0.0,
            ]);
        }

        return back()->with('success', 'Coupon removed.');
    }

    public function shippingQuote(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validated = $request->validate([
            'city_id' => ['required', 'integer'],
            'delivery_type_id' => ['required', 'integer'],
        ]);

        $city = City::query()->active()->find((int) $validated['city_id']);
        $deliveryType = DeliveryType::query()->active()->find((int) $validated['delivery_type_id']);

        if (!$city || !$deliveryType) {
            return response()->json(['ok' => false, 'message' => 'Invalid shipping selection.'], 422);
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['ok' => false, 'message' => 'Cart is empty.'], 422);
        }

        $items = collect($cart)->map(function ($item, $key) {
            return [
                'product_id' => (int) ($item['product_id'] ?? $key),
                'quantity' => (int) ($item['quantity'] ?? 0),
            ];
        })->filter(fn ($i) => $i['product_id'] > 0 && $i['quantity'] > 0)->values();

        if ($items->isEmpty()) {
            return response()->json(['ok' => false, 'message' => 'Cart is empty.'], 422);
        }

        $productIds = $items->pluck('product_id')->all();

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->with(['deliveryCategory:id,name,extra_charge'])
            ->get(['id', 'delivery_category_id']);

        $productExtra = 0.0;
        foreach ($items as $i) {
            $p = $products->firstWhere('id', $i['product_id']);
            $extra = (float) ($p?->deliveryCategory?->extra_charge ?? 0);
            $productExtra += $extra * (int) $i['quantity'];
        }

        $quote = $this->calculateShipping($city, $deliveryType, $productExtra);

        return response()->json([
            'ok' => true,
            'shipping_cost' => $quote['shipping_cost'],
            'breakdown' => $quote['breakdown'],
        ]);
    }

    public function placeOrder(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->guest(route('login'))->with('error', 'Please login to checkout.');
        }

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_email' => 'nullable|email|max:255',
            'shipping_address' => 'required|string|max:2000',
            'billing_address' => 'nullable|string|max:2000',
            'note' => 'nullable|string|max:2000',
            'payment_method_id' => ['required', 'integer'],
            'city_id' => ['required', 'integer'],
            'delivery_type_id' => ['required', 'integer'],
        ]);

        $paymentMethod = PaymentMethod::query()
            ->whereKey((int) $validated['payment_method_id'])
            ->where('is_active', true)
            ->first();

        if (!$paymentMethod) {
            throw ValidationException::withMessages([
                'payment_method_id' => 'Selected payment method is not available.',
            ]);
        }

        $isCod = strtoupper((string) $paymentMethod->type) === 'COD';

        $request->validate([
            'payment_proof' => [
                Rule::requiredIf(!$isCod),
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ]);

        $city = City::query()->active()->find((int) $validated['city_id']);
        $deliveryType = DeliveryType::query()->active()->find((int) $validated['delivery_type_id']);
        if (!$city || !$deliveryType) {
            throw ValidationException::withMessages([
                'city_id' => 'Selected city is not available.',
            ]);
        }

        $items = collect($cart)->map(function ($item, $key) {
            return [
                'product_id' => (int) ($item['product_id'] ?? $key),
                'name' => (string) ($item['name'] ?? ''),
                'sku' => $item['sku'] ?? null,
                'price' => (float) ($item['price'] ?? 0),
                'quantity' => (int) ($item['quantity'] ?? 0),
            ];
        })->filter(fn ($i) => $i['product_id'] > 0 && $i['quantity'] > 0)->values();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = round($items->sum(fn ($i) => $i['price'] * $i['quantity']), 2);
        $tax = 0.00;
        $shippingCost = 0.00; // calculated inside transaction from locked products
        $discount = 0.00;
        $total = round(($subtotal + $tax + $shippingCost) - $discount, 2);

        // Store proof file first (if provided). If the transaction fails we delete it.
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payments', 'public');
        }

        try {
            $order = DB::transaction(function () use ($validated, $paymentMethod, $city, $deliveryType, $items, $subtotal, $tax, $discount, $paymentProofPath) {
            $productIds = $items->pluck('product_id')->all();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get(['id', 'name', 'sku', 'selling_price', 'stock', 'status', 'not_for_sale', 'delivery_category_id']);

            if ($products->count() !== count($productIds)) {
                throw ValidationException::withMessages([
                    'cart' => 'One or more products in your cart are no longer available.',
                ]);
            }

            $orderNumber = $this->generateOrderNumber();

            $categoryIds = $products->pluck('delivery_category_id')->filter()->unique()->values()->all();
            $categories = DeliveryCategory::query()
                ->whereIn('id', $categoryIds)
                ->get(['id', 'extra_charge'])
                ->keyBy('id');

            $productExtra = 0.0;
            foreach ($items as $item) {
                $p = $products->firstWhere('id', $item['product_id']);
                if (!$p) continue;
                $extra = (float) ($categories[$p->delivery_category_id]->extra_charge ?? 0);
                $productExtra += $extra * (int) $item['quantity'];
            }

            $quote = $this->calculateShipping($city, $deliveryType, $productExtra);
            $shippingCost = (float) $quote['shipping_cost'];

            // Coupon (re-validate inside transaction; never trust session blindly)
            $couponCode = (string) session()->get('coupon.code', '');
            $couponId = (int) session()->get('coupon.id', 0);
            $coupon = null;
            $discount = 0.0;

            if ($couponId > 0 && $couponCode !== '') {
                $coupon = Coupon::query()->whereKey($couponId)->lockForUpdate()->first();
                if (!$coupon || strtoupper($coupon->code) !== strtoupper($couponCode) || !$coupon->is_active) {
                    throw ValidationException::withMessages([
                        'coupon' => 'Your coupon is no longer valid. Please apply again.',
                    ]);
                }

                $now = now();
                if ($now->lt($coupon->start_date) || $now->gt($coupon->end_date)) {
                    throw ValidationException::withMessages([
                        'coupon' => 'Your coupon is no longer valid. Please apply again.',
                    ]);
                }

                if ($subtotal < (float) ($coupon->min_order_amount ?? 0)) {
                    throw ValidationException::withMessages([
                        'coupon' => 'Minimum order amount not met for this coupon.',
                    ]);
                }

                if (!is_null($coupon->usage_limit) && (int) $coupon->used_count >= (int) $coupon->usage_limit) {
                    throw ValidationException::withMessages([
                        'coupon' => 'This coupon has reached its usage limit.',
                    ]);
                }

                if (!is_null($coupon->per_user_limit)) {
                    $usedByUser = CouponUsage::query()
                        ->where('coupon_id', $coupon->id)
                        ->where('user_id', Auth::id())
                        ->count();

                    if ($usedByUser >= (int) $coupon->per_user_limit) {
                        throw ValidationException::withMessages([
                            'coupon' => 'You have already used this coupon the maximum number of times.',
                        ]);
                    }
                }

                $svc = new CouponService();
                $discount = (float) $svc->calculateDiscountAmount($coupon->type, (float) $coupon->value, (float) $subtotal, $coupon->max_discount_amount);
            }

            $total = round(($subtotal + $tax + $shippingCost) - $discount, 2);

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => $orderNumber,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'coupon_code' => $coupon?->code,
                'total' => $total,
                'status' => 'pending',
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
                'city_name' => $city->name,
                'delivery_type' => $deliveryType->name,
                'shipping_address' => $validated['shipping_address'],
                'billing_address' => $validated['billing_address'] ?? null,
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($items as $item) {
                $product = $products->firstWhere('id', $item['product_id']);

                if (!$product || !$product->status || $product->not_for_sale) {
                    throw ValidationException::withMessages([
                        'cart' => 'One or more products in your cart are not available for sale.',
                    ]);
                }

                if ((int) $product->stock < (int) $item['quantity']) {
                    throw ValidationException::withMessages([
                        'cart' => "Insufficient stock for {$product->name}.",
                    ]);
                }

                $lineTotal = round(((float) $item['price']) * ((int) $item['quantity']), 2);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'price' => (float) $item['price'],
                    'quantity' => (int) $item['quantity'],
                    'total' => $lineTotal,
                ]);

                // Deduct stock
                $product->decrement('stock', (int) $item['quantity']);
            }

            Payment::create([
                'order_id' => $order->id,
                // Backward-compatible column (used in existing admin views)
                'payment_method' => $paymentMethod->name,
                'payment_proof' => $paymentProofPath,
                // Snapshots (do not rely on future edits to payment methods)
                'payment_method_name' => $paymentMethod->name,
                'payment_type' => $paymentMethod->type,
                'account_number' => $paymentMethod->account_number,
                'payment_status' => 'pending',
                'amount' => $total,
                'transaction_id' => null,
            ]);

            if ($coupon) {
                $coupon->increment('used_count');
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'used_at' => now(),
                ]);
            }

            return $order;
            });
        } catch (\Throwable $e) {
            if ($paymentProofPath) {
                Storage::disk('public')->delete($paymentProofPath);
            }
            throw $e;
        }

        session()->forget('cart');
        session()->forget('coupon');

        return redirect()->route('checkout.success', $order)->with('success', 'Order placed successfully.');
    }

    public function success(Order $order)
    {
        if (!Auth::check() || (int) $order->user_id !== (int) Auth::id()) {
            abort(404);
        }

        $order->load(['items', 'payment']);

        return view('checkout.success', compact('order'));
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'ORD-' . now()->format('Ymd');

        for ($i = 0; $i < 10; $i++) {
            $number = $prefix . '-' . strtoupper(Str::random(6));
            if (!Order::where('order_number', $number)->exists()) {
                return $number;
            }
        }

        // fallback
        return $prefix . '-' . strtoupper(Str::random(10));
    }

    private function calculateShipping(City $city, DeliveryType $deliveryType, float $productExtra): array
    {
        $base = (float) $city->base_charge;
        $productExtra = max(0.0, (float) $productExtra);

        $basePlusProduct = round($base + $productExtra, 2);

        $deliveryExtra = 0.0;
        if ($deliveryType->charge_type === 'percent') {
            $deliveryExtra = round($basePlusProduct * ((float) $deliveryType->extra_charge) / 100, 2);
        } else {
            $deliveryExtra = (float) $deliveryType->extra_charge;
        }

        $shipping = round($basePlusProduct + $deliveryExtra, 2);

        return [
            'shipping_cost' => $shipping,
            'breakdown' => [
                'city_base' => round($base, 2),
                'product_extra' => round($productExtra, 2),
                'delivery_type' => $deliveryType->name,
                'delivery_extra' => round($deliveryExtra, 2),
            ],
        ];
    }
}
