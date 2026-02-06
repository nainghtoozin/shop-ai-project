<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('category.view'), 403);

        $query = Category::with('parent');
        
        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active' ? 1 : 0);
        }
        
        // Filter by parent
        if ($request->filled('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }
        
        $categories = $query->orderBy('name')->paginate(15);
        
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('category.create'), 403);

        $parentOptions = Category::getParentOptions();
        
        return view('admin.categories.create', compact('parentOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('category.create'), 403);

        $validated = $request->validated();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('categories', 'public');
            $validated['image'] = basename($imagePath);
        }
        
        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        Category::create($validated);
        
        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('category.view'), 403);

        $category
            ->load([
                'parent',
                'children' => function ($q) {
                    $q->withCount('products');
                },
            ])
            ->loadCount(['products', 'children']);
        
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('category.edit'), 403);

        $parentOptions = Category::getParentOptions($category->id);
        $imagePreview = $category->image ? $category->image_url : null;
        
        return view('admin.categories.edit', compact('category', 'parentOptions', 'imagePreview'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('category.edit'), 403);

        $validated = $request->validated();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete('categories/' . $category->image);
            }
            
            $image = $request->file('image');
            $imagePath = $image->store('categories', 'public');
            $validated['image'] = basename($imagePath);
        }
        
        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        $category->update($validated);
        
        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('category.delete'), 403);

        if ($category->children()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Cannot delete category. It has sub-categories.');
        }
        
        if ($category->products()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with('error', 'Cannot delete category. It has associated products.');
        }
        
        // Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete('categories/' . $category->image);
        }
        
        $category->delete();
        
        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Toggle category status.
     */
    public function toggleStatus(Category $category)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('category.edit'), 403);

        $category->status = !$category->status;
        $category->save();
        
        return response()->json([
            'success' => true,
            'status' => $category->status,
            'message' => 'Category status updated successfully.'
        ]);
    }

    /**
     * AJAX method to get subcategories for a parent.
     */
    public function getSubcategories(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('category.view'), 403);

        $parent = $request->get('parent_id');
        
        $subcategories = Category::where('parent_id', $parent)
            ->where('status', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        
        return response()->json($subcategories);
    }
}
