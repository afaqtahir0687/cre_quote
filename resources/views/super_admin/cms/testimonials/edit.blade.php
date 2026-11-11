@extends('admin.layouts.app')

@section('title', __('Edit Testimonial'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">{{ __('Edit Customer Testimonial') }}</h2>
    </div>
    <a href="{{ route('super_admin.cms.testimonials.index') }}" class="btn btn-secondary-modern btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Testimonials') }}
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 col-lg-8 mx-auto overflow-hidden">
    <div class="card-body p-4">
        <form action="{{ route('super_admin.cms.testimonials.update', $testimonial->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Client Name') }} *</label>
                    <input type="text" name="name" class="form-control" value="{{ $testimonial->name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Designation / Role') }}</label>
                    <input type="text" name="designation" class="form-control" value="{{ $testimonial->designation }}">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Company Name') }}</label>
                    <input type="text" name="company" class="form-control" value="{{ $testimonial->company }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Rating (Stars 1 to 5)') }} *</label>
                    <select name="rating" class="form-select">
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ $testimonial->rating == $i ? 'selected' : '' }}>{{ str_repeat('⭐', $i) }} ({{ $i }} Stars)</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Testimonial Content') }} *</label>
                <textarea name="content" class="form-control" rows="4" required>{{ $testimonial->content }}</textarea>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">{{ __('Display Order') }} *</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ $testimonial->sort_order }}" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="is_approved" class="form-check-input" value="1" {{ $testimonial->is_approved ? 'checked' : '' }} id="apprCheck">
                        <label class="form-check-label fw-bold text-success" for="apprCheck">{{ __('Approved for Display') }}</label>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ $testimonial->is_active ? 'checked' : '' }} id="testActiveCheck">
                        <label class="form-check-label fw-bold" for="testActiveCheck">{{ __('Active') }}</label>
                    </div>
                </div>
            </div>

            <div class="text-end border-top pt-3">
                <button type="submit" class="btn btn-primary-modern px-4">
                    <i class="fas fa-save me-1"></i> {{ __('Save Testimonial Changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

