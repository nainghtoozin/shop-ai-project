<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;


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
            ->where('status', true)
            ->where('not_for_sale', false)
            ->orderBy('featured', 'desc')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        ]);

        return view('products.show', compact('product'));
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
