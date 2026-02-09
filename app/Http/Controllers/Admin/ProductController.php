<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\DeliveryCategory;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Generate a unique SKU for a product.
     */
    private function generateUniqueSKU($categoryId)
    {
        $prefix = 'PRD';
        $categoryIdPadded = str_pad($categoryId, 2, '0', STR_PAD_LEFT);
        $maxAttempts = 10;
        $attempts = 0;

        do {
            $unique = strtoupper(Str::random(5));
            $sku = "{$prefix}-{$categoryIdPadded}-{$unique}";
            $attempts++;

            // Check if SKU already exists
            $exists = Product::where('sku', $sku)->exists();
        } while ($exists && $attempts < $maxAttempts);

        if ($exists) {
            // Fallback: use timestamp if still collision after max attempts
            $unique = strtoupper(substr(md5(uniqid()), 0, 5));
            $sku = "{$prefix}-{$categoryIdPadded}-{$unique}";
        }

        return $sku;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('product.view'), 403);

        $query = Product::with(['category', 'unit', 'images']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($catQuery) use ($search) {
                      $catQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        // Filter by featured
        if ($request->filled('featured')) {
            $query->where('featured', $request->featured === 'featured');
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $products = $query->paginate(15);
        $categories = Category::where('status', true)->orderBy('name')->pluck('name', 'id');

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('product.create'), 403);

        $categories = Category::where('status', true)->orderBy('name')->pluck('name', 'id');
        $units = Unit::where('status', true)->orderBy('name')->pluck('name', 'id');
        $deliveryCategories = DeliveryCategory::query()->orderBy('name')->pluck('name', 'id');

        return view('admin.products.create', compact('categories', 'units', 'deliveryCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('product.create'), 403);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'delivery_category_id' => 'nullable|exists:delivery_categories,id',
            'name' => 'required|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'alert_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'featured' => 'boolean',
            'not_for_sale' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'primary_image' => 'nullable|integer',
        ]);

        try {
            DB::beginTransaction();

            // Generate SKU automatically
            $validated['sku'] = $this->generateUniqueSKU($validated['category_id']);

            $product = Product::create($validated);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $primaryImageId = $request->input('primary_image');
                
                foreach ($request->file('images') as $index => $image) {
                    $filename = uniqid('product_', true) . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('products', $filename, 'public');

                    $isPrimary = ($primaryImageId == $index) || ($index === 0 && !$primaryImageId);

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $filename,
                        'is_primary' => $isPrimary,
                        'sort_order' => $index
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->withInput()
                ->with('error', 'Error creating product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('product.view'), 403);

        $product->load(['category', 'unit', 'images' => function ($query) {
            $query->orderBy('sort_order');
        }]);

        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('product.edit'), 403);

        $product->load(['images' => function ($query) {
            $query->orderBy('sort_order');
        }]);

        $categories = Category::where('status', true)->orderBy('name')->pluck('name', 'id');
        $units = Unit::where('status', true)->orderBy('name')->pluck('name', 'id');
        $deliveryCategories = DeliveryCategory::query()->orderBy('name')->pluck('name', 'id');

        return view('admin.products.edit', compact('product', 'categories', 'units', 'deliveryCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('product.edit'), 403);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'delivery_category_id' => 'nullable|exists:delivery_categories,id',
            'name' => 'required|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'alert_stock' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'featured' => 'boolean',
            'not_for_sale' => 'boolean',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'primary_image' => 'nullable|integer',
            'images_to_delete' => 'nullable|array',
            'images_to_delete.*' => 'integer',
            // Backward compatibility
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer',
        ]);

        try {
            DB::beginTransaction();

            // Keep existing SKU, don't allow changes
            $validated['sku'] = $product->sku;

            $product->update($validated);

            // Delete selected images (delete record + storage file via ProductImage::delete override)
            $deleteIds = collect($request->input('images_to_delete', []))
                ->merge($request->input('delete_images', []))
                ->filter(fn ($v) => is_numeric($v))
                ->map(fn ($v) => (int) $v)
                ->unique()
                ->values()
                ->all();

            if (!empty($deleteIds)) {
                $imagesToDelete = ProductImage::query()
                    ->where('product_id', $product->id)
                    ->whereIn('id', $deleteIds)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    $image->delete();
                }
            }

            // Handle new image uploads
            if ($request->hasFile('new_images')) {
                $existingImages = $product->images()->count();
                $primaryImageId = $request->input('primary_image');
                
                foreach ($request->file('new_images') as $index => $image) {
                    $filename = uniqid('product_', true) . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('products', $filename, 'public');

                    $isPrimary = ($primaryImageId == ($existingImages + $index));

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $filename,
                        'is_primary' => $isPrimary,
                        'sort_order' => $existingImages + $index
                    ]);
                }
            }

            // Update primary image if specified
            if ($request->filled('primary_image') && !$request->hasFile('new_images')) {
                $primaryImageId = $request->input('primary_image');
                
                // Remove primary flag from all images
                $product->images()->update(['is_primary' => false]);
                
                // Set primary flag on selected image
                if ($primaryImageId) {
                    $product->images()->where('id', $primaryImageId)->update(['is_primary' => true]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->withInput()
                ->with('error', 'Error updating product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('product.delete'), 403);

        try {
            DB::beginTransaction();

            // Check if product has any orders (future-proof for order system)
            // For now, we'll allow deletion but you can uncomment this when orders are implemented
            /*
            if ($product->orders()->exists()) {
                return back()
                    ->with('error', 'Cannot delete product. It has associated orders.');
            }
            */

            // Delete all images
            foreach ($product->images as $image) {
                $image->delete();
            }

            $product->delete();

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Product deleted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->with('error', 'Error deleting product: ' . $e->getMessage());
        }
    }

    /**
     * Toggle product status.
     */
    public function toggleStatus(Product $product)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('product.edit'), 403);

        $product->update(['status' => !$product->status]);

        return back()
            ->with('success', 'Product status updated successfully!');
    }

    /**
     * Toggle product featured status.
     */
    public function toggleFeatured(Product $product)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('product.edit'), 403);

        $product->update(['featured' => !$product->featured]);

        return back()
            ->with('success', 'Product featured status updated successfully!');
    }
}
