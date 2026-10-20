@extends('admin.layouts.app')

@section('title', 'Edit Invoice')

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
                    <select name="customer_id" id="customer_id" class="cust-select-hidden" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            @php
                                $displayPhone = $customer->formatted_phone ?: $customer->phone;
                                $displayStr = $customer->name;
                                if (!empty($customer->company_name)) $displayStr .= ' — ' . $customer->company_name;
                                if (!empty($displayPhone)) $displayStr .= ' (' . $displayPhone . ')';
                            @endphp
                            <option value="{{ $customer->id }}" 
                                    data-name="{{ $customer->name }}"
                                    data-company="{{ $customer->company_name ?? '' }}" 
                                    data-phone="{{ $displayPhone ?? '' }}"
                                    data-display="{{ $displayStr }}"
                                    {{ old('customer_id', $invoice->customer_id) == $customer->id ? 'selected' : '' }}>{{ $displayStr }}</option>
                        @endforeach
                    </select>
                    <div class="cust-dropdown" id="cust-dropdown">
                        <div class="form-floating">
                            @php
                                $currCustId = old('customer_id', $invoice->customer_id);
                                $selCust = $currCustId ? $customers->firstWhere('id', $currCustId) : null;
                                $selTrigger = '';
                                if ($selCust) {
                                    $selTrigger = $selCust->name;
                                    if (!empty($selCust->company_name)) $selTrigger .= ' — ' . $selCust->company_name;
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
                <div class="col-md-4 mb-3">
                    <label class="form-label">Invoice Date *</label>
                    <input type="date" name="invoice_date" class="form-control" value="{{ old('invoice_date', $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : '') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : '') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-white"><h5 class="mb-0">Items</h5></div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 25%">Description</th>
                        <th style="width: 15%">Size and meter</th>
                        <th style="width: 15%">Unit Price</th>
                        <th style="width: 10%">Qty</th>
                        <th style="width: 10%">Tax %</th>
                        <th style="width: 10%">Discount</th>
                        <th style="width: 10%">Total</th>
                        <th style="width: 5%"></th>
                    </tr>
                </thead>
                <tbody id="item-rows">
                    @foreach($invoice->items as $index => $item)
                    <tr class="item-row">
                        <td><textarea name="items[{{ $index }}][description]" class="form-control bg-white border p-2 fs-6 shadow-sm" rows="2" style="resize: vertical; min-height: 80px;" required>{{ $item->description }}</textarea></td>
                        <td><input type="text" name="items[{{ $index }}][item_text]" class="form-control" value="{{ $item->item_text }}"></td>
                        <td><input type="number" step="0.01" name="items[{{ $index }}][unit_price]" class="form-control p-2 fs-6 shadow-sm unit-price" value="{{ $item->unit_price }}" required></td>
                        <td><input type="number" name="items[{{ $index }}][quantity]" class="form-control p-2 fs-6 shadow-sm quantity" value="{{ $item->quantity }}" required></td>
                        <td><input type="number" step="0.01" name="items[{{ $index }}][tax_percentage]" class="form-control p-2 fs-6 shadow-sm tax-percentage" value="{{ $item->tax_percentage }}"></td>
                        <td><input type="number" step="0.01" name="items[{{ $index }}][discount]" class="form-control p-2 fs-6 shadow-sm discount" value="{{ $item->discount }}"></td>
                        <td><input type="number" step="0.01" name="items[{{ $index }}][total]" class="form-control p-2 fs-6 shadow-sm line-total border-0 bg-transparent text-end" readonly value="{{ $item->total }}"></td>
                        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-times"></i></button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-3"><button type="button" class="btn btn-sm btn-outline-primary" id="add-item"><i class="fas fa-plus"></i> Add Row</button></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card mb-4">
                <div class="card-body">
                    <label class="form-label">Terms & Conditions</label>
                    <textarea name="terms" class="form-control" rows="3">{{ $invoice->terms }}</textarea>
                    <label class="form-label mt-3">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ $invoice->notes }}</textarea>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td>Subtotal</td><td><input type="number" step="0.01" name="subtotal" id="subtotal" class="form-control text-end border-0" readonly value="{{ $invoice->subtotal }}"></td></tr>
                        <tr><td>Total Tax</td><td><input type="number" step="0.01" name="total_tax" id="total_tax" class="form-control text-end border-0" readonly value="{{ $invoice->total_tax }}"></td></tr>
                        <tr><td>Total Discount</td><td><input type="number" step="0.01" name="total_discount" id="total_discount" class="form-control text-end border-0" readonly value="{{ $invoice->total_discount }}"></td></tr>
                        <tr><td>Shipping</td><td><input type="number" step="0.01" name="shipping" id="shipping" class="form-control text-end" value="{{ $invoice->shipping }}"></td></tr>
                        <tr class="table-light fw-bold"><td>Grand Total</td><td><input type="number" step="0.01" name="grand_total" id="grand_total" class="form-control text-end border-0 fw-bold" readonly value="{{ $invoice->grand_total }}"></td></tr>
                    </table>
                </div>
            </div>
            <div class="text-end mt-4">
                <a href="{{ route('invoices.index') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-lg btn-success px-5">Update Invoice</button>
            </div>
        </div>
    </div>
</form>

<template id="item-template">
    <tr class="item-row">
        <td><textarea name="items[INDEX][description]" class="form-control bg-white border p-2 fs-6 shadow-sm" rows="2" style="resize: vertical; min-height: 80px;" required></textarea></td>
        <td><input type="text" name="items[INDEX][item_text]" class="form-control"></td>
        <td><input type="number" step="0.01" name="items[INDEX][unit_price]" class="form-control p-2 fs-6 shadow-sm unit-price" value="0.00" required></td>
        <td><input type="number" name="items[INDEX][quantity]" class="form-control p-2 fs-6 shadow-sm quantity" value="1" required></td>
        <td><input type="number" step="0.01" name="items[INDEX][tax_percentage]" class="form-control p-2 fs-6 shadow-sm tax-percentage" value="0.00"></td>
        <td><input type="number" step="0.01" name="items[INDEX][discount]" class="form-control p-2 fs-6 shadow-sm discount" value="0.00"></td>
        <td><input type="number" step="0.01" name="items[INDEX][total]" class="form-control p-2 fs-6 shadow-sm line-total border-0 bg-transparent text-end" readonly value="0.00"></td>
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-times"></i></button></td>
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
        var selectedId = realSelect.value || '';

        var customers = [];
        Array.from(realSelect.options).forEach(function(opt) {
            if (!opt.value) return;
            customers.push({ 
                id: opt.value, 
                name: opt.dataset.name || opt.text.trim(), 
                company: opt.dataset.company || '', 
                phone: opt.dataset.phone || '',
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
                return '<div class="cust-option' + (c.id == selectedId ? ' selected' : '') + '" data-id="' + c.id + '" data-display="' + esc(c.display) + '">'
                     + '  <div class="d-flex justify-content-between align-items-center">'
                     + '    <span class="opt-name">' + hl(c.name, filter) + '</span>'
                     + '    ' + phoneBadge
                     + '  </div>'
                     + '  ' + companyHtml
                     + '</div>';
            }).join('');
            listEl.querySelectorAll('.cust-option').forEach(function(el) {
                el.addEventListener('click', function() { selectCustomer(el.dataset.id, el.dataset.display); });
            });
        }

        function selectCustomer(id, display) {
            selectedId = id;
            realSelect.value = id;
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
            var found = customers.find(function(c){ return c.id == selectedId; });
            if (found) trigger.value = found.display || found.name;
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
</script>
@endsection
@endsection




