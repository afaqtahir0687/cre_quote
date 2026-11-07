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
                    <img src="{{ $platLogo }}" alt="Platform Logo" style="max-height: 40px; max-width: 140px; object-fit: contain;">
                @else
                    <div class="d-inline-flex align-items-center justify-content-center bg-warning text-dark rounded-3 shadow-sm flex-shrink-0"
                        style="width: 38px; height: 38px; font-size: 1.2rem;">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-white small text-truncate" style="font-size: 0.88rem;" title="{{ platform_brand_name() }}">
                            {{ platform_brand_name() }}
                        </div>
                        <span class="badge bg-warning text-dark extra-small py-0 px-1" style="font-size: 0.65rem;">
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
                    <img src="{{ $sidebarLogo }}" alt="{{ $sidebarTitle }}" style="max-height: 40px; max-width: 140px; object-fit: contain;">
                @else
                    <div class="d-inline-flex align-items-center justify-content-center text-white rounded-3 shadow-sm flex-shrink-0"
                        style="width: 38px; height: 38px; font-size: 1.2rem; background: var(--erp-primary);">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-white small text-truncate" style="font-size: 0.88rem;" title="{{ $sidebarTitle }}">
                            {{ $sidebarTitle }}
                        </div>
                        @if($currentShop)
                            <span class="badge bg-light text-dark extra-small py-0 px-1" style="font-size: 0.65rem;">
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

            @php $isSuperActive = request()->routeIs('super_admin.*'); @endphp
            <a href="#menuSuperAdmin" class="sidebar-link sidebar-parent {{ $isSuperActive ? 'active' : '' }}"
               data-bs-toggle="collapse" role="button" aria-expanded="{{ $isSuperActive ? 'true' : 'false' }}" aria-controls="menuSuperAdmin">
                <div class="sidebar-parent-label">
                    <i class="fas fa-shield-alt text-warning"></i>
                    <span>{{ __('Platform Admin') }}</span>
                </div>
                <i class="fas fa-chevron-right sidebar-arrow"></i>
            </a>
            <div class="collapse sidebar-submenu {{ $isSuperActive ? 'show' : '' }}" id="menuSuperAdmin">
                <a href="{{ route('super_admin.dashboard') }}" class="submenu-link {{ request()->routeIs('super_admin.dashboard') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('Overview') }}</span>
                </a>
                <a href="{{ route('super_admin.shops.index') }}" class="submenu-link {{ request()->routeIs('super_admin.shops.*') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('Manage Shops') }}</span>
                </a>
                <a href="{{ route('super_admin.users.index') }}" class="submenu-link {{ request()->routeIs('super_admin.users.*') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('System Users') }}</span>
                </a>
                <a href="{{ route('super_admin.subscriptions.index') }}" class="submenu-link {{ request()->routeIs('super_admin.subscriptions.*') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('Subscriptions') }}</span>
                </a>
                <a href="{{ route('super_admin.packages.index') }}" class="submenu-link {{ request()->routeIs('super_admin.packages.*') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('SaaS Packages') }}</span>
                </a>
                <a href="{{ route('super_admin.platform_settings') }}" class="submenu-link {{ request()->routeIs('super_admin.platform_settings') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('Branding & Mail') }}</span>
                </a>
            </div>
        @endif

        <!-- ================= ACTIVE SHOP OPERATIONS ================= -->
        <div class="sidebar-group-title">
            <i class="fas fa-briefcase me-1"></i> {{ __('Business Operations') }}
        </div>

        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i>
            <span>{{ __('Dashboard') }}</span>
        </a>

        <!-- Job Orders Dropdown -->
        @php $isJobActive = request()->routeIs('job-orders.*'); @endphp
        <a href="#menuJobOrders" class="sidebar-link sidebar-parent {{ $isJobActive ? 'active' : '' }}"
           data-bs-toggle="collapse" role="button" aria-expanded="{{ $isJobActive ? 'true' : 'false' }}" aria-controls="menuJobOrders">
            <div class="sidebar-parent-label">
                <i class="fas fa-tasks"></i>
                <span>{{ __('Job Orders') }}</span>
            </div>
            <i class="fas fa-chevron-right sidebar-arrow"></i>
        </a>
        <div class="collapse sidebar-submenu {{ $isJobActive ? 'show' : '' }}" id="menuJobOrders">
            <a href="{{ route('job-orders.index') }}" class="submenu-link {{ request()->routeIs('job-orders.index') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('All Job Orders') }}</span>
            </a>
            <a href="{{ route('job-orders.create') }}" class="submenu-link {{ request()->routeIs('job-orders.create') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('Create Job Order') }}</span>
            </a>
        </div>

        <!-- Quotations Dropdown -->
        @php $isQuoteActive = request()->routeIs('quotes.*'); @endphp
        <a href="#menuQuotes" class="sidebar-link sidebar-parent {{ $isQuoteActive ? 'active' : '' }}"
           data-bs-toggle="collapse" role="button" aria-expanded="{{ $isQuoteActive ? 'true' : 'false' }}" aria-controls="menuQuotes">
            <div class="sidebar-parent-label">
                <i class="fas fa-file-invoice"></i>
                <span>{{ __('Quotations') }}</span>
            </div>
            <i class="fas fa-chevron-right sidebar-arrow"></i>
        </a>
        <div class="collapse sidebar-submenu {{ $isQuoteActive ? 'show' : '' }}" id="menuQuotes">
            <a href="{{ route('quotes.index') }}" class="submenu-link {{ request()->routeIs('quotes.index') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('All Quotations') }}</span>
            </a>
            <a href="{{ route('quotes.create') }}" class="submenu-link {{ request()->routeIs('quotes.create') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('Create Quote') }}</span>
            </a>
        </div>

        <!-- Invoices Dropdown -->
        @php $isInvoiceActive = request()->routeIs('invoices.*'); @endphp
        <a href="#menuInvoices" class="sidebar-link sidebar-parent {{ $isInvoiceActive ? 'active' : '' }}"
           data-bs-toggle="collapse" role="button" aria-expanded="{{ $isInvoiceActive ? 'true' : 'false' }}" aria-controls="menuInvoices">
            <div class="sidebar-parent-label">
                <i class="fas fa-receipt"></i>
                <span>{{ __('Invoices') }}</span>
            </div>
            <i class="fas fa-chevron-right sidebar-arrow"></i>
        </a>
        <div class="collapse sidebar-submenu {{ $isInvoiceActive ? 'show' : '' }}" id="menuInvoices">
            <a href="{{ route('invoices.index') }}" class="submenu-link {{ request()->routeIs('invoices.index') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('All Invoices') }}</span>
            </a>
            <a href="{{ route('invoices.create') }}" class="submenu-link {{ request()->routeIs('invoices.create') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('Create Invoice') }}</span>
            </a>
        </div>

        <!-- Delivery Notes Dropdown -->
        @php $isDeliveryActive = request()->routeIs('delivery_notes.*'); @endphp
        <a href="#menuDeliveryNotes" class="sidebar-link sidebar-parent {{ $isDeliveryActive ? 'active' : '' }}"
           data-bs-toggle="collapse" role="button" aria-expanded="{{ $isDeliveryActive ? 'true' : 'false' }}" aria-controls="menuDeliveryNotes">
            <div class="sidebar-parent-label">
                <i class="fas fa-truck"></i>
                <span>{{ __('Delivery Notes') }}</span>
            </div>
            <i class="fas fa-chevron-right sidebar-arrow"></i>
        </a>
        <div class="collapse sidebar-submenu {{ $isDeliveryActive ? 'show' : '' }}" id="menuDeliveryNotes">
            <a href="{{ route('delivery_notes.index') }}" class="submenu-link {{ request()->routeIs('delivery_notes.index') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('All Delivery Notes') }}</span>
            </a>
            <a href="{{ route('delivery_notes.create') }}" class="submenu-link {{ request()->routeIs('delivery_notes.create') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('Create Delivery Note') }}</span>
            </a>
        </div>

        <!-- ================= CRM & INVENTORY ================= -->
        <div class="sidebar-group-title">
            <i class="fas fa-warehouse me-1"></i> {{ __('CRM & Catalog') }}
        </div>

        <!-- Customers Dropdown -->
        @php $isCustActive = request()->routeIs('customers.*'); @endphp
        <a href="#menuCustomers" class="sidebar-link sidebar-parent {{ $isCustActive ? 'active' : '' }}"
           data-bs-toggle="collapse" role="button" aria-expanded="{{ $isCustActive ? 'true' : 'false' }}" aria-controls="menuCustomers">
            <div class="sidebar-parent-label">
                <i class="fas fa-user-friends"></i>
                <span>{{ __('Customers') }}</span>
            </div>
            <i class="fas fa-chevron-right sidebar-arrow"></i>
        </a>
        <div class="collapse sidebar-submenu {{ $isCustActive ? 'show' : '' }}" id="menuCustomers">
            <a href="{{ route('customers.index') }}" class="submenu-link {{ request()->routeIs('customers.index') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('All Customers') }}</span>
            </a>
            <a href="{{ route('customers.create') }}" class="submenu-link {{ request()->routeIs('customers.create') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('Add Customer') }}</span>
            </a>
        </div>

        <!-- Products & Catalog Dropdown -->
        @php $isCatalogActive = request()->routeIs('products.*') || request()->routeIs('categories.*') || request()->routeIs('units.*'); @endphp
        <a href="#menuCatalog" class="sidebar-link sidebar-parent {{ $isCatalogActive ? 'active' : '' }}"
           data-bs-toggle="collapse" role="button" aria-expanded="{{ $isCatalogActive ? 'true' : 'false' }}" aria-controls="menuCatalog">
            <div class="sidebar-parent-label">
                <i class="fas fa-boxes"></i>
                <span>{{ __('Products & Catalog') }}</span>
            </div>
            <i class="fas fa-chevron-right sidebar-arrow"></i>
        </a>
        <div class="collapse sidebar-submenu {{ $isCatalogActive ? 'show' : '' }}" id="menuCatalog">
            <a href="{{ route('products.index') }}" class="submenu-link {{ request()->routeIs('products.index') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('All Products') }}</span>
            </a>
            <a href="{{ route('products.create') }}" class="submenu-link {{ request()->routeIs('products.create') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('Add Product / Service') }}</span>
            </a>
            <a href="{{ route('categories.index') }}" class="submenu-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('Categories') }}</span>
            </a>
            <a href="{{ route('units.index') }}" class="submenu-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('Units') }}</span>
            </a>
        </div>

        <!-- ================= STAFF & WORKSHOP ================= -->
        <div class="sidebar-group-title">
            <i class="fas fa-users-cog me-1"></i> {{ __('HRM & Workshop') }}
        </div>

        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
            @php $isEmployeeActive = request()->routeIs('employees.*'); @endphp
            <a href="#menuEmployees" class="sidebar-link sidebar-parent {{ $isEmployeeActive ? 'active' : '' }}"
               data-bs-toggle="collapse" role="button" aria-expanded="{{ $isEmployeeActive ? 'true' : 'false' }}" aria-controls="menuEmployees">
                <div class="sidebar-parent-label">
                    <i class="fas fa-id-badge"></i>
                    <span>{{ __('Employees') }}</span>
                </div>
                <i class="fas fa-chevron-right sidebar-arrow"></i>
            </a>
            <div class="collapse sidebar-submenu {{ $isEmployeeActive ? 'show' : '' }}" id="menuEmployees">
                <a href="{{ route('employees.index') }}" class="submenu-link {{ request()->routeIs('employees.index') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('Staff List') }}</span>
                </a>
                <a href="{{ route('employees.create') }}" class="submenu-link {{ request()->routeIs('employees.create') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('Add Employee') }}</span>
                </a>
            </div>
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
            @php $isSettingsActive = request()->routeIs('branches.*') || request()->routeIs('subscription.*') || request()->routeIs('settings.*'); @endphp
            <a href="#menuSettings" class="sidebar-link sidebar-parent {{ $isSettingsActive ? 'active' : '' }}"
               data-bs-toggle="collapse" role="button" aria-expanded="{{ $isSettingsActive ? 'true' : 'false' }}" aria-controls="menuSettings">
                <div class="sidebar-parent-label">
                    <i class="fas fa-cog"></i>
                    <span>{{ __('Shop Settings') }}</span>
                </div>
                <i class="fas fa-chevron-right sidebar-arrow"></i>
            </a>
            <div class="collapse sidebar-submenu {{ $isSettingsActive ? 'show' : '' }}" id="menuSettings">
                <a href="{{ route('branches.index') }}" class="submenu-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('Branches / Multi-Shop') }}</span>
                </a>
                <a href="{{ route('subscription.index') }}" class="submenu-link {{ request()->routeIs('subscription.*') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('My Subscription') }}</span>
                </a>
                <a href="{{ route('settings.index') }}" class="submenu-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('General Settings') }}</span>
                </a>
            </div>
        @endif

        @php $isMaintActive = request()->routeIs('recycle_bin.*') || request()->routeIs('system_logs.*'); @endphp
        <a href="#menuMaintenance" class="sidebar-link sidebar-parent {{ $isMaintActive ? 'active' : '' }}"
           data-bs-toggle="collapse" role="button" aria-expanded="{{ $isMaintActive ? 'true' : 'false' }}" aria-controls="menuMaintenance">
            <div class="sidebar-parent-label">
                <i class="fas fa-tools"></i>
                <span>{{ __('System & Logs') }}</span>
            </div>
            <i class="fas fa-chevron-right sidebar-arrow"></i>
        </a>
        <div class="collapse sidebar-submenu {{ $isMaintActive ? 'show' : '' }}" id="menuMaintenance">
            <a href="{{ route('recycle_bin.index') }}" class="submenu-link {{ request()->routeIs('recycle_bin.*') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('Recycle Bin') }}</span>
            </a>
            <a href="{{ route('system_logs.index') }}" class="submenu-link {{ request()->routeIs('system_logs.*') ? 'active' : '' }}">
                <span class="submenu-bullet"></span>
                <span>{{ __('System Logs') }}</span>
            </a>
        </div>
    </nav>
</aside>
<div class="sidebar-backdrop d-none" id="sidebarBackdrop"></div>

{{-- Resilient Vanilla JS Accordion Engine --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    var parents = document.querySelectorAll('.sidebar-parent');
    parents.forEach(function(parent) {
        parent.addEventListener('click', function(e) {
            var targetId = this.getAttribute('href');
            if (targetId && targetId.startsWith('#')) {
                var targetEl = document.querySelector(targetId);
                if (targetEl && typeof bootstrap === 'undefined') {
                    // Fallback if bootstrap is not loaded
                    e.preventDefault();
                    var isShown = targetEl.classList.contains('show');
                    targetEl.classList.toggle('show', !isShown);
                    this.setAttribute('aria-expanded', !isShown ? 'true' : 'false');
                }
            }
        });
    });
});
</script>
