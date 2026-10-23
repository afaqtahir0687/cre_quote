@extends('admin.layouts.app')

@section('title', 'Platform Branding & Central Settings')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">Platform Branding & Super Admin Logo</h2>
            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill small">
                <i class="fas fa-crown me-1 text-warning"></i> Super Admin Only
            </span>
        </div>
        <p class="text-muted small mb-0">Manage the central system logo and brand identity. This logo appears in the Super Admin portal, navigation headers, and the customer/staff login page.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Super Admin Portal
        </a>
    </div>
</div>

<form action="{{ route('super_admin.platform_settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Central Logo Card -->
        <div class="col-lg-12">
            <x-card title="Central Platform Logo & Identity">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-4 text-center">
                        <div class="p-3 border rounded-4 bg-light d-flex flex-column align-items-center justify-content-center position-relative" style="min-height: 180px;">
                            @php
                                $currentPlatLogo = platform_logo_url();
                            @endphp
                            <div class="bg-white p-3 rounded-3 shadow-sm border mb-2 d-flex align-items-center justify-content-center" style="width: 220px; height: 110px;">
                                <img id="platformLogoPreviewImg" src="{{ $currentPlatLogo }}" alt="Platform Logo" class="img-fluid" style="max-height: 90px; max-width: 190px; object-fit: contain;">
                            </div>
                            <span class="badge bg-white text-dark border px-2 py-1 small" id="platPreviewBadge">
                                <i class="fas fa-check-circle text-success me-1"></i> Active Platform Logo
                            </span>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">
                                <i class="fas fa-cloud-upload-alt text-primary me-1"></i> Upload Central Platform Logo
                            </label>
                            <input type="file" name="company_logo" id="platform_logo_input" class="form-control @error('company_logo') is-invalid @enderror" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml">
                            @error('company_logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text small mt-2">
                                <i class="fas fa-shield-alt text-warning me-1"></i> <strong>Isolation Note:</strong> This logo is the master system logo for the <strong>Super Admin portal</strong> and the central <strong>Login page</strong>. Changing this logo will <strong>never</strong> overwrite individual shop logos.
                            </div>
                        </div>

                        <div class="p-3 rounded-3 bg-light border border-light-subtle small text-muted">
                            <i class="fas fa-info-circle text-primary me-1"></i>
                            Individual shop admins configure their own logos separately via their Shop Settings.
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Platform General Info -->
        <div class="col-lg-7">
            <x-card title="Platform Profile Details">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark">Platform System Name *</label>
                        <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name', $platformSetting->company_name) }}" required placeholder="e.g. Crelogics Platform">
                        <div class="form-text small">Displayed on Super Admin dashboard and central page titles.</div>
                        @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Platform Support Email *</label>
                        <input type="email" name="company_email" class="form-control @error('company_email') is-invalid @enderror" value="{{ old('company_email', $platformSetting->company_email) }}" required placeholder="support@crelogics.com">
                        @error('company_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Platform Support Phone</label>
                        <input type="text" name="company_phone" class="form-control @error('company_phone') is-invalid @enderror" value="{{ old('company_phone', $platformSetting->company_phone) }}" placeholder="+966 50 000 0000">
                        @error('company_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <button type="submit" class="btn btn-primary-modern px-4 py-2">
                        <i class="fas fa-save me-2"></i> Save Platform Branding
                    </button>
                </div>
            </x-card>
        </div>

        <!-- Info Card -->
        <div class="col-lg-5">
            <x-card title="Branding Architecture Overview">
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-3 d-flex align-items-start gap-2">
                        <i class="fas fa-crown text-warning mt-1"></i>
                        <div>
                            <strong>Super Admin Logo:</strong>
                            <p class="text-muted mb-0">Belongs to the platform system. Seen on Super Admin Portal and central Login page.</p>
                        </div>
                    </li>
                    <li class="mb-3 d-flex align-items-start gap-2">
                        <i class="fas fa-store text-primary mt-1"></i>
                        <div>
                            <strong>Shop Logos:</strong>
                            <p class="text-muted mb-0">Each shop owner / admin controls their own shop logo from their shop settings. Completely isolated per branch.</p>
                        </div>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="fas fa-lock text-success mt-1"></i>
                        <div>
                            <strong>Zero Cross-Contamination:</strong>
                            <p class="text-muted mb-0">Updating a shop logo will never modify Super Admin branding, and updating Super Admin branding will never alter existing shops.</p>
                        </div>
                    </li>
                </ul>
            </x-card>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.getElementById('platform_logo_input')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const previewImg = document.getElementById('platformLogoPreviewImg');
                const badge = document.getElementById('platPreviewBadge');
                if (previewImg) {
                    previewImg.src = evt.target.result;
                }
                if (badge) {
                    badge.innerHTML = '<i class="fas fa-clock text-warning me-1"></i> New Logo Selected (Unsaved)';
                    badge.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 small';
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection
