<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Shop;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Display a listing of services with category filter and search.
     */
    public function index(Request $request)
    {
        $query = Service::orderBy('category')->orderBy('sort_order');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $services = $query->paginate(20)->withQueryString();
        
        $counts = [
            'all' => Service::count(),
            'Creativity' => Service::where('category', 'Creativity')->count(),
            'Marketing' => Service::where('category', 'Marketing')->count(),
            'Advertising' => Service::where('category', 'Advertising')->count(),
        ];

        return view('admin.services.index', compact('services', 'counts'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        $categories = ['Creativity', 'Marketing', 'Advertising'];
        return view('admin.services.create', compact('categories'));
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'name' => 'required|string|max:200',
            'sub_tag' => 'nullable|string|max:150',
            'headline' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:100',
            'slug' => 'nullable|string|max:220|unique:services,slug',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'features' => 'nullable|string',
            'feature_1_title' => 'nullable|string|max:150',
            'feature_1_text' => 'nullable|string|max:255',
            'feature_1_icon' => 'nullable|string|max:100',
            'feature_2_title' => 'nullable|string|max:150',
            'feature_2_text' => 'nullable|string|max:255',
            'feature_2_icon' => 'nullable|string|max:100',
            'cta_title' => 'nullable|string|max:255',
            'cta_subtitle' => 'nullable|string',
            'starting_price' => 'nullable|numeric|min:0',
            'price_unit' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            // Ensure unique slug
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Service::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count++;
            }
        }

        // Parse features from newline-delimited text
        if (!empty($request->features)) {
            $featuresList = array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/', $request->features))));
            $validated['features'] = $featuresList;
        } else {
            $validated['features'] = [];
        }

        $shop = Shop::first();
        $validated['shop_id'] = $shop ? $shop->id : 1;
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;
        $validated['sort_order'] = $request->input('sort_order', 0);
        $validated['icon'] = $validated['icon'] ?: 'fas fa-concierge-bell';
        $validated['price_unit'] = $validated['price_unit'] ?: 'Project';

        Service::create($validated);

        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        $categories = ['Creativity', 'Marketing', 'Advertising'];
        return view('admin.services.edit', compact('service', 'categories'));
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'name' => 'required|string|max:200',
            'sub_tag' => 'nullable|string|max:150',
            'headline' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:100',
            'slug' => 'nullable|string|max:220|unique:services,slug,' . $service->id,
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'features' => 'nullable|string',
            'feature_1_title' => 'nullable|string|max:150',
            'feature_1_text' => 'nullable|string|max:255',
            'feature_1_icon' => 'nullable|string|max:100',
            'feature_2_title' => 'nullable|string|max:150',
            'feature_2_text' => 'nullable|string|max:255',
            'feature_2_icon' => 'nullable|string|max:100',
            'cta_title' => 'nullable|string|max:255',
            'cta_subtitle' => 'nullable|string',
            'starting_price' => 'nullable|numeric|min:0',
            'price_unit' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if (!empty($request->features)) {
            $featuresList = array_values(array_filter(array_map('trim', preg_split('/[\r\n]+/', $request->features))));
            $validated['features'] = $featuresList;
        } else {
            $validated['features'] = [];
        }

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : false;
        $validated['sort_order'] = $request->input('sort_order', 0);
        $validated['icon'] = $validated['icon'] ?: 'fas fa-concierge-bell';
        $validated['price_unit'] = $validated['price_unit'] ?: 'Project';

        $service->update($validated);

        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }
}

