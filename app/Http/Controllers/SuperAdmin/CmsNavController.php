<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CmsNavItem;
use Illuminate\Http\Request;

class CmsNavController extends Controller
{
    public function index()
    {
        $headerItems = CmsNavItem::where('location', 'header')->orderBy('sort_order')->get();
        $footerNavItems = CmsNavItem::where('location', 'footer_nav')->orderBy('sort_order')->get();
        $footerModules = CmsNavItem::where('location', 'footer_modules')->orderBy('sort_order')->get();

        return view('super_admin.cms.nav.index', compact('headerItems', 'footerNavItems', 'footerModules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'location' => 'required|in:header,footer_nav,footer_modules,footer_legal',
            'target' => 'required|in:_self,_blank',
            'sort_order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        CmsNavItem::create($validated);
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->back()->with('success', 'Navigation item added.');
    }

    public function update(Request $request, CmsNavItem $item)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'location' => 'required|in:header,footer_nav,footer_modules,footer_legal',
            'target' => 'required|in:_self,_blank',
            'sort_order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $item->update($validated);
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->back()->with('success', 'Navigation item updated.');
    }

    public function destroy(CmsNavItem $item)
    {
        $item->delete();
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->back()->with('success', 'Navigation item deleted.');
    }
}

