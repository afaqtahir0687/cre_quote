@extends('website.layouts.app')

@section('title', 'Services & Capabilities - ' . ($settings['site_name'] ?? 'Crelogics ERP'))

@section('content')
<!-- Services Hero Section -->
<section class="hero-section py-5 position-relative overflow-hidden" style="background: radial-gradient(circle at 10% 20%, rgba(10, 111, 183, 0.08) 0%, transparent 45%), radial-gradient(circle at 90% 80%, rgba(250, 116, 54, 0.07) 0%, transparent 45%), #FFFFFF;">
    <div class="container py-4 text-center">
        <span class="badge-pill-soft mb-3" style="background: rgba(10, 111, 183, 0.08); color: #0A6FB7; border: 1px solid rgba(10, 111, 183, 0.18); font-weight: 600; padding: 6px 16px; border-radius: 50px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-layer-group text-primary"></i> Real-World Capabilities & Digital Solutions
        </span>
        <h1 class="section-title text-dark fw-bold mb-3" style="font-size: clamp(2rem, 3.5vw, 2.75rem); letter-spacing: -0.02em;">
            Creativity, Marketing & <span style="background: linear-gradient(135deg, #0A6FB7 0%, #FA7436 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Advertising Solutions</span>
        </h1>
        <p class="section-subtitle text-muted mx-auto" style="max-width: 760px; font-size: 1.1rem; line-height: 1.7;">
            From UI/UX web development and cyber security to high-converting SEO, digital marketing, commercial printing, and 3D architectural signage — Crelogics powers your business end-to-end.
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
            <a href="#services-catalog" class="btn-erp-primary" style="background: #FA7436; border-color: #FA7436; color: #FFF; font-weight: 600; padding: 10px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; box-shadow: 0 4px 14px rgba(250, 116, 54, 0.25);">
                <i class="fas fa-th-large me-2"></i> Browse All Services
            </a>
            <button type="button" class="btn-erp-secondary" data-bs-toggle="modal" data-bs-target="#proposalModal" style="background: #0A6FB7; border-color: #0A6FB7; color: #FFF; font-weight: 600; padding: 10px 24px; border-radius: 8px; border: none; display: inline-flex; align-items: center; box-shadow: 0 4px 14px rgba(10, 111, 183, 0.25);">
                <i class="fas fa-file-invoice me-2"></i> Request Custom Proposal
            </button>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? '966557834154') }}?text={{ urlencode('Hello Crelogics, I would like to inquire about your professional services.') }}" target="_blank" class="btn-erp-secondary" style="background: #FFFFFF; border: 1px solid #25D366; color: #25D366; font-weight: 600; padding: 10px 24px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center;">
                <i class="fab fa-whatsapp me-2" style="font-size: 1.2rem;"></i> WhatsApp Specialist
            </a>
        </div>
    </div>
</section>

