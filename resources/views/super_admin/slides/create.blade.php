@extends('admin.layouts.app')

@section('title', __('Add New Homepage Slide'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="fw-bold text-dark mb-1">{{ __('Add New Hero Slide') }}</h2>
        <p class="text-muted small mb-0">{{ __('Create a new slide for the homepage hero carousel with custom content and actions.') }}</p>
    </div>
    <a href="{{ route('super_admin.cms.slides.index') }}" class="btn btn-secondary-modern">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Slides') }}
    </a>
</div>

@if(isset($errors) && $errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
        <div class="fw-bold mb-1"><i class="fas fa-exclamation-circle me-1"></i> Please fix the errors below:</div>
        <ul class="mb-0 ps-3 small">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('super_admin.cms.slides.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- Left: Slide Content & Buttons -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fas fa-pen text-primary me-2"></i>{{ __('Slide Headlines & Copy') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Internal Admin Title') }} <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="e.g. Marketing Slide 2 - Digital Solutions" required>
                        <div class="form-text small">{{ __('Used to identify this slide in the admin panel.') }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Eyebrow Badge Text') }}</label>
                        <input type="text" name="badge_text" class="form-control" value="{{ old('badge_text', 'Smart Digital Solutions') }}" placeholder="e.g. Complete Business ERP Solution">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-bold text-dark small">{{ __('Main Heading') }} <span class="text-danger">*</span></label>
                            <input type="text" name="heading" class="form-control" value="{{ old('heading') }}" placeholder="e.g. Grow Your Business With" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold text-dark small">{{ __('Highlighted Heading Phrase') }}</label>
                            <input type="text" name="heading_highlight" class="form-control" value="{{ old('heading_highlight') }}" placeholder="e.g. Smart Digital Solutions">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Subheading') }}</label>
                        <input type="text" name="subheading" class="form-control" value="{{ old('subheading') }}" placeholder="e.g. Transform your business with professional digital marketing...">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Description / Lead Paragraph') }}</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Detailed marketing copy for this slide...">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Trust Bullets (One per line)') }}</label>
                        <textarea name="trust_bullets" class="form-control font-monospace small" rows="3" placeholder="No credit card required&#10;Custom ROI Strategy&#10;Dedicated Digital Experts">{{ old('trust_bullets') }}</textarea>
                        <div class="form-text small">{{ __('Appears as checked feature bullet points below the action buttons.') }}</div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Settings -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fas fa-mouse-pointer text-primary me-2"></i>{{ __('Call to Action (CTA) Buttons') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">{{ __('Primary Button Text') }}</label>
                            <input type="text" name="primary_btn_text" class="form-control" value="{{ old('primary_btn_text', 'GET YOUR FREE PROPOSAL') }}" placeholder="e.g. GET YOUR FREE PROPOSAL">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">{{ __('Primary Button Action') }} <span class="text-danger">*</span></label>
                            <select name="primary_btn_action" id="primaryBtnAction" class="form-select" required>
                                <option value="open_proposal_modal" {{ old('primary_btn_action') === 'open_proposal_modal' ? 'selected' : '' }}>
                                    ✨ Opens 4-Step Free Proposal Modal
                                </option>
                                <option value="url" {{ old('primary_btn_action') === 'url' ? 'selected' : '' }}>
                                    🔗 Custom URL / Route Link
                                </option>
                                <option value="register" {{ old('primary_btn_action') === 'register' ? 'selected' : '' }}>
                                    🚀 Registration / Sign Up Page
                                </option>
                            </select>
                        </div>
                        <div class="col-12" id="primaryUrlWrapper">
                            <label class="form-label fw-bold text-dark small">{{ __('Primary Button URL') }}</label>
                            <input type="text" name="primary_btn_url" class="form-control" value="{{ old('primary_btn_url') }}" placeholder="e.g. /dashboard or #contact">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">{{ __('Secondary Button Text (Optional)') }}</label>
                            <input type="text" name="secondary_btn_text" class="form-control" value="{{ old('secondary_btn_text', 'Explore Our Services') }}" placeholder="e.g. Explore Our Services">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark small">{{ __('Secondary Button URL') }}</label>
                            <input type="text" name="secondary_btn_url" class="form-control" value="{{ old('secondary_btn_url', '/services') }}" placeholder="e.g. /services or #pricing">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Design, Images & Display Settings -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fas fa-image text-primary me-2"></i>{{ __('Images & Background') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Desktop Background Image') }}</label>
                        <input type="file" name="bg_image" class="form-control" accept="image/*">
                        <div class="form-text small">{{ __('Recommended 1920x1080px (JPG, PNG, WebP).') }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Mobile Background Image (Optional)') }}</label>
                        <input type="file" name="bg_image_mobile" class="form-control" accept="image/*">
                        <div class="form-text small">{{ __('Optimized portrait image for phones (800x1200px).') }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Optional Foreground Visual Image') }}</label>
                        <input type="file" name="fg_image" class="form-control" accept="image/*">
                        <div class="form-text small">{{ __('Image or illustration displayed alongside the text.') }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Background Color / Gradient') }}</label>
                        <input type="text" name="bg_color" class="form-control" value="{{ old('bg_color') }}" placeholder="#FFFFFF or linear-gradient(...)">
                    </div>
                </div>
            </div>

            <!-- Slide Behavior & Ordering -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="fas fa-cogs text-primary me-2"></i>{{ __('Slide Behavior & Layout') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Slide Type') }} <span class="text-danger">*</span></label>
                        <select name="slide_type" class="form-select" required>
                            <option value="marketing" {{ old('slide_type') === 'marketing' ? 'selected' : '' }}>
                                🚀 Marketing & Proposal Slide
                            </option>
                            <option value="existing_hero" {{ old('slide_type') === 'existing_hero' ? 'selected' : '' }}>
                                🏢 CreLogics ERP Preview (Hero Slide 1)
                            </option>
                            <option value="custom" {{ old('slide_type') === 'custom' ? 'selected' : '' }}>
                                🎨 Custom Presentation Slide
                            </option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small">{{ __('Text Alignment') }}</label>
                            <select name="text_align" class="form-select">
                                <option value="center" {{ old('text_align') === 'center' ? 'selected' : '' }}>Center</option>
                                <option value="left" {{ old('text_align') === 'left' ? 'selected' : '' }}>Left</option>
                                <option value="right" {{ old('text_align') === 'right' ? 'selected' : '' }}>Right</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small">{{ __('Content Position') }}</label>
                            <select name="content_position" class="form-select">
                                <option value="center" {{ old('content_position') === 'center' ? 'selected' : '' }}>Center</option>
                                <option value="left" {{ old('content_position') === 'left' ? 'selected' : '' }}>Left</option>
                                <option value="right" {{ old('content_position') === 'right' ? 'selected' : '' }}>Right</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small">{{ __('Animation') }}</label>
                            <select name="animation_type" class="form-select">
                                <option value="fade" {{ old('animation_type') === 'fade' ? 'selected' : '' }}>Smooth Fade</option>
                                <option value="slide" {{ old('animation_type') === 'slide' ? 'selected' : '' }}>Slide Horizontal</option>
                                <option value="zoom" {{ old('animation_type') === 'zoom' ? 'selected' : '' }}>Zoom Subtle</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small">{{ __('Duration (ms)') }}</label>
                            <input type="number" name="display_duration" class="form-control" value="{{ old('display_duration', 6000) }}" step="500" min="2000" max="30000" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-bold text-dark small">{{ __('Display Order') }}</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 3) }}" min="0" required>
                        </div>
                        <div class="col-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActiveSwitch" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold small text-dark" for="isActiveSwitch">{{ __('Active') }}</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-modern w-100 py-3">
                        <i class="fas fa-save me-1"></i> {{ __('Save & Publish Slide') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const actionSelect = document.getElementById('primaryBtnAction');
    const urlWrapper = document.getElementById('primaryUrlWrapper');

    function toggleUrl() {
        if (actionSelect.value === 'open_proposal_modal') {
            urlWrapper.style.opacity = '0.5';
        } else {
            urlWrapper.style.opacity = '1';
        }
    }
    actionSelect.addEventListener('change', toggleUrl);
    toggleUrl();
});
</script>
@endpush
@endsection
