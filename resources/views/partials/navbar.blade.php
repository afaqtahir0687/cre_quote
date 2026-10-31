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
                @php
                    $isSpecificShop = session('active_shop_id') && session('active_shop_id') !== 'all';
                    $activeShopLogo = $isSpecificShop ? shop_logo_url($currentShop, $setting) : null;
                @endphp
                <button class="btn btn-light border btn-sm dropdown-toggle d-flex align-items-center gap-2 px-3 py-2 shadow-sm rounded-pill" type="button" id="shopSwitcherDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    @if($activeShopLogo)
                        <img src="{{ $activeShopLogo }}" alt="Logo" class="rounded-circle" style="width: 22px; height: 22px; object-fit: contain;">
                    @else
                        <i class="fas fa-store text-primary"></i>
                    @endif
                    <span class="fw-semibold text-dark">
                        @if($isSpecificShop)
                            {{ $currentShop->name ?? 'Active Shop' }}
                        @else
                            All Shops (Global View)
                        @endif
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2" aria-labelledby="shopSwitcherDropdown" style="min-width: 280px;">
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
                        @php
                            $sLogo = !empty($s->logo) && file_exists(public_path($s->logo)) ? asset($s->logo) : null;
                        @endphp
                        <li>
                            <form action="{{ route('super_admin.switch_shop', $s->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item rounded py-2 d-flex align-items-center justify-content-between {{ (session('active_shop_id') == $s->id) ? 'active text-white' : '' }}">
                                    <span class="text-truncate d-flex align-items-center gap-2" style="max-width: 200px;">
                                        @if($sLogo)
                                            <img src="{{ $sLogo }}" alt="{{ $s->name }}" class="rounded border" style="width: 20px; height: 20px; object-fit: contain;">
                                        @else
                                            <i class="fas fa-store-alt text-primary"></i>
                                        @endif
                                        <span>{{ $s->name }}</span>
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
            @php
                $staffShopLogo = shop_logo_url($currentShop, $setting);
                $userShop = auth()->user()->shop;
                $mainShop = ($userShop && $userShop->parent_shop_id && $userShop->parentShop) ? $userShop->parentShop : $userShop;
                $hasBranches = auth()->user()->isAdmin() && $mainShop && ($mainShop->branches->isNotEmpty() || $mainShop->isBusinessAccount());
            @endphp

            @if($hasBranches)
                <!-- Multi-Shop Branch Switcher for Business Admin -->
                <div class="dropdown me-2">
                    <button class="btn btn-outline-primary btn-sm dropdown-toggle rounded-pill px-3 shadow-none d-flex align-items-center gap-2" 
                            type="button" id="branchSwitcherDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-building text-primary"></i>
                        <span class="small fw-semibold">{{ $currentShop->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-2" aria-labelledby="branchSwitcherDropdown" style="min-width: 230px;">
                        <li class="dropdown-header small text-uppercase text-muted fw-bold">My Business Branches</li>
                        <li>
                            <form action="{{ route('branches.switch', $mainShop->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item rounded py-2 d-flex align-items-center justify-content-between {{ ($currentShop->id == $mainShop->id) ? 'active text-white' : '' }}">
                                    <span>{{ $mainShop->name }} (HQ)</span>
                                    @if($currentShop->id == $mainShop->id) <i class="fas fa-check"></i> @endif
                                </button>
                            </form>
                        </li>
                        @foreach($mainShop->branches as $b)
                            <li>
                                <form action="{{ route('branches.switch', $b->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item rounded py-2 d-flex align-items-center justify-content-between {{ ($currentShop->id == $b->id) ? 'active text-white' : '' }}">
                                        <span>{{ $b->name }}</span>
                                        @if($currentShop->id == $b->id) <i class="fas fa-check"></i> @endif
                                    </button>
                                </form>
                            </li>
                        @endforeach
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item rounded py-2 text-primary fw-semibold" href="{{ route('branches.index') }}">
                                <i class="fas fa-cog me-2"></i> Manage Branches
                            </a>
                        </li>
                    </ul>
                </div>
            @else
                <!-- Single Shop Brand Indicator for Branch Admin & Staff -->
                <div class="d-none d-md-flex align-items-center px-3 py-1 bg-light rounded-pill border gap-2">
                    @if($staffShopLogo)
                        <img src="{{ $staffShopLogo }}" alt="{{ $currentShop->name }}" class="rounded-circle" style="width: 22px; height: 22px; object-fit: contain;">
                    @else
                        <i class="fas fa-store text-primary"></i>
                    @endif
                    <span class="small fw-semibold text-dark">{{ $currentShop->name }}</span>
                </div>
            @endif
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

                @if(auth()->check() && Auth::user()->isSuperAdmin())
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('super_admin.platform_settings') }}">
                            <i class="fas fa-paint-brush me-2 text-warning"></i>Platform Branding
                        </a>
                    </li>
                    @if(session('active_shop_id') && session('active_shop_id') !== 'all')
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('settings.index') }}">
                                <i class="fas fa-cog me-2 text-primary"></i>Setting
                            </a>
                        </li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                @elseif(auth()->check() && Auth::user()->isAdmin())
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('settings.index') }}">
                            <i class="fas fa-cog me-2"></i>Setting
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