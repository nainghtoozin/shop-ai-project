<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('unit.view'), 403);

        $units = Unit::latest()->paginate(10);
        return view('admin.units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('unit.create'), 403);

        return view('admin.units.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUnitRequest $request)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('unit.create'), 403);

        Unit::create($request->validated());
        
        return redirect()
            ->route('admin.units.index')
            ->with('success', 'Unit created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('unit.view'), 403);

        $unit->loadCount('products');

        return view('admin.units.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('unit.edit'), 403);

        return view('admin.units.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUnitRequest $request, Unit $unit)
    {
        $user = $request->user();
        abort_if(!$user || !$user->can('unit.edit'), 403);

        $unit->update($request->validated());
        
        return redirect()
            ->route('admin.units.index')
            ->with('success', 'Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('unit.delete'), 403);

        if ($unit->products()->exists()) {
            return redirect()
                ->route('admin.units.index')
                ->with('error', 'Cannot delete unit. It has associated products.');
        }

        $unit->delete();
        
        return redirect()
            ->route('admin.units.index')
            ->with('success', 'Unit deleted successfully.');
    }

    /**
     * Toggle unit status
     */
    public function toggleStatus(Unit $unit)
    {
        $user = request()->user();
        abort_if(!$user || !$user->can('unit.edit'), 403);

        $unit->status = !$unit->status;
        $unit->save();
        
        return response()->json([
            'success' => true,
            'status' => $unit->status,
            'message' => 'Unit status updated successfully.'
        ]);
    }
}
