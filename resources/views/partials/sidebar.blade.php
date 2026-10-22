<aside class="sidebar" id="sidebar">
    <div class="brand text-center py-3 px-2 border-bottom">
        <a href="{{ auth()->check() && auth()->user()->isSuperAdmin() ? route('super_admin.dashboard') : route('dashboard') }}" class="text-decoration-none">
            @php
                $displayLogo = !empty($setting->company_logo) 
                    ? asset($setting->company_logo) 
                    : (!empty($currentShop->logo) ? asset($currentShop->logo) : asset('uploads/logo/newsign-logo.png'));
            @endphp
            <img src="{{ $displayLogo }}" alt="Logo" class="img-fluid mb-2" style="max-height: 65px; object-fit: contain;">
            <div class="fw-bold text-dark small text-truncate px-2" style="font-size: 0.85rem;" title="{{ $currentShop->name ?? config('app.name') }}">
                {{ $currentShop->name ?? config('app.name', 'Quote System') }}
            </div>
        </a>
    </div>

    <nav class="sidebar-nav">
        @if(auth()->check() && auth()->user()->isSuperAdmin())
        <!-- Super Admin Control Panel -->
        <div class="px-3 pt-2 pb-1 text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">
            <i class="fas fa-crown text-warning me-1"></i> Super Admin
        </div>

        <a href="{{ route('super_admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('super_admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt text-warning"></i>
            <span>Platform Overview</span>
        </a>

        <a href="{{ route('super_admin.shops.index') }}" class="sidebar-link {{ request()->routeIs('super_admin.shops.*') ? 'active' : '' }}">
            <i class="fas fa-store text-primary"></i>
            <span>Manage Shops</span>
        </a>

        <a href="{{ route('super_admin.users.index') }}" class="sidebar-link {{ request()->routeIs('super_admin.users.*') ? 'active' : '' }}">
            <i class="fas fa-users-cog text-info"></i>
            <span>Platform Users</span>
        </a>

        <hr class="my-2 text-secondary opacity-25">
        @endif

        <!-- Shop Operations Section -->
        @if(auth()->check() && auth()->user()->isSuperAdmin())
        <div class="px-3 pt-1 pb-1 text-uppercase text-muted fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">
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
            <span>Departments & Units</span>
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

        <a href="{{ route('recycle_bin.index') }}" class="sidebar-link {{ request()->routeIs('recycle_bin.*') ? 'active' : '' }}">
            <i class="fas fa-trash-alt"></i>
            <span>Recycle Bin</span>
        </a>

        <a href="{{ route('system_logs.index') }}"
            class="sidebar-link {{ request()->routeIs('system_logs.*') ? 'active' : '' }}">
            <i class="fas fa-history"></i>
            <span>Activity Logs</span>
        </a>

        @if(auth()->check() && in_array(auth()->user()->role, ['admin', 'super_admin']))
        <a href="{{ route('settings.index') }}"
            class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Shop Settings</span>
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
