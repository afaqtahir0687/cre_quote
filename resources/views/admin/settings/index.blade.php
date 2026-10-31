@extends('admin.layouts.app')

@section('title', 'Shop & Company Settings')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">Shop & Brand Settings</h2>
            @if(isset($shop) && $shop)
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill small">
                    <i class="fas fa-store me-1"></i> {{ $shop->name }} (Branch #{{ $shop->id }})
                </span>
            @endif
        </div>
        <p class="text-muted small mb-0">Control company title, upload branding logo, manage billing prefixes, and invoice header information.</p>
    </div>
    
    @if(auth()->user()->isSuperAdmin())
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('super_admin.platform_settings') }}" class="btn btn-warning-subtle text-warning-emphasis border border-warning-subtle btn-sm">
                <i class="fas fa-paint-brush me-1 text-warning"></i> Platform Branding (Super Admin)
            </a>
            <a href="{{ route('super_admin.shops.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-store-alt me-1"></i> Manage All Shops
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-light border btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Return to Dashboard
            </a>
        </div>
    @endif
</div>

@if(auth()->user()->isSuperAdmin())
    <div class="alert alert-info d-flex align-items-center mb-4 shadow-sm border-0 border-start border-4 border-info">
        <i class="fas fa-info-circle fa-2x text-info me-3"></i>
        <div>
            <div class="fw-bold text-dark">Currently Configuring: {{ $shop->name }} (Branch #{{ $shop->id }})</div>
            <div class="small text-muted">All logo and branding changes saved on this page apply <strong>strictly to this shop</strong>. To change the master system logo for the Super Admin portal and login page, please visit <a href="{{ route('super_admin.platform_settings') }}" class="fw-bold text-primary text-decoration-underline">Platform Branding</a>.</div>
        </div>
    </div>
@endif

<form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Card 1: Visual Brand & Logo Identity -->
        <div class="col-lg-12">
            <x-card title="Brand Identity & Logo Configuration">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-4 text-center">
                        <div class="p-3 border rounded-4 bg-light d-flex flex-column align-items-center justify-content-center position-relative" style="min-height: 180px;">
                            @php
                                $logoUrl = shop_logo_url($shop, $setting);
                            @endphp
                            <div class="bg-white p-3 rounded-3 shadow-sm border mb-2 d-flex align-items-center justify-content-center" style="width: 220px; height: 110px;">
                                <img id="logoPreviewImg" src="{{ $logoUrl ?: asset('uploads/logo/crelogics-logo.png') }}" alt="Shop Logo" class="img-fluid" style="max-height: 90px; max-width: 190px; object-fit: contain;">
                            </div>
                            <span class="badge bg-white text-secondary border px-2 py-1 small" id="logoPreviewBadge">
                                {{ $logoUrl ? 'Current Active Logo' : 'Default Platform Logo' }}
                            </span>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">
                                <i class="fas fa-cloud-upload-alt text-primary me-1"></i> Upload New Logo
                            </label>
                            <input type="file" name="company_logo" id="company_logo_input" class="form-control @error('company_logo') is-invalid @enderror" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml">
                            @error('company_logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text small mt-2">
                                <i class="fas fa-info-circle text-primary me-1"></i> Supported: <strong>PNG, JPG, WebP, SVG</strong> (Max 20MB). A clean horizontal logo with transparent or white background is recommended. It will automatically update the <strong>Sidebar, Dashboard Header, Quotes, Job Orders, Invoices, and Delivery Notes</strong>.
                            </div>
                        </div>

                        @if(!empty($setting?->company_logo) || !empty($shop?->logo))
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogoCheck">
                                <label class="form-check-label text-danger small fw-medium" for="removeLogoCheck">
                                    <i class="fas fa-trash-alt me-1"></i> Remove custom logo and restore system default
                                </label>
                            </div>
                        @endif
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Card: Bank Details & Official Stamp (PDF) -->
        <div class="col-lg-12">
            <x-card title="Bank Account Details & Official Stamp (PDF Documents)">
                <div class="row g-4 align-items-start">
                    <!-- Left: Live Preview of Bank Slip -->
                    <div class="col-lg-5">
                        <div class="p-3 border rounded-4 bg-light d-flex flex-column align-items-center justify-content-center">
                            <div class="fw-bold text-dark small mb-2 d-flex align-items-center gap-1">
                                <i class="fas fa-eye text-primary"></i>
                                <span>Live PDF Bank Slip Preview</span>
                            </div>
                            
                            <div class="bg-white p-3 rounded-3 shadow-sm border mb-2 w-100" style="max-width: 360px;">
                                <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-family: 'amiri', sans-serif; direction: ltr;">
                                    <tr>
                                        <td style="width: 55%; vertical-align: middle; text-align: left; padding-bottom: 8px;">
                                            <div style="background-color: #2b2e35; color: #ffffff; padding: 5px 12px; border-radius: 6px; display: inline-block;">
                                                <div id="prev_bank_name" style="font-size: 14px; font-weight: bold; letter-spacing: 1px; line-height: 1.1;">{{ $setting->bank_name ?? 'SNB' }}</div>
                                                <div id="prev_bank_name_ar" style="font-size: 8.5px; color: #d0d4dc; margin-top: 2px;">{{ $setting->bank_name_ar ?? 'البنك الأهلي السعودي' }}</div>
                                            </div>
                                        </td>
                                        <td style="width: 45%; vertical-align: middle; text-align: right; padding-bottom: 8px;">
                                            @php
                                                $stampUrl = !empty($setting?->company_stamp) && file_exists(public_path($setting->company_stamp))
                                                    ? asset($setting->company_stamp)
                                                    : (file_exists(public_path('uploads/logo/stamp.png')) ? asset('uploads/logo/stamp.png') : null);
                                            @endphp
                                            <img id="stampPreviewImg" src="{{ $stampUrl }}" style="max-height: 48px; max-width: 95px; object-fit: contain; {{ !$stampUrl ? 'display:none;' : '' }}" alt="Stamp">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding: 2px 0;">
                                            <table style="width: 100%; border-collapse: collapse;">
                                                <tr>
                                                    <td style="width: 22%; font-size: 10.5px; font-weight: bold; color: #222; text-align: left; white-space: nowrap;">Name :</td>
                                                    <td style="width: 56%; text-align: center; vertical-align: middle;">
                                                        <div id="prev_account_name" style="background-color: #2b2e35; color: #ffffff; font-size: 10px; font-weight: bold; padding: 4px 6px; border-radius: 5px; line-height: 1.2;">
                                                            {{ $setting->bank_account_name ?? 'شركة وكالة الإعلان الجديد للدعاية والإعلان' }}
                                                        </div>
                                                    </td>
                                                    <td style="width: 22%; font-size: 10.5px; font-weight: bold; color: #222; text-align: right; white-space: nowrap; direction: rtl;">: الإسم</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding: 2px 0;">
                                            <table style="width: 100%; border-collapse: collapse;">
                                                <tr>
                                                    <td style="width: 22%; font-size: 10.5px; font-weight: bold; color: #222; text-align: left; white-space: nowrap;">A.C. No. :</td>
                                                    <td style="width: 56%; text-align: center; vertical-align: middle;">
                                                        <div id="prev_account_no" style="background-color: #2b2e35; color: #ffffff; font-size: 11px; font-weight: bold; padding: 4px 6px; border-radius: 5px; letter-spacing: 0.5px;">
                                                            {{ $setting->bank_account_no ?? '24400001567203' }}
                                                        </div>
                                                    </td>
                                                    <td style="width: 22%; font-size: 10.5px; font-weight: bold; color: #222; text-align: right; white-space: nowrap; direction: rtl;">: رقم الحساب</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding: 2px 0;">
                                            <table style="width: 100%; border-collapse: collapse;">
                                                <tr>
                                                    <td style="width: 22%; font-size: 10.5px; font-weight: bold; color: #222; text-align: left; white-space: nowrap;">IBAN. :</td>
                                                    <td style="width: 56%; text-align: center; vertical-align: middle;">
                                                        <div id="prev_iban" style="background-color: #2b2e35; color: #ffffff; font-size: 9px; font-weight: bold; padding: 4px 6px; border-radius: 5px; letter-spacing: 0.3px;">
                                                            {{ $setting->bank_iban ?? 'SA0210000024400001567203' }}
                                                        </div>
                                                    </td>
                                                    <td style="width: 22%; font-size: 10.5px; font-weight: bold; color: #222; text-align: right; white-space: nowrap; direction: rtl;">: رقم الآيبان</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="padding: 2px 0;">
                                            <table style="width: 100%; border-collapse: collapse;">
                                                <tr>
                                                    <td style="width: 22%; font-size: 10.5px; font-weight: bold; color: #222; text-align: left; white-space: nowrap;">SWIFT. :</td>
                                                    <td style="width: 56%; text-align: center; vertical-align: middle;">
                                                        <div id="prev_swift" style="background-color: #2b2e35; color: #ffffff; font-size: 11px; font-weight: bold; padding: 4px 6px; border-radius: 5px; letter-spacing: 1px;">
                                                            {{ $setting->bank_swift ?? 'NCBKSAJE' }}
                                                        </div>
                                                    </td>
                                                    <td style="width: 22%; font-size: 10.5px; font-weight: bold; color: #222; text-align: right; white-space: nowrap; direction: rtl;">: رمز السويفت</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 small">
                                <i class="fas fa-check-circle me-1"></i> Rendered on all Quotes, Invoices, Job Orders & Statements
                            </span>
                        </div>
                    </div>

                    <!-- Right: Editable Inputs -->
                    <div class="col-lg-7">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">
                                    Bank Name (English)
                                </label>
                                <input type="text" name="bank_name" id="in_bank_name" class="form-control"
                                       value="{{ old('bank_name', $setting?->bank_name ?? 'SNB') }}" placeholder="e.g. SNB">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">
                                    Bank Name (Arabic)
                                </label>
                                <input type="text" name="bank_name_ar" id="in_bank_name_ar" class="form-control" dir="rtl"
                                       value="{{ old('bank_name_ar', $setting?->bank_name_ar ?? 'البنك الأهلي السعودي') }}" placeholder="البنك الأهلي السعودي">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small">
                                    Account Holder / Company Name (Name / الإسم) *
                                </label>
                                <input type="text" name="bank_account_name" id="in_account_name" class="form-control"
                                       value="{{ old('bank_account_name', $setting?->bank_account_name ?? 'شركة وكالة الإعلان الجديد للدعاية والإعلان') }}" placeholder="e.g. شركة وكالة الإعلان الجديد للدعاية والإعلان">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">
                                    Account Number (A.C. No. / رقم الحساب) *
                                </label>
                                <input type="text" name="bank_account_no" id="in_account_no" class="form-control font-monospace"
                                       value="{{ old('bank_account_no', $setting?->bank_account_no ?? '24400001567203') }}" placeholder="24400001567203">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">
                                    SWIFT Code (رمز السويفت)
                                </label>
                                <input type="text" name="bank_swift" id="in_swift" class="form-control font-monospace"
                                       value="{{ old('bank_swift', $setting?->bank_swift ?? 'NCBKSAJE') }}" placeholder="NCBKSAJE">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small">
                                    IBAN Number (رقم الآيبان) *
                                </label>
                                <input type="text" name="bank_iban" id="in_iban" class="form-control font-monospace"
                                       value="{{ old('bank_iban', $setting?->bank_iban ?? 'SA0210000024400001567203') }}" placeholder="SA0210000024400001567203">
                            </div>

                            <div class="col-md-12"><hr class="my-1 opacity-25"></div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">
                                    <i class="fas fa-stamp text-primary me-1"></i> Upload Company Stamp
                                </label>
                                <input type="file" name="company_stamp" id="company_stamp_input" class="form-control form-control-sm" accept="image/png,image/jpeg,image/jpg,image/webp">
                                <div class="form-text small">Upload transparent PNG/JPG official stamp.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">
                                    <i class="fas fa-image text-muted me-1"></i> Or Full Graphic Card Image (Optional)
                                </label>
                                <input type="file" name="bank_stamp_logo" id="bank_stamp_logo_input" class="form-control form-control-sm" accept="image/png,image/jpeg,image/jpg,image/webp">
                                <div class="form-text small">If you prefer uploading a pre-made image instead.</div>
                            </div>

                            @if(!empty($setting?->bank_stamp_logo))
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="use_custom_bank_image" value="1" id="useCustomBankImg" {{ old('use_custom_bank_image', $setting?->use_custom_bank_image) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold text-dark small" for="useCustomBankImg">
                                        Use uploaded graphic card image instead of dynamic text card on PDFs
                                    </label>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="col-lg-7">
            
            <x-card title="Shop Profile & Company Details">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-dark">
                            Company / Shop Name *
                        </label>
                        <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $setting?->company_name ?? $shop?->name) }}" required placeholder="e.g. New Crelogics Signage">
                        <div class="form-text small">This title appears across the system header, browser title, and all printed documents.</div>
                        @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Official Email *</label>
                        <input type="email" name="company_email" class="form-control @error('company_email') is-invalid @enderror" value="{{ old('company_email', $setting?->company_email ?? $shop?->email) }}" required placeholder="info@crelogics.com">
                        @error('company_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Phone Number</label>
                        <input type="text" name="company_phone" class="form-control @error('company_phone') is-invalid @enderror" value="{{ old('company_phone', $setting?->company_phone ?? $shop?->phone) }}" placeholder="+966 50 123 4567">
                        @error('company_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Physical Address</label>
                        <textarea name="company_address" id="company_address" rows="2" class="form-control @error('company_address') is-invalid @enderror" placeholder="Street, Industrial Area, Building...">{{ old('company_address', $setting?->company_address ?? $shop?->address) }}</textarea>
                        @error('company_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">City</label>
                        <input type="text" name="company_city" class="form-control @error('company_city') is-invalid @enderror" value="{{ old('company_city', $setting?->company_city ?? $shop?->city) }}" placeholder="e.g. Riyadh">
                        @error('company_city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Country</label>
                        <input type="text" name="company_country" class="form-control @error('company_country') is-invalid @enderror" value="{{ old('company_country', $setting?->company_country ?? $shop?->country ?? 'Saudi Arabia') }}" placeholder="Saudi Arabia">
                        @error('company_country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Card 3: Billing, Prefixes & Legal Numbers -->
        <div class="col-lg-5">
            <x-card title="Billing Prefixes & Tax Registration">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Currency *</label>
                        <input type="text" name="currency_symbol" class="form-control @error('currency_symbol') is-invalid @enderror" value="{{ old('currency_symbol', $setting?->currency_symbol ?? $shop?->currency_symbol ?? '$') }}" required placeholder="$">
                        @error('currency_symbol') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Quote Prefix *</label>
                        <input type="text" name="quote_prefix" class="form-control @error('quote_prefix') is-invalid @enderror" value="{{ old('quote_prefix', $setting?->quote_prefix ?? $shop?->quote_prefix ?? 'QT-') }}" required placeholder="QT-">
                        @error('quote_prefix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Invoice Prefix *</label>
                        <input type="text" name="invoice_prefix" class="form-control @error('invoice_prefix') is-invalid @enderror" value="{{ old('invoice_prefix', $setting?->invoice_prefix ?? $shop?->invoice_prefix ?? 'INV-') }}" required placeholder="INV-">
                        @error('invoice_prefix') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12"><hr class="my-2 opacity-25"></div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Tax ID / GSTIN</label>
                        <input type="text" name="company_tax_id" class="form-control @error('company_tax_id') is-invalid @enderror" value="{{ old('company_tax_id', $setting?->company_tax_id) }}" placeholder="Tax identification number">
                        @error('company_tax_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">VAT Number</label>
                        <input type="text" name="company_vat_no" class="form-control @error('company_vat_no') is-invalid @enderror" value="{{ old('company_vat_no', $setting?->company_vat_no) }}" placeholder="300XXXXXXXXXXXX">
                        @error('company_vat_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">CR Number (Commercial Reg.)</label>
                        <input type="text" name="company_cr_no" class="form-control @error('company_cr_no') is-invalid @enderror" value="{{ old('company_cr_no', $setting?->company_cr_no) }}" placeholder="1010XXXXXX">
                        @error('company_cr_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12"><hr class="my-2 opacity-25"></div>

                    <div class="col-12">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" name="show_customer_name_on_pdf" id="show_customer_name_on_pdf" value="1" {{ old('show_customer_name_on_pdf', $setting?->show_customer_name_on_pdf ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark" for="show_customer_name_on_pdf">
                                Show Customer Name on PDF Documents by Default
                            </label>
                        </div>
                        <div class="form-text small text-muted">When enabled, the Customer Name will be included on Quotes, Job Orders, Invoices, and Delivery Note PDFs by default. You can also customize this for individual documents.</div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-primary-modern px-4 py-2">
                        <i class="fas fa-save me-2"></i> Save & Apply Settings
                    </button>
                </div>
            </x-card>
        </div>
    </div>
</form>

@push('scripts')
<script>
    // Live logo preview using FileReader
    document.getElementById('company_logo_input')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const previewImg = document.getElementById('logoPreviewImg');
                const badge = document.getElementById('logoPreviewBadge');
                if (previewImg) {
                    previewImg.src = evt.target.result;
                }
                if (badge) {
                    badge.textContent = 'Selected New Logo (Unsaved Preview)';
                    badge.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 small';
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Live bank stamp preview using FileReader
    document.getElementById('bank_stamp_logo_input')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const previewImg = document.getElementById('bankStampPreviewImg');
                const badge = document.getElementById('bankStampPreviewBadge');
                if (previewImg) {
                    previewImg.src = evt.target.result;
                }
                if (badge) {
                    badge.textContent = 'Selected New Bank/Stamp (Unsaved Preview)';
                    badge.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 small';
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Live update bank slip preview as user types
    const bindSync = (inputId, prevId) => {
        const inp = document.getElementById(inputId);
        const prev = document.getElementById(prevId);
        if (inp && prev) {
            inp.addEventListener('input', function() {
                prev.textContent = this.value || '-';
            });
        }
    };

    bindSync('in_bank_name', 'prev_bank_name');
    bindSync('in_bank_name_ar', 'prev_bank_name_ar');
    bindSync('in_account_name', 'prev_account_name');
    bindSync('in_account_no', 'prev_account_no');
    bindSync('in_iban', 'prev_iban');
    bindSync('in_swift', 'prev_swift');

    // Live stamp preview
    document.getElementById('company_stamp_input')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const stampImg = document.getElementById('stampPreviewImg');
                if (stampImg) {
                    stampImg.src = evt.target.result;
                    stampImg.style.display = 'inline-block';
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection
