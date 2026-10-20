@extends('admin.layouts.app')

@section('title', 'Delivery Note Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">Delivery Note #{{ $deliveryNote->delivery_number }}</h2>
    <div>
        <a href="{{ route('delivery_notes.pdf', $deliveryNote) }}" class="btn btn-danger">
            <i class="fas fa-file-pdf me-2"></i> Download PDF
        </a>
        <a href="{{ route('delivery_notes.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <x-card>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Customer Information</h6>
                        <p class="mb-1"><strong>Name:</strong> {{ $deliveryNote->customer->name }}</p>
                        <p class="mb-1"><strong>Company:</strong> {{ $deliveryNote->customer->company_name ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Phone:</strong> {{ $deliveryNote->customer->phone ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $deliveryNote->customer->email ?? 'N/A' }}</p>
                        <p class="mb-0"><strong>Address:</strong> {{ $deliveryNote->customer->address ?? 'N/A' }}</p>
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
                        <p class="mb-1"><strong>Invoice #:</strong> {{ $deliveryNote->invoice->invoice_number }}</p>
                        <p class="mb-0"><strong>Employee:</strong> {{ $deliveryNote->employee?->name ?? 'N/A' }}</p>
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
@endsection
