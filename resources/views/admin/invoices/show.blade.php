@extends('admin.layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('invoices.index') }}" class="btn btn-light btn-sm rounded-circle me-3 shadow-sm">
            <i class="fas fa-arrow-left text-muted"></i>
        </a>
        <h2 class="fw-bold text-dark mb-0">Invoice Detail</h2>
        <span class="badge-soft badge-soft-primary ms-3">{{ $invoice->invoice_number }}</span>
    </div>
    <div class="d-flex gap-2">
        @if(strtolower($invoice->status) !== 'cancelled')
            <button type="button" class="btn btn-primary-modern d-flex align-items-center shadow-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="fas fa-plus me-2"></i> Record Payment
            </button>
        @endif
        <div class="btn-group">
            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-outline-secondary border-2 d-flex align-items-center fw-medium">
                <i class="fas fa-file-pdf me-2"></i> Download PDF
            </a>
            <button type="button" class="btn btn-outline-secondary border-2 dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" title="PDF Options">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li>
                    <a class="dropdown-item py-2" href="{{ route('invoices.pdf', ['invoice' => $invoice->id, 'show_customer_name' => 1]) }}">
                        <i class="fas fa-user-check text-success me-2"></i> With Customer Name
                    </a>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="{{ route('invoices.pdf', ['invoice' => $invoice->id, 'show_customer_name' => 0]) }}">
                        <i class="fas fa-user-slash text-danger me-2"></i> Without Customer Name
                    </a>
                </li>
            </ul>
        </div>
        @if(strtolower($invoice->status) !== 'cancelled' && $invoice->status != 'Paid')
            <form action="{{ route('invoices.convert_to_delivery_note', $invoice) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-success d-flex align-items-center fw-medium" onclick="return confirm('Convert this Invoice to Delivery Note?')">
                    <i class="fas fa-exchange-alt me-2"></i> Convert to Delivery Note
                </button>
            </form>
            <a href="{{ route('delivery_notes.create_from_invoice', $invoice) }}" class="btn btn-outline-success d-flex align-items-center fw-medium">
                <i class="fas fa-truck me-2"></i> Create Delivery Note
            </a>
        @endif
        @if(strtolower($invoice->status) !== 'cancelled')
            <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-outline-info border-2 d-flex align-items-center fw-medium">
                <i class="fas fa-edit me-2"></i> Edit
            </a>
        @endif
        <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="delete-form m-0">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger border-2 d-flex align-items-center fw-medium">
                <i class="fas fa-trash me-2"></i> Delete
            </button>
        </form>
    </div>
</div>

@if(strtolower($invoice->status) === 'cancelled')
<div class="alert alert-danger d-flex align-items-center mb-4 py-2 px-3 shadow-sm border border-danger-subtle rounded-3">
    <i class="fas fa-ban fs-4 me-3 text-danger"></i>
    <div>
        <strong>This Invoice has been Cancelled!</strong>
        <div class="small">The related Job Order was cancelled, and all amounts, payments, and balances on this invoice have been set to 0.00.</div>
    </div>
</div>
@endif

<div class="row g-4">
    <div class="col-lg-9">
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
                        @php
                            $bComp = $invoice->billing_company;
                        @endphp
                        <label class="text-uppercase text-muted fw-bold small mb-3 d-block">Bill To</label>
                        <h5 class="fw-bold text-dark mb-1">{{ $invoice->customer?->name ?? 'N/A' }}</h5>
                        @if($bComp?->company_name || $invoice->customer?->company_name)
                            <div class="fw-semibold text-primary mb-1"><i class="fas fa-building me-1"></i>{{ $bComp?->company_name ?? $invoice->customer?->company_name }}</div>
                        @endif
                        <p class="text-muted mb-0">
                            {{ $bComp?->address ?? ($invoice->customer?->address ?? '') }}<br>
                            {{ collect([$bComp?->city ?? $invoice->customer?->city, $bComp?->zip ?? $invoice->customer?->zip, $bComp?->country ?? $invoice->customer?->country])->filter()->join(', ') }}<br>
                            @if($bComp?->vat_no || $invoice->customer?->vat_no)
                                <span class="d-inline-block me-2"><strong>VAT:</strong> {{ $bComp?->vat_no ?? $invoice->customer?->vat_no }}</span>
                            @endif
                            @if($bComp?->cr_no || $invoice->customer?->cr_no)
                                <span class="d-inline-block"><strong>CR:</strong> {{ $bComp?->cr_no ?? $invoice->customer?->cr_no }}</span><br>
                            @endif
                            <i class="fas fa-phone me-1 small"></i> {{ $invoice->customer?->formatted_phone ?: ($invoice->customer?->phone ?? 'N/A') }}<br>
                            @if($invoice->customer?->email)
                                <i class="fas fa-envelope me-1 small"></i> {{ $invoice->customer->email }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row mb-5 py-3 border-top border-bottom bg-light-subtle">
                    <div class="col-3">
                        <span class="text-muted small d-block">Invoice Date</span>
                        <span class="fw-bold text-dark">{{ $invoice->invoice_date }}</span>
                    </div>
                    <div class="col-3">
                        <span class="text-muted small d-block">Due Date</span>
                        <span class="fw-bold text-dark">{{ $invoice->due_date }}</span>
                    </div>
                    <div class="col-3">
                        <span class="text-muted small d-block">Status</span>
                        @php
                            $statusClass = match(ucfirst(strtolower($invoice->status))) {
                                'Paid' => 'badge-soft-success',
                                'Partially Paid' => 'badge-soft-info',
                                'Unpaid' => 'badge-soft-danger',
                                'Cancelled' => 'badge-soft-danger',
                                default => 'badge-soft-warning'
                            };
                        @endphp
                        <span class="badge-soft {{ $statusClass }}">{{ $invoice->status }}</span>
                    </div>
                    <div class="col-3 text-end">
                        <span class="text-muted small d-block">Total Due</span>
                        <span class="fw-bold text-primary fs-5">{{ format_currency($invoice->balance_due) }}</span>
                    </div>
                    <div class="col-3 text-end">
                        <span class="text-muted small d-block">Assigned To</span>
                        <div class="d-inline-flex align-items-center gap-2">
                            <span class="fw-bold text-dark">{{ $invoice->employee?->name ?? 'Admin / Unassigned' }}</span>
                            <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2 rounded-pill small" data-bs-toggle="modal" data-bs-target="#assignInvoiceModal" title="Reassign Employee">
                                <i class="fas fa-user-edit small"></i> Assign
                            </button>
                        </div>
                    </div>
                </div>

                <x-table class="mb-5">
                    <x-slot name="thead">
                        <th class="ps-0">Description</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-center">Tax %</th>
                        <th class="text-center">Tax Amount</th>
                        <th class="text-end pe-0">Total</th>
                    </x-slot>

                    @foreach($invoice->items as $item)
                    @php
                        $itemSubtotal = $item->unit_price * $item->quantity;
                        if ($item->tax_percentage && $item->tax_percentage > 0) {
                            $itemTax = $itemSubtotal * ($item->tax_percentage / 100);
                            $displayTaxPercentage = $item->tax_percentage;
                        } else {
                            if ($invoice->subtotal > 0 && $invoice->total_tax > 0) {
                                $itemTax = ($itemSubtotal / $invoice->subtotal) * $invoice->total_tax;
                                $displayTaxPercentage = ($invoice->total_tax / $invoice->subtotal) * 100;
                            } else {
                                $itemTax = 0;
                                $displayTaxPercentage = 0;
                            }
                        }
                    @endphp
                    <tr class="align-middle">
                        <td class="ps-0">
                            <div class="fw-medium text-dark">{{ $item->description }}</div>
                            @if($item->item_text)
                                <div class="text-muted small">{{ $item->item_text }}</div>
                            @endif
                        </td>
                        <td class="text-end text-muted">{{ format_currency($item->unit_price) }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center text-muted">{{ number_format($displayTaxPercentage, 2) }}%</td>
                        <td class="text-center text-muted">{{ format_currency($itemTax) }}</td>
                        <td class="text-end pe-0 fw-bold text-dark">{{ format_currency($item->total) }}</td>
                    </tr>
                    @endforeach
                </x-table>

                <div class="row justify-content-end">
                    <div class="col-md-5">
                        <div class="d-flex flex-column gap-3 p-4 bg-light rounded-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Subtotal</span>
                                <span class="fw-medium text-dark">{{ format_currency($invoice->subtotal) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Tax</span>
                                <span class="fw-medium text-dark">{{ format_currency($invoice->total_tax) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <span class="fw-bold text-dark">Grand Total</span>
                                <span class="fw-bold text-dark fs-5">{{ format_currency($invoice->grand_total) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-success fw-bold">
                                <span>Amount Paid</span>
                                <span>{{ format_currency($invoice->amount_paid) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-danger fw-bold border-top pt-2">
                                <span>Balance Due</span>
                                <span class="fs-5">{{ format_currency($invoice->balance_due) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($invoice->notes)
                <div class="mt-5 pt-4 border-top">
                    <h6 class="fw-bold text-dark mb-2">Notes:</h6>
                    <p class="text-muted mb-0 small">{{ $invoice->notes }}</p>
                </div>
                @endif
            </div>
        </x-card>
    </div>
    
    <div class="col-lg-3">
        <x-card title="Payment History">
            <div class="timeline">
                @forelse($invoice->payments as $payment)
                <div class="d-flex mb-4 position-relative">
                    <div class="me-3">
                        <div class="rounded-circle bg-primary-subtle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="fas fa-check text-primary small"></i>
                        </div>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark">{{ format_currency($payment->amount) }}</h6>
                        <small class="text-muted d-block">{{ $payment->payment_date }}</small>
                        <span class="badge-soft badge-soft-info mt-1" style="font-size: 0.65rem;">{{ $payment->payment_method }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="fas fa-receipt fa-3x text-light mb-3"></i>
                    <p class="text-muted small">No payments recorded yet.</p>
                </div>
                @endforelse
            </div>
        </x-card>
    </div>
</div>

<!-- Record Payment Modal (Styled) -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('payments.store', $invoice) }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">Record Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4 text-center p-3 bg-light rounded-3">
                        <span class="text-muted d-block small mb-1">Current Balance Due</span>
                        <h3 class="fw-bold text-danger mb-0">{{ format_currency($invoice->balance_due) }}</h3>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        <label for="payment_date">Payment Date</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="number" step="0.01" name="amount" id="payment_amount" class="form-control" value="{{ $invoice->balance_due }}" max="{{ $invoice->balance_due }}" required>
                        <label for="payment_amount">Payment Amount</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="Cash">Cash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Check">Check</option>
                            <option value="Credit Card">Credit Card</option>
                        </select>
                        <label for="payment_method">Payment Method</label>
                    </div>

                    <div class="form-floating">
                        <textarea name="note" id="payment_note" class="form-control" style="height: 100px" placeholder="Optional note"></textarea>
                        <label for="payment_note">Note (Optional)</label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-modern px-4">Confirm Payment</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Assign Employee Modal -->
<div class="modal fade" id="assignInvoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-tag text-primary me-2"></i> Assign Invoice to Employee</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('invoices.assign_employee', $invoice) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Assign this invoice to an employee who will manage billing and collection follow-up.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Employee <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ $invoice->employee_id == $emp->id ? 'selected' : '' }}>
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

