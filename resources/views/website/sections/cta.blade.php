@php
    $sec = $sections['cta'] ?? null;
    $ctaTitle = $sec?->title ?: 'Ready to Take Total Control of Your Business Operations?';
    $ctaSub = $sec?->subtitle ?: 'Eliminate scattered spreadsheets, accelerate customer quotation turnarounds, and keep your entire team synchronized from quote to final cash collection.';
    $waNum = preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? '996598500143');
@endphp

<section class="cta-section position-relative overflow-hidden" id="cta">
    <div class="cta-circle-bg circle-1"></div>
    <div class="cta-circle-bg circle-2"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="cta-box text-center">
            <span class="badge-pill-accent mb-3">
                <i class="fas fa-rocket me-1"></i> Quick 2-Minute Onboarding
            </span>
            <h2 class="text-white fw-bold mb-3">{{ $ctaTitle }}</h2>
            <p class="mb-4">
                {{ $ctaSub }}
            </p>
            <div class="cta-buttons">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-cta-white">
                        <i class="fas fa-th-large me-1"></i> Open CreLogics ERP Dashboard
                    </a>
                @else
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-cta-white">
                            <span>Start Free 7-Day Trial</span>
                            <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    @endif
                    <a href="{{ route('login') }}" class="btn-erp-outline-white">
                        <i class="fas fa-lock me-1"></i> Sign In to Portal
                    </a>
                @endauth
                <a href="https://wa.me/{{ $waNum }}" target="_blank" rel="noopener noreferrer" class="btn-erp-outline-white">
                    <i class="fab fa-whatsapp text-success me-1"></i> Chat on WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>
