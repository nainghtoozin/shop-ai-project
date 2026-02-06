<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('dashboard.view'), 403);

        $totalOrders = Order::query()->count();
        $totalProducts = Product::query()->count();
        $totalCategories = Category::query()->count();
        $totalUsers = User::query()->count();

        $totalRevenue = (float) Order::query()
            ->where('status', 'completed')
            ->sum('total');

        $monthlyRevenue = (float) Order::query()
            ->where('status', 'completed')
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total');

        return view('dashboard', compact(
            'totalOrders',
            'totalProducts',
            'totalCategories',
            'totalUsers',
            'totalRevenue',
            'monthlyRevenue'
        ));
    }
}
