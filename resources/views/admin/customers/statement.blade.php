@extends('admin.layouts.app')

@section('title', 'Customer Statement')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Statement of Account</h2>
        @php
            $defComp = $customer->defaultCompany();
            $cName = $defComp?->company_name ?: $customer->company_name;
        @endphp
        <p class="text-muted mb-0">{{ $customer->name }}{{ $cName ? ' - ' . $cName : '' }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('customers.index') }}" class="btn btn-light border">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>
        <a href="{{ route('customers.statement_pdf', $customer->id) }}" class="btn btn-danger" >
            <i class="fas fa-file-pdf me-2"></i> Download PDF
        </a>
    </div>
</div>

@php
    $totalBilled = $invoices->sum('grand_total');
    $totalPaid = $invoices->sum('amount_paid');
    $totalDue = $totalBilled - $totalPaid;
@endphp

<div class="row mb-4">
    <div class="col-md-4">
        <x-card class="bg-primary text-white">
            <div class="d-flex align-items-center">
                <div class="fs-1 me-3 opacity-50"><i class="fas fa-file-invoice-dollar"></i></div>
                <div>
                    <h5 class="mb-1 text-white opacity-75">Total Billed</h5>
                    <h3 class="mb-0 fw-bold text-white">{{ format_currency($totalBilled) }}</h3>
                </div>
            </div>
        </x-card>
    </div>
    <div class="col-md-4">
        <x-card class="bg-success text-white">
            <div class="d-flex align-items-center">
                <div class="fs-1 me-3 opacity-50"><i class="fas fa-money-bill-wave"></i></div>
                <div>
                    <h5 class="mb-1 text-white opacity-75">Total Paid</h5>
                    <h3 class="mb-0 fw-bold text-white">{{ format_currency($totalPaid) }}</h3>
                </div>
            </div>
        </x-card>
    </div>
    <div class="col-md-4">
        <x-card class="bg-danger text-white">
            <div class="d-flex align-items-center">
                <div class="fs-1 me-3 opacity-50"><i class="fas fa-exclamation-circle"></i></div>
                <div>
                    <h5 class="mb-1 text-white opacity-75">Total Due</h5>
                    <h3 class="mb-0 fw-bold text-white">{{ format_currency($totalDue) }}</h3>
                </div>
            </div>
        </x-card>
    </div>
</div>

<x-card :padding="false">
    <x-table>
        <x-slot name="thead">
            <th>Date</th>
            <th>Invoice #</th>
            <th class="text-end">Amount</th>
            <th class="text-end">Paid</th>
            <th class="text-end">Balance</th>
            <th class="text-center">Status</th>
        </x-slot>

        @forelse($invoices as $invoice)
            <tr>
                <td class="text-muted">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-M-Y') }}</td>
                <td>
                    <a href="{{ route('invoices.show', $invoice->id) }}" class="fw-bold text-primary text-decoration-none">
                        {{ $invoice->invoice_number }}
                    </a>
                </td>
                <td class="text-end fw-semibold text-dark">{{ format_currency($invoice->grand_total) }}</td>
                <td class="text-end text-success fw-medium">{{ format_currency($invoice->amount_paid) }}</td>
                <td class="text-end text-danger fw-medium">{{ format_currency($invoice->balance_due) }}</td>
                <td class="text-center">
                    @php
                        $statusClass = match($invoice->status) {
                            'Paid' => 'badge-soft-success',
                            'Partially Paid' => 'badge-soft-info',
                            'Unpaid' => 'badge-soft-danger',
                            default => 'badge-soft-secondary'
                        };
                    @endphp
                    <span class="badge-soft {{ $statusClass }}">{{ $invoice->status }}</span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                    <i class="fas fa-folder-open fa-3x mb-3 opacity-50"></i>
                    <p class="mb-0">No invoices found for this customer.</p>
                </td>
            </tr>
        @endforelse
    </x-table>
</x-card>
@endsection
