@php
    $sec = $sections['how_it_works'] ?? null;
    $stepTitle = $sec?->title ?: 'Get Started in Three Simple Steps';
    $stepSub = $sec?->subtitle ?: 'No complex enterprise configurations or weeks of setup. Crelogics ERP gets your team up and operational on day one.';
@endphp

<section class="steps-section" id="how-it-works">
    <div class="container">
        <!-- Section Header -->
        <div class="text-center mb-4">
            <span class="badge-pill-soft mb-3">
                <i class="fas fa-rocket text-primary me-1"></i> {{ __('Quick Onboarding') }}
            </span>
            <h2 class="section-title">{{ __($stepTitle) }}</h2>
            <p class="section-subtitle">{{ __($stepSub) }}</p>
        </div>

        <div class="row g-4 justify-content-center">
            @if(isset($steps) && $steps->count() > 0)
                @foreach($steps as $st)
                    <div class="col-lg-4 col-md-6">
                        <div class="step-card h-100">
                            <div class="step-badge" style="{{ $st->badge_color ? 'background-color: ' . $st->badge_color . ';' : '' }}">
                                {{ $st->step_number }}
                            </div>
                            <h4 class="step-title">{{ __($st->title) }}</h4>
                            <p class="step-desc">{{ __($st->description) }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Default 3 Steps -->
                <div class="col-lg-4 col-md-6">
                    <div class="step-card h-100">
                        <div class="step-badge">1</div>
                        <h4 class="step-title">{{ __('Create Your Business Account') }}</h4>
                        <p class="step-desc">{{ __('Register in less than 60 seconds with no credit card required. Your multi-shop workspace is ready immediately.') }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="step-card h-100">
                        <div class="step-badge" style="background-color: var(--clr-accent);">2</div>
                        <h4 class="step-title">{{ __('Configure Items & Branches') }}</h4>
                        <p class="step-desc">{{ __('Add product sizes, measurement rates (sqft, running meters, pieces), upload company logo, and invite staff members.') }}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="step-card h-100">
                        <div class="step-badge" style="background-color: var(--clr-secondary);">3</div>
                        <h4 class="step-title">{{ __('Generate Quotes & Collect Cash') }}</h4>
                        <p class="step-desc">{{ __('Draft proposals on the fly, dispatch Job Orders to technicians, generate invoices, and record cash payments seamlessly.') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
