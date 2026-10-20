@extends('admin.layouts.app')

@section('title', 'Company Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">System Settings</h2>
</div>

<div class="row">
    <div class="col-lg-12">
        <x-card title="General Configuration">
            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <x-form-input label="Company Name" name="company_name" :value="$setting?->company_name" />
                    </div>
                    <div class="col-md-6">
                        <x-form-input label="Company Email" name="company_email" type="email" :value="$setting?->company_email" />
                    </div>
                    <div class="col-md-6">
                        <x-form-input label="Company Phone" name="company_phone" :value="$setting?->company_phone" />
                    </div>
                    <div class="col-md-6">
                        <x-form-input label="Tax ID / GSTIN" name="company_tax_id" :value="$setting?->company_tax_id" />
                    </div>
                    <div class="col-md-6">
                        <x-form-input label="VAT No" name="company_vat_no" :value="$setting?->company_vat_no" />
                    </div>
                    <div class="col-md-6">
                        <x-form-input label="CR No" name="company_cr_no" :value="$setting?->company_cr_no" />
                    </div>
                    <div class="col-md-12">
                        <div class="form-floating mb-3">
                            <textarea name="company_address" id="company_address" class="form-control" style="height: 100px">{{ $setting?->company_address }}</textarea>
                            <label for="company_address">Company Address</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <x-form-input label="City" name="company_city" :value="$setting?->company_city" />
                    </div>
                    <div class="col-md-6">
                        <x-form-input label="Country" name="company_country" :value="$setting?->company_country" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input label="Currency Symbol" name="currency_symbol" :value="$setting?->currency_symbol ?: '$'" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input label="Invoice Prefix" name="invoice_prefix" :value="$setting?->invoice_prefix ?: 'INV-'" />
                    </div>
                    <div class="col-md-4">
                        <x-form-input label="Quote Prefix" name="quote_prefix" :value="$setting?->quote_prefix ?: 'QT-'" />
                    </div>
                    
                    <div class="col-md-6">
                        <div class="p-4 border rounded-4 bg-light-subtle">
                            <label class="form-label fw-bold text-dark mb-3">Company Logo</label>
                            <div class="d-flex align-items-center gap-4">
                                @if($setting?->company_logo)
                                    <div class="avatar-lg bg-white p-2 rounded-3 shadow-sm border">
                                        <img src="{{ asset($setting?->company_logo) }}" alt="Logo" class="img-fluid" style="max-height: 60px;">
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <input type="file" name="company_logo" class="form-control form-control-sm border-2">
                                    <div class="form-text small">Recommended size: 200x60px. PNG or SVG.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top text-end">
                    <button type="submit" class="btn btn-primary-modern px-5 py-3">
                        <i class="fas fa-save me-2"></i> Update Settings
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection
