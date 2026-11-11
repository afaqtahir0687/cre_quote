@php
    $sec = $sections['benefits'] ?? null;
    $benTitle = $sec?->title ?: 'Why Businesses Choose Crelogics ERP Over Generic Tools';
    $benSub = $sec?->subtitle ?: 'Most small and mid-sized enterprises struggle with scattered spreadsheets, lost quotation drafts, and manual paperwork that slows down billing.';
@endphp

<section class="benefits-section" id="benefits">
    <div class="container">
        <div class="row g-5 align-items-center">
            <!-- Left Column: Core Value Propositions from DB -->
            <div class="col-lg-6">
                <span class="section-tag">Measurable Impact</span>
                <h2 class="section-title text-start mb-4">{{ $benTitle }}</h2>
                <p class="text-muted mb-4">{{ $benSub }}</p>

                @forelse($benefits as $b)
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="{{ $b->icon_class ?: 'fas fa-check' }}"></i>
                        </div>
                        <div>
                            <h4 class="benefit-title">{{ $b->title }}</h4>
                            <p class="benefit-desc">{{ $b->description }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-muted">No benefits configured.</div>
                @endforelse
            </div>

            <!-- Right Column: Visual Comparison Table -->
            <div class="col-lg-6">
                <div class="comparison-box">
                    <h4 class="fw-bold text-dark mb-4 text-center">Transforming Everyday Business Operations</h4>
                    <div class="row g-4">
                        <!-- Left: Old Way -->
                        <div class="col-md-6 border-end-md">
                            <div class="comparison-col-header col-header-bad">
                                <i class="fas fa-times-circle me-1"></i> Disconnected Tools
                            </div>
                            <ul class="comparison-list">
                                <li>
                                    <i class="fas fa-times"></i>
                                    <span>Manual Excel spreadsheets with broken pricing formulas</span>
                                </li>
                                <li>
                                    <i class="fas fa-times"></i>
                                    <span>Quotes lost in email threads or chat messages</span>
                                </li>
                                <li>
                                    <i class="fas fa-times"></i>
                                    <span>No real-time visibility into branch revenue or orders</span>
                                </li>
                                <li>
                                    <i class="fas fa-times"></i>
                                    <span>Unpaid customer balances tracked on paper notebooks</span>
                                </li>
                                <li>
                                    <i class="fas fa-times"></i>
                                    <span>Duplicate data re-entry from quotation to delivery</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Right: Crelogics ERP Way -->
                        <div class="col-md-6">
                            <div class="comparison-col-header col-header-good">
                                <i class="fas fa-check-circle me-1"></i> With Crelogics ERP
                            </div>
                            <ul class="comparison-list">
                                <li>
                                    <i class="fas fa-check"></i>
                                    <span>Unified cloud database accessible from any device</span>
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    <span>Instant PDF quotes & invoices with professional branding</span>
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    <span>Consolidated multi-shop overview for owners</span>
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    <span>Automated customer transaction ledger & statements</span>
                                </li>
                                <li>
                                    <i class="fas fa-check"></i>
                                    <span>1-Click workflow from quote to invoice to delivery</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
