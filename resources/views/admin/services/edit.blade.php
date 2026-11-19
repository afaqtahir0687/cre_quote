@extends('admin.layouts.app')

@section('title', __('Edit Service: ') . $service->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-11">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">{{ __('Edit Service: ') . $service->name }}</h2>
                <p class="text-muted small mb-0">{{ __('Update service offering details, pricing, headlines, and dynamic inner page content for both English and Arabic.') }}</p>
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
                    <div class="col-md-3">
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

                    <!-- Service Name (EN) -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark small">🇬🇧 {{ __('Service Name') }} (EN) <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $service->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Service Name (AR) -->
                    <div class="col-md-5">
                        <label class="form-label fw-bold text-dark small">🇸🇦 اسم الخدمة بالعربية (Arabic Name)</label>
                        <input type="text" name="name_ar" class="form-control text-end @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $service->name_ar) }}" dir="rtl" placeholder="مثال: خدمات التصميم الجرافيكي">
                        @error('name_ar')
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
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Starting Price (Optional)') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">{{ $currentShop->currency_symbol ?? '$' }}</span>
                            <input type="number" step="0.01" name="starting_price" class="form-control @error('starting_price') is-invalid @enderror" value="{{ old('starting_price', $service->starting_price) }}" placeholder="0.00">
                        </div>
                    </div>

                    <!-- Price Unit -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Pricing Unit') }}</label>
                        <input type="text" name="price_unit" class="form-control @error('price_unit') is-invalid @enderror" value="{{ old('price_unit', $service->price_unit) }}" placeholder="Project, Month, Hour, Job">
                    </div>

                    <!-- Sort Order & Active -->
                    <div class="col-md-1">
                        <label class="form-label fw-bold text-dark small">{{ __('Order') }}</label>
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

                    <!-- Description (EN) -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small">🇬🇧 {{ __('Service Overview Paragraph') }} (EN)</label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $service->description) }}</textarea>
                    </div>

                    <!-- Description (AR) -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small">🇸🇦 نبذة عن الخدمة بالعربية (Arabic Description)</label>
                        <textarea name="description_ar" rows="3" class="form-control text-end @error('description_ar') is-invalid @enderror" dir="rtl" placeholder="وصف الخدمة الذي يظهر في بطاقة العرض الرئيسية باللغة العربية...">{{ old('description_ar', $service->description_ar) }}</textarea>
                    </div>
                </div>

                <!-- 2. Dynamic Inner Page Content -->
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4">
                    <i class="fas fa-layer-group text-primary me-2"></i> {{ __('2. Dynamic Inner Page Content (English & Arabic)') }}
                </h5>

                <div class="row g-4 mb-4">
                    <!-- Sub-Tag (EN) -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">🇬🇧 {{ __('Sub-Tag (Upper Label)') }} (EN)</label>
                        <input type="text" name="sub_tag" class="form-control" value="{{ old('sub_tag', $service->sub_tag ?: 'BEST FOR YOU') }}" placeholder="BEST FOR YOU">
                    </div>
                    <!-- Sub-Tag (AR) -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">🇸🇦 الشعار العلوي بالعربية (AR)</label>
                        <input type="text" name="sub_tag_ar" class="form-control text-end" dir="rtl" value="{{ old('sub_tag_ar', $service->sub_tag_ar) }}" placeholder="مثال: الخيار الأفضل لعملك">
                    </div>

                    <!-- Main Headline (EN) -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">🇬🇧 {{ __('Main Impact Headline') }} (EN)</label>
                        <input type="text" name="headline" class="form-control" value="{{ old('headline', $service->headline) }}" placeholder="e.g. Boosts Your Website Traffic!">
                    </div>
                    <!-- Main Headline (AR) -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">🇸🇦 العنوان الرئيسي بالعربية (AR)</label>
                        <input type="text" name="headline_ar" class="form-control text-end" dir="rtl" value="{{ old('headline_ar', $service->headline_ar) }}" placeholder="مثال: تعزيز المبيعات والانتشار التجاري">
                    </div>

                    <!-- Badge Text (EN) -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small">🇬🇧 {{ __('Card Floating Badge') }} (EN)</label>
                        <input type="text" name="badge_text" class="form-control" value="{{ old('badge_text', $service->badge_text ?: 'Verified Quality') }}" placeholder="Verified Quality">
                    </div>
                    <!-- Badge Text (AR) -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small">🇸🇦 نص الشارة العائمة بالعربية (AR)</label>
                        <input type="text" name="badge_text_ar" class="form-control text-end" dir="rtl" value="{{ old('badge_text_ar', $service->badge_text_ar) }}" placeholder="مثال: جودة تنفيذ معتمدة">
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

                    <!-- Features (One per line) EN -->
                    @php
                        $featuresText = is_array($service->features) ? implode("\n", $service->features) : '';
                        $featuresArText = is_array($service->features_ar) ? implode("\n", $service->features_ar) : '';
                    @endphp
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small">🇬🇧 {{ __('Key Features Grid (EN - One per line)') }}</label>
                        <textarea name="features" rows="4" class="form-control @error('features') is-invalid @enderror">{{ old('features', $featuresText) }}</textarea>
                    </div>
                    <!-- Features (One per line) AR -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small">🇸🇦 قائمة المزايا والتسليمات بالعربية (سطر لكل بند)</label>
                        <textarea name="features_ar" rows="4" class="form-control text-end @error('features_ar') is-invalid @enderror" dir="rtl" placeholder="سطر لكل ميزة...">{{ old('features_ar', $featuresArText) }}</textarea>
                    </div>

                    <!-- Bottom CTA Banner Texts -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">🇬🇧 {{ __('Bottom CTA Title') }} (EN)</label>
                        <input type="text" name="cta_title" class="form-control" value="{{ old('cta_title', $service->cta_title) }}" placeholder="e.g. Ready to Accelerate?">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">🇸🇦 عنوان الدعوة للإجراء (AR)</label>
                        <input type="text" name="cta_title_ar" class="form-control text-end" dir="rtl" value="{{ old('cta_title_ar', $service->cta_title_ar) }}" placeholder="مثال: جاهز لبدء مشروعك الآن؟">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">🇬🇧 {{ __('Bottom CTA Subtitle') }} (EN)</label>
                        <input type="text" name="cta_subtitle" class="form-control" value="{{ old('cta_subtitle', $service->cta_subtitle) }}" placeholder="e.g. Contact Crelogics today...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark small">🇸🇦 النص الفرعي للدعوة (AR)</label>
                        <input type="text" name="cta_subtitle_ar" class="form-control text-end" dir="rtl" value="{{ old('cta_subtitle_ar', $service->cta_subtitle_ar) }}" placeholder="مثال: تواصل معنا اليوم لتحويل فكرتك إلى واقع...">
                    </div>

                    <!-- Optional Custom Rich HTML Body -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small">🇬🇧 {{ __('Additional Body Content') }} (EN)</label>
                        <textarea name="content" rows="4" class="form-control @error('content') is-invalid @enderror" placeholder="Optional extra paragraphs, case studies, or custom text...">{{ old('content', $service->content) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small">🇸🇦 تفاصيل إضافية للمحتوى بالعربية (AR)</label>
                        <textarea name="content_ar" rows="4" class="form-control text-end @error('content_ar') is-invalid @enderror" dir="rtl" placeholder="محتوى إضافي أو فقرات مخصصة باللغة العربية...">{{ old('content_ar', $service->content_ar) }}</textarea>
                    </div>
                </div>

                <!-- 3. Dynamic 4-Stage Project Lifecycle / Methodology Steps -->
                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4">
                    <i class="fas fa-tasks text-primary me-2"></i> {{ __('3. 4-Stage Project Lifecycle Steps (English & Arabic)') }}
                </h5>
                <p class="text-muted small mb-3">{{ __('Customize the 4 execution milestone cards displayed on the service detail page.') }}</p>

                @php
                    $enSteps = is_array($service->process_steps) ? $service->process_steps : [];
                    $arSteps = is_array($service->process_steps_ar) ? $service->process_steps_ar : [];
                @endphp

                <div class="row g-3 mb-4">
                    @for($i = 0; $i < 4; $i++)
                        @php
                            $stepNum = $i + 1;
                            $curEnTitle = $enSteps[$i]['title'] ?? '';
                            $curEnDesc = $enSteps[$i]['desc'] ?? '';
                            $curArTitle = $arSteps[$i]['title'] ?? '';
                            $curArDesc = $arSteps[$i]['desc'] ?? '';
                        @endphp
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border bg-light h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-primary">Stage / المرحلة #{{ $stepNum }}</span>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label extra-small text-muted mb-1">🇬🇧 Stage {{ $stepNum }} Title (EN)</label>
                                    <input type="text" name="steps[{{ $i }}][title_en]" class="form-control form-control-sm" value="{{ old("steps.$i.title_en", $curEnTitle) }}" placeholder="Stage {{ $stepNum }} Title">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label extra-small text-muted mb-1">🇸🇦 عنوان المرحلة {{ $stepNum }} بالعربية (AR)</label>
                                    <input type="text" name="steps[{{ $i }}][title_ar]" class="form-control form-control-sm text-end" dir="rtl" value="{{ old("steps.$i.title_ar", $curArTitle) }}" placeholder="عنوان المرحلة بالعربية">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label extra-small text-muted mb-1">🇬🇧 Stage {{ $stepNum }} Description (EN)</label>
                                    <textarea name="steps[{{ $i }}][desc_en]" rows="2" class="form-control form-control-sm" placeholder="Short description in English...">{{ old("steps.$i.desc_en", $curEnDesc) }}</textarea>
                                </div>
                                <div>
                                    <label class="form-label extra-small text-muted mb-1">🇸🇦 وصف المرحلة {{ $stepNum }} بالعربية (AR)</label>
                                    <textarea name="steps[{{ $i }}][desc_ar]" rows="2" class="form-control form-control-sm text-end" dir="rtl" placeholder="وصف موجز للمرحلة بالعربية...">{{ old("steps.$i.desc_ar", $curArDesc) }}</textarea>
                                </div>
                            </div>
                        </div>
                    @endfor
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
