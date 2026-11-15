<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BusinessType;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('super_admin');
    }

    /**
     * Display a listing of business types / industries.
     */
    public function index()
    {
        $businessTypes = BusinessType::withCount('shops')->ordered()->get();
        $allModules = Shop::availableModules();

        return view('super_admin.business_types.index', compact('businessTypes', 'allModules'));
    }

    /**
     * Show form to create a new business type / industry.
     */
    public function create()
    {
        $allModules = Shop::availableModules();
        $suggestedIcons = [
            'fas fa-briefcase' => 'Briefcase',
            'fas fa-print' => 'Printing & Press',
            'fas fa-kaaba' => 'Kaaba / Umrah',
            'fas fa-bus' => 'Bus / Transport',
            'fas fa-shopping-cart' => 'Retail / Cart',
            'fas fa-store' => 'Store / Shop',
            'fas fa-laptop-code' => 'IT & Software',
            'fas fa-tools' => 'Workshop & Repairs',
            'fas fa-truck' => 'Logistics & Cargo',
            'fas fa-utensils' => 'Restaurant & Food',
            'fas fa-hospital' => 'Medical & Clinic',
            'fas fa-cubes' => 'Enterprise / Multi',
            'fas fa-graduation-cap' => 'Education & Academy',
            'fas fa-hotel' => 'Hotel & Hospitality',
        ];

        return view('super_admin.business_types.create', compact('allModules', 'suggestedIcons'));
    }

    /**
     * Store a newly created business type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:100|unique:business_types,slug',
            'icon' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'description_ar' => 'nullable|string|max:1000',
            'default_modules' => 'nullable|array',
            'default_modules.*' => 'string|in:' . implode(',', array_keys(Shop::availableModules())),
            'default_departments_text' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'], '_')
            : Str::slug($validated['name'], '_');

        // Ensure unique slug
        $originalSlug = $slug;
        $counter = 1;
        while (BusinessType::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}_{$counter}";
            $counter++;
        }

        // Parse departments from lines or commas
        $departments = [];
        if (!empty($validated['default_departments_text'])) {
            $lines = preg_split('/[\r\n,]+/', $validated['default_departments_text']);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (!empty($trimmed)) {
                    $departments[] = $trimmed;
                }
            }
        }

        $businessType = BusinessType::create([
            'slug' => $slug,
            'name' => $validated['name'],
            'name_ar' => $validated['name_ar'] ?? null,
            'icon' => $validated['icon'],
            'description' => $validated['description'] ?? null,
            'description_ar' => $validated['description_ar'] ?? null,
            'default_modules' => $validated['default_modules'] ?? [],
            'default_departments' => $departments,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'is_system' => false,
        ]);

        return redirect()->route('super_admin.business_types.index')
            ->with('success', __('Business Category ":name" created successfully!', ['name' => $businessType->name]));
    }

    /**
     * Show form to edit a business type.
     */
    public function edit($id)
    {
        $businessType = BusinessType::findOrFail($id);
        $allModules = Shop::availableModules();
        $suggestedIcons = [
            'fas fa-briefcase' => 'Briefcase',
            'fas fa-print' => 'Printing & Press',
            'fas fa-kaaba' => 'Kaaba / Umrah',
            'fas fa-bus' => 'Bus / Transport',
            'fas fa-shopping-cart' => 'Retail / Cart',
            'fas fa-store' => 'Store / Shop',
            'fas fa-laptop-code' => 'IT & Software',
            'fas fa-tools' => 'Workshop & Repairs',
            'fas fa-truck' => 'Logistics & Cargo',
            'fas fa-utensils' => 'Restaurant & Food',
            'fas fa-hospital' => 'Medical & Clinic',
            'fas fa-cubes' => 'Enterprise / Multi',
            'fas fa-graduation-cap' => 'Education & Academy',
            'fas fa-hotel' => 'Hotel & Hospitality',
        ];

        return view('super_admin.business_types.edit', compact('businessType', 'allModules', 'suggestedIcons'));
    }

    /**
     * Update an existing business type.
     */
    public function update(Request $request, $id)
    {
        $businessType = BusinessType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:100|unique:business_types,slug,' . $businessType->id,
            'icon' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'description_ar' => 'nullable|string|max:1000',
            'default_modules' => 'nullable|array',
            'default_modules.*' => 'string|in:' . implode(',', array_keys(Shop::availableModules())),
            'default_departments_text' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'], '_')
            : $businessType->slug;

        // Parse departments
        $departments = [];
        if (!empty($validated['default_departments_text'])) {
            $lines = preg_split('/[\r\n,]+/', $validated['default_departments_text']);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (!empty($trimmed)) {
                    $departments[] = $trimmed;
                }
            }
        }

        $oldSlug = $businessType->slug;

        $businessType->update([
            'slug' => $slug,
            'name' => $validated['name'],
            'name_ar' => $validated['name_ar'] ?? null,
            'icon' => $validated['icon'],
            'description' => $validated['description'] ?? null,
            'description_ar' => $validated['description_ar'] ?? null,
            'default_modules' => $validated['default_modules'] ?? [],
            'default_departments' => $departments,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // If slug changed, update associated shops
        if ($oldSlug !== $slug) {
            Shop::where('business_type', $oldSlug)->update(['business_type' => $slug]);
        }

        return redirect()->route('super_admin.business_types.index')
            ->with('success', __('Business Category ":name" updated successfully!', ['name' => $businessType->name]));
    }

    /**
     * Delete a business type.
     */
    public function destroy($id)
    {
        $businessType = BusinessType::findOrFail($id);

        $shopsCount = Shop::where('business_type', $businessType->slug)->count();
        if ($shopsCount > 0) {
            return redirect()->back()
                ->with('error', __('Cannot delete ":name" because it is currently assigned to :count active shop(s). Please reassign those shops first.', [
                    'name' => $businessType->name,
                    'count' => $shopsCount,
                ]));
        }

        $name = $businessType->name;
        $businessType->delete();

        return redirect()->route('super_admin.business_types.index')
            ->with('success', __('Business Category ":name" deleted successfully!', ['name' => $name]));
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus($id)
    {
        $businessType = BusinessType::findOrFail($id);
        $businessType->is_active = !$businessType->is_active;
        $businessType->save();

        return redirect()->back()->with('success', __('Status updated for ":name"!', ['name' => $businessType->name]));
    }
}
