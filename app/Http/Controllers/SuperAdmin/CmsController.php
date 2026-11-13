<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\CmsSetting;
use App\Models\CmsSection;
use App\Models\CmsAppModule;
use App\Models\CmsFeature;
use App\Models\CmsShowcaseTab;
use App\Models\CmsSolution;
use App\Models\CmsStep;
use App\Models\CmsBenefit;
use App\Models\CmsTestimonial;
use App\Models\CmsFaq;
use App\Models\CmsNavItem;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CmsController extends Controller
{
    /**
     * Display the CMS Dashboard Hub.
     */
    public function index()
    {
        $stats = [
            'sections' => CmsSection::count(),
            'active_sections' => CmsSection::where('is_active', true)->count(),
            'modules' => CmsAppModule::count(),
            'features' => CmsFeature::count(),
            'showcase_tabs' => CmsShowcaseTab::count(),
            'solutions' => CmsSolution::count(),
            'testimonials' => CmsTestimonial::count(),
            'pending_testimonials' => CmsTestimonial::where('is_approved', false)->count(),
            'faqs' => CmsFaq::count(),
            'nav_items' => CmsNavItem::count(),
            'pages' => class_exists(\App\Models\CmsPage::class) ? \App\Models\CmsPage::count() : 0,
            'unread_messages' => ContactMessage::where('is_read', false)->count(),
        ];

        return view('super_admin.cms.index', compact('stats'));
    }

    /**
     * Display and edit global website settings & Hero section.
     */
    public function settings()
    {
        $settings = CmsSetting::allKeyed();
        return view('super_admin.cms.settings', compact('settings'));
    }

    /**
     * Save global website settings & Hero section.
     */
    public function updateSettings(Request $request)
    {
        $data = $request->except(['_token', '_method', 'website_logo', 'hero_image']);

        // Handle Website Logo upload
        if ($request->hasFile('website_logo')) {
            $request->validate(['website_logo' => 'image|mimes:jpeg,png,jpg,webp,svg|max:4096']);
            $logoName = 'website_logo_' . time() . '.' . $request->file('website_logo')->getClientOriginalExtension();
            $request->file('website_logo')->move(public_path('uploads/cms'), $logoName);
            CmsSetting::set('website_logo', 'uploads/cms/' . $logoName, 'branding');
        }

        // Handle Hero Image / Dashboard Screenshot upload
        if ($request->hasFile('hero_image')) {
            $request->validate(['hero_image' => 'image|mimes:jpeg,png,jpg,webp,svg|max:6144']);
            $heroName = 'hero_mockup_' . time() . '.' . $request->file('hero_image')->getClientOriginalExtension();
            $request->file('hero_image')->move(public_path('uploads/cms'), $heroName);
            CmsSetting::set('hero_image', 'uploads/cms/' . $heroName, 'hero');
        }

        foreach ($data as $key => $val) {
            CmsSetting::set($key, $val ?? '');
        }

        // Clear public view cache
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->back()->with('success', 'Website settings updated successfully.');
    }

    /**
     * Manage homepage sections: visibility, ordering, titles.
     */
    public function sections()
    {
        $sections = CmsSection::orderBy('sort_order')->get();
        return view('super_admin.cms.sections', compact('sections'));
    }

    /**
     * Update homepage sections order and active status.
     */
    public function updateSections(Request $request)
    {
        $request->validate([
            'sections' => 'required|array',
            'sections.*.id' => 'required|exists:cms_sections,id',
            'sections.*.title' => 'nullable|string',
            'sections.*.subtitle' => 'nullable|string',
            'sections.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->sections as $secData) {
            $section = CmsSection::find($secData['id']);
            if ($section) {
                $section->title = $secData['title'] ?? null;
                $section->subtitle = $secData['subtitle'] ?? null;
                $section->sort_order = (int)$secData['sort_order'];
                $section->is_active = isset($secData['is_active']) && $secData['is_active'] == '1';
                $section->save();
            }
        }

        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->back()->with('success', 'Homepage sections updated successfully.');
    }
}

