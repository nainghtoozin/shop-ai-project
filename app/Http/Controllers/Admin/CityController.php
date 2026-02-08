<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.view'), 403);

        $search = trim((string) $request->get('search', ''));
        $status = (string) $request->get('status', '');

        $cities = City::query()
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

        return view('admin.cities.index', compact('cities', 'search', 'status'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        return view('admin.cities.create');
    }

    public function store(Request $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:cities,name'],
            'base_charge' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable'],
        ]);

        City::create([
            'name' => $validated['name'],
            'base_charge' => $validated['base_charge'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.cities.index')->with('success', 'City created successfully.');
    }

    public function edit(Request $request, City $city)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        return view('admin.cities.edit', compact('city'));
    }

    public function update(Request $request, City $city)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('cities', 'name')->ignore($city->id)],
            'base_charge' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable'],
        ]);

        $city->update([
            'name' => $validated['name'],
            'base_charge' => $validated['base_charge'],
            'is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.cities.index')->with('success', 'City updated successfully.');
    }

    public function destroy(Request $request, City $city)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $city->delete();

        return redirect()->route('admin.cities.index')->with('success', 'City deleted successfully.');
    }

    public function toggleStatus(Request $request, City $city)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('setting.edit'), 403);

        $city->is_active = !$city->is_active;
        $city->save();

        return response()->json([
            'success' => true,
            'is_active' => (bool) $city->is_active,
        ]);
    }
}
