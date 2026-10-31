@extends('admin.layouts.app')

@section('title', 'Create Delivery Note')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5>Create Delivery Note for Invoice #{{ $invoice->invoice_number }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('delivery_notes.store') }}" method="POST">
            @csrf
            <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
            <input type="hidden" name="customer_company_id" value="{{ $invoice->customer_company_id }}">

            @php
                $bComp = $invoice->billing_company;
            @endphp
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="fw-bold">Customer Information</h6>
                    <p class="mb-1"><strong>Name:</strong> {{ $invoice->customer?->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Company:</strong> {{ $bComp?->company_name ?? ($invoice->customer?->company_name ?? 'N/A') }}</p>
                    <p class="mb-1"><strong>Phone:</strong> {{ $invoice->customer?->formatted_phone ?: ($invoice->customer?->phone ?? 'N/A') }}</p>
                    <p class="mb-0"><strong>Email:</strong> {{ $invoice->customer?->email ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Invoice Summary</h6>
                    <p class="mb-1"><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</p>
                    <p class="mb-1"><strong>Date:</strong> {{ $invoice->invoice_date }}</p>
                    <p class="mb-1"><strong>Grand Total:</strong> {{ format_currency($invoice->grand_total) }}</p>
                    <p class="mb-0"><strong>Status:</strong> {{ $invoice->status }}</p>
                </div>
            </div>

            <hr class="my-4">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Delivery Date *</label>
                    <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-6 mb-3 d-flex align-items-center">
                    <div class="form-check form-switch pt-4">
                        <input class="form-check-input" type="checkbox" role="switch" name="show_customer_name" id="show_customer_name" value="1" {{ old('show_customer_name', $invoice->show_customer_name ?? ($setting->show_customer_name_on_pdf ?? true)) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark small cursor-pointer" for="show_customer_name">
                            <i class="fas fa-user-check text-primary me-1"></i> Show Customer Name on PDF
                        </label>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Delivery Address</label>
                    <textarea name="delivery_address" class="form-control" rows="3">{{ old('delivery_address', $bComp?->address ?: ($invoice->customer?->address ?? '')) }}</textarea>
                    <small class="text-muted">Leave empty to use customer's default address</small>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('invoices.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Delivery Note</button>
            </div>
        </form>
    </div>
</div>
@endsection
