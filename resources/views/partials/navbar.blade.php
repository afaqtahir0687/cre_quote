<nav class="top-navbar">
    <div class="d-flex align-items-center">
        <button class="btn btn-link link-dark d-lg-none me-3" id="sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <h4 class="mb-0 text-dark fw-bold">@yield('title', 'Welcome')</h4>
    </div>

    <div class="d-flex align-items-center gap-3">
        @if(auth()->check() && auth()->user()->isSuperAdmin())
            <!-- Super Admin Multi-Shop Switcher Dropdown -->
            <div class="dropdown">
                <button class="btn btn-light border btn-sm dropdown-toggle d-flex align-items-center gap-2 px-3 py-2 shadow-sm rounded-pill" type="button" id="shopSwitcherDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-store text-primary"></i>
                    <span class="fw-semibold text-dark">
                        @if(session('active_shop_id') && session('active_shop_id') !== 'all')
                            {{ $currentShop->name ?? 'Active Shop' }}
                        @else
                            All Shops (Global View)
                        @endif
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2" aria-labelledby="shopSwitcherDropdown" style="min-width: 260px;">
                    <li class="dropdown-header small text-uppercase fw-bold text-muted">
                        <i class="fas fa-exchange-alt me-1"></i> Switch Active Shop
                    </li>
                    <li>
                        <form action="{{ route('super_admin.switch_shop_all') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item rounded py-2 d-flex align-items-center justify-content-between {{ (!session('active_shop_id') || session('active_shop_id') === 'all') ? 'active text-white' : '' }}">
                                <span><i class="fas fa-globe me-2"></i> All Shops (Super Admin)</span>
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
                                    <span class="text-truncate" style="max-width: 190px;">
                                        <i class="fas fa-store-alt me-2 text-primary"></i> {{ $s->name }}
                                    </span>
                                    @if(session('active_shop_id') == $s->id) <i class="fas fa-check"></i> @endif
                                </button>
                            </form>
                        </li>
                        @endforeach
                    @endif
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item rounded py-2 text-primary fw-semibold" href="{{ route('super_admin.shops.index') }}">
                            <i class="fas fa-cog me-2"></i> Manage All Shops
                        </a>
                    </li>
                </ul>
            </div>
        @elseif(auth()->check() && $currentShop)
            <!-- Shop Brand Indicator for Branch Admin & Staff -->
            <div class="d-none d-md-flex align-items-center px-3 py-1 bg-light rounded-pill border">
                <i class="fas fa-store text-primary me-2"></i>
                <span class="small fw-semibold text-dark">{{ $currentShop->name }}</span>
            </div>
        @endif

        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="userDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                
                <span class="d-none d-md-inline text-dark fw-medium">
                    {{ auth()->check() ? Auth::user()->name : 'Guest' }}
                    @if(auth()->check() && Auth::user()->isSuperAdmin())
                        <span class="badge-soft badge-soft-warning ms-1"><i class="fas fa-crown"></i> Super Admin</span>
                    @elseif(auth()->check() && Auth::user()->isAdmin())
                        <span class="badge-soft badge-soft-primary ms-1">Shop Admin</span>
                    @endif
                </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3" aria-labelledby="userDropdown">
                @if(auth()->check() && Auth::user()->isSuperAdmin())
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('super_admin.dashboard') }}">
                            <i class="fas fa-crown text-warning me-2"></i>Super Admin Portal
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('super_admin.shops.index') }}">
                            <i class="fas fa-store me-2 text-primary"></i>Manage Shops
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('super_admin.users.index') }}">
                            <i class="fas fa-users-cog me-2 text-info"></i>Platform Users
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                @endif

                @if(auth()->check() && (Auth::user()->isAdmin() || Auth::user()->isSuperAdmin()))
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('settings.index') }}">
                            <i class="fas fa-cog me-2"></i>Shop Settings
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                @endif
                
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item py-2 text-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>