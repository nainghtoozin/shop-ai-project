<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\HeroSlider;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $heroSliders = HeroSlider::query()
            ->active()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get(['id', 'title', 'subtitle', 'image', 'link', 'badge_text', 'sort_order']);

        $categories = Category::query()
            ->where('status', true)
            ->orderBy('name')
            ->take(6)
            ->get(['id', 'name', 'slug', 'image']);

        $products = Product::query()
            ->with([
                'unit:id,name,short_name',
                'primaryImage:id,product_id,image,is_primary,sort_order',
            ])
            ->where('status', true)
            ->where('not_for_sale', false)
            ->orderBy('featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get(['id', 'category_id', 'unit_id', 'name', 'slug', 'selling_price', 'stock', 'alert_stock', 'description', 'featured', 'created_at']);

        return view('welcome', compact('heroSliders', 'categories', 'products'));
    }
}
