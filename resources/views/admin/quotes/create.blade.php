@extends('admin.layouts.app')

@section('title', 'Create New Quote')

@section('styles')
<style>
    .cust-dropdown { position: relative; }
    .cust-trigger {
        cursor: pointer;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px 12px;
        padding-right: 2.25rem !important;
    }
    .cust-trigger:focus { box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25); border-color: #86b7fe; }
    .cust-panel {
        display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0;
        z-index: 1055; background:#fff; border:1px solid #dee2e6;
        border-radius:0.5rem; box-shadow:0 8px 24px rgba(0,0,0,.12); overflow:hidden;
    }
    .cust-panel.open { display: block; }
    .cust-search-wrap { padding:8px 10px; border-bottom:1px solid #f0f0f0; background:#f8f9fa; }
    .cust-search {
        width:100%; border:1px solid #dee2e6; border-radius:0.375rem;
        padding:6px 10px 6px 32px; font-size:.875rem; outline:none;
        background:#fff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%236c757d' viewBox='0 0 16 16'%3e%3cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398l3.85 3.85a1 1 0 0 0 1.415-1.415zm-5.242 1.156a5 5 0 1 1 0-10 5 5 0 0 1 0 10'/%3e%3c/svg%3e") no-repeat 10px center;
    }
    .cust-search:focus { border-color:#86b7fe; box-shadow:0 0 0 3px rgba(13,110,253,.15); }
    .cust-list { max-height:220px; overflow-y:auto; padding:4px 0; }
    .cust-list::-webkit-scrollbar { width:5px; }
    .cust-list::-webkit-scrollbar-thumb { background:#dee2e6; border-radius:4px; }
    .cust-option {
        padding:8px 14px; font-size:.9rem; cursor:pointer;
        border-left:3px solid transparent; transition:background .12s, border-color .12s;
    }
    .cust-option:hover, .cust-option.focused { background:#f0f4ff; border-left-color:#0d6efd; }
    .cust-option.selected { background:#e7f0ff; border-left-color:#0d6efd; font-weight:600; }
    .cust-option .opt-name { display:block; color:#212529; }
    .cust-option .opt-company { display:block; font-size:.78rem; color:#6c757d; margin-top:1px; }
    .cust-empty { padding:14px; text-align:center; color:#6c757d; font-size:.875rem; }
    #customer_id { display:none !important; }
</style>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0">Create Quote</h2>
        <div class="d-flex align-items-center gap-2">
            <div class="d-flex align-items-center bg-white px-3 py-2 rounded-3 shadow-sm border">
                <span class="text-muted me-2 small fw-medium">Created By:</span>
                <i class="fas fa-user-circle text-primary me-1"></i>
                <span class="fw-semibold text-dark">{{ auth()->user()->name }}</span>
            </div>
            <div class="d-flex align-items-center bg-white px-3 py-2 rounded-3 shadow-sm border">
                <span class="text-muted me-2 small fw-medium">Quote Number:</span>
                <span class="fw-bold text-primary">{{ $quoteNumber }}</span>
            </div>
        </div>
    </div>

    <form action="{{ route('quotes.store') }}" method="POST" id="quote-form" enctype="multipart/form-data" novalidate>
        @csrf
        <input type="hidden" name="quote_number" value="{{ $quoteNumber }}">
        <input type="hidden" name="customer_company_id" id="customer_company_id" value="{{ old('customer_company_id') }}">
        <select name="customer_id" id="customer_id" required>
            <option value="">Select Customer</option>
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
                        if (!empty($cName)) {
                            $displayStr .= ' — ' . $cName;
                        }
                        if (!empty($displayPhone)) {
                            $displayStr .= ' (' . $displayPhone . ')';
                        }
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

        <div class="row g-4">
            <div class="col-lg-12">
                <x-card title="Quote Details">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex gap-2 align-items-start">
                                <div class="flex-grow-1 cust-dropdown" id="cust-dropdown">
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
                                        <label for="cust-trigger">Customer *</label>
                                        @error('customer_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="cust-panel" id="cust-panel">
                                        <div class="cust-search-wrap">
                                            <input type="text" class="cust-search" id="cust-search" placeholder="Search by name, phone, or company...">
                                        </div>
                                        <div class="cust-list" id="cust-list"></div>
                                    </div>
                                </div>
                                <div class="pt-1">
                                    <button type="button" class="btn btn-outline-success"
                                            style="height:58px;width:42px;padding:0"
                                            data-bs-toggle="modal" data-bs-target="#addCustomerModal" title="Add new customer">
                                        <i class="fas fa-user-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <x-form-input label="Quote Date *" name="quote_date" type="date" :value="date('Y-m-d')" required />
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
                <x-card title="Quote Items" :padding="false">
                    <x-table>
                        <x-slot name="thead">
                            <th style="width:25%">Description</th>
                            <th style="width:8%">Images</th>
                            <th style="width:12%">Size and meter</th>
                            <th style="width:12%">Unit Price</th>
                            <th style="width:8%">Qty</th>
                            <th style="width:10%">Vat %</th>
                            <th style="width:10%">Discount</th>
                            <th style="width:10%">Total</th>
                            <th style="width:5%" class="text-center"></th>
                        </x-slot>
                        <tbody id="item-rows"></tbody>
                    </x-table>
                    <div class="p-3 border-top bg-light-subtle rounded-bottom-4">
                        <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center" id="add-item">
                            <i class="fas fa-plus me-2"></i> Add Item Row
                        </button>
                    </div>
                </x-card>
            </div>

            <div class="col-md-7">
                <x-card title="Additional Information">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium small">Payment Terms &amp; Conditions</label>
                        <textarea name="terms" class="form-control" rows="3">1. This quote is valid for 30 days.
    2. Prices are subject to change after the validity period.</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium small">Notes</label>
                        <textarea name="notes" class="form-control" rows="8" placeholder="Additional notes for the customer...">Kindly be aware that this quotation may be reviewed, and the New Crelogics retains the right to amend the pricing if errors are identified. Additionally, the New Crelogics reserves the right to revise, reject, or cancel the work entirely if there is any misunderstanding regarding the customer's requirements.

يرجى العلم أن هذا العرض قد يخضع للمراجعة، وتحتفظ وكالة نيو كريلوجيكس (New Crelogics) بالحق في تعديل الأسعار إذا تم اكتشاف أي أخطاء.
بالإضافة إلى ذلك، تحتفظ وكالة نيو كريلوجيكس (New Crelogics) بالحق في تعديل السعر أو رفض العمل أو إلغائه بالكامل إذا كان هناك أي سوء فهم بخصوص متطلبات العميل.</textarea>
                    </div>
                </x-card>
            </div>

            <div class="col-md-5">
                <x-card title="Summary">
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Subtotal</span>
                            <div class="input-group input-group-sm w-50">
                                <span class="input-group-text border-0 bg-transparent">SAR</span>
                                <input type="number" step="0.01" name="subtotal" id="subtotal" class="form-control text-end border-0 bg-transparent fw-semibold" readonly value="0.00">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Tax</span>
                            <div class="input-group input-group-sm w-50">
                                <span class="input-group-text border-0 bg-transparent">SAR</span>
                                <input type="number" step="0.01" name="total_tax" id="total_tax" class="form-control text-end border-0 bg-transparent fw-semibold" readonly value="0.00">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Discount</span>
                            <div class="input-group input-group-sm w-50">
                                <span class="input-group-text border-0 bg-transparent">-SAR</span>
                                <input type="number" step="0.01" name="total_discount" id="total_discount" class="form-control text-end border-0 bg-transparent fw-semibold" readonly value="0.00">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-muted">Shipping</span>
                            <div class="input-group input-group-sm w-50">
                                <span class="input-group-text border-0 bg-light">SAR</span>
                                <input type="number" step="0.01" name="shipping" id="shipping" class="form-control text-end border-1 rounded-2" value="0.00">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3 p-3 bg-light rounded-3">
                            <span class="fw-bold text-dark fs-5">Grand Total</span>
                            <div class="input-group w-50">
                                <span class="input-group-text border-0 bg-transparent fs-5 fw-bold">SAR</span>
                                <input type="number" step="0.01" name="grand_total" id="grand_total" class="form-control text-end border-0 bg-transparent fs-5 fw-bold" readonly value="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 d-grid gap-2">
                        <button type="submit" class="btn btn-primary-modern py-3"><i class="fas fa-save me-2"></i> Save Quote</button>
                        <a href="{{ route('quotes.index') }}" class="btn btn-link text-muted">Cancel and Go Back</a>
                    </div>
                </x-card>
            </div>
        </div>
    </form>

    {{-- ADD CUSTOMER MODAL --}}
    <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addCustomerModalLabel"><i class="fas fa-user-plus me-2"></i>Add New Customer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="customerModalAlert" class="d-none mb-3"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="c_name" class="form-control" placeholder="Full Name">
                                <label>Full Name *</label>
                                <div class="invalid-feedback" id="c_name_err"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="c_company_name" class="form-control" placeholder="Company Name">
                                <label>Company Name</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Company Phone</label>
                            <div class="input-group">
                                <select id="c_company_country_code" class="form-select flex-grow-0" style="width: 130px; max-width: 40%;">
                                    @foreach(\App\Models\Customer::allCountries() as $iso => $country)
                                        <option value="{{ $country['code'] }}" {{ $country['code'] === '+966' ? 'selected' : '' }}>
                                            {{ $country['code'] }} ({{ $country['name'] }})
                                        </option>
                                    @endforeach
                                </select>
                                <input type="tel" id="c_company_phone" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" id="c_email" class="form-control" placeholder="Email">
                                <label>Email</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Phone Number *</label>
                            <x-country-phone-picker id="c_phone" name="c_phone" codeName="c_country_code" />
                            <div class="invalid-feedback d-block" id="c_phone_err"></div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="text" id="c_address" class="form-control" placeholder="Address">
                                <label>Address</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" id="c_city" class="form-control" placeholder="City">
                                <label>City</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" id="c_zip" class="form-control" placeholder="ZIP">
                                <label>ZIP / Postal Code</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" id="c_country" class="form-control" placeholder="Country">
                                <label>Country</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="c_tax_id" class="form-control" placeholder="Tax ID">
                                <label>Tax ID</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" id="c_vat_no" class="form-control" placeholder="VAT No.">
                                <label>VAT No.</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveCustomerBtn">
                        <span id="saveCustomerSpinner" class="spinner-border spinner-border-sm me-1 d-none"></span>
                        <i class="fas fa-save me-1" id="saveCustomerIcon"></i> Save Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

<template id="item-template">
    <tr class="item-row align-middle">
        <td><textarea name="items[INDEX][description]" class="form-control bg-white border p-2 fs-6 shadow-sm" rows="2" style="resize: vertical; min-height: 80px;" placeholder="Enter item description" required></textarea></td>
        <td>
            <button type="button" class="btn btn-outline-secondary item-image-btn mb-2">Add Image</button>
            <input type="file" name="items[INDEX][image]" class="item-image-input d-none" accept="image/jpeg,image/jpg,image/png,image/gif">
            <div class="item-image-preview row g-2"></div>
        </td>
        <td><input type="text" name="items[INDEX][item_text]" class="form-control bg-white border p-2 fs-6 shadow-sm" placeholder="Size / Meter"></td>
        <td><input type="number" step="0.01" name="items[INDEX][unit_price]" class="form-control p-2 fs-6 shadow-sm unit-price text-end rounded-2" value="0.00" required></td>
        <td><input type="number" name="items[INDEX][quantity]" class="form-control p-2 fs-6 shadow-sm quantity text-center rounded-2" value="1" required></td>
        <td><input type="number" step="0.01" name="items[INDEX][tax_percentage]" class="form-control p-2 fs-6 shadow-sm tax-percentage text-center rounded-2" value="0.00"></td>
        <td><input type="number" step="0.01" name="items[INDEX][discount]" class="form-control p-2 fs-6 shadow-sm discount text-end rounded-2" value="0.00"></td>
        <td><input type="number" step="0.01" name="items[INDEX][total]" class="form-control p-2 fs-6 shadow-sm line-total border-0 bg-transparent text-end fw-semibold" readonly value="0.00"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger remove-item p-0"><i class="fas fa-trash-alt"></i></button></td>
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
                listEl.innerHTML = '<div class="cust-empty"><i class="fas fa-search me-1"></i>No customers found</div>';
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

        document.getElementById('quote-form').addEventListener('submit', function(e) {
            if (!realSelect.value) {
                e.preventDefault();
                trigger.classList.add('is-invalid');
                openPanel();
            }
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

        window.addCustomerToDropdown = function(id, name, company, phone, display, companyId, vat, cr, address) {
            display = display || name;
            companyId = companyId || '';
            customers.push({ 
                id: String(id), 
                companyId: String(companyId), 
                name: name, 
                company: company || '', 
                phone: phone || '', 
                vat: vat || '', 
                cr: cr || '', 
                address: address || '', 
                display: display 
            });
            var opt = document.createElement('option');
            opt.value = id; 
            opt.text = display; 
            opt.dataset.companyId = companyId;
            opt.dataset.name = name;
            opt.dataset.company = company || '';
            opt.dataset.phone = phone || '';
            opt.dataset.vat = vat || '';
            opt.dataset.cr = cr || '';
            opt.dataset.address = address || '';
            opt.dataset.display = display;
            realSelect.appendChild(opt);
            selectCustomer(String(id), String(companyId), display, vat, cr, address);
        };

        renderList('');
    })();

    /* Quote Items */
    var rowIndex = 0;
    var itemRows = document.getElementById('item-rows');
    var template = document.getElementById('item-template').innerHTML;

    function addRow() {
        var tr = document.createElement('tr');
        tr.className = 'item-row align-middle';
        tr.innerHTML = template.replace(/INDEX/g, rowIndex++);
        var taxInput = tr.querySelector('.tax-percentage');
        if (taxInput) taxInput.value = '15.00';
        itemRows.appendChild(tr);
        attachImageHandlers(tr);
        calculate();
    }

    function calculate() {
        var subtotal = 0, totalTax = 0, totalDiscount = 0;
        var shipping = parseFloat(document.getElementById('shipping').value) || 0;
        document.querySelectorAll('.item-row').forEach(function(row) {
            var price = parseFloat(row.querySelector('.unit-price').value) || 0;
            var qty   = parseFloat(row.querySelector('.quantity').value) || 0;
            var taxP  = parseFloat(row.querySelector('.tax-percentage').value) || 0;
            var disc  = parseFloat(row.querySelector('.discount').value) || 0;
            var ls = qty * price, lt = ls * (taxP/100);
            row.querySelector('.line-total').value = (ls + lt - disc).toFixed(2);
            subtotal += ls; totalTax += lt; totalDiscount += disc;
        });
        document.getElementById('subtotal').value       = subtotal.toFixed(2);
        document.getElementById('total_tax').value      = totalTax.toFixed(2);
        document.getElementById('total_discount').value = totalDiscount.toFixed(2);
        document.getElementById('grand_total').value    = (subtotal + totalTax - totalDiscount + shipping).toFixed(2);
    }

    function attachImageHandlers(row) {
        var btn = row.querySelector('.item-image-btn');
        var input = row.querySelector('.item-image-input');
        var preview = row.querySelector('.item-image-preview');
        if (!btn||!input||!preview) return;
        btn.addEventListener('click', function(){ input.click(); });
        input.addEventListener('change', function() {
            preview.innerHTML = '';
            var file = this.files[0]; if (!file) return;
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.createElement('img');
                img.src = e.target.result; img.className = 'img-thumbnail p-1 col-12';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }

    document.getElementById('add-item').addEventListener('click', addRow);
    document.getElementById('quote-form').addEventListener('submit', function(e) {
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
            errorMessage = 'Please add at least one item to the quote.';
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

    document.getElementById('quote-form').addEventListener('input', function(e) {
        if (e.target.classList.contains('unit-price') || e.target.classList.contains('quantity') ||
            e.target.classList.contains('tax-percentage') || e.target.classList.contains('discount') ||
            e.target.id === 'shipping') calculate();
    });
    itemRows.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) { e.target.closest('tr').remove(); calculate(); }
    });
    addRow();

    /* Add Customer AJAX */
    document.getElementById('saveCustomerBtn').addEventListener('click', function() {
        var btn = this, spinner = document.getElementById('saveCustomerSpinner'),
            icon = document.getElementById('saveCustomerIcon'),
            alertEl = document.getElementById('customerModalAlert');
        alertEl.className = 'd-none';
        document.querySelectorAll('#addCustomerModal .is-invalid').forEach(function(el){ el.classList.remove('is-invalid'); });
        var name = document.getElementById('c_name').value.trim();
        var phoneEl = document.getElementById('c_phone_input') || document.getElementById('c_phone');
        var codeEl = document.getElementById('c_phone_code') || document.getElementById('c_country_code');
        var countryCode = codeEl ? codeEl.value : '+966';
        var phone = phoneEl ? phoneEl.value.trim() : '';
        var hasErr = false;
        if (!name) {
            document.getElementById('c_name').classList.add('is-invalid');
            document.getElementById('c_name_err').textContent = 'Full name is required.';
            hasErr = true;
        }
        if (!phone) {
            if (phoneEl) phoneEl.classList.add('is-invalid');
            document.getElementById('c_phone_err').textContent = 'Phone number is required.';
            hasErr = true;
        }
        if (hasErr) return;

        btn.disabled = true; spinner.classList.remove('d-none'); icon.classList.add('d-none');
        fetch('{{ route("customers.ajax_store") }}', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
            body: JSON.stringify({
                name: name,
                company_name: document.getElementById('c_company_name').value.trim()||null,
                company_phone: (document.getElementById('c_company_phone') ? document.getElementById('c_company_phone').value.trim() : null) || null,
                company_country_code: (document.getElementById('c_company_country_code') ? document.getElementById('c_company_country_code').value : '+966'),
                email: document.getElementById('c_email').value.trim()||null,
                country_code: countryCode,
                phone: phone,
                address: document.getElementById('c_address').value.trim()||null,
                city: document.getElementById('c_city').value.trim()||null,
                zip: document.getElementById('c_zip').value.trim()||null,
                country: document.getElementById('c_country').value.trim()||null,
                tax_id: document.getElementById('c_tax_id').value.trim()||null,
                vat_no: document.getElementById('c_vat_no').value.trim()||null,
                is_active: true
            })
        })
        .then(function(r){ return r.json(); })
        .then(function(data) {
            if (data.success) {
                window.addCustomerToDropdown(
                    data.customer.id, 
                    data.customer.name, 
                    data.customer.company_name||'', 
                    data.customer.phone||'', 
                    data.customer.display||data.customer.name,
                    data.customer.company_id||'',
                    data.customer.vat_no||'',
                    data.customer.cr_no||'',
                    data.customer.address||''
                );
                // Close modal immediately
                bootstrap.Modal.getInstance(document.getElementById('addCustomerModal')).hide();
                // Show same Bootstrap toast as the rest of the app
                showToast('Customer <strong>' + data.customer.name + '</strong> added and selected!', 'success');
                ['c_name','c_company_name','c_email','c_phone','c_address','c_city','c_zip','c_country','c_tax_id','c_vat_no','c_company_phone']
                    .forEach(function(id){ document.getElementById(id).value=''; });
            } else if (data.errors) {
                alertEl.className = 'alert alert-danger rounded-3';
                alertEl.innerHTML = Object.values(data.errors).flat().join('<br>');
                if (data.errors.phone) {
                    if (phoneEl) phoneEl.classList.add('is-invalid');
                    document.getElementById('c_phone_err').textContent = data.errors.phone[0];
                }
                if (data.errors.name) {
                    document.getElementById('c_name').classList.add('is-invalid');
                    document.getElementById('c_name_err').textContent = data.errors.name[0];
                }
            }
        })
        .catch(function(){
            alertEl.className = 'alert alert-danger rounded-3';
            alertEl.innerHTML = 'An unexpected error occurred. Please try again.';
        })
        .finally(function(){ btn.disabled=false; spinner.classList.add('d-none'); icon.classList.remove('d-none'); });
    });

    document.getElementById('addCustomerModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('customerModalAlert').className = 'd-none';
        document.querySelectorAll('#addCustomerModal .is-invalid').forEach(function(el){ el.classList.remove('is-invalid'); });
        ['c_name','c_company_name','c_email','c_phone_input','c_phone','c_address','c_city','c_zip','c_country','c_tax_id','c_vat_no','c_company_phone']
            .forEach(function(id){ if(document.getElementById(id)) document.getElementById(id).value=''; });
        if (window.setCountry_c_phone) window.setCountry_c_phone('+966', 'sa');
        if (document.getElementById('c_phone_err')) document.getElementById('c_phone_err').textContent = '';
        if (document.getElementById('c_name_err')) document.getElementById('c_name_err').textContent = '';
    });
    </script>
    @endsection
@endsection



