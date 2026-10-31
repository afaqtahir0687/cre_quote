@extends('admin.layouts.app')

@section('title', 'Quote ' . $quote->quote_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('quotes.index') }}" class="btn btn-light btn-sm rounded-circle me-3 shadow-sm">
            <i class="fas fa-arrow-left text-muted"></i>
        </a>
        <h2 class="fw-bold text-dark mb-0">Quote Detail</h2>
        <span class="badge-soft badge-soft-primary ms-3">{{ $quote->quote_number }}</span>
    </div>
    <div class="d-flex gap-2">
        <div class="btn-group">
            <a href="{{ route('quotes.pdf', $quote) }}" class="btn btn-outline-secondary border-2 d-flex align-items-center fw-medium">
                <i class="fas fa-file-pdf me-2"></i> Download PDF
            </a>
            <button type="button" class="btn btn-outline-secondary border-2 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="PDF Options">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li>
                    <a class="dropdown-item py-2" href="{{ route('quotes.pdf', ['quote' => $quote->id, 'show_customer_name' => 1]) }}">
                        <i class="fas fa-user-check text-success me-2"></i> With Customer Name
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('quotes.pdf', ['quote' => $quote->id, 'show_customer_name' => 0]) }}">
                        <i class="fas fa-user-slash text-danger me-2"></i> Without Customer Name
                    </a>
                </li>
            </ul>
        </div>
        <button class="btn btn-primary-modern d-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#convertJobOrderModal">
            <i class="fas fa-check-circle me-2"></i> Approve & Convert to Job Order
        </button>
    </div>
</div>

