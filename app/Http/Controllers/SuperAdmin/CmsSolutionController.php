<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CmsSolution;
use Illuminate\Http\Request;

class CmsSolutionController extends Controller
{
    public function index()
    {
        $solutions = CmsSolution::orderBy('sort_order')->get();
        return view('super_admin.cms.solutions.index', compact('solutions'));
    }

    public function create()
    {
        return view('super_admin.cms.solutions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tag' => 'required|string|max:100',
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'checklist' => 'nullable|string',
            'sort_order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $checklist = array_filter(array_map('trim', preg_split('/[\r\n]+/', $request->checklist ?? '')));
        $validated['checklist'] = array_values($checklist);
        $validated['is_active'] = $request->has('is_active');

        CmsSolution::create($validated);
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.solutions.index')->with('success', 'Industry solution created successfully.');
    }

    public function edit(CmsSolution $solution)
    {
        return view('super_admin.cms.solutions.edit', compact('solution'));
    }

    public function update(Request $request, CmsSolution $solution)
    {
        $validated = $request->validate([
            'tag' => 'required|string|max:100',
            'title' => 'required|string|max:150',
            'description' => 'required|string',
            'checklist' => 'nullable|string',
            'sort_order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $checklist = array_filter(array_map('trim', preg_split('/[\r\n]+/', $request->checklist ?? '')));
        $validated['checklist'] = array_values($checklist);
        $validated['is_active'] = $request->has('is_active');

        $solution->update($validated);
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.solutions.index')->with('success', 'Industry solution updated successfully.');
    }

    public function destroy(CmsSolution $solution)
    {
        $solution->delete();
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.solutions.index')->with('success', 'Industry solution deleted successfully.');
    }
}

