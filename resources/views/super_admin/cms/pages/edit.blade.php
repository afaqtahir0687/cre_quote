@extends('admin.layouts.app')

@section('title', __('Edit Page: ') . $page->title)

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<style>
.note-editor.note-frame {
    border: 1px solid #E2E8F0 !important;
    border-radius: 12px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03) !important;
    overflow: hidden;
}
.note-toolbar {
    background: #F8FAFC !important;
    border-bottom: 1px solid #E2E8F0 !important;
    padding: 10px 12px !important;
}
.note-btn {
    border: 1px solid #E2E8F0 !important;
    background: #FFFFFF !important;
    border-radius: 6px !important;
    color: #475569 !important;
}
.note-btn:hover {
    background: #EEF2FF !important;
    color: #4361EE !important;
}
.note-editable {
    font-size: 1rem !important;
    line-height: 1.8 !important;
    color: #334155 !important;
    padding: 24px !important;
    min-height: 380px !important;
    background: #FFFFFF !important;
}
.note-statusbar {
    border-top: 1px solid #E2E8F0 !important;
    background: #F8FAFC !important;
}
</style>
@endpush

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Edit Page') }}: {{ $page->title }}</h2>
            <span class="badge-soft badge-soft-warning px-2 py-1 rounded-pill small">Editing</span>
        </div>
        <p class="text-muted small mb-0">{{ __('Modify page content, slug, placement, or publishing status.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('website.page', $page->slug) }}" target="_blank" class="btn btn-outline-info btn-sm">
            <i class="fas fa-external-link-alt me-1"></i> {{ __('View Live Page') }}
        </a>
        <a href="{{ route('super_admin.cms.pages.index') }}" class="btn btn-secondary-modern btn-sm">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Pages List') }}
        </a>
    </div>
</div>

@if(isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
        <strong class="d-block mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Please fix the following errors:</strong>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('super_admin.cms.pages.update', $page->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-4">
        <!-- Main Content (Left 8 cols) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-edit text-primary me-2"></i> {{ __('Page Details & Content') }}</h5>

                <!-- Title -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">{{ __('Page Title') }} <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="pageTitle" class="form-control form-control-lg @error('title') is-invalid @enderror" 
                           value="{{ old('title', $page->title) }}" required>
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Subtitle / Tagline -->
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">{{ __('Hero Subtitle / Tagline') }}</label>
                    <input type="text" name="subtitle" class="form-control @error('subtitle') is-invalid @enderror" 
                           value="{{ old('subtitle', $page->subtitle) }}" placeholder="Short summary displayed under the title in the page header hero">
                    <div class="form-text small">{{ __('Optional. Displayed as a lead paragraph in the header banner.') }}</div>
                    @error('subtitle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Content Body -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold text-dark m-0">{{ __('Page Body Content') }} <span class="text-danger">*</span></label>
                        <span class="badge bg-light text-muted border px-2 py-1 small">
                            <i class="fas fa-check-circle text-success me-1"></i> Visual WYSIWYG
                        </span>
                    </div>

                    <div class="alert alert-light border p-2 px-3 rounded-3 mb-2 small text-muted d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle text-primary"></i>
                        <span>Aapko koi <code>&lt;div&gt;</code> ya CSS styles likhne ki zaroorat nahi hai. Design code mein auto-applied hai — yahan sirf normal text, headings aur bullet points likhein.</span>
                    </div>

                    <textarea name="content" id="pageContent" class="form-control @error('content') is-invalid @enderror" 
                              required>{{ old('content', $page->content) }}</textarea>
                    @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- SEO Settings Card -->
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-search text-success me-2"></i> {{ __('Search Engine Optimization (SEO)') }}</h5>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">{{ __('Meta Title') }}</label>
                    <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $page->meta_title) }}">
                </div>
                <div class="mb-0">
                    <label class="form-label fw-semibold text-dark">{{ __('Meta Description') }}</label>
                    <textarea name="meta_description" rows="2" class="form-control">{{ old('meta_description', $page->meta_description) }}</textarea>
                </div>
            </div>
        </div>

        <!-- Right Settings Sidebar (4 cols) -->
        <div class="col-lg-4">
            <!-- Publishing Controls -->
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="fas fa-globe text-primary me-2"></i> {{ __('Publishing Settings') }}</h5>

                <!-- Slug / URL -->
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">{{ __('URL Slug') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted small">/page/</span>
                        <input type="text" name="slug" id="pageSlug" class="form-control @error('slug') is-invalid @enderror" 
                               value="{{ old('slug', $page->slug) }}" required>
                    </div>
                    <div class="form-text small">{{ __('Used to form the public URL.') }}</div>
                    @error('slug') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <!-- Footer Placement -->
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">{{ __('Footer Column Placement') }} <span class="text-danger">*</span></label>
                    <select name="footer_column" class="form-select @error('footer_column') is-invalid @enderror" required>
                        <option value="company" {{ old('footer_column', $page->footer_column) === 'company' ? 'selected' : '' }}>
                            🏢 Company (Col 2: About, Contact, Services)
                        </option>
                        <option value="legal" {{ old('footer_column', $page->footer_column) === 'legal' ? 'selected' : '' }}>
                            ⚖️ Legal & Trust (Col 4: Privacy, Terms, Refund)
                        </option>
                        <option value="quick_links" {{ old('footer_column', $page->footer_column) === 'quick_links' ? 'selected' : '' }}>
                            🔗 Quick Navigation
                        </option>
                        <option value="none" {{ old('footer_column', $page->footer_column) === 'none' ? 'selected' : '' }}>
                            🚫 Hidden from Footer (Accessible via direct link only)
                        </option>
                    </select>
                    @error('footer_column') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Sort Order -->
                <div class="mb-3">
                    <label class="form-label fw-semibold text-dark">{{ __('Display Order') }} <span class="text-danger">*</span></label>
                    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" 
                           value="{{ old('sort_order', $page->sort_order) }}" min="1" required>
                    <div class="form-text small">{{ __('Lower numbers appear first in lists.') }}</div>
                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Active Status -->
                <div class="form-check form-switch mb-4">
                    <input type="checkbox" name="is_active" id="isActiveSwitch" class="form-check-input" value="1" 
                           {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold text-dark" for="isActiveSwitch">
                        {{ __('Active & Published') }}
                    </label>
                    <div class="form-text small">{{ __('When unchecked, this page is hidden from visitors.') }}</div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary-modern py-2">
                        <i class="fas fa-save me-1"></i> {{ __('Update Page') }}
                    </button>
                    <a href="{{ route('super_admin.cms.pages.index') }}" class="btn btn-outline-secondary py-2">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </div>

            <!-- Page Meta Info -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-light">
                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-info-circle text-info me-1"></i> Page Information</h6>
                <div class="small text-muted mb-1"><strong>Created:</strong> {{ $page->created_at->format('M d, Y H:i') }}</div>
                <div class="small text-muted mb-1"><strong>Last Updated:</strong> {{ $page->updated_at->format('M d, Y H:i') }}</div>
                <div class="small text-muted"><strong>Public Link:</strong> <a href="{{ route('website.page', $page->slug) }}" target="_blank" class="text-primary text-decoration-none">Open Link</a></div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
$(document).ready(function() {
    $('#pageContent').summernote({
        placeholder: 'Yahan apna page content type karein...',
        tabsize: 2,
        height: 420,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'hr']],
            ['view', ['fullscreen', 'codeview']]
        ],
        styleTags: [
            { title: 'Normal Paragraph', tag: 'p', className: '' },
            { title: 'Heading 2', tag: 'h2', className: '' },
            { title: 'Heading 3', tag: 'h3', className: '' },
            { title: 'Heading 4', tag: 'h4', className: '' },
            { title: 'Quote Callout', tag: 'blockquote', className: '' }
        ]
    });
});
</script>
@endpush
