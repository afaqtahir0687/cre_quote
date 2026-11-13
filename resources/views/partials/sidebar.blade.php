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
                    <img src="{{ $platLogo }}" alt="{{ platform_brand_name() }}" style="max-height: 40px; max-width: 140px; object-fit: contain;">
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

    <!-- Quick Menu Search -->
    <div class="sidebar-search-box">
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" id="sidebarMenuSearch" class="form-control" placeholder="{{ __('Search menu...') }}" autocomplete="off">
        </div>
    </div>

    <nav class="sidebar-nav" id="sidebarNav">
        @if(auth()->check() && auth()->user()->isSuperAdmin())
            <!-- ================= SUPER ADMIN CONTROL ================= -->
            <div class="sidebar-group-title menu-group-header">
                <i class="fas fa-crown text-warning me-1"></i> {{ __('Super Admin') }}
            </div>

            @php $isSuperActive = request()->routeIs('super_admin.*'); @endphp
            <div class="menu-item-wrapper">
                <a href="#menuSuperAdmin" class="sidebar-link sidebar-parent {{ $isSuperActive ? 'active' : 'collapsed' }}"
                   data-bs-toggle="collapse" role="button" aria-expanded="{{ $isSuperActive ? 'true' : 'false' }}" aria-controls="menuSuperAdmin">
                    <div class="sidebar-parent-label">
                        <i class="fas fa-shield-alt text-warning"></i>
                        <span>{{ __('Super Admin Panel') }}</span>
                    </div>
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
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
                    <a href="{{ route('super_admin.cms.index') }}" class="submenu-link {{ request()->routeIs('super_admin.cms.*') ? 'active' : '' }}">
                        <span class="submenu-bullet"></span>
                        <span class="d-flex align-items-center justify-content-between w-100">
                            <span>{{ __('Website CMS') }}</span>
                            <span class="badge bg-primary rounded-pill" style="font-size: 0.65rem;">CMS</span>
                        </span>
                    </a>
                    <a href="{{ route('super_admin.cms.slides.index') }}" class="submenu-link {{ request()->routeIs('super_admin.cms.slides.*') ? 'active' : '' }}">
                        <span class="submenu-bullet"></span>
                        <span>{{ __('Hero Slider') }}</span>
                    </a>
                    <a href="{{ route('super_admin.cms.pages.index') }}" class="submenu-link {{ request()->routeIs('super_admin.cms.pages.*') ? 'active' : '' }}">
                        <span class="submenu-bullet"></span>
                        <span>{{ __('Footer & Pages') }}</span>
                    </a>
                    @php
                        $pendingProposalsCount = class_exists(\App\Models\ProposalRequest::class) ? \App\Models\ProposalRequest::where('status', 'Pending')->count() : 0;
                    @endphp
                    <a href="{{ route('super_admin.proposals.index') }}" class="submenu-link {{ request()->routeIs('super_admin.proposals.*') ? 'active' : '' }}">
                        <span class="submenu-bullet"></span>
                        <span class="d-flex align-items-center justify-content-between w-100">
                            <span>{{ __('Proposal Requests') }}</span>
                            @if($pendingProposalsCount > 0)
                                <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.65rem;">{{ $pendingProposalsCount }}</span>
                            @endif
                        </span>
                    </a>
                    <a href="{{ route('super_admin.cms.proposals.settings') }}" class="submenu-link {{ request()->routeIs('super_admin.cms.proposals.*') ? 'active' : '' }}">
                        <span class="submenu-bullet"></span>
                        <span>{{ __('Proposal Settings') }}</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- ================= 1. OVERVIEW ================= -->
        <div class="sidebar-group-title menu-group-header">
            <span>{{ __('Overview') }}</span>
        </div>

        <div class="menu-item-wrapper">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i>
                <span>{{ __('Dashboard') }}</span>
            </a>
        </div>

        <!-- ================= 2. SALES & REVENUE ================= -->
        <div class="sidebar-group-title menu-group-header">
            <span>{{ __('Sales & Revenue') }}</span>
        </div>

        <!-- Job Orders -->
        @php $isJobActive = request()->routeIs('job-orders.*'); @endphp
        <div class="menu-item-wrapper">
            <a href="#menuJobOrders" class="sidebar-link sidebar-parent {{ $isJobActive ? 'active' : 'collapsed' }}"
               data-bs-toggle="collapse" role="button" aria-expanded="{{ $isJobActive ? 'true' : 'false' }}" aria-controls="menuJobOrders">
                <div class="sidebar-parent-label">
                    <i class="fas fa-tasks"></i>
                    <span>{{ __('Job Orders') }}</span>
                </div>
                <i class="fas fa-chevron-down sidebar-arrow"></i>
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
        </div>

        <!-- Quotations -->
        @php $isQuoteActive = request()->routeIs('quotes.*'); @endphp
        <div class="menu-item-wrapper">
            <a href="#menuQuotes" class="sidebar-link sidebar-parent {{ $isQuoteActive ? 'active' : 'collapsed' }}"
               data-bs-toggle="collapse" role="button" aria-expanded="{{ $isQuoteActive ? 'true' : 'false' }}" aria-controls="menuQuotes">
                <div class="sidebar-parent-label">
                    <i class="fas fa-file-invoice"></i>
                    <span>{{ __('Quotations') }}</span>
                </div>
                <i class="fas fa-chevron-down sidebar-arrow"></i>
            </a>
            <div class="collapse sidebar-submenu {{ $isQuoteActive ? 'show' : '' }}" id="menuQuotes">
                <a href="{{ route('quotes.index') }}" class="submenu-link {{ request()->routeIs('quotes.index') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('All Quotations') }}</span>
                </a>
                <a href="{{ route('quotes.create') }}" class="submenu-link {{ request()->routeIs('quotes.create') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('Create Quotation') }}</span>
                </a>
            </div>
        </div>

        <!-- Sales Invoices -->
        @php $isInvoiceActive = request()->routeIs('invoices.*'); @endphp
        <div class="menu-item-wrapper">
            <a href="#menuInvoices" class="sidebar-link sidebar-parent {{ $isInvoiceActive ? 'active' : 'collapsed' }}"
               data-bs-toggle="collapse" role="button" aria-expanded="{{ $isInvoiceActive ? 'true' : 'false' }}" aria-controls="menuInvoices">
                <div class="sidebar-parent-label">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>{{ __('Sales Invoices') }}</span>
                </div>
                <i class="fas fa-chevron-down sidebar-arrow"></i>
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
        </div>

        <!-- Delivery Notes -->
        @php $isDeliveryActive = request()->routeIs('delivery_notes.*'); @endphp
        <div class="menu-item-wrapper">
            <a href="#menuDeliveryNotes" class="sidebar-link sidebar-parent {{ $isDeliveryActive ? 'active' : 'collapsed' }}"
               data-bs-toggle="collapse" role="button" aria-expanded="{{ $isDeliveryActive ? 'true' : 'false' }}" aria-controls="menuDeliveryNotes">
                <div class="sidebar-parent-label">
                    <i class="fas fa-truck"></i>
                    <span>{{ __('Delivery Notes') }}</span>
                </div>
                <i class="fas fa-chevron-down sidebar-arrow"></i>
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
        </div>

        <!-- Customers / CRM -->
        @php $isCustActive = request()->routeIs('customers.*'); @endphp
        <div class="menu-item-wrapper">
            <a href="#menuCustomers" class="sidebar-link sidebar-parent {{ $isCustActive ? 'active' : 'collapsed' }}"
               data-bs-toggle="collapse" role="button" aria-expanded="{{ $isCustActive ? 'true' : 'false' }}" aria-controls="menuCustomers">
                <div class="sidebar-parent-label">
                    <i class="fas fa-user-friends"></i>
                    <span>{{ __('Customers (CRM)') }}</span>
                </div>
                <i class="fas fa-chevron-down sidebar-arrow"></i>
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
                @if(auth()->user()->is_super_admin ?? false)
                    <a href="{{ route('super_admin.proposals.index') }}" class="submenu-link {{ request()->routeIs('super_admin.proposals.*') ? 'active' : '' }}">
                        <span class="submenu-bullet"></span>
                        <span class="d-flex align-items-center justify-content-between w-100">
                            <span>{{ __('Proposal Leads') }}</span>
                            @if(($pendingProposalsCount ?? 0) > 0)
                                <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.65rem;">{{ $pendingProposalsCount }}</span>
                            @endif
                        </span>
                    </a>
                @endif
            </div>
        </div>

        <!-- ================= 3. PURCHASE & INVENTORY ================= -->
        <div class="sidebar-group-title menu-group-header">
            <span>{{ __('Purchase & Inventory') }}</span>
        </div>

        <!-- Products -->
        @php $isProdActive = request()->routeIs('products.*'); @endphp
        <div class="menu-item-wrapper">
            <a href="#menuProducts" class="sidebar-link sidebar-parent {{ $isProdActive ? 'active' : 'collapsed' }}"
               data-bs-toggle="collapse" role="button" aria-expanded="{{ $isProdActive ? 'true' : 'false' }}" aria-controls="menuProducts">
                <div class="sidebar-parent-label">
                    <i class="fas fa-boxes"></i>
                    <span>{{ __('Products') }}</span>
                </div>
                <i class="fas fa-chevron-down sidebar-arrow"></i>
            </a>
            <div class="collapse sidebar-submenu {{ $isProdActive ? 'show' : '' }}" id="menuProducts">
                <a href="{{ route('products.index') }}" class="submenu-link {{ request()->routeIs('products.index') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('All Products') }}</span>
                </a>
                <a href="{{ route('products.create') }}" class="submenu-link {{ request()->routeIs('products.create') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('Add Product') }}</span>
                </a>
            </div>
        </div>

        <!-- Dedicated Services Module -->
        @php $isServActive = request()->routeIs('services.*'); @endphp
        <div class="menu-item-wrapper">
            <a href="#menuServices" class="sidebar-link sidebar-parent {{ $isServActive ? 'active' : 'collapsed' }}"
               data-bs-toggle="collapse" role="button" aria-expanded="{{ $isServActive ? 'true' : 'false' }}" aria-controls="menuServices">
                <div class="sidebar-parent-label">
                    <i class="fas fa-concierge-bell"></i>
                    <span>{{ __('Services') }}</span>
                </div>
                <i class="fas fa-chevron-down sidebar-arrow"></i>
            </a>
            <div class="collapse sidebar-submenu {{ $isServActive ? 'show' : '' }}" id="menuServices">
                <a href="{{ route('services.index') }}" class="submenu-link {{ request()->routeIs('services.index') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('All Services') }}</span>
                </a>
                <a href="{{ route('services.create') }}" class="submenu-link {{ request()->routeIs('services.create') ? 'active' : '' }}">
                    <span class="submenu-bullet"></span>
                    <span>{{ __('Add New Service') }}</span>
                </a>
            </div>
        </div>

        <!-- Categories -->
        <div class="menu-item-wrapper">
            <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="fas fa-tags"></i>
                <span>{{ __('Categories') }}</span>
            </a>
        </div>

        <!-- Units -->
        <div class="menu-item-wrapper">
            <a href="{{ route('units.index') }}" class="sidebar-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                <i class="fas fa-balance-scale"></i>
                <span>{{ __('Units') }}</span>
            </a>
        </div>

        <!-- ================= 4. HRM & WORKSHOP ================= -->
        <div class="sidebar-group-title menu-group-header">
            <span>{{ __('HRM & Workshop') }}</span>
        </div>

        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
            @php $isEmployeeActive = request()->routeIs('employees.*'); @endphp
            <div class="menu-item-wrapper">
                <a href="#menuEmployees" class="sidebar-link sidebar-parent {{ $isEmployeeActive ? 'active' : 'collapsed' }}"
                   data-bs-toggle="collapse" role="button" aria-expanded="{{ $isEmployeeActive ? 'true' : 'false' }}" aria-controls="menuEmployees">
                    <div class="sidebar-parent-label">
                        <i class="fas fa-id-badge"></i>
                        <span>{{ __('Employees') }}</span>
                    </div>
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
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
            </div>
        @endif

        <div class="menu-item-wrapper">
            <a href="{{ route('departments.index') }}" class="sidebar-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                <i class="fas fa-cogs"></i>
                <span>{{ __('Departments / Units') }}</span>
            </a>
        </div>

        <!-- ================= 5. SETTINGS & SYSTEM ================= -->
        <div class="sidebar-group-title menu-group-header">
            <span>{{ __('Settings & System') }}</span>
        </div>

        @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()))
            @php $isSettingsActive = request()->routeIs('branches.*') || request()->routeIs('subscription.*') || request()->routeIs('settings.*'); @endphp
            <div class="menu-item-wrapper">
                <a href="#menuSettings" class="sidebar-link sidebar-parent {{ $isSettingsActive ? 'active' : 'collapsed' }}"
                   data-bs-toggle="collapse" role="button" aria-expanded="{{ $isSettingsActive ? 'true' : 'false' }}" aria-controls="menuSettings">
                    <div class="sidebar-parent-label">
                        <i class="fas fa-sliders-h"></i>
                        <span>{{ __('Shop Settings') }}</span>
                    </div>
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
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
            </div>
        @endif

        @php $isMaintActive = request()->routeIs('recycle_bin.*') || request()->routeIs('system_logs.*'); @endphp
        <div class="menu-item-wrapper">
            <a href="#menuMaintenance" class="sidebar-link sidebar-parent {{ $isMaintActive ? 'active' : 'collapsed' }}"
               data-bs-toggle="collapse" role="button" aria-expanded="{{ $isMaintActive ? 'true' : 'false' }}" aria-controls="menuMaintenance">
                <div class="sidebar-parent-label">
                    <i class="fas fa-shield-alt"></i>
                    <span>{{ __('System & Logs') }}</span>
                </div>
                <i class="fas fa-chevron-down sidebar-arrow"></i>
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
        </div>
    </nav>
