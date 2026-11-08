@extends('admin.layouts.app')

@section('title', __('Add Item'))

@section('content')
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('products.index') }}" class="btn btn-light btn-sm rounded-circle shadow-sm me-3"
            style="width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;"
            title="{{ __('Back to Products') }}">
            <i class="fas fa-arrow-left text-muted"></i>
        </a>
        <div>
            <h3 class="fw-bold text-dark mb-0">{{ __('Add New Product / Service') }}</h3>
        </div>
    </div>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf

        <!-- Basic & Categorization Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-info-circle text-primary me-2"></i>
                    {{ __('General Information') }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">{{ __('Item Name') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="{{ __('e.g. Acrylic Board 5mm / Business Cards') }}"
                            required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark">{{ __('SKU (Stock Keeping Unit)') }}</label>
                        <div class="input-group">
                            <input type="text" name="sku" id="product_sku"
                                class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku') }}"
                                placeholder="e.g. PRD-849201">
                            <button class="btn btn-outline-primary d-flex align-items-center" type="button"
                                id="btn-generate-sku" onclick="if(window.generateProductSku){window.generateProductSku();}return false;"
                                title="{{ __('Auto Generate SKU') }}">
                                <i class="fas fa-magic me-1" id="sku-magic-icon"></i>
                                <span>{{ __('Generate') }}</span>
                            </button>
                        </div>
                        <script>
                            window.generateProductSku = function() {
                                var skuInput = document.getElementById('product_sku');
                                var magicIcon = document.getElementById('sku-magic-icon');
                                var typeSelect = document.getElementById('product_type');
                                if (!skuInput) return;
                                if (magicIcon) magicIcon.classList.add('fa-spin');
                                var pType = typeSelect ? typeSelect.value : 'product';
                                var prefix = (pType === 'service') ? 'SRV' : 'PRD';
                                var fallback = prefix + '-' + Math.floor(100000 + Math.random() * 900000);
                                var url = '{{ route("products.generate_sku") }}' + '?type=' + encodeURIComponent(pType);
                                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                                    .then(function(res) { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
                                    .then(function(data) {
                                        skuInput.value = (data && data.sku) ? data.sku : fallback;
                                        skuInput.classList.remove('is-invalid');
                                        skuInput.classList.add('is-valid');
                                        setTimeout(function() { skuInput.classList.remove('is-valid'); }, 1200);
                                    })
                                    .catch(function(err) {
                                        console.warn('SKU fetch fallback:', err);
                                        skuInput.value = fallback;
                                        skuInput.classList.remove('is-invalid');
                                    })
                                    .finally(function() {
                                        if (magicIcon) magicIcon.classList.remove('fa-spin');
                                    });
                            };
                            document.addEventListener('DOMContentLoaded', function() {
                                var btn = document.getElementById('btn-generate-sku');
                                if (btn) {
                                    btn.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        window.generateProductSku();
                                    });
                                }
                            });
                        </script>
                        @error('sku')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <small
                            class="text-muted">{{ __('Leave blank to auto-generate upon saving, or click Generate.') }}</small>
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold text-dark mb-0">{{ __('Category') }}</label>
                            <a href="javascript:void(0)" class="small text-decoration-none" data-bs-toggle="modal"
                                data-bs-target="#quickCategoryModal">
                                <i class="fas fa-plus-circle me-1"></i>{{ __('Quick Add') }}
                            </a>
                        </div>
                        <select name="category_id" id="category_select"
                            class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">-- {{ __('Select Category') }} --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->localized_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold text-dark mb-0">{{ __('Unit of Measure') }}</label>
                            <a href="javascript:void(0)" class="small text-decoration-none" data-bs-toggle="modal"
                                data-bs-target="#quickUnitModal">
                                <i class="fas fa-plus-circle me-1"></i>{{ __('Quick Add') }}
                            </a>
                        </div>
                        <select name="unit_id" id="unit_select" class="form-select @error('unit_id') is-invalid @enderror">
                            <option value="">-- {{ __('Select Unit') }} --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->localized_name }} @if($unit->code) ({{ $unit->code }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">{{ __('Type') }} <span
                                class="text-danger">*</span></label>
                        <select name="type" id="product_type" class="form-select @error('type') is-invalid @enderror">
                            <option value="product" {{ old('type', 'product') == 'product' ? 'selected' : '' }}>
                                {{ __('Physical Product (Track Stock)') }}</option>
                            <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>
                                {{ __('Service / Labor (No Stock)') }}</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold text-dark">{{ __('Description') }}</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                            rows="3"
                            placeholder="{{ __('Detailed description or specifications...') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing & Inventory Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-coins text-warning me-2"></i>
                    {{ __('Pricing & Inventory Management') }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">{{ __('Purchase Price (Cost)') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fw-bold">SAR</span>
                            <input type="number" step="0.01" min="0" name="purchase_price"
                                class="form-control @error('purchase_price') is-invalid @enderror"
                                value="{{ old('purchase_price', '0.00') }}" placeholder="0.00">
                        </div>
                        @error('purchase_price')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">{{ __('What you paid to purchase / produce this item.') }}</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">{{ __('Sale Price') }} <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fw-bold">SAR</span>
                            <input type="number" step="0.01" min="0" name="sale_price" id="sale_price_input"
                                class="form-control @error('sale_price') is-invalid @enderror"
                                value="{{ old('sale_price', old('price', '0.00')) }}" placeholder="0.00" required>
                        </div>
                        @error('sale_price')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">{{ __('Customer selling price.') }}</small>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark">{{ __('VAT Tax (%)') }}</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" max="100" name="tax_percentage"
                                class="form-control @error('tax_percentage') is-invalid @enderror"
                                value="{{ old('tax_percentage', 15) }}">
                            <span class="input-group-text bg-light text-muted fw-bold">%</span>
                        </div>
                        @error('tax_percentage')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Stock Section -->
                    <div class="col-12 mt-4" id="stock_management_section">
                        <div class="p-3 rounded-3 bg-light-subtle border">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h6 class="fw-bold text-dark mb-0">
                                    <i class="fas fa-boxes text-info me-2"></i>
                                    {{ __('Stock Quantities & Automatic 30% Low Stock Alert') }}
                                </h6>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="fas fa-bell me-1"></i> {{ __('Auto Email at 30% Remaining') }}
                                </span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label
                                        class="form-label fw-semibold text-dark">{{ __('Initial Stock / Total Items Received') }}</label>
                                    <input type="number" min="0" name="initial_stock" id="initial_stock_input"
                                        class="form-control @error('initial_stock') is-invalid @enderror"
                                        value="{{ old('initial_stock', '0') }}">
                                    @error('initial_stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small
                                        class="text-muted">{{ __('Total starting quantity added to inventory.') }}</small>
                                </div>

                                <div class="col-md-6">
                                    <label
                                        class="form-label fw-semibold text-dark">{{ __('Current Stock Remaining') }}</label>
                                    <input type="number" min="0" name="stock" id="current_stock_input"
                                        class="form-control @error('stock') is-invalid @enderror"
                                        value="{{ old('stock', '0') }}">
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">{{ __('Available items currently in stock.') }}</small>
                                </div>
                            </div>

                            <div class="alert alert-info border-0 mt-3 mb-0 py-2 small d-flex align-items-center">
                                <i class="fas fa-info-circle fs-5 me-2 text-info"></i>
                                <div>
                                    {{ __('When 70% of the total quantity is sold and 30% or less remains in stock, an automated email notification will be sent directly to the shop owner.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Images Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-images text-success me-2"></i>
                    {{ __('Product Images') }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Primary Image -->
                    <div class="col-md-6 border-end-md">
                        <label class="form-label fw-semibold text-dark">{{ __('Primary / Main Image') }}</label>
                        <p class="text-muted small mb-2">
                            {{ __('Single main photo shown on invoices, job orders, and catalog.') }}</p>

                        <div class="d-flex align-items-center gap-3">
                            <div class="image-preview-wrapper border rounded-3 p-1 bg-light text-center"
                                style="width: 110px; height: 110px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <img id="primary_preview" src="" alt="Preview"
                                    style="max-width: 100%; max-height: 100%; object-fit: contain; display: none;">
                                <div id="primary_placeholder" class="text-muted small">
                                    <i class="fas fa-image fs-3 d-block mb-1 opacity-50"></i>
                                    <span>{{ __('No Image') }}</span>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" name="image" id="primary_image_input"
                                    class="form-control @error('image') is-invalid @enderror" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-1">{{ __('JPEG, PNG, WEBP up to 10MB.') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Multiple Gallery Images -->
                    <div class="col-md-6">
                        <label
                            class="form-label fw-semibold text-dark">{{ __('Additional Gallery Images (Multiple)') }}</label>
                        <p class="text-muted small mb-2">
                            {{ __('Select multiple extra photos (angles, samples, details).') }}</p>

                        <input type="file" name="images[]" id="gallery_images_input"
                            class="form-control @error('images.*') is-invalid @enderror" accept="image/*" multiple>
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">{{ __('Hold Ctrl/Cmd to select multiple images.') }}</small>

                        <!-- Gallery Live Previews -->
                        <div id="gallery_previews" class="d-flex flex-wrap gap-2 mt-3"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="d-flex justify-content-end gap-3 mb-5">
            <a href="{{ route('products.index') }}" class="btn btn-secondary px-4">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary-modern px-5 py-2">
                <i class="fas fa-save me-2"></i> {{ __('Save Product') }}
            </button>
        </div>
    </form>

    <!-- Quick Add Category Modal -->
    <div class="modal fade" id="quickCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fas fa-tag text-primary me-2"></i>
                        {{ __('Quick Add Category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Category Name') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" id="quick_cat_name" class="form-control"
                            placeholder="{{ __('e.g. Banners, Printing, Stationery') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Category Code') }}</label>
                        <input type="text" id="quick_cat_code" class="form-control text-uppercase"
                            placeholder="{{ __('e.g. CAT-PRN or SGN') }}">
                        <small class="text-muted" style="font-size: 0.75rem;">{{ __('Leave blank to auto-generate.') }}</small>
                    </div>
                    <div id="quick_cat_error" class="alert alert-danger py-2 small d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="btn-save-quick-category">
                        <i class="fas fa-check me-1"></i> {{ __('Add Category') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Add Unit Modal -->
    <div class="modal fade" id="quickUnitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fas fa-ruler-combined text-info me-2"></i>
                        {{ __('Quick Add Unit') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Unit Name') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" id="quick_unit_name" class="form-control"
                            placeholder="{{ __('e.g. Bundle, Pack, Meter') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Code / Symbol') }}</label>
                        <input type="text" id="quick_unit_code" class="form-control" placeholder="e.g. bdl, pck">
                    </div>
                    <div id="quick_unit_error" class="alert alert-danger py-2 small d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-info text-white" id="btn-save-quick-unit">
                        <i class="fas fa-check me-1"></i> {{ __('Add Unit') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
            // 1. SKU Generator (Global and Instant Fallback)
            window.generateProductSku = function () {
                const skuInput = document.getElementById('product_sku');
                const magicIcon = document.getElementById('sku-magic-icon');
                const typeSelect = document.getElementById('product_type');

                if (!skuInput) return;

                if (magicIcon) magicIcon.classList.add('fa-spin');

                const pType = typeSelect ? typeSelect.value : 'product';
                const prefix = (pType === 'service') ? 'SRV' : 'PRD';
                const fallback = prefix + '-' + Math.floor(100000 + Math.random() * 900000);

                const url = '{{ route("products.generate_sku") }}' + '?type=' + encodeURIComponent(pType);

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                    .then(res => {
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        return res.json();
                    })
                    .then(data => {
                        if (data && data.sku) {
                            skuInput.value = data.sku;
                        } else {
                            skuInput.value = fallback;
                        }
                        skuInput.classList.remove('is-invalid');
                        skuInput.classList.add('is-valid');
                        setTimeout(() => skuInput.classList.remove('is-valid'), 1200);
                    })
                    .catch(err => {
                        console.warn('SKU fetch failed, applied instant fallback:', err);
                        skuInput.value = fallback;
                        skuInput.classList.remove('is-invalid');
                        skuInput.classList.add('is-valid');
                        setTimeout(() => skuInput.classList.remove('is-valid'), 1200);
                    })
                    .finally(() => {
                        if (magicIcon) magicIcon.classList.remove('fa-spin');
                    });
            };

            document.addEventListener('DOMContentLoaded', function () {

                // 2. Product Type toggle (service hides stock)
                const productType = document.getElementById('product_type');
                const stockSection = document.getElementById('stock_management_section');
                function toggleStockSection() {
                    if (productType && stockSection) {
                        if (productType.value === 'service') {
                            stockSection.style.display = 'none';
                        } else {
                            stockSection.style.display = 'block';
                        }
                    }
                }
                if (productType) {
                    productType.addEventListener('change', toggleStockSection);
                    toggleStockSection();
                }

                // 3. Auto-sync initial stock to current stock if current stock is untouched
                const initialStockInput = document.getElementById('initial_stock_input');
                const currentStockInput = document.getElementById('current_stock_input');
                if (initialStockInput && currentStockInput) {
                    initialStockInput.addEventListener('input', function () {
                        if (currentStockInput.value === '0' || currentStockInput.value === '') {
                            currentStockInput.value = this.value;
                        }
                    });
                }

                // 4. Primary Image Preview
                const primaryImgInput = document.getElementById('primary_image_input');
                const primaryPreview = document.getElementById('primary_preview');
                const primaryPlaceholder = document.getElementById('primary_placeholder');
                if (primaryImgInput && primaryPreview) {
                    primaryImgInput.addEventListener('change', function (e) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = function (evt) {
                                primaryPreview.src = evt.target.result;
                                primaryPreview.style.display = 'block';
                                if (primaryPlaceholder) primaryPlaceholder.style.display = 'none';
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }

                // 5. Additional Gallery Images Live Preview
                const galleryInput = document.getElementById('gallery_images_input');
                const galleryPreviews = document.getElementById('gallery_previews');
                if (galleryInput && galleryPreviews) {
                    galleryInput.addEventListener('change', function (e) {
                        galleryPreviews.innerHTML = '';
                        Array.from(e.target.files).forEach((file, idx) => {
                            const reader = new FileReader();
                            reader.onload = function (evt) {
                                const box = document.createElement('div');
                                box.className = 'border rounded-2 p-1 bg-light position-relative';
                                box.style.width = '70px';
                                box.style.height = '70px';
                                box.style.display = 'flex';
                                box.style.alignItems = 'center';
                                box.style.justifyContent = 'center';
                                box.style.overflow = 'hidden';

                                const img = document.createElement('img');
                                img.src = evt.target.result;
                                img.style.maxWidth = '100%';
                                img.style.maxHeight = '100%';
                                img.style.objectFit = 'contain';

                                box.appendChild(img);
                                galleryPreviews.appendChild(box);
                            };
                            reader.readAsDataURL(file);
                        });
                    });
                }

                // 6. Quick Add Category AJAX
                const btnSaveQuickCat = document.getElementById('btn-save-quick-category');
                if (btnSaveQuickCat) {
                    btnSaveQuickCat.addEventListener('click', function () {
                        const name = document.getElementById('quick_cat_name').value.trim();
                        const codeInput = document.getElementById('quick_cat_code');
                        const code = codeInput ? codeInput.value.trim() : '';
                        const errBox = document.getElementById('quick_cat_error');

                        if (!name) {
                            errBox.textContent = '{{ __("Please enter a category name.") }}';
                            errBox.classList.remove('d-none');
                            return;
                        }

                        btnSaveQuickCat.disabled = true;
                        fetch('{{ route("categories.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ name: name, code: code, status: 1 })
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success && data.category) {
                                    const sel = document.getElementById('category_select');
                                    const opt = document.createElement('option');
                                    opt.value = data.category.id;
                                    opt.textContent = data.category.name;
                                    opt.selected = true;
                                    sel.appendChild(opt);

                                    const modalEl = document.getElementById('quickCategoryModal');
                                    const modal = bootstrap.Modal.getInstance(modalEl);
                                    if (modal) modal.hide();

                                    document.getElementById('quick_cat_name').value = '';
                                    if (codeInput) codeInput.value = '';
                                    errBox.classList.add('d-none');
                                } else {
                                    errBox.textContent = data.message || 'Error creating category';
                                    errBox.classList.remove('d-none');
                                }
                            })
                            .catch(err => {
                                errBox.textContent = 'Server error occurred.';
                                errBox.classList.remove('d-none');
                            })
                            .finally(() => { btnSaveQuickCat.disabled = false; });
                    });
                }

                // 7. Quick Add Unit AJAX
                const btnSaveQuickUnit = document.getElementById('btn-save-quick-unit');
                if (btnSaveQuickUnit) {
                    btnSaveQuickUnit.addEventListener('click', function () {
                        const name = document.getElementById('quick_unit_name').value.trim();
                        const code = document.getElementById('quick_unit_code').value.trim();
                        const errBox = document.getElementById('quick_unit_error');

                        if (!name) {
                            errBox.textContent = '{{ __("Please enter a unit name.") }}';
                            errBox.classList.remove('d-none');
                            return;
                        }

                        btnSaveQuickUnit.disabled = true;
                        fetch('{{ route("units.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({ name: name, code: code, status: 1 })
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success && data.unit) {
                                    const sel = document.getElementById('unit_select');
                                    const opt = document.createElement('option');
                                    opt.value = data.unit.id;
                                    let label = data.unit.name;
                                    if (data.unit.code) label += ` (${data.unit.code})`;
                                    opt.textContent = label;
                                    opt.selected = true;
                                    sel.appendChild(opt);

                                    const modalEl = document.getElementById('quickUnitModal');
                                    const modal = bootstrap.Modal.getInstance(modalEl);
                                    if (modal) modal.hide();

                                    document.getElementById('quick_unit_name').value = '';
                                    document.getElementById('quick_unit_code').value = '';
                                    errBox.classList.add('d-none');
                                } else {
                                    errBox.textContent = data.message || 'Error creating unit';
                                    errBox.classList.remove('d-none');
                                }
                            })
                            .catch(err => {
                                errBox.textContent = 'Server error occurred.';
                                errBox.classList.remove('d-none');
                            })
                            .finally(() => { btnSaveQuickUnit.disabled = false; });
                    });
                }
            });
    </script>
@endpush