<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CmsAppModule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsAppModuleController extends Controller
{
    public function index()
    {
        $modules = CmsAppModule::orderBy('sort_order')->get();
        return view('super_admin.cms.modules.index', compact('modules'));
    }

    public function create()
    {
        return view('super_admin.cms.modules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100',
            'destination_url' => 'nullable|string|max:255',
            'icon_svg' => 'nullable|string',
            'sort_order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        CmsAppModule::create($validated);
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.modules.index')->with('success', 'Application module created successfully.');
    }

    public function edit(CmsAppModule $module)
    {
        return view('super_admin.cms.modules.edit', compact('module'));
    }

    public function update(Request $request, CmsAppModule $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100',
            'destination_url' => 'nullable|string|max:255',
            'icon_svg' => 'nullable|string',
            'sort_order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $module->update($validated);
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.modules.index')->with('success', 'Application module updated successfully.');
    }

    public function destroy(CmsAppModule $module)
    {
        $module->delete();
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.modules.index')->with('success', 'Application module deleted successfully.');
    }
}

