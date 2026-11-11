@php
    $sec = $sections['faq'] ?? null;
    $faqTitle = $sec?->title ?: 'Frequently Asked Questions';
    $faqSub = $sec?->subtitle ?: 'Everything you need to know about setting up Crelogics ERP, managing branches, and subscription options.';
@endphp

<section class="faq-section" id="faq">
    <div class="container">
        <!-- Section Header -->
        <div class="text-center">
            <span class="section-tag">Common Inquiries</span>
            <h2 class="section-title">{{ $faqTitle }}</h2>
            <p class="section-subtitle">{{ $faqSub }}</p>
        </div>

        <div class="row justify-content-center mt-3">
            <div class="col-lg-9">
                @forelse($faqs as $f)
                    <div class="faq-card">
                        <div class="faq-header">
                            <span>{{ $f->question }}</span>
                            <div class="faq-icon"><i class="fas fa-chevron-down"></i></div>
                        </div>
                        <div class="faq-content">
                            {{ $f->answer }}
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted">No FAQs found.</div>
                @endforelse
            </div>
        </div>
    </div>
</section>
