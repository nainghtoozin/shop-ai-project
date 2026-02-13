<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::query()
            ->with([
                'category:id,name,slug',
                'unit:id,name,short_name',
                'primaryImage:id,product_id,image,is_primary,sort_order',
            ])
            ->withCount('approvedReviews')
            ->withAvg('approvedReviews', 'rating')
            ->where('status', true)
            ->where('not_for_sale', false)
            ->orderBy('featured', 'desc')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('products.index', compact('products'));
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return redirect()->route('products.index');
        }

        $products = Product::query()
            ->with([
                'category:id,name,slug',
                'unit:id,name,short_name',
                'primaryImage:id,product_id,image,is_primary,sort_order',
            ])
            ->withCount('approvedReviews')
            ->withAvg('approvedReviews', 'rating')
            ->where('status', true)
            ->where('not_for_sale', false)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%");
            })
            ->orderBy('featured', 'desc')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'searchQuery' => $q,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a review for the product.
     */
    public function storeReview(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check if user has purchased this product
        $hasPurchased = OrderItem::whereHas('order', function ($query) {
            $query->where('user_id', Auth::id());
        })->where('product_id', $product->id)->exists();

        if (!$hasPurchased) {
            return back()->with('error', 'You can only review products you have purchased.');
        }

        // Check if user already reviewed
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            // Update existing review
            $existingReview->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
                'status' => 'pending', // Reset to pending if edited
            ]);
            $message = 'Your review has been updated and is pending approval.';
        } else {
            // Create new review
            Review::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'rating' => $request->rating,
                'comment' => $request->comment,
                'status' => 'pending',
            ]);
            $message = 'Your review has been submitted and is pending approval.';
        }

        return back()->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        if (!$product->status || $product->not_for_sale) {
            abort(404);
        }

        $product->load([
            'category:id,name,slug',
            'unit:id,name,short_name',
            'images' => function ($query) {
                $query->orderBy('sort_order');
            },
            'primaryImage:id,product_id,image,is_primary,sort_order',
            'approvedReviews.user:id,name',
        ]);

        $userCanReview = false;
        $userReview = null;

        if (Auth::check()) {
            // Check if user has purchased this product
            $hasPurchased = OrderItem::whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })->where('product_id', $product->id)->exists();

            if ($hasPurchased) {
                $userCanReview = true;
                $userReview = Review::where('user_id', Auth::id())
                    ->where('product_id', $product->id)
                    ->first();
            }
        }

        return view('products.show', compact('product', 'userCanReview', 'userReview'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
