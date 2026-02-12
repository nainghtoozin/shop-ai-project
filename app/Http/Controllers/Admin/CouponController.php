<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('coupon.view'), 403);

        $search = trim((string) $request->get('search', ''));
        $status = (string) $request->get('status', '');

        $coupons = Coupon::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%");
            })
            ->when($status !== '', function ($q) use ($status) {
                if ($status === 'active') $q->where('is_active', true);
                if ($status === 'inactive') $q->where('is_active', false);
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.coupons.index', compact('coupons', 'search', 'status'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('coupon.create'), 403);

        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('coupon.create'), 403);

        $validated = $this->validateCoupon($request);
        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function edit(Request $request, Coupon $coupon)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('coupon.edit'), 403);

        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('coupon.edit'), 403);

        $validated = $this->validateCoupon($request, $coupon);
        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Request $request, Coupon $coupon)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('coupon.delete'), 403);

        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
    }

    public function toggleStatus(Request $request, Coupon $coupon)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('coupon.edit'), 403);

        $coupon->is_active = !$coupon->is_active;
        $coupon->save();

        return response()->json([
            'success' => true,
            'is_active' => (bool) $coupon->is_active,
        ]);
    }

    private function validateCoupon(Request $request, ?Coupon $coupon = null): array
    {
        // Normalize code for case-insensitive uniqueness
        $normalized = strtoupper(trim((string) $request->input('code', '')));
        $normalized = preg_replace('/\s+/', '', $normalized);
        $request->merge(['code' => $normalized]);

        $unique = Rule::unique('coupons', 'code');
        if ($coupon) $unique = $unique->ignore($coupon->id);

        return $request->validate([
            'code' => ['required', 'string', 'max:50', $unique],
            'type' => ['required', Rule::in(['percentage', 'fixed'])],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_order_amount' => ['required', 'numeric', 'min:0'],
            'max_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'per_user_limit' => ['nullable', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'is_active' => ['nullable'],
        ], [], [
            'min_order_amount' => 'minimum order amount',
            'max_discount_amount' => 'maximum discount amount',
            'usage_limit' => 'usage limit',
            'per_user_limit' => 'per-user limit',
        ]);
    }
}
