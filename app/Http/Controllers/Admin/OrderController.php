<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('order.view.all'), 403);

        $query = Order::query()
            ->with(['user:id,name,email', 'payment:id,order_id,payment_method,payment_status,amount,transaction_id,payment_proof'])
            ->withCount('items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        $statusOptions = [
            'pending',
            'confirmed',
            'processing',
            'shipped',
            'completed',
            'cancelled',
        ];

        return view('admin.orders.index', compact('orders', 'statusOptions'));
    }

    public function show(Order $order)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('order.view.all'), 403);

        $order->load([
            'user:id,name,email',
            'items' => function ($q) {
                $q->latest();
            },
            'payment',
        ]);

        $statusOptions = [
            'pending',
            'confirmed',
            'processing',
            'shipped',
            'completed',
            'cancelled',
        ];

        return view('admin.orders.edit', compact('order', 'statusOptions'));
    }

    public function update(Request $request, Order $order)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('order.edit'), 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'confirmed', 'processing', 'shipped', 'completed', 'cancelled'])],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $order->update([
            'status' => $validated['status'],
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }
}
