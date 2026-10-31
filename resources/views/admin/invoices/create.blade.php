@extends('admin.layouts.app')

@section('title', 'Create New Invoice')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">Create Invoice</h2>
    <div class="d-flex align-items-center bg-white px-3 py-2 rounded-3 shadow-sm border">
        <span class="text-muted me-2 small fw-medium">Invoice Number:</span>
        <span class="fw-bold text-primary">{{ $invoiceNumber }}</span>
    </div>
</div>

<form action="{{ route('invoices.store') }}" method="POST" id="invoice-form" novalidate>
    @csrf
    <input type="hidden" name="invoice_number" value="{{ $invoiceNumber }}">
    
    <div class="row g-4">
        <div class="col-lg-12">
            <x-card title="Invoice Details">
                <div class="row">
                    <div class="col-md-4">
                        <input type="hidden" name="customer_company_id" id="customer_company_id" value="{{ old('customer_company_id') }}">
                        <select name="customer_id" id="customer_id" class="cust-select-hidden" required>
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
            <x-card title="Invoice Items" :padding="false">
                <x-table>
                    <x-slot name="thead">
                        <th style="width: 25%">Description</th>
                        <th style="width: 15%">Size and meter</th>
                        <th style="width: 15%">Unit Price</th>
                        <th style="width: 10%">Qty</th>
                        <th style="width: 10%">Tax %</th>
                        <th style="width: 10%">Discount</th>
                        <th style="width: 10%">Total</th>
                        <th style="width: 5%" class="text-center"></th>
                    </x-slot>

                    <tbody id="item-rows">
                        <!-- Items will be added here via JS -->
                    </tbody>
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
                    <label class="form-label text-muted fw-medium small">Payment Terms & Conditions</label>
                    <textarea name="terms" class="form-control" rows="3">1. Payment is due within 15 days.
2. Please include invoice number on your check.</textarea>
                </div>
                
                <div class="mb-0">
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
                            <span class="input-group-text border-0 bg-transparent">$</span>
                            <input type="number" step="0.01" name="subtotal" id="subtotal" class="form-control text-end border-0 bg-transparent fw-semibold" readonly value="0.00">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Total Tax</span>
                        <div class="input-group input-group-sm w-50">
                            <span class="input-group-text border-0 bg-transparent">$</span>
                            <input type="number" step="0.01" name="total_tax" id="total_tax" class="form-control text-end border-0 bg-transparent fw-semibold" readonly value="0.00">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Total Discount</span>
                        <div class="input-group input-group-sm w-50">
                            <span class="input-group-text border-0 bg-transparent">-$</span>
                            <input type="number" step="0.01" name="total_discount" id="total_discount" class="form-control text-end border-0 bg-transparent fw-semibold" readonly value="0.00">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <span class="text-muted">Shipping</span>
                        <div class="input-group input-group-sm w-50">
                            <span class="input-group-text border-0 bg-light">$</span>
                            <input type="number" step="0.01" name="shipping" id="shipping" class="form-control text-end border-1 rounded-2" value="0.00">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 p-3 bg-light rounded-3">
                        <span class="fw-bold text-dark fs-5">Grand Total</span>
                        <div class="input-group w-50">
                            <span class="input-group-text border-0 bg-transparent fs-5 fw-bold">$</span>
                            <input type="number" step="0.01" name="grand_total" id="grand_total" class="form-control text-end border-0 bg-transparent fs-5 fw-bold" readonly value="0.00">
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-grid gap-2">
                    <button type="submit" class="btn btn-primary-modern py-3">
                        <i class="fas fa-save me-2"></i> Save Invoice
                    </button>
                    <a href="{{ route('invoices.index') }}" class="btn btn-link text-muted">Cancel and Go Back</a>
                </div>
            </x-card>
        </div>
    </div>
</form>

<template id="item-template">
    <tr class="item-row">
        <td><textarea name="items[INDEX][description]" class="form-control bg-white border p-2 fs-6 shadow-sm" rows="2" style="resize: vertical; min-height: 80px;" placeholder="Enter item description" required></textarea></td>
        <td><input type="text" name="items[INDEX][item_text]" class="form-control bg-white border p-2 fs-6 shadow-sm" placeholder="Size / Meter"></td>
        <td><input type="number" step="0.01" name="items[INDEX][unit_price]" class="form-control p-2 fs-6 shadow-sm unit-price text-end rounded-2" value="0.00" required></td>
        <td><input type="number" name="items[INDEX][quantity]" class="form-control p-2 fs-6 shadow-sm quantity text-center rounded-2" value="1" required></td>
        <td><input type="number" step="0.01" name="items[INDEX][tax_percentage]" class="form-control p-2 fs-6 shadow-sm tax-percentage text-center rounded-2" value="0.00"></td>
        <td><input type="number" step="0.01" name="items[INDEX][discount]" class="form-control p-2 fs-6 shadow-sm discount text-end rounded-2" value="0.00"></td>
        <td><input type="number" step="0.01" name="items[INDEX][total]" class="form-control p-2 fs-6 shadow-sm line-total border-0 bg-transparent text-end fw-semibold" readonly value="0.00"></td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-link text-danger remove-item p-0">
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
</script>
@endsection
@endsection



