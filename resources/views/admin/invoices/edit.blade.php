@extends('admin.layouts.app')

@section('title', __('Edit Invoice'))

@section('styles')
<style>
    .cust-dropdown { position: relative; width: 100%; }
    .cust-select-hidden, select.cust-select-hidden, #customer_id.cust-select-hidden {
        display: none !important;
        position: absolute !important;
        width: 0 !important;
        height: 0 !important;
        opacity: 0 !important;
        pointer-events: none !important;
    }
    .cust-trigger {
        cursor: pointer;
        background-color: #FFFFFF !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 0.85rem center !important;
        background-size: 14px 10px !important;
        padding-right: 2.5rem !important;
        font-weight: 500;
    }
    .cust-trigger:focus, .cust-trigger.is-focused {
        border-color: var(--erp-primary, #4361EE) !important;
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15) !important;
    }
    .cust-panel {
        display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0;
        z-index: 1055; background: #FFFFFF; border: 1px solid #E9EDF3;
        border-radius: 10px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    .cust-panel.open { display: block !important; }
    .cust-search-wrap { padding: 8px 12px; border-bottom: 1px solid #F1F4F9; background: #F8FAFC; }
    .cust-search {
        width: 100%; border: 1px solid #DCE3EC; border-radius: 6px;
        padding: 7px 12px 7px 34px; font-size: 0.86rem; outline: none;
        background: #FFFFFF url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%236c757d' viewBox='0 0 16 16'%3e%3cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398l3.85 3.85a1 1 0 0 0 1.415-1.415zm-5.242 1.156a5 5 0 1 1 0-10 5 5 0 0 1 0 10'/%3e%3c/svg%3e") no-repeat 11px center;
    }
    .cust-search:focus { border-color: var(--erp-primary, #4361EE); box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.12); }
    .cust-list { max-height: 220px; overflow-y: auto; padding: 4px 0; }
    .cust-list::-webkit-scrollbar { width: 5px; }
    .cust-list::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
    .cust-option {
        padding: 8px 14px; font-size: 0.88rem; cursor: pointer; border-left: 3px solid transparent;
        transition: background 0.12s, border-color 0.12s;
    }
    .cust-option:hover, .cust-option.focused { background: #F0F4FF; border-left-color: var(--erp-primary, #4361EE); }
    .cust-option.selected { background: #EEF2FF; border-left-color: var(--erp-primary, #4361EE); font-weight: 600; }
    .cust-option .opt-name { display: block; color: #1E293B; font-weight: 600; }
    .cust-option .opt-company { display: block; font-size: 0.78rem; color: #64748B; margin-top: 1px; }
    .cust-empty { padding: 14px; text-align: center; color: #94A3B8; font-size: 0.86rem; }
    #customer_id { display: none !important; }

    /* ERP Items Table styling */
    .erp-items-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        margin-bottom: 0;
    }
    .erp-items-table thead th {
        background: #F8FAFC !important;
        color: #475569;
        font-weight: 600;
        font-size: 0.76rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.85rem 0.75rem;
        border-bottom: 2px solid #E2E8F0;
        border-top: none;
    }
    .erp-items-table tbody td {
        padding: 0.75rem 0.6rem;
        vertical-align: top;
        border-bottom: 1px solid #EDF2F7;
    }
    .erp-items-table .form-control,
    .erp-items-table .form-select {
        border-color: #DCE3EC;
        border-radius: 6px;
        font-size: 0.88rem;
    }
    .erp-items-table .form-control:focus,
    .erp-items-table .form-select:focus {
        border-color: var(--erp-primary, #4361EE);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.12);
    }
    .erp-items-table .btn-delete-row {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        color: #EF4444;
        background: #FEE2E2;
        border: 1px solid #FECACA;
        transition: all 0.15s ease;
        cursor: pointer;
    }
    .erp-items-table .btn-delete-row:hover {
        background: #EF4444;
        color: #FFFFFF;
        border-color: #EF4444;
    }
</style>
@endsection

@section('content')
<form action="{{ route('invoices.update', $invoice) }}" method="POST" id="invoice-form" novalidate>
    @csrf
    @method('PUT')
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Invoice: {{ $invoice->invoice_number }}</h5>
            <input type="hidden" name="invoice_number" value="{{ $invoice->invoice_number }}">
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <input type="hidden" name="customer_company_id" id="customer_company_id" value="{{ old('customer_company_id', $invoice->customer_company_id) }}">
                    <select name="customer_id" id="customer_id" class="cust-select-hidden" required>
                        <option value="">{{ __('Select Customer') }}</option>
                        @foreach($customers as $customer)
                            @php
                                $displayPhone = $customer->formatted_phone ?: $customer->phone;
                                $companies = $customer->companies->isNotEmpty() ? $customer->companies : collect([[
                                    'id' => null,
                                    'company_name' => $customer->company_name ?? '',
                                    'vat_no' => $customer->vat_no ?? '',
                                    'cr_no' => $customer->cr_no ?? '',
                                    'address' => $customer->address ?? ''
                                ]]);
                            @endphp
                            @foreach($companies as $comp)
                                @php
                                    $cId = is_array($comp) ? ($comp['id'] ?? '') : $comp->id;
                                    $cName = is_array($comp) ? ($comp['company_name'] ?? '') : $comp->company_name;
                                    $cVat = is_array($comp) ? ($comp['vat_no'] ?? '') : $comp->vat_no;
                                    $cCr = is_array($comp) ? ($comp['cr_no'] ?? '') : $comp->cr_no;
                                    $cAddr = is_array($comp) ? ($comp['address'] ?? '') : $comp->address;

                                    $displayStr = $customer->name;
                                    if (!empty($cName)) $displayStr .= ' — ' . $cName;
                                    if (!empty($displayPhone)) $displayStr .= ' (' . $displayPhone . ')';
                                    $targetCompId = old('customer_company_id', $invoice->customer_company_id);
                                    $isSel = (old('customer_id', $invoice->customer_id) == $customer->id && ($targetCompId == $cId || empty($targetCompId)));
                                @endphp
                                <option value="{{ $customer->id }}" 
                                        data-company-id="{{ $cId }}"
                                        data-name="{{ $customer->name }}"
                                        data-company="{{ $cName ?? '' }}" 
                                        data-phone="{{ $displayPhone ?? '' }}"
                                        data-vat="{{ $cVat ?? '' }}"
                                        data-cr="{{ $cCr ?? '' }}"
                                        data-address="{{ $cAddr ?? '' }}"
                                        data-display="{{ $displayStr }}"
                                        {{ $isSel ? 'selected' : '' }}>{{ $displayStr }}</option>
                            @endforeach
                        @endforeach
                    </select>
                    <div class="cust-dropdown" id="cust-dropdown">
                        <div class="form-floating">
                            @php
                                $currCustId = old('customer_id', $invoice->customer_id);
                                $currCompId = old('customer_company_id', $invoice->customer_company_id);
                                $selCust = $currCustId ? $customers->firstWhere('id', $currCustId) : null;
                                $selComp = null;
                                if ($selCust) {
                                    $selComp = $currCompId ? $selCust->companies->firstWhere('id', $currCompId) : $selCust->defaultCompany();
                                }
                                $selTrigger = '';
                                if ($selCust) {
                                    $selTrigger = $selCust->name;
                                    $cName = $selComp?->company_name ?: $selCust->company_name;
                                    if (!empty($cName)) $selTrigger .= ' — ' . $cName;
                                    $sPhone = $selCust->formatted_phone ?: $selCust->phone;
                                    if (!empty($sPhone)) $selTrigger .= ' (' . $sPhone . ')';
                                }
                            @endphp
                            <input type="text" id="cust-trigger" class="form-control cust-trigger @error('customer_id') is-invalid @enderror"
                                   placeholder="Customer *" autocomplete="off" readonly
                                   value="{{ $selTrigger }}">
                            <label for="cust-trigger">{{ __('Customer') }} *</label>
                            @error('customer_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="cust-panel" id="cust-panel">
                            <div class="cust-search-wrap">
                                <input type="text" class="cust-search" id="cust-search" placeholder="{{ __('Search by name, phone, or company...') }}">
                            </div>
                            <div class="cust-list" id="cust-list"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">{{ __('Invoice Date') }} *</label>
                    <input type="date" name="invoice_date" class="form-control" value="{{ old('invoice_date', $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : '') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">{{ __('Due Date') }}</label>
                    <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : '') }}">
                </div>
                <div class="col-12 mt-2 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" role="switch" name="show_customer_name" id="show_customer_name" value="1" {{ old('show_customer_name', $invoice->show_customer_name ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold text-dark small cursor-pointer" for="show_customer_name">
                            <i class="fas fa-user-check text-primary me-1"></i> Show Customer Name on PDF
                        </label>
                    </div>
                    <span class="text-muted small"><i class="fas fa-info-circle me-1"></i>Always Yes by default. Uncheck if you want to hide the Customer Name on PDF.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom"><h5 class="mb-0 fw-bold text-dark">{{ __('Invoice Items') }}</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="erp-items-table">
                    <thead>
                        <tr>
                            <th style="min-width: 320px;">{{ __('Description') }}</th>
                            <th style="min-width: 130px;">{{ __('Size and meter') }}</th>
                            <th style="min-width: 120px;" class="text-end">{{ __('Unit Price') }}</th>
                            <th style="min-width: 85px;" class="text-center">{{ __('Qty') }}</th>
                            <th style="min-width: 85px;" class="text-center">{{ __('Tax %') }}</th>
                            <th style="min-width: 100px;" class="text-end">{{ __('Discount') }}</th>
                            <th style="min-width: 120px;" class="text-end">{{ __('Total') }}</th>
                            <th style="width: 50px;" class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="item-rows">
                        @foreach($invoice->items as $index => $item)
                        <tr class="item-row align-top">
                            <td>
                                <div class="mb-1">
                                    <select name="items[{{ $index }}][product_id]" class="form-select form-select-sm product-picker bg-light border" style="font-size: 0.82rem;">
                                        <option value="">-- {{ __('Select Product / Service (or custom below)') }} --</option>
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->id }}"
                                                    data-name="{{ $prod->name }}"
                                                    data-sku="{{ $prod->sku }}"
                                                    data-price="{{ $prod->sale_price ?? $prod->price }}"
                                                    data-tax="{{ $prod->tax_percentage ?? ($setting->default_tax_rate ?? 0) }}"
                                                    data-stock="{{ $prod->stock }}"
                                                    data-type="{{ $prod->type }}"
                                                    data-unit="{{ $prod->unit?->name }}"
                                                    {{ (string)$item->product_id === (string)$prod->id ? 'selected' : '' }}>
                                                {{ $prod->name }} [{{ $prod->sku }}] &bull; {{ format_currency($prod->sale_price ?? $prod->price) }}
                                                @if($prod->type === 'product')
                                                    ({{ __('Stock:') }} {{ $prod->stock }})
                                                @else
                                                    ({{ __('Service') }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <textarea name="items[{{ $index }}][description]" class="form-control item-desc bg-white border p-2 shadow-sm" rows="2" style="font-size: 0.88rem; resize: vertical; min-height: 52px;" required>{{ old("items.$index.description", tdb($item->description)) }}</textarea>
                                <div class="stock-badge-wrap mt-1">
                                    @if($item->product)
                                        @if($item->product->type === 'product')
                                            <span class="badge bg-light text-muted border extra-small">
                                                <i class="fas fa-boxes text-primary me-1"></i> {{ __('Current Stock:') }} <strong>{{ $item->product->stock }}</strong>
                                            </span>
                                        @else
                                            <span class="badge bg-light text-info border extra-small">
                                                <i class="fas fa-cog text-info me-1"></i> {{ __('Service') }}
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td><input type="text" name="items[{{ $index }}][item_text]" class="form-control item-unit-text bg-white border p-2 shadow-sm" value="{{ $item->item_text }}" placeholder="Size / Meter"></td>
                            <td><input type="number" step="0.01" name="items[{{ $index }}][unit_price]" class="form-control p-2 shadow-sm unit-price text-end rounded-2" value="{{ $item->unit_price }}" required></td>
                            <td><input type="number" name="items[{{ $index }}][quantity]" class="form-control p-2 shadow-sm quantity text-center rounded-2" value="{{ $item->quantity }}" min="1" required></td>
                            <td><input type="number" step="0.01" name="items[{{ $index }}][tax_percentage]" class="form-control p-2 shadow-sm tax-percentage text-center rounded-2" value="{{ $item->tax_percentage }}"></td>
                            <td><input type="number" step="0.01" name="items[{{ $index }}][discount]" class="form-control p-2 shadow-sm discount text-end rounded-2" value="{{ $item->discount }}"></td>
                            <td><input type="number" step="0.01" name="items[{{ $index }}][total]" class="form-control p-2 shadow-sm line-total border-0 bg-transparent text-end fw-bold text-dark" readonly value="{{ $item->total }}"></td>
                            <td class="text-center align-middle">
                                <button type="button" class="btn-delete-row remove-item" title="{{ __('Remove Item') }}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top bg-light-subtle rounded-bottom-4">
                <button type="button" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center" id="add-item">
                    <i class="fas fa-plus me-2"></i> {{ __('Add Item Row') }}
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card mb-4">
                <div class="card-body">
                    <label class="form-label">{{ __('Terms & Conditions') }}</label>
                    <textarea name="terms" class="form-control" rows="3">{{ old('terms', tdb($invoice->terms)) }}</textarea>
                    <label class="form-label mt-3">{{ __('Notes') }}</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('Additional notes for the customer...') }}">{{ old('notes', tdb($invoice->notes)) }}</textarea>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td>{{ __('Subtotal') }}</td><td><input type="number" step="0.01" name="subtotal" id="subtotal" class="form-control text-end border-0" readonly value="{{ $invoice->subtotal }}"></td></tr>
                        <tr><td>{{ __('Total Tax') }}</td><td><input type="number" step="0.01" name="total_tax" id="total_tax" class="form-control text-end border-0" readonly value="{{ $invoice->total_tax }}"></td></tr>
                        <tr><td>{{ __('Total Discount') }}</td><td><input type="number" step="0.01" name="total_discount" id="total_discount" class="form-control text-end border-0" readonly value="{{ $invoice->total_discount }}"></td></tr>
                        <tr><td>{{ __('Shipping') }}</td><td><input type="number" step="0.01" name="shipping" id="shipping" class="form-control text-end" value="{{ $invoice->shipping }}"></td></tr>
                        <tr class="table-light fw-bold"><td>{{ __('Grand Total') }}</td><td><input type="number" step="0.01" name="grand_total" id="grand_total" class="form-control text-end border-0 fw-bold" readonly value="{{ $invoice->grand_total }}"></td></tr>
                    </table>
                </div>
            </div>
            <div class="text-end mt-4">
                <a href="{{ route('invoices.index') }}" class="btn btn-secondary me-2">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-lg btn-success px-5">Update Invoice</button>
            </div>
        </div>
    </div>
</form>

<template id="item-template">
    <tr class="item-row align-top">
        <td>
            <div class="mb-1">
                <select name="items[INDEX][product_id]" class="form-select form-select-sm product-picker bg-light border" style="font-size: 0.82rem;">
                    <option value="">-- {{ __('Select Product / Service (or custom below)') }} --</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}"
                                data-name="{{ $prod->name }}"
                                data-sku="{{ $prod->sku }}"
                                data-price="{{ $prod->sale_price ?? $prod->price }}"
                                data-tax="{{ $prod->tax_percentage ?? ($setting->default_tax_rate ?? 0) }}"
                                data-stock="{{ $prod->stock }}"
                                data-type="{{ $prod->type }}"
                                data-unit="{{ $prod->unit?->name }}">
                            {{ $prod->name }} [{{ $prod->sku }}] &bull; {{ format_currency($prod->sale_price ?? $prod->price) }}
                            @if($prod->type === 'product')
                                ({{ __('Stock:') }} {{ $prod->stock }})
                            @else
                                ({{ __('Service') }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <textarea name="items[INDEX][description]" class="form-control item-desc bg-white border p-2 shadow-sm" rows="2" style="font-size: 0.88rem; resize: vertical; min-height: 52px;" placeholder="{{ __('Enter item description...') }}" required></textarea>
            <div class="stock-badge-wrap mt-1"></div>
        </td>
        <td><input type="text" name="items[INDEX][item_text]" class="form-control item-unit-text bg-white border p-2 shadow-sm" placeholder="Size / Meter"></td>
        <td><input type="number" step="0.01" name="items[INDEX][unit_price]" class="form-control p-2 shadow-sm unit-price text-end rounded-2" value="0.00" required></td>
        <td><input type="number" name="items[INDEX][quantity]" class="form-control p-2 shadow-sm quantity text-center rounded-2" value="1" min="1" required></td>
        <td><input type="number" step="0.01" name="items[INDEX][tax_percentage]" class="form-control p-2 shadow-sm tax-percentage text-center rounded-2" value="{{ $setting->default_tax_rate ?? 0 }}"></td>
        <td><input type="number" step="0.01" name="items[INDEX][discount]" class="form-control p-2 shadow-sm discount text-end rounded-2" value="0.00"></td>
        <td><input type="number" step="0.01" name="items[INDEX][total]" class="form-control p-2 shadow-sm line-total border-0 bg-transparent text-end fw-bold text-dark" readonly value="0.00"></td>
        <td class="text-center align-middle">
            <button type="button" class="btn-delete-row remove-item" title="{{ __('Remove Item') }}">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    </tr>
</template>

@section('scripts')
<script>
    /* Custom Searchable Customer Dropdown */
    (function () {
        var realSelect = document.getElementById('customer_id');
        var trigger    = document.getElementById('cust-trigger');
        var panel      = document.getElementById('cust-panel');
        var searchBox  = document.getElementById('cust-search');
        var listEl     = document.getElementById('cust-list');
        var dropdown   = document.getElementById('cust-dropdown');
        var compHidden = document.getElementById('customer_company_id');
        var selectedId = realSelect.value || '';
        var selectedCompanyId = compHidden ? compHidden.value : '';

        var customers = [];
        Array.from(realSelect.options).forEach(function(opt) {
            if (!opt.value) return;
            customers.push({ 
                id: opt.value, 
                companyId: opt.dataset.companyId || '',
                name: opt.dataset.name || opt.text.trim(), 
                company: opt.dataset.company || '', 
                phone: opt.dataset.phone || '',
                vat: opt.dataset.vat || '',
                cr: opt.dataset.cr || '',
                address: opt.dataset.address || '',
                display: opt.dataset.display || opt.text.trim()
            });
        });

        function esc(s) { return (s || '').replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }

        function hl(text, q) {
            if (!text) return '';
            if (!q) return esc(text);
            var rx = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')', 'gi');
            return esc(text).replace(rx, '<mark style="background:#fff3cd;padding:0 2px;border-radius:2px">$1</mark>');
        }

        function renderList(filter) {
            filter = (filter || '').toLowerCase().trim();
            var filterDigits = filter.replace(/[^\d]/g, '').replace(/^0+/, '');
            var filtered = customers.filter(function(c) {
                var phoneDigits = c.phone.replace(/[^\d]/g, '');
                var matchPhone = c.phone.toLowerCase().indexOf(filter) > -1 || 
                                 (filterDigits.length >= 3 && phoneDigits.indexOf(filterDigits) > -1);
                return c.name.toLowerCase().indexOf(filter) > -1 || 
                       c.company.toLowerCase().indexOf(filter) > -1 ||
                       matchPhone;
            });
            if (!filtered.length) {
                listEl.innerHTML = '<div class="cust-empty"><i class="fas fa-search me-1"></i>'+ window.__('No customers found', 'No customers found') +'</div>';
                return;
            }
            listEl.innerHTML = filtered.map(function(c) {
                var phoneBadge = c.phone ? '<span class="badge bg-light text-primary border opt-phone"><i class="fas fa-phone-alt me-1"></i>' + hl(c.phone, filter) + '</span>' : '';
                var companyHtml = c.company ? '<div class="opt-company"><i class="fas fa-building me-1 text-muted"></i>' + hl(c.company, filter) + '</div>' : '';
                var isSel = (c.id == selectedId && (!selectedCompanyId || c.companyId == selectedCompanyId));
                return '<div class="cust-option' + (isSel ? ' selected' : '') + '" data-id="' + c.id + '" data-company-id="' + c.companyId + '" data-display="' + esc(c.display) + '" data-vat="' + esc(c.vat) + '" data-cr="' + esc(c.cr) + '" data-address="' + esc(c.address) + '">'
                     + '  <div class="d-flex justify-content-between align-items-center">'
                     + '    <span class="opt-name">' + hl(c.name, filter) + '</span>'
                     + '    ' + phoneBadge
                     + '  </div>'
                     + '  ' + companyHtml
                     + '</div>';
            }).join('');
            listEl.querySelectorAll('.cust-option').forEach(function(el) {
                el.addEventListener('click', function() { 
                    selectCustomer(el.dataset.id, el.dataset.companyId, el.dataset.display, el.dataset.vat, el.dataset.cr, el.dataset.address); 
                });
            });
        }

        function selectCustomer(id, companyId, display, vat, cr, address) {
            selectedId = id;
            selectedCompanyId = companyId;
            realSelect.value = id;
            if (compHidden) compHidden.value = companyId || '';
            trigger.value = display;
            trigger.classList.remove('is-invalid');
            closePanel();
        }

        function openPanel() {
            panel.classList.add('open');
            searchBox.value = '';
            renderList('');
            setTimeout(function(){ searchBox.focus(); }, 50);
        }

        function closePanel() {
            panel.classList.remove('open');
        }

        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            panel.classList.contains('open') ? closePanel() : openPanel();
        });

        searchBox.addEventListener('input', function() { renderList(searchBox.value); });
        searchBox.addEventListener('click', function(e) { e.stopPropagation(); });

        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target)) closePanel();
        });

        if (selectedId) {
            var found = customers.find(function(c){ 
                return c.id == selectedId && (!selectedCompanyId || c.companyId == selectedCompanyId); 
            }) || customers.find(function(c){ return c.id == selectedId; });
            if (found) {
                trigger.value = found.display || found.name;
                if (compHidden && found.companyId) compHidden.value = found.companyId;
            }
        }

        renderList('');
    })();

    let rowIndex = {{ $invoice->items->count() }};
    const itemRows = document.getElementById('item-rows');
    const template = document.getElementById('item-template').innerHTML;
    function addRow() {
        const tr = document.createElement('tr'); tr.className = 'item-row';
        tr.innerHTML = template.replace(/INDEX/g, rowIndex++);
        itemRows.appendChild(tr); calculate();
    }
    function calculate() {
        let subtotal = 0, totalTax = 0, totalDiscount = 0;
        const shipping = parseFloat(document.getElementById('shipping').value) || 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const price = parseFloat(row.querySelector('.unit-price').value) || 0;
            const qty = parseFloat(row.querySelector('.quantity').value) || 0;
            const taxP = parseFloat(row.querySelector('.tax-percentage').value) || 0;
            const disc = parseFloat(row.querySelector('.discount').value) || 0;
            const lineSubtotal = price * qty;
            const lineTax = lineSubtotal * (taxP / 100);
            const lineTotal = lineSubtotal + lineTax - disc;
            row.querySelector('.line-total').value = lineTotal.toFixed(2);
            subtotal += lineSubtotal; totalTax += lineTax; totalDiscount += disc;
        });
        const grandTotal = subtotal + totalTax - totalDiscount + shipping;
        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('total_tax').value = totalTax.toFixed(2);
        document.getElementById('total_discount').value = totalDiscount.toFixed(2);
        document.getElementById('grand_total').value = grandTotal.toFixed(2);
    }
    document.getElementById('add-item').addEventListener('click', addRow);
    document.getElementById('invoice-form').addEventListener('submit', function(e) {
        let isValid = true;
        let errorMessage = '';

        const customer = document.getElementById('customer_id').value;
        if (!customer) {
            isValid = false;
            errorMessage = 'Please select a customer.';
        }

        const rows = document.querySelectorAll('.item-row');
        if (rows.length === 0 && isValid) {
            isValid = false;
            errorMessage = 'Please add at least one item to the invoice.';
        }

        if (isValid) {
            for (let i = 0; i < rows.length; i++) {
                const descInput = rows[i].querySelector('textarea[name^="items"][name$="[description]"]');
                const qtyInput = rows[i].querySelector('.quantity');
                const priceInput = rows[i].querySelector('.unit-price');
                
                const desc = descInput ? descInput.value.trim() : '';
                const qty = qtyInput ? parseFloat(qtyInput.value) || 0 : 0;
                const price = priceInput ? parseFloat(priceInput.value) : NaN;

                if (!desc) {
                    isValid = false;
                    errorMessage = 'Please enter a description for item #' + (i + 1) + '.';
                    break;
                }
                if (qty <= 0) {
                    isValid = false;
                    errorMessage = 'Please enter a valid quantity for item #' + (i + 1) + '.';
                    break;
                }
                if (isNaN(price) || price < 0) {
                    isValid = false;
                    errorMessage = 'Please enter a valid price for item #' + (i + 1) + '.';
                    break;
                }
            }
        }

        if (!isValid) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: errorMessage,
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
            } else {
                alert(errorMessage);
            }
        }
    });

    document.getElementById('item-rows').addEventListener('input', calculate);
    document.getElementById('shipping').addEventListener('input', calculate);
    document.getElementById('item-rows').addEventListener('click', e => {
        if (e.target.closest('.remove-item')) {
            e.target.closest('tr').remove(); calculate();
        }
    });

    // Auto-fill row when product picker is selected
    document.getElementById('item-rows').addEventListener('change', function(e) {
        if (e.target.classList.contains('product-picker')) {
            const select = e.target;
            const row = select.closest('.item-row');
            const selectedOpt = select.options[select.selectedIndex];
            
            if (!selectedOpt || !select.value) {
                const badgeWrap = row.querySelector('.stock-badge-wrap');
                if (badgeWrap) badgeWrap.innerHTML = '';
                return;
            }

            const name = selectedOpt.dataset.name || '';
            const sku = selectedOpt.dataset.sku || '';
            const price = parseFloat(selectedOpt.dataset.price) || 0;
            const tax = parseFloat(selectedOpt.dataset.tax) || 0;
            const stock = selectedOpt.dataset.stock !== undefined ? parseInt(selectedOpt.dataset.stock) : null;
            const type = selectedOpt.dataset.type || 'product';
            const unit = selectedOpt.dataset.unit || '';

            const descInput = row.querySelector('.item-desc');
            const priceInput = row.querySelector('.unit-price');
            const taxInput = row.querySelector('.tax-percentage');
            const unitInput = row.querySelector('.item-unit-text');
            const badgeWrap = row.querySelector('.stock-badge-wrap');

            if (descInput) {
                descInput.value = (sku ? '[' + sku + '] ' : '') + name;
            }
            if (priceInput) {
                priceInput.value = price.toFixed(2);
            }
            if (taxInput) {
                taxInput.value = tax.toFixed(2);
            }
            if (unitInput && unit && !unitInput.value) {
                unitInput.value = unit;
            }

            if (badgeWrap) {
                if (type === 'product') {
                    let badgeClass = 'bg-light text-success border';
                    let badgeText = '{{ __("In Stock:") }} ' + stock;
                    if (stock <= 0) {
                        badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                        badgeText = '{{ __("Out of Stock (0)") }}';
                    } else if (stock <= 5) {
                        badgeClass = 'bg-warning-subtle text-warning-emphasis border border-warning-subtle';
                        badgeText = '{{ __("Low Stock:") }} ' + stock;
                    }
                    badgeWrap.innerHTML = `<span class="badge ${badgeClass} extra-small"><i class="fas fa-boxes me-1"></i>${badgeText}</span>`;
                } else {
                    badgeWrap.innerHTML = `<span class="badge bg-light text-info border extra-small"><i class="fas fa-cog me-1"></i>{{ __("Service") }}</span>`;
                }
            }

            calculate();
        }
    });
</script>
@endsection
@endsection




