@extends('admin.layouts.app')

@section('title', __('Add New Branch'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">{{ __('Add New Branch Location') }}</h2>
        <p class="text-muted small mb-0">{{ __('Create a sub-branch under your Business Account') }} ({{ $mainShop->name }}).</p>
    </div>
    <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Branches') }}
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <x-card :title="__('Branch Details')">
            <form action="{{ route('branches.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Branch / Shop Name') }} *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Crelogics - Downtown Branch" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('City') }} *</label>
                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $mainShop->city) }}" placeholder="e.g. Riyadh" required>
                        @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Branch Phone') }}</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="e.g. +966 50 123 4567">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">{{ __('Branch Email') }}</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="e.g. downtown@crelogics.com">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">{{ __('Address') }}</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Branch street address, building #...">{{ old('address') }}</textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Currency Symbol') }} *</label>
                        <input type="text" name="currency_symbol" class="form-control" value="{{ old('currency_symbol', $mainShop->currency_symbol) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Invoice Prefix') }} *</label>
                        <input type="text" name="invoice_prefix" class="form-control" value="{{ old('invoice_prefix', 'BR2-INV-') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">{{ __('Quote Prefix') }} *</label>
                        <input type="text" name="quote_prefix" class="form-control" value="{{ old('quote_prefix', 'BR2-QT-') }}" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">{{ __('Branch Logo (Optional)') }}</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <div class="form-text">{{ __('Leave blank to use main shop logo.') }}</div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <a href="{{ route('branches.index') }}" class="btn btn-light me-2">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary-modern px-4">
                        <i class="fas fa-plus-circle me-1"></i> {{ __('Create Branch') }}
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection
