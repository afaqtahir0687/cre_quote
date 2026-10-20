@extends('admin.layouts.app')

@section('title', 'Employee Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <!-- KPI Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card-modern kpi-card bg-gradient-indigo">
            <div class="icon-box">
                <i class="fas fa-file-invoice fa-lg"></i>
            </div>
            <h6 class="text-white-50 text-uppercase mb-2 fw-semibold">My Quotes</h6>
            <h2 class="mb-0 fw-bold">{{ $stats['quotes_count'] }}</h2>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card-modern kpi-card bg-gradient-emerald">
            <div class="icon-box">
                <i class="fas fa-file-invoice-dollar fa-lg"></i>
            </div>
            <h6 class="text-white-50 text-uppercase mb-2 fw-semibold">My Invoices</h6>
            <h2 class="mb-0 fw-bold">{{ $stats['invoices_count'] }}</h2>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card-modern kpi-card bg-gradient-amber">
            <div class="icon-box">
                <i class="fas fa-tasks fa-lg"></i>
            </div>
            <h6 class="text-white-50 text-uppercase mb-2 fw-semibold">My Job Orders</h6>
            <h2 class="mb-0 fw-bold">{{ $stats['job_orders_count'] }}</h2>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card-modern kpi-card bg-gradient-rose">
            <div class="icon-box">
                <i class="fas fa-tags fa-lg"></i>
            </div>
            <h6 class="text-white-50 text-uppercase mb-2 fw-semibold">Total Customer Discount</h6>
            <h2 class="mb-0 fw-bold">{{ format_currency($stats['total_discount']) }}</h2>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <x-card title="Recent Quotes" :padding="false">
            <x-slot name="headerActions">
                <a href="{{ route('quotes.index') }}" class="btn btn-sm btn-link text-decoration-none fw-medium">View All</a>
            </x-slot>

            <x-table>
                <x-slot name="thead">
                    <th>Quote #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </x-slot>

                @foreach($recentQuotes as $quote)
                <tr>
                    <td class="fw-medium text-dark">{{ $quote->quote_number }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            
                            {{ $quote->customer->name }}
                        </div>
                    </td>
                    <td class="fw-semibold text-dark">{{ format_currency($quote->grand_total) }}</td>
                    <td>
                        <span class="badge-soft {{ $quote->status == 'Accepted' ? 'badge-soft-success' : 'badge-soft-warning' }}">
                            {{ $quote->status }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('quotes.show', $quote->id) }}" class="btn btn-sm btn-light rounded-circle shadow-none">
                            <i class="fas fa-eye text-muted"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </x-table>
        </x-card>
    </div>

    <div class="col-lg-6">
        <x-card title="Recent Job Orders" :padding="false">
            <x-slot name="headerActions">
                <a href="{{ route('job-orders.index') }}" class="btn btn-sm btn-link text-decoration-none fw-medium">View All</a>
            </x-slot>

            <x-table>
                <x-slot name="thead">
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </x-slot>

                @foreach($recentJobOrders as $jobOrder)
                <tr>
                    <td class="fw-medium text-dark">{{ $jobOrder->order_number }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            
                            {{ $jobOrder->customer->name }}
                        </div>
                    </td>
                    <td>{{ $jobOrder->order_date ?? 'N/A' }}</td>
                    <td>
                        <span class="badge-soft {{ $jobOrder->status == 'completed' ? 'badge-soft-success' : 'badge-soft-warning' }}">
                            {{ ucfirst($jobOrder->status) }}
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('job-orders.show', $jobOrder->id) }}" class="btn btn-sm btn-light rounded-circle shadow-none">
                            <i class="fas fa-eye text-muted"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </x-table>
        </x-card>
    </div>
</div>

<div class="row g-4 mt-4">
    <div class="col-lg-12">
        <x-card title="Quick Actions">
            <div class="d-grid gap-3 d-md-flex">
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

                <a href="{{ route('job-orders.create') }}" class="btn btn-outline-success border-2 text-start d-flex align-items-center py-3 rounded-3">
                    <i class="fas fa-tasks me-3 fa-lg opacity-75"></i>
                    <div>
                        <span class="d-block fw-bold text-dark">Create Job Order</span>
                        <small class="text-muted fw-normal">Create a new job order</small>
                    </div>
                </a>
            </div>
        </x-card>
    </div>
</div>
@endsection
