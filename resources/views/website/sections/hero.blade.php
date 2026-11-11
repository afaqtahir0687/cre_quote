@php
    $heroSlides = (isset($slides) && $slides->count() > 0)
        ? $slides
        : \App\Models\HomepageSlide::active()->get();

    // Fallback if database table has no slides yet
    if ($heroSlides->isEmpty()) {
        $heroSlides = collect([
            (object) [
                'id' => 1,
                'title' => 'Crelogics ERP Hero',
                'badge_text' => $settings['hero_badge'] ?? 'Complete Business ERP Solution',
                'heading' => $settings['hero_headline'] ?? 'Run Your Entire Business From',
                'heading_highlight' => $settings['hero_headline_highlight'] ?? 'One Intelligent System',
                'description' => $settings['hero_subheading'] ?? 'Manage job orders, quotations, invoices, customers, products and daily operations with one connected, modern ERP designed for performance and scale.',
                'primary_btn_text' => $settings['hero_primary_btn_text'] ?? 'Open ERP Portal',
                'primary_btn_action' => 'url',
                'primary_btn_url' => $settings['hero_primary_btn_url'] ?? '/dashboard',
                'secondary_btn_text' => $settings['hero_secondary_btn_text'] ?? 'Explore Live Workflow',
                'secondary_btn_url' => $settings['hero_secondary_btn_url'] ?? '#modules',
                'display_duration' => 7000,
                'trust_bullets' => [
                    'No credit card required',
                    '7-Day full access trial',
                    'Multi-shop & branch ready',
                    'Instant PDF quotes & invoices'
                ],
                'is_active' => true,
            ]
        ]);
    }
@endphp

