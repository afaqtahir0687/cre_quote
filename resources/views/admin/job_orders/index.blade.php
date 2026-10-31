@extends('admin.layouts.app')

@section('title', 'Job Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">Job Orders</h2>
    <div class="d-flex gap-2">
        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2 mb-0">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Search Name, Job Order #..." onkeypress="if(event.keyCode == 13) this.form.submit()">
            </div>
            <input type="date" name="date" value="{{ request('date') }}" class="form-control" style="max-width: 150px; cursor: pointer;" title="Filter by date" onclick="this.showPicker()" onchange="this.form.submit()">
            @if(request()->has('search') || request()->has('date'))
                <a href="{{ url()->current() }}" class="btn btn-light border" title="Clear Filters"><i class="fas fa-times"></i></a>
            @endif
        </form>
        <a href="{{ route('job-orders.statement-pdf', array_filter(['date' => request('date'), 'search' => request('search')])) }}" 
           class="btn btn-outline-danger d-flex align-items-center text-nowrap shadow-sm" 
           target="_blank"
           title="Download Statement PDF">
            <i class="fas fa-file-pdf me-2"></i> 
            <span>{{ request('date') ? 'Statement (' . \Carbon\Carbon::parse(request('date'))->format('d-M-Y') . ')' : 'Daily Statement' }}</span>
        </a>
        <a href="{{ route('job-orders.create') }}" class="btn btn-primary-modern text-nowrap">
            <i class="fas fa-plus me-2"></i> Create Job Order
        </a>
    </div>
</div>

@if(request()->filled('date'))
    <div class="alert alert-light border border-primary-subtle d-flex justify-content-between align-items-center mb-3 py-2 px-3 rounded-3 bg-primary-subtle bg-opacity-10 shadow-sm">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-calendar-check text-primary fs-5"></i>
            <div>
                <span class="fw-bold text-dark">Statement Date:</span>
                <span class="badge bg-primary fs-6 ms-1">{{ \Carbon\Carbon::parse(request('date'))->format('d-M-Y') }}</span>
                <span class="text-muted ms-2 small">({{ $jobOrders->total() }} {{ Str::plural('Job Order', $jobOrders->total()) }} found)</span>
            </div>
        </div>
        <a href="{{ route('job-orders.statement-pdf', array_filter(['date' => request('date'), 'search' => request('search')])) }}" class="btn btn-sm btn-danger d-flex align-items-center shadow-sm" target="_blank">
            <i class="fas fa-file-pdf me-2"></i> Download Statement PDF
        </a>
    </div>
@endif

<x-card>
    @if($jobOrders->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No Job Orders Found</h5>
            <p class="text-muted small">Create a Job Order from a Quote to get started.</p>
            <a href="{{ route('quotes.index') }}" class="btn btn-primary-modern">
                <i class="fas fa-plus me-2"></i> View Quotes
            </a>
        </div>
    @else
        <x-table>
            <x-slot name="thead">
                <th>Order #</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Employee</th>
                <th>Status</th>
                <th>Total Amount</th>
                <th>Paid</th>
                <th>Balance</th>
                <th class="text-end pe-4">Action</th>
            </x-slot>

                @foreach($jobOrders as $jobOrder)
                <tr class="job-order-row">
                    <td><strong>{{ $jobOrder->order_number }}</strong></td>
                    <td>
                        <div class="fw-bold">{{ $jobOrder->customer->name ?? '' }}</div>
                        @if(!empty($jobOrder->customer->company_name))
                            <div class="small text-muted"><i class="fas fa-building me-1"></i>{{ $jobOrder->customer->company_name }}</div>
                        @endif
                        @if(!empty($jobOrder->customer->phone))
                            <div class="small text-muted mt-1"><i class="fas fa-phone-alt me-1"></i>{{ $jobOrder->customer->phone }}</div>
                        @endif
                    </td>
                    <td class="text-nowrap"><span class="d-none">{{ \Carbon\Carbon::parse($jobOrder->order_date ?? now())->format('Y-m-d') }}</span>{{ \Carbon\Carbon::parse($jobOrder->order_date ?? now())->format('d-M-Y') }}</td>
                    <td>
                        <button type="button" 
                                class="btn btn-sm {{ $jobOrder->employee ? 'btn-light border text-dark' : 'btn-outline-primary' }} d-inline-flex align-items-center gap-1 py-1 px-2 rounded-pill shadow-none assign-emp-trigger"
                                style="font-size: 0.8rem; {{ !$jobOrder->employee ? 'border-style: dashed !important;' : '' }}"
                                data-bs-toggle="modal" 
                                data-bs-target="#assignJobOrderModal"
                                data-id="{{ $jobOrder->id }}"
                                data-number="{{ $jobOrder->order_number }}"
                                data-current-emp-id="{{ $jobOrder->employee_id ?? '' }}"
                                title="Click to assign / change employee">
                            <i class="fas {{ $jobOrder->employee ? 'fa-user-circle text-primary' : 'fa-user-plus text-primary' }}"></i>
                            <span class="fw-medium">{{ $jobOrder->employee->name ?? 'Assign' }}</span>
                            <i class="fas fa-edit text-muted ms-1" style="font-size: 0.65rem;"></i>
                        </button>
                    </td>
                    <td>
                        @php
                            $statusClass = match(strtolower($jobOrder->status)) {
                                'completed' => 'badge-soft-success',
                                'draft' => 'badge-soft-warning',
                                'cancelled' => 'badge-soft-danger',
                                default => 'badge-soft-info'
                            };
                        @endphp
                        <span class="badge-soft {{ $statusClass }}">{{ ucfirst($jobOrder->status) }}</span>
                    </td>
                    <td>{{ format_currency($jobOrder->total_amount) }}</td>
                    <td class="text-success">{{ format_currency($jobOrder->paid_amount) }}</td>
                    <td class="text-danger">{{ format_currency($jobOrder->remaining_balance) }}</td>
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('job-orders.show', $jobOrder->id) }}">
                                        <i class="fas fa-eye me-2 text-info"></i> View
                                    </a>
                                </li>
                                @if($jobOrder->status !== 'cancelled')
                                <li>
                                    <a class="dropdown-item" href="{{ route('job-orders.edit', $jobOrder->id) }}">
                                        <i class="fas fa-edit me-2 text-warning"></i> Edit
                                    </a>
                                </li>
                                @endif
                                <li>
                                    <a class="dropdown-item" href="{{ route('job-orders.pdf', $jobOrder->id) }}">
                                        <i class="fas fa-file-pdf me-2 text-danger"></i> PDF
                                    </a>
                                </li>
                                @if($jobOrder->status !== 'cancelled')
                                <li>
                                    <a class="dropdown-item" href="{{ route('job-orders.invoice', $jobOrder->id) }}">
                                        <i class="fas fa-file-invoice-dollar me-2 text-success"></i> Convert to Invoice
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('job-orders.cancel', $jobOrder->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel Job Order #{{ $jobOrder->order_number }}? All amounts and totals will be set to 0.00.');">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-ban me-2"></i> Cancel Order
                                        </button>
                                    </form>
                                </li>
                                @endif
                                @if(auth()->user()->role !== 'employee')
                                <li>
                                    <button type="button" class="dropdown-item py-2 assign-emp-trigger"
                                            data-bs-toggle="modal" data-bs-target="#assignJobOrderModal"
                                            data-id="{{ $jobOrder->id }}"
                                            data-number="{{ $jobOrder->order_number }}"
                                            data-current-emp-id="{{ $jobOrder->employee_id ?? '' }}">
                                        <i class="fas fa-user-tag me-2 text-warning"></i> Assign / Reclaim
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @endif
                                <li>
                                    <form action="{{ route('job-orders.destroy', $jobOrder->id) }}" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
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
        
        @if($jobOrders->hasPages())
            <div class="card-footer bg-transparent border-top p-4">
                {{ $jobOrders->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</x-card>

    <!-- Assign Employee Modal -->
    <div class="modal fade" id="assignJobOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fas fa-user-tag text-primary me-2"></i> Assign Job Order <span id="modalJobOrderNumber" class="text-primary"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="assignJobOrderForm" method="POST" action="">
                    @csrf
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-3">Select an employee to assign this job order to, or choose <strong>Admin (Myself / Reclaim)</strong> to reclaim full control.</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Assignee / Owner</label>
                            <select name="employee_id" id="modalJobOrderEmployeeSelect" class="form-select">
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
        const joModal = document.getElementById('assignJobOrderModal');
        if (joModal) {
            const form = document.getElementById('assignJobOrderForm');
            const numberSpan = document.getElementById('modalJobOrderNumber');
            const select = document.getElementById('modalJobOrderEmployeeSelect');

            joModal.addEventListener('show.bs.modal', function(event) {
                const btn = event.relatedTarget;
                if (!btn) return;
                const id = btn.getAttribute('data-id');
                const num = btn.getAttribute('data-number');
                const empId = btn.getAttribute('data-current-emp-id');

                numberSpan.textContent = num ? `#${num}` : '';
                select.value = empId || '';
                form.action = `/job-orders/${id}/assign`;
            });
        }
    });
</script>
@endsection
