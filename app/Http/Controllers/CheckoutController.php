<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
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
        $total = round(($subtotal + $tax + $shippingCost) - $discount, 2);

        return view('checkout.index', compact('items', 'subtotal', 'tax', 'shippingCost', 'discount', 'total'));
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
            'payment_method' => ['required', Rule::in(['COD', 'KBZPay', 'WavePay', 'Bank Transfer'])],
            'payment_proof' => ['nullable', 'required_unless:payment_method,COD', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

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
        $shippingCost = 0.00;
        $discount = 0.00;
        $total = round(($subtotal + $tax + $shippingCost) - $discount, 2);

        // Store proof file first (if provided). If the transaction fails we delete it.
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('payments', 'public');
        }

        try {
            $order = DB::transaction(function () use ($validated, $items, $subtotal, $tax, $shippingCost, $discount, $total, $paymentProofPath) {
            $productIds = $items->pluck('product_id')->all();

            $products = Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get(['id', 'name', 'sku', 'selling_price', 'stock', 'status', 'not_for_sale']);

            if ($products->count() !== count($productIds)) {
                throw ValidationException::withMessages([
                    'cart' => 'One or more products in your cart are no longer available.',
                ]);
            }

            $orderNumber = $this->generateOrderNumber();

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => $orderNumber,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'total' => $total,
                'status' => 'pending',
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'] ?? null,
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
                'payment_method' => $validated['payment_method'],
                'payment_proof' => $paymentProofPath,
                'payment_status' => 'pending',
                'amount' => $total,
                'transaction_id' => null,
            ]);

            return $order;
            });
        } catch (\Throwable $e) {
            if ($paymentProofPath) {
                Storage::disk('public')->delete($paymentProofPath);
            }
            throw $e;
        }

        session()->forget('cart');

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
}
