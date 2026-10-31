@extends('admin.layouts.app')

@section('title', 'Job Order ' . $jobOrder->order_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('job-orders.index') }}" class="btn btn-light btn-sm rounded-circle me-3 shadow-sm">
            <i class="fas fa-arrow-left text-muted"></i>
        </a>
        <h2 class="fw-bold text-dark mb-0">Job Order Detail</h2>
        <span class="badge-soft badge-soft-primary ms-3">{{ $jobOrder->order_number }}</span>
        @if($jobOrder->status === 'cancelled')
            <span class="badge bg-danger ms-2"><i class="fas fa-ban me-1"></i> Cancelled</span>
        @elseif($jobOrder->remaining_balance <= 0 && ($jobOrder->grand_total ?? $jobOrder->total_amount) > 0)
            <span class="badge bg-success ms-2"><i class="fas fa-check-circle me-1"></i> Paid</span>
        @elseif($jobOrder->paid_amount > 0)
            <span class="badge bg-warning text-dark ms-2"><i class="fas fa-clock me-1"></i> Partial Paid</span>
        @else
            <span class="badge bg-secondary ms-2"><i class="fas fa-hourglass-start me-1"></i> Unpaid</span>
        @endif
    </div>
    <div class="d-flex gap-2">
        <div class="btn-group">
            <a href="{{ route('job-orders.pdf', $jobOrder->id) }}" class="btn btn-outline-danger d-flex align-items-center shadow-sm">
                <i class="fas fa-file-pdf me-2"></i> Download PDF
            </a>
            <button type="button" class="btn btn-outline-danger dropdown-toggle dropdown-toggle-split shadow-sm" data-bs-toggle="dropdown" aria-expanded="false" title="PDF Options">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li>
                    <a class="dropdown-item py-2" href="{{ route('job-orders.pdf', ['id' => $jobOrder->id, 'show_customer_name' => 1]) }}">
                        <i class="fas fa-user-check text-success me-2"></i> With Customer Name
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('job-orders.pdf', ['id' => $jobOrder->id, 'show_customer_name' => 0]) }}">
                        <i class="fas fa-user-slash text-danger me-2"></i> Without Customer Name
                    </a>
                </li>
            </ul>
        </div>
        @if($jobOrder->status !== 'cancelled')
            <a href="{{ route('job-orders.edit', $jobOrder->id) }}" class="btn btn-outline-info d-flex align-items-center shadow-sm">
                <i class="fas fa-edit me-2"></i> Edit
            </a>
            <button type="button" class="btn btn-outline-danger d-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#cancelJobOrderModal">
                <i class="fas fa-ban me-2"></i> Cancel Order
            </button>
        @endif
    </div>
</div>

@if($jobOrder->status === 'cancelled')
<div class="alert alert-danger d-flex align-items-center mb-4 py-2 px-3 shadow-sm border border-danger-subtle rounded-3">
    <i class="fas fa-ban fs-4 me-3 text-danger"></i>
    <div>
        <strong>This Job Order has been Cancelled!</strong>
        <div class="small">All items, totals, discounts, taxes, and balances have been set to 0.00.</div>
    </div>
