<aside class="sidebar" id="sidebar">
    <div class="brand">
        <img src="{{ asset($setting->company_logo ?? 'uploads/logo/newsign-logo.png') }}" alt="Company Logo"
            class="img-fluid" style="max-height: 100px;">
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        @if(auth()->check() && auth()->user()->role === 'admin')
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

        @if(auth()->check() && auth()->user()->role === 'admin')
        <a href="{{ route('settings.index') }}"
            class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
        @endif

        <div class="mt-auto pt-4 px-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                </button>
            </form>
        </div>
    </nav>
</aside>

