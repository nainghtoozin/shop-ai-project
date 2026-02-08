<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.view'), 403);

        $search = trim((string) $request->get('search', ''));
        $status = (string) $request->get('status', '');

        $paymentMethods = PaymentMethod::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('type', 'like', "%{$search}%")
                        ->orWhere('account_number', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', function ($q) use ($status) {
                if ($status === 'active') $q->where('is_active', true);
                if ($status === 'inactive') $q->where('is_active', false);
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.payment-methods.index', compact('paymentMethods', 'search', 'status'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        return view('admin.payment-methods.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable'],
        ]);

        PaymentMethod::create([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'account_number' => $validated['account_number'],
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method created successfully.');
    }

    public function edit(Request $request, PaymentMethod $payment_method)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $paymentMethod = $payment_method;
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $payment_method)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable'],
        ]);

        $payment_method->update([
            'type' => $validated['type'],
            'name' => $validated['name'],
            'account_number' => $validated['account_number'],
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method updated successfully.');
    }

    public function destroy(Request $request, PaymentMethod $payment_method)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $payment_method->delete();

        return redirect()->route('admin.payment-methods.index')->with('success', 'Payment method deleted successfully.');
    }

    public function toggleStatus(Request $request, PaymentMethod $payment_method)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $payment_method->is_active = !$payment_method->is_active;
        $payment_method->save();

        return response()->json([
            'success' => true,
            'is_active' => (bool) $payment_method->is_active,
            'message' => 'Payment method status updated successfully.',
        ]);
    }
}
