@php
    $sec = $sections['features'] ?? null;
    $featTitle = $sec?->title ?: 'Powerful Features to Simplify Your Business';
    $featSub = $sec?->subtitle ?: 'Everything required to handle customer inquiries, manage production work, issue compliant tax invoices, and track outstanding revenue.';
@endphp

<section class="features-section" id="features">
    <div class="container">
        <!-- Section Header -->
        <div class="text-center">
            <span class="section-tag">Core Capabilities</span>
            <h2 class="section-title">{{ $featTitle }}</h2>
            <p class="section-subtitle">{{ $featSub }}</p>
        </div>

        <!-- Dynamic Features Grid -->
        <div class="row g-4 mt-2">
            @forelse($features as $f)
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon-wrap {{ $f->icon_color_class }}">
                            <i class="{{ $f->icon_class }}"></i>
                        </div>
                        <h3 class="feature-title">{{ $f->title }}</h3>
                        <p class="feature-desc">{{ $f->description }}</p>
                        @if(is_array($f->highlights) && count($f->highlights) > 0)
                            <ul class="feature-highlights">
                                @foreach($f->highlights as $h)
                                    <li><i class="fas fa-check"></i> {{ $h }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-4">No features configured.</div>
            @endforelse
        </div>
    </div>
</section>
