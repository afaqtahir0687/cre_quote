@php
    $sec = $sections['solutions'] ?? null;
    $solTitle = $sec?->title ?: 'Built for Businesses That Want Better Control';
    $solSub = $sec?->subtitle ?: 'Engineered with practical flexibility to support specialized workflows, custom sizing calculations, and multi-location businesses.';
@endphp

<section class="solutions-section" id="solutions">
    <div class="container">
        <!-- Section Header -->
        <div class="text-center">
            <span class="section-tag">Industry Solutions</span>
            <h2 class="section-title">{{ $solTitle }}</h2>
            <p class="section-subtitle">{{ $solSub }}</p>
        </div>

        <div class="row g-4 mt-2">
            @forelse($solutions as $sol)
                <div class="col-lg-6">
                    <div class="solution-card">
                        <span class="solution-tag"><i class="fas fa-layer-group me-1"></i> {{ $sol->tag }}</span>
                        <h3 class="solution-title">{{ $sol->title }}</h3>
                        <p class="solution-desc">{{ $sol->description }}</p>
                        @if(is_array($sol->checklist) && count($sol->checklist) > 0)
                            <ul class="solution-checklist">
                                @foreach($sol->checklist as $item)
                                    <li><i class="fas fa-check-circle"></i> {{ $item }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">No solutions configured.</div>
            @endforelse
        </div>
    </div>
</section>
