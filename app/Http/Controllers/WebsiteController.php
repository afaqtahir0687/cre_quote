<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Customer;
use App\Models\User;
use App\Models\Shop;
use App\Models\Invoice;
use App\Models\JobOrder;
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
use App\Models\HomepageSlide;
use App\Models\ProposalOption;
use App\Models\Product;
use App\Models\Category;
use App\Models\CmsPage;
use App\Models\Service;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    /**
     * Display the fully dynamic SaaS landing page for Crelogics ERP.
     */
    public function index()
    {
        // 1. Global Settings
        $settings = CmsSetting::allKeyed();

        // 2. Active Sections (Ordered)
        $sections = CmsSection::active()->get()->keyBy('section_key');
        $orderedSections = CmsSection::active()->get();

        // Dynamic Hero Slides
        $slides = HomepageSlide::active()->get();

        // Dynamic 4-Step Proposal Options & Settings
        $proposalServices = ProposalOption::ofType('service')->active()->get();
        $proposalLocations = ProposalOption::ofType('location')->active()->get();
        $proposalGoals = ProposalOption::ofType('goal')->active()->get();
        $proposalBudgets = ProposalOption::ofType('budget')->active()->get();
        $proposalTimelines = ProposalOption::ofType('timeline')->active()->get();
        $proposalSettings = CmsSetting::where('group', 'proposal')->pluck('value', 'key')->toArray();

        // 3. Dynamic Business Models
        $packages = Package::where('is_active', true)->orderBy('sort_order')->get();
        $appModules = CmsAppModule::active()->get();
        $features = CmsFeature::active()->get();
        $showcaseTabs = CmsShowcaseTab::active()->get();
        $solutions = CmsSolution::active()->get();
        $steps = CmsStep::active()->get();
        $benefits = CmsBenefit::active()->get();
        $testimonials = CmsTestimonial::active()->get();
        $faqs = CmsFaq::active()->get();

        // 4. Navigation & Footer Links
        $headerNav = CmsNavItem::where('location', 'header')->active()->get();
        $footerNav = CmsNavItem::where('location', 'footer_nav')->active()->get();
        $footerModules = CmsNavItem::where('location', 'footer_modules')->active()->get();

        // 5. Dynamic Metrics
        $customerCount = Customer::count();
        $userCount = User::count();
        $shopCount = Shop::count();
        $invoiceCount = Invoice::count();
        $orderCount = JobOrder::count();

        // Determine social proof metric according to CMS configuration
        $metricMode = $settings['social_proof_metric_mode'] ?? 'dynamic';
        if ($metricMode === 'manual' && !empty($settings['social_proof_manual_count'])) {
            $businessCount = (int)$settings['social_proof_manual_count'];
        } else {
            $businessCount = $customerCount > 0 ? $customerCount : ($shopCount > 0 ? $shopCount : $userCount);
        }

        // Real verified client accounts for social proof chips
        $customers = Customer::select('id', 'name', 'company_name')
            ->orderBy('id', 'desc')
            ->take(12)
            ->get();

        // 6. Dynamic Admin Database Records for Live Module Previews (Zero Static Data)
        $recentJobOrders = JobOrder::with('customer')->latest()->take(4)->get();
        $recentQuotes = class_exists(\App\Models\Quote::class) ? \App\Models\Quote::with('customer')->latest()->take(4)->get() : collect();
        $recentInvoices = Invoice::with('customer')->latest()->take(4)->get();
        $recentCustomers = Customer::latest()->take(6)->get();
        $recentProducts = class_exists(\App\Models\Product::class) ? \App\Models\Product::with('category')->latest()->take(6)->get() : collect();
        $allCategories = class_exists(\App\Models\Category::class) ? \App\Models\Category::take(6)->get() : collect();
        $allEmployees = class_exists(\App\Models\Employee::class) ? \App\Models\Employee::take(4)->get() : collect();
        $allDepartments = class_exists(\App\Models\Department::class) ? \App\Models\Department::take(4)->get() : collect();
        $allShops = Shop::all();
        $totalRevenue = (float)Invoice::sum('grand_total');
        $paidRevenue = (float)Invoice::sum('amount_paid');
        $pendingDue = (float)Invoice::sum('balance_due');

        return view('website.home', compact(
            'settings',
            'sections',
            'orderedSections',
            'packages',
            'appModules',
            'features',
            'showcaseTabs',
            'solutions',
            'steps',
            'benefits',
            'testimonials',
            'faqs',
            'headerNav',
            'footerNav',
            'footerModules',
            'businessCount',
            'customerCount',
            'userCount',
            'shopCount',
            'invoiceCount',
            'orderCount',
            'customers',
            'recentJobOrders',
            'recentQuotes',
            'recentInvoices',
            'recentCustomers',
            'recentProducts',
            'allCategories',
            'allEmployees',
            'allDepartments',
            'allShops',
            'totalRevenue',
            'paidRevenue',
            'pendingDue',
            'slides',
            'proposalServices',
            'proposalLocations',
            'proposalGoals',
            'proposalBudgets',
            'proposalTimelines',
            'proposalSettings'
        ));
    }

    /**
     * Handle public contact form submission with validation and CSRF.
     */
    public function contactSubmit(Request $request)
    {
        $validated = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:150',
            'message' => 'required|string|max:3000',
        ])->validate();

        $contactMessage = ContactMessage::create($validated);

        // Send notification email to Super Admin using Gmail SMTP
        try {
            $recipients = \App\Models\User::where('role', 'super_admin')
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();

            $cmsEmail = \App\Models\CmsSetting::get('contact_email');
            if ($cmsEmail && filter_var($cmsEmail, FILTER_VALIDATE_EMAIL)) {
                $recipients[] = $cmsEmail;
            }

            $mailFrom = config('mail.from.address') ?: 'creelogics@gmail.com';
            if ($mailFrom && filter_var($mailFrom, FILTER_VALIDATE_EMAIL)) {
                $recipients[] = $mailFrom;
            }

            $recipients = array_unique(array_filter($recipients));

            if (!empty($recipients)) {
                \Illuminate\Support\Facades\Mail::send(
                    'emails.contact_inquiry',
                    ['contactMessage' => $contactMessage],
                    function ($mail) use ($recipients, $contactMessage) {
                        $mail->to($recipients)
                            ->replyTo($contactMessage->email, $contactMessage->name)
                            ->subject('New Website Contact Inquiry: ' . ($contactMessage->subject ?: 'Message from ' . $contactMessage->name));
                    }
                );
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Contact form email failed: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'contact_message_id' => $contactMessage->id,
            ]);
        }

        $successMsg = 'Thank you! Your message has been sent successfully. Our team will contact you shortly.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
            ]);
        }

        return redirect()->back()->with('contact_success', $successMsg);
    }

    /**
     * Display the dynamic Services page.
     */
    public function services(Request $request)
    {
        $settings = CmsSetting::allKeyed();
        $headerNav = CmsNavItem::where('location', 'header')->active()->get();
        $footerNav = CmsNavItem::where('location', 'footer_nav')->active()->get();
        $footerModules = CmsNavItem::where('location', 'footer_modules')->active()->get();

        // 1. Dynamic Dedicated Services (From services table)
        $services = Service::active()->orderBy('category')->orderBy('sort_order')->get();
        $servicesByCategory = $services->groupBy('category');

        // 2. Dynamic Industry Solutions
        $solutions = CmsSolution::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // 3. Dynamic Categories
        $categories = Category::where('status', true)->get();

        // 4. Dynamic Proposal Modal Options
        $proposalServices = ProposalOption::ofType('service')->active()->get();
        $proposalLocations = ProposalOption::ofType('location')->active()->get();
        $proposalGoals = ProposalOption::ofType('goal')->active()->get();
        $proposalBudgets = ProposalOption::ofType('budget')->active()->get();
        $proposalTimelines = ProposalOption::ofType('timeline')->active()->get();
        $proposalSettings = CmsSetting::where('group', 'proposal')->pluck('value', 'key')->toArray();

        return view('website.services', compact(
            'settings',
            'headerNav',
            'footerNav',
            'footerModules',
            'services',
            'servicesByCategory',
            'solutions',
            'categories',
            'proposalServices',
            'proposalLocations',
            'proposalGoals',
            'proposalBudgets',
            'proposalTimelines',
            'proposalSettings'
        ));
    }

    /**
     * Display a dedicated inner page for a specific service.
     */
    public function serviceDetail($slug)
    {
        $settings = CmsSetting::allKeyed();
        $headerNav = CmsNavItem::where('location', 'header')->active()->get();
        $footerNav = CmsNavItem::where('location', 'footer_nav')->active()->get();
        $footerModules = CmsNavItem::where('location', 'footer_modules')->active()->get();

        $service = Service::where('slug', $slug)->first();

        if (!$service) {
            // Also try matching slug with hyphens or spaces
            $service = Service::all()->first(function ($s) use ($slug) {
                return \Illuminate\Support\Str::slug($s->name) === $slug || $s->slug === $slug;
            });
        }

        if (!$service) {
            abort(404, 'Service not found');
        }

        // Related services in the same department
        $relatedServices = Service::active()
            ->where('category', $service->category)
            ->where('id', '!=', $service->id)
            ->take(6)
            ->get();

        // Dynamic Proposal Modal Options
        $proposalServices = ProposalOption::ofType('service')->active()->get();
        $proposalLocations = ProposalOption::ofType('location')->active()->get();
        $proposalGoals = ProposalOption::ofType('goal')->active()->get();
        $proposalBudgets = ProposalOption::ofType('budget')->active()->get();
        $proposalTimelines = ProposalOption::ofType('timeline')->active()->get();
        $proposalSettings = CmsSetting::where('group', 'proposal')->pluck('value', 'key')->toArray();

        return view('website.service_detail', compact(
            'settings',
            'headerNav',
            'footerNav',
            'footerModules',
            'service',
            'relatedServices',
            'proposalServices',
            'proposalLocations',
            'proposalGoals',
            'proposalBudgets',
            'proposalTimelines',
            'proposalSettings'
        ));
    }

    /**
     * Display the dynamic Products catalog page.
     */
    public function products(Request $request)
    {
        $settings = CmsSetting::allKeyed();
        $headerNav = CmsNavItem::where('location', 'header')->active()->get();
        $footerNav = CmsNavItem::where('location', 'footer_nav')->active()->get();
        $footerModules = CmsNavItem::where('location', 'footer_modules')->active()->get();

        // 1. Dynamic Categories with product count
        $categories = Category::where('status', true)
            ->withCount(['products' => function($q) {
                $q->where('type', 'product');
            }])
            ->get();

        // 2. Query dynamic products with filtering & search
        $query = Product::where('type', 'product')->with(['category', 'unit']);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $products = $query->orderBy('name')->paginate(12)->withQueryString();

        return view('website.products', compact(
            'settings',
            'headerNav',
            'footerNav',
            'footerModules',
            'categories',
            'products'
        ));
    }

    /**
     * Display a dynamic CMS page (e.g., About Us, Privacy Policy, Terms, etc.).
     */
    public function showPage(string $slug)
    {
        $settings = CmsSetting::allKeyed();
        $headerNav = CmsNavItem::where('location', 'header')->active()->get();
        $footerNav = CmsNavItem::where('location', 'footer_nav')->active()->get();
        $footerModules = CmsNavItem::where('location', 'footer_modules')->active()->get();

        $page = CmsPage::where('slug', $slug)->firstOrFail();

        // If inactive, only allow authenticated super_admin or admin to preview
        if (!$page->is_active && !auth()->check()) {
            abort(404);
        }

        $otherPages = CmsPage::active()->where('id', '!=', $page->id)->get();
        $companyPages = CmsPage::forFooter('company')->get();
        $legalPages = CmsPage::forFooter('legal')->get();

        return view('website.page', compact(
            'page',
            'settings',
            'headerNav',
            'footerNav',
            'footerModules',
            'otherPages',
            'companyPages',
            'legalPages'
        ));
    }
}