<!-- Dedicated Services Catalog (From services table) -->
<section class="py-5 bg-white" id="services-catalog">
    <div class="container">
        <div class="text-center mb-4">
            <span class="badge px-3 py-1 text-uppercase fw-bold mb-2" style="background: rgba(10, 111, 183, 0.08); color: #0A6FB7; font-size: 0.75rem; letter-spacing: 0.08em; border-radius: 4px;">Service Domains</span>
            <h2 class="fw-bold text-dark fs-2 mb-2">Explore Our Full Service Catalog</h2>
            <p class="text-muted mx-auto" style="max-width: 650px;">
                Filter by department to explore detailed deliverables, technical stacks, and turnkey execution scopes.
            </p>
        </div>

        <!-- Department Filter Tabs -->
        @php
            $activeCategory = request('category', 'all');
            $creativityCount = $services->where('category', 'Creativity')->count();
            $marketingCount = $services->where('category', 'Marketing')->count();
            $advertisingCount = $services->where('category', 'Advertising')->count();
        @endphp
        <div class="d-flex flex-wrap justify-content-center gap-2 mb-5" id="servicesFilterTabs">
            <button type="button" class="btn btn-sm px-4 py-2 rounded-pill fw-semibold filter-tab-btn {{ $activeCategory === 'all' ? 'active' : '' }}" data-filter="all">
                <i class="fas fa-th-large me-1"></i> All Services ({{ $services->count() }})
            </button>
            <button type="button" class="btn btn-sm px-4 py-2 rounded-pill fw-semibold filter-tab-btn {{ $activeCategory === 'Creativity' ? 'active' : '' }}" data-filter="Creativity">
                <i class="fas fa-paint-brush me-1 text-primary"></i> Creativity ({{ $creativityCount }})
            </button>
            <button type="button" class="btn btn-sm px-4 py-2 rounded-pill fw-semibold filter-tab-btn {{ $activeCategory === 'Marketing' ? 'active' : '' }}" data-filter="Marketing">
                <i class="fas fa-chart-line me-1 text-success"></i> Marketing ({{ $marketingCount }})
            </button>
            <button type="button" class="btn btn-sm px-4 py-2 rounded-pill fw-semibold filter-tab-btn {{ $activeCategory === 'Advertising' ? 'active' : '' }}" data-filter="Advertising">
                <i class="fas fa-bullhorn me-1" style="color: #FA7436;"></i> Advertising ({{ $advertisingCount }})
            </button>
        </div>

        <div class="row g-4" id="servicesGrid">
            @forelse($services as $srv)
                @php
                    $waNumber = preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? '966557834154');
                    $waMsg = 'Hello Crelogics, I would like to inquire about: ' . $srv->name . ' (' . $srv->category . ')';
                @endphp
                <div class="col-lg-6 service-item" data-category="{{ $srv->category }}" id="{{ $srv->slug }}">
                    <div class="card border rounded-4 h-100 shadow-sm overflow-hidden service-card-hover" style="transition: all 0.25s ease; border-color: #E2E8F0 !important;">
                        <div class="p-4 p-md-5 d-flex flex-column flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                @if($srv->category === 'Creativity')
                                    <span class="badge px-3 py-1 rounded-pill" style="background: rgba(10, 111, 183, 0.1); color: #0A6FB7; font-weight: 600; font-size: 0.8rem;">
                                        <i class="fas fa-paint-brush me-1"></i> Creativity
                                    </span>
                                @elseif($srv->category === 'Marketing')
                                    <span class="badge px-3 py-1 rounded-pill" style="background: rgba(0, 184, 148, 0.1); color: #00B894; font-weight: 600; font-size: 0.8rem;">
                                        <i class="fas fa-chart-line me-1"></i> Marketing
                                    </span>
                                @else
                                    <span class="badge px-3 py-1 rounded-pill" style="background: rgba(250, 116, 54, 0.1); color: #FA7436; font-weight: 600; font-size: 0.8rem;">
                                        <i class="fas fa-bullhorn me-1"></i> Advertising
                                    </span>
                                @endif

                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px; background: linear-gradient(135deg, #0A6FB7, #FA7436); font-size: 1rem;">
                                    <i class="{{ $srv->icon ?: 'fas fa-concierge-bell' }}"></i>
                                </div>
                            </div>

                            <h3 class="fw-bold text-dark fs-4 mb-2" style="line-height: 1.35;">
                                <a href="{{ route('website.service_detail', $srv->slug) }}" class="text-dark text-decoration-none service-title-hover">
                                    {{ $srv->name }}
                                </a>
                            </h3>
                            <p class="text-muted mb-4" style="line-height: 1.65; font-size: 0.95rem;">
                                {{ $srv->description ?: 'Full commercial service execution including scoping, production, quality assurance, and on-time fulfillment.' }}
                            </p>

                            @if(is_array($srv->features) && count($srv->features) > 0)
                                <div class="p-3 rounded-3 mb-4" style="background: #F8FAFC; border: 1px solid #EDF2F7;">
                                    <h6 class="fw-bold text-dark mb-2 small text-uppercase" style="letter-spacing: 0.05em; font-size: 0.75rem;">
                                        <i class="fas fa-check-double text-success me-1"></i> Key Deliverables & Scope:
                                    </h6>
                                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small text-muted">
                                        @foreach($srv->features as $item)
                                            <li class="d-flex align-items-start">
                                                <i class="fas fa-check-circle text-success me-2 mt-1" style="font-size: 0.85rem;"></i>
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="mt-auto pt-3 border-top d-flex flex-wrap justify-content-between align-items-center gap-3">
                                <div>
                                    <span class="text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em;">Starting Rate</span>
                                    <div class="d-flex align-items-baseline gap-1">
                                        @if($srv->starting_price)
                                            <span class="fw-bold text-dark" style="font-size: 1.35rem; color: #0A6FB7 !important;">
                                                {{ format_currency($srv->starting_price) }}
                                            </span>
                                            <span class="text-muted small">/ {{ $srv->price_unit ?: 'Project' }}</span>
                                        @else
                                            <span class="fw-bold text-dark fs-5" style="color: #0A6FB7 !important;">Custom Quote</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('website.service_detail', $srv->slug) }}" class="btn btn-sm btn-light border fw-semibold d-inline-flex align-items-center" style="border-radius: 8px; padding: 8px 12px;">
                                        <span>Details</span> <i class="fas fa-arrow-right ms-1 extra-small"></i>
                                    </a>
                                    <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode($waMsg) }}" target="_blank" class="btn btn-sm fw-semibold d-inline-flex align-items-center" style="background: #25D366; color: #FFF; border-radius: 8px; padding: 8px 12px; text-decoration: none;">
                                        <i class="fab fa-whatsapp me-1" style="font-size: 1rem;"></i> WhatsApp
                                    </a>
                                    <button type="button" class="btn btn-sm fw-semibold d-inline-flex align-items-center btn-open-proposal" data-service-name="{{ $srv->name }}" style="background: rgba(10, 111, 183, 0.1); color: #0A6FB7; border: 1px solid rgba(10, 111, 183, 0.2); border-radius: 8px; padding: 8px 12px;">
                                        <i class="fas fa-file-alt me-1"></i> Proposal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <p>No services published yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Turnkey Industry Solutions Section -->
