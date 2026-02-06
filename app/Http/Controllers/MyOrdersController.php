<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyOrdersController extends Controller
{
    public function index()
    {
        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('orders.index', compact('orders'));
    }
}
