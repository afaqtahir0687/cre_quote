@extends('admin.layouts.app')

@section('title', __('Edit Industry Solution'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">{{ __('Edit Industry Solution') }}</h2>
    </div>
    <a href="{{ route('super_admin.cms.solutions.index') }}" class="btn btn-secondary-modern btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Solutions') }}
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 col-lg-8 mx-auto overflow-hidden">
    <div class="card-body p-4">
        <form action="{{ route('super_admin.cms.solutions.update', $solution->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Industry Tag') }} *</label>
                    <input type="text" name="tag" class="form-control" value="{{ $solution->tag }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Solution Title') }} *</label>
                    <input type="text" name="title" class="form-control" value="{{ $solution->title }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Description') }} *</label>
                <textarea name="description" class="form-control" rows="3" required>{{ $solution->description }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Checklist Points (one per line)') }}</label>
                <textarea name="checklist" class="form-control" rows="3">{{ is_array($solution->checklist) ? implode("\n", $solution->checklist) : '' }}</textarea>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Display Order') }} *</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ $solution->sort_order }}" required>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ $solution->is_active ? 'checked' : '' }} id="solActiveCheck">
                        <label class="form-check-label fw-bold" for="solActiveCheck">{{ __('Active') }}</label>
                    </div>
                </div>
            </div>

            <div class="text-end border-top pt-3">
                <button type="submit" class="btn btn-primary-modern px-4">
                    <i class="fas fa-save me-1"></i> {{ __('Save Solution Changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

