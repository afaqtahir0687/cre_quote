@extends('admin.layouts.app')

@section('title', __('Website Settings & Hero Management'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Website Settings & Hero Management') }}</h2>
            <span class="badge-soft badge-soft-primary px-2 py-1 rounded-pill small">
                CMS Settings
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Update global branding, contact details, social links, SEO meta tags, and hero section content.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.cms.index') }}" class="btn btn-secondary-modern btn-sm">
            <i class="fas fa-arrow-left me-1"></i> {{ __('CMS Hub') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('super_admin.cms.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-4">
        <!-- 1. Hero Section Management -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold text-primary mb-0"><i class="fas fa-bullhorn me-2"></i> {{ __('Hero Section Content') }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">{{ __('Top Pill Badge') }}</label>
                            <input type="text" name="hero_badge" class="form-control" value="{{ $settings['hero_badge'] ?? 'Complete Cloud Business ERP Solution' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Main Headline (First Part)') }}</label>
                            <input type="text" name="hero_headline" class="form-control" value="{{ $settings['hero_headline'] ?? 'Run Your Entire Business From' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Highlighted Headline (Gradient Text)') }}</label>
                            <input type="text" name="hero_headline_highlight" class="form-control" value="{{ $settings['hero_headline_highlight'] ?? 'One Intelligent System' }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">{{ __('Subheading Lead Paragraph') }}</label>
                            <textarea name="hero_subheading" class="form-control" rows="3" required>{{ $settings['hero_subheading'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">{{ __('Primary Button Text') }}</label>
                            <input type="text" name="hero_primary_btn_text" class="form-control" value="{{ $settings['hero_primary_btn_text'] ?? 'Start Free 7-Day Trial' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">{{ __('Primary Button URL') }}</label>
                            <input type="text" name="hero_primary_btn_url" class="form-control" value="{{ $settings['hero_primary_btn_url'] ?? '/register' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">{{ __('Secondary Button Text') }}</label>
                            <input type="text" name="hero_secondary_btn_text" class="form-control" value="{{ $settings['hero_secondary_btn_text'] ?? 'Explore Live Workflow' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">{{ __('Secondary Button URL') }}</label>
                            <input type="text" name="hero_secondary_btn_url" class="form-control" value="{{ $settings['hero_secondary_btn_url'] ?? '#showcase' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Social Proof Mosaic Settings -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-users me-2"></i> {{ __('Social Proof Mosaic Settings') }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('User/Business Count Source') }}</label>
                            <select name="social_proof_metric_mode" class="form-select">
                                <option value="dynamic" {{ ($settings['social_proof_metric_mode'] ?? '') === 'dynamic' ? 'selected' : '' }}>
                                    {{ __('Dynamic Database Count (Actual Registered Businesses)') }}
                                </option>
                                <option value="manual" {{ ($settings['social_proof_metric_mode'] ?? '') === 'manual' ? 'selected' : '' }}>
                                    {{ __('Manual Count Override (Custom Number)') }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('Manual Count (When Override Selected)') }}</label>
                            <input type="number" name="social_proof_manual_count" class="form-control" value="{{ $settings['social_proof_manual_count'] ?? '28' }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">{{ __('Handwritten Note') }}</label>
                            <input type="text" name="social_proof_handwritten_note" class="form-control" value="{{ $settings['social_proof_handwritten_note'] ?? 'happy' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Headline Entity Name') }}</label>
                            <input type="text" name="social_proof_heading" class="form-control" value="{{ $settings['social_proof_heading'] ?? 'businesses' }}" placeholder="e.g. businesses or users">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Supporting Subtitle') }}</label>
                            <input type="text" name="social_proof_subheading" class="form-control" value="{{ $settings['social_proof_subheading'] ?? 'who grow and manage their business with Crelogics ERP' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. General Information & SEO -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-info-circle me-2"></i> {{ __('Site Information & SEO') }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Website / App Name') }}</label>
                            <input type="text" name="site_name" class="form-control" value="{{ $settings['site_name'] ?? 'Crelogics ERP' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Company Legal Name') }}</label>
                            <input type="text" name="company_name" class="form-control" value="{{ $settings['company_name'] ?? 'Crelogics ERP' }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">{{ __('Website Tagline') }}</label>
                            <input type="text" name="tagline" class="form-control" value="{{ $settings['tagline'] ?? 'All-In-One Cloud Business ERP Solution' }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">{{ __('Default Meta Title') }}</label>
                            <input type="text" name="meta_title" class="form-control" value="{{ $settings['meta_title'] ?? '' }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">{{ __('Default Meta Description') }}</label>
                            <textarea name="meta_description" class="form-control" rows="3">{{ $settings['meta_description'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Contact Details & Social Links -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-address-book me-2"></i> {{ __('Contact Information & Socials') }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Contact Email') }}</label>
                            <input type="email" name="contact_email" class="form-control" value="{{ $settings['contact_email'] ?? 'support@crelogics.com' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Contact Phone') }}</label>
                            <input type="text" name="contact_phone" class="form-control" value="{{ $settings['contact_phone'] ?? '+996 598 500 143' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('WhatsApp Number (without +)') }}</label>
                            <input type="text" name="whatsapp_number" class="form-control" value="{{ $settings['whatsapp_number'] ?? '996598500143' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Company Address') }}</label>
                            <input type="text" name="company_address" class="form-control" value="{{ $settings['company_address'] ?? 'Business District, Modern Industrial Zone' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Facebook URL') }}</label>
                            <input type="url" name="facebook_url" class="form-control" value="{{ $settings['facebook_url'] ?? '#' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('Instagram URL') }}</label>
                            <input type="url" name="instagram_url" class="form-control" value="{{ $settings['instagram_url'] ?? '#' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('LinkedIn URL') }}</label>
                            <input type="url" name="linkedin_url" class="form-control" value="{{ $settings['linkedin_url'] ?? '#' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">{{ __('YouTube URL') }}</label>
                            <input type="url" name="youtube_url" class="form-control" value="{{ $settings['youtube_url'] ?? '#' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Footer & Copyright -->
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-shoe-prints me-2"></i> {{ __('Footer Information') }}</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">{{ __('Footer Description') }}</label>
                            <textarea name="footer_description" class="form-control" rows="2">{{ $settings['footer_description'] ?? '' }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">{{ __('Copyright Notice') }}</label>
                            <input type="text" name="copyright_text" class="form-control" value="{{ $settings['copyright_text'] ?? '' }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Floating Bar -->
    <div class="mt-4 text-end">
        <button type="submit" class="btn btn-primary-modern btn-lg px-5 shadow-sm">
            <i class="fas fa-save me-1"></i> {{ __('Save All Changes') }}
        </button>
    </div>
</form>
@endsection

