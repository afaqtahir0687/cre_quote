@extends('admin.layouts.app')

@section('title', __('Record Daily Office Expense'))

@section('content')
<div class="container-fluid px-3 px-md-4 py-3">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="fas fa-plus-circle text-primary"></i>
                {{ __('Record Daily Office Expense') }}
            </h3>
            <p class="text-muted small mb-0">{{ __('Record a new daily expense for office, staff refreshment, supplies, or maintenance.') }}</p>
        </div>
        <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>{{ __('Back to List') }}
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 bg-white">
                <div class="card-body p-4">
                    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            {{-- Office / Branch --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small mb-1">
                                    <i class="fas fa-building text-primary me-1"></i>{{ __('Office / Branch') }} *
                                </label>
                                @if($isAdmin)
                                    <select name="shop_id" id="createShopSelect" class="form-select @error('shop_id') is-invalid @enderror" required>
                                        @foreach($offices as $off)
                                            <option value="{{ $off->id }}" {{ old('shop_id', $defaultShopId) == $off->id ? 'selected' : '' }}>
                                                {{ $off->name }} {{ $off->parent_shop_id ? '(' . __('Branch') . ')' : '(' . __('Head Office') . ')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('shop_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @else
                                    <div class="form-control bg-light text-dark fw-bold d-flex align-items-center gap-2">
                                        <i class="fas fa-check-circle text-success"></i>
                                        <span>{{ auth()->user()->shop?->name ?: __('Your Assigned Office') }}</span>
                                    </div>
                                    <input type="hidden" name="shop_id" value="{{ auth()->user()->shop_id }}">
                                    <small class="text-muted extra-small">{{ __('Expenses will be recorded for your assigned office.') }}</small>
                                @endif
                            </div>

                            {{-- Expense Date --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small mb-1">
                                    <i class="fas fa-calendar-alt text-primary me-1"></i>{{ __('Expense Date') }} *
                                </label>
                                <input type="date" name="expense_date" class="form-control @error('expense_date') is-invalid @enderror" 
                                       value="{{ old('expense_date', date('Y-m-d')) }}" required>
                                @error('expense_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Category --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small mb-1">
                                    <i class="fas fa-tags text-warning me-1"></i>{{ __('Category') }} *
                                </label>
                                <select name="category" id="createCategorySelect" class="form-select @error('category') is-invalid @enderror" required>
                                    @foreach(\App\Models\Expense::categories() as $key => $label)
                                        <option value="{{ $key }}" {{ old('category', 'tea_refreshment') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Payment Method --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small mb-1">
                                    <i class="fas fa-money-bill-wave text-success me-1"></i>{{ __('Payment Method') }} *
                                </label>
                                <select name="payment_method" id="createPaymentMethodSelect" class="form-select @error('payment_method') is-invalid @enderror" required>
                                    @foreach(\App\Models\Expense::paymentMethods() as $mKey => $mLabel)
                                        <option value="{{ $mKey }}" {{ old('payment_method', 'cash') === $mKey ? 'selected' : '' }}>
                                            {{ $mLabel }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('payment_method') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Expense Title --}}
                            <div class="col-md-8">
                                <label class="form-label fw-bold text-dark small mb-1">
                                    <i class="fas fa-pen text-primary me-1"></i>{{ __('Expense Title / Item Description') }} *
                                </label>
                                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                       placeholder="{{ __('e.g., Daily Tea, Sugar & Milk, Printer Paper, Water Bottles') }}" 
                                       value="{{ old('title') }}" required>
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Amount --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-success small mb-1">
                                    <i class="fas fa-tag me-1"></i>{{ __('Amount') }} *
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success-subtle text-success fw-bold">{{ currency_symbol() }}</span>
                                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control font-monospace fw-bold text-success @error('amount') is-invalid @enderror" 
                                           placeholder="0.00" value="{{ old('amount') }}" required>
                                    @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            {{-- Reference / Bill No. --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small mb-1">
                                    <i class="fas fa-hashtag text-muted me-1"></i>{{ __('Receipt / Bill / Voucher No.') }}
                                </label>
                                <input type="text" name="reference_no" class="form-control @error('reference_no') is-invalid @enderror" 
                                       placeholder="{{ __('e.g., REC-10492 or Bill #') }}" value="{{ old('reference_no') }}">
                                @error('reference_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Receipt Attachment --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small mb-1">
                                    <i class="fas fa-paperclip text-muted me-1"></i>{{ __('Attach Receipt / Bill (Image or PDF)') }}
                                </label>
                                <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror" 
                                       accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf">
                                @error('attachment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted extra-small">{{ __('Optional. Max 5MB (JPG, PNG, PDF).') }}</small>
                            </div>

                            {{-- Notes / Remarks --}}
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small mb-1">
                                    <i class="fas fa-sticky-note text-muted me-1"></i>{{ __('Notes / Additional Remarks') }}
                                </label>
                                <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" 
                                          placeholder="{{ __('Any extra details, vendor name, or staff notes...') }}">{{ old('notes') }}</textarea>
                                @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Submit Buttons --}}
                            <div class="col-12 pt-3 border-top d-flex justify-content-end gap-2">
                                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary px-4">
                                    {{ __('Cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary px-4 fw-bold">
                                    <i class="fas fa-check-circle me-1"></i>{{ __('Save Expense') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('partials.searchable_select')

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.initSearchableFilterSelect === 'function') {
        if (document.getElementById('createShopSelect')) {
            window.initSearchableFilterSelect('createShopSelect', {
                placeholder: "{{ __('Select Office / Branch...') }}",
                searchPlaceholder: "{{ __('Search office...') }}"
            });
        }
        if (document.getElementById('createCategorySelect')) {
            window.initSearchableFilterSelect('createCategorySelect', {
                placeholder: "{{ __('Select Category...') }}",
                searchPlaceholder: "{{ __('Search category...') }}"
            });
        }
        if (document.getElementById('createPaymentMethodSelect')) {
            window.initSearchableFilterSelect('createPaymentMethodSelect', {
                placeholder: "{{ __('Select Payment Method...') }}",
                searchPlaceholder: "{{ __('Search payment method...') }}"
            });
        }
    }
});
</script>
@endpush