@if(isset($solutions) && $solutions->count() > 0)
<section class="py-5" style="background-color: #F8FAFC;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge px-3 py-1 text-uppercase fw-bold mb-2" style="background: rgba(250, 116, 54, 0.1); color: #FA7436; font-size: 0.75rem; letter-spacing: 0.08em; border-radius: 4px;">Integrated Workflows</span>
            <h2 class="section-title fw-bold text-dark fs-2 mb-2">Turnkey Enterprise Solutions</h2>
            <p class="section-subtitle text-muted mx-auto" style="max-width: 700px;">
                End-to-end production pipelines engineered for multi-branch organizations, retail franchises, and corporate agencies.
            </p>
        </div>

        <div class="row g-4">
            @foreach($solutions as $solution)
                <div class="col-lg-6 col-md-6">
                    <div class="solution-card h-100 p-4 p-lg-5 rounded-4 bg-white shadow-sm border position-relative" style="transition: all 0.25s ease; border-color: #E2E8F0 !important;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge px-3 py-1 rounded-pill" style="background: rgba(10, 111, 183, 0.08); color: #0A6FB7; font-weight: 600; font-size: 0.8rem;">
                                <i class="fas fa-star me-1 text-warning"></i> {{ $solution->tag }}
                            </span>
                            @if(!empty($solution->icon_class))
                                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(250, 116, 54, 0.08); color: #FA7436;">
                                    <i class="{{ $solution->icon_class }}"></i>
                                </div>
                            @endif
                        </div>

                        <h3 class="fw-bold text-dark fs-4 mb-3">{{ $solution->title }}</h3>
                        <p class="text-muted mb-4" style="line-height: 1.65; font-size: 0.95rem;">{{ $solution->description }}</p>

                        @if(is_array($solution->checklist) && count($solution->checklist) > 0)
                            <div class="p-3 rounded-3 mb-4" style="background: #F8FAFC; border: 1px solid #EDF2F7;">
                                <h6 class="fw-bold text-dark mb-2 small text-uppercase" style="letter-spacing: 0.05em; color: #17243A !important;">
                                    <i class="fas fa-check-double text-success me-1"></i> Key Capabilities Included:
                                </h6>
                                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small text-muted">
                                    @foreach($solution->checklist as $item)
                                        <li class="d-flex align-items-start">
                                            <i class="fas fa-check-circle text-success me-2 mt-1" style="font-size: 0.85rem;"></i>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="text-muted small">
                                <i class="fas fa-shield-alt text-primary me-1"></i> Turnkey Execution & Support
                            </span>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? '966557834154') }}?text={{ urlencode('Hello Crelogics, I want to inquire about the turnkey solution: ' . $solution->title) }}" target="_blank" class="btn btn-sm fw-semibold px-3 py-2" style="background: #FA7436; color: #FFF; border-radius: 8px; text-decoration: none;">
                                <span>Inquire Solution &rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- How We Deliver (4-Step Process) -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge px-3 py-1 text-uppercase fw-bold mb-2" style="background: rgba(10, 111, 183, 0.08); color: #0A6FB7; font-size: 0.75rem; letter-spacing: 0.08em; border-radius: 4px;">Standard of Excellence</span>
            <h2 class="section-title fw-bold text-dark fs-2 mb-2">How Every Service Project Is Delivered</h2>
            <p class="section-subtitle text-muted mx-auto" style="max-width: 650px;">
                A rigorous four-stage quality lifecycle ensuring exact millimeter tolerances, vibrant colors, on-time delivery, and total transparency.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-sm-6">
                <div class="p-4 rounded-4 bg-white border text-center h-100 shadow-sm" style="border-color: #E2E8F0 !important;">
                    <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; font-weight: 800; font-size: 1.25rem; background: #0A6FB7;">
                        1
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Scope & Discovery</h5>
                    <p class="text-muted small mb-0">Project roadmap, technical specification review, architectural feasibility, and timeline agreement.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="p-4 rounded-4 bg-white border text-center h-100 shadow-sm" style="border-color: #E2E8F0 !important;">
                    <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; font-weight: 800; font-size: 1.25rem; background: #00B894;">
                        2
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Quotation & 3D Proof</h5>
                    <p class="text-muted small mb-0">Itemized pricing breakdown, UI/UX interactive wireframes or 3D mockups, and client sign-off.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="p-4 rounded-4 bg-white border text-center h-100 shadow-sm" style="border-color: #E2E8F0 !important;">
                    <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; font-weight: 800; font-size: 1.25rem; background: #FA7436;">
                        3
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Agile Production</h5>
                    <p class="text-muted small mb-0">Clean coding sprints, CNC/Laser fabrication, eco-solvent UV printing, and rigorous QA testing.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="p-4 rounded-4 bg-white border text-center h-100 shadow-sm" style="border-color: #E2E8F0 !important;">
                    <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; font-weight: 800; font-size: 1.25rem; background: #17243A;">
                        4
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Deployment & Support</h5>
                    <p class="text-muted small mb-0">Production deployment, on-site structural installation, source handover, and 24/7 post-launch warranty.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="cta-section py-5 position-relative text-white overflow-hidden" style="background: linear-gradient(135deg, #17243A 0%, #0A6FB7 60%, #FA7436 100%);">
    <div class="container text-center py-4 position-relative" style="z-index: 2;">
        <h2 class="fw-bold fs-2 mb-3">Have a Custom Project or Enterprise Requirement?</h2>
        <p class="mx-auto mb-4" style="max-width: 650px; opacity: 0.95; font-size: 1.05rem;">
            Whether you need custom web applications, cyber security audits, marketing campaigns, or nationwide physical signage rollouts — our specialists are ready to execute.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <button type="button" class="btn fw-semibold px-4 py-2 text-white" data-bs-toggle="modal" data-bs-target="#proposalModal" style="background: #FA7436; border: 1px solid #FA7436; border-radius: 8px; box-shadow: 0 4px 14px rgba(250, 116, 54, 0.4);">
                <i class="fas fa-file-signature me-2"></i> Get Instant 4-Step Proposal
            </button>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? '966557834154') }}?text={{ urlencode('Hello Crelogics, I have a custom project requirement.') }}" target="_blank" class="btn fw-semibold px-4 py-2" style="background: #25D366; color: #FFF; border-radius: 8px;">
                <i class="fab fa-whatsapp me-2" style="font-size: 1.1rem;"></i> Chat with Specialist
            </a>
            <a href="{{ url('/products') }}" class="btn fw-semibold px-4 py-2" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: #FFF; border-radius: 8px;">
                <i class="fas fa-boxes me-2"></i> View Products Catalog
            </a>
        </div>
    </div>
