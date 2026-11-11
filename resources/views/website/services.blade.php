@extends('website.layouts.app')

@section('title', 'Services & Capabilities - ' . ($settings['site_name'] ?? 'Crelogics ERP'))

@section('content')
<!-- Services Hero Section -->
<section class="hero-section py-5 position-relative overflow-hidden" style="background: radial-gradient(circle at 10% 20%, rgba(67, 97, 238, 0.08) 0%, transparent 45%), radial-gradient(circle at 90% 80%, rgba(0, 184, 148, 0.06) 0%, transparent 45%), #FFFFFF;">
    <div class="container py-4 text-center">
        <span class="badge-pill-soft mb-3">
            <i class="fas fa-tools text-primary me-1"></i> Capabilities & Solutions
        </span>
        <h1 class="section-title text-dark fw-bold mb-3">
            Tailored Enterprise & <span class="gradient-text-primary">Commercial Services</span>
        </h1>
        <p class="section-subtitle text-muted mx-auto" style="max-width: 720px;">
            Explore our end-to-end commercial printing, signage fabrication, and cloud ERP solutions engineered to power growing businesses.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4">
            <a href="{{ url('/products') }}" class="btn-erp-primary">
                <i class="fas fa-boxes me-2"></i> Browse Products Catalog
            </a>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? '996598500143') }}" target="_blank" class="btn-erp-secondary">
                <i class="fab fa-whatsapp text-success me-2"></i> Talk to a Specialist
            </a>
        </div>
    </div>
</section>

