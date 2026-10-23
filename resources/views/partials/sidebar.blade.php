<aside class="sidebar" id="sidebar">
    <div class="brand text-center py-3 px-2 border-bottom">
        @php
            $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
            $isGlobalView = $isSuperAdmin && (!session('active_shop_id') || session('active_shop_id') === 'all');
            $isInSuperAdminSection = $isSuperAdmin && request()->routeIs('super_admin.*');
        @endphp

        @if($isSuperAdmin && ($isGlobalView || $isInSuperAdminSection))
            {{-- Super Admin in Global Platform View or Super Admin Section: ALWAYS show Platform / Crelogics Logo --}}
            <a href="{{ route('super_admin.dashboard') }}" class="text-decoration-none d-block">
                @php $platLogo = platform_logo_url(); @endphp
                @if($platLogo)
                    <img src="{{ $platLogo }}" alt="Platform Logo" class="img-fluid mb-2"
                        style="max-height: 60px; max-width: 180px; object-fit: contain;">
                @else
                    <div class="d-inline-flex align-items-center justify-content-center bg-warning text-dark rounded-3 mb-2 shadow-sm"
                        style="width: 52px; height: 52px; font-size: 1.5rem;">
                        <i class="fas fa-crown"></i>
                    </div>
                @endif
                <div class="fw-bold text-dark small text-truncate px-2" style="font-size: 0.85rem;"
                    title="{{ platform_brand_name() }}">
                    {{ platform_brand_name() }}
                </div>
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle mt-1"
                    style="font-size: 0.65rem; font-weight: 500;">
                    <i class="fas fa-crown text-warning me-1"></i> Super Admin Portal
                </span>
            </a>
        @else
            {{-- Shop Context (Shop Admin, Employee, or Super Admin switched to a specific Shop) --}}
            <a href="{{ route('dashboard') }}" class="text-decoration-none d-block">
                @php
                    $sidebarLogo = shop_logo_url($currentShop, $setting);
                    $sidebarTitle = shop_brand_name($currentShop, $setting);
                @endphp

                @if($sidebarLogo)
                    <img src="{{ $sidebarLogo }}" alt="{{ $sidebarTitle }}" class="img-fluid mb-2"
                        style="max-height: 60px; max-width: 180px; object-fit: contain;">
                @else
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-3 mb-2 shadow-sm"
                        style="width: 52px; height: 52px; font-size: 1.5rem;">
                        <i class="fas fa-store"></i>
                    </div>
                @endif

                <div class="fw-bold text-dark small text-truncate px-2" style="font-size: 0.85rem;"
                    title="{{ $sidebarTitle }}">
                    {{ $sidebarTitle }}
                </div>

                @if($currentShop)
                    <span class="badge bg-light text-secondary border mt-1" style="font-size: 0.65rem; font-weight: 500;">
                        <i class="fas fa-store text-primary me-1"></i> Branch #{{ $currentShop->id }}
                    </span>
                @endif
            </a>
        @endif
    </div>

    <nav class="sidebar-nav">
        @if(auth()->check() && auth()->user()->isSuperAdmin())
            <!-- Super Admin Control Panel -->
            <div class="px-3 pt-2 pb-1 text-uppercase text-muted fw-bold"
                style="font-size: 0.7rem; letter-spacing: 0.05em;">
                <i class="fas fa-crown text-warning me-1"></i> Super Admin
            </div>

            <a href="{{ route('super_admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt text-warning"></i>
                <span>Platform Overview</span>
            </a>

            <a href="{{ route('super_admin.shops.index') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.shops.*') ? 'active' : '' }}">
                <i class="fas fa-store text-primary"></i>
                <span>Manage Shops</span>
            </a>

            <a href="{{ route('super_admin.users.index') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog text-info"></i>
                <span>Platform Users</span>
            </a>

            <a href="{{ route('super_admin.platform_settings') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.platform_settings*') ? 'active' : '' }}">
                <i class="fas fa-paint-brush text-warning"></i>
                <span>Platform Branding</span>
            </a>

            <hr class="my-2 text-secondary opacity-25">
        @endif

        <!-- Shop Operations Section -->
        @if(auth()->check() && auth()->user()->isSuperAdmin())
            <div class="px-3 pt-1 pb-1 text-uppercase text-muted fw-bold"
                style="font-size: 0.7rem; letter-spacing: 0.05em;">
                <i class="fas fa-store-alt text-secondary me-1"></i> Active Shop
            </div>
        @endif

        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'super_admin']))
            <a href="{{ route('employees.index') }}"
                class="sidebar-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <i class="fas fa-user-tie"></i>
                <span>Employees</span>
            </a>
        @endif

        <a href="{{ route('customers.index') }}"
            class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Customers</span>
        </a>

        <a href="{{ route('quotes.index') }}" class="sidebar-link {{ request()->routeIs('quotes.*') ? 'active' : '' }}">
            <i class="fas fa-file-invoice"></i>
            <span>Quotes</span>
        </a>

        <a href="{{ route('job-orders.index') }}"
            class="sidebar-link {{ request()->routeIs('job-orders.*') ? 'active' : '' }}">
            <i class="fas fa-tasks"></i>
            <span>Job Orders</span>
        </a>

        <a href="{{ route('departments.index') }}"
            class="sidebar-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
            <i class="fas fa-sitemap"></i>
            <span>Department</span>
        </a>

        <a href="{{ route('invoices.index') }}"
            class="sidebar-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i>
            <span>Invoices</span>
        </a>

        <a href="{{ route('delivery_notes.index') }}"
            class="sidebar-link {{ request()->routeIs('delivery_notes.*') ? 'active' : '' }}">
            <i class="fas fa-truck"></i>
            <span>Delivery Notes</span>
        </a>

        <a href="{{ route('products.index') }}"
            class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="fas fa-box"></i>
            <span>Products</span>
        </a>

        <hr class="my-3 text-secondary opacity-25">

        <a href="{{ route('recycle_bin.index') }}"
            class="sidebar-link {{ request()->routeIs('recycle_bin.*') ? 'active' : '' }}">
            <i class="fas fa-trash-alt"></i>
            <span>Recycle Bin</span>
        </a>

        <a href="{{ route('system_logs.index') }}"
            class="sidebar-link {{ request()->routeIs('system_logs.*') ? 'active' : '' }}">
            <i class="fas fa-history"></i>
            <span>Activity Logs</span>
        </a>

        @if(auth()->check() && auth()->user()->isSuperAdmin())
            @if(session('active_shop_id') && session('active_shop_id') !== 'all')
                <a href="{{ route('settings.index') }}"
                    class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i>
                    <span>Setting</span>
                </a>
            @else
                <a href="{{ route('super_admin.platform_settings') }}"
                    class="sidebar-link {{ request()->routeIs('super_admin.platform_settings*') ? 'active' : '' }}">
                    <i class="fas fa-paint-brush text-warning"></i>
                    <span>Platform Branding</span>
                </a>
            @endif
        @elseif(auth()->check() && auth()->user()->isAdmin())
            <a href="{{ route('settings.index') }}"
                class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>Setting</span>
            </a>
        @endif

        <div class="mt-auto pt-4 px-3 mb-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </button>
            </form>
        </div>
    </nav>
</aside>