<!-- Dynamic Homepage Hero Section -->
<section class="hero-section position-relative overflow-hidden" id="hero">
    <div class="container position-relative" style="z-index: 3;">

        <!-- 1. TOP CAROUSEL: Headings, Text & Action Buttons ONLY -->
        <div class="hero-text-slider-wrapper position-relative mx-auto mb-4" id="heroTextSliderWrapper"
            style="max-width: 860px;">
            <div class="hero-text-slider position-relative" id="homepageHeroSlider">

                @foreach($heroSlides as $idx => $slide)
                    @php
                        $isActive = ($idx === 0);
                        $opensModal = ($slide->primary_btn_action === 'open_proposal_modal');
                        $bullets = is_array($slide->trust_bullets) ? $slide->trust_bullets : [];
                    @endphp

                    <div class="hero-text-slide {{ $isActive ? 'active' : '' }}" id="slide-{{ $slide->id }}"
                        data-slide-index="{{ $idx }}" data-duration="{{ $slide->display_duration ?: 7000 }}">

                        <div class="hero-content-col text-center mx-auto">
                            <!-- Eyebrow Badge -->
                            @if(!empty($slide->badge_text))
                                <div class="mb-3 d-inline-block">
                                    <span class="badge-pill-soft">
                                        <i class="fas fa-sparkles text-primary me-1"></i> {{ $slide->badge_text }}
                                    </span>
                                </div>
                            @endif

                            <!-- Main Bold Headline -->
                            <h1 class="hero-heading text-center">
                                {{ $slide->heading }}
                                @if(!empty($slide->heading_highlight))
                                    <span class="gradient-text d-block d-sm-inline">{{ $slide->heading_highlight }}</span>
                                @endif
                            </h1>

                            <!-- Subheading / Description -->
                            @if(!empty($slide->description))
                                <p class="hero-lead text-center mx-auto mb-4" style="max-width: 680px;">
                                    {{ $slide->description }}
                                </p>
                            @endif

                            <!-- Action Buttons -->
                            <div class="hero-cta-group justify-content-center mb-3">
                                @if($opensModal)
                                    <button type="button" class="btn-erp-orange btn-lg shadow-lg pulse-btn"
                                        data-bs-toggle="modal" data-bs-target="#proposalModal"
                                        style="padding: 13px 34px; font-size: 1.05rem;">
                                        <span>{{ $slide->primary_btn_text ?: 'GET YOUR FREE PROPOSAL' }}</span>
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                @else
                                    @auth
                                        <a href="{{ route('dashboard') }}" class="btn-erp-primary">
                                            <i class="fas fa-th-large me-1"></i> {{ $slide->primary_btn_text ?: 'Open ERP Portal' }}
                                        </a>
                                    @else
                                        @if(Route::has('register') && $slide->primary_btn_url === '/register')
                                            <a href="{{ route('register') }}" class="btn-erp-primary">
                                                <span>{{ $slide->primary_btn_text ?: 'Get Started' }}</span>
                                                <i class="fas fa-arrow-right ms-1"></i>
                                            </a>
                                        @else
                                            <a href="{{ url($slide->primary_btn_url ?: '/dashboard') }}" class="btn-erp-primary">
                                                <span>{{ $slide->primary_btn_text ?: 'Open ERP Portal' }}</span>
                                                <i class="fas fa-arrow-right ms-1"></i>
                                            </a>
                                        @endif
                                    @endauth
                                @endif

                                @if(!empty($slide->secondary_btn_text))
                                    <a href="{{ url($slide->secondary_btn_url ?: '#modules') }}" class="btn-erp-secondary">
                                        <i class="fas fa-compass text-primary me-1"></i> {{ $slide->secondary_btn_text }}
                                    </a>
                                @endif
                            </div>

                            <!-- Trust Bullets -->
                            @if(count($bullets) > 0)
                                <div class="hero-trust-bullets justify-content-center">
                                    @foreach($bullets as $bullet)
                                        <span><i class="fas fa-check-circle text-success"></i> {{ $bullet }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                    </div>
                @endforeach

            </div>

            <!-- Sleek Slider Controls (Dots & Compact Arrows) -->
            @if($heroSlides->count() > 1)
                <div class="d-flex align-items-center justify-content-center gap-3 mt-3">
                    <button class="hero-mini-arrow" id="heroPrevBtn" aria-label="Previous Slide">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="hero-slider-dots" id="heroSliderDots">
                        @foreach($heroSlides as $i => $s)
                            <button class="slider-dot {{ $i === 0 ? 'active' : '' }}" data-target-slide="{{ $i }}"
                                aria-label="Slide {{ $i + 1 }}"></button>
                        @endforeach
                    </div>
                    <button class="hero-mini-arrow" id="heroNextBtn" aria-label="Next Slide">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            @endif
        </div>

        <!-- 2. FIXED ANCHORED ERP BROWSER MOCKUP (Permanent Centerpiece Below) -->
        <div class="browser-mockup-wrapper mx-auto mt-4" style="max-width: 1080px;">
            <!-- Floating Badge 1: Instant Invoice -->
            <div class="floating-badge badge-1 d-none d-md-flex">
                <div class="badge-icon-box badge-icon-green">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="text-start">
                    <div style="font-size: 0.72rem; color: #64748B;">Invoice #INV-2049</div>
                    <div style="font-weight: 700; color: #00B894;">Paid $1,250.00 • Instant Receipt</div>
                </div>
            </div>

            <!-- Floating Badge 2: Production Job Order -->
            <div class="floating-badge badge-2 d-none d-md-flex">
                <div class="badge-icon-box badge-icon-blue">
                    <i class="fas fa-tools"></i>
                </div>
                <div class="text-start">
                    <div style="font-size: 0.72rem; color: #64748B;">Job Order #JOB-118</div>
                    <div style="font-weight: 700; color: #4361EE;">Ready for Delivery • 100%</div>
                </div>
            </div>

            <!-- Browser Window Container -->
            <div class="browser-mockup text-start shadow-lg">
                <div class="browser-topbar">
                    <div class="browser-dots">
                        <span class="browser-dot red"></span>
                        <span class="browser-dot yellow"></span>
                        <span class="browser-dot green"></span>
                    </div>
                </div>

                <!-- High-Fidelity ERP Dashboard Interface Preview -->
                <div class="mockup-ui">
                    <!-- Mini Sidebar -->
                    <div class="mockup-sidebar">
                        <div class="mockup-brand">
                            <i class="fas fa-cubes text-primary"></i> Cre<span>Logics</span>
                        </div>
                        <div class="mockup-menu-list">
                            <div class="mockup-menu-item active"><i class="fas fa-th-large"></i> Dashboard</div>
                            <div class="mockup-menu-item"><i class="fas fa-clipboard-list"></i> Job Orders</div>
                            <div class="mockup-menu-item"><i class="fas fa-file-signature"></i> Quotations</div>
                            <div class="mockup-menu-item"><i class="fas fa-file-invoice-dollar"></i> Invoices</div>
                            <div class="mockup-menu-item"><i class="fas fa-truck"></i> Delivery</div>
                            <div class="mockup-menu-item"><i class="fas fa-users"></i> CRM & Clients</div>
                            <div class="mockup-menu-item"><i class="fas fa-boxes"></i> Inventory</div>
                        </div>
                    </div>

                    <!-- Main Dashboard Content -->
                    <div class="mockup-content">
                        <div class="mockup-navbar">
                            <div class="mockup-shop-badge">
                                <i class="fas fa-store text-primary"></i>
                                <span>Shop: <strong>New Crelogics (Main)</strong></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success-subtle text-success border px-2 py-1 rounded-pill"
                                    style="font-size: 0.7rem;">
                                    <i class="fas fa-check-circle me-1"></i> Live Sync
                                </span>
                                <div class="mockup-user-pill">
                                    <div class="mockup-avatar">A</div>
                                    <span class="d-none d-sm-inline">Admin</span>
                                </div>
                            </div>
                        </div>

                        <!-- 4 KPI Cards (Dynamic counts from database) -->
                        <div class="mockup-kpi-grid">
                            <div class="mockup-kpi-card kpi-blue">
                                <div class="mockup-kpi-title">Active Shops</div>
                                <div class="mockup-kpi-value">{{ $shopCount ?? 1 }}</div>
                                <div class="mockup-kpi-sub">Branches online</div>
                            </div>
                            <div class="mockup-kpi-card kpi-green">
                                <div class="mockup-kpi-title">Total Invoices</div>
                                <div class="mockup-kpi-value">{{ $invoiceCount ?? 3 }}</div>
                                <div class="mockup-kpi-sub">Across branches</div>
                            </div>
                            <div class="mockup-kpi-card kpi-red">
                                <div class="mockup-kpi-title">Pending Due</div>
                                <div class="mockup-kpi-value">${{ number_format($pendingDue ?? 220, 2) }}</div>
                                <div class="mockup-kpi-sub">Unpaid balances</div>
                            </div>
                            <div class="mockup-kpi-card kpi-purple">
                                <div class="mockup-kpi-title">Quotes / Orders</div>
                                <div class="mockup-kpi-value">{{ ($recentQuotes?->count() ?? 2) }} /
                                    {{ ($orderCount ?? 4) }}</div>
                                <div class="mockup-kpi-sub">Live conversion</div>
                            </div>
                        </div>

                        <!-- Recent Job Orders Mini Table -->
                        <div class="mockup-table-card">
                            <div class="mockup-table-header">
                                <span class="mockup-table-title">Recent Job Orders</span>
                                <span class="badge bg-light text-primary border" style="font-size: 0.72rem;">View
                                    All</span>
                            </div>
                            <div class="table-responsive">
                                <table class="mockup-table">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Customer</th>
                                            <th>Details</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($recentJobOrders) && $recentJobOrders->count() > 0)
                                            @foreach($recentJobOrders->take(3) as $jo)
                                                <tr>
                                                    <td class="fw-bold text-primary">
                                                        {{ $jo->order_number ?: '#JOB-' . $jo->id }}</td>
                                                    <td>{{ $jo->customer?->name ?: 'Client' }}</td>
                                                    <td>Production Order</td>
                                                    <td class="fw-bold">
                                                        ${{ number_format($jo->grand_total ?: $jo->total_amount, 2) }}</td>
                                                    <td>
                                                        <span class="mockup-tag tag-blue">
                                                            <i class="fas fa-cog fa-spin me-1"></i>
                                                            {{ ucfirst(str_replace('_', ' ', $jo->status ?: 'In Prod')) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="fw-bold text-primary">#JOB-1024</td>
                                                <td>Farooq Builders</td>
                                                <td>Flex Sign (12x4 ft)</td>
                                                <td class="fw-bold">$450.00</td>
                                                <td><span class="mockup-tag tag-green"><i
                                                            class="fas fa-check-circle me-1"></i> Ready</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-primary">#JOB-1023</td>
                                                <td>Imran Trading LLC</td>
                                                <td>3D Letter Signage</td>
                                                <td class="fw-bold">$820.00</td>
                                                <td><span class="mockup-tag tag-blue"><i
                                                            class="fas fa-cog fa-spin me-1"></i> In Prod</span></td>
                                            </tr>
                                            <tr>
                                                <td class="fw-bold text-primary">#JOB-1022</td>
                                                <td>Salman Agencies</td>
                                                <td>Vinyl Stickers (100)</td>
                                                <td class="fw-bold">$210.00</td>
                                                <td><span class="mockup-tag tag-amber"><i class="fas fa-clock me-1"></i>
                                                        Pending</span></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Sleek Text Slider Styles -->
<style>
    .hero-text-slider-wrapper {
        position: relative;
        min-height: 230px;
    }

    .hero-text-slider {
        position: relative;
        width: 100%;
    }

    .hero-text-slide {
        display: none;
        opacity: 0;
        transition: opacity 0.45s ease-in-out, transform 0.45s ease-in-out;
        transform: translateY(8px);
    }

    .hero-text-slide.active {
        display: block;
        opacity: 1;
        transform: translateY(0);
        animation: fadeInHeroText 0.45s ease-in-out forwards;
    }

    @keyframes fadeInHeroText {
        0% {
            opacity: 0;
            transform: translateY(8px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .hero-mini-arrow {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        color: #64748B;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .hero-mini-arrow:hover {
        background: #0A6FB7;
        color: #FFFFFF;
        border-color: #0A6FB7;
        transform: scale(1.1);
    }

    .hero-slider-dots {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(4px);
        padding: 5px 12px;
        border-radius: 20px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .slider-dot {
        width: 8px;
        height: 8px;
        border-radius: 10px;
        background: #CBD5E1;
        border: none;
        padding: 0;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .slider-dot.active {
        width: 22px;
        background: #FF8300;
    }

    .pulse-btn {
        animation: pulseCta 2.2s infinite;
    }

    @keyframes pulseCta {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 131, 0, 0.5);
        }

        70% {
            box-shadow: 0 0 0 14px rgba(255, 131, 0, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(255, 131, 0, 0);
        }
    }
</style>

<!-- Text Carousel Vanilla JS Engine -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sliderContainer = document.getElementById('homepageHeroSlider');
        if (!sliderContainer) return;

        const slides = sliderContainer.querySelectorAll('.hero-text-slide');
        const prevBtn = document.getElementById('heroPrevBtn');
        const nextBtn = document.getElementById('heroNextBtn');
        const dots = document.querySelectorAll('#heroSliderDots .slider-dot');

        if (slides.length <= 1) return;

        let currentIndex = 0;
        let autoplayTimer = null;
        let isPaused = false;

        function goToSlide(idx) {
            if (idx < 0) {
                idx = slides.length - 1;
            } else if (idx >= slides.length) {
                idx = 0;
            }

            slides.forEach((s, i) => {
                if (i === idx) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });

            dots.forEach((d, i) => {
                if (i === idx) {
                    d.classList.add('active');
                } else {
                    d.classList.remove('active');
                }
            });

            currentIndex = idx;
            resetAutoplay();
        }

        function nextSlide() {
            goToSlide(currentIndex + 1);
        }

        function prevSlide() {
            goToSlide(currentIndex - 1);
        }

        function startAutoplay() {
            if (isPaused) return;
            const currentSlideEl = slides[currentIndex];
            const duration = parseInt(currentSlideEl.getAttribute('data-duration')) || 7000;

            clearTimeout(autoplayTimer);
            autoplayTimer = setTimeout(function () {
                if (!isPaused) {
                    nextSlide();
                }
            }, duration);
        }

        function resetAutoplay() {
            clearTimeout(autoplayTimer);
            startAutoplay();
        }

        if (nextBtn) nextBtn.addEventListener('click', nextSlide);
        if (prevBtn) prevBtn.addEventListener('click', prevSlide);

        dots.forEach((dot, i) => {
            dot.addEventListener('click', function () {
                goToSlide(i);
            });
        });

        const wrapper = document.getElementById('heroTextSliderWrapper');
        if (wrapper) {
            wrapper.addEventListener('mouseenter', function () {
                isPaused = true;
                clearTimeout(autoplayTimer);
            });
            wrapper.addEventListener('mouseleave', function () {
                isPaused = false;
                startAutoplay();
            });
        }

        startAutoplay();
    });
</script>