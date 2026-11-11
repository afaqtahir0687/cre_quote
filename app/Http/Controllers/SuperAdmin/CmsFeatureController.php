<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CmsFeature;
use Illuminate\Http\Request;

class CmsFeatureController extends Controller
{
    public function index()
    {
        $features = CmsFeature::orderBy('sort_order')->get();
        return view('super_admin.cms.features.index', compact('features'));
    }

    public function create()
    {
        return view('super_admin.cms.features.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'icon_class' => 'required|string|max:100',
            'icon_color_class' => 'required|string|max:100',
            'highlights' => 'nullable|string', // comma or newline separated
            'sort_order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $highlights = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $request->highlights ?? '')));
        $validated['highlights'] = array_values($highlights);
        $validated['is_active'] = $request->has('is_active');

        CmsFeature::create($validated);
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.features.index')->with('success', 'Feature created successfully.');
    }

    public function edit(CmsFeature $feature)
    {
        return view('super_admin.cms.features.edit', compact('feature'));
    }

    public function update(Request $request, CmsFeature $feature)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'icon_class' => 'required|string|max:100',
            'icon_color_class' => 'required|string|max:100',
            'highlights' => 'nullable|string',
            'sort_order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $highlights = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $request->highlights ?? '')));
        $validated['highlights'] = array_values($highlights);
        $validated['is_active'] = $request->has('is_active');

        $feature->update($validated);
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.features.index')->with('success', 'Feature updated successfully.');
    }

    public function destroy(CmsFeature $feature)
    {
        $feature->delete();
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.features.index')->with('success', 'Feature deleted successfully.');
    }
}

