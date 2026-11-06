@extends('admin.layouts.app')

@section('title', __('Edit Category'))

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('categories.index') }}" class="btn btn-light btn-sm rounded-circle shadow-sm me-3" style="width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center;" title="{{ __('Back to Categories') }}">
        <i class="fas fa-arrow-left text-muted"></i>
    </a>
    <div>
        <h3 class="fw-bold text-dark mb-0">{{ __('Edit Category') }}</h3>
    </div>
</div>

<x-card>
    <form action="{{ route('categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label fw-semibold text-dark">{{ __('Category Name') }} <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label fw-semibold text-dark">{{ __('Description') }}</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" value="1" {{ old('status', $category->status ? '1' : '0') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="statusSwitch">{{ __('Active') }}</label>
                </div>
            </div>
        </div>

        <div class="text-end border-top pt-3 mt-3">
            <a href="{{ route('categories.index') }}" class="btn btn-secondary me-2">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary-modern">
                <i class="fas fa-save me-1"></i> {{ __('Update Category') }}
            </button>
        </div>
    </form>
</x-card>
@endsection