</section>

<style>
/* Service Card Hover & Filter Styling */
.service-card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(10, 111, 183, 0.12) !important;
    border-color: #0A6FB7 !important;
}

.filter-tab-btn {
    background: #F1F5F9;
    color: #475569;
    border: 1px solid #E2E8F0;
    transition: all 0.2s ease;
}

.filter-tab-btn:hover {
    background: #E2E8F0;
    color: #1E293B;
}

.filter-tab-btn.active {
    background: #0A6FB7 !important;
    color: #FFFFFF !important;
    border-color: #0A6FB7 !important;
    box-shadow: 0 4px 12px rgba(10, 111, 183, 0.25);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Department Filter Tab Logic
    const filterTabs = document.querySelectorAll('.filter-tab-btn');
    const serviceItems = document.querySelectorAll('.service-item');

    function applyFilter(filterValue) {
        filterTabs.forEach(t => {
            if (t.getAttribute('data-filter') === filterValue) {
                t.classList.add('active');
            } else {
                t.classList.remove('active');
            }
        });

        serviceItems.forEach(item => {
            if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }

    filterTabs.forEach(tab => {
        tab.addEventListener('click', function () {
            const filterValue = this.getAttribute('data-filter');
            applyFilter(filterValue);
        });
    });

    // Check URL params for category
    const urlParams = new URLSearchParams(window.location.search);
    const categoryParam = urlParams.get('category');
    if (categoryParam) {
        applyFilter(categoryParam);
    }

    // Handle "Proposal &rarr;" button to open modal with selected service
    const proposalBtns = document.querySelectorAll('.btn-open-proposal');
    const proposalModalEl = document.getElementById('proposalModal');

    proposalBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const serviceName = this.getAttribute('data-service-name');
            if (proposalModalEl && typeof bootstrap !== 'undefined') {
                const modal = bootstrap.Modal.getInstance(proposalModalEl) || new bootstrap.Modal(proposalModalEl);
                modal.show();

                const serviceSelect = document.getElementById('proposalServiceSelect');
                if (serviceSelect) {
                    for (let i = 0; i < serviceSelect.options.length; i++) {
                        if (serviceSelect.options[i].text.toLowerCase().includes(serviceName.toLowerCase()) ||
                            serviceName.toLowerCase().includes(serviceSelect.options[i].text.toLowerCase())) {
                            serviceSelect.selectedIndex = i;
                            break;
                        }
                    }
                }

                const cards = document.querySelectorAll('.proposal-service-card');
                cards.forEach(card => {
                    const val = card.getAttribute('data-value') || '';
                    if (val && (val.toLowerCase().includes(serviceName.toLowerCase()) || serviceName.toLowerCase().includes(val.toLowerCase()))) {
                        cards.forEach(c => c.classList.remove('selected'));
                        card.classList.add('selected');
                    }
                });
            }
        });
    });
});
</script>
@endsection
