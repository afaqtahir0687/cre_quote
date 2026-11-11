@extends('admin.layouts.app')

@section('title', __('Add Industry Solution'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">{{ __('Add Industry Solution') }}</h2>
    </div>
    <a href="{{ route('super_admin.cms.solutions.index') }}" class="btn btn-secondary-modern btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Solutions') }}
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 col-lg-8 mx-auto overflow-hidden">
    <div class="card-body p-4">
        <form action="{{ route('super_admin.cms.solutions.store') }}" method="POST">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Industry Tag') }} *</label>
                    <input type="text" name="tag" class="form-control" placeholder="e.g. Signage & Advertising" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Solution Title') }} *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Printing & Advertising Agencies" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Description') }} *</label>
                <textarea name="description" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Checklist Points (one per line)') }}</label>
                <textarea name="checklist" class="form-control" rows="3" placeholder="Point 1&#10;Point 2&#10;Point 3"></textarea>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Display Order') }} *</label>
                    <input type="number" name="sort_order" class="form-control" value="0" required>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="is_active" class="form-check-input" value="1" checked id="solActiveCheck">
                        <label class="form-check-label fw-bold" for="solActiveCheck">{{ __('Active') }}</label>
                    </div>
                </div>
            </div>

            <div class="text-end border-top pt-3">
                <button type="submit" class="btn btn-primary-modern px-4">
                    <i class="fas fa-save me-1"></i> {{ __('Create Solution') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

