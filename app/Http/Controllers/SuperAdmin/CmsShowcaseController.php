<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CmsShowcaseTab;
use Illuminate\Http\Request;

class CmsShowcaseController extends Controller
{
    public function index()
    {
        $tabs = CmsShowcaseTab::orderBy('sort_order')->get();
        return view('super_admin.cms.showcase.index', compact('tabs'));
    }

    public function edit(CmsShowcaseTab $tab)
    {
        return view('super_admin.cms.showcase.edit', compact('tab'));
    }

    public function update(Request $request, CmsShowcaseTab $tab)
    {
        $validated = $request->validate([
            'tab_label' => 'required|string|max:100',
            'tab_icon' => 'required|string|max:100',
            'badge_text' => 'nullable|string|max:100',
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'highlights' => 'nullable|string',
            'cta_text' => 'nullable|string|max:100',
            'cta_url' => 'nullable|string|max:255',
            'sort_order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('screenshot')) {
            $request->validate(['screenshot' => 'image|mimes:jpeg,png,jpg,webp|max:5120']);
            $imgName = 'showcase_' . $tab->tab_key . '_' . time() . '.' . $request->file('screenshot')->getClientOriginalExtension();
            $request->file('screenshot')->move(public_path('uploads/cms'), $imgName);
            $validated['screenshot_path'] = 'uploads/cms/' . $imgName;
        }

        $highlights = array_filter(array_map('trim', preg_split('/[\r\n]+/', $request->highlights ?? '')));
        $validated['highlights'] = array_values($highlights);
        $validated['is_active'] = $request->has('is_active');

        $tab->update($validated);
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.showcase.index')->with('success', 'Workflow tab updated successfully.');
    }
}

