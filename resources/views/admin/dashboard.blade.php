@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row g-3 mb-4 kpi-row-5">
    <!-- KPI Cards (5 in 1 row) -->
    <div class="kpi-col-5">
        <div class="card-modern kpi-card kpi-card-sm bg-gradient-indigo h-100">
            <div class="icon-box">
                <i class="fas fa-users"></i>
            </div>
            <h6 class="text-white-50 text-uppercase fw-semibold">Total Customers</h6>
            <h2 class="mb-0 fw-bold">{{ $stats['customers_count'] }}</h2>
        </div>
    </div>
    
    <div class="kpi-col-5">
        <div class="card-modern kpi-card kpi-card-sm bg-gradient-emerald h-100">
            <div class="icon-box">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <h6 class="text-white-50 text-uppercase fw-semibold">Total Invoices</h6>
            <h2 class="mb-0 fw-bold">{{ $stats['invoices_count'] }}</h2>
        </div>
    </div>

    <div class="kpi-col-5">
        <div class="card-modern kpi-card kpi-card-sm bg-gradient-amber h-100">
            <div class="icon-box">
                <i class="fas fa-check-circle"></i>
            </div>
            <h6 class="text-white-50 text-uppercase fw-semibold">Paid Amount</h6>
            <h2 class="mb-0 fw-bold">{{ format_currency($stats['total_paid']) }}</h2>
        </div>
    </div>

    <div class="kpi-col-5">
        <div class="card-modern kpi-card kpi-card-sm bg-gradient-rose h-100">
            <div class="icon-box">
                <i class="fas fa-clock"></i>
            </div>
            <h6 class="text-white-50 text-uppercase fw-semibold">Pending Amount</h6>
            <h2 class="mb-0 fw-bold">{{ format_currency($stats['total_pending']) }}</h2>
        </div>
    </div>

    <div class="kpi-col-5">
        <div class="card-modern kpi-card kpi-card-sm bg-gradient-purple h-100">
            <div class="icon-box">
                <i class="fas fa-tags"></i>
            </div>
            <h6 class="text-white-50 text-uppercase fw-semibold">Total Discount</h6>
            <h2 class="mb-0 fw-bold">{{ format_currency($stats['total_discount']) }}</h2>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <x-card title="Recent Invoices" :padding="false">
            <x-slot name="headerActions">
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-link text-decoration-none fw-medium">View All</a>
            </x-slot>

            <x-table>
                <x-slot name="thead">
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </x-slot>

                @foreach($recentInvoices as $invoice)
                <tr>
                    <td class="fw-medium text-dark">{{ $invoice->invoice_number }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            {{ $invoice->customer->name }}
                        </div>
                    </td>
                    <td class="fw-semibold text-dark">{{ format_currency($invoice->grand_total) }}</td>
                    <td>
                        <span class="badge-soft {{ $invoice->status == 'Paid' ? 'badge-soft-success' : 'badge-soft-warning' }}">
                            {{ $invoice->status }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-sm btn-light rounded-circle shadow-none">
                            <i class="fas fa-eye text-muted"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </x-table>
        </x-card>
    </div>

    <div class="col-lg-4">
        <x-card title="Quick Actions">
            <div class="d-grid gap-3">
                <a href="{{ route('quotes.create') }}" class="btn btn-primary-modern text-start d-flex align-items-center">
                    <i class="fas fa-file-invoice me-3"></i> 
                    <div>
                        <span class="d-block fw-bold">Create New Quote</span>
                        <small class="text-white-50 fw-normal">Generate a professional quote</small>
                    </div>
                </a>
                
                <a href="{{ route('invoices.create') }}" class="btn btn-outline-primary border-2 text-start d-flex align-items-center py-3 rounded-3 group">
                    <i class="fas fa-file-invoice-dollar me-3 fa-lg opacity-75"></i>
                    <div>
                        <span class="d-block fw-bold text-dark">Create New Invoice</span>
                        <small class="text-muted fw-normal">Bill your customers easily</small>
                    </div>
                </a>

                <a href="{{ route('customers.create') }}" class="btn btn-outline-info border-2 text-start d-flex align-items-center py-3 rounded-3">
                    <i class="fas fa-user-plus me-3 fa-lg opacity-75"></i>
                    <div>
                        <span class="d-block fw-bold text-dark">Add New Customer</span>
                        <small class="text-muted fw-normal">Expand your client base</small>
                    </div>
                </a>

                <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary border-2 text-start d-flex align-items-center py-3 rounded-3">
                    <i class="fas fa-cog me-3 fa-lg opacity-75"></i>
                    <div>
                        <span class="d-block fw-bold text-dark">System Settings</span>
                        <small class="text-muted fw-normal">Configure your system</small>
                    </div>
                </a>
            </div>
        </x-card>
    </div>
</div>
@endsection