<x-card :padding="false" class="overflow-hidden">
    <div class="p-5">
        <div class="row mb-5">
            <div class="col-sm-6">
                <label class="text-uppercase text-muted fw-bold small mb-3 d-block">From</label>
                <h5 class="fw-bold text-dark mb-1">{{ $setting->company_name ?? 'My Company' }}</h5>
                <p class="text-muted mb-0">
                    {{ $setting->company_address ?? 'Address not set' }}<br>
                    <i class="fas fa-envelope me-1 small"></i> {{ $setting->company_email ?? 'email@example.com' }}<br>
                    <i class="fas fa-phone me-1 small"></i> {{ $setting->company_phone ?? '+00 000 0000' }}
                </p>
            </div>
            <div class="col-sm-6 text-sm-end">
                <label class="text-uppercase text-muted fw-bold small mb-3 d-block">Prepared For</label>
                <h5 class="fw-bold text-dark mb-1">{{ $quote->customer->name }}</h5>
                <p class="text-muted mb-0">
                    {{ $quote->customer->address }}<br>
                    {{ $quote->customer->city }}, {{ $quote->customer->zip }}, {{ $quote->customer->country }}<br>
                    <i class="fas fa-envelope me-1 small"></i> {{ $quote->customer->email }}
                </p>
            </div>
        </div>

        <div class="row mb-5 py-3 border-top border-bottom bg-light-subtle">
            <div class="col-3">
                <span class="text-muted small d-block">Quote Date</span>
                <span class="fw-bold text-dark">{{ $quote->quote_date }}</span>
            </div>
            <div class="col-3">
                <span class="text-muted small d-block">Due Date</span>
                <span class="fw-bold text-dark">{{ $quote->due_date ?? 'N/A' }}</span>
            </div>
            <div class="col-3">
                <span class="text-muted small d-block">Status</span>
                @php
                    $statusClass = match($quote->status) {
                        'Accepted' => 'badge-soft-success',
                        'Sent' => 'badge-soft-info',
                        'Invoiced' => 'badge-soft-primary',
                        'Declined' => 'badge-soft-danger',
                        default => 'badge-soft-warning'
                    };
                @endphp
                <span class="badge-soft {{ $statusClass }}">{{ $quote->status }}</span>
            </div>
            <div class="col-3">
                <span class="text-muted small d-block">Assigned To</span>
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold text-dark"><i class="fas fa-user-circle text-primary me-1"></i>{{ $quote->employee?->name ?? 'Admin' }}</span>
                    <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2 rounded-pill small" data-bs-toggle="modal" data-bs-target="#assignQuoteModal" title="Reassign Employee">
                        <i class="fas fa-user-edit small"></i> Assign
                    </button>
                </div>
            </div>
            <div class="col-3 text-end">
                <span class="text-muted small d-block">Reference</span>
                <span class="fw-bold text-primary">{{ $quote->reference ?? 'N/A' }}</span>
            </div>
            <div class="col-3 text-end">
                <span class="text-muted small d-block">Created By</span>
                <span class="fw-bold text-primary">{{ $quote->employee->name ?? 'Admin' }}</span>
            </div>
        </div>

        <x-table class="mb-5">
            <x-slot name="thead">
                <th style="width: 5%" class="ps-0">#</th>
                <th style="width: 35%">Description</th>
                <th style="width: 12%">Size and meter</th>
                <th style="width: 10%">Image</th>
                <th style="width: 8%" class="text-end">Unit Price</th>
                <th style="width: 8%" class="text-center">Qty</th>
                <th style="width: 10%" class="text-center">Tax %</th>
                <th style="width: 12%" class="text-end pe-0">Total</th>
            </x-slot>

            @foreach($quote->items as $index => $item)
            <tr class="align-middle">
                <td class="ps-0 text-muted">{{ $index + 1 }}</td>
                <td class="fw-medium text-dark">{{ $item->description }}</td>
                <td class="text-muted">{{ $item->item_text ?? '-' }}</td>
                <td>
                    @if(!empty($item->images))
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($item->images as $img)
                                <img src="{{ asset($img) }}" class="rounded" style="width: 150px; height: 150px; object-fit: cover; cursor: pointer; margin-bottom: 5px;" onclick="showImageModal('{{ asset($img) }}')">
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="text-end text-muted">{{ format_currency($item->unit_price) }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-center">{{ $item->tax_percentage }}%</td>
                <td class="text-end pe-0 fw-bold text-dark">{{ format_currency($item->total) }}</td>
            </tr>
            @endforeach
        </x-table>

        <div class="row">
            <div class="col-md-12">
                <div class="d-flex flex-column gap-3 p-4 bg-light rounded-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-medium text-dark">{{ format_currency($quote->subtotal) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Total Tax</span>
                        <span class="fw-medium text-dark">{{ format_currency($quote->total_tax) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Total Discount</span>
                        <span class="fw-medium text-danger">-{{ format_currency($quote->total_discount) }}</span>
                    </div>
                    @if($quote->shipping > 0)
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Shipping</span>
                        <span class="fw-medium text-dark">{{ format_currency($quote->shipping) }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="fw-bold text-dark fs-5">Grand Total</span>
                        <span class="fw-bold text-primary fs-5">{{ format_currency($quote->grand_total) }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($quote->notes)
        <div class="mt-5 pt-4 border-top">
            <h6 class="fw-bold text-dark mb-2">Notes:</h6>
            <p class="text-muted mb-0 small">{{ $quote->notes }}</p>
        </div>
        @endif

        @if($quote->images && count($quote->images) > 0)
        <div class="mt-5 pt-4 border-top">
            <h6 class="fw-bold text-dark mb-3">Attached Images:</h6>
            <div class="row g-3">
                @foreach($quote->images as $index => $image)
                <div class="col-md-3">
                    <div class="position-relative overflow-hidden rounded-3 shadow-sm" style="cursor: pointer;" onclick="showImageModal('{{ asset($image) }}')">
                        <img src="{{ asset($image) }}" class="img-fluid w-100" style="height: 200px; object-fit: cover; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-50 text-white p-2 text-center">
                            <i class="fas fa-search-plus"></i> View
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-card>

<!-- Modal: Approve Quote & Assign Departments -->
<div class="modal fade" id="convertJobOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('job-orders.from-quote', $quote->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-check-circle me-2 text-success"></i> Approve Quotation & Assign Work
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        Approving this quotation will mark it as <strong>Accepted</strong> and create a new <strong>Job Order</strong>. Select which departments/machines should begin work on this job:
                    </p>

                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-sitemap text-primary me-2"></i> Department Assignments</h6>

                    <div id="departmentAssignmentList">
                        @foreach($departments as $idx => $dept)
                        <div class="card border mb-2 shadow-none">
                            <div class="card-body p-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input dept-checkbox" type="checkbox" 
                                           name="departments[{{ $idx }}][department_id]" 
                                           value="{{ $dept->id }}" 
                                           id="dept_chk_{{ $dept->id }}"
                                           onchange="toggleDeptFields(this, 'dept_fields_{{ $dept->id }}')">
                                    <label class="form-check-label fw-bold text-dark cursor-pointer" for="dept_chk_{{ $dept->id }}">
                                        {{ $dept->name }}
                                    </label>
                                    <span class="text-muted small ms-2">({{ $dept->description ?? 'Department' }})</span>
                                </div>

                                <div class="row g-2 d-none mt-1 pt-2 border-top" id="dept_fields_{{ $dept->id }}">
                                    <div class="col-md-4">
                                        <label class="form-label extra-small fw-bold text-muted">Assign Worker / Employee</label>
                                        <select name="departments[{{ $idx }}][employee_id]" class="form-select form-select-sm">
                                            <option value="">-- Any Available Worker --</option>
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label extra-small fw-bold text-muted">Custom Worker Name (Optional)</label>
                                        <input type="text" name="departments[{{ $idx }}][assigned_to_name]" class="form-control form-control-sm" placeholder="e.g. Workshop Team A">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label extra-small fw-bold text-muted">Target End Date</label>
                                        <input type="date" name="departments[{{ $idx }}][due_date]" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+2 days')) }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label extra-small fw-bold text-muted">Work Instructions</label>
                                        <input type="text" name="departments[{{ $idx }}][instructions]" class="form-control form-control-sm" placeholder="e.g. Dimensions, material specs, or special notes...">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Custom Department Option -->
                    <div class="p-3 bg-light rounded mt-3 border">
                        <label class="form-label fw-bold small text-dark mb-1">Need a Custom Unit / Department?</label>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="departments[999][custom_name]" class="form-control form-control-sm" placeholder="Custom Name (e.g. Outdoor Mounting)">
                            </div>
                            <div class="col-md-4">
                                <input type="date" name="departments[999][due_date]" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('+2 days')) }}">
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="departments[999][instructions]" class="form-control form-control-sm" placeholder="Instructions for custom unit">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold">
                        <i class="fas fa-check me-2"></i> Confirm Approval & Create Job Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 10;"></button>
                <img id="modalImage" src="" class="img-fluid w-100 rounded-3">
            </div>
        </div>
    </div>
</div>

<!-- Assign Employee Modal -->
<div class="modal fade" id="assignQuoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-tag text-primary me-2"></i> Assign Quote to Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('quotes.assign_employee', $quote) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Assign this quote to an employee so they can manage and follow up with the client.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $quote->employee_id == $emp->id ? 'selected' : '' }}>
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

@section('scripts')
<script>
    function showImageModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        modal.show();
    }

    function toggleDeptFields(checkbox, fieldsId) {
        const fields = document.getElementById(fieldsId);
        if (checkbox.checked) {
            fields.classList.remove('d-none');
        } else {
            fields.classList.add('d-none');
        }
    }
</script>
@endsection
@endsection
