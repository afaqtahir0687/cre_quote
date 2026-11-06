<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\SystemLog;

class UnitController extends Controller
{
    /**
     * Ensure standard units exist for convenience.
     */
    protected function ensureDefaultUnitsExist()
    {
        if (Unit::count() === 0) {
            $defaults = [
                ['name' => 'Piece', 'code' => 'pcs'],
                ['name' => 'Meter', 'code' => 'm'],
                ['name' => 'Square Meter', 'code' => 'sqm'],
                ['name' => 'Roll', 'code' => 'roll'],
                ['name' => 'Box', 'code' => 'box'],
                ['name' => 'Kilogram', 'code' => 'kg'],
                ['name' => 'Sheet', 'code' => 'sheet'],
                ['name' => 'Packet', 'code' => 'pkt'],
            ];

            foreach ($defaults as $d) {
                Unit::create(array_merge($d, ['status' => true]));
            }
        }
    }

    public function index(Request $request)
    {
        $this->ensureDefaultUnitsExist();

        $query = Unit::withCount('products')->latest('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $units = $query->paginate(15)->withQueryString();
        return view('admin.units.index', compact('units'));
    }

    public function create()
    {
        return view('admin.units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:30',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? $request->boolean('status') : true;

        $unit = Unit::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unit' => $unit,
                'message' => __('Unit created successfully.')
            ]);
        }

        return redirect()->route('units.index')->with('success', __('Unit created successfully.'));
    }

    public function edit(Unit $unit)
    {
        return view('admin.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:30',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? $request->boolean('status') : true;

        $unit->update($validated);

        return redirect()->route('units.index')->with('success', __('Unit updated successfully.'));
    }

    public function destroy(Unit $unit)
    {
        $name = $unit->name;
        $unit->delete();

        SystemLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted',
            'model_type' => 'Unit',
            'description' => "Deleted Unit: {$name}"
        ]);

        return redirect()->route('units.index')->with('success', __('Unit deleted successfully.'));
    }
}

