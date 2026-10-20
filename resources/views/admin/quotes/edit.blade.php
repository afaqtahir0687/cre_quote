@extends('admin.layouts.app')

@section('title', 'Edit Quote')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('quotes.index') }}" class="btn btn-light btn-sm rounded-circle me-3 shadow-sm">
                <i class="fas fa-arrow-left text-muted"></i>
            </a>
            <h2 class="fw-bold text-dark mb-0">Edit Quote</h2>
            <span class="badge-soft badge-soft-primary ms-3">{{ $quote->quote_number }}</span>
        </div>
    </div>

    <form action="{{ route('quotes.update', $quote) }}" method="POST" id="quote-form" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        <input type="hidden" name="quote_number" value="{{ $quote->quote_number }}">

        <div class="row g-4">
            <div class="col-lg-12">
                <x-card title="Quote Details">
                    <div class="row">
                        <div class="col-md-4">
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
                                            {{ old('customer_id', $quote->customer_id) == $customer->id ? 'selected' : '' }}>{{ $displayStr }}</option>
                                @endforeach
                            </select>
                            <div class="cust-dropdown" id="cust-dropdown">
                                <div class="form-floating">
                                    @php
                                        $currCustId = old('customer_id', $quote->customer_id);
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
                        <div class="col-md-4">
                            <x-form-input label="Quote Date *" name="quote_date" type="date" :value="old('quote_date', $quote->quote_date ? \Carbon\Carbon::parse($quote->quote_date)->format('Y-m-d') : '')"
                                required />
                        </div>
                        <div class="col-md-4">
                            <x-form-input label="Due Date" name="due_date" type="date" :value="old('due_date', $quote->due_date ? \Carbon\Carbon::parse($quote->due_date)->format('Y-m-d') : '')" />
                        </div>
                        <div class="col-md-6">
                            <x-form-input label="Reference" name="reference" :value="$quote->reference" />
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <select name="status" id="status" class="form-select">
                                    <option value="Draft" {{ $quote->status == 'Draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="Sent" {{ $quote->status == 'Sent' ? 'selected' : '' }}>Sent</option>
                                    <option value="Accepted" {{ $quote->status == 'Accepted' ? 'selected' : '' }}>Accepted
                                    </option>
                                    <option value="Declined" {{ $quote->status == 'Declined' ? 'selected' : '' }}>Declined
                                    </option>
                                    <option value="Invoiced" {{ $quote->status == 'Invoiced' ? 'selected' : '' }}>Invoiced
                                    </option>
                                </select>
                                <label for="status">Status</label>
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>

            <div class="col-lg-12">
                <x-card title="Quote Items" :padding="false">
                    <x-table>
                        <x-slot name="thead">
                            <th style="width: 25%">Description</th>
                            <th style="width: 8%">Images</th>
                            <th style="width: 12%">Size and meter</th>
                            <th style="width: 12%">Unit Price</th>
                            <th style="width: 8%">Qty</th>
                            <th style="width: 10%">Tax %</th>
                            <th style="width: 10%">Discount</th>
                            <th style="width: 10%">Total</th>
                            <th style="width: 5%" class="text-center"></th>
                        </x-slot>

                        <tbody id="item-rows">
                            @foreach($quote->items as $index => $item)
                                <tr class="item-row align-middle">
                                    <td>
                                        <textarea name="items[{{ $index }}][description]" class="form-control bg-white border p-2 fs-6 shadow-sm" rows="2" style="resize: vertical; min-height: 80px;" required>{{ $item->description }}</textarea>
                                    </td>
                                    <td>
                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                        <input type="hidden" name="items[{{ $index }}][existing_images]" value='{{ json_encode($item->images) }}'>
                                        <input type="hidden" name="items[{{ $index }}][removed_images]" class="removed-images-input" value='[]'>
                                        <button type="button" class="btn btn-outline-secondary item-image-btn mb-2">Add Image</button>
                                        <input type="file" name="items[{{ $index }}][image]" class="item-image-input d-none" accept="image/jpeg,image/jpg,image/png,image/gif">
                                        <div class="item-image-preview row g-2">
                                            @if(!empty($item->images))
                                                @foreach($item->images as $img)
                                                    <div class="col-md-12 position-relative" data-image-path="{{ $img }}">
                                                        <img src="{{ asset($img) }}" class="img-thumbnail p-1 col-12" style="height:150px; object-fit:cover;">
                                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle" onclick="removeExistingImage('{{ $img }}', this)">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $index }}][item_text]" class="form-control bg-white border p-2 fs-6 shadow-sm" value="{{ $item->item_text ?? '' }}" placeholder="Size / Meter">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" class="form-control p-2 fs-6 shadow-sm unit-price text-end rounded-2" value="{{ $item->unit_price }}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $index }}][quantity]" class="form-control p-2 fs-6 shadow-sm quantity text-center rounded-2" value="{{ $item->quantity }}" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[{{ $index }}][tax_percentage]" class="form-control p-2 fs-6 shadow-sm tax-percentage text-center rounded-2" value="{{ $item->tax_percentage }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[{{ $index }}][discount]" class="form-control p-2 fs-6 shadow-sm discount text-end rounded-2" value="{{ $item->discount }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="items[{{ $index }}][total]" class="form-control p-2 fs-6 shadow-sm line-total border-0 bg-transparent text-end fw-semibold" readonly value="{{ $item->total }}">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-link text-danger remove-item p-0">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-table>

                    <div class="p-3 border-top bg-light-subtle rounded-bottom-4">
                        <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center"
                            id="add-item">
                            <i class="fas fa-plus me-2"></i> Add Item Row
                        </button>
                    </div>
                </x-card>
            </div>

            <div class="col-md-7">
                <x-card title="Additional Information">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium small">Payment Terms & Conditions</label>
                        <textarea name="terms" class="form-control" rows="3">{{ $quote->terms }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted fw-medium small">Notes</label>
                        <textarea name="notes" class="form-control" rows="3"
                            placeholder="Additional notes for the customer...">{{ $quote->notes }}</textarea>
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
                                <input type="number" step="0.01" name="subtotal" id="subtotal"
                                    class="form-control text-end border-0 bg-transparent fw-semibold" readonly
                                    value="{{ $quote->subtotal }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Tax</span>
                            <div class="input-group input-group-sm w-50">
                                <span class="input-group-text border-0 bg-transparent">$</span>
                                <input type="number" step="0.01" name="total_tax" id="total_tax"
                                    class="form-control text-end border-0 bg-transparent fw-semibold" readonly
                                    value="{{ $quote->total_tax }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Total Discount</span>
                            <div class="input-group input-group-sm w-50">
                                <span class="input-group-text border-0 bg-transparent">-$</span>
                                <input type="number" step="0.01" name="total_discount" id="total_discount"
                                    class="form-control text-end border-0 bg-transparent fw-semibold" readonly
                                    value="{{ $quote->total_discount }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-muted">Shipping</span>
                            <div class="input-group input-group-sm w-50">
                                <span class="input-group-text border-0 bg-light">$</span>
                                <input type="number" step="0.01" name="shipping" id="shipping"
                                    class="form-control text-end border-1 rounded-2" value="{{ $quote->shipping }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3 p-3 bg-light rounded-3">
                            <span class="fw-bold text-dark fs-5">Grand Total</span>
                            <div class="input-group w-50">
                                <span class="input-group-text border-0 bg-transparent fs-5 fw-bold">$</span>
                                <input type="number" step="0.01" name="grand_total" id="grand_total"
                                    class="form-control text-end border-0 bg-transparent fs-5 fw-bold" readonly
                                    value="{{ $quote->grand_total }}">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-grid gap-2">
                        <button type="submit" class="btn btn-primary-modern py-3">
                            <i class="fas fa-save me-2"></i> Update Quote
                        </button>
                        <a href="{{ route('quotes.index') }}" class="btn btn-link text-muted">Cancel and Go Back</a>
                    </div>
                </x-card>
            </div>
        </div>
    </form>

    <template id="item-template">
        <tr class="item-row align-middle">
            <td>
                <textarea name="items[INDEX][description]" class="form-control bg-white border p-2 fs-6 shadow-sm" rows="2" style="resize: vertical; min-height: 80px;" placeholder="Enter item description" required></textarea>
            </td>
            <td>
                <input type="hidden" name="items[INDEX][existing_images]" value='[]'>
                <input type="hidden" name="items[INDEX][removed_images]" class="removed-images-input" value='[]'>
                <button type="button" class="btn btn-outline-secondary item-image-btn mb-2">Add Image</button>
                <input type="file" name="items[INDEX][image]" class="item-image-input d-none" accept="image/jpeg,image/jpg,image/png,image/gif">
                <div class="item-image-preview row g-2"></div>
            </td>
            <td>
                <input type="text" name="items[INDEX][item_text]" class="form-control bg-white border p-2 fs-6 shadow-sm" placeholder="Size / Meter">
            </td>
            <td>
                <input type="number" step="0.01" name="items[INDEX][unit_price]" class="form-control p-2 fs-6 shadow-sm unit-price text-end rounded-2" value="0.00" required>
            </td>
            <td>
                <input type="number" name="items[INDEX][quantity]" class="form-control p-2 fs-6 shadow-sm quantity text-center rounded-2" value="1" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[INDEX][tax_percentage]" class="form-control p-2 fs-6 shadow-sm tax-percentage text-center rounded-2" value="0.00">
            </td>
            <td>
                <input type="number" step="0.01" name="items[INDEX][discount]" class="form-control p-2 fs-6 shadow-sm discount text-end rounded-2" value="0.00">
            </td>
            <td>
                <input type="number" step="0.01" name="items[INDEX][total]" class="form-control p-2 fs-6 shadow-sm line-total border-0 bg-transparent text-end fw-semibold" readonly value="0.00">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-link text-danger remove-item p-0"><i class="fas fa-trash-alt"></i></button>
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

                document.getElementById('quote-form').addEventListener('submit', function(e) {
                    if (!realSelect.value) {
                        e.preventDefault();
                        trigger.classList.add('is-invalid');
                        openPanel();
                    }
                });

                if (selectedId) {
                    var found = customers.find(function(c){ return c.id == selectedId; });
                    if (found) trigger.value = found.display || found.name;
                }

                renderList('');
            })();

            let rowIndex = {{ $quote->items->count() }};
            const itemRows = document.getElementById('item-rows');
            const template = document.getElementById('item-template').innerHTML;

            function addRow() {
                const tr = document.createElement('tr');
                tr.className = 'item-row align-middle';
                tr.innerHTML = template.replace(/INDEX/g, rowIndex++);

                itemRows.appendChild(tr);
                attachImageHandlers(tr);
                calculate();
            }

            function attachImageHandlers(row) {
                // Ensure we don't attach multiple listeners
                const btn = row.querySelector('.item-image-btn');
                const input = row.querySelector('.item-image-input');
                const preview = row.querySelector('.item-image-preview');
                
                if (!btn || !input || !preview) return;
                
                // Remove existing listeners by replacing the button clone
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);

                newBtn.addEventListener('click', () => input.click());
                input.addEventListener('change', function () {
                    // First remove any previously added new image preview in this session
                    const existingNew = preview.querySelector('.new-preview');
                    if(existingNew) existingNew.remove();

                    const file = this.files[0];
                    if (!file) return;
                    const reader = new FileReader();
                    reader.onload = e => {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-thumbnail p-1 col-12 new-preview mt-2';
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                });
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

            // Attach handlers to existing rows on page load
            document.querySelectorAll('.item-row').forEach(row => attachImageHandlers(row));

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

            document.getElementById('quote-form').addEventListener('input', (e) => {
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

            // Image Upload Functionality
            const imageInput = document.getElementById('image-input');
            const imagePreview = document.getElementById('image-preview');
            const dropZone = document.getElementById('drop-zone');
            let selectedFiles = [];

            imageInput.addEventListener('change', function (e) {
                handleFiles(this.files);
            });

            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.classList.add('border-primary', 'bg-primary-subtle');
            });

            dropZone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-primary', 'bg-primary-subtle');
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.classList.remove('border-primary', 'bg-primary-subtle');
                handleFiles(e.dataTransfer.files);
            });

            function handleFiles(files) {
                const maxFiles = 5;
                const maxSize = 2 * 1024 * 1024;
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

                if (selectedFiles.length + files.length > maxFiles) {
                    alert(`You can only upload a maximum of ${maxFiles} images.`);
                    return;
                }

                Array.from(files).forEach(file => {
                    if (!allowedTypes.includes(file.type)) {
                        alert(`${file.name} is not a valid image type.`);
                        return;
                    }

                    if (file.size > maxSize) {
                        alert(`${file.name} is too large.`);
                        return;
                    }

                    selectedFiles.push(file);
                    displayImagePreview(file);
                });

                updateFileInput();
            }

            function displayImagePreview(file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-3';
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-thumbnail rounded-3" style="height: 150px; width: 100%; object-fit: cover;">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle" 
                                    onclick="removeImage('${file.name}')" style="width: 30px; height: 30px; padding: 0;">
                                <i class="fas fa-times"></i>
                            </button>
                            <p class="text-muted small mt-2 mb-0 text-truncate">${file.name}</p>
                        </div>
                    `;
                    imagePreview.appendChild(col);
                };
                reader.readAsDataURL(file);
            }

            window.removeImage = function (fileName) {
                selectedFiles = selectedFiles.filter(f => f.name !== fileName);
                updateFileInput();
                renderPreviews();
            }

            window.removeExistingImage = function (imagePath, btn) {
                const row = btn.closest('tr');
                const removedInput = row ? row.querySelector('input.removed-images-input') : document.getElementById('existing-images').closest('.mb-0').querySelector('input.removed-images-input');

                let removed = [];
                if (removedInput && removedInput.value) {
                    try { removed = JSON.parse(removedInput.value); } catch (e) { removed = []; }
                }
                removed.push(imagePath);
                if (removedInput) removedInput.value = JSON.stringify(removed);

                const imageElement = btn.closest('[data-image-path]');
                if (imageElement) imageElement.remove();
            }

            function updateFileInput() {
                const dataTransfer = new DataTransfer();
                selectedFiles.forEach(file => dataTransfer.items.add(file));
                imageInput.files = dataTransfer.files;
            }

            function renderPreviews() {
                imagePreview.innerHTML = '';
                selectedFiles.forEach(file => displayImagePreview(file));
            }
        </script>
    @endsection
@endsection