</aside>
<div class="sidebar-backdrop d-none" id="sidebarBackdrop"></div>

{{-- Sidebar Interactive Engine: Smooth Accordion & Instant Search Filter --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Live Menu Filter
    var searchInput = document.getElementById('sidebarMenuSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var q = this.value.toLowerCase().trim();
            var itemWrappers = document.querySelectorAll('#sidebarNav .menu-item-wrapper');
            var groupHeaders = document.querySelectorAll('#sidebarNav .menu-group-header');

            if (!q) {
                // Restore all items
                itemWrappers.forEach(function(wrap) {
                    wrap.style.display = '';
                    var subLinks = wrap.querySelectorAll('.submenu-link');
                    subLinks.forEach(function(sl) { sl.style.display = ''; });
                });
                groupHeaders.forEach(function(gh) { gh.style.display = ''; });
                return;
            }

            itemWrappers.forEach(function(wrap) {
                var parentLink = wrap.querySelector('.sidebar-link');
                var parentText = parentLink ? parentLink.innerText.toLowerCase() : '';
                var subLinks = wrap.querySelectorAll('.submenu-link');
                var hasSubMatch = false;

                subLinks.forEach(function(sl) {
                    var slText = sl.innerText.toLowerCase();
                    if (slText.includes(q)) {
                        sl.style.display = '';
                        hasSubMatch = true;
                    } else {
                        sl.style.display = 'none';
                    }
                });

                if (parentText.includes(q) || hasSubMatch) {
                    wrap.style.display = '';
                    // Auto expand submenu if child matches
                    if (hasSubMatch) {
                        var collapseEl = wrap.querySelector('.collapse');
                        if (collapseEl && !collapseEl.classList.contains('show')) {
                            collapseEl.classList.add('show');
                            if (parentLink) {
                                parentLink.classList.remove('collapsed');
                                parentLink.setAttribute('aria-expanded', 'true');
                            }
                        }
                    }
                } else {
                    wrap.style.display = 'none';
                }
            });

            // Hide headers if all following wrappers until next header are hidden
            groupHeaders.forEach(function(gh) {
                var next = gh.nextElementSibling;
                var anyVisible = false;
                while (next && !next.classList.contains('menu-group-header')) {
                    if (next.style.display !== 'none') {
                        anyVisible = true;
                        break;
                    }
                    next = next.nextElementSibling;
                }
                gh.style.display = anyVisible ? '' : 'none';
            });
        });
    }

    // 2. Resilient Accordion Toggle
    var parents = document.querySelectorAll('.sidebar-parent');
    parents.forEach(function(parent) {
        parent.addEventListener('click', function(e) {
            var targetId = this.getAttribute('href');
            if (targetId && targetId.startsWith('#')) {
                var targetEl = document.querySelector(targetId);
                if (targetEl) {
                    // Update state attribute for rotation
                    setTimeout(function() {
                        var isShown = targetEl.classList.contains('show');
                        parent.setAttribute('aria-expanded', isShown ? 'true' : 'false');
                        if (isShown) {
                            parent.classList.remove('collapsed');
                        } else {
                            parent.classList.add('collapsed');
                        }
                    }, 50);

                    // Fallback if bootstrap JS is not present
                    if (typeof bootstrap === 'undefined') {
                        e.preventDefault();
                        var isCurrentlyShown = targetEl.classList.contains('show');
                        targetEl.classList.toggle('show', !isCurrentlyShown);
                        parent.setAttribute('aria-expanded', !isCurrentlyShown ? 'true' : 'false');
                        parent.classList.toggle('collapsed', isCurrentlyShown);
                    }
                }
            }
        });
    });
});
</script>
