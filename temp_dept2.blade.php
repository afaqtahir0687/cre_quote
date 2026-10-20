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
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('job-orders.pdf', $jobOrder->id) }}" class="btn btn-outline-danger d-flex align-items-center shadow-sm">
            <i class="fas fa-file-pdf me-2"></i> Download PDF
        </a>
        <a href="{{ route('job-orders.edit', $jobOrder->id) }}" class="btn btn-outline-info d-flex align-items-center shadow-sm">
            <i class="fas fa-edit me-2"></i> Edit
        </a>
    </div>
</div>

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
                <p class="text-muted mb-0">
                <strong>Date:</strong> {{ $jobOrder->order_date ? $jobOrder->order_date->format('Y-m-d') : now()->format('Y-m-d') }}<br>
                <strong>Customer:</strong> {{ $jobOrder->customer->name ?? '' }}<br>
                {{ $jobOrder->customer->address ?? '' }}<br>
                <i class="fas fa-phone me-1 small"></i> {{ $jobOrder->customer->phone ?? '' }}<br>
                <strong>Created By:</strong> {{ $jobOrder->employee->name ?? 'N/A' }}</p>
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

        <div class="d-flex justify-content-end mt-3">
            <div class="p-3 bg-light rounded text-end" style="min-width: 250px;">
                <span class="text-muted d-block small">Grand Total</span>
                <span class="fs-4 fw-bold text-primary">{{ format_currency($jobOrder->grand_total ?? $jobOrder->items->sum('total')) }}</span>
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
        <button class="btn btn-primary-modern btn-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
            <i class="fas fa-plus me-2"></i> Assign Department / Worker
        </button>
    </div>
    <div class="card-body">
        @if($jobOrder->assignments->isEmpty())
            <div class="text-center py-4 border rounded bg-light-subtle">
                <i class="fas fa-tools text-muted fs-2 mb-2"></i>
                <p class="text-muted mb-2">No departments assigned to this job order yet.</p>
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
                    <i class="fas fa-plus me-1"></i> Assign Workshop / Banner Machine / Flex / Sticker / Sign Board
                </button>
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
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-bold text-dark mb-3"><i class="fas fa-wallet me-2 text-success"></i> Payments & Balance</h5>
        @if($jobOrder->payments->isEmpty())
            <p class="text-muted small">No payments recorded yet.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-3">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Method</th>
                            <th class="text-end">Amount</th>
                            <th>Date</th>
                            <th>Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jobOrder->payments as $idx => $pay)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td><span class="badge bg-light text-dark border">{{ ucfirst($pay->method) }}</span></td>
                            <td class="text-end fw-bold text-success">{{ format_currency($pay->amount) }}</td>
                            <td>{{ $pay->received_at ? \Carbon\Carbon::parse($pay->received_at)->format('Y-m-d') : '-' }}</td>
                            <td class="text-muted small">{{ $pay->reference ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row text-center mb-3 g-2">
                <div class="col-md-6">
                    <div class="p-2 border rounded bg-success-subtle">
                        <span class="text-muted small d-block">Paid Amount</span>
                        <span class="fw-bold text-success fs-5">{{ format_currency($jobOrder->paid_amount) }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-2 border rounded bg-danger-subtle">
                        <span class="text-muted small d-block">Remaining Balance</span>
                        <span class="fw-bold text-danger fs-5">{{ format_currency($jobOrder->remaining_balance) }}</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Add Payment Form -->
        <form action="{{ route('job-orders.payments', $jobOrder->id) }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-3">
                <label class="form-label small fw-bold">Payment Method</label>
                <select name="method" class="form-select form-select-sm" required>
                    <option value="cash">Cash</option>
                    <option value="bank">Bank Transfer</option>
                    <option value="check">Check</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Amount</label>
                <input type="number" name="amount" step="0.01" class="form-control form-control-sm" required placeholder="0.00">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Reference</label>
                <input type="text" name="reference" class="form-control form-control-sm" placeholder="Ref # / Note">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-success btn-sm w-100 fw-semibold">
                    <i class="fas fa-plus me-1"></i> Add Payment
                </button>
            </div>
        </form>

        <hr class="my-4">

        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small">Ready to bill the client? Convert this Job Order to a final Invoice.</span>
            <a href="{{ route('job-orders.invoice', $jobOrder->id) }}" class="btn btn-primary-modern d-flex align-items-center shadow-sm" onclick="return confirm('Create a final Invoice from this Job Order?')">
                <i class="fas fa-exchange-alt me-2"></i> Convert to Invoice
            </a>
        </div>
    </div>
</div>

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

    document.querySelectorAll('.delete-assignment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
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
