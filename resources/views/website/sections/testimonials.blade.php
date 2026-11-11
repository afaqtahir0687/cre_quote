@php
    $sec = $sections['testimonials'] ?? null;
    $testTitle = $sec?->title ?: 'Loved by Growing Enterprises';
    $testSub = $sec?->subtitle ?: 'Real feedback from business owners and workshop managers using Crelogics ERP daily.';
@endphp

@if(isset($testimonials) && $testimonials->count() > 0)
<section class="testimonials-section py-5 bg-light-soft position-relative" id="testimonials">
    <div class="container py-4">
        <div class="text-center mb-4">
            <span class="section-tag">Verified Customer Reviews</span>
            <h2 class="section-title">{{ $testTitle }}</h2>
            <p class="section-subtitle">{{ $testSub }}</p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($testimonials as $t)
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm p-4 rounded-4 h-100 d-flex flex-column bg-white">
                        <div class="d-flex align-items-center gap-1 text-warning mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $t->rating ? '' : 'text-muted opacity-25' }}"></i>
                            @endfor
                        </div>
                        <p class="text-muted flex-grow-1" style="font-size: 0.95rem; line-height: 1.6;">
                            "{{ $t->content }}"
                        </p>
                        <div class="d-flex align-items-center gap-3 pt-3 border-top mt-auto">
                            <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 1.1rem; flex-shrink: 0;">
                                {{ substr($t->name, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0">{{ $t->name }}</h6>
                                <div class="text-muted small">
                                    {{ $t->designation ? $t->designation . ' • ' : '' }}<strong>{{ $t->company }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

