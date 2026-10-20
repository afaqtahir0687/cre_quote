@extends('admin.layouts.app')

@section('title', 'Manage Invoices')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">Invoices</h2>
    <div class="d-flex gap-2">
        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2 mb-0">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Search Name, Invoice #..." onkeypress="if(event.keyCode == 13) this.form.submit()">
            </div>
            <input type="date" name="date" value="{{ request('date') }}" class="form-control" style="max-width: 150px; cursor: pointer;" title="Filter by date" onclick="this.showPicker()" onchange="this.form.submit()">
            @if(request()->has('search') || request()->has('date'))
                <a href="{{ url()->current() }}" class="btn btn-light border" title="Clear Filters"><i class="fas fa-times"></i></a>
            @endif
        </form>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary-modern text-nowrap">
            <i class="fas fa-plus me-2"></i> Create New Invoice
        </a>
    </div>
</div>

<x-card :padding="false">
    <x-table>
        <x-slot name="thead">
            <th>Invoice #</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Employee</th>
            <th>Grand Total</th>
            <th>Paid</th>
            <th>Due</th>
            <th>Status</th>
            <th class="text-end">Action</th>
        </x-slot>

        @foreach($invoices as $invoice)
        <tr class="invoice-row">
            <td class="fw-bold text-primary">{{ $invoice->invoice_number }}</td>
            <td>
                <div class="fw-bold">{{ $invoice->customer->name ?? '' }}</div>
                @if(!empty($invoice->customer->company_name))
                    <div class="small text-muted"><i class="fas fa-building me-1"></i>{{ $invoice->customer->company_name }}</div>
                @endif
                @if(!empty($invoice->customer->phone))
                    <div class="small text-muted mt-1"><i class="fas fa-phone-alt me-1 text-primary"></i>{{ $invoice->customer->phone }}</div>
                @endif
            </td>
            <td class="text-muted text-nowrap"><span class="d-none">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') }}</span>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-M-Y') }}</td>
            <td>
                @if($invoice->employee)
                    <div class="d-flex align-items-center">
                        <span class="text-muted small">{{ $invoice->employee->name }}</span>
                    </div>
                @else
                    <span class="badge bg-light text-muted border" style="font-size: 0.75rem;">N/A</span>
                @endif
            </td>
            <td class="fw-semibold text-dark">{{ format_currency($invoice->grand_total) }}</td>
            <td class="text-success fw-medium">{{ format_currency($invoice->amount_paid) }}</td>
            <td class="text-danger fw-medium">{{ format_currency($invoice->balance_due) }}</td>
            <td>
                @php
                    $statusClass = match(ucfirst(strtolower($invoice->status))) {
                        'Paid' => 'badge-soft-success',
                        'Partially Paid' => 'badge-soft-info',
                        'Unpaid' => 'badge-soft-danger',
                        'Cancelled' => 'badge-soft-danger',
                        default => 'badge-soft-warning'
                    };
                @endphp
                <span class="badge-soft {{ $statusClass }}">
                    {{ $invoice->status }}
                </span>
            </td>
            <td class="text-end">
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-circle shadow-none" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item py-2" href="{{ route('invoices.show', $invoice) }}"><i class="fas fa-eye me-2 text-primary"></i> View Detail</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('invoices.edit', $invoice) }}"><i class="fas fa-edit me-2 text-info"></i> Edit</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('invoices.pdf', $invoice) }}"><i class="fas fa-file-pdf me-2 text-danger"></i> Download PDF</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="fas fa-trash me-2"></i> Delete
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        @endforeach
    </x-table>
    
    @if($invoices->hasPages())
        <div class="card-footer bg-transparent border-top p-4">
            {{ $invoices->links('pagination::bootstrap-5') }}
        </div>
    @endif
</x-card>
@endsection


@section('scripts')
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! All payments and items will be deleted.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
