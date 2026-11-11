@extends('admin.layouts.app')

@section('title', __('Add FAQ'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-0">{{ __('Add FAQ') }}</h2>
    </div>
    <a href="{{ route('super_admin.cms.faqs.index') }}" class="btn btn-secondary-modern btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to FAQs') }}
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 col-lg-8 mx-auto overflow-hidden">
    <div class="card-body p-4">
        <form action="{{ route('super_admin.cms.faqs.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Question') }} *</label>
                <input type="text" name="question" class="form-control" required placeholder="e.g. Can I manage multiple shop branches?">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Answer') }} *</label>
                <textarea name="answer" class="form-control" rows="5" required placeholder="Provide a clear, detailed answer..."></textarea>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">{{ __('Display Order') }} *</label>
                    <input type="number" name="sort_order" class="form-control" value="0" required>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="is_active" class="form-check-input" value="1" checked id="faqActiveCheck">
                        <label class="form-check-label fw-bold" for="faqActiveCheck">{{ __('Active (Visible in Accordion)') }}</label>
                    </div>
                </div>
            </div>

            <div class="text-end border-top pt-3">
                <button type="submit" class="btn btn-primary-modern px-4">
                    <i class="fas fa-save me-1"></i> {{ __('Create FAQ') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

