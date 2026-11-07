<aside class="sidebar" id="sidebar">
    <div class="brand">
        @php
            $isSuperAdmin = auth()->check() && auth()->user()->isSuperAdmin();
            $isGlobalView = $isSuperAdmin && (!session('active_shop_id') || session('active_shop_id') === 'all');
            $isInSuperAdminSection = $isSuperAdmin && request()->routeIs('super_admin.*');
        @endphp

        @if($isSuperAdmin && ($isGlobalView || $isInSuperAdminSection))
            {{-- Super Admin in Global Platform View or Super Admin Section --}}
            <a href="{{ route('super_admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none w-100 overflow-hidden">
                @php $platLogo = platform_logo_url(); @endphp
                @if($platLogo)
                    <img src="{{ $platLogo }}" alt="Platform Logo" style="max-height: 42px; max-width: 140px; object-fit: contain;">
                @else
                    <div class="d-inline-flex align-items-center justify-content-center bg-warning text-dark rounded-3 shadow-sm flex-shrink-0"
                        style="width: 40px; height: 40px; font-size: 1.25rem;">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-white small text-truncate" style="font-size: 0.9rem;" title="{{ platform_brand_name() }}">
                            {{ platform_brand_name() }}
                        </div>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle extra-small py-0 px-1">
                            {{ __('Super Admin') }}
                        </span>
                    </div>
                @endif
            </a>
        @else
            {{-- Shop Context (Shop Admin, Employee, or Super Admin switched to a specific Shop) --}}
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none w-100 overflow-hidden">
                @php
                    $sidebarLogo = shop_logo_url($currentShop, $setting);
                    $sidebarTitle = shop_brand_name($currentShop, $setting);
                @endphp

                @if($sidebarLogo)
                    <img src="{{ $sidebarLogo }}" alt="{{ $sidebarTitle }}" style="max-height: 42px; max-width: 140px; object-fit: contain;">
                @else
                    <div class="d-inline-flex align-items-center justify-content-center text-white rounded-3 shadow-sm flex-shrink-0"
                        style="width: 40px; height: 40px; font-size: 1.25rem; background: var(--erp-primary);">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-white small text-truncate" style="font-size: 0.9rem;" title="{{ $sidebarTitle }}">
                            {{ $sidebarTitle }}
                        </div>
                        @if($currentShop)
                            <span class="badge bg-light text-dark extra-small py-0 px-1">
                                {{ __('Branch') }} #{{ $currentShop->id }}
                            </span>
                        @endif
                    </div>
                @endif
            </a>
        @endif
    </div>

    <nav class="sidebar-nav">
        @if(auth()->check() && auth()->user()->isSuperAdmin())
            <!-- ================= SUPER ADMIN CONTROL ================= -->
            <div class="sidebar-group-title">
                <i class="fas fa-crown text-warning me-1"></i> {{ __('Super Admin') }}
            </div>

            <a href="{{ route('super_admin.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>{{ __('Overview') }}</span>
            </a>

            <a href="{{ route('super_admin.shops.index') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.shops.*') ? 'active' : '' }}">
                <i class="fas fa-store"></i>
                <span>{{ __('Manage Shops') }}</span>
            </a>

            <a href="{{ route('super_admin.users.index') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i>
                <span>{{ __('Users') }}</span>
            </a>

            <a href="{{ route('super_admin.subscriptions.index') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.subscriptions.*') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-usd"></i>
                <span>{{ __('Subscriptions') }}</span>
            </a>

            <a href="{{ route('super_admin.packages.index') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.packages.*') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i>
                <span>{{ __('Packages') }}</span>
            </a>

            <a href="{{ route('super_admin.platform_settings') }}"
                class="sidebar-link {{ request()->routeIs('super_admin.platform_settings') ? 'active' : '' }}">
                <i class="fas fa-paint-brush"></i>
                <span>{{ __('Branding & Mail') }}</span>
            </a>
        @endif

        <!-- ================= ACTIVE SHOP OPERATIONS ================= -->
        <div class="sidebar-group-title">
            <i class="fas fa-briefcase me-1"></i> {{ __('Business Operations') }}
        </div>

        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i>
            <span>{{ __('Dashboard') }}</span>
        </a>

        <a href="{{ route('job-orders.index') }}"
            class="sidebar-link {{ request()->routeIs('job-orders.*') ? 'active' : '' }}">
            <i class="fas fa-tasks"></i>
            <span>{{ __('Job Orders') }}</span>
        </a>

        <a href="{{ route('quotes.index') }}"
            class="sidebar-link {{ request()->routeIs('quotes.*') ? 'active' : '' }}">
            <i class="fas fa-file-invoice"></i>
            <span>{{ __('Quotations') }}</span>
        </a>

        <a href="{{ route('invoices.index') }}"
            class="sidebar-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i>
            <span>{{ __('Invoices') }}</span>
        </a>

        <a href="{{ route('delivery_notes.index') }}"
            class="sidebar-link {{ request()->routeIs('delivery_notes.*') ? 'active' : '' }}">
            <i class="fas fa-truck"></i>
            <span>{{ __('Delivery Notes') }}</span>
        </a>

        <!-- ================= CRM & INVENTORY ================= -->
        <div class="sidebar-group-title">
            <i class="fas fa-warehouse me-1"></i> {{ __('CRM & Catalog') }}
        </div>

        <a href="{{ route('customers.index') }}"
            class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <i class="fas fa-user-friends"></i>
            <span>{{ __('Customers') }}</span>
        </a>

        <a href="{{ route('products.index') }}"
            class="sidebar-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="fas fa-box-open"></i>
            <span>{{ __('Products & Services') }}</span>
        </a>

        <a href="{{ route('categories.index') }}"
            class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
            <i class="fas fa-tags"></i>
            <span>{{ __('Categories') }}</span>
        </a>

        <a href="{{ route('units.index') }}"
            class="sidebar-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
            <i class="fas fa-balance-scale"></i>
            <span>{{ __('Units') }}</span>
        </a>

        <!-- ================= STAFF & WORKSHOP ================= -->
        <div class="sidebar-group-title">
            <i class="fas fa-users-cog me-1"></i> {{ __('HRM & Workshop') }}
        </div>

        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
            <a href="{{ route('employees.index') }}"
                class="sidebar-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <i class="fas fa-id-badge"></i>
                <span>{{ __('Employees') }}</span>
            </a>
        @endif

        <a href="{{ route('departments.index') }}"
            class="sidebar-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
            <i class="fas fa-cogs"></i>
            <span>{{ __('Departments / Units') }}</span>
        </a>

        <!-- ================= SHOP CONFIGURATION ================= -->
        <div class="sidebar-group-title">
            <i class="fas fa-sliders-h me-1"></i> {{ __('Configuration') }}
        </div>

        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
            <a href="{{ route('branches.index') }}"
                class="sidebar-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                <i class="fas fa-store-alt"></i>
                <span>{{ __('Branches / Multi-Shop') }}</span>
            </a>

            <a href="{{ route('subscription.index') }}"
                class="sidebar-link {{ request()->routeIs('subscription.*') ? 'active' : '' }}">
                <i class="fas fa-gem"></i>
                <span>{{ __('My Subscription') }}</span>
            </a>

            <a href="{{ route('settings.index') }}"
                class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i>
                <span>{{ __('Shop Settings') }}</span>
            </a>
        @endif

        <a href="{{ route('recycle_bin.index') }}"
            class="sidebar-link {{ request()->routeIs('recycle_bin.*') ? 'active' : '' }}">
            <i class="fas fa-trash-alt"></i>
            <span>{{ __('Recycle Bin') }}</span>
        </a>

        <a href="{{ route('system_logs.index') }}"
            class="sidebar-link {{ request()->routeIs('system_logs.*') ? 'active' : '' }}">
            <i class="fas fa-history"></i>
            <span>{{ __('System Logs') }}</span>
        </a>
    </nav>
</aside>
<div class="sidebar-backdrop d-none" id="sidebarBackdrop"></div>
