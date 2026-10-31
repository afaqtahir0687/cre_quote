@extends('admin.layouts.app')

@section('title', 'Delivery Note Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">Delivery Note #{{ $deliveryNote->delivery_number }}</h2>
    <div>
        <div class="btn-group">
            <a href="{{ route('delivery_notes.pdf', $deliveryNote) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-2"></i> Download PDF
            </a>
            <button type="button" class="btn btn-danger dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="PDF Options">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li>
                    <a class="dropdown-item py-2" href="{{ route('delivery_notes.pdf', ['deliveryNote' => $deliveryNote->id, 'show_customer_name' => 1]) }}">
                        <i class="fas fa-user-check text-success me-2"></i> With Customer Name
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('delivery_notes.pdf', ['deliveryNote' => $deliveryNote->id, 'show_customer_name' => 0]) }}">
                        <i class="fas fa-user-slash text-danger me-2"></i> Without Customer Name
                    </a>
                </li>
            </ul>
        </div>
        <a href="{{ route('delivery_notes.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <x-card>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        @php
                            $bComp = $deliveryNote->billing_company;
                        @endphp
                        <h6 class="fw-bold mb-3">Customer Information</h6>
                        <p class="mb-1"><strong>Name:</strong> {{ $deliveryNote->customer?->name ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Company:</strong> {{ $bComp?->company_name ?? ($deliveryNote->customer?->company_name ?? 'N/A') }}</p>
                        <p class="mb-1"><strong>Phone:</strong> {{ $deliveryNote->customer?->formatted_phone ?: ($deliveryNote->customer?->phone ?? 'N/A') }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $deliveryNote->customer?->email ?? 'N/A' }}</p>
                        @if($bComp?->vat_no || $deliveryNote->customer?->vat_no)
                            <p class="mb-1"><strong>VAT:</strong> {{ $bComp?->vat_no ?? $deliveryNote->customer?->vat_no }}</p>
                        @endif
                        @if($bComp?->cr_no || $deliveryNote->customer?->cr_no)
                            <p class="mb-1"><strong>CR:</strong> {{ $bComp?->cr_no ?? $deliveryNote->customer?->cr_no }}</p>
                        @endif
                        <p class="mb-0"><strong>Address:</strong> {{ $bComp?->address ?? ($deliveryNote->customer?->address ?? 'N/A') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Delivery Information</h6>
                        <p class="mb-1"><strong>Delivery #:</strong> {{ $deliveryNote->delivery_number }}</p>
                        <p class="mb-1"><strong>Date:</strong> {{ $deliveryNote->delivery_date }}</p>
                        <p class="mb-1"><strong>Status:</strong> 
                            <span class="badge bg-{{ $deliveryNote->status == 'Delivered' ? 'success' : ($deliveryNote->status == 'Pending' ? 'warning' : 'danger') }}">
                                {{ $deliveryNote->status }}
                            </span>
                        </p>
                        <p class="mb-1"><strong>Invoice #:</strong> {{ $deliveryNote->invoice?->invoice_number ?? 'N/A' }}</p>
                        <p class="mb-0 d-flex align-items-center gap-2 flex-wrap">
                            <span><strong>Assigned Employee:</strong> <span class="fw-bold text-dark">{{ $deliveryNote->employee?->name ?? 'Admin / Unassigned' }}</span></span>
                            <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2 rounded-pill small" data-bs-toggle="modal" data-bs-target="#assignDeliveryNoteModal" title="Reassign Employee">
                                <i class="fas fa-user-edit small"></i> Assign
                            </button>
                        </p>
                    </div>
                </div>

                @if($deliveryNote->delivery_address)
                    <div class="mb-4">
                        <h6 class="fw-bold">Delivery Address</h6>
                        <p>{{ $deliveryNote->delivery_address }}</p>
                    </div>
                @endif

                @if($deliveryNote->notes)
                    <div class="mb-4">
                        <h6 class="fw-bold">Notes</h6>
                        <p>{{ $deliveryNote->notes }}</p>
                    </div>
                @endif
            </div>
        </x-card>

        <x-card class="mt-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Invoice Items</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Tax %</th>
                            <th class="text-center">Tax Amount</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveryNote->invoice->items as $item)
                        @php
                            $itemSubtotal = $item->unit_price * $item->quantity;
                            if ($item->tax_percentage && $item->tax_percentage > 0) {
                                $itemTax = $itemSubtotal * ($item->tax_percentage / 100);
                                $displayTaxPercentage = $item->tax_percentage;
                            } else {
                                if ($deliveryNote->invoice->subtotal > 0 && $deliveryNote->invoice->total_tax > 0) {
                                    $itemTax = ($itemSubtotal / $deliveryNote->invoice->subtotal) * $deliveryNote->invoice->total_tax;
                                    $displayTaxPercentage = ($deliveryNote->invoice->total_tax / $deliveryNote->invoice->subtotal) * 100;
                                } else {
                                    $itemTax = 0;
                                    $displayTaxPercentage = 0;
                                }
                            }
                        @endphp
                            <tr>
                                <td>{{ $item->description }}</td>
                                <td class="text-end">{{ format_currency($item->unit_price) }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-center">{{ number_format($displayTaxPercentage, 2) }}%</td>
                                <td class="text-center">{{ format_currency($itemTax) }}</td>
                                <td class="text-end">{{ format_currency($item->total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Subtotal:</th>
                            <th class="text-end">{{ format_currency($deliveryNote->invoice->subtotal) }}</th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-end">Tax:</th>
                            <th class="text-end">{{ format_currency($deliveryNote->invoice->total_tax) }}</th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-end">Discount:</th>
                            <th class="text-end">{{ format_currency($deliveryNote->invoice->total_discount) }}</th>
                        </tr>
                        <tr>
                            <th colspan="5" class="text-end">Shipping:</th>
                            <th class="text-end">{{ format_currency($deliveryNote->invoice->shipping) }}</th>
                        </tr>
                        <tr class="table-primary">
                            <th colspan="5" class="text-end">Grand Total:</th>
                            <th class="text-end fw-bold">{{ format_currency($deliveryNote->invoice->grand_total) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-card>
    </div>

    <div class="col-md-4">
        <x-card>
            <div class="card-body">
                <h6 class="fw-bold mb-3">Update Status</h6>
                <form action="{{ route('delivery_notes.update_status', $deliveryNote) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Pending" {{ $deliveryNote->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Delivered" {{ $deliveryNote->status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="Cancelled" {{ $deliveryNote->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                </form>
            </div>
        </x-card>
    </div>
</div>

<!-- Assign Employee Modal -->
<div class="modal fade" id="assignDeliveryNoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-tag text-primary me-2"></i> Assign Delivery Note to Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('delivery_notes.assign_employee', $deliveryNote) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Assign this delivery note to an employee who will handle the dispatch and customer handover.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $deliveryNote->employee_id == $emp->id ? 'selected' : '' }}>
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