<!-- Dynamic Industry Solutions Section -->
<section class="py-5" style="background-color: #F8FAFC;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag">Target Industries</span>
            <h2 class="section-title">Industry Solutions We Specialize In</h2>
            <p class="section-subtitle">Turnkey services tailored to unique operational demands across retail, advertising, fabrication, and multi-branch networks.</p>
        </div>

        <div class="row g-4">
            @forelse($solutions as $solution)
                <div class="col-lg-6 col-md-6">
                    <div class="solution-card h-100 p-4 p-lg-5 rounded-4 bg-white shadow-sm border position-relative" style="transition: all 0.25s ease;">
                        <span class="badge-soft badge-soft-primary mb-3">{{ $solution->tag }}</span>
                        <h3 class="fw-bold text-dark fs-4 mb-3">{{ $solution->title }}</h3>
                        <p class="text-muted mb-4" style="line-height: 1.6;">{{ $solution->description }}</p>

                        @if(is_array($solution->checklist) && count($solution->checklist) > 0)
                            <div class="p-3 rounded-3 mb-4" style="background: #F8FAFC; border: 1px solid #EDF2F7;">
                                <h6 class="fw-bold text-dark mb-2 small text-uppercase" style="letter-spacing: 0.05em;">Key Capabilities Included:</h6>
                                <ul class="list-unstyled mb-0 d-flex flex-column gap-2 small text-muted">
                                    @foreach($solution->checklist as $item)
                                        <li class="d-flex align-items-center">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <span>{{ $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                            <span class="text-muted small"><i class="fas fa-shield-alt text-primary me-1"></i> Full Support & Installation</span>
                            <a href="{{ url('/#contact') }}" class="btn-erp-secondary btn-sm px-3 py-2">
                                <span>Inquire Solution &rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 text-muted">
                    <p>No industry solutions published yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Dynamic Professional Services Catalog (From Database Products where type = service) -->
@if($services->count() > 0)
<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag">Direct Services</span>
            <h2 class="section-title">Specialized Production & Installation Services</h2>
            <p class="section-subtitle">On-demand technical services provided by our certified production and field engineering teams.</p>
        </div>

        <div class="row g-4">
            @foreach($services as $srv)
                <div class="col-lg-6">
                    <div class="card border rounded-4 h-100 shadow-sm overflow-hidden" style="transition: transform 0.2s ease;">
                        @if(!empty($srv->image) && file_exists(public_path($srv->image)))
                            <div style="height: 180px; overflow: hidden; background: #F8FAFC; border-bottom: 1px solid #EDF2F7;">
                                <img src="{{ asset($srv->image) }}" alt="{{ $srv->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @endif
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge-soft badge-soft-info">{{ $srv->category->name ?? 'Professional Service' }}</span>
                                <span class="badge bg-light text-muted border font-monospace extra-small" style="font-size: 0.72rem;">{{ $srv->sku }}</span>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">{{ $srv->name }}</h4>
                            <p class="text-muted mb-4" style="line-height: 1.6;">{{ $srv->description ?: 'Full professional service execution including scoping, execution, quality validation, and post-delivery support.' }}</p>

                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted extra-small d-block" style="font-size: 0.75rem;">Standard Rate:</span>
                                    <strong class="text-primary fs-5">{{ format_currency($srv->sale_price ?: $srv->price) }}</strong>
                                    <span class="text-muted small">/ {{ $srv->unit->name ?? 'Job' }}</span>
                                </div>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? '996598500143') }}?text={{ urlencode('Hello, I am inquiring about ' . $srv->name . ' (SKU: ' . $srv->sku . ')') }}" target="_blank" class="btn-erp-primary btn-sm px-3 py-2">
                                    <i class="fab fa-whatsapp me-1"></i> Book Service
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- How We Deliver (4-Step Process) -->
<section class="py-5" style="background-color: #F8FAFC;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-tag">Quality Process</span>
            <h2 class="section-title">How Every Service Project Is Delivered</h2>
            <p class="section-subtitle">A seamless four-stage process ensuring precision tolerances, on-time delivery, and total transparency.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-sm-6">
                <div class="p-4 rounded-4 bg-white border text-center h-100 shadow-sm">
                    <div class="p-3 rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; font-weight: 800; font-size: 1.25rem;">
                        1
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Scope & Survey</h5>
                    <p class="text-muted small mb-0">Site measurements, engineering review, and material feasibility check.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="p-4 rounded-4 bg-white border text-center h-100 shadow-sm">
                    <div class="p-3 rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; font-weight: 800; font-size: 1.25rem; background: #00B894;">
                        2
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Quotation & Proof</h5>
                    <p class="text-muted small mb-0">Transparent dimensional pricing formula, 3D proof mockup, and approval.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="p-4 rounded-4 bg-white border text-center h-100 shadow-sm">
                    <div class="p-3 rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; font-weight: 800; font-size: 1.25rem; background: #F59E0B;">
                        3
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Production</h5>
                    <p class="text-muted small mb-0">Fabrication, CNC routing, UV solvent printing, and electronic assembly.</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="p-4 rounded-4 bg-white border text-center h-100 shadow-sm">
                    <div class="p-3 rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px; font-weight: 800; font-size: 1.25rem; background: #17243A;">
                        4
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Handover</h5>
                    <p class="text-muted small mb-0">On-site crane/scaffolding installation, client sign-off, and warranty.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="cta-section py-5 position-relative text-white overflow-hidden" style="background: linear-gradient(135deg, #17243A 0%, #1e3fae 50%, #4361EE 100%);">
    <div class="container text-center py-3 position-relative" style="z-index: 2;">
        <h2 class="fw-bold fs-2 mb-3">Have a Custom Project or Requirement?</h2>
        <p class="mx-auto mb-4" style="max-width: 600px; opacity: 0.9;">
            Get an instant quotation tailored to your exact measurements, artwork, and installation timeframe.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ url('/#contact') }}" class="btn-erp-primary" style="background: #00B894; border-color: #00B894;">
                <i class="fas fa-envelope me-2"></i> Submit Inquiry
            </a>
            <a href="{{ url('/') }}" class="btn-erp-secondary" style="background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.3); color: #FFF;">
                <i class="fas fa-home me-2"></i> Return to Homepage
            </a>
        </div>
    </div>
</section>
@endsection

