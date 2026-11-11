<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CmsTestimonial;
use Illuminate\Http\Request;

class CmsTestimonialController extends Controller
{
    public function index()
    {
        $testimonials = CmsTestimonial::orderBy('sort_order')->get();
        return view('super_admin.cms.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('super_admin.cms.testimonials.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'designation' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:100',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'sort_order' => 'required|integer',
            'is_approved' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('avatar')) {
            $avatarName = 'testimonial_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            $request->file('avatar')->move(public_path('uploads/cms'), $avatarName);
            $validated['avatar'] = 'uploads/cms/' . $avatarName;
        }

        $validated['is_approved'] = $request->has('is_approved');
        $validated['is_active'] = $request->has('is_active');

        CmsTestimonial::create($validated);
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.testimonials.index')->with('success', 'Testimonial created successfully.');
    }

    public function edit(CmsTestimonial $testimonial)
    {
        return view('super_admin.cms.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, CmsTestimonial $testimonial)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'designation' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:100',
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'sort_order' => 'required|integer',
            'is_approved' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('avatar')) {
            $avatarName = 'testimonial_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
            $request->file('avatar')->move(public_path('uploads/cms'), $avatarName);
            $validated['avatar'] = 'uploads/cms/' . $avatarName;
        }

        $validated['is_approved'] = $request->has('is_approved');
        $validated['is_active'] = $request->has('is_active');

        $testimonial->update($validated);
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.testimonials.index')->with('success', 'Testimonial updated successfully.');
    }

    public function toggleApproval(CmsTestimonial $testimonial)
    {
        $testimonial->is_approved = !$testimonial->is_approved;
        $testimonial->save();
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->back()->with('success', 'Approval status updated.');
    }

    public function destroy(CmsTestimonial $testimonial)
    {
        $testimonial->delete();
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('super_admin.cms.testimonials.index')->with('success', 'Testimonial deleted successfully.');
    }
}