</div>
@endif

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <h5 class="mb-2 fw-bold text-dark">{{ $setting->company_name ?? 'My Company' }}</h5>
                <p class="text-muted mb-0">{{ $setting->company_address ?? '' }}<br>
                {{ $setting->company_city ?? '' }} {{ $setting->company_country ?? '' }}<br>
                <i class="fas fa-phone me-1 small"></i> {{ $setting->company_phone ?? '' }}<br>
                <i class="fas fa-envelope me-1 small"></i> {{ $setting->company_email ?? '' }}</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h5 class="mb-2 fw-bold text-primary">Job Order #: {{ $jobOrder->order_number }}</h5>
                @php
                    $bComp = $jobOrder->billing_company;
                @endphp
                <p class="text-muted mb-0">
                <strong>Date:</strong> {{ $jobOrder->order_date ? $jobOrder->order_date->format('d-M-Y') : now()->format('d-M-Y') }}<br>
                <strong>Customer:</strong> {{ $jobOrder->customer?->name ?? 'N/A' }}<br>
                @if($bComp?->company_name || $jobOrder->customer?->company_name)
                    <strong>Company:</strong> {{ $bComp?->company_name ?? $jobOrder->customer?->company_name }}<br>
                @endif
                {{ $bComp?->address ?? ($jobOrder->customer?->address ?? '') }}<br>
                <i class="fas fa-phone me-1 small"></i> {{ $jobOrder->customer?->formatted_phone ?: ($jobOrder->customer?->phone ?? 'N/A') }}<br>
                <div class="mt-2 d-inline-flex align-items-center flex-wrap gap-2">
                    <span><strong>Assigned To:</strong> <span class="fw-bold text-dark">{{ $jobOrder->employee->name ?? 'Admin / Unassigned' }}</span></span>
                    <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2 rounded-pill" data-bs-toggle="modal" data-bs-target="#assignJobOrderModal" title="Reassign Employee">
                        <i class="fas fa-user-edit small"></i> Assign
                    </button>
                </div></p>
            </div>
        </div>

        <h6 class="fw-bold text-dark mt-4 mb-3">Job Items</h6>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:5%">#</th>
                        <th style="width:35%">Description</th>
                        <th style="width:20%">Image</th>
                        <th class="text-end" style="width:10%">Unit Price</th>
                        <th class="text-center" style="width:8%">Qty</th>
                        <th class="text-center" style="width:10%">Tax %</th>
                        <th class="text-end" style="width:12%">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jobOrder->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->description }}</div>
                            @if($item->item_text)
                                <div class="text-muted small">{{ $item->item_text }}</div>
                            @endif
                        </td>
                        <td>
                            @if(!empty($item->images))
                                @php $images = is_string($item->images) ? json_decode($item->images, true) : $item->images; @endphp
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach((array)$images as $img)
                                        <img src="{{ asset($img) }}" class="rounded border" style="width:60px; height:60px; object-fit:cover;" />
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-end">{{ format_currency($item->unit_price) }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">{{ number_format($item->tax_percentage ?? 0, 2) }}%</td>
                        <td class="text-end fw-bold">{{ format_currency($item->total) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end mt-3">
            <div class="col-md-5">
                <div class="d-flex flex-column gap-2 p-3 bg-light rounded-3 shadow-sm border">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Subtotal</span>
                        <span class="fw-semibold text-dark">{{ format_currency($jobOrder->subtotal ?? $jobOrder->items->sum('total')) }}</span>
                    </div>
                    @if($jobOrder->total_tax > 0)
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Total Tax</span>
                        <span class="fw-semibold text-dark">{{ format_currency($jobOrder->total_tax) }}</span>
                    </div>
                    @endif
                    @if($jobOrder->total_discount > 0)
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Total Discount</span>
                        <span class="fw-semibold text-danger">-{{ format_currency($jobOrder->total_discount) }}</span>
                    </div>
                    @endif
                    @if($jobOrder->shipping > 0)
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Shipping</span>
                        <span class="fw-semibold text-dark">{{ format_currency($jobOrder->shipping) }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="fw-bold text-dark">Grand Total</span>
                        <span class="fw-bold text-primary fs-5">{{ format_currency($jobOrder->grand_total ?? $jobOrder->total_amount) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-success fw-bold small">
                        <span>Paid Amount</span>
                        <span>{{ format_currency($jobOrder->paid_amount) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center text-danger fw-bold border-top pt-2">
                        <span>Remaining Balance</span>
                        <span class="fs-5">{{ format_currency($jobOrder->remaining_balance) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($jobOrder->notes)
        <div class="mt-3 p-3 bg-light-subtle border-start border-3 border-warning rounded">
            <h6 class="fw-bold text-dark mb-1">Notes:</h6>
            <p class="text-muted mb-0 small">{{ $jobOrder->notes }}</p>
        </div>
        @endif
    </div>
</div>

<!-- WORK DEPARTMENT ASSIGNMENTS CARD -->
<div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold text-dark mb-0">
                <i class="fas fa-sitemap me-2 text-primary"></i> Department & Work Assignments
            </h5>
            <span class="text-muted small">Track work assigned to Workshop, Banner Machine, Sticker Print, Flex, Sign Board, etc.</span>
        </div>
        @if($jobOrder->status !== 'cancelled')
        <button class="btn btn-primary-modern btn-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
            <i class="fas fa-plus me-2"></i> Assign Department / Worker
        </button>
        @endif
    </div>
    <div class="card-body">
        @if($jobOrder->assignments->isEmpty())
            <div class="text-center py-4 border rounded bg-light-subtle">
                <i class="fas fa-tools text-muted fs-2 mb-2"></i>
                <p class="text-muted mb-2">No departments assigned to this job order yet.</p>
                @if($jobOrder->status !== 'cancelled')
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
                    <i class="fas fa-plus me-1"></i> Assign Workshop / Banner Machine / Flex / Sticker / Sign Board
                </button>
                @endif
            </div>
        @else
            <div class="row g-3">
                @foreach($jobOrder->assignments as $assignment)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border shadow-none">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-primary-subtle text-primary fw-bold text-uppercase px-2 py-1 mb-1 d-inline-block">
                                        <i class="fas fa-industry me-1"></i> {{ $assignment->department_name }}
                                    </span>
                                    <h6 class="fw-bold text-dark mb-0">
                                        <i class="fas fa-user-circle text-muted me-1"></i> {{ $assignment->assigned_display_name }}
                                    </h6>
                                </div>
                                <span class="badge-soft {{ $assignment->status_badge_class }}">
                                    {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                </span>
                            </div>

                            @if($assignment->instructions)
                                <p class="text-muted small mb-2 bg-light p-2 rounded">
                                    <i class="fas fa-info-circle me-1"></i> {{ $assignment->instructions }}
                                </p>
                            @endif

                            <div class="text-muted extra-small mb-3">
                                @if($assignment->started_at)
                                    <div><i class="far fa-clock me-1"></i> Started: {{ $assignment->started_at->format('M d, H:i') }}</div>
                                @endif
                                @if($assignment->completed_at)
                                    <div><i class="fas fa-check-circle me-1 text-success"></i> Completed: {{ $assignment->completed_at->format('M d, H:i') }}</div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-1 pt-2 border-top">
                                @if($assignment->status === 'pending')
                                    <form action="{{ route('job-assignments.update-status', $assignment->id) }}" method="POST" class="d-inline flex-grow-1">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                            <i class="fas fa-play me-1"></i> Start Work
                                        </button>
                                    </form>
                                @elseif($assignment->status === 'in_progress')
                                    <form action="{{ route('job-assignments.update-status', $assignment->id) }}" method="POST" class="d-inline flex-grow-1">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-sm btn-success w-100">
                                            <i class="fas fa-check me-1"></i> Mark Completed
                                        </button>
                                    </form>
                                @else
                                    <span class="btn btn-sm btn-light text-muted w-100 disabled">
                                        <i class="fas fa-check-circle text-success me-1"></i> Done
                                    </span>
                                @endif

                                <form action="{{ route('job-assignments.destroy', $assignment->id) }}" method="POST" class="d-inline delete-assignment-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light text-danger" title="Remove Assignment">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- PAYMENTS & INVOICE CARD -->
<x-card title="Payments & Balance" class="mb-4">
    <div class="row text-center mb-4 g-3">
        <div class="col-md-4">
            <div class="p-3 border rounded shadow-sm bg-light h-100 d-flex flex-column justify-content-center">
                <span class="text-muted fw-bold d-block mb-1">Total Amount</span>
                <span class="fw-bold text-dark fs-3">{{ format_currency($jobOrder->grand_total ?? $jobOrder->total_amount) }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 border rounded shadow-sm bg-success-subtle h-100 d-flex flex-column justify-content-center">
                <span class="text-muted fw-bold d-block mb-1">Paid Amount</span>
                <span class="fw-bold text-success fs-3">{{ format_currency($jobOrder->paid_amount) }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-3 border rounded shadow-sm bg-danger-subtle h-100 d-flex flex-column justify-content-center">
                <span class="text-muted fw-bold d-block mb-1">Remaining Balance</span>
                <span class="fw-bold text-danger fs-3">{{ format_currency($jobOrder->remaining_balance) }}</span>
            </div>
        </div>
    </div>

    @if($jobOrder->payments->isEmpty())
        <div class="alert alert-light border text-muted small mb-4">
            <i class="fas fa-info-circle me-1"></i> No payments recorded yet. You can add advance payment below.
        </div>
    @else
        <div class="table-responsive">
            <x-table class="mb-4">
                <x-slot name="thead">
                    <th>#</th>
                    <th>Method</th>
                    <th class="text-end">Amount</th>
                    <th>Date</th>
                    <th>Reference</th>
                    <th class="text-end">Actions</th>
                </x-slot>
                    @foreach($jobOrder->payments as $idx => $pay)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td><span class="badge bg-light text-dark border">{{ ucfirst($pay->method) }}</span></td>
                        <td class="text-end fw-bold text-success">{{ format_currency($pay->amount) }}</td>
                        <td>{{ $pay->received_at ? \Carbon\Carbon::parse($pay->received_at)->format('d-M-Y') : '-' }}</td>
                        <td class="text-muted small">{{ $pay->reference ?? '-' }}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary p-1 px-2" data-bs-toggle="modal" data-bs-target="#editPaymentModal{{ $pay->id }}" title="Edit Payment">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('job-order-payments.destroy', $pay->id) }}" method="POST" class="d-inline-block delete-payment-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-outline-danger p-1 px-2 delete-payment-btn" title="Delete Payment">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Payment Modal -->
                    <div class="modal fade" id="editPaymentModal{{ $pay->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('job-order-payments.update', $pay->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Payment</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Payment Method</label>
                                            <select name="method" class="form-select" required>
                                                <option value="cash" {{ $pay->method == 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="bank" {{ $pay->method == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                                <option value="check" {{ $pay->method == 'check' ? 'selected' : '' }}>Check</option>
                                                <option value="other" {{ $pay->method == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Amount</label>
                                            <input type="number" name="amount" step="0.01" class="form-control" value="{{ $pay->amount }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Reference</label>
                                            <input type="text" name="reference" class="form-control" value="{{ $pay->reference }}">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Update Payment</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
            </x-table>
        </div>
    @endif

        @if($jobOrder->status !== 'cancelled')
        <!-- Add Payment Form -->
        <form action="{{ route('job-orders.payments', $jobOrder->id) }}" method="POST" class="row g-3 align-items-end mb-2">
            @csrf
            <div class="col-md-3">
                <label class="form-label fw-bold">Payment Method</label>
                <select name="method" class="form-select p-2 shadow-sm fs-6" required>
                    <option value="cash">Cash</option>
                    <option value="bank">Bank Transfer</option>
                    <option value="check">Check</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Amount</label>
                <input type="number" name="amount" step="0.01" class="form-control p-2 shadow-sm fs-6" required placeholder="0.00">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Reference</label>
                <input type="text" name="reference" class="form-control p-2 shadow-sm fs-6" placeholder="Ref # / Note">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success p-2 w-100 shadow-sm fw-bold fs-6">
                    <i class="fas fa-plus me-1"></i> Add Payment
                </button>
            </div>
        </form>

        <hr class="my-4">

        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted">Ready to bill the client? Convert this Job Order to a final Invoice.</span>
            <a href="{{ route('job-orders.invoice', $jobOrder->id) }}" class="btn btn-primary-modern px-4 py-2 fw-bold d-flex align-items-center shadow-sm convert-to-invoice-btn">
                <i class="fas fa-exchange-alt me-2"></i> Convert to Invoice
            </a>
        </div>
        @else
        <div class="alert alert-light text-muted small border mb-0 text-center py-3">
            <i class="fas fa-ban text-danger me-1"></i> This Job Order is cancelled. Payments and invoice conversion are disabled.
        </div>
        @endif
</x-card>

<!-- Modal: Add Department Assignment -->
<div class="modal fade" id="addAssignmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('job-orders.assignments.store', $jobOrder->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Assign Department / Worker</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Select Department / Machine Unit</label>
                        <select name="department_id" class="form-select" id="departmentSelect" onchange="toggleCustomDept(this)">
                            <option value="">-- Select Existing Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                            <option value="custom">+ Add Custom Name (Workshop, Flex, Sticker, Sign Board...)</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="customDeptGroup">
                        <label class="form-label fw-bold small">Custom Department / Machine Name <span class="text-danger">*</span></label>
                        <input type="text" name="department_name" class="form-control" placeholder="e.g. Banner Machine #2, Acrylic Cutter, Outdoor Mounting">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Assign Worker / Employee</label>
                        <select name="employee_id" class="form-select">
                            <option value="">-- Select Registered Employee (Optional) --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->designation ?? 'Staff' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Custom Worker / Team Name (If non-employee)</label>
                        <input type="text" name="assigned_to_name" class="form-control" placeholder="e.g. Master Farhan, Printing Team B">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">Task Instructions / Notes</label>
                        <textarea name="instructions" class="form-control" rows="3" placeholder="e.g. Print 500 copies on 300gsm vinyl sheet, frame size 4x6 ft"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Work</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Cancel Job Order -->
<div class="modal fade" id="cancelJobOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('job-orders.cancel', $jobOrder->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i> Cancel Job Order
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <p class="mb-2">Are you sure you want to cancel Job Order <strong>#{{ $jobOrder->order_number }}</strong>?</p>
                    <div class="alert alert-warning small mb-0">
                        <i class="fas fa-info-circle me-1"></i> Cancelling will set all amounts, item prices, discounts, taxes, and balance to <strong>0.00</strong> and mark the order as <strong>Cancelled</strong>.
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">No, Keep Order</button>
                    <button type="submit" class="btn btn-danger">Yes, Cancel Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Employee Modal -->
<div class="modal fade" id="assignJobOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-tag text-primary me-2"></i> Assign Job Order to Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('job-orders.assign_employee', $jobOrder->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Assign this job order to an employee who will manage execution and client updates.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $jobOrder->employee_id == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }} ({{ $emp->designation ?? 'Employee' }})
                                </option>
                            @endforeach
                        </select>
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
    function toggleCustomDept(select) {
        const customGroup = document.getElementById('customDeptGroup');
        if (select.value === 'custom') {
            customGroup.classList.remove('d-none');
        } else {
            customGroup.classList.add('d-none');
        }
    }

    document.querySelectorAll('.convert-to-invoice-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            Swal.fire({
                title: 'Convert to Invoice?',
                text: 'Are you sure you want to create or sync a final invoice from this job order?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#dc3545',
                confirmButtonText: '<i class="fas fa-exchange-alt me-1"></i> Yes, convert it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

    document.querySelectorAll('.delete-payment-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            let form = this.closest('form');
            Swal.fire({
                title: 'Delete Payment?',
                text: "Are you sure you want to delete this payment record?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    document.querySelectorAll('.delete-assignment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            Swal.fire({
                title: 'Remove assignment?',
                text: "This work assignment will be removed from this job order.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, remove'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection












