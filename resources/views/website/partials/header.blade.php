@php
    $logo = !empty($settings['website_logo']) ? asset($settings['website_logo']) : (platform_logo_url() ?: asset('uploads/logo/crelogics-logo.png'));
    $siteName = $settings['site_name'] ?? 'Crelogics ERP';
@endphp
<header class="erp-header">
    <nav class="navbar navbar-expand-lg py-3">
        <div class="container">
            <!-- Brand Logo -->
            <a class="navbar-brand navbar-brand-wrap" href="{{ url('/') }}">
                @if($logo && (file_exists(public_path('uploads/logo/crelogics-logo.png')) || !empty($settings['website_logo']) || platform_logo_url()))
                    <img src="{{ $logo }}" alt="{{ $siteName }}" class="navbar-brand-logo">
                @else
                    <span class="navbar-brand-text">Cre<span>Logics</span></span>
                @endif
                <span class="navbar-brand-badge">ERP</span>
            </a>

            <!-- Mobile Menu Toggle Button -->
            <button class="navbar-toggler border-0 shadow-none px-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarLandingContent" aria-controls="navbarLandingContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars fa-lg text-dark"></i>
            </button>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarLandingContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 gap-1">
                    @if(isset($headerNav) && $headerNav->count() > 0)
                        @foreach($headerNav as $nav)
                            @php
                                $isCurrent = request()->is(trim($nav->url, '/')) || (request()->is('/') && ($nav->url === '/' || $nav->url === ''));
                                $navHref = str_starts_with($nav->url, '#') ? url('/' . $nav->url) : (str_starts_with($nav->url, 'http') ? $nav->url : url($nav->url));
                            @endphp
                            <li class="nav-item">
                                <a class="nav-link nav-link-erp {{ $isCurrent ? 'active fw-bold text-primary' : '' }}" href="{{ $navHref }}" target="{{ $nav->target ?: '_self' }}">
                                    {{ $nav->label }}
                                </a>
                            </li>
                        @endforeach
                    @else
                        <li class="nav-item"><a class="nav-link nav-link-erp {{ request()->is('/') ? 'active fw-bold text-primary' : '' }}" href="{{ url('/') }}">Home</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-erp {{ request()->is('services') ? 'active fw-bold text-primary' : '' }}" href="{{ url('/services') }}">Services</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-erp {{ request()->is('products*') ? 'active fw-bold text-primary' : '' }}" href="{{ url('/products') }}">Products</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-erp" href="{{ url('/#features') }}">Features</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-erp" href="{{ url('/#modules') }}">Modules</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-erp" href="{{ url('/#showcase') }}">Workflow</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-erp" href="{{ url('/#pricing') }}">Pricing</a></li>
                        <li class="nav-item"><a class="nav-link nav-link-erp" href="{{ url('/#contact') }}">Contact</a></li>
                    @endif
                </ul>

                <!-- Action CTAs -->
                <div class="nav-actions mt-3 mt-lg-0">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-erp-primary">
                            <i class="fas fa-th-large me-1"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-erp-secondary">
                            <i class="fas fa-sign-in-alt me-1"></i> Sign In
                        </a>
                        @if(Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-erp-primary">
                                <span>Get Started</span>
                                <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>
