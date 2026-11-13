@extends('website.layouts.app')

@section('title', ($page->meta_title ?: $page->title) . ' - ' . ($settings['site_name'] ?? 'CreLogics ERP'))

@push('styles')
<style>
.cms-page-hero {
    background: radial-gradient(circle at 10% 20%, rgba(250, 116, 54, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(10, 111, 183, 0.06) 0%, transparent 50%),
                #FFFFFF;
    border-bottom: 1px solid #E2E8F0;
    padding: 55px 0 45px 0;
}
.cms-breadcrumb {
    font-size: 0.85rem;
    font-weight: 500;
}
.cms-breadcrumb a {
    color: #64748B;
    text-decoration: none;
    transition: color 0.15s ease;
}
.cms-breadcrumb a:hover {
    color: #FA7436;
}
.cms-main-card {
    background: #FFFFFF;
    border-radius: 16px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    padding: 40px;
}
@media (max-width: 768px) {
    .cms-main-card {
        padding: 24px 18px;
    }
}

/* Automatic Code-Level Typography & Card Styling (No manual CSS needed from Admin) */
.cms-article-body {
    font-size: 1.02rem;
    color: #475569;
    line-height: 1.85;
}
.cms-article-body > p:first-of-type {
    font-size: 1.12rem;
    color: #334155;
    line-height: 1.85;
    font-weight: 500;
    background: #FFF8F3; /* Soft CreLogics peach cream */
    padding: 20px 24px;
    border-radius: 12px;
    border-left: 4px solid #FA7436; /* Exact CreLogics Orange Accent */
    margin-bottom: 2rem;
    box-shadow: 0 1px 4px rgba(250, 116, 54, 0.06);
}
.cms-article-body h2,
.cms-article-body h3,
.cms-article-body h4 {
    color: #0F172A;
    font-weight: 700;
    font-size: 1.35rem;
    margin-top: 2.2rem;
    margin-bottom: 0.9rem;
    letter-spacing: -0.01em;
    display: flex;
    align-items: center;
    gap: 10px;
}
.cms-article-body h2::before,
.cms-article-body h3::before {
    content: "";
    display: inline-block;
    width: 5px;
    height: 18px;
    border-radius: 3px;
    background: linear-gradient(180deg, #FA7436 0%, #FF8300 100%);
    flex-shrink: 0;
}
.cms-article-body p {
    color: #475569;
    font-size: 1rem;
    line-height: 1.85;
    margin-bottom: 1.25rem;
}
.cms-article-body ul,
.cms-article-body ol {
    padding-left: 1.5rem;
    margin-bottom: 1.8rem;
}
.cms-article-body li {
    color: #475569;
    line-height: 1.8;
    margin-bottom: 0.65rem;
    padding-left: 4px;
}
.cms-article-body strong,
.cms-article-body b {
    color: #0F172A;
    font-weight: 600;
}
.cms-article-body a {
    color: #0A6FB7;
    text-decoration: underline;
    font-weight: 500;
    transition: color 0.15s ease;
}
.cms-article-body a:hover {
    color: #FA7436;
}

/* Sidebar Widgets */
.cms-sidebar-widget {
    background: #FFFFFF;
    border-radius: 16px;
    border: 1px solid #E2E8F0;
    padding: 24px;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
    margin-bottom: 24px;
}
.cms-nav-item-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    border-radius: 10px;
    color: #475569;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    margin-bottom: 8px;
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
}
.cms-nav-item-link:hover {
    background: rgba(250, 116, 54, 0.08) !important;
    color: #FA7436 !important;
    border-color: rgba(250, 116, 54, 0.35) !important;
    transform: translateX(5px);
    box-shadow: 0 4px 14px rgba(250, 116, 54, 0.12);
}
.cms-nav-item-link:hover i {
    color: #FA7436 !important;
}
.cms-nav-item-link.active {
    background: linear-gradient(135deg, #FA7436 0%, #FF8300 100%) !important;
    color: #FFFFFF !important;
    border-color: #FA7436 !important;
    box-shadow: 0 6px 18px rgba(250, 116, 54, 0.38) !important;
}
.cms-nav-item-link.active i {
    color: #FFFFFF !important;
}

/* CreLogics Logo-Themed Primary CTA Button */
.btn-crelogics-cta {
    background: linear-gradient(135deg, #FA7436 0%, #FF8300 100%) !important;
    color: #FFFFFF !important;
    font-weight: 700;
    padding: 11px 24px;
    border-radius: 12px;
    border: none !important;
    box-shadow: 0 8px 22px rgba(250, 116, 54, 0.38);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.btn-crelogics-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(250, 116, 54, 0.48);
    color: #FFFFFF !important;
    filter: brightness(1.05);
}

/* Action Support Buttons */
.btn-action-whatsapp {
    background: linear-gradient(135deg, #25D366 0%, #1EBE5D 100%) !important;
    color: #FFFFFF !important;
    font-weight: 700;
    border: none !important;
    border-radius: 10px;
    padding: 10px 18px;
    box-shadow: 0 6px 16px rgba(37, 211, 102, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.25s ease;
}
.btn-action-whatsapp:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(37, 211, 102, 0.45);
    color: #FFFFFF !important;
}

.btn-action-email {
    background: linear-gradient(135deg, #0A6FB7 0%, #0070BA 100%) !important;
    color: #FFFFFF !important;
    font-weight: 700;
    border: none !important;
    border-radius: 10px;
    padding: 10px 18px;
    box-shadow: 0 6px 16px rgba(10, 111, 183, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.25s ease;
}
.btn-action-email:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(10, 111, 183, 0.45);
    color: #FFFFFF !important;
}

.btn-action-orange {
    background: linear-gradient(135deg, #FA7436 0%, #FF8300 100%) !important;
    color: #FFFFFF !important;
    font-weight: 700;
    border: none !important;
    border-radius: 10px;
    padding: 10px 18px;
    box-shadow: 0 6px 16px rgba(250, 116, 54, 0.35);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.25s ease;
}
.btn-action-orange:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(250, 116, 54, 0.48);
    color: #FFFFFF !important;
}
</style>
@endpush

@section('content')
<!-- Page Header & Hero Section -->
<header class="cms-page-hero">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb cms-breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}"><i class="fas fa-home me-1"></i> Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('home') }}#footer">Pages</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $page->title }}</li>
            </ol>
        </nav>

        <div class="row align-items-center justify-content-between g-3">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 mb-2">
                    <span class="badge px-3 py-1 rounded-pill fw-semibold text-uppercase" style="background: rgba(250, 116, 54, 0.12); color: #FA7436; border: 1px solid rgba(250, 116, 54, 0.25); font-size: 0.75rem; letter-spacing: 0.05em;">
                        @if($page->footer_column === 'company')
                            <i class="fas fa-building me-1"></i> Company
                        @elseif($page->footer_column === 'legal')
                            <i class="fas fa-balance-scale me-1"></i> Legal & Policy
                        @else
                            <i class="fas fa-file-alt me-1"></i> Information
                        @endif
                    </span>
                    <span class="text-muted small">
                        <i class="far fa-clock me-1"></i> Updated {{ $page->updated_at->format('M d, Y') }}
                    </span>
                </div>
                <h1 class="display-6 fw-bold text-dark mb-2">
                    {{ $page->title }}
                </h1>
                @if($page->subtitle)
                    <p class="lead text-muted mb-0" style="font-size: 1.1rem;">
                        {{ $page->subtitle }}
                    </p>
                @endif
            </div>

            <div class="col-lg-4 text-lg-end">
                <a href="{{ route('register') }}" class="btn-crelogics-cta">
                    <i class="fas fa-rocket me-1"></i> Start 7-Day Free Trial
                </a>
            </div>
        </div>
    </div>
</header>

<!-- Main Page Content & Sidebar -->
<div class="py-5" style="background-color: #F8FAFC;">
    <div class="container">
        <div class="row g-4">
            <!-- Left / Center: Main Content Card -->
            <div class="col-lg-8">
                <article class="cms-main-card">
                    <div class="cms-article-body">
                        {!! $page->content !!}
                    </div>

                    {{-- Code-driven rich components for About Us --}}
                    @if($page->slug === 'about-us')
                        <!-- Mission & Vision Cards in Code -->
                        <div class="row g-4 my-4">
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 h-100" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(10, 111, 183, 0.12); color: #0A6FB7; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                                            <i class="fas fa-bullseye"></i>
                                        </div>
                                        <h4 class="m-0 fw-bold text-dark" style="font-size: 1.2rem;">Our Mission</h4>
                                    </div>
                                    <p class="text-muted m-0" style="line-height: 1.7;">
                                        To empower manufacturers and custom-order businesses with transparent, automated workflows that turn slow quotes into signed contracts, streamline shop floor production, and deliver real-time financial profitability.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 h-100" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(250, 116, 54, 0.12); color: #FA7436; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                                            <i class="fas fa-eye"></i>
                                        </div>
                                        <h4 class="m-0 fw-bold text-dark" style="font-size: 1.2rem;">Our Vision</h4>
                                    </div>
                                    <p class="text-muted m-0" style="line-height: 1.7;">
                                        To become the global gold standard ERP for personalized production, providing an intuitive, multi-branch platform accessible on any device with zero cumbersome installation.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- 4 Core Capabilities in Code -->
                        <div class="row g-3 my-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background: #FFFFFF; border: 1px solid #EDF2F7;">
                                    <i class="fas fa-check-circle text-success fs-5 mt-1"></i>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1rem;">Lightning-Fast Quotations</h5>
                                        <p class="text-muted small m-0">Generate polished, branded PDF quotes with accurate square-foot or unit pricing in under 60 seconds.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background: #FFFFFF; border: 1px solid #EDF2F7;">
                                    <i class="fas fa-check-circle text-success fs-5 mt-1"></i>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1rem;">Complete Job Order Tracking</h5>
                                        <p class="text-muted small m-0">From pre-press artwork proofing to fabrication, quality check, and dispatch delivery notes.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background: #FFFFFF; border: 1px solid #EDF2F7;">
                                    <i class="fas fa-check-circle text-success fs-5 mt-1"></i>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1rem;">Multi-Branch & Central Control</h5>
                                        <p class="text-muted small m-0">Manage multiple shop outlets or workshops from a single centralized dashboard with branch-level permissions.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-3 p-3 rounded-3" style="background: #FFFFFF; border: 1px solid #EDF2F7;">
                                    <i class="fas fa-check-circle text-success fs-5 mt-1"></i>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1rem;">99.9% Cloud Reliability</h5>
                                        <p class="text-muted small m-0">Bank-grade data encryption, automated offsite backups, and high-availability cloud hosting.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Global Operations Banner in Code -->
                        <div class="p-4 rounded-4 mt-4 text-white" style="background: linear-gradient(135deg, #17243A 0%, #1e3a5f 100%);">
                            <h4 class="fw-bold text-white mb-2">Global Operations & Support</h4>
                            <p class="text-white-50 mb-3" style="line-height: 1.7;">
                                CreLogics maintains active technology engineering and customer success operations across Pakistan and the United States, providing around-the-clock implementation support, training, and custom workflow consultations.
                            </p>
                            <div class="d-flex flex-wrap gap-4 text-white small">
                                <div><i class="fas fa-map-marker-alt text-warning me-2"></i><strong>Lahore, Pakistan:</strong> Airline Housing Society</div>
                                <div><i class="fas fa-map-marker-alt text-warning me-2"></i><strong>Montana, USA:</strong> Kalispell, MT 59901</div>
                                <div><i class="fas fa-envelope text-info me-2"></i><strong>Email:</strong> info@crelogics.com</div>
                            </div>
                        </div>
                    @endif

                    {{-- Code-driven rich components for Contact Us --}}
                    @if($page->slug === 'contact-us')
                        <!-- Regional Office Cards in Code -->
                        <div class="row g-4 my-4">
                            <!-- Pakistan Office -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm p-4 h-100 rounded-4" style="background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <span class="badge px-3 py-2 text-white fw-bold" style="background: #16A579; font-size: 0.85rem; border-radius: 8px;">
                                            PAKISTAN HEADQUARTERS
                                        </span>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-3">Lahore Commercial Office</h5>
                                    <p class="text-muted mb-3" style="line-height: 1.6;">
                                        <i class="fas fa-map-marked-alt text-primary me-2"></i>
                                        First Floor, Building No. 184-Commercial,<br>
                                        Airline Housing Society, Lahore, Pakistan
                                    </p>
                                    <div class="d-flex flex-column gap-2 mt-auto pt-3 border-top">
                                        <a href="tel:+923451438102" class="text-decoration-none text-dark fw-semibold">
                                            <i class="fas fa-phone-alt text-success me-2"></i> +92 3451438102
                                        </a>
                                        <a href="mailto:info@crelogics.com" class="text-decoration-none text-muted">
                                            <i class="fas fa-envelope text-info me-2"></i> info@crelogics.com
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- USA Office -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm p-4 h-100 rounded-4" style="background: #FFFFFF; border: 1px solid #E2E8F0 !important;">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <span class="badge px-3 py-2 text-white fw-bold" style="background: #FA7436; font-size: 0.85rem; border-radius: 8px;">
                                            UNITED STATES OFFICE
                                        </span>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-3">Montana Regional Hub</h5>
                                    <p class="text-muted mb-3" style="line-height: 1.6;">
                                        <i class="fas fa-map-marked-alt text-primary me-2"></i>
                                        KALISPELL, MT 59901 USA.
                                    </p>
                                    <div class="d-flex flex-column gap-2 mt-auto pt-3 border-top">
                                        <a href="tel:+14062251210" class="text-decoration-none text-dark fw-semibold">
                                            <i class="fas fa-phone-alt text-success me-2"></i> +14062251210
                                        </a>
                                        <a href="mailto:info@crelogics.com" class="text-decoration-none text-muted">
                                            <i class="fas fa-envelope text-info me-2"></i> info@crelogics.com
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Direct Support Channels in Code -->
                        <div class="p-4 rounded-4 my-4" style="background: #F1F5F9;">
                            <h4 class="fw-bold text-dark mb-3" style="font-size: 1.15rem;">Fast Support Channels</h4>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <a href="https://wa.me/923451438102" target="_blank" class="btn-action-whatsapp w-100 py-3">
                                        <i class="fab fa-whatsapp fs-5"></i> Chat on WhatsApp
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="mailto:info@crelogics.com" class="btn-action-email w-100 py-3">
                                        <i class="fas fa-envelope fs-5"></i> Send Email
                                    </a>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ route('home') }}#contact" class="btn-action-orange w-100 py-3">
                                        <i class="fas fa-paper-plane fs-5"></i> Submit Web Inquiry
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Bottom Page Action Bar -->
                    <div class="mt-5 pt-4 border-top d-flex flex-wrap align-items-center justify-content-between gap-3 text-muted small">
                        <div>
                            <i class="fas fa-check-shield text-success me-1"></i>
                            Official Documentation &bull; <strong>CreLogics Cloud ERP</strong>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('home') }}" class="text-decoration-none text-muted">
                                <i class="fas fa-arrow-left me-1"></i> Back to Homepage
                            </a>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Right Sidebar: Other Pages & Fast Support -->
            <div class="col-lg-4">
                <!-- 1. All Dynamic Pages Navigation Widget -->
                @php
                    $allActivePages = class_exists(\App\Models\CmsPage::class) ? \App\Models\CmsPage::active()->get() : collect();
                @endphp
                @if($allActivePages->count() > 0)
                    <div class="cms-sidebar-widget">
                        <h5 class="fw-bold text-dark mb-3" style="font-size: 1.05rem;">
                            <i class="fas fa-book-open text-primary me-2"></i> Company & Legal Pages
                        </h5>
                        <div class="cms-nav-list">
                            @foreach($allActivePages as $p)
                                <a href="{{ route('website.page', $p->slug) }}" class="cms-nav-item-link {{ $p->id === $page->id ? 'active' : '' }}">
                                    <span>
                                        @if($p->footer_column === 'legal')
                                            <i class="fas fa-shield-alt text-muted me-2" style="font-size: 0.85rem;"></i>
                                        @elseif($p->slug === 'contact-us')
                                            <i class="fas fa-headset text-muted me-2" style="font-size: 0.85rem;"></i>
                                        @else
                                            <i class="fas fa-file-alt text-muted me-2" style="font-size: 0.85rem;"></i>
                                        @endif
                                        {{ $p->title }}
                                    </span>
                                    <i class="fas fa-chevron-right fa-xs"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- 2. Fast Help & Regional Support Widget -->
                <div class="cms-sidebar-widget">
                    <h5 class="fw-bold text-dark mb-3" style="font-size: 1.05rem;">
                        <i class="fas fa-phone-volume text-success me-2"></i> Need Direct Assistance?
                    </h5>
                    <p class="text-muted small mb-3">
                        Our regional support engineers are available across Pakistan and USA time zones.
                    </p>

                    <!-- Pakistan Contact -->
                    <div class="p-3 rounded-3 mb-2" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge bg-success-subtle text-success fw-bold" style="font-size: 0.7rem;">PAKISTAN</span>
                            <span class="text-muted small">+92 3451438102</span>
                        </div>
                        <div class="small text-muted">First Floor, Bldg 184-Commercial, Airline Housing, Lahore</div>
                    </div>

                    <!-- USA Contact -->
                    <div class="p-3 rounded-3 mb-3" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="badge bg-primary-subtle text-primary fw-bold" style="font-size: 0.7rem;">USA</span>
                            <span class="text-muted small">+14062251210</span>
                        </div>
                        <div class="small text-muted">KALISPELL, MT 59901 USA</div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="https://wa.me/923451438102" target="_blank" class="btn-action-whatsapp w-100 py-2">
                            <i class="fab fa-whatsapp me-1"></i> WhatsApp Live Chat
                        </a>
                        <a href="mailto:info@crelogics.com" class="btn-action-email w-100 py-2">
                            <i class="fas fa-envelope me-1"></i> info@crelogics.com
                        </a>
                    </div>
                </div>

                <!-- 3. Free Trial Banner Widget -->
                <div class="cms-sidebar-widget text-white text-center" style="background: linear-gradient(135deg, #17243A 0%, #203a63 100%); border: none;">
                    <div class="mb-3" style="width: 52px; height: 52px; border-radius: 50%; background: rgba(250, 116, 54, 0.2); color: #FA7436; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h5 class="fw-bold text-white mb-2">7-Day Free Trial</h5>
                    <p class="text-white-50 small mb-4">
                        Experience quotes, job sheets, delivery notes, and multi-branch features without any commitment.
                    </p>
                    <a href="{{ route('register') }}" class="btn-crelogics-cta w-100 py-3">
                        Create Free Account
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
