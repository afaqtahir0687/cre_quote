<section class="pricing-section" id="pricing">
    <div class="container">
        <!-- Section Header -->
        <div class="text-center">
            <span class="section-tag">Plans & Investment</span>
            <h2 class="section-title">Transparent, Predictable Pricing</h2>
            <p class="section-subtitle">
                No hidden charges or surprise transaction fees. Select the plan that fits your current business scale and grow with confidence.
            </p>
        </div>

        <div class="row g-4 justify-content-center mt-2">
            @forelse($packages as $pkg)
                @php
                    $isPopular = ($pkg->id == 2) || (str_contains(strtolower($pkg->name), 'standard'));
                    $isFree = ((float)$pkg->price == 0);
                @endphp
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card {{ $isPopular ? 'featured' : '' }}">
                        @if($isPopular)
                            <div class="pricing-popular-badge">
                                <i class="fas fa-crown me-1"></i> Most Popular
                            </div>
                        @endif

                        <h3 class="pricing-name">{{ $pkg->name }}</h3>
                        <p class="pricing-desc">{{ $pkg->description }}</p>

                        <div class="pricing-price-wrap">
                            <span class="pricing-currency">$</span>
                            <span class="pricing-amount">{{ number_format($pkg->price, 0) }}</span>
                            <span class="pricing-period">
                                @if($isFree)
                                    / {{ $pkg->duration_days }} days trial
                                @elseif($pkg->billing_cycle === 'yearly')
                                    / year ({{ $pkg->duration_days }} days)
                                @else
                                    / {{ $pkg->billing_cycle }}
                                @endif
                            </span>
                        </div>

                        @if(!$isFree && (float)$pkg->price > 0)
                            <div class="small text-muted mb-3" style="font-size: 0.82rem;">
                                <i class="fas fa-coins text-warning me-1"></i> ≈ <strong class="text-dark">{{ number_format($pkg->price * 3.75, 2) }} SAR</strong> <span style="font-size: 0.72rem;">(1 USD = 3.75 SAR)</span>
                            </div>
                        @else
                            <div class="mb-3"></div>
                        @endif

                        <!-- Capacity Badges -->
                        <div class="d-flex gap-2 flex-wrap mb-3">
                            <span class="badge bg-light text-dark border px-2 py-1">
                                <i class="fas fa-store text-primary me-1"></i> 
                                {{ $pkg->max_shops ?? 1 }} {{ ($pkg->max_shops ?? 1) > 1 ? 'Shops' : 'Shop' }}
                            </span>
                            <span class="badge bg-light text-dark border px-2 py-1">
                                <i class="fas fa-users text-primary me-1"></i> 
                                Up to {{ $pkg->max_employees ?? 3 }} Staff
                            </span>
                        </div>

                        <!-- Features Checklist -->
                        <ul class="pricing-features-list">
                            @if(is_array($pkg->features) && count($pkg->features) > 0)
                                @foreach($pkg->features as $feature)
                                    <li>
                                        <i class="fas fa-check-circle"></i>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            @else
                                <li><i class="fas fa-check-circle"></i> Quotes & Invoices Management</li>
                                <li><i class="fas fa-check-circle"></i> Job Orders & Delivery Notes</li>
                                <li><i class="fas fa-check-circle"></i> Customer CRM & Ledgers</li>
                                <li><i class="fas fa-check-circle"></i> Branded PDF Exports</li>
                            @endif
                        </ul>

                        <!-- CTA Button -->
                        <div class="mt-auto">
                            @auth
                                <a href="{{ route('subscription.index') }}" class="{{ $isPopular ? 'btn-erp-primary' : 'btn-erp-secondary' }} w-100">
                                    <span>Upgrade Subscription</span>
                                    <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            @else
                                @if(Route::has('register'))
                                    <a href="{{ route('register') }}" class="{{ $isPopular ? 'btn-erp-primary' : 'btn-erp-secondary' }} w-100">
                                        <span>{{ $isFree ? 'Start 7-Day Free Trial' : 'Get Started Now' }}</span>
                                        <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="{{ $isPopular ? 'btn-erp-primary' : 'btn-erp-secondary' }} w-100">
                                        <span>Sign In to Activate</span>
                                        <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <!-- Fallback in case packages table is empty -->
                <div class="col-lg-4 col-md-6">
                    <div class="pricing-card featured">
                        <div class="pricing-popular-badge">Starter Trial</div>
                        <h3 class="pricing-name">Test Account</h3>
                        <p class="pricing-desc">7-day test account for system trial & evaluation.</p>
                        <div class="pricing-price-wrap">
                            <span class="pricing-currency">$</span>
                            <span class="pricing-amount">0</span>
                            <span class="pricing-period">/ 7 days</span>
                        </div>
                        <ul class="pricing-features-list">
                            <li><i class="fas fa-check-circle"></i> 7 Days Full Test Period</li>
                            <li><i class="fas fa-check-circle"></i> Single Shop Location (1 Shop)</li>
                            <li><i class="fas fa-check-circle"></i> Up to 3 Staff Members</li>
                            <li><i class="fas fa-check-circle"></i> Quotes & Invoices Management</li>
                            <li><i class="fas fa-check-circle"></i> Job Orders & Delivery Notes</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn-erp-primary w-100">Start Free Trial</a>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-5">
            <p class="text-muted mb-0">
                <i class="fas fa-shield-alt text-success me-1"></i>
                All plans include offline cash activation support, data backups, and priority WhatsApp technical assistance.
            </p>
        </div>
    </div>
</section>

