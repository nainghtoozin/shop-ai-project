<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeliveryTypeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.view'), 403);

        $search = trim((string) $request->get('search', ''));
        $status = (string) $request->get('status', '');

        $deliveryTypes = DeliveryType::query()
            ->when($search !== '', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->when($status !== '', function ($q) use ($status) {
                if ($status === 'active') $q->where('is_active', true);
                if ($status === 'inactive') $q->where('is_active', false);
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.delivery-types.index', compact('deliveryTypes', 'search', 'status'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        return view('admin.delivery-types.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:delivery_types,name'],
            'charge_type' => ['required', Rule::in(['fixed', 'percent'])],
            'extra_charge' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable'],
        ]);

        DeliveryType::create([
            'name' => $validated['name'],
            'charge_type' => $validated['charge_type'],
            'extra_charge' => $validated['extra_charge'],
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.delivery-types.index')->with('success', 'Delivery type created successfully.');
    }

    public function edit(Request $request, DeliveryType $delivery_type)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $deliveryType = $delivery_type;
        return view('admin.delivery-types.edit', compact('deliveryType'));
    }

    public function update(Request $request, DeliveryType $delivery_type)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('delivery_types', 'name')->ignore($delivery_type->id)],
            'charge_type' => ['required', Rule::in(['fixed', 'percent'])],
            'extra_charge' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable'],
        ]);

        $delivery_type->update([
            'name' => $validated['name'],
            'charge_type' => $validated['charge_type'],
            'extra_charge' => $validated['extra_charge'],
            'description' => $validated['description'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.delivery-types.index')->with('success', 'Delivery type updated successfully.');
    }

    public function destroy(Request $request, DeliveryType $delivery_type)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $delivery_type->delete();

        return redirect()->route('admin.delivery-types.index')->with('success', 'Delivery type deleted successfully.');
    }

    public function toggleStatus(Request $request, DeliveryType $delivery_type)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $delivery_type->is_active = !$delivery_type->is_active;
        $delivery_type->save();

        return response()->json([
            'success' => true,
            'is_active' => (bool) $delivery_type->is_active,
        ]);
    }
}
