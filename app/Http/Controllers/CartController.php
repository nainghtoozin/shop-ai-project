<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function pricingFromCart(array $cart): array
    {
        $totals = $this->cartTotals($cart);
        $subtotal = (float) $totals['total'];
        $discount = 0.0;
        $couponCode = null;
        $couponMessage = null;

        $sessionCode = (string) session()->get('coupon.code', '');
        if ($sessionCode !== '') {
            $svc = new CouponService();
            $result = $svc->validateAndCalculate($sessionCode, $subtotal, Auth::id() ?: null);
            if (!($result['ok'] ?? false)) {
                session()->forget('coupon');
                $couponMessage = $result['message'] ?? 'Coupon is not valid.';
            } else {
                $discount = (float) ($result['discount'] ?? 0);
                $couponCode = $result['coupon']?->code;
            }
        }

        $grand = round(max(0.0, $subtotal - $discount), 2);

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'grand_total' => $grand,
            'coupon_code' => $couponCode,
            'coupon_message' => $couponMessage,
        ];
    }

    private function cartTotals(array $cart): array
    {
        $count = 0;
        $total = 0.0;

        foreach ($cart as $item) {
            $qty = (int) ($item['quantity'] ?? 0);
            $price = (float) ($item['price'] ?? 0);
            $count += $qty;
            $total += $qty * $price;
        }

        return [
            'count' => $count,
            'total' => round($total, 2),
        ];
    }

    private function jsonCartResponse(array $cart, string $message, array $extra = []): JsonResponse
    {
        $totals = $this->cartTotals($cart);
        $pricing = $this->pricingFromCart($cart);

        return response()->json(array_merge([
            'ok' => true,
            'message' => $message,
            'cart' => [
                'count' => $totals['count'],
                'total' => $totals['total'],
            ],
            'pricing' => [
                'subtotal' => $pricing['subtotal'],
                'discount' => $pricing['discount'],
                'grand_total' => $pricing['grand_total'],
                'coupon_code' => $pricing['coupon_code'],
            ],
            'coupon_message' => $pricing['coupon_message'],
        ], $extra));
    }

    public function index()
    {
        $cart = session()->get('cart', []);

        $items = collect($cart)->map(function ($item) {
            $item['subtotal'] = (float) $item['price'] * (int) $item['quantity'];
            return $item;
        });

        $subtotal = (float) $items->sum('subtotal');
        $count = $items->sum('quantity');

        $pricing = $this->pricingFromCart($cart);

        // If cart is empty, always clear coupon.
        if ((int) $count === 0) {
            session()->forget('coupon');
        }

        return view('carts.index', [
            'items' => $items,
            'subtotal' => $pricing['subtotal'],
            'discount' => $pricing['discount'],
            'grandTotal' => $pricing['grand_total'],
            'couponCode' => $pricing['coupon_code'],
            'couponMessage' => $pricing['coupon_message'],
            'count' => $count,
        ]);
    }

    public function applyCoupon(Request $request): JsonResponse
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return response()->json(['ok' => false, 'message' => 'Your cart is empty.'], 422);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        // Prevent multiple coupons (force remove first)
        if (session()->has('coupon.code')) {
            return response()->json([
                'ok' => false,
                'message' => 'A coupon is already applied. Remove it first to apply a different one.',
            ], 422);
        }

        $subtotal = (float) $this->cartTotals($cart)['total'];

        $svc = new CouponService();
        $result = $svc->validateAndCalculate($validated['code'], $subtotal, Auth::id() ?: null);
        if (!($result['ok'] ?? false)) {
            return response()->json([
                'ok' => false,
                'message' => $result['message'] ?? 'Invalid coupon.',
            ], 422);
        }

        /** @var \App\Models\Coupon $coupon */
        $coupon = $result['coupon'];
        session()->put('coupon', ['id' => $coupon->id, 'code' => $coupon->code]);

        $pricing = $this->pricingFromCart($cart);

        return response()->json([
            'ok' => true,
            'message' => 'Coupon applied successfully.',
            'pricing' => [
                'subtotal' => $pricing['subtotal'],
                'discount' => $pricing['discount'],
                'grand_total' => $pricing['grand_total'],
                'coupon_code' => $pricing['coupon_code'],
            ],
        ]);
    }

    public function removeCoupon(Request $request): JsonResponse
    {
        session()->forget('coupon');
        $cart = session()->get('cart', []);
        $pricing = $this->pricingFromCart($cart);

        return response()->json([
            'ok' => true,
            'message' => 'Coupon removed.',
            'pricing' => [
                'subtotal' => $pricing['subtotal'],
                'discount' => $pricing['discount'],
                'grand_total' => $pricing['grand_total'],
                'coupon_code' => null,
            ],
        ]);
    }

    public function add(Request $request, int $productId)
    {
        $product = Product::query()
            ->with([
                'unit:id,name,short_name',
                'primaryImage:id,product_id,image,is_primary,sort_order',
            ])
            ->whereKey($productId)
            ->firstOrFail();

        if (!$product->status || $product->not_for_sale) {
            abort(404);
        }

        $cart = session()->get('cart', []);
        $key = (string) $product->id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] = (int) $cart[$key]['quantity'] + 1;
        } else {
            $cart[$key] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->selling_price,
                'quantity' => 1,
                'image' => $product->image_url,
                'unit' => $product->unit?->short_name ?: ($product->unit?->name ?: null),
            ];
        }

        session()->put('cart', $cart);

        if ($request->expectsJson()) {
            $qty = (int) $cart[$key]['quantity'];
            $subtotal = (float) $cart[$key]['price'] * $qty;

            return $this->jsonCartResponse($cart, 'Added to cart.', [
                'item' => [
                    'product_id' => (int) $cart[$key]['product_id'],
                    'quantity' => $qty,
                    'price' => (float) $cart[$key]['price'],
                    'subtotal' => round($subtotal, 2),
                ],
            ]);
        }

        return back()->with('success', 'Added to cart.');
    }

    public function update(Request $request, int $productId)
    {
        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1|max:999',
            'action' => 'nullable|in:inc,dec',
        ]);

        $cart = session()->get('cart', []);
        $key = (string) $productId;

        if (!isset($cart[$key])) {
            return redirect()->route('cart.index')->with('error', 'Item not found in cart.');
        }

        $qty = (int) $cart[$key]['quantity'];

        if (($validated['action'] ?? null) === 'inc') {
            $qty++;
        } elseif (($validated['action'] ?? null) === 'dec') {
            $qty = max(1, $qty - 1);
        } elseif (isset($validated['quantity'])) {
            $qty = (int) $validated['quantity'];
        }

        $cart[$key]['quantity'] = $qty;
        session()->put('cart', $cart);

        if ($request->expectsJson()) {
            $subtotal = (float) $cart[$key]['price'] * (int) $cart[$key]['quantity'];

            return $this->jsonCartResponse($cart, 'Cart updated.', [
                'item' => [
                    'product_id' => (int) $productId,
                    'quantity' => (int) $cart[$key]['quantity'],
                    'price' => (float) $cart[$key]['price'],
                    'subtotal' => round($subtotal, 2),
                ],
            ]);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function remove(Request $request, int $productId)
    {
        $cart = session()->get('cart', []);
        $key = (string) $productId;

        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
        }

        if (empty($cart)) {
            session()->forget('coupon');
        }

        if ($request->expectsJson()) {
            return $this->jsonCartResponse($cart, 'Item removed from cart.', [
                'removed' => (int) $productId,
            ]);
        }

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear(Request $request)
    {
        session()->forget('cart');
        session()->forget('coupon');

        if ($request->expectsJson()) {
            return $this->jsonCartResponse([], 'Cart cleared.');
        }

        return back()->with('success', 'Cart cleared.');
    }
}
