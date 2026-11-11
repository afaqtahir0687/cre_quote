@extends('admin.layouts.app')

@section('title', __('Add Feature Card'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">{{ __('Add Feature Card') }}</h2>
    </div>
    <a href="{{ route('super_admin.cms.features.index') }}" class="btn btn-secondary-modern btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Features') }}
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 col-lg-8 mx-auto overflow-hidden">
    <div class="card-body p-4">
        <form action="{{ route('super_admin.cms.features.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Feature Title') }} *</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Smart Quotation Builder" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Description') }} *</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('FontAwesome Icon Class') }} *</label>
                    <input type="text" name="icon_class" class="form-control" value="fas fa-star" placeholder="e.g. fas fa-file-invoice" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Color Palette Class') }}</label>
                    <select name="icon_color_class" class="form-select">
                        <option value="icon-blue">Blue</option>
                        <option value="icon-indigo">Indigo</option>
                        <option value="icon-green">Green</option>
                        <option value="icon-amber">Amber</option>
                        <option value="icon-purple">Purple</option>
                        <option value="icon-teal">Teal</option>
                        <option value="icon-red">Red</option>
                        <option value="icon-sky">Sky Blue</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Bullet Highlights (one per line)') }}</label>
                <textarea name="highlights" class="form-control" rows="3" placeholder="Point 1&#10;Point 2&#10;Point 3"></textarea>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Display Order') }} *</label>
                    <input type="number" name="sort_order" class="form-control" value="0" required>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="is_active" class="form-check-input" value="1" checked id="featActiveCheck">
                        <label class="form-check-label fw-bold" for="featActiveCheck">{{ __('Active') }}</label>
                    </div>
                </div>
            </div>

            <div class="text-end border-top pt-3">
                <button type="submit" class="btn btn-primary-modern px-4">
                    <i class="fas fa-save me-1"></i> {{ __('Create Feature') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

