<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CouponService
{
    /**
     * Validate a coupon code and compute discount for a given subtotal.
     * Returns [coupon, discount] on success.
     * Throws \Illuminate\Validation\ValidationException style arrays handled by caller.
     */
    public function validateAndCalculate(string $code, float $subtotal, ?int $userId = null): array
    {
        $normalized = $this->normalizeCode($code);
        if ($normalized === '') {
            return ['ok' => false, 'message' => 'Please enter a coupon code.'];
        }

        $coupon = Coupon::query()->where('code', $normalized)->first();
        if (!$coupon) {
            return ['ok' => false, 'message' => 'Invalid coupon code.'];
        }

        if (!$coupon->is_active) {
            return ['ok' => false, 'message' => 'This coupon is currently inactive.'];
        }

        $now = now();
        if ($coupon->start_date && $now->lt($coupon->start_date)) {
            return ['ok' => false, 'message' => 'This coupon is not active yet.'];
        }
        if ($coupon->end_date && $now->gt($coupon->end_date)) {
            return ['ok' => false, 'message' => 'This coupon has expired.'];
        }

        $subtotal = max(0.0, (float) $subtotal);
        $min = (float) ($coupon->min_order_amount ?? 0);
        if ($subtotal < $min) {
            return ['ok' => false, 'message' => 'Minimum order amount not met for this coupon.'];
        }

        if (!is_null($coupon->usage_limit) && (int) $coupon->used_count >= (int) $coupon->usage_limit) {
            return ['ok' => false, 'message' => 'This coupon has reached its usage limit.'];
        }

        if (!is_null($coupon->per_user_limit) && !$userId) {
            return ['ok' => false, 'message' => 'Please login to use this coupon.'];
        }

        if ($userId && !is_null($coupon->per_user_limit)) {
            $usedByUser = CouponUsage::query()
                ->where('coupon_id', $coupon->id)
                ->where('user_id', $userId)
                ->count();

            if ($usedByUser >= (int) $coupon->per_user_limit) {
                return ['ok' => false, 'message' => 'You have already used this coupon the maximum number of times.'];
            }
        }

        $discount = $this->calculateDiscountAmount($coupon->type, (float) $coupon->value, $subtotal, $coupon->max_discount_amount);
        if ($discount <= 0) {
            return ['ok' => false, 'message' => 'This coupon does not apply to your order.'];
        }

        return ['ok' => true, 'coupon' => $coupon, 'discount' => $discount];
    }

    public function normalizeCode(string $code): string
    {
        $c = strtoupper(trim($code));
        $c = preg_replace('/\s+/', '', $c);
        return (string) $c;
    }

    public function calculateDiscountAmount(string $type, float $value, float $subtotal, $maxDiscount = null): float
    {
        $subtotal = max(0.0, (float) $subtotal);
        $value = max(0.0, (float) $value);

        if ($subtotal <= 0 || $value <= 0) return 0.0;

        if ($type === 'fixed') {
            return round(min($value, $subtotal), 2);
        }

        // percentage
        $percent = min($value, 100.0);
        $raw = round($subtotal * $percent / 100, 2);

        $cap = is_null($maxDiscount) ? null : (float) $maxDiscount;
        if ($cap !== null && $cap > 0) {
            $raw = min($raw, $cap);
        }

        return round(min($raw, $subtotal), 2);
    }
}
