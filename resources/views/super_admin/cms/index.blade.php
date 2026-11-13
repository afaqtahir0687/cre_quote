@extends('admin.layouts.app')

@section('title', __('Website CMS & Landing Page Manager'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Website CMS & Landing Page Manager') }}</h2>
            <span class="badge-soft badge-soft-primary px-2 py-1 rounded-pill small">
                <i class="fas fa-globe me-1"></i> Public Website Control
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Manage all content, headlines, images, application modules, testimonials, pricing presentation, and navigation without touching code.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ url('/') }}" target="_blank" class="btn btn-primary-modern btn-sm">
            <i class="fas fa-external-link-alt me-1"></i> {{ __('View Live Website') }}
        </a>
        <a href="{{ route('super_admin.dashboard') }}" class="btn btn-secondary-modern btn-sm">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Super Admin Portal') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Quick Metrics Grid -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm p-3 h-100 rounded-4" style="border: 1px solid var(--erp-border-color) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small text-uppercase fw-bold">{{ __('Active Sections') }}</span>
                <div class="p-2 rounded-3" style="background: rgba(67, 97, 238, 0.1); color: #4361EE;">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
            <div class="fs-3 fw-bold text-dark my-1">{{ $stats['active_sections'] }} <span class="fs-6 fw-normal text-muted">/ {{ $stats['sections'] }}</span></div>
            <div class="small text-muted"><i class="fas fa-check-circle text-success me-1"></i> {{ __('Homepage sections enabled') }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm p-3 h-100 rounded-4" style="border: 1px solid var(--erp-border-color) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small text-uppercase fw-bold">{{ __('App Grid Launchers') }}</span>
                <div class="p-2 rounded-3" style="background: rgba(0, 184, 148, 0.1); color: #00B894;">
                    <i class="fas fa-th-large"></i>
                </div>
            </div>
            <div class="fs-3 fw-bold text-dark my-1">{{ $stats['modules'] }}</div>
            <div class="small text-muted"><i class="fas fa-cubes text-info me-1"></i> {{ __('Application launcher boxes') }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm p-3 h-100 rounded-4" style="border: 1px solid var(--erp-border-color) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small text-uppercase fw-bold">{{ __('Testimonials') }}</span>
                <div class="p-2 rounded-3" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                    <i class="fas fa-quote-left"></i>
                </div>
            </div>
            <div class="fs-3 fw-bold text-dark my-1">{{ $stats['testimonials'] }}</div>
            <div class="small text-muted"><i class="fas fa-clock text-warning me-1"></i> {{ $stats['pending_testimonials'] }} {{ __('awaiting approval') }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm p-3 h-100 rounded-4" style="border: 1px solid var(--erp-border-color) !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small text-uppercase fw-bold">{{ __('Contact Inquiries') }}</span>
                <div class="p-2 rounded-3" style="background: rgba(239, 68, 68, 0.1); color: #EF4444;">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
            </div>
            <div class="fs-3 fw-bold text-dark my-1">{{ $stats['unread_messages'] }}</div>
            <div class="small text-muted"><i class="fas fa-bell text-danger me-1"></i> {{ __('Unread messages from website') }}</div>
        </div>
    </div>
</div>

<!-- Management Hub Cards -->
<div class="row g-4">
    <!-- Hero Slider Manager -->
    <div class="col-lg-4 col-md-6">
        <div class="cms-hub-card" style="border: 2px solid #4361EE !important; box-shadow: 0 10px 25px rgba(67, 97, 238, 0.08) !important;">
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="cms-icon-box" style="background: rgba(67, 97, 238, 0.15); color: #4361EE;">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <span class="badge bg-primary text-white px-2 py-1 rounded-pill small">Slider Engine</span>
                </div>
                <h5 class="cms-title">{{ __('Homepage Hero Slider') }}</h5>
                <p class="cms-desc">{{ __('Manage multi-slide carousel, drag-and-drop order, desktop & mobile backgrounds, headlines, and proposal modal CTAs.') }}</p>
                <div class="mt-auto pt-3 border-top">
                    <a href="{{ route('super_admin.cms.slides.index') }}" class="cms-btn">
                        <span><i class="fas fa-layer-group me-1"></i> {{ __('Manage Hero Slides') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Proposal Form Settings & Options -->
    <div class="col-lg-4 col-md-6">
        <div class="cms-hub-card" style="border: 2px solid #00B894 !important; box-shadow: 0 10px 25px rgba(0, 184, 148, 0.08) !important;">
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="cms-icon-box" style="background: rgba(0, 184, 148, 0.15); color: #00B894;">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <span class="badge bg-success text-white px-2 py-1 rounded-pill small">4-Step Wizard</span>
                </div>
                <h5 class="cms-title">{{ __('Proposal Form Settings') }}</h5>
                <p class="cms-desc">{{ __('Configure the 4-step modal popup: dynamic services, locations, goals, monthly budgets, and email notifications.') }}</p>
                <div class="mt-auto pt-3 border-top">
                    <a href="{{ route('super_admin.cms.proposals.settings') }}" class="cms-btn">
                        <span><i class="fas fa-cogs me-1"></i> {{ __('Form Options & Config') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Proposal Requests Leads -->
    @php
        $cmsPendingProps = class_exists(\App\Models\ProposalRequest::class) ? \App\Models\ProposalRequest::where('status', 'Pending')->count() : 0;
    @endphp
    <div class="col-lg-4 col-md-6">
        <div class="cms-hub-card">
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="cms-icon-box" style="background: rgba(245, 158, 11, 0.15); color: #D97706;">
                        <i class="fas fa-inbox"></i>
                    </div>
                    @if($cmsPendingProps > 0)
                        <span class="badge bg-warning text-dark px-2 py-1 rounded-pill small">{{ $cmsPendingProps }} Pending</span>
                    @else
                        <span class="badge-soft badge-soft-secondary">Marketing CRM</span>
                    @endif
                </div>
                <h5 class="cms-title">{{ __('Proposal Requests (Leads)') }}</h5>
                <p class="cms-desc">{{ __('Review all 4-step proposal submissions, contact clients, update lead status, and export to CSV.') }}</p>
                <div class="mt-auto pt-3 border-top">
                    <a href="{{ route('super_admin.proposals.index') }}" class="cms-btn">
                        <span><i class="fas fa-eye me-1"></i> {{ __('View Proposal Leads') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. Settings & Hero -->
    <div class="col-lg-4 col-md-6">
        <div class="cms-hub-card">
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="cms-icon-box" style="background: rgba(67, 97, 238, 0.1); color: #4361EE;">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                    <span class="badge-soft badge-soft-primary">Core Setup</span>
                </div>
                <h5 class="cms-title">{{ __('General & Hero Settings') }}</h5>
                <p class="cms-desc">{{ __('Branding, contacts, social URLs, SEO and Hero headline/buttons.') }}</p>
                <div class="mt-auto pt-3 border-top">
                    <a href="{{ route('super_admin.cms.settings') }}" class="cms-btn">
                        <span><i class="fas fa-edit me-1"></i> {{ __('Manage Settings & Hero') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Sections Manager -->
    <div class="col-lg-4 col-md-6">
        <div class="cms-hub-card">
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="cms-icon-box" style="background: rgba(0, 184, 148, 0.1); color: #00B894;">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <span class="badge-soft badge-soft-success">{{ $stats['active_sections'] }}/{{ $stats['sections'] }} Active</span>
                </div>
                <h5 class="cms-title">{{ __('Homepage Sections') }}</h5>
                <p class="cms-desc">{{ __('Reorder sections, toggle visibility, and update section headlines.') }}</p>
                <div class="mt-auto pt-3 border-top">
                    <a href="{{ route('super_admin.cms.sections') }}" class="cms-btn">
                        <span><i class="fas fa-sort me-1"></i> {{ __('Reorder & Toggle Sections') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. App Modules Grid -->
    <div class="col-lg-4 col-md-6">
        <div class="cms-hub-card">
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="cms-icon-box" style="background: rgba(6, 182, 212, 0.1); color: #0891B2;">
                        <i class="fas fa-th-large"></i>
                    </div>
                    <span class="badge-soft badge-soft-info">{{ $stats['modules'] }} Apps</span>
                </div>
                <h5 class="cms-title">{{ __('Application Icon Grid') }}</h5>
                <p class="cms-desc">{{ __('Manage the 18 application boxes, vector SVG icons & interactive previews.') }}</p>
                <div class="mt-auto pt-3 border-top">
                    <a href="{{ route('super_admin.cms.modules.index') }}" class="cms-btn">
                        <span><i class="fas fa-cubes me-1"></i> {{ __('Manage App Grid') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Features Grid -->
    <div class="col-lg-4 col-md-6">
        <div class="cms-hub-card">
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="cms-icon-box" style="background: rgba(245, 158, 11, 0.1); color: #D97706;">
                        <i class="fas fa-star"></i>
                    </div>
                    <span class="badge-soft badge-soft-warning">{{ $stats['features'] }} Features</span>
                </div>
                <h5 class="cms-title">{{ __('Core Features Grid') }}</h5>
                <p class="cms-desc">{{ __('Manage feature cards with titles, icons, and bullet point highlights.') }}</p>
                <div class="mt-auto pt-3 border-top">
                    <a href="{{ route('super_admin.cms.features.index') }}" class="cms-btn">
                        <span><i class="fas fa-list-check me-1"></i> {{ __('Manage Features') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Product Showcase Tabs -->
    <div class="col-lg-4 col-md-6">
        <div class="cms-hub-card">
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="cms-icon-box" style="background: rgba(139, 92, 246, 0.1); color: #7C3AED;">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <span class="badge-soft badge-soft-secondary">Workflow Steps</span>
                </div>
                <h5 class="cms-title">{{ __('Workflow Showcase Tabs') }}</h5>
                <p class="cms-desc">{{ __('Manage interactive workflow steps, descriptions, and previews.') }}</p>
                <div class="mt-auto pt-3 border-top">
                    <a href="{{ route('super_admin.cms.showcase.index') }}" class="cms-btn">
                        <span><i class="fas fa-desktop me-1"></i> {{ __('Manage Showcase Tabs') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Industry Solutions -->
    <div class="col-lg-4 col-md-6">
        <div class="cms-hub-card">
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="cms-icon-box" style="background: rgba(239, 68, 68, 0.1); color: #DC2626;">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <span class="badge-soft badge-soft-danger">{{ $stats['solutions'] }} Solutions</span>
                </div>
                <h5 class="cms-title">{{ __('Industry Solutions') }}</h5>
                <p class="cms-desc">{{ __('Printing/Signage, Fabrication, Wholesale & Multi-Branch cards.') }}</p>
                <div class="mt-auto pt-3 border-top">
                    <a href="{{ route('super_admin.cms.solutions.index') }}" class="cms-btn">
                        <span><i class="fas fa-building me-1"></i> {{ __('Manage Solutions') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 7. Customer Testimonials -->
    <div class="col-lg-4 col-md-6">
        <div class="cms-hub-card">
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="cms-icon-box" style="background: rgba(67, 97, 238, 0.1); color: #4361EE;">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <span class="badge-soft badge-soft-primary">{{ $stats['testimonials'] }} Reviews</span>
                </div>
                <h5 class="cms-title">{{ __('Testimonials & Reviews') }}</h5>
                <p class="cms-desc">{{ __('Add client testimonials, approve reviews, and manage star ratings.') }}</p>
                <div class="mt-auto pt-3 border-top">
                    <a href="{{ route('super_admin.cms.testimonials.index') }}" class="cms-btn">
                        <span><i class="fas fa-comment-dots me-1"></i> {{ __('Manage Testimonials') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 8. FAQs -->
    <div class="col-lg-4 col-md-6">
        <div class="cms-hub-card">
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="cms-icon-box" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <span class="badge-soft badge-soft-success">{{ $stats['faqs'] }} FAQs</span>
                </div>
                <h5 class="cms-title">{{ __('Frequently Asked Questions') }}</h5>
                <p class="cms-desc">{{ __('Manage accordion questions and answers on the landing page.') }}</p>
                <div class="mt-auto pt-3 border-top">
                    <a href="{{ route('super_admin.cms.faqs.index') }}" class="cms-btn">
                        <span><i class="fas fa-clipboard-question me-1"></i> {{ __('Manage FAQs') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 9. Navigation & Footer Links -->
    <div class="col-lg-4 col-md-6">
        <div class="cms-hub-card">
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="cms-icon-box" style="background: rgba(6, 182, 212, 0.1); color: #0891B2;">
                        <i class="fas fa-bars"></i>
                    </div>
                    <span class="badge-soft badge-soft-info">{{ $stats['nav_items'] }} Menus</span>
                </div>
                <h5 class="cms-title">{{ __('Navigation & Footer Menu') }}</h5>
                <p class="cms-desc">{{ __('Configure header navigation links and footer column items.') }}</p>
                <div class="mt-auto pt-3 border-top">
                    <a href="{{ route('super_admin.cms.nav.index') }}" class="cms-btn">
                        <span><i class="fas fa-link me-1"></i> {{ __('Manage Menus') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 10. Dynamic CMS & Footer Pages -->
    <div class="col-lg-4 col-md-6">
        <div class="cms-hub-card" style="border: 2px solid #10B981 !important; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.08) !important;">
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="cms-icon-box" style="background: rgba(16, 185, 129, 0.15); color: #10B981;">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <span class="badge bg-success text-white px-2 py-1 rounded-pill small">{{ $stats['pages'] ?? 0 }} Pages</span>
                </div>
                <h5 class="cms-title">{{ __('Footer & Dynamic Pages') }}</h5>
                <p class="cms-desc">{{ __('Manage company pages (About Us, Contact Us) and legal policies (Privacy Policy, Terms, Refund Policy).') }}</p>
                <div class="mt-auto pt-3 border-top">
                    <a href="{{ route('super_admin.cms.pages.index') }}" class="cms-btn">
                        <span><i class="fas fa-edit me-1"></i> {{ __('Manage Pages & Footer') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Dedicated Services Catalog Module -->
    <div class="col-lg-4 col-md-6">
        <div class="cms-hub-card" style="border: 2px solid #0A6FB7 !important; box-shadow: 0 10px 25px rgba(10, 111, 183, 0.08) !important;">
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="cms-icon-box" style="background: rgba(10, 111, 183, 0.15); color: #0A6FB7;">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <span class="badge bg-primary text-white px-2 py-1 rounded-pill small">{{ class_exists(\App\Models\Service::class) ? \App\Models\Service::count() : 0 }} Services</span>
                </div>
                <h5 class="cms-title">{{ __('Services Portfolio') }}</h5>
                <p class="cms-desc">{{ __('Manage all services across Creativity, Marketing, and Advertising with custom pricing and features.') }}</p>
                <div class="mt-auto pt-3 border-top">
                    <a href="{{ route('services.index') }}" class="cms-btn">
                        <span><i class="fas fa-cogs me-1"></i> {{ __('Manage Services') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 10. Contact Inquiries -->
    <div class="col-lg-4 col-md-6">
        <div class="cms-hub-card">
            <div class="p-4 d-flex flex-column h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="cms-icon-box" style="background: rgba(239, 68, 68, 0.1); color: #DC2626;">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    @if($stats['unread_messages'] > 0)
                        <span class="badge-soft badge-soft-danger">{{ $stats['unread_messages'] }} New</span>
                    @else
                        <span class="badge-soft badge-soft-secondary">0 New</span>
                    @endif
                </div>
                <h5 class="cms-title">{{ __('Contact Inquiries') }}</h5>
                <p class="cms-desc">{{ __('Review messages submitted by visitors on the landing page.') }}</p>
                <div class="mt-auto pt-3 border-top">
                    <a href="{{ route('super_admin.cms.messages.index') }}" class="cms-btn">
                        <span><i class="fas fa-inbox me-1"></i> {{ __('View Inquiries') }}</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
