@extends('admin.layouts.app')

@section('title', __('SaaS Packages & Plans'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('SaaS Packages & Plans') }}</h2>
            <span class="badge bg-primary-subtle text-primary border px-2 py-1 rounded-pill small">
                <i class="fas fa-dollar-sign me-1"></i>USD / SAR Currency Engine
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Configure package base pricing in Dollar ($ USD) with automatic real-time conversion to Saudi Riyal (SAR).') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <div class="badge bg-light text-dark border px-3 py-2 rounded-3 small">
            <i class="fas fa-coins text-warning me-1"></i>
            <span>Exchange Peg: <strong>1 USD = 3.75 SAR</strong></span>
        </div>
        <a href="{{ route('super_admin.subscriptions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Subscriptions') }}
        </a>
    </div>
</div>

<div class="row g-4">
    @foreach($packages as $pkg)
        @php
            $isFree = ((float)$pkg->price == 0);
            $usdPrice = (float) old('price', $pkg->price);
            $sarPrice = round($usdPrice * \App\Models\Package::USD_TO_SAR_RATE, 2);
        @endphp
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden package-card" id="pkg-card-{{ $pkg->id }}">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge-soft {{ $pkg->badgeClass() }}">
                            @if($pkg->isTrial())
                                <i class="fas fa-flask me-1"></i> {{ __('Trial Account') }}
                            @elseif($pkg->isBusiness())
                                <i class="fas fa-building me-1"></i> {{ __('Multi-Shop') }}
                            @else
                                <i class="fas fa-calendar-alt me-1"></i> {{ __('Single Shop') }}
                            @endif
                        </span>
                        <span class="badge-soft badge-soft-secondary">
                            {{ $pkg->shops_count }} {{ __('active shops') }}
                        </span>
                    </div>

                    <h4 class="fw-bold text-dark mb-1">{{ $pkg->name }}</h4>
                    <p class="text-muted small mb-0">{{ $pkg->description }}</p>

                    <!-- Live Price Header Preview -->
                    <div class="p-3 mt-3 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small d-block" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Base Price (USD)</span>
                            <span class="fs-4 fw-bolder text-primary" id="preview-usd-{{ $pkg->id }}">
                                {{ $isFree ? 'Free' : '$' . number_format($usdPrice, 2) }}
                            </span>
                            @if(!$isFree)
                                <span class="text-muted small">/ {{ $pkg->duration_days >= 365 ? 'yr' : $pkg->duration_days . 'd' }}</span>
                            @endif
                        </div>
                        <div class="text-end">
                            <span class="text-muted small d-block" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Converted (SAR)</span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fs-6 fw-bold" id="preview-sar-{{ $pkg->id }}">
                                {{ $isFree ? '0.00 SAR' : 'SAR ' . number_format($sarPrice, 2) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('super_admin.packages.update', $pkg->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">{{ __('Package Name') }}</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', tdb($pkg->name)) }}" required>
                        </div>

                        <!-- Dual Currency Synchronized Pricing Row -->
                        <div class="mb-3 p-3 rounded-3 border" style="background: #FAFBFC;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-bold text-dark small mb-0">
                                    <i class="fas fa-coins text-warning me-1"></i> {{ __('Package Pricing & Currency') }}
                                </label>
                                <span class="badge bg-light text-muted border" style="font-size: 0.7rem;">1 USD = 3.75 SAR</span>
                            </div>

                            <div class="row g-2">
                                <!-- Price in USD (Primary saved field) -->
                                <div class="col-6">
                                    <label class="form-label text-muted small mb-1" style="font-size: 0.75rem;">{{ __('Price ($ USD)') }} <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-primary text-white fw-bold">$</span>
                                        <input type="number" step="0.01" min="0" name="price" id="usd-{{ $pkg->id }}" class="form-control fw-bold pkg-usd-input" data-id="{{ $pkg->id }}" value="{{ $usdPrice }}" placeholder="0.00" required>
                                    </div>
                                </div>

                                <!-- Converted Price in SAR (Live bi-directional calculator) -->
                                <div class="col-6">
                                    <label class="form-label text-muted small mb-1" style="font-size: 0.75rem;">{{ __('Converted (SAR)') }}</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-success text-white fw-bold">SAR</span>
                                        <input type="number" step="0.01" min="0" id="sar-{{ $pkg->id }}" class="form-control fw-bold pkg-sar-input" data-id="{{ $pkg->id }}" value="{{ $sarPrice }}" placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top" style="font-size: 0.72rem;">
                                <span class="text-muted"><i class="fas fa-info-circle me-1 text-primary"></i>Stored in <strong>$ USD</strong></span>
                                <span class="text-success fw-semibold"><i class="fas fa-sync-alt me-1"></i>Live Bi-directional Sync</span>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark small">{{ __('Duration (Days)') }}</label>
                                <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', $pkg->duration_days) }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark small">{{ __('Allowed Shops') }}</label>
                                <input type="number" name="max_shops" class="form-control" value="{{ old('max_shops', $pkg->max_shops) }}" min="1" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">{{ __('Description') }}</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', tdb($pkg->description)) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small">{{ __('Features (One per line)') }}</label>
                            @php
                                $featuresText = is_array($pkg->features) ? implode("\n", $pkg->features) : '';
                            @endphp
                            <textarea name="features" class="form-control font-monospace small" rows="5">{{ old('features', $featuresText) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary-modern w-100 py-2">
                            <i class="fas fa-save me-1"></i> {{ __('Update') }} {{ $pkg->name }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const RATE = 3.75; // 1 USD = 3.75 SAR (Saudi SAMA pegged rate)

    function fmt(val) {
        return parseFloat(val || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // When USD input changes -> calculate SAR
    document.querySelectorAll('.pkg-usd-input').forEach(function(input) {
        input.addEventListener('input', function() {
            const id = this.getAttribute('data-id');
            const usdVal = parseFloat(this.value);
            const sarInput = document.getElementById('sar-' + id);
            const previewUsd = document.getElementById('preview-usd-' + id);
            const previewSar = document.getElementById('preview-sar-' + id);

            if (!isNaN(usdVal) && usdVal >= 0) {
                const sarVal = Math.round((usdVal * RATE) * 100) / 100;
                if (sarInput) sarInput.value = sarVal.toFixed(2);
                if (previewUsd) previewUsd.textContent = usdVal === 0 ? 'Free' : '$' + fmt(usdVal);
                if (previewSar) previewSar.textContent = usdVal === 0 ? '0.00 SAR' : 'SAR ' + fmt(sarVal);
            } else {
                if (sarInput) sarInput.value = '';
                if (previewUsd) previewUsd.textContent = '$0.00';
                if (previewSar) previewSar.textContent = 'SAR 0.00';
            }
        });
    });

    // When SAR input changes -> calculate USD
    document.querySelectorAll('.pkg-sar-input').forEach(function(input) {
        input.addEventListener('input', function() {
            const id = this.getAttribute('data-id');
            const sarVal = parseFloat(this.value);
            const usdInput = document.getElementById('usd-' + id);
            const previewUsd = document.getElementById('preview-usd-' + id);
            const previewSar = document.getElementById('preview-sar-' + id);

            if (!isNaN(sarVal) && sarVal >= 0) {
                const usdVal = Math.round((sarVal / RATE) * 100) / 100;
                if (usdInput) usdInput.value = usdVal.toFixed(2);
                if (previewUsd) previewUsd.textContent = usdVal === 0 ? 'Free' : '$' + fmt(usdVal);
                if (previewSar) previewSar.textContent = sarVal === 0 ? '0.00 SAR' : 'SAR ' + fmt(sarVal);
            } else {
                if (usdInput) usdInput.value = '';
                if (previewUsd) previewUsd.textContent = '$0.00';
                if (previewSar) previewSar.textContent = 'SAR 0.00';
            }
        });
    });
});
</script>
@endpush
