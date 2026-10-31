@extends('admin.layouts.app')

@section('title', 'Edit Delivery Note')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5>Edit Delivery Note #{{ $deliveryNote->delivery_number }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('delivery_notes.update', $deliveryNote) }}" method="POST">
            @method('PUT')
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Customer *</label>
                    <input type="hidden" name="customer_company_id" id="customer_company_id" value="{{ old('customer_company_id', $deliveryNote->customer_company_id) }}">
                    <select name="customer_id" id="delivery_customer_select" class="form-select" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            @php
                                $displayPhone = $customer->formatted_phone ?: $customer->phone;
                                $companies = $customer->companies->isNotEmpty() ? $customer->companies : collect([[
                                    'id' => null,
                                    'company_name' => $customer->company_name ?? '',
                                    'address' => $customer->address ?? ''
                                ]]);
                            @endphp
                            @foreach($companies as $comp)
                                @php
                                    $cId = is_array($comp) ? ($comp['id'] ?? '') : $comp->id;
                                    $cName = is_array($comp) ? ($comp['company_name'] ?? '') : $comp->company_name;
                                    $cAddr = is_array($comp) ? ($comp['address'] ?? '') : $comp->address;

                                    $displayStr = $customer->name;
                                    if (!empty($cName)) $displayStr .= ' — ' . $cName;
                                    if (!empty($displayPhone)) $displayStr .= ' (' . $displayPhone . ')';
                                    $targetCompId = old('customer_company_id', $deliveryNote->customer_company_id);
                                    $isSel = (old('customer_id', $deliveryNote->customer_id) == $customer->id && ($targetCompId == $cId || empty($targetCompId)));
                                @endphp
                                <option value="{{ $customer->id }}"
                                        data-company-id="{{ $cId }}"
                                        data-address="{{ $cAddr }}"
                                        {{ $isSel ? 'selected' : '' }}>
                                    {{ $displayStr }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Invoice (Optional)</label>
                    <select name="invoice_id" class="form-select">
                        <option value="">Select Invoice (Optional)</option>
                        @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}" {{ old('invoice_id', $deliveryNote->invoice_id) == $invoice->id ? 'selected' : '' }}>
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
                            <option value="{{ $employee->id }}" {{ old('employee_id', $deliveryNote->employee_id) == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Delivery Date *</label>
                    <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date', $deliveryNote->delivery_date) }}" required>
                </div>
                <div class="col-md-6 mb-3 d-flex align-items-center">
                    <div class="form-check form-switch pt-4">
                        <input class="form-check-input" type="checkbox" role="switch" name="show_customer_name" id="show_customer_name" value="1" {{ old('show_customer_name', $deliveryNote->show_customer_name ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark small cursor-pointer" for="show_customer_name">
                            <i class="fas fa-user-check text-primary me-1"></i> Show Customer Name on PDF
                        </label>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Delivery Address</label>
                    <textarea name="delivery_address" class="form-control" rows="3">{{ old('delivery_address', $deliveryNote->delivery_address) }}</textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $deliveryNote->notes) }}</textarea>
                </div>
            </div>

            <div class="text-end mt-4">
                <a href="{{ route('delivery_notes.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Delivery Note</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('delivery_customer_select')?.addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    var compId = opt ? (opt.dataset.companyId || '') : '';
    var addr = opt ? (opt.dataset.address || '') : '';
    document.getElementById('customer_company_id').value = compId;
    var addrField = document.querySelector('textarea[name="delivery_address"]');
    if (addrField && !addrField.value.trim() && addr) {
        addrField.value = addr;
    }
});
</script>
@endsection
