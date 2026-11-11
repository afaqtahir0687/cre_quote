<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HomepageSlideController extends Controller
{
    /**
     * Display a listing of homepage hero slides.
     */
    public function index()
    {
        $slides = HomepageSlide::orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        return view('super_admin.slides.index', compact('slides'));
    }

    /**
     * Show the form for creating a new slide.
     */
    public function create()
    {
        $slide = new HomepageSlide([
            'text_align' => 'center',
            'content_position' => 'center',
            'slide_type' => 'marketing',
            'animation_type' => 'fade',
            'display_duration' => 6000,
            'is_active' => true,
            'sort_order' => (HomepageSlide::max('sort_order') ?? 0) + 1,
            'primary_btn_action' => 'open_proposal_modal',
        ]);
        return view('super_admin.slides.create', compact('slide'));
    }

    /**
     * Store a newly created slide in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'badge_text' => 'nullable|string|max:150',
            'heading' => 'required|string|max:255',
            'heading_highlight' => 'nullable|string|max:255',
            'subheading' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'primary_btn_text' => 'nullable|string|max:100',
            'primary_btn_action' => 'required|string|in:url,open_proposal_modal,register',
            'primary_btn_url' => 'nullable|string|max:255',
            'secondary_btn_text' => 'nullable|string|max:100',
            'secondary_btn_url' => 'nullable|string|max:255',
            'bg_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'bg_image_mobile' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'fg_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'bg_color' => 'nullable|string|max:100',
            'text_align' => 'required|in:left,center,right',
            'content_position' => 'required|in:left,center,right',
            'slide_type' => 'required|in:existing_hero,marketing,custom',
            'animation_type' => 'required|in:fade,slide,zoom',
            'display_duration' => 'required|integer|min:2000|max:30000',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'trust_bullets' => 'nullable|string',
        ]);

        $uploadDir = public_path('uploads/slides');
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        // Upload desktop background
        if ($request->hasFile('bg_image')) {
            $file = $request->file('bg_image');
            $filename = 'slide_bg_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $validated['bg_image'] = 'uploads/slides/' . $filename;
        }

        // Upload mobile background
        if ($request->hasFile('bg_image_mobile')) {
            $file = $request->file('bg_image_mobile');
            $filename = 'slide_bg_mob_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $validated['bg_image_mobile'] = 'uploads/slides/' . $filename;
        }

        // Upload foreground image
        if ($request->hasFile('fg_image')) {
            $file = $request->file('fg_image');
            $filename = 'slide_fg_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $validated['fg_image'] = 'uploads/slides/' . $filename;
        }

        // Parse trust bullets
        if ($request->filled('trust_bullets')) {
            $bullets = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $request->trust_bullets))));
            $validated['trust_bullets'] = array_values($bullets);
        } else {
            $validated['trust_bullets'] = null;
        }

        $validated['is_active'] = $request->boolean('is_active');

        HomepageSlide::create($validated);

        return redirect()->route('super_admin.cms.slides.index')->with('success', __('Hero Slide created successfully.'));
    }

    /**
     * Show the form for editing the specified slide.
     */
    public function edit(HomepageSlide $slide)
    {
        return view('super_admin.slides.edit', compact('slide'));
    }

    /**
     * Update the specified slide in storage.
     */
    public function update(Request $request, HomepageSlide $slide)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'badge_text' => 'nullable|string|max:150',
            'heading' => 'required|string|max:255',
            'heading_highlight' => 'nullable|string|max:255',
            'subheading' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'primary_btn_text' => 'nullable|string|max:100',
            'primary_btn_action' => 'required|string|in:url,open_proposal_modal,register',
            'primary_btn_url' => 'nullable|string|max:255',
            'secondary_btn_text' => 'nullable|string|max:100',
            'secondary_btn_url' => 'nullable|string|max:255',
            'bg_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'bg_image_mobile' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'fg_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'bg_color' => 'nullable|string|max:100',
            'text_align' => 'required|in:left,center,right',
            'content_position' => 'required|in:left,center,right',
            'slide_type' => 'required|in:existing_hero,marketing,custom',
            'animation_type' => 'required|in:fade,slide,zoom',
            'display_duration' => 'required|integer|min:2000|max:30000',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'trust_bullets' => 'nullable|string',
        ]);

        $uploadDir = public_path('uploads/slides');
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        // Upload desktop background
        if ($request->hasFile('bg_image')) {
            if ($slide->bg_image && File::exists(public_path($slide->bg_image))) {
                @File::delete(public_path($slide->bg_image));
            }
            $file = $request->file('bg_image');
            $filename = 'slide_bg_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $validated['bg_image'] = 'uploads/slides/' . $filename;
        }

        // Upload mobile background
        if ($request->hasFile('bg_image_mobile')) {
            if ($slide->bg_image_mobile && File::exists(public_path($slide->bg_image_mobile))) {
                @File::delete(public_path($slide->bg_image_mobile));
            }
            $file = $request->file('bg_image_mobile');
            $filename = 'slide_bg_mob_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $validated['bg_image_mobile'] = 'uploads/slides/' . $filename;
        }

        // Upload foreground image
        if ($request->hasFile('fg_image')) {
            if ($slide->fg_image && File::exists(public_path($slide->fg_image))) {
                @File::delete(public_path($slide->fg_image));
            }
            $file = $request->file('fg_image');
            $filename = 'slide_fg_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            $validated['fg_image'] = 'uploads/slides/' . $filename;
        }

        // Parse trust bullets
        if ($request->filled('trust_bullets')) {
            $bullets = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $request->trust_bullets))));
            $validated['trust_bullets'] = array_values($bullets);
        } else {
            $validated['trust_bullets'] = null;
        }

        $validated['is_active'] = $request->boolean('is_active');

        $slide->update($validated);

        return redirect()->route('super_admin.cms.slides.index')->with('success', __('Hero Slide updated successfully.'));
    }

    /**
     * Remove the specified slide from storage.
     */
    public function destroy(HomepageSlide $slide)
    {
        // Don't delete if it's the only slide
        if (HomepageSlide::count() <= 1) {
            return back()->with('error', __('At least one hero slide must remain in the system.'));
        }

        if ($slide->bg_image && File::exists(public_path($slide->bg_image))) {
            @File::delete(public_path($slide->bg_image));
        }
        if ($slide->bg_image_mobile && File::exists(public_path($slide->bg_image_mobile))) {
            @File::delete(public_path($slide->bg_image_mobile));
        }
        if ($slide->fg_image && File::exists(public_path($slide->fg_image))) {
            @File::delete(public_path($slide->fg_image));
        }

        $slide->delete();

        return redirect()->route('super_admin.cms.slides.index')->with('success', __('Hero Slide deleted successfully.'));
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleActive(HomepageSlide $slide)
    {
        $slide->is_active = !$slide->is_active;
        $slide->save();

        return back()->with('success', __('Slide status updated successfully.'));
    }

    /**
     * Update slide sorting order.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:homepage_slides,id',
        ]);

        foreach ($request->order as $position => $id) {
            HomepageSlide::where('id', $id)->update(['sort_order' => $position + 1]);
        }

        return response()->json(['success' => true, 'message' => __('Slide order updated successfully.')]);
    }
}

