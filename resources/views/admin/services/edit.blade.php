@extends('admin.layouts.app')

@section('title', __('Edit Service: ') . $service->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">{{ __('Edit Service: ') . $service->name }}</h2>
                <p class="text-muted small mb-0">{{ __('Update service offering details, pricing, headlines, and dynamic inner page content.') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('website.service_detail', $service->slug) }}" target="_blank" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center">
                    <i class="fas fa-external-link-alt me-1"></i> {{ __('View Live Page') }}
                </a>
                <a href="{{ route('services.index') }}" class="btn btn-light border btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Services') }}
                </a>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
            <form action="{{ route('services.update', $service) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- 1. Basic Details -->
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                    <i class="fas fa-info-circle text-primary me-2"></i> {{ __('1. Basic Service Information') }}
                </h5>

                <div class="row g-4 mb-4">
                    <!-- Service Category -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small">{{ __('Service Department / Category') }} <span class="text-danger">*</span></label>
                        <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ old('category', $service->category) === $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Service Name -->
                    <div class="col-md-5">
                        <label class="form-label fw-bold text-dark small">{{ __('Service Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $service->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Icon Class -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">{{ __('FontAwesome Icon') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="{{ $service->icon ?: 'fas fa-icons' }} text-muted"></i></span>
                            <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', $service->icon) }}" placeholder="fas fa-paint-brush">
                        </div>
                    </div>

                    <!-- Starting Price -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small">{{ __('Starting Price (Optional)') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">{{ $currentShop->currency_symbol ?? '$' }}</span>
                            <input type="number" step="0.01" name="starting_price" class="form-control @error('starting_price') is-invalid @enderror" value="{{ old('starting_price', $service->starting_price) }}" placeholder="0.00">
                        </div>
                    </div>

                    <!-- Price Unit -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small">{{ __('Pricing Unit') }}</label>
                        <input type="text" name="price_unit" class="form-control @error('price_unit') is-invalid @enderror" value="{{ old('price_unit', $service->price_unit) }}" placeholder="Project, Month, Hour, Job">
                    </div>

                    <!-- Sort Order & Active -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-dark small">{{ __('Sort Order') }}</label>
                        <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $service->sort_order) }}">
                    </div>

                    <div class="col-md-2 d-flex align-items-center">
                        <div class="form-check form-switch mt-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActiveCheck" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark small" for="isActiveCheck">
                                {{ __('Active') }}
                            </label>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark small">{{ __('Service Overview Paragraph') }}</label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $service->description) }}</textarea>
                    </div>
                </div>

                <!-- 2. Dynamic Inner Page Content (Matches Screenshot Layout) -->
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4">
                    <i class="fas fa-layer-group text-primary me-2"></i> {{ __('2. Dynamic Inner Page Content (crelogics.com Style)') }}
                </h5>

                <div class="row g-4 mb-4">
                    <!-- Sub-Tag (BEST FOR YOU) -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small">{{ __('Sub-Tag (Upper Label)') }}</label>
                        <input type="text" name="sub_tag" class="form-control" value="{{ old('sub_tag', $service->sub_tag ?: 'BEST FOR YOU') }}" placeholder="BEST FOR YOU">
                    </div>

                    <!-- Main Headline -->
                    <div class="col-md-5">
                        <label class="form-label fw-bold text-dark small">{{ __('Main Impact Headline') }}</label>
                        <input type="text" name="headline" class="form-control" value="{{ old('headline', $service->headline) }}" placeholder="e.g. Boosts Your Website Traffic!">
                    </div>

                    <!-- Badge Text -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Card Floating Badge') }}</label>
                        <input type="text" name="badge_text" class="form-control" value="{{ old('badge_text', $service->badge_text ?: 'Verified Quality') }}" placeholder="Verified Quality">
                    </div>

                    <!-- Feature Box 1 (Left Card) -->
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light">
                            <span class="badge bg-primary text-white mb-2">Feature Highlight Box 1 (Left)</span>
                            <div class="row g-2">
                                <div class="col-8">
                                    <label class="form-label small text-muted mb-1">Title</label>
                                    <input type="text" name="feature_1_title" class="form-control form-control-sm" value="{{ old('feature_1_title', $service->feature_1_title ?: 'Actionable Audit') }}" placeholder="Actionable Audit">
                                </div>
                                <div class="col-4">
                                    <label class="form-label small text-muted mb-1">Icon Class</label>
                                    <input type="text" name="feature_1_icon" class="form-control form-control-sm" value="{{ old('feature_1_icon', $service->feature_1_icon ?: 'fas fa-bullhorn') }}" placeholder="fas fa-bullhorn">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small text-muted mb-1">Subtitle / Subtext</label>
                                    <input type="text" name="feature_1_text" class="form-control form-control-sm" value="{{ old('feature_1_text', $service->feature_1_text ?: 'Detailed scoping & technical roadmap') }}" placeholder="Detailed scoping & technical roadmap">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feature Box 2 (Right Card) -->
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light">
                            <span class="badge bg-info text-white mb-2">Feature Highlight Box 2 (Right)</span>
                            <div class="row g-2">
                                <div class="col-8">
                                    <label class="form-label small text-muted mb-1">Title</label>
                                    <input type="text" name="feature_2_title" class="form-control form-control-sm" value="{{ old('feature_2_title', $service->feature_2_title ?: 'What We Charge') }}" placeholder="What We Charge">
                                </div>
                                <div class="col-4">
                                    <label class="form-label small text-muted mb-1">Icon Class</label>
                                    <input type="text" name="feature_2_icon" class="form-control form-control-sm" value="{{ old('feature_2_icon', $service->feature_2_icon ?: 'fas fa-chart-pie') }}" placeholder="fas fa-chart-pie">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small text-muted mb-1">Custom Rate / Pricing Text (Optional)</label>
                                    <input type="text" name="feature_2_text" class="form-control form-control-sm" value="{{ old('feature_2_text', $service->feature_2_text) }}" placeholder="Leave blank to use starting price">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Features (One per line) -->
                    @php
                        $featuresText = is_array($service->features) ? implode("\n", $service->features) : '';
                    @endphp
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark small">{{ __('Key Features / Deliverables Grid (One item per line)') }}</label>
                        <textarea name="features" rows="4" class="form-control @error('features') is-invalid @enderror">{{ old('features', $featuresText) }}</textarea>
                    </div>

                    <!-- Bottom CTA Banner Texts -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small">{{ __('Bottom CTA Title') }}</label>
                        <input type="text" name="cta_title" class="form-control" value="{{ old('cta_title', $service->cta_title) }}" placeholder="e.g. Ready to Accelerate with {{ $service->name }}?">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small">{{ __('Bottom CTA Subtitle') }}</label>
                        <input type="text" name="cta_subtitle" class="form-control" value="{{ old('cta_subtitle', $service->cta_subtitle) }}" placeholder="e.g. Contact Crelogics today to discuss your vision...">
                    </div>

                    <!-- Optional Custom Rich HTML Body -->
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark small">{{ __('Optional Additional Body Content (HTML Allowed)') }}</label>
                        <textarea name="content" rows="4" class="form-control @error('content') is-invalid @enderror" placeholder="Optional extra paragraphs, case studies, or custom text...">{{ old('content', $service->content) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <a href="{{ route('website.service_detail', $service->slug) }}" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-external-link-alt me-1"></i> {{ __('Preview Live Page') }}
                    </a>
                    <div class="d-flex gap-2">
                        <a href="{{ route('services.index') }}" class="btn btn-light px-4">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary-modern px-4" style="background: #FA7436; border-color: #FA7436;">
                            <i class="fas fa-save me-1"></i> {{ __('Save & Update Service') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
