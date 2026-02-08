<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeliveryCategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.view'), 403);

        $search = trim((string) $request->get('search', ''));

        $deliveryCategories = DeliveryCategory::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.delivery-categories.index', compact('deliveryCategories', 'search'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        return view('admin.delivery-categories.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:delivery_categories,name'],
            'extra_charge' => ['required', 'numeric', 'min:0'],
        ]);

        DeliveryCategory::create($validated);

        return redirect()->route('admin.delivery-categories.index')->with('success', 'Delivery category created successfully.');
    }

    public function edit(Request $request, DeliveryCategory $delivery_category)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $deliveryCategory = $delivery_category;
        return view('admin.delivery-categories.edit', compact('deliveryCategory'));
    }

    public function update(Request $request, DeliveryCategory $delivery_category)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('delivery_categories', 'name')->ignore($delivery_category->id)],
            'extra_charge' => ['required', 'numeric', 'min:0'],
        ]);

        $delivery_category->update($validated);

        return redirect()->route('admin.delivery-categories.index')->with('success', 'Delivery category updated successfully.');
    }

    public function destroy(Request $request, DeliveryCategory $delivery_category)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $delivery_category->delete();

        return redirect()->route('admin.delivery-categories.index')->with('success', 'Delivery category deleted successfully.');
    }
}
