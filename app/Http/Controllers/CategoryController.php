<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SystemLog;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::withCount('products')->latest('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->paginate(15)->withQueryString();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? $request->boolean('status') : true;

        if (empty($validated['code']) && !empty($validated['name'])) {
            $words = explode(' ', trim($validated['name']));
            $abbr = '';
            foreach ($words as $w) {
                if (!empty($w)) $abbr .= strtoupper(substr($w, 0, 1));
            }
            if (strlen($abbr) < 2) {
                $clean = preg_replace('/[^A-Za-z0-9]/', '', $validated['name']);
                $abbr = strtoupper(substr($clean, 0, 4));
            }
            $validated['code'] = 'CAT-' . ($abbr ?: 'GEN');
        }

        $category = Category::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => __('Category created successfully.')
            ]);
        }

        return redirect()->route('categories.index')->with('success', __('Category created successfully.'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $request->has('status') ? $request->boolean('status') : true;

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', __('Category updated successfully.'));
    }

    public function destroy(Category $category)
    {
        $name = $category->name;
        $category->delete();

        SystemLog::create([
            'user_id' => auth()->id(),
            'action' => 'Deleted',
            'model_type' => 'Category',
            'description' => "Deleted Category: {$name}"
        ]);

        return redirect()->route('categories.index')->with('success', __('Category deleted successfully.'));
    }
}

