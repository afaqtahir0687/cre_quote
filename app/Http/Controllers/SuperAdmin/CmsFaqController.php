<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CmsFaq;
use Illuminate\Http\Request;

class CmsFaqController extends Controller
{
    public function index()
    {
        $faqs = CmsFaq::orderBy('sort_order')->get();
        return view('super_admin.cms.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('super_admin.cms.faqs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
            'sort_order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        CmsFaq::create($validated);
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.faqs.index')->with('success', 'FAQ created successfully.');
    }

    public function edit(CmsFaq $faq)
    {
        return view('super_admin.cms.faqs.edit', compact('faq'));
    }

    public function update(Request $request, CmsFaq $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
            'sort_order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $faq->update($validated);
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.faqs.index')->with('success', 'FAQ updated successfully.');
    }

    public function destroy(CmsFaq $faq)
    {
        $faq->delete();
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.faqs.index')->with('success', 'FAQ deleted successfully.');
    }
}

