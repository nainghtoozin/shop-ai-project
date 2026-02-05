<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CartController extends Controller
{
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

        return response()->json(array_merge([
            'ok' => true,
            'message' => $message,
            'cart' => [
                'count' => $totals['count'],
                'total' => $totals['total'],
            ],
        ], $extra));
    }

    public function index()
    {
        $cart = session()->get('cart', []);

        $items = collect($cart)->map(function ($item) {
            $item['subtotal'] = (float) $item['price'] * (int) $item['quantity'];
            return $item;
        });

        $total = $items->sum('subtotal');
        $count = $items->sum('quantity');

        return view('carts.index', [
            'items' => $items,
            'total' => $total,
            'count' => $count,
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

        if ($request->expectsJson()) {
            return $this->jsonCartResponse([], 'Cart cleared.');
        }

        return back()->with('success', 'Cart cleared.');
    }
}
