@extends('admin.layouts.app')

@section('title', __('Daily Staff Sales, Seats & Expenses Statement'))

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fas fa-file-invoice-dollar text-success"></i>
                {{ __('Daily Staff Sales, Seats & Expenses Closing') }}
            </h3>
            <p class="text-muted small mb-0">
                @if($isAdmin)
                    {{ __('Comprehensive daily performance: Track seats booked, gross sales, cash collected, and daily office expenses per employee.') }}
                @else
                    {{ __('Your daily booking performance, seats, collections, and office expenses for :office', ['office' => auth()->user()->shop?->name ?: __('Your Office')]) }}
                @endif
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('travel.daily_report.pdf', request()->query()) }}" target="_blank" class="btn btn-outline-danger fw-semibold d-flex align-items-center gap-2 shadow-xs">
                <i class="fas fa-file-pdf"></i>
                <span>{{ __('Export Daily PDF Statement') }}</span>
            </a>
            <a href="{{ route('travel.bookings.create') }}" class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2">
                <i class="fas fa-plus-circle"></i>
                <span>{{ __('Book a Seat') }}</span>
            </a>
        </div>
    </div>

    {{-- KPI Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-auto flex-fill">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small fw-bold text-uppercase d-block mb-1">{{ __('Seats Booked') }}</span>
                        <h4 class="fw-bold text-primary mb-0 font-monospace">{{ $grandSeats }} <small class="fs-6 text-muted">{{ __('Seats') }}</small></h4>
                        <small class="text-muted extra-small">{{ count($staffStats) }} {{ __('Active Staff') }}</small>
                    </div>
                    <div class="rounded-3 bg-primary-subtle p-3 text-primary fs-4">
                        <i class="fas fa-chair"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-auto flex-fill">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small fw-bold text-uppercase d-block mb-1">{{ __('Gross Booking Sales') }}</span>
                        <h4 class="fw-bold text-dark mb-0 font-monospace">{{ format_currency($grandSales) }}</h4>
                        <small class="text-muted extra-small">{{ __('Total Fares Realized') }}</small>
                    </div>
                    <div class="rounded-3 bg-light p-3 text-dark fs-4">
                        <i class="fas fa-bus-alt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-auto flex-fill">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small fw-bold text-uppercase d-block mb-1">{{ __('Cash Collected (In)') }}</span>
                        <h4 class="fw-bold text-success mb-0 font-monospace">{{ format_currency($grandCashIn) }}</h4>
                        <small class="text-muted extra-small">{{ __('Actual Paid') }}</small>
                    </div>
                    <div class="rounded-3 bg-success-subtle p-3 text-success fs-4">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-auto flex-fill">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small fw-bold text-uppercase d-block mb-1">{{ __('Daily Expenses (Out)') }}</span>
                        <h4 class="fw-bold text-danger mb-0 font-monospace">{{ format_currency($grandExpenses) }}</h4>
                        <small class="text-muted extra-small">{{ $expenses->count() }} {{ __('Expense Items') }}</small>
                    </div>
                    <div class="rounded-3 bg-danger-subtle p-3 text-danger fs-4">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-xl-auto flex-fill">
            <div class="card border-0 shadow-sm rounded-3 h-100 bg-white border-start border-4 border-success">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted extra-small fw-bold text-uppercase d-block mb-1">{{ __('Net Cash in Hand / Register') }}</span>
                        <h4 class="fw-bold mb-0 font-monospace {{ $grandNetCash >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ format_currency($grandNetCash) }}
                        </h4>
                        <small class="text-muted extra-small">{{ __('Cash In minus Expenses') }}</small>
                    </div>
                    <div class="rounded-3 {{ $grandNetCash >= 0 ? 'bg-success text-white' : 'bg-danger text-white' }} p-3 fs-4">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('travel.daily_report') }}" id="filterForm">
                {{-- Quick Date Presets --}}
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <span class="text-muted extra-small fw-bold text-uppercase me-2"><i class="fas fa-clock me-1"></i>{{ __('Quick Dates') }}:</span>
                    <a href="{{ route('travel.daily_report', array_merge(request()->except('date_preset', 'start_date', 'end_date', 'date'), ['date_preset' => 'today'])) }}" 
                       class="btn btn-sm {{ request('date_preset', 'today') === 'today' && !request('start_date') ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                        {{ __('Today') }}
                    </a>
                    <a href="{{ route('travel.daily_report', array_merge(request()->except('date_preset', 'start_date', 'end_date', 'date'), ['date_preset' => 'yesterday'])) }}" 
                       class="btn btn-sm {{ request('date_preset') === 'yesterday' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                        {{ __('Yesterday') }}
                    </a>
                    <a href="{{ route('travel.daily_report', array_merge(request()->except('date_preset', 'start_date', 'end_date', 'date'), ['date_preset' => 'this_week'])) }}" 
                       class="btn btn-sm {{ request('date_preset') === 'this_week' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                        {{ __('This Week') }}
                    </a>
                    <a href="{{ route('travel.daily_report', array_merge(request()->except('date_preset', 'start_date', 'end_date', 'date'), ['date_preset' => 'this_month'])) }}" 
                       class="btn btn-sm {{ request('date_preset') === 'this_month' ? 'btn-primary' : 'btn-outline-secondary' }} rounded-pill px-3">
                        {{ __('This Month') }}
                    </a>
                </div>

                <div class="row g-2 align-items-end">
                    {{-- Date Filter --}}
                    <div class="col-md-2">
                        <label class="form-label extra-small text-muted fw-bold text-uppercase mb-1">{{ __('From Date') }}</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label extra-small text-muted fw-bold text-uppercase mb-1">{{ __('To Date') }}</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                    </div>

                    {{-- Office / Branch Filter (Admin Only) --}}
                    @if($isAdmin)
                    <div class="col-md-3">
                        <label class="form-label extra-small text-muted fw-bold text-uppercase mb-1">{{ __('Office / Branch') }}</label>
                        <select name="shop_id" id="reportShopSelect" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">{{ __('All Offices / Branches') }}</option>
                            @foreach($availableOffices as $off)
                                <option value="{{ $off->id }}" {{ request('shop_id') == $off->id ? 'selected' : '' }}>
                                    {{ $off->name }} {{ $off->parent_shop_id ? '(' . __('Branch') . ')' : '(' . __('Head Office') . ')' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Staff Filter (Admin Only) --}}
                    @if($isAdmin)
                    <div class="col-md-3">
                        <label class="form-label extra-small text-muted fw-bold text-uppercase mb-1">{{ __('Staff Member') }}</label>
                        <select name="user_id" id="reportStaffSelect" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">{{ __('All Staff Members') }}</option>
                            @foreach($availableStaff as $stf)
                                <option value="{{ $stf->id }}" {{ request('user_id') == $stf->id ? 'selected' : '' }}>{{ $stf->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-auto d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-primary px-3">
                            <i class="fas fa-filter me-1"></i>{{ __('Filter') }}
                        </button>
                        <a href="{{ route('travel.daily_report') }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Reset Filters') }}">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- SECTION 1: PER-EMPLOYEE PERFORMANCE & CLOSING SUMMARY TABLE --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
        <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="fas fa-users-cog text-primary"></i>
                {{ __('Per-Employee Daily Performance & Cash Summary') }}
            </h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark border px-2 py-1 extra-small">
                    {{ $periodLabel }}
                </span>
                <a href="{{ route('travel.daily_report.pdf', array_merge(request()->query(), ['type' => 'staff_summary'])) }}" target="_blank" class="btn btn-sm btn-outline-danger fw-semibold d-flex align-items-center gap-1 shadow-xs">
                    <i class="fas fa-file-pdf"></i>
                    <span>{{ __('Download Staff Summary PDF') }}</span>
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 py-3 text-muted extra-small text-uppercase fw-bold">#</th>
                            <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Staff Member / Employee') }}</th>
                            <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Office / Branch') }}</th>
                            <th class="py-3 text-center text-muted extra-small text-uppercase fw-bold">{{ __('Seats Booked') }}</th>
                            <th class="py-3 text-end text-muted extra-small text-uppercase fw-bold">{{ __('Gross Sales') }}</th>
                            <th class="py-3 text-end text-muted extra-small text-uppercase fw-bold text-success">{{ __('Cash Collected (In)') }}</th>
                            <th class="py-3 text-end text-muted extra-small text-uppercase fw-bold text-secondary">{{ __('Pending Due') }}</th>
                            <th class="py-3 text-end text-muted extra-small text-uppercase fw-bold text-danger">{{ __('Daily Expenses (Out)') }}</th>
                            <th class="py-3 text-end text-muted extra-small text-uppercase fw-bold">{{ __('Net Cash Position') }}</th>
                            <th class="pe-3 py-3 text-center text-muted extra-small text-uppercase fw-bold" style="width: 80px;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffStats as $idx => $st)
                        <tr>
                            <td class="ps-3 py-3 text-muted extra-small">{{ $loop->iteration }}</td>
                            <td class="py-3">
                                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                        {{ strtoupper(substr($st['name'], 0, 1)) }}
                                    </div>
                                    <span>{{ $st['name'] }}</span>
                                </div>
                                @if($st['designation'])
                                    <div class="extra-small text-muted ms-4 ps-2">{{ $st['designation'] }}</div>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-building text-primary me-1"></i>{{ $st['office'] }}
                                </span>
                            </td>
                            <td class="py-3 text-center">
                                <span class="badge bg-primary px-2 py-1 fs-6 font-monospace">
                                    {{ $st['seats_count'] }}
                                </span>
                                <div class="extra-small text-muted mt-1">{{ $st['bookings_count'] }} {{ __('Bookings') }}</div>
                            </td>
                            <td class="py-3 text-end font-monospace fw-bold text-dark">
                                {{ format_currency($st['gross_sales']) }}
                            </td>
                            <td class="py-3 text-end font-monospace fw-bold text-success">
                                {{ format_currency($st['cash_collected']) }}
                            </td>
                            <td class="py-3 text-end font-monospace text-secondary">
                                {{ format_currency($st['balance_due']) }}
                            </td>
                            <td class="py-3 text-end font-monospace fw-bold text-danger">
                                {{ format_currency($st['expenses']) }}
                                @if($st['expenses_count'] > 0)
                                    <div class="extra-small text-muted">({{ $st['expenses_count'] }} {{ __('items') }})</div>
                                @endif
                            </td>
                            <td class="py-3 text-end">
                                <span class="badge {{ $st['net_cash'] >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} fs-6 font-monospace px-2 py-1">
                                    {{ format_currency($st['net_cash']) }}
                                </span>
                            </td>
                            <td class="pe-3 py-3 text-center">
                                @if(!empty($st['user_id']))
                                    <a href="{{ route('travel.daily_report.pdf', array_merge(request()->query(), ['user_id' => $st['user_id']])) }}" target="_blank" class="btn btn-xs btn-outline-danger shadow-xs py-1 px-2" title="{{ __('Download Individual Daily Statement PDF') }}">
                                        <i class="fas fa-file-pdf me-1"></i>{{ __('PDF') }}
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fas fa-clipboard-check fs-1 opacity-50 mb-2 d-block"></i>
                                <h6 class="fw-bold">{{ __('No Booking or Expense Activity Recorded for this Period') }}</h6>
                                <p class="small text-muted">{{ __('Select another date range or record new bookings / expenses.') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($staffStats) > 0)
                    <tfoot class="table-light border-top border-2 border-dark">
                        <tr class="fw-bold">
                            <td colspan="3" class="ps-3 py-3 text-uppercase text-dark">{{ __('Grand Total / الإجمالي الكلي') }}:</td>
                            <td class="text-center py-3">
                                <span class="badge bg-dark font-monospace fs-6 px-2 py-1">{{ $grandSeats }}</span>
                            </td>
                            <td class="text-end py-3 font-monospace text-dark">{{ format_currency($grandSales) }}</td>
                            <td class="text-end py-3 font-monospace text-success">{{ format_currency($grandCashIn) }}</td>
                            <td class="text-end py-3 font-monospace text-secondary">{{ format_currency($grandPendingDue) }}</td>
                            <td class="text-end py-3 font-monospace text-danger">{{ format_currency($grandExpenses) }}</td>
                            <td class="py-3 text-end py-3 font-monospace text-success fs-6">
                                {{ format_currency($grandNetCash) }}
                            </td>
                            <td class="pe-3 py-3 text-center">
                                <a href="{{ route('travel.daily_report.pdf', request()->query()) }}" target="_blank" class="btn btn-xs btn-dark shadow-xs py-1 px-2" title="{{ __('Full Statement PDF') }}">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    {{-- SECTION 2: TABS FOR DETAILED INDIVIDUAL BREAKDOWNS --}}
    <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs card-header-tabs m-0 px-3 pt-2" id="detailTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold py-3 px-4 d-flex align-items-center gap-2" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#tabBookings" type="button" role="tab">
                        <i class="fas fa-ticket-alt text-primary"></i>
                        <span>{{ __('Seat Bookings Breakdown') }}</span>
                        <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $bookings->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold py-3 px-4 d-flex align-items-center gap-2" id="expenses-tab" data-bs-toggle="tab" data-bs-target="#tabExpenses" type="button" role="tab">
                        <i class="fas fa-receipt text-danger"></i>
                        <span>{{ __('Daily Expenses Breakdown') }}</span>
                        <span class="badge bg-danger-subtle text-danger rounded-pill">{{ $expenses->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="tab-content" id="detailTabsContent">
                {{-- TAB A: BOOKINGS --}}
                <div class="tab-pane fade show active" id="tabBookings" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Time') }}</th>
                                    <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Booking Ref') }}</th>
                                    <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Passenger') }}</th>
                                    <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Trip & Route') }}</th>
                                    <th class="py-3 text-center text-muted extra-small text-uppercase fw-bold">{{ __('Seat #') }}</th>
                                    <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Agent') }}</th>
                                    <th class="py-3 text-end text-muted extra-small text-uppercase fw-bold">{{ __('Total Fare') }}</th>
                                    <th class="py-3 text-end text-muted extra-small text-uppercase fw-bold text-success">{{ __('Paid') }}</th>
                                    <th class="py-3 text-end text-muted extra-small text-uppercase fw-bold text-danger">{{ __('Due') }}</th>
                                    <th class="pe-3 py-3 text-end text-muted extra-small text-uppercase fw-bold">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $b)
                                <tr>
                                    <td class="ps-3 py-3 font-monospace small text-muted">
                                        {{ $b->created_at->format('h:i A') }}
                                        <div class="extra-small text-muted">{{ $b->created_at->format('d/m/Y') }}</div>
                                    </td>
                                    <td class="py-3 font-monospace fw-bold text-primary">
                                        <a href="{{ route('travel.bookings.show', $b->id) }}" class="text-decoration-none">
                                            {{ $b->booking_reference }}
                                        </a>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-bold text-dark">{{ $b->passenger_name }}</div>
                                        <div class="extra-small text-muted" dir="ltr">{{ $b->passenger_phone }}</div>
                                    </td>
                                    <td class="py-3">
                                        <div class="small fw-semibold text-dark">{{ $b->trip?->title ?: __('Trip') }}</div>
                                        <div class="extra-small text-muted">
                                            {{ $b->trip?->departure_city }} &rarr; {{ $b->trip?->destination_city }}
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="badge bg-primary font-monospace px-2 py-1">
                                            #{{ $b->seat_number }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-user-tie text-secondary me-1"></i>{{ $b->effective_salesman }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-end font-monospace fw-bold text-dark">
                                        {{ format_currency($b->total_amount) }}
                                    </td>
                                    <td class="py-3 text-end font-monospace fw-bold text-success">
                                        {{ format_currency($b->paid_amount) }}
                                    </td>
                                    <td class="py-3 text-end font-monospace text-danger">
                                        {{ format_currency($b->balance_due) }}
                                    </td>
                                    <td class="pe-3 py-3 text-end">
                                        <a href="{{ route('travel.bookings.voucher', $b->id) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="{{ __('Print Ticket Voucher') }}">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">
                                        {{ __('No seat bookings recorded on this date.') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB B: EXPENSES --}}
                <div class="tab-pane fade" id="tabExpenses" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Date') }}</th>
                                    <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Office / Branch') }}</th>
                                    <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Category') }}</th>
                                    <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Expense Title & Notes') }}</th>
                                    <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Staff') }}</th>
                                    <th class="py-3 text-muted extra-small text-uppercase fw-bold">{{ __('Method') }}</th>
                                    <th class="py-3 text-end text-muted extra-small text-uppercase fw-bold text-danger">{{ __('Amount') }}</th>
                                    <th class="pe-3 py-3 text-end text-muted extra-small text-uppercase fw-bold">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $exp)
                                <tr>
                                    <td class="ps-3 py-3 font-monospace small text-muted">
                                        {{ $exp->expense_date->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-dark border">
                                            <i class="fas fa-building text-primary me-1"></i>{{ $exp->shop?->name ?: __('Office') }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        {!! $exp->categoryBadge() !!}
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-bold text-dark">{{ $exp->title }}</div>
                                        @if($exp->reference_no)
                                            <div class="extra-small text-muted font-monospace">Ref: {{ $exp->reference_no }}</div>
                                        @endif
                                        @if($exp->notes)
                                            <div class="extra-small text-muted text-truncate" style="max-width: 250px;">{{ $exp->notes }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <div class="small fw-semibold text-dark">{{ $exp->user?->name ?: __('Staff') }}</div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-light text-secondary border small">{{ $exp->paymentMethodLabel() }}</span>
                                    </td>
                                    <td class="py-3 text-end font-monospace fw-bold text-danger">
                                        {{ format_currency($exp->amount) }}
                                    </td>
                                    <td class="pe-3 py-3 text-end">
                                        <a href="{{ route('expenses.single_pdf', $exp->id) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="{{ __('Print Voucher') }}">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        {{ __('No daily expenses recorded on this date.') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.searchable_select')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.initSearchableFilterSelect === 'function') {
        if (document.getElementById('reportShopSelect')) {
            window.initSearchableFilterSelect('reportShopSelect', {
                placeholder: "{{ __('All Offices / Branches') }}",
                searchPlaceholder: "{{ __('Search office...') }}"
            });
        }
        if (document.getElementById('reportStaffSelect')) {
            window.initSearchableFilterSelect('reportStaffSelect', {
                placeholder: "{{ __('All Staff Members') }}",
                searchPlaceholder: "{{ __('Search staff...') }}"
            });
        }
    }
});
</script>
@endpush
@endsection
