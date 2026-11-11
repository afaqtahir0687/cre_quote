@extends('admin.layouts.app')

@section('title', __('Edit Workflow Tab'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">{{ __('Edit Workflow Tab') }}: {{ $tab->tab_label }}</h2>
    </div>
    <a href="{{ route('super_admin.cms.showcase.index') }}" class="btn btn-secondary-modern btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Tabs') }}
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 col-lg-8 mx-auto overflow-hidden">
    <div class="card-body p-4">
        <form action="{{ route('super_admin.cms.showcase.update', $tab->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Tab Navigation Button Label') }} *</label>
                    <input type="text" name="tab_label" class="form-control" value="{{ $tab->tab_label }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Tab Icon (FontAwesome)') }} *</label>
                    <input type="text" name="tab_icon" class="form-control" value="{{ $tab->tab_icon }}" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">{{ __('Pill Badge Text') }}</label>
                    <input type="text" name="badge_text" class="form-control" value="{{ $tab->badge_text }}">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">{{ __('Main Headline') }} *</label>
                    <input type="text" name="title" class="form-control" value="{{ $tab->title }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Description') }} *</label>
                <textarea name="description" class="form-control" rows="3" required>{{ $tab->description }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Checklist Points (one per line)') }}</label>
                <textarea name="highlights" class="form-control" rows="3">{{ is_array($tab->highlights) ? implode("\n", $tab->highlights) : '' }}</textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Button Text') }}</label>
                    <input type="text" name="cta_text" class="form-control" value="{{ $tab->cta_text }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Button Link URL') }}</label>
                    <input type="text" name="cta_url" class="form-control" value="{{ $tab->cta_url }}">
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Display Order') }} *</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ $tab->sort_order }}" required>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ $tab->is_active ? 'checked' : '' }} id="tabActiveCheck">
                        <label class="form-check-label fw-bold" for="tabActiveCheck">{{ __('Active') }}</label>
                    </div>
                </div>
            </div>

            <div class="text-end border-top pt-3">
                <button type="submit" class="btn btn-primary-modern px-4">
                    <i class="fas fa-save me-1"></i> {{ __('Save Tab Changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

