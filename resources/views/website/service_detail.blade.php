@extends('website.layouts.app')

@section('title', ($service->meta_title ?: $service->name) . ' - ' . ($settings['site_name'] ?? 'Crelogics'))
@section('meta_description', $service->meta_description ?: Str::limit(strip_tags($service->description), 160))

@section('content')
<!-- CreLogics Logo Theme Vibrant Waves Hero Banner (Orange #FA7436 & Blue #0A6FB7) -->
<section class="service-crelogics-hero position-relative text-center overflow-hidden">
    <div class="service-hero-shapes">
        <div class="shape-blob shape-blob-1"></div>
        <div class="shape-blob shape-blob-2"></div>
        <div class="shape-blob shape-blob-3"></div>
        <div class="shape-wave-overlay"></div>
    </div>
    <div class="container position-relative py-5" style="z-index: 3;">
        <h1 class="service-crelogics-title fw-bold text-white mb-0">
            {{ $service->name }}
        </h1>
    </div>
</section>

<!-- "BEST FOR YOU" Main Split Section (100% Dynamic & Logo CSS Themed) -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="row align-items-center g-5">
            <!-- Left Column: Dynamic Image / Illustration Card -->
            <div class="col-lg-6">
                <div class="service-illustration-wrapper p-4 p-md-5 rounded-4 shadow-sm position-relative overflow-hidden" style="background: linear-gradient(135deg, #F0F7FC 0%, #E8F3FA 50%, #FFF8F4 100%); border: 1px solid #D6E8F7;">
                    <!-- Dynamic verified chip -->
                    <div class="service-mini-chip bg-white shadow-sm rounded-pill px-3 py-1 position-absolute top-0 end-0 m-4 d-inline-flex align-items-center gap-2 extra-small text-dark fw-bold" style="font-size: 0.75rem; border: 1px solid #D6E8F7; z-index: 2;">
                        <span class="rounded-circle" style="width: 8px; height: 8px; background: #FA7436;"></span>
                        <span>{{ $service->badge_text ?: 'Verified Quality' }}</span>
                    </div>

                    <div class="text-center py-2">
                        @if(!empty($service->image) && file_exists(public_path($service->image)))
                            <img src="{{ asset($service->image) }}" alt="{{ $service->name }}" class="img-fluid rounded-3 shadow-sm" style="max-height: 330px; object-fit: contain;">
                        @else
                            <!-- Dynamic CreLogics Logo Styled SVG Graphic -->
                            <svg class="img-fluid" viewBox="0 0 540 360" fill="none" xmlns="http://www.w3.org/2000/svg" style="max-height: 330px; width: 100%;">
                                <!-- Main Window -->
                                <rect x="20" y="30" width="360" height="270" rx="16" fill="#FFFFFF" stroke="#D0E3F3" stroke-width="2"/>
                                <rect x="20" y="30" width="360" height="38" rx="16" fill="#F8FAFC"/>
                                <rect x="20" y="52" width="360" height="16" fill="#F8FAFC"/>
                                <line x1="20" y1="68" x2="380" y2="68" stroke="#EDF2F7" stroke-width="1.5"/>
                                <circle cx="44" cy="49" r="5" fill="#FA7436"/>
                                <circle cx="60" cy="49" r="5" fill="#F59E0B"/>
                                <circle cx="76" cy="49" r="5" fill="#0A6FB7"/>
                                <rect x="100" y="44" width="120" height="10" rx="5" fill="#E2E8F0"/>

                                <!-- Left Widget: Logo Color Donut Chart -->
                                <rect x="42" y="88" width="150" height="95" rx="12" fill="#F8FAFC" stroke="#EDF2F7"/>
                                <circle cx="95" cy="135" r="32" stroke="#E2E8F0" stroke-width="10" fill="none"/>
                                <circle cx="95" cy="135" r="32" stroke="#0A6FB7" stroke-width="10" stroke-dasharray="140" stroke-dashoffset="40" fill="none"/>
                                <circle cx="95" cy="135" r="32" stroke="#FA7436" stroke-width="10" stroke-dasharray="80" stroke-dashoffset="120" fill="none"/>
                                <rect x="135" y="115" width="45" height="7" rx="3" fill="#0A6FB7"/>
                                <rect x="135" y="130" width="35" height="7" rx="3" fill="#FA7436"/>
                                <rect x="135" y="145" width="40" height="7" rx="3" fill="#17243A"/>

                                <!-- Right Widget: Logo Color Bar Chart -->
                                <rect x="206" y="88" width="154" height="95" rx="12" fill="#F8FAFC" stroke="#EDF2F7"/>
                                <rect x="226" y="138" width="16" height="32" rx="4" fill="#0A6FB7"/>
                                <rect x="250" y="115" width="16" height="55" rx="4" fill="#FA7436"/>
                                <rect x="274" y="100" width="16" height="70" rx="4" fill="#0A6FB7"/>
                                <rect x="298" y="125" width="16" height="45" rx="4" fill="#FA7436"/>
                                <rect x="322" y="108" width="16" height="62" rx="4" fill="#17243A"/>
                                <line x1="220" y1="172" x2="346" y2="172" stroke="#CBD5E1" stroke-width="1.5"/>

                                <!-- Lower Line Graph Wave -->
                                <rect x="42" y="196" width="318" height="88" rx="12" fill="#F8FAFC" stroke="#EDF2F7"/>
                                <path d="M56 260 C 100 220, 140 250, 190 225 C 240 200, 280 235, 340 210" fill="none" stroke="#0A6FB7" stroke-width="3" stroke-linecap="round"/>
                                <path d="M56 260 C 100 220, 140 250, 190 225 C 240 200, 280 235, 340 210 L 340 274 L 56 274 Z" fill="url(#creBlueWaveGrad)" opacity="0.18"/>
                                <circle cx="190" cy="225" r="5" fill="#FA7436" stroke="#FFFFFF" stroke-width="2"/>
                                <circle cx="340" cy="210" r="5" fill="#0A6FB7" stroke="#FFFFFF" stroke-width="2"/>

                                <!-- Layered Perspective Window -->
                                <rect x="290" y="60" width="180" height="150" rx="14" fill="#FFFFFF" stroke="#0A6FB7" stroke-width="1.5" filter="drop-shadow(0 10px 20px rgba(10,111,183,0.12))"/>
                                <rect x="290" y="60" width="180" height="28" rx="14" fill="#17243A"/>
                                <rect x="290" y="74" width="180" height="14" fill="#17243A"/>
                                <circle cx="308" cy="74" r="3.5" fill="#FA7436"/>
                                <circle cx="320" cy="74" r="3.5" fill="#F59E0B"/>
                                <circle cx="332" cy="74" r="3.5" fill="#0A6FB7"/>
                                <rect x="306" y="102" width="90" height="8" rx="4" fill="#0A6FB7"/>
                                <rect x="306" y="118" width="130" height="8" rx="4" fill="#E2E8F0"/>
                                <rect x="306" y="134" width="110" height="8" rx="4" fill="#FA7436"/>
                                <rect x="306" y="150" width="140" height="8" rx="4" fill="#E2E8F0"/>
                                <rect x="306" y="166" width="70" height="8" rx="4" fill="#00B894"/>

                                <!-- Floating Badge in Logo Colors -->
                                <rect x="330" y="240" width="170" height="60" rx="12" fill="#FFFFFF" stroke="#FA7436" stroke-width="1.5" filter="drop-shadow(0 8px 16px rgba(250,116,54,0.18))"/>
                                <circle cx="360" cy="270" r="16" fill="#FFF7ED"/>
                                <path d="M354 270 L358 274 L367 265" stroke="#FA7436" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <rect x="386" y="260" width="95" height="9" rx="4.5" fill="#17243A"/>
                                <rect x="386" y="274" width="60" height="7" rx="3.5" fill="#0A6FB7"/>

                                <defs>
                                    <linearGradient id="creBlueWaveGrad" x1="198" y1="210" x2="198" y2="274" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#0A6FB7"/>
                                        <stop offset="1" stop-color="#0A6FB7" stop-opacity="0"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: 100% Dynamic Text & Feature Highlight Boxes -->
            <div class="col-lg-6">
                <span class="text-uppercase fw-bold small d-block mb-2" style="letter-spacing: 0.14em; font-size: 0.85rem; color: #FA7436;">
                    {{ $service->sub_tag ?: 'BEST FOR YOU' }}
                </span>

                <h2 class="fw-bold text-dark mb-3" style="font-size: clamp(1.8rem, 2.6vw, 2.45rem); line-height: 1.25; letter-spacing: -0.01em;">
                    {{ $service->headline ?: 'Boosts Your Business Traffic & Revenue!' }}
                </h2>

                <p class="text-muted mb-4" style="font-size: 1.05rem; line-height: 1.7;">
                    {{ $service->description ?: 'Drives measurable business growth through improved visibility, sharper targeting, superior design aesthetics, and consistent performance optimization.' }}
                </p>

                <!-- Two Side-by-Side Highlight Feature Boxes (100% Dynamic with Logo Colors) -->
                <div class="row g-3 mb-4">
                    <!-- Feature Box 1 -->
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 text-center border h-100 bg-white highlight-box-hover" style="border-color: #E2E8F0 !important; transition: all 0.25s ease;">
                            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-3 mb-2" style="background: #FFF7ED; border: 1px solid rgba(250, 116, 54, 0.25); width: 62px; height: 62px;">
                                <i class="{{ $service->feature_1_icon ?: 'fas fa-bullhorn' }} fa-lg" style="color: #FA7436;"></i>
                            </div>
                            <h5 class="fw-bold text-dark fs-6 mb-1">{{ $service->feature_1_title ?: 'Actionable Audit' }}</h5>
                            <span class="text-muted extra-small d-block" style="font-size: 0.78rem;">{{ $service->feature_1_text ?: 'Detailed scoping & technical roadmap' }}</span>
                        </div>
                    </div>

                    <!-- Feature Box 2 -->
                    <div class="col-sm-6">
                        <div class="p-3 rounded-3 text-center border h-100 bg-white highlight-box-hover" style="border-color: #E2E8F0 !important; transition: all 0.25s ease;">
                            <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-3 mb-2" style="background: #F0F9FF; border: 1px solid rgba(10, 111, 183, 0.25); width: 62px; height: 62px;">
                                <i class="{{ $service->feature_2_icon ?: 'fas fa-chart-pie' }} fa-lg" style="color: #0A6FB7;"></i>
                            </div>
                            <h5 class="fw-bold text-dark fs-6 mb-1">{{ $service->feature_2_title ?: 'What We Charge' }}</h5>
                            <span class="fw-bold extra-small d-block" style="font-size: 0.85rem; color: #0A6FB7;">
                                {{ $service->feature_2_text ?: ($service->starting_price ? 'From ' . format_currency($service->starting_price) . ' / ' . ($service->price_unit ?: 'Project') : 'Custom Itemized Quote') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Action CTAs (Logo Colors: Orange #FA7436 & WhatsApp #25D366) -->
                @php
                    $waNum = preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? '966557834154');
                    $waText = 'Hello Crelogics, I am interested in ' . $service->name . ' (' . url('/service/' . $service->slug) . ')';
                @endphp
                <div class="d-flex flex-wrap gap-3">
                    <button type="button" class="btn fw-semibold px-4 py-2 text-white d-inline-flex align-items-center btn-logo-orange" data-bs-toggle="modal" data-bs-target="#proposalModal">
                        <i class="fas fa-file-signature me-2"></i> Get Free Proposal
                    </button>
                    <a href="https://wa.me/{{ $waNum }}?text={{ urlencode($waText) }}" target="_blank" class="btn fw-semibold px-4 py-2 text-white d-inline-flex align-items-center btn-logo-wa">
                        <i class="fab fa-whatsapp me-2" style="font-size: 1.15rem;"></i> WhatsApp Specialist
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Dynamic Key Deliverables & Scope Checklist Section -->
@if(is_array($service->features) && count($service->features) > 0)
<section class="py-5" style="background-color: #F8FAFC; border-top: 1px solid #EDF2F7; border-bottom: 1px solid #EDF2F7;">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="badge px-3 py-1 text-uppercase fw-bold mb-2" style="background: rgba(10, 111, 183, 0.08); color: #0A6FB7; font-size: 0.75rem; letter-spacing: 0.08em; border-radius: 4px;">Detailed Scope</span>
            <h2 class="fw-bold text-dark fs-2 mb-2">Key Deliverables Included in {{ $service->name }}</h2>
            <p class="text-muted mx-auto" style="max-width: 650px;">
                Every client project includes end-to-end execution, quality controls, and source handover with zero hidden fees.
            </p>
        </div>

        <div class="row g-4">
            @foreach($service->features as $item)
                <div class="col-lg-3 col-md-6">
                    <div class="p-4 rounded-4 bg-white border h-100 shadow-sm d-flex flex-column" style="border-color: #E2E8F0 !important; transition: transform 0.2s ease;">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 44px; height: 44px; background: rgba(10, 111, 183, 0.12); color: #0A6FB7; font-size: 1.1rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h5 class="fw-bold text-dark fs-6 mb-2">{{ $item }}</h5>
                        <p class="text-muted extra-small mb-0 mt-auto" style="line-height: 1.5;">Executed by senior specialists strictly adhering to corporate brand standards.</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Custom Body Content / Strategic Overview -->
@if(!empty($service->content))
<section class="py-5 bg-white">
    <div class="container py-2">
        <div class="p-4 p-md-5 rounded-4 border" style="background: linear-gradient(180deg, #FDFDFE 0%, #F8FAFC 100%); border-color: #E2E8F0 !important;">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge px-3 py-1 text-uppercase fw-bold" style="background: rgba(10, 111, 183, 0.08); color: #0A6FB7; font-size: 0.75rem; letter-spacing: 0.08em; border-radius: 4px;">Service Deep Dive</span>
            </div>
            <h3 class="fw-bold text-dark fs-3 mb-3">Overview & Core Strategic Value</h3>
            <div class="service-content-body text-secondary" style="font-size: 1.02rem; line-height: 1.8;">
                {!! $service->content !!}
            </div>
        </div>
    </div>
</section>
@endif

<!-- Dynamic 4-Step Execution Workflow Section -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="badge px-3 py-1 text-uppercase fw-bold mb-2" style="background: rgba(250, 116, 54, 0.1); color: #FA7436; font-size: 0.75rem; letter-spacing: 0.08em; border-radius: 4px;">Methodology</span>
            <h2 class="fw-bold text-dark fs-2 mb-2">Our 4-Stage Project Lifecycle</h2>
            <p class="text-muted mx-auto" style="max-width: 650px;">
                A transparent, agile execution roadmap designed specifically for {{ $service->name }} to ensure seamless milestones and guaranteed outcomes.
            </p>
        </div>

        <div class="row g-4">
            @php
                $steps = is_array($service->process_steps) && count($service->process_steps) > 0 
                    ? $service->process_steps 
                    : [
                        ['title' => 'Discovery & Strategy', 'desc' => 'Detailed review of business goals, technical requirements, target audience, and timeline milestones.'],
                        ['title' => 'Proofing & Mockups', 'desc' => 'Interactive prototypes, wireframes, or 3D architectural mockups for client feedback and sign-off.'],
                        ['title' => 'Agile Production', 'desc' => 'Precision coding, CNC fabrication, or digital marketing execution with rigorous internal QA testing.'],
                        ['title' => 'Deployment & Support', 'desc' => 'Final delivery, on-site setup, full source file IP handover, and ongoing 24/7 post-launch warranty.']
                    ];
                $stepColors = ['#0A6FB7', '#00B894', '#FA7436', '#17243A'];
            @endphp
            @foreach($steps as $idx => $step)
                <div class="col-lg-3 col-sm-6">
                    <div class="p-4 rounded-4 bg-white border text-center h-100 shadow-sm" style="border-color: #E2E8F0 !important; transition: transform 0.2s ease;">
                        <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-3 fw-bold" style="width: 48px; height: 48px; font-size: 1.15rem; background: {{ $stepColors[$idx % count($stepColors)] }};">
                            {{ $idx + 1 }}
                        </div>
                        <h5 class="fw-bold text-dark fs-6 mb-2">{{ is_array($step) ? ($step['title'] ?? '') : $step }}</h5>
                        <p class="text-muted small mb-0" style="line-height: 1.5;">{{ is_array($step) ? ($step['desc'] ?? '') : '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Related Services in Same Department -->
@if(isset($relatedServices) && $relatedServices->count() > 0)
<section class="py-5" style="background-color: #F8FAFC; border-top: 1px solid #EDF2F7;">
    <div class="container py-2">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
            <div>
                <span class="badge px-3 py-1 text-uppercase fw-bold mb-2" style="background: rgba(10, 111, 183, 0.08); color: #0A6FB7; font-size: 0.75rem; letter-spacing: 0.08em; border-radius: 4px;">Explore More</span>
                <h3 class="fw-bold text-dark fs-3 mb-0">Related {{ $service->category }} Services</h3>
            </div>
            <a href="{{ url('/services?category=' . $service->category) }}" class="btn btn-sm btn-outline-primary fw-semibold rounded-pill px-3 py-2 mt-2 mt-sm-0" style="border-color: #0A6FB7; color: #0A6FB7;">
                <span>View All {{ $service->category }}</span> &rarr;
            </a>
        </div>

        <div class="row g-4">
            @foreach($relatedServices->take(4) as $rel)
                <div class="col-lg-3 col-sm-6">
                    <a href="{{ route('website.service_detail', $rel->slug) }}" class="card border rounded-4 h-100 shadow-sm p-4 text-decoration-none bg-white related-card-hover" style="border-color: #E2E8F0 !important; transition: all 0.25s ease;">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3" style="width: 44px; height: 44px; background: rgba(10, 111, 183, 0.08); color: #0A6FB7; font-size: 1.1rem;">
                            <i class="{{ $rel->icon ?: 'fas fa-concierge-bell' }}"></i>
                        </div>
                        <h5 class="fw-bold text-dark fs-6 mb-2">{{ $rel->name }}</h5>
                        <p class="text-muted extra-small mb-3" style="line-height: 1.5;">{{ Str::limit($rel->description, 70) }}</p>
                        <div class="mt-auto d-flex align-items-center justify-content-between pt-2 border-top text-primary small fw-semibold" style="color: #0A6FB7 !important;">
                            <span>Explore Page</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Dynamic Logo Theme Call to Action Banner (Navy #17243A to Blue #0A6FB7 to Orange #FA7436) -->
<section class="py-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #17243A 0%, #0A6FB7 50%, #FA7436 100%);">
    <div class="container text-center py-4 position-relative" style="z-index: 2;">
        <h2 class="fw-bold fs-2 mb-3">{{ $service->cta_title ?: ('Ready to Accelerate with ' . $service->name . '?') }}</h2>
        <p class="mx-auto mb-4" style="max-width: 650px; opacity: 0.95; font-size: 1.05rem;">
            {{ $service->cta_subtitle ?: 'Contact Crelogics today to discuss your vision, review technical requirements, and receive an itemized proposal within 24 hours.' }}
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <button type="button" class="btn fw-semibold px-4 py-2 text-white btn-logo-orange shadow" data-bs-toggle="modal" data-bs-target="#proposalModal">
                <i class="fas fa-file-signature me-2"></i> Request Custom Proposal
            </button>
            <a href="https://wa.me/{{ $waNum }}?text={{ urlencode($waText) }}" target="_blank" class="btn fw-semibold px-4 py-2 btn-logo-wa">
                <i class="fab fa-whatsapp me-2" style="font-size: 1.1rem;"></i> Chat with Specialist
            </a>
            <a href="{{ url('/services') }}" class="btn fw-semibold px-4 py-2 text-white" style="background: rgba(255,255,255,0.18); border: 1px solid rgba(255,255,255,0.35); border-radius: 8px;">
                <i class="fas fa-th-large me-2"></i> All Services
            </a>
        </div>
    </div>
</section>

<style>
/* CreLogics Logo Theme Hero Banner (Blue #0A6FB7, Navy #17243A, Orange #FA7436) */
.service-crelogics-hero {
    background: linear-gradient(135deg, #17243A 0%, #0A6FB7 60%, #FA7436 100%);
    padding: 75px 20px 85px 20px;
    position: relative;
}

.service-hero-shapes {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    pointer-events: none;
}

.shape-blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(45px);
    opacity: 0.45;
}

.shape-blob-1 {
    width: 380px;
    height: 380px;
    background: #0A6FB7;
    top: -90px;
    left: 10%;
}

.shape-blob-2 {
    width: 440px;
    height: 440px;
    background: #FA7436;
    bottom: -120px;
    right: 10%;
}

.shape-blob-3 {
    width: 260px;
    height: 260px;
    background: #FF8300;
    top: 15%;
    right: 35%;
    opacity: 0.3;
}

.shape-wave-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 50% 50%, transparent 40%, rgba(23, 36, 58, 0.45) 100%);
}

.service-crelogics-title {
    font-size: clamp(2rem, 3.6vw, 2.9rem);
    letter-spacing: -0.01em;
}

/* CreLogics Logo Buttons */
.btn-logo-orange {
    background: #FA7436 !important;
    border: 1px solid #FA7436 !important;
    border-radius: 8px !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 14px rgba(250, 116, 54, 0.35) !important;
    transition: all 0.2s ease !important;
}
.btn-logo-orange:hover {
    background: #e05f24 !important;
    border-color: #e05f24 !important;
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(250, 116, 54, 0.45) !important;
}

.btn-logo-blue {
    background: #0A6FB7 !important;
    border: 1px solid #0A6FB7 !important;
    border-radius: 8px !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 14px rgba(10, 111, 183, 0.35) !important;
    transition: all 0.2s ease !important;
}
.btn-logo-blue:hover {
    background: #085a96 !important;
    border-color: #085a96 !important;
    transform: translateY(-2px);
}

.btn-logo-wa {
    background: #25D366 !important;
    border: 1px solid #25D366 !important;
    border-radius: 8px !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 14px rgba(37, 211, 102, 0.25) !important;
    transition: all 0.2s ease !important;
}
.btn-logo-wa:hover {
    background: #1eb956 !important;
    transform: translateY(-2px);
}

.highlight-box-hover:hover {
    transform: translateY(-3px);
    border-color: #FA7436 !important;
    box-shadow: 0 8px 20px rgba(250, 116, 54, 0.12) !important;
}

.related-card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 14px 30px rgba(10, 111, 183, 0.12) !important;
    border-color: #0A6FB7 !important;
}
</style>
@endsection
