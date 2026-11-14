@php
    $sec = $sections['modules'] ?? null;
    $modTitle = $sec?->title ?: 'Everything Your Business Needs, All in One Place';
    $modSub = $sec?->subtitle ?: 'Explore interconnected ERP applications designed to automate everyday sales, production, invoicing, and reporting.';
    $modulesList = (isset($appModules) && $appModules->count() > 0) ? $appModules : collect();
@endphp

<!-- Modules Section: 1-Line Horizontal Track + ERP-Themed Modern Data Tables & Panels -->
<section class="erpgo-modules-section" id="modules">
    <div class="container position-relative">
        
        <!-- Section Header -->
        <div class="text-center mb-4">
            <span class="badge-pill-soft mb-3">
                <i class="fas fa-th-large text-primary me-1"></i> {{ __('Core ERP Modules') }} ({{ $modulesList->count() }} {{ __('Connected Apps') }})
            </span>
            <h2 class="section-title text-dark fw-bold mb-3">{{ __($modTitle) }}</h2>
            <p class="section-subtitle text-muted mx-auto" style="max-width: 680px;">{{ __($modSub) }}</p>
        </div>

        <!-- 1 SINGLE HORIZONTAL LINE OF DYNAMIC MODULE BOXES (From cms_app_modules database table) -->
        <div class="module-carousel-wrapper" id="moduleCarouselWrapper">
            <div class="module-carousel-track" id="moduleCarouselTrack" role="tablist">
                @foreach($modulesList as $index => $app)
                    <button type="button" 
                            class="erpgo-module-tab {{ $index === 0 ? 'active' : '' }}" 
                            data-erpgo-tab="mod-{{ $app->slug }}" 
                            id="tab-mod-{{ $app->slug }}"
                            title="{{ __($app->name) }}">
                        <div class="erpgo-tab-svg-icon">
                            @if(!empty($app->icon_svg))
                                {!! $app->icon_svg !!}
                            @else
                                <i class="fas fa-cube text-primary"></i>
                            @endif
                        </div>
                        <span>{{ __($app->name) }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- DYNAMIC ACTIVE PREVIEW WINDOW (Auto-switches with the active module box) -->
        <div class="erpgo-preview-window">
            
            <!-- Window Topbar -->
            <div class="erpgo-preview-topbar">
                <div class="browser-dots">
                    <span class="browser-dot red"></span>
                    <span class="browser-dot yellow"></span>
                    <span class="browser-dot green"></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-soft badge-soft-primary" style="font-size: 0.72rem;">
                        <i class="fas fa-database me-1"></i> {{ __('Live Database Connected') }}
                    </span>
                    <span class="text-muted d-none d-sm-inline" style="font-size: 0.75rem;">{{ __('CreLogics ERP SaaS Engine') }}</span>
                </div>
            </div>

            <!-- Dynamic Body: Automatically populated from Real Admin Database Models -->
            <div class="erpgo-preview-body">
                @foreach($modulesList as $index => $app)
                    <div class="erpgo-module-panel {{ $index === 0 ? 'active' : '' }}" id="pane-mod-{{ $app->slug }}">
                        
                        <!-- Panel Header -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between pb-3 border-bottom mb-4 gap-2">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge-soft badge-soft-primary fw-bold">
                                        {{ __('Module') }} #{{ $app->sort_order ?: ($index + 1) }} &bull; {{ __($app->name) }}
                                    </span>
                                    <span class="badge-soft badge-soft-success">
                                        <i class="fas fa-circle me-1" style="font-size: 0.45rem;"></i> {{ __('Active Module') }}
                                    </span>
                                </div>
                                <h4 class="fw-bold text-dark mb-1">
                                    <span class="me-2 d-inline-block" style="width: 26px; height: 26px; vertical-align: middle;">
                                        @if(!empty($app->icon_svg))
                                            {!! $app->icon_svg !!}
                                        @else
                                            <i class="fas fa-cube text-primary"></i>
                                        @endif
                                    </span>
                                    {{ __($app->name) }} {{ __('Management Workspace') }}
                                </h4>
                                <p class="text-muted mb-0" style="font-size: 0.88rem;">
                                    {{ __($app->description ?: 'Centralized business operations, secure permissions, and real-time database synchronization.') }}
                                </p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-white text-muted border px-3 py-2 rounded-3 shadow-xs small">
                                    <i class="fas fa-calendar-day text-primary me-1"></i> {{ \Carbon\Carbon::now()->locale(app()->getLocale())->translatedFormat('D, d M Y') }}
                                </span>
                                @auth
                                    <a href="{{ route('dashboard') }}" class="btn-erp-primary btn-sm px-3 py-1">{{ __('Open Module') }}</a>
                                @else
                                    <a href="{{ route('login') }}" class="btn-erp-primary btn-sm px-3 py-1">{{ __('Live Sign In') }}</a>
                                @endauth
                            </div>
                        </div>

                        {{-- =========================================================
                             DYNAMIC CONTENT BASED ON MODULE SLUG
                             ========================================================= --}}
                        
                        {{-- 1. DASHBOARD / OVERVIEW --}}
                        @if($app->slug === 'dashboard')
                            <!-- Active Shop & Context Banner (Exact ERPGo Style) -->
                            <div class="p-3 bg-white rounded-3 shadow-sm border mb-4 position-relative" style="background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%); border-left: 4px solid var(--clr-primary, #4361EE) !important;">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="p-2 rounded-3 shadow-xs border d-flex align-items-center justify-content-center bg-white" style="width: 52px; height: 52px;">
                                            <i class="fas fa-store text-primary fs-3"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <h5 class="fw-bold text-dark mb-0">{{ __($allShops->first()?->name ?: 'New Crelogics') }}</h5>
                                                <span class="badge-soft badge-soft-success">
                                                    <i class="fas fa-circle me-1" style="font-size: 0.45rem;"></i> {{ __('Active Shop') }}
                                                </span>
                                                <span class="badge bg-light text-secondary border px-2 py-1 rounded-pill small">
                                                    <i class="fas fa-store text-primary me-1"></i> {{ __('Branch #1') }}
                                                </span>
                                            </div>
                                            <p class="text-muted small mb-0 mt-1">
                                                <span><i class="fas fa-map-marker-alt text-primary me-1"></i> {{ __('Karachi HQ') }}</span>
                                                <span class="ms-3"><i class="fas fa-coins text-warning me-1"></i> {{ __('Currency:') }} <strong>{{ __('USD ($)') }}</strong></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <span class="btn btn-sm btn-light border text-primary fw-semibold"><i class="fas fa-chart-line me-1"></i> {{ __('Shop Dashboard') }}</span>
                                        <span class="btn btn-sm btn-light border text-secondary"><i class="fas fa-sync-alt me-1"></i> {{ __('All Branches') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- 5 Real KPI Cards (Exact Colors from User Dashboard) -->
                            <div class="row g-3 mb-4">
                                <div class="col-6 col-md">
                                    <div class="p-3 text-white rounded-3 shadow-sm h-100" style="background: #4361EE;">
                                        <small class="text-white-50 text-uppercase fw-bold d-block" style="font-size: 0.68rem; letter-spacing: 0.04em;">{{ __('TOTAL SHOPS') }}</small>
                                        <h3 class="fw-bold mb-0">{{ $shopCount }}</h3>
                                        <small class="text-white-50" style="font-size: 0.72rem;">{{ $shopCount }} {{ __('Active Branch') }}</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md">
                                    <div class="p-3 text-white rounded-3 shadow-sm h-100" style="background: #00B894;">
                                        <small class="text-white-50 text-uppercase fw-bold d-block" style="font-size: 0.68rem; letter-spacing: 0.04em;">{{ __('TOTAL REVENUE') }}</small>
                                        <h3 class="fw-bold mb-0">${{ number_format($totalRevenue, 2) }}</h3>
                                        <small class="text-white-50" style="font-size: 0.72rem;">{{ __('Across all shops') }}</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md">
                                    <div class="p-3 text-white rounded-3 shadow-sm h-100" style="background: #FF4B72;">
                                        <small class="text-white-50 text-uppercase fw-bold d-block" style="font-size: 0.68rem; letter-spacing: 0.04em;">{{ __('PENDING DUE') }}</small>
                                        <h3 class="fw-bold mb-0">${{ number_format($pendingDue, 2) }}</h3>
                                        <small class="text-white-50" style="font-size: 0.72rem;">{{ __('Unpaid balances') }}</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md">
                                    <div class="p-3 text-white rounded-3 shadow-sm h-100" style="background: #00A8CC;">
                                        <small class="text-white-50 text-uppercase fw-bold d-block" style="font-size: 0.68rem; letter-spacing: 0.04em;">{{ __('QUOTES & ORDERS') }}</small>
                                        <h3 class="fw-bold mb-0">{{ $recentQuotes->count() }} / {{ $orderCount }}</h3>
                                        <small class="text-white-50" style="font-size: 0.72rem;">{{ $invoiceCount }} {{ __('Invoices Generated') }}</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md">
                                    <div class="p-3 text-white rounded-3 shadow-sm h-100" style="background: #845EC2;">
                                        <small class="text-white-50 text-uppercase fw-bold d-block" style="font-size: 0.68rem; letter-spacing: 0.04em;">{{ __('USERS & CLIENTS') }}</small>
                                        <h3 class="fw-bold mb-0">{{ $customerCount }}</h3>
                                        <small class="text-white-50" style="font-size: 0.72rem;">{{ $customerCount }} {{ __('Registered Customers') }}</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Orders Table Preview inside Card -->
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-tasks text-primary"></i> {{ __('Recent Job Orders') }}</h6>
                                    <span class="badge bg-white text-primary border small px-2 py-1 rounded-pill">{{ __('Real-Time Data') }}</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table-modern">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Order #') }}</th>
                                                <th>{{ __('Customer') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th class="text-end">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentJobOrders as $order)
                                                <tr>
                                                    <td class="fw-bold text-primary">{{ $order->order_number ?: '#JOB-' . $order->id }}</td>
                                                    <td>
                                                        <div class="fw-semibold text-dark">{{ __($order->customer?->name ?: ($order->customer?->company_name ?: 'Walk-in Client')) }}</div>
                                                        <div class="text-muted extra-small" style="font-size: 0.75rem;">{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->locale(app()->getLocale())->translatedFormat('d M, Y') : \Carbon\Carbon::now()->locale(app()->getLocale())->translatedFormat('d M, Y') }}</div>
                                                    </td>
                                                    <td class="fw-semibold text-dark">${{ number_format($order->grand_total ?: $order->total_amount, 2) }}</td>
                                                    <td>
                                                        <span class="badge-soft {{ $order->status === 'completed' ? 'badge-soft-success' : ($order->status === 'in_progress' ? 'badge-soft-info' : 'badge-soft-warning') }}">
                                                            {{ __(ucfirst(str_replace('_', ' ', $order->status ?: 'In Production'))) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="btn btn-sm btn-light border p-1 px-2" title="View"><i class="fas fa-eye text-primary"></i></span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center py-3 text-muted">{{ __('No records found.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        {{-- 2. JOB ORDERS --}}
                        @elseif($app->slug === 'job-orders')
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-clipboard-list text-info"></i> {{ __('Workshop Production Orders') }}</h6>
                                    <span class="badge-soft badge-soft-info">{{ $recentJobOrders->count() }} {{ __('Active Orders') }}</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table-modern">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Order #') }}</th>
                                                <th>{{ __('Customer Name') }}</th>
                                                <th>{{ __('Date Logged') }}</th>
                                                <th>{{ __('Total Amount') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th class="text-end">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentJobOrders as $order)
                                                <tr>
                                                    <td class="fw-bold text-primary">{{ $order->order_number ?: 'JO-#' . $order->id }}</td>
                                                    <td>
                                                        <div class="fw-semibold text-dark">{{ __($order->customer?->name ?: 'Customer Account') }}</div>
                                                        <div class="text-muted extra-small" style="font-size: 0.75rem;">{{ __($order->customer?->company_name ?: 'General Client') }}</div>
                                                    </td>
                                                    <td>{{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->locale(app()->getLocale())->translatedFormat('d M, Y') : \Carbon\Carbon::now()->locale(app()->getLocale())->translatedFormat('d M, Y') }}</td>
                                                    <td class="fw-bold text-dark">${{ number_format($order->grand_total ?: $order->total_amount, 2) }}</td>
                                                    <td>
                                                        <span class="badge-soft {{ $order->status === 'completed' ? 'badge-soft-success' : ($order->status === 'in_progress' ? 'badge-soft-info' : 'badge-soft-warning') }}">
                                                            {{ __(ucfirst(str_replace('_', ' ', $order->status ?: 'In Production'))) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="btn btn-sm btn-light border p-1 px-2"><i class="fas fa-eye text-primary"></i></span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center py-3 text-muted">{{ __('No job orders recorded yet.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        {{-- 3. QUOTATIONS --}}
                        @elseif($app->slug === 'quotations')
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-file-signature text-warning"></i> {{ __('Client Quotations & Proposals') }}</h6>
                                    <span class="badge-soft badge-soft-warning">{{ $recentQuotes->count() }} {{ __('Generated') }}</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table-modern">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Quote #') }}</th>
                                                <th>{{ __('Client / Firm') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Estimated Total') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th class="text-end">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentQuotes as $quote)
                                                <tr>
                                                    <td class="fw-bold text-warning">{{ $quote->quote_number ?: 'QT-#' . $quote->id }}</td>
                                                    <td>
                                                        <div class="fw-semibold text-dark">{{ __($quote->customer?->name ?: 'Client') }}</div>
                                                        <div class="text-muted extra-small" style="font-size: 0.75rem;">{{ __($quote->customer?->company_name ?: 'Commercial Account') }}</div>
                                                    </td>
                                                    <td>{{ $quote->quote_date ? \Carbon\Carbon::parse($quote->quote_date)->locale(app()->getLocale())->translatedFormat('d M, Y') : \Carbon\Carbon::now()->locale(app()->getLocale())->translatedFormat('d M, Y') }}</td>
                                                    <td class="fw-bold text-dark">${{ number_format($quote->grand_total, 2) }}</td>
                                                    <td>
                                                        <span class="badge-soft badge-soft-warning">
                                                            {{ __($quote->status ?: 'Draft') }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="btn btn-sm btn-light border p-1 px-2"><i class="fas fa-eye text-primary"></i></span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center py-3 text-muted">{{ __('No quotations recorded yet.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        {{-- 4. INVOICES --}}
                        @elseif($app->slug === 'invoices')
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-file-invoice-dollar text-success"></i> {{ __('Official Sales Invoices') }}</h6>
                                    <span class="badge-soft badge-soft-success">{{ $recentInvoices->count() }} {{ __('Invoices') }}</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table-modern">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Invoice #') }}</th>
                                                <th>{{ __('Billed Customer') }}</th>
                                                <th>{{ __('Invoice Date') }}</th>
                                                <th>{{ __('Due Date') }}</th>
                                                <th>{{ __('Balance Due') }}</th>
                                                <th>{{ __('Total') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th class="text-end">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentInvoices as $inv)
                                                <tr>
                                                    <td class="fw-bold text-primary">{{ $inv->invoice_number ?: 'INV-#' . $inv->id }}</td>
                                                    <td class="fw-semibold text-dark">{{ __($inv->customer?->name ?: 'Client') }}</td>
                                                    <td>{{ $inv->invoice_date ? \Carbon\Carbon::parse($inv->invoice_date)->locale(app()->getLocale())->translatedFormat('d M, Y') : \Carbon\Carbon::now()->locale(app()->getLocale())->translatedFormat('d M, Y') }}</td>
                                                    <td class="text-muted">{{ $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->locale(app()->getLocale())->translatedFormat('d M, Y') : __('Net 15') }}</td>
                                                    <td class="fw-bold text-danger">${{ number_format($inv->balance_due, 2) }}</td>
                                                    <td class="fw-bold text-dark">${{ number_format($inv->grand_total, 2) }}</td>
                                                    <td>
                                                        <span class="badge-soft {{ $inv->status === 'Paid' ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                                            {{ __($inv->status ?: 'Unpaid') }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="btn btn-sm btn-light border p-1 px-2"><i class="fas fa-eye text-primary"></i></span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="8" class="text-center py-3 text-muted">{{ __('No invoices recorded yet.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        {{-- 5. CUSTOMERS CRM --}}
                        @elseif($app->slug === 'customers')
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-users text-danger"></i> {{ __('Registered Customer Directory') }}</h6>
                                    <span class="badge-soft badge-soft-primary">{{ $customerCount }} {{ __('Customers') }}</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table-modern">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Client Name') }}</th>
                                                <th>{{ __('Company / Business') }}</th>
                                                <th>{{ __('Phone Number') }}</th>
                                                <th>{{ __('Email Address') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th class="text-end">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentCustomers as $c)
                                                <tr>
                                                    <td class="fw-bold text-dark">{{ __(trim($c->name)) }}</td>
                                                    <td>
                                                        <div class="fw-semibold text-dark">{{ __(trim($c->company_name ?: 'Individual Client')) }}</div>
                                                        <div class="text-muted extra-small" style="font-size: 0.75rem;">{{ __('Account #') }}{{ $c->id }}</div>
                                                    </td>
                                                    <td><i class="fas fa-phone text-muted me-1 small"></i> {{ $c->phone ?: 'N/A' }}</td>
                                                    <td class="text-muted">{{ $c->email ?: 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge-soft badge-soft-success">{{ __('Verified') }}</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="btn btn-sm btn-light border p-1 px-2"><i class="fas fa-eye text-primary"></i></span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center py-3 text-muted">{{ __('No customers recorded yet.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        {{-- 6. PRODUCTS & SIZING --}}
                        @elseif($app->slug === 'products')
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-boxes text-warning"></i> {{ __('Product Catalog & Dimensional Rates') }}</h6>
                                    <span class="badge-soft badge-soft-info">{{ $recentProducts->count() }} {{ __('Catalog Items') }}</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table-modern">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Product Name') }}</th>
                                                <th>{{ __('SKU Code') }}</th>
                                                <th>{{ __('Category') }}</th>
                                                <th>{{ __('Sale Price') }}</th>
                                                <th>{{ __('Stock Status') }}</th>
                                                <th class="text-end">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentProducts as $prod)
                                                <tr>
                                                    <td class="fw-bold text-dark">{{ __($prod->name) }}</td>
                                                    <td><span class="badge bg-light text-muted border">{{ $prod->sku ?: 'PRD-' . $prod->id }}</span></td>
                                                    <td><span class="badge-soft badge-soft-primary">{{ __($prod->category?->name ?: 'General') }}</span></td>
                                                    <td class="fw-bold text-dark">${{ number_format($prod->sale_price ?: $prod->price, 2) }}</td>
                                                    <td>
                                                        <span class="badge-soft badge-soft-success">
                                                            <i class="fas fa-check-circle me-1" style="font-size: 0.5rem;"></i> {{ $prod->stock ?: 0 }} {{ __('Units In Stock') }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="btn btn-sm btn-light border p-1 px-2"><i class="fas fa-eye text-primary"></i></span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center py-3 text-muted">{{ __('No products recorded yet.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        {{-- 7. CATEGORIES --}}
                        @elseif($app->slug === 'categories')
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-tags text-primary"></i> {{ __('Item & Material Categories') }}</h6>
                                    <span class="badge-soft badge-soft-primary">{{ $allCategories->count() }} {{ __('Categories') }}</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table-modern">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Category Name') }}</th>
                                                <th>{{ __('Category Code') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th class="text-end">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($allCategories as $cat)
                                                <tr>
                                                    <td class="fw-bold text-dark">{{ __($cat->name) }}</td>
                                                    <td><span class="badge bg-light text-primary border">{{ $cat->category_code ?: 'CAT-0' . $cat->id }}</span></td>
                                                    <td><span class="badge-soft badge-soft-success">{{ __('Active') }}</span></td>
                                                    <td class="text-end">
                                                        <span class="btn btn-sm btn-light border p-1 px-2"><i class="fas fa-eye text-primary"></i></span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="4" class="text-center py-3 text-muted">{{ __('No categories recorded yet.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        {{-- 8. EMPLOYEES & HRM --}}
                        @elseif($app->slug === 'employees')
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-id-badge text-primary"></i> {{ __('Staff Members & Workshop Technicians') }}</h6>
                                    <span class="badge-soft badge-soft-success">{{ $allEmployees->count() }} {{ __('Employees Active') }}</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table-modern">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Employee Name') }}</th>
                                                <th>{{ __('Designation / Role') }}</th>
                                                <th>{{ __('Employee ID') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th class="text-end">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($allEmployees as $emp)
                                                <tr>
                                                    <td class="fw-bold text-dark">{{ __($emp->name) }}</td>
                                                    <td><span class="badge-soft badge-soft-primary">{{ __($emp->designation ?: 'Staff Specialist') }}</span></td>
                                                    <td><span class="badge bg-light text-muted border">EMP-0{{ $emp->id }}</span></td>
                                                    <td><span class="badge-soft badge-soft-success">{{ __('On Duty') }}</span></td>
                                                    <td class="text-end">
                                                        <span class="btn btn-sm btn-light border p-1 px-2"><i class="fas fa-eye text-primary"></i></span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center py-3 text-muted">{{ __('No employees recorded yet.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        {{-- 9. MULTI-BRANCH / SHOPS --}}
                        @elseif($app->slug === 'multi-branch')
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-store text-primary"></i> {{ __('Registered Shops & Branches') }}</h6>
                                    <span class="badge-soft badge-soft-success">{{ $shopCount }} {{ __('Active Shops') }}</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table-modern">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Shop / Branch') }}</th>
                                                <th>{{ __('Primary Admin') }}</th>
                                                <th>{{ __('Quotes / Orders') }}</th>
                                                <th>{{ __('Invoices') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th class="text-end">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($allShops as $shop)
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold text-dark">{{ __($shop->name) }}</div>
                                                        <div class="text-muted extra-small" style="font-size: 0.75rem;"><i class="fas fa-map-marker-alt me-1"></i> {{ __($shop->address ?: 'Karachi Branch HQ') }}</div>
                                                    </td>
                                                    <td>
                                                        <div class="fw-semibold text-dark">{{ __('Afaq Tahir') }}</div>
                                                        <div class="text-muted extra-small" style="font-size: 0.75rem;">{{ $shop->email ?: 'meemwork0687@gmail.com' }}</div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-light text-primary border me-1">{{ $recentQuotes->count() }} {{ __('Quotes') }}</span>
                                                        <span class="badge bg-light text-info border">{{ $orderCount }} {{ __('Orders') }}</span>
                                                    </td>
                                                    <td><span class="fw-bold text-dark">{{ $invoiceCount }} {{ __('Invoices') }}</span></td>
                                                    <td><span class="badge-soft badge-soft-success">{{ __('Active') }}</span></td>
                                                    <td class="text-end">
                                                        <span class="btn btn-sm btn-primary-modern px-3 py-1"><i class="fas fa-sign-in-alt me-1"></i> {{ __('Enter Shop') }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="text-center py-3 text-muted">{{ __('No shops recorded yet.') }}</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        {{-- 10. REPORTS & FINANCIAL AUDIT --}}
                        @elseif($app->slug === 'reports')
                            <div class="row g-3 text-center mb-4">
                                <div class="col-md-4">
                                    <div class="p-3 bg-white rounded-3 border shadow-xs">
                                        <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.7rem;">{{ __('TOTAL BILLED REVENUE') }}</small>
                                        <h3 class="fw-bold text-dark mb-0">${{ number_format($totalRevenue, 2) }}</h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-white rounded-3 border shadow-xs">
                                        <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.7rem;">{{ __('COLLECTED REVENUE') }}</small>
                                        <h3 class="fw-bold text-success mb-0">${{ number_format($paidRevenue, 2) }}</h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 bg-white rounded-3 border shadow-xs">
                                        <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.7rem;">{{ __('PENDING DUE BALANCE') }}</small>
                                        <h3 class="fw-bold text-danger mb-0">${{ number_format($pendingDue, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-file-invoice text-success"></i> {{ __('Revenue Statement Ledger') }}</h6>
                                    <span class="badge bg-light text-primary border">{{ __('Automated Calculation') }}</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table-modern">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Reference') }}</th>
                                                <th>{{ __('Customer Account') }}</th>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Total Invoiced') }}</th>
                                                <th>{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentInvoices as $inv)
                                                <tr>
                                                    <td class="fw-bold text-primary">{{ $inv->invoice_number }}</td>
                                                    <td>{{ __($inv->customer?->name ?: 'Client') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($inv->invoice_date)->locale(app()->getLocale())->translatedFormat('d M, Y') }}</td>
                                                    <td class="fw-bold text-dark">${{ number_format($inv->grand_total, 2) }}</td>
                                                    <td><span class="badge-soft {{ $inv->status === 'Paid' ? 'badge-soft-success' : 'badge-soft-danger' }}">{{ __($inv->status) }}</span></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        {{-- 11. ERP SETTINGS --}}
                        @elseif($app->slug === 'settings')
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-sliders-h text-dark"></i> {{ __('System & Platform Configurations') }}</h6>
                                    <span class="badge-soft badge-soft-success">{{ __('Configured') }}</span>
                                </div>
                                <div class="p-4 bg-white">
                                    <div class="row g-4 small">
                                        <div class="col-sm-6">
                                            <div class="text-muted mb-1">{{ __('Company / Platform Title:') }}</div>
                                            <strong class="text-dark fs-6">{{ $settings['site_name'] ?? 'Crelogics ERP' }}</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="text-muted mb-1">{{ __('Contact Email Address:') }}</div>
                                            <strong class="text-dark fs-6">{{ $settings['contact_email'] ?? 'support@crelogics.com' }}</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="text-muted mb-1">{{ __('Official WhatsApp Hotline:') }}</div>
                                            <strong class="text-dark fs-6">{{ $settings['whatsapp_number'] ?? '+92-300-1111111' }}</strong>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="text-muted mb-1">{{ __('Base Currency Configuration:') }}</div>
                                            <strong class="text-dark fs-6">{{ __('USD ($) • Multi-Currency Enabled') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        {{-- GENERIC FALLBACK FOR OTHER MODULES (Delivery Notes, Inventory, Accounting, Subscriptions, etc.) --}}
                        @else
                            <div class="preview-card-wrap text-center p-5 bg-white">
                                <div class="mb-3 d-inline-block" style="width: 54px; height: 54px;">
                                    @if(!empty($app->icon_svg))
                                        {!! $app->icon_svg !!}
                                    @else
                                        <i class="fas fa-th-large text-primary fs-1"></i>
                                    @endif
                                </div>
                                <h5 class="fw-bold text-dark mb-2">{{ __($app->name) }} {{ __('Operational Management') }}</h5>
                                <p class="text-muted mx-auto mb-4" style="max-width: 580px; font-size: 0.92rem;">
                                    {{ __($app->description ?: 'This business module is actively integrated with CreLogics multi-branch database. Access live records and transactions in your workspace.') }}
                                </p>
                                <div class="d-inline-flex gap-2">
                                    @auth
                                        <a href="{{ route('dashboard') }}" class="btn-erp-primary btn-sm px-4">Open {{ $app->name }} Workspace &rarr;</a>
                                    @else
                                        <a href="{{ route('login') }}" class="btn-erp-primary btn-sm px-4">{{ __('Sign In to Manage →') }}</a>
                                    @endauth
                                </div>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>

        </div>

    </div>
</section>
