@extends('admin.layouts.app')

@section('title', __('Products'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">{{ __('Products') }}</h2>
    <div class="d-flex gap-2 align-items-center">
        <form action="{{ route('products.index') }}" method="GET" class="d-flex gap-2 mb-0" id="productsFilterForm">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="{{ __('Search products...') }}" onkeypress="if(event.keyCode == 13) this.form.submit()">
            </div>

            <!-- Searchable Category Dropdown -->
            <div class="dropdown cust-dropdown" id="catFilterDropdown">
                <input type="hidden" name="category_id" id="filter_cat_id" value="{{ request('category_id') }}">
                <button class="form-select bg-white cust-trigger text-start" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" style="min-width: 170px;">
                    <span class="text-truncate">
                        @if(request('category_id') && $categories->firstWhere('id', request('category_id')))
                            {{ tdb($categories->firstWhere('id', request('category_id'))->name) }}
                        @else
                            {{ __('All Categories') }}
                        @endif
                    </span>
                </button>
                <div class="dropdown-menu p-2 shadow border-0" style="min-width: 230px;">
                    <div class="cust-search-wrap mb-2 p-1">
                        <input type="text" class="form-control form-control-sm cust-search" id="catSearchInput" placeholder="{{ __('Type to search...') }}" autocomplete="off">
                    </div>
                    <div class="cust-list" id="catDropdownList" style="max-height: 200px; overflow-y: auto;">
                        <a class="dropdown-item py-1 px-2 rounded {{ !request('category_id') ? 'active' : '' }}" href="javascript:void(0)" data-value="">
                            {{ __('All Categories') }}
                        </a>
                        @foreach($categories as $cat)
                            <a class="dropdown-item py-1 px-2 rounded {{ request('category_id') == $cat->id ? 'active' : '' }}" href="javascript:void(0)" data-value="{{ $cat->id }}" data-text="{{ tdb($cat->name) }}">
                                {{ tdb($cat->name) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Stock Status Dropdown -->
            <select name="stock_status" class="form-select" style="min-width: 130px; font-size: 0.875rem;" onchange="this.form.submit()">
                <option value="">{{ __('All Stock') }}</option>
                <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>{{ __('In Stock') }}</option>
                <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>{{ __('Low Stock') }}</option>
                <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>{{ __('Out of Stock') }}</option>
            </select>

            @if(request()->anyFilled(['search', 'category_id', 'stock_status']))
                <a href="{{ route('products.index') }}" class="btn btn-light border" title="{{ __('Clear Filters') }}">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>

        <a href="{{ route('products.create') }}" class="btn btn-primary-modern text-nowrap">
            <i class="fas fa-plus me-2"></i> {{ __('Add New Item') }}
        </a>
    </div>
</div>

<x-card :padding="false">
    <x-table>
        <x-slot name="thead">
            <th style="min-width: 220px;">{{ __('Product Info') }}</th>
            <th class="text-nowrap" style="min-width: 140px;">{{ __('Category') }}</th>
            <th class="text-nowrap">{{ __('Unit') }}</th>
            <th class="text-nowrap">{{ __('Purchase Price') }}</th>
            <th class="text-nowrap">{{ __('Sale Price') }}</th>
            <th class="text-center text-nowrap">{{ __('Stock') }}</th>
            <th class="text-end pe-4 text-nowrap">{{ __('Action') }}</th>
        </x-slot>

        @forelse($products as $product)
        <tr class="align-middle">
            <td>
                <div>
                    <div class="fw-semibold text-dark text-break" style="font-size: 0.84rem; line-height: 1.3;">{{ tdb($product->name) }}</div>
                    <div class="text-muted" style="font-size: 0.72rem;">SKU: <span class="font-monospace text-secondary">{{ $product->sku }}</span></div>
                </div>
            </td>

            <td class="text-nowrap">
                @if($product->category)
                    <span class="d-inline-flex align-items-center px-2 py-1 bg-light border rounded text-dark fw-medium text-nowrap" style="font-size: 0.8rem;">
                        <i class="fas fa-tag text-primary me-1 opacity-75" style="font-size: 0.72rem;"></i>
                        {{ tdb($product->category->name) }}
                    </span>
                @else
                    <span class="text-muted small">---</span>
                @endif
            </td>

            <td class="text-nowrap">
                @if($product->unit)
                    <span class="text-dark fw-medium" style="font-size: 0.85rem;">{{ tdb($product->unit->name) }}</span>
                    @if($product->unit->code)
                        <span class="text-muted ms-1" style="font-size: 0.78rem;">({{ $product->unit->code }})</span>
                    @endif
                @else
                    <span class="text-muted small">---</span>
                @endif
            </td>

            <td class="text-muted text-nowrap" style="font-size: 0.85rem;">
                {{ $product->purchase_price > 0 ? format_currency($product->purchase_price) : '0.00' }}
            </td>

            <td class="fw-bold text-dark text-nowrap" style="font-size: 0.88rem;">
                {{ format_currency($product->sale_price ?? $product->price) }}
            </td>

            <td class="text-center text-nowrap">
                @if($product->type == 'product')
                    @php
                        $init = (int) ($product->initial_stock ?? 0);
                        $current = (int) ($product->stock ?? 0);
                        $isLow = $product->isLowStock();
                        $isOut = $current <= 0;
                    @endphp

                    <div class="d-inline-flex flex-column align-items-center">
                        <div class="fw-bold {{ $isOut ? 'text-danger' : ($isLow ? 'text-warning' : 'text-dark') }}" style="font-size: 0.85rem;">
                            {{ $current }}
                            @if($init > 0)
                                <span class="text-muted fw-normal" style="font-size: 0.78rem;">/ {{ $init }}</span>
                            @endif
                        </div>
                        <div class="mt-1">
                            @if($isOut)
                                <span class="badge-soft badge-soft-danger text-nowrap" style="font-size: 0.72rem; padding: 2px 7px;">{{ __('Out of Stock') }}</span>
                            @elseif($isLow)
                                <span class="badge-soft badge-soft-warning text-nowrap" style="font-size: 0.72rem; padding: 2px 7px;">
                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ __('Low Stock') }}
                                </span>
                            @else
                                <span class="badge-soft badge-soft-success text-nowrap" style="font-size: 0.72rem; padding: 2px 7px;">{{ __('In Stock') }}</span>
                            @endif
                        </div>
                    </div>
                @else
                    <span class="badge-soft badge-soft-secondary text-nowrap" style="font-size: 0.75rem; padding: 2px 8px;">{{ __('Service') }}</span>
                @endif
            </td>

            <td class="text-end pe-4">
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li>
                            <a class="dropdown-item py-2 text-primary fw-medium" href="{{ route('invoices.create', ['product_id' => $product->id]) }}">
                                <i class="fas fa-cash-register me-2 text-primary"></i> {{ __('Sell / Create Invoice') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('products.edit', $product) }}">
                                <i class="fas fa-edit me-2 text-info"></i> {{ __('Edit') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="dropdown-item py-2 text-danger" onclick="return confirm('{{ __('Are you sure you want to delete this product?') }}')">
                                    <i class="fas fa-trash-alt me-2"></i> {{ __('Delete') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3 d-block opacity-25"></i>
                <h5 class="text-muted">{{ __('No products found') }}</h5>
                <p class="text-muted small">{{ __('Start by creating your first product or try adjusting your search filters.') }}</p>
                <a href="{{ route('products.create') }}" class="btn btn-primary-modern btn-sm mt-2">
                    <i class="fas fa-plus me-1"></i> {{ __('Add New Item') }}
                </a>
            </td>
        </tr>
        @endforelse
    </x-table>

    @if($products->hasPages())
        <div class="card-footer bg-transparent border-top p-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @endif
</x-card>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('catFilterDropdown');
    if (!dropdown) return;

    const hiddenInput = document.getElementById('filter_cat_id');
    const searchInput = document.getElementById('catSearchInput');
    const list = document.getElementById('catDropdownList');
    const items = list ? list.querySelectorAll('.dropdown-item') : [];
    const form = document.getElementById('productsFilterForm');

    // Auto-focus search input when dropdown opens
    dropdown.addEventListener('shown.bs.dropdown', function() {
        if (searchInput) {
            searchInput.value = '';
            items.forEach(function(i) { i.classList.remove('d-none'); });
            setTimeout(function() { searchInput.focus(); }, 50);
        }
    });

    // Live search as user types any word in category dropdown
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            items.forEach(function(item) {
                const text = (item.getAttribute('data-text') || item.textContent).toLowerCase();
                const isAll = item.getAttribute('data-value') === '';
                if (query === '' || isAll || text.includes(query)) {
                    item.classList.remove('d-none');
                } else {
                    item.classList.add('d-none');
                }
            });
        });
    }

    // Select category and submit form
    items.forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            hiddenInput.value = this.getAttribute('data-value');
            if (form) form.submit();
        });
    });
});
</script>
@endpush
@endsection
