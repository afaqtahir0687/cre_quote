@extends('admin.layouts.app')

@section('title', __('Create New Invoice'))

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
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">{{ __('Create Invoice') }}</h2>
    <div class="d-flex align-items-center bg-white px-3 py-2 rounded-3 shadow-sm border">
        <span class="text-muted me-2 small fw-medium">{{ __('Invoice Number:') }}</span>
        <span class="fw-bold text-primary">{{ $invoiceNumber }}</span>
    </div>
</div>

<form action="{{ route('invoices.store') }}" method="POST" id="invoice-form" novalidate>
    @csrf
    <input type="hidden" name="invoice_number" value="{{ $invoiceNumber }}">
    
    <div class="row g-4">
        <div class="col-lg-12">
            <x-card title="{{ __('Invoice Details') }}">
                <div class="row">
                    <div class="col-md-4">
                        <input type="hidden" name="customer_company_id" id="customer_company_id" value="{{ old('customer_company_id') }}">
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
                                        $isSel = (old('customer_id') == $customer->id && (old('customer_company_id') == $cId || empty(old('customer_company_id'))));
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
                                    $selCust = old('customer_id') ? $customers->firstWhere('id', old('customer_id')) : null;
                                    $selCompId = old('customer_company_id');
                                    $selComp = null;
                                    if ($selCust) {
                                        $selComp = $selCompId ? $selCust->companies->firstWhere('id', $selCompId) : $selCust->defaultCompany();
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
                    <div class="col-md-4">
                        <x-form-input label="Invoice Date *" name="invoice_date" type="date" :value="date('Y-m-d')" required />
                    </div>
                    <div class="col-md-4">
                        <x-form-input label="Due Date" name="due_date" type="date" :value="date('Y-m-d', strtotime('+15 days'))" />
                    </div>
                    <div class="col-12 mt-2 pt-2 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" role="switch" name="show_customer_name" id="show_customer_name" value="1" {{ old('show_customer_name', $setting->show_customer_name_on_pdf ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-dark small cursor-pointer" for="show_customer_name">
                                <i class="fas fa-user-check text-primary me-1"></i> Show Customer Name on PDF
                            </label>
                        </div>
                        <span class="text-muted small"><i class="fas fa-info-circle me-1"></i>Always Yes by default. Uncheck if you want to hide the Customer Name on PDF.</span>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="col-lg-12">
            <x-card title="{{ __('Invoice Items') }}" :padding="false">
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
                            <!-- Items will be added here via JS -->
                        </tbody>
                    </table>
                </div>
                
                <div class="p-3 border-top bg-light-subtle rounded-bottom-4">
                    <button type="button" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center" id="add-item">
                        <i class="fas fa-plus me-2"></i> {{ __('Add Item Row') }}
                    </button>
                </div>
            </x-card>
        </div>

        <div class="col-md-7">
            <x-card :title="__('Additional Information')">
                <div class="mb-3">
                    <label class="form-label text-muted fw-medium small">{{ __('Payment Terms & Conditions') }}</label>
                    <textarea name="terms" class="form-control" rows="3">{{ old('terms', "1. Payment is due within 15 days.\n2. Please include invoice number on your check.") }}</textarea>
                </div>
                
                <div class="mb-0">
                    <label class="form-label text-muted fw-medium small">{{ __('Notes') }}</label>
                    <textarea name="notes" class="form-control" rows="5" placeholder="{{ __('Additional notes for the customer...') }}">{{ old('notes', 'Dear Customer attention, Please Check The Design, Printing material type Size, And All other Details Completely before Signature on Print Approval. However After the Signature ' . ($setting->company_name ?? 'New sign') . ' or Designer Is not Responsible For Any mistake Printing Colours can Be Different than Desktop Computer Screen. Please, ask for a color sample if it is necessary') }}</textarea>
                </div>
            </x-card>
        </div>

        <div class="col-md-5">
            <x-card title="Summary">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">{{ __('Subtotal') }}</span>
                        <div class="input-group input-group-sm w-50">
                            <span class="input-group-text border-0 bg-transparent">$</span>
                            <input type="number" step="0.01" name="subtotal" id="subtotal" class="form-control text-end border-0 bg-transparent fw-semibold" readonly value="0.00">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">{{ __('Total Tax') }}</span>
                        <div class="input-group input-group-sm w-50">
                            <span class="input-group-text border-0 bg-transparent">$</span>
                            <input type="number" step="0.01" name="total_tax" id="total_tax" class="form-control text-end border-0 bg-transparent fw-semibold" readonly value="0.00">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">{{ __('Total Discount') }}</span>
                        <div class="input-group input-group-sm w-50">
                            <span class="input-group-text border-0 bg-transparent">-$</span>
                            <input type="number" step="0.01" name="total_discount" id="total_discount" class="form-control text-end border-0 bg-transparent fw-semibold" readonly value="0.00">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="text-muted">{{ __('Shipping') }}</span>
                        <div class="input-group input-group-sm w-50">
                            <span class="input-group-text border-0 bg-light">$</span>
                            <input type="number" step="0.01" name="shipping" id="shipping" class="form-control text-end border-1 rounded-2" value="0.00">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 p-3 bg-light rounded-3">
                        <span class="fw-bold text-dark fs-5">{{ __('Grand Total') }}</span>
                        <div class="input-group w-50">
                            <span class="input-group-text border-0 bg-transparent fs-5 fw-bold">$</span>
                            <input type="number" step="0.01" name="grand_total" id="grand_total" class="form-control text-end border-0 bg-transparent fs-5 fw-bold" readonly value="0.00">
                        </div>
                    </div>

                    <!-- Instant Payment on Sale Section -->
                    <div class="border rounded-3 p-3 mt-3 bg-white shadow-sm">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" id="immediate_payment_switch" name="receive_payment" value="1">
                            <label class="form-check-label fw-bold text-dark small" for="immediate_payment_switch">
                                <i class="fas fa-money-bill-wave text-success me-1"></i> {{ __('Record Payment on Sale') }}
                            </label>
                        </div>
                        <div id="payment_details_box" style="display: none;" class="pt-2 border-top">
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label small fw-semibold text-muted mb-0">{{ __('Amount Paid') }}</label>
                                    <button type="button" class="btn btn-xs btn-outline-success py-0 px-2" id="btn-pay-full" style="font-size: 0.72rem;">
                                        {{ __('Pay Full Amount') }}
                                    </button>
                                </div>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="amount_paid" id="amount_paid_input" class="form-control text-end fw-bold text-success" value="0.00">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Payment Method') }}</label>
                                <select name="payment_method" class="form-select form-select-sm">
                                    <option value="Cash">{{ __('Cash') }}</option>
                                    <option value="Bank Transfer">{{ __('Bank Transfer') }}</option>
                                    <option value="Credit Card">{{ __('Credit Card') }}</option>
                                    <option value="Debit Card">{{ __('Debit Card') }}</option>
                                    <option value="Online / POS">{{ __('Online / POS') }}</option>
                                    <option value="Cheque">{{ __('Cheque') }}</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Transaction / Reference #') }}</label>
                                <input type="text" name="payment_reference" class="form-control form-control-sm" placeholder="{{ __('e.g. TXN-84920 or POS receipt') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-grid gap-2">
                    <button type="submit" class="btn btn-primary-modern py-3">
                        <i class="fas fa-save me-2"></i> {{ __('Generate & Save Invoice') }}
                    </button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-link text-muted">{{ __('Cancel and Go Back') }}</a>
                </div>
            </x-card>
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

    let rowIndex = 0;
    const itemRows = document.getElementById('item-rows');
    const template = document.getElementById('item-template').innerHTML;

    function addRow() {
        const tr = document.createElement('tr');
        tr.className = 'item-row align-middle';
        tr.innerHTML = template.replace(/INDEX/g, rowIndex++);
        itemRows.appendChild(tr);
        calculate();
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

            subtotal += lineSubtotal;
            totalTax += lineTax;
            totalDiscount += disc;
        });

        const grandTotal = subtotal + totalTax - totalDiscount + shipping;
        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('total_tax').value = totalTax.toFixed(2);
        document.getElementById('total_discount').value = totalDiscount.toFixed(2);
        document.getElementById('grand_total').value = grandTotal.toFixed(2);

        if (typeof syncPaidAmount === 'function') {
            syncPaidAmount();
        }
    }

    // Instant Payment Toggle
    const paySwitch = document.getElementById('immediate_payment_switch');
    const payBox = document.getElementById('payment_details_box');
    const paidInput = document.getElementById('amount_paid_input');
    const btnPayFull = document.getElementById('btn-pay-full');

    function syncPaidAmount() {
        if (paySwitch && paySwitch.checked && paidInput) {
            const gt = parseFloat(document.getElementById('grand_total').value) || 0;
            paidInput.value = gt.toFixed(2);
        }
    }

    if (paySwitch && payBox) {
        paySwitch.addEventListener('change', function() {
            if (this.checked) {
                payBox.style.display = 'block';
                const gt = parseFloat(document.getElementById('grand_total').value) || 0;
                paidInput.value = gt.toFixed(2);
            } else {
                payBox.style.display = 'none';
                paidInput.value = '0.00';
            }
        });
    }

    if (btnPayFull && paidInput) {
        btnPayFull.addEventListener('click', function() {
            const gt = parseFloat(document.getElementById('grand_total').value) || 0;
            paidInput.value = gt.toFixed(2);
        });
    }

    // Product Picker change event: auto-populate line details
    itemRows.addEventListener('change', function(e) {
        if (e.target.classList.contains('product-picker')) {
            const select = e.target;
            const row = select.closest('.item-row');
            const opt = select.options[select.selectedIndex];
            const badgeWrap = row.querySelector('.stock-badge-wrap');

            if (!opt || !opt.value) {
                if (badgeWrap) badgeWrap.innerHTML = '';
                return;
            }

            const name = opt.getAttribute('data-name') || '';
            const sku = opt.getAttribute('data-sku') || '';
            const price = parseFloat(opt.getAttribute('data-price')) || 0;
            const tax = parseFloat(opt.getAttribute('data-tax')) || 0;
            const stock = parseInt(opt.getAttribute('data-stock')) || 0;
            const type = opt.getAttribute('data-type') || 'product';
            const unit = opt.getAttribute('data-unit') || '';

            const descArea = row.querySelector('.item-desc');
            if (descArea) {
                descArea.value = (sku ? '[' + sku + '] ' : '') + name;
            }

            const priceInput = row.querySelector('.unit-price');
            if (priceInput) {
                priceInput.value = price.toFixed(2);
            }

            const taxInput = row.querySelector('.tax-percentage');
            if (taxInput) {
                taxInput.value = tax.toFixed(2);
            }

            const unitTextInput = row.querySelector('.item-unit-text');
            if (unitTextInput && unit) {
                unitTextInput.value = unit;
            }

            if (badgeWrap) {
                if (type === 'service') {
                    badgeWrap.innerHTML = '<span class="badge bg-light text-secondary border"><i class="fas fa-concierge-bell me-1"></i>{{ __("Service Item") }}</span>';
                } else if (stock <= 0) {
                    badgeWrap.innerHTML = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fas fa-exclamation-triangle me-1"></i>{{ __("Out of Stock") }} (0 {{ __("in stock") }})</span>';
                } else if (stock <= 5) {
                    badgeWrap.innerHTML = '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle"><i class="fas fa-exclamation-circle me-1"></i>{{ __("Low Stock:") }} ' + stock + ' {{ __("available") }}</span>';
                } else {
                    badgeWrap.innerHTML = '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fas fa-check-circle me-1"></i>{{ __("In Stock:") }} ' + stock + ' {{ __("available") }}</span>';
                }
            }

            calculate();
        }
    });

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

    document.getElementById('invoice-form').addEventListener('input', (e) => {
        if (e.target.classList.contains('unit-price') || 
            e.target.classList.contains('quantity') || 
            e.target.classList.contains('tax-percentage') || 
            e.target.classList.contains('discount') ||
            e.target.id === 'shipping') {
            calculate();
        }
    });

    itemRows.addEventListener('click', e => {
        if (e.target.closest('.remove-item')) {
            e.target.closest('tr').remove();
            calculate();
        }
    });

    // Add initial row
    addRow();

    @if(isset($selectedProduct) && $selectedProduct)
        setTimeout(function() {
            const firstRow = document.querySelector('.item-row');
            if (firstRow) {
                const picker = firstRow.querySelector('.product-picker');
                if (picker) {
                    picker.value = "{{ $selectedProduct->id }}";
                    picker.dispatchEvent(new Event('change'));
                }
            }
        }, 120);
    @endif
</script>
@endsection
@endsection



