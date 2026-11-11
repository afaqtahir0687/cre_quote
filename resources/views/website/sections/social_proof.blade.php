@php
    $sec = $sections['social_proof'] ?? null;
    $headingEntity = $settings['social_proof_heading'] ?? 'Businesses';
    $proofSub = $settings['social_proof_subheading'] ?? 'who grow and manage their daily sales, job production, and finances with Crelogics ERP';
@endphp

<!-- CreLogics Original Constellation Social Proof Section (Original Bespoke UI) -->
<section class="crelogics-social-proof-section position-relative overflow-hidden" id="social-proof">
    <div class="container-fluid px-lg-5">
        <div class="crelogics-mosaic-wrapper position-relative mx-auto">
            
            <!-- Central Modern Floating Card -->
            <div class="crelogics-central-card text-center">
                <!-- Verified Network Pill -->
                <div class="mb-3">
                    <span class="badge bg-primary-subtle text-primary border px-3 py-1 rounded-pill fw-bold" style="font-size: 0.8rem;">
                        <i class="fas fa-shield-alt me-1"></i> Verified Business Network
                    </span>
                </div>

                <!-- Main Dynamic Headline -->
                <h2 class="crelogics-proof-title mb-2">
                    Join <span class="counter-highlight" data-count="{{ $businessCount }}">{{ $businessCount }}+</span> {{ $headingEntity }}
                </h2>
                
                <p class="crelogics-proof-subtitle text-muted mb-3 mx-auto" style="max-width: 520px;">
                    {{ $proofSub }}
                </p>

                <!-- Dynamic Verified Client Badges (From Database) -->
                @if(isset($customers) && $customers->count() > 0)
                    <div class="verified-clients-row d-none d-md-flex align-items-center justify-content-center gap-2 mb-3">
                        <span class="badge bg-light text-muted border px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                            <i class="fas fa-check-circle text-success me-1"></i> Verified Clients:
                        </span>
                        @foreach($customers->take(4) as $client)
                            <span class="badge bg-white text-dark border px-2 py-1 rounded-pill shadow-xs" style="font-size: 0.72rem;">
                                {{ $client->company_name ?: $client->name }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <!-- Call to action button -->
                <div>
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-erp-primary btn-sm px-4 py-2 rounded-pill">
                            <span>Open ERP Portal</span>
                            <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    @else
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-erp-primary btn-sm px-4 py-2 rounded-pill">
                                <span>Start 7-Day Free Trial</span>
                                <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-erp-primary btn-sm px-4 py-2 rounded-pill">
                                <span>Sign In to Get Started</span>
                                <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Surrounding Dynamic Constellation Grid (Custom Shapes, Avatars & Metric Badges) -->
            <div class="crelogics-mosaic-grid">
                
                <!-- ROW 1 -->
                <div class="mosaic-tile tile-sq-blue tile-col-1 tile-row-1"></div>
                <div class="mosaic-tile tile-avatar tile-col-3 tile-row-1">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=140&h=140&fit=crop&crop=faces&q=80" alt="Client Founder" loading="lazy">
                </div>
                <div class="mosaic-tile tile-circle-grey tile-col-5 tile-row-1"></div>
                <div class="mosaic-tile tile-chip-badge tile-col-6 tile-row-1">
                    <span>⭐ 4.9/5 Rating</span>
                </div>
                <div class="mosaic-tile tile-avatar tile-col-7 tile-row-1">
                    <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?w=140&h=140&fit=crop&crop=faces&q=80" alt="Retail Head" loading="lazy">
                </div>
                <div class="mosaic-tile tile-sq-green tile-col-9 tile-row-1"></div>
                <div class="mosaic-tile tile-avatar tile-col-10 tile-row-1">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=140&h=140&fit=crop&crop=faces&q=80" alt="Director" loading="lazy">
                </div>
                <div class="mosaic-tile tile-sq-purple tile-col-12 tile-row-1"></div>

                <!-- ROW 2 -->
                <div class="mosaic-tile tile-avatar tile-col-2 tile-row-2">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=140&h=140&fit=crop&crop=faces&q=80" alt="Production Manager" loading="lazy">
                </div>
                <div class="mosaic-tile tile-chip-badge tile-col-3 tile-row-2">
                    <span>⚡ 100% Sync</span>
                </div>
                <div class="mosaic-tile tile-sq-blue tile-col-11 tile-row-2"></div>
                <div class="mosaic-tile tile-avatar tile-col-12 tile-row-2">
                    <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=140&h=140&fit=crop&crop=faces&q=80" alt="Shop Owner" loading="lazy">
                </div>

                <!-- ROW 3 -->
                <div class="mosaic-tile tile-circle-grey tile-col-1 tile-row-3"></div>
                <div class="mosaic-tile tile-avatar tile-col-11 tile-row-3">
                    <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=140&h=140&fit=crop&crop=faces&q=80" alt="Signage Partner" loading="lazy">
                </div>
                <div class="mosaic-tile tile-sq-green tile-col-12 tile-row-3"></div>

                <!-- ROW 4 -->
                <div class="mosaic-tile tile-avatar tile-col-1 tile-row-4">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=140&h=140&fit=crop&crop=faces&q=80" alt="Workshop Contractor" loading="lazy">
                </div>
                <div class="mosaic-tile tile-sq-purple tile-col-2 tile-row-4"></div>
                <div class="mosaic-tile tile-chip-badge tile-col-11 tile-row-4">
                    <span>💼 Multi-Shop</span>
                </div>
                <div class="mosaic-tile tile-avatar tile-col-12 tile-row-4">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=140&h=140&fit=crop&crop=faces&q=80" alt="Enterprise Client" loading="lazy">
                </div>

                <!-- ROW 5 -->
                <div class="mosaic-tile tile-sq-blue tile-col-2 tile-row-5"></div>
                <div class="mosaic-tile tile-avatar tile-col-3 tile-row-5">
                    <img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?w=140&h=140&fit=crop&crop=faces&q=80" alt="Printing Manager" loading="lazy">
                </div>
                <div class="mosaic-tile tile-chip-badge tile-col-5 tile-row-5">
                    <span>📈 +35% Growth</span>
                </div>
                <div class="mosaic-tile tile-circle-grey tile-col-7 tile-row-5"></div>
                <div class="mosaic-tile tile-avatar tile-col-8 tile-row-5">
                    <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=140&h=140&fit=crop&crop=faces&q=80" alt="Branch Admin" loading="lazy">
                </div>
                <div class="mosaic-tile tile-sq-green tile-col-10 tile-row-5"></div>
                <div class="mosaic-tile tile-avatar tile-col-11 tile-row-5">
                    <img src="https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=140&h=140&fit=crop&crop=faces&q=80" alt="Executive Director" loading="lazy">
                </div>

            </div>

        </div>
    </div>
</section>
