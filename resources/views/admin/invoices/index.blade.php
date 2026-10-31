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
                <button type="button" 
                        class="btn btn-sm {{ $invoice->employee ? 'btn-light border text-dark' : 'btn-outline-primary' }} d-inline-flex align-items-center gap-1 py-1 px-2 rounded-pill shadow-none assign-emp-trigger"
                        style="font-size: 0.8rem; {{ !$invoice->employee ? 'border-style: dashed !important;' : '' }}"
                        data-bs-toggle="modal" 
                        data-bs-target="#assignInvoiceModal"
                        data-id="{{ $invoice->id }}"
                        data-number="{{ $invoice->invoice_number }}"
                        data-current-emp-id="{{ $invoice->employee_id ?? '' }}"
                        title="Click to assign / change employee">
                    <i class="fas {{ $invoice->employee ? 'fa-user-circle text-primary' : 'fa-user-plus text-primary' }}"></i>
                    <span class="fw-medium">{{ $invoice->employee->name ?? 'Assign' }}</span>
                    <i class="fas fa-edit text-muted ms-1" style="font-size: 0.65rem;"></i>
                </button>
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
                        @if(auth()->user()->role !== 'employee')
                        <li>
                            <button type="button" class="dropdown-item py-2 assign-emp-trigger"
                                    data-bs-toggle="modal" data-bs-target="#assignInvoiceModal"
                                    data-id="{{ $invoice->id }}"
                                    data-number="{{ $invoice->invoice_number }}"
                                    data-current-emp-id="{{ $invoice->employee_id ?? '' }}">
                                <i class="fas fa-user-tag me-2 text-warning"></i> Assign / Reclaim
                            </button>
                        </li>
                        @endif
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

    <!-- Assign Employee Modal -->
    <div class="modal fade" id="assignInvoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fas fa-user-tag text-primary me-2"></i> Assign Invoice <span id="modalInvoiceNumber" class="text-primary"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignInvoiceForm" method="POST" action="">
                    @csrf
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-3">Select an employee to assign this invoice to, or choose <strong>Admin (Myself / Reclaim)</strong> to reclaim full control.</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Assignee / Owner</label>
                            <select name="employee_id" id="modalInvoiceEmployeeSelect" class="form-select">
                                <option value="">-- Admin (Myself / Reclaim) --</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">
                                        {{ $emp->name }} ({{ $emp->designation ?? 'Staff' }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text text-muted">
                                Selecting <em>Admin (Myself / Reclaim)</em> unassigns employees and keeps it under Admin ownership.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-modern">
                            <i class="fas fa-save me-1"></i> Save Assignment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const invModal = document.getElementById('assignInvoiceModal');
        if (invModal) {
            const form = document.getElementById('assignInvoiceForm');
            const numberSpan = document.getElementById('modalInvoiceNumber');
            const select = document.getElementById('modalInvoiceEmployeeSelect');

            invModal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                if (!btn) return;
                const id = btn.getAttribute('data-id');
                const num = btn.getAttribute('data-number');
                const empId = btn.getAttribute('data-current-emp-id');

                numberSpan.textContent = num ? `#${num}` : '';
                select.value = empId || '';
                form.action = `/invoices/${id}/assign`;
            });
        }
    });
</script>
@endsection
