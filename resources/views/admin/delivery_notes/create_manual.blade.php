@extends('admin.layouts.app')

@section('title', 'Create Delivery Note')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5>Create Delivery Note</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('delivery_notes.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Customer *</label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }}{{ $customer->company_name ? ' — ' . $customer->company_name : '' }}{{ $customer->phone ? ' (' . $customer->phone . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Invoice (Optional)</label>
                    <select name="invoice_id" class="form-select">
                        <option value="">Select Invoice (Optional)</option>
                        @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}" {{ old('invoice_id') == $invoice->id ? 'selected' : '' }}>
                                {{ $invoice->invoice_number }} - {{ $invoice->customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Employee (Optional)</label>
                    <select name="employee_id" class="form-select">
                        <option value="">Select Employee (Optional)</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Delivery Date *</label>
                    <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date', date('Y-m-d')) }}" required>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Delivery Address</label>
                    <textarea name="delivery_address" class="form-control" rows="3">{{ old('delivery_address') }}</textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('delivery_notes.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Delivery Note</button>
            </div>
        </form>
    </div>
</div>
@endsection
