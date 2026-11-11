@extends('admin.layouts.app')

@section('title', __('Add Application Module'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">{{ __('Add Application Module') }}</h2>
        <p class="text-muted small mb-0">{{ __('Create a new application launcher for the interactive grid.') }}</p>
    </div>
    <a href="{{ route('super_admin.cms.modules.index') }}" class="btn btn-secondary-modern btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to App Grid') }}
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 col-lg-8 mx-auto overflow-hidden">
    <div class="card-body p-4">
        <form action="{{ route('super_admin.cms.modules.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Application Name') }} *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Invoices or Delivery Notes" required>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Slug / Identifier') }}</label>
                    <input type="text" name="slug" class="form-control" placeholder="e.g. invoices">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Destination URL or Anchor') }}</label>
                    <input type="text" name="destination_url" class="form-control" value="#modules" placeholder="#modules or /login">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Vector Icon SVG Code') }}</label>
                <textarea name="icon_svg" class="form-control font-monospace" rows="4" placeholder="<svg viewBox='0 0 48 48' ...>...</svg>"></textarea>
                <div class="form-text small">Paste standard SVG markup or leave blank for a default icon.</div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Display Order') }} *</label>
                    <input type="number" name="sort_order" class="form-control" value="0" required>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="is_active" class="form-check-input" value="1" checked id="isActiveCheck">
                        <label class="form-check-label fw-bold" for="isActiveCheck">{{ __('Active (Visible on Landing Page)') }}</label>
                    </div>
                </div>
            </div>

            <div class="text-end border-top pt-3">
                <button type="submit" class="btn btn-primary-modern px-4">
                    <i class="fas fa-save me-1"></i> {{ __('Create Application') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
