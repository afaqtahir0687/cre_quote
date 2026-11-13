<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CmsPageController extends Controller
{
    /**
     * Display a listing of CMS pages.
     */
    public function index(Request $request)
    {
        $query = CmsPage::query();

        if ($request->filled('column')) {
            $query->where('footer_column', $request->column);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $pages = $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();

        $stats = [
            'total' => CmsPage::count(),
            'active' => CmsPage::where('is_active', true)->count(),
            'company' => CmsPage::where('footer_column', 'company')->count(),
            'legal' => CmsPage::where('footer_column', 'legal')->count(),
        ];

        return view('super_admin.cms.pages.index', compact('pages', 'stats'));
    }

    /**
     * Show the form for creating a new CMS page.
     */
    public function create()
    {
        $nextOrder = (CmsPage::max('sort_order') ?? 0) + 1;
        return view('super_admin.cms.pages.create', compact('nextOrder'));
    }

    /**
     * Store a newly created CMS page in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:cms_pages,slug',
            'subtitle' => 'nullable|string|max:255',
            'footer_column' => 'required|in:company,legal,quick_links,none',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'sort_order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_system'] = false;

        $page = CmsPage::create($validated);

        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.pages.index')
            ->with('success', "Page '{$page->title}' created successfully.");
    }

    /**
     * Show the form for editing the specified CMS page.
     */
    public function edit(CmsPage $page)
    {
        return view('super_admin.cms.pages.edit', compact('page'));
    }

    /**
     * Update the specified CMS page in storage.
     */
    public function update(Request $request, CmsPage $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'required|string|max:200|unique:cms_pages,slug,' . $page->id,
            'subtitle' => 'nullable|string|max:255',
            'footer_column' => 'required|in:company,legal,quick_links,none',
            'content' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'sort_order' => 'required|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['is_active'] = $request->has('is_active');

        $page->update($validated);

        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.pages.index')
            ->with('success', "Page '{$page->title}' updated successfully.");
    }

    /**
     * Remove the specified CMS page from storage.
     */
    public function destroy(CmsPage $page)
    {
        $title = $page->title;
        $page->delete();

        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.pages.index')
            ->with('success', "Page '{$title}' was deleted successfully.");
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(CmsPage $page)
    {
        $page->update(['is_active' => !$page->is_active]);

        \Illuminate\Support\Facades\Artisan::call('view:clear');

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_active' => $page->is_active,
                'message' => 'Page status updated.'
            ]);
        }

        return redirect()->back()->with('success', "Page '{$page->title}' status updated.");
    }
}

