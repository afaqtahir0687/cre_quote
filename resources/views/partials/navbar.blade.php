<nav class="top-navbar">
    <div class="d-flex align-items-center gap-3">
        <!-- Sidebar Mobile Toggle -->
        <button class="btn btn-light btn-icon border d-lg-none shadow-none" id="sidebar-toggle" type="button" title="{{ __('Toggle Menu') }}">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Current View Title & Dynamic Breadcrumb -->
        <div>
            <h5 class="mb-0 text-dark fw-bold" style="font-size: 1.05rem;">
                {!! html_entity_decode(e(__(trim($__env->yieldContent('title', 'Dashboard')))), ENT_QUOTES, 'UTF-8') !!}
            </h5>
            <div class="d-none d-md-flex align-items-center gap-1 text-muted extra-small" style="font-size: 0.75rem;">
                <a href="{{ route('dashboard') }}" class="text-muted"><i class="fas fa-home me-1"></i>{{ __('Home') }}</a>
                <span>/</span>
                <span class="text-primary fw-medium">{!! html_entity_decode(e(__(trim($__env->yieldContent('title', 'Overview')))), ENT_QUOTES, 'UTF-8') !!}</span>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 gap-md-3">
        <!-- Super Admin Multi-Shop Switcher -->
        @if(auth()->check() && auth()->user()->isSuperAdmin())
            <div class="dropdown">
                @php
                    $isSpecificShop = session('active_shop_id') && session('active_shop_id') !== 'all';
                    $activeShopLogo = $isSpecificShop ? shop_logo_url($currentShop, $setting) : null;
                @endphp
                <button class="btn btn-light border btn-sm dropdown-toggle d-flex align-items-center gap-2 px-3 py-1 rounded-pill shadow-none" type="button" id="shopSwitcherDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    @if($activeShopLogo)
                        <img src="{{ $activeShopLogo }}" alt="Logo" class="rounded-circle" style="width: 22px; height: 22px; object-fit: contain;">
                    @else
                        <i class="fas fa-store text-primary"></i>
                    @endif
                    <span class="fw-semibold text-dark text-truncate d-none d-sm-inline" style="max-width: 140px;">
                        @if($isSpecificShop)
                            {{ $currentShop->name ?? __('Active Shop') }}
                        @else
                            {{ __('All Shops (Global)') }}
                        @endif
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2" aria-labelledby="shopSwitcherDropdown" style="min-width: 260px;">
                    <li class="dropdown-header small text-uppercase fw-bold text-muted">
                        <i class="fas fa-exchange-alt me-1"></i> {{ __('Switch Shop View') }}
                    </li>
                    <li>
                        <form action="{{ route('super_admin.switch_shop_all') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item rounded py-2 d-flex align-items-center justify-content-between {{ (!session('active_shop_id') || session('active_shop_id') === 'all') ? 'active text-white' : '' }}">
                                <span><i class="fas fa-globe me-2"></i> {{ __('All Shops (Global)') }}</span>
                                @if(!session('active_shop_id') || session('active_shop_id') === 'all') <i class="fas fa-check"></i> @endif
                            </button>
                        </form>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    @if(isset($allShops) && count($allShops) > 0)
                        @foreach($allShops as $s)
                        <li>
                            <form action="{{ route('super_admin.switch_shop', $s->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item rounded py-2 d-flex align-items-center justify-content-between {{ (session('active_shop_id') == $s->id) ? 'active text-white' : '' }}">
                                    <span class="text-truncate d-flex align-items-center gap-2" style="max-width: 180px;">
                                        <i class="fas fa-store text-secondary"></i>
                                        <span>{{ $s->name }}</span>
                                    </span>
                                    @if(session('active_shop_id') == $s->id) <i class="fas fa-check"></i> @endif
                                </button>
                            </form>
                        </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        @elseif(auth()->check() && $currentShop)
            <!-- Active Shop Indicator Pill -->
            <div class="d-none d-md-flex align-items-center gap-2 px-3 py-1 bg-light border rounded-pill small">
                <i class="fas fa-store text-primary"></i>
                <span class="fw-semibold text-dark text-truncate" style="max-width: 160px;">{{ $currentShop->name }}</span>
            </div>
        @endif

        <!-- Language Switcher Pill -->
        <div class="dropdown">
            <button class="btn btn-light border btn-sm dropdown-toggle d-flex align-items-center gap-1 px-2 py-1 rounded-pill shadow-none" type="button" data-bs-toggle="dropdown">
                @if(app()->getLocale() === 'ar')
                    <span class="fi fi-sa"></span>
                    <span class="small fw-semibold ms-1">العربية</span>
                @else
                    <span class="fi fi-gb"></span>
                    <span class="small fw-semibold ms-1">English</span>
                @endif
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-1" style="min-width: 130px;">
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 rounded {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">
                        <span class="fi fi-gb"></span>
                        <span>English</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 rounded {{ app()->getLocale() === 'ar' ? 'active' : '' }}" href="{{ route('language.switch', 'ar') }}">
                        <span class="fi fi-sa"></span>
                        <span>العربية</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- User Profile Dropdown -->
        @if(auth()->check())
            <div class="dropdown">
                <button class="btn btn-light border p-1 rounded-pill d-flex align-items-center gap-2 pe-3 shadow-none" type="button" data-bs-toggle="dropdown">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                        style="width: 32px; height: 32px; font-size: 0.85rem; background: var(--erp-primary);">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="text-start d-none d-sm-block leading-tight">
                        <div class="fw-bold text-dark small" style="line-height: 1.1;">{{ auth()->user()->name }}</div>
                        <span class="text-muted extra-small" style="font-size: 0.7rem;">
                            {{ ucfirst(auth()->user()->role ?? 'Admin') }}
                        </span>
                    </div>
                    <i class="fas fa-chevron-down text-muted ms-1" style="font-size: 0.65rem;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2" style="min-width: 220px;">
                    <li class="px-2 py-2 border-bottom mb-2">
                        <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
                        <div class="text-muted extra-small">{{ auth()->user()->email }}</div>
                    </li>
                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('settings.index') }}">
                                <i class="fas fa-cog me-2 text-primary"></i> {{ __('Shop Settings') }}
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()->isSuperAdmin())
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('super_admin.platform_settings') }}">
                                <i class="fas fa-paint-brush me-2 text-warning"></i> {{ __('System Settings') }}
                            </a>
                        </li>
                    @endif
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item py-2 text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endif
    </div>
</nav>
