@php
    $logo = !empty($settings['website_logo']) ? asset($settings['website_logo']) : (platform_logo_url() ?: asset('uploads/logo/crelogics-logo.png'));
    $siteName = $settings['site_name'] ?? 'Crelogics ERP';
    $groupedServices = isset($websiteServices) ? $websiteServices : (class_exists(\App\Models\Service::class) ? \App\Models\Service::active()->orderBy('category')->orderBy('sort_order')->get()->groupBy('category') : collect());
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
                                $isServices = (strtolower(trim($nav->label)) === 'services' || trim($nav->url, '/') === 'services');
                                $isCurrent = request()->is(trim($nav->url, '/')) || (request()->is('/') && ($nav->url === '/' || $nav->url === ''));
                                $navHref = str_starts_with($nav->url, '#') ? url('/' . $nav->url) : (str_starts_with($nav->url, 'http') ? $nav->url : url($nav->url));
                            @endphp

                            @if($isServices && $groupedServices->count() > 0)
                                <!-- Multi-Level Services Dropdown (crelogics.com structure) -->
                                <li class="nav-item dropdown services-nav-dropdown">
                                    <a class="nav-link nav-link-erp dropdown-toggle d-flex align-items-center gap-1 {{ request()->is('services*') ? 'active fw-bold text-primary' : '' }}" 
                                       href="{{ url('/services') }}" 
                                       id="servicesNavbarDropdown" 
                                       role="button" 
                                       data-bs-toggle="dropdown" 
                                       aria-expanded="false">
                                        <span>Services</span>
                                        <i class="fas fa-caret-down text-muted" style="font-size: 0.8rem;"></i>
                                    </a>
                                    <ul class="dropdown-menu services-menu-parent border-0 shadow-lg rounded-3" aria-labelledby="servicesNavbarDropdown">
                                        @foreach(['Creativity', 'Marketing', 'Advertising'] as $catKey)
                                            @if(isset($groupedServices[$catKey]) && $groupedServices[$catKey]->count() > 0)
                                                <li class="services-flyout-item">
                                                    <a href="{{ url('/services?category=' . $catKey) }}" class="services-flyout-link">
                                                        <span>{{ $catKey }}</span>
                                                        <i class="fas fa-caret-right text-muted small ms-2"></i>
                                                    </a>
                                                    <ul class="services-flyout-submenu list-unstyled mb-0">
                                                        @foreach($groupedServices[$catKey] as $sItem)
                                                            <li>
                                                                <a href="{{ route('website.service_detail', $sItem->slug) }}" class="services-sub-item-link">
                                                                    <i class="{{ $sItem->icon ?: 'fas fa-chevron-right' }} extra-small" style="color: #FA7436; font-size: 0.75rem;"></i>
                                                                    <span>{{ $sItem->name }}</span>
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endif
                                        @endforeach
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li>
                                            <a href="{{ url('/services') }}" class="services-sub-item-link fw-bold text-primary">
                                                <i class="fas fa-layer-group extra-small"></i>
                                                <span>{{ __('View All Services Overview') }} &rarr;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link nav-link-erp {{ $isCurrent ? 'active fw-bold text-primary' : '' }}" href="{{ $navHref }}" target="{{ $nav->target ?: '_self' }}">
                                        {{ $nav->label }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li class="nav-item"><a class="nav-link nav-link-erp {{ request()->is('/') ? 'active fw-bold text-primary' : '' }}" href="{{ url('/') }}">Home</a></li>
                        
                        <!-- Fallback Services Dropdown -->
                        <li class="nav-item dropdown services-nav-dropdown">
                            <a class="nav-link nav-link-erp dropdown-toggle d-flex align-items-center gap-1 {{ request()->is('services*') ? 'active fw-bold text-primary' : '' }}" 
                               href="{{ url('/services') }}" 
                               id="servicesNavbarDropdownFallback" 
                               role="button" 
                               data-bs-toggle="dropdown" 
                               aria-expanded="false">
                                <span>Services</span>
                                <i class="fas fa-caret-down text-muted" style="font-size: 0.8rem;"></i>
                            </a>
                            <ul class="dropdown-menu services-menu-parent border-0 shadow-lg rounded-3" aria-labelledby="servicesNavbarDropdownFallback">
                                @foreach(['Creativity', 'Marketing', 'Advertising'] as $catKey)
                                    @if(isset($groupedServices[$catKey]) && $groupedServices[$catKey]->count() > 0)
                                        <li class="services-flyout-item">
                                            <a href="{{ url('/services?category=' . $catKey) }}" class="services-flyout-link">
                                                <span>{{ $catKey }}</span>
                                                <i class="fas fa-caret-right text-muted small ms-2"></i>
                                            </a>
                                            <ul class="services-flyout-submenu list-unstyled mb-0">
                                                @foreach($groupedServices[$catKey] as $sItem)
                                                    <li>
                                                        <a href="{{ route('website.service_detail', $sItem->slug) }}" class="services-sub-item-link">
                                                            <i class="{{ $sItem->icon ?: 'fas fa-chevron-right' }} extra-small" style="color: #FA7436; font-size: 0.75rem;"></i>
                                                            <span>{{ $sItem->name }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endif
                                @endforeach
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a href="{{ url('/services') }}" class="services-sub-item-link fw-bold text-primary">
                                        <i class="fas fa-layer-group extra-small"></i>
                                        <span>{{ __('View All Services') }} &rarr;</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

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

<style>
/* Multi-Level Services Dropdown (crelogics.com style) */
.services-nav-dropdown {
    position: relative;
}
.services-menu-parent {
    min-width: 210px;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.08);
    box-shadow: 0 16px 36px rgba(0,0,0,0.12);
    padding: 8px 0;
    margin-top: 4px;
}
.services-flyout-item {
    position: relative;
}
.services-flyout-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 18px;
    font-size: 0.92rem;
    font-weight: 600;
    color: #1E293B;
    text-decoration: none;
    transition: all 0.18s ease;
}
.services-flyout-link:hover, .services-flyout-item:hover > .services-flyout-link {
    color: #FA7436;
    background: #FFF7ED;
}
.services-flyout-submenu {
    display: none;
    position: absolute;
    left: 100%;
    top: 0;
    min-width: 290px;
    background: #FFFFFF;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.08);
    box-shadow: 0 18px 40px rgba(0,0,0,0.14);
    padding: 8px 0;
    z-index: 1060;
}
@media (min-width: 992px) {
    .services-nav-dropdown:hover > .services-menu-parent {
        display: block;
    }
    .services-flyout-item:hover > .services-flyout-submenu {
        display: block;
    }
}
@media (max-width: 991.98px) {
    .services-flyout-submenu {
        position: static;
        box-shadow: none;
        border: none;
        padding-left: 16px;
        background: #F8FAFC;
    }
    .services-flyout-item:hover > .services-flyout-submenu,
    .services-flyout-item.open > .services-flyout-submenu {
        display: block;
    }
}
.services-sub-item-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 18px;
    font-size: 0.88rem;
    color: #475569;
    text-decoration: none;
    transition: all 0.15s ease;
}
.services-sub-item-link:hover {
    color: #0A6FB7;
    background: #F0F9FF;
    padding-left: 22px;
}
</style>
