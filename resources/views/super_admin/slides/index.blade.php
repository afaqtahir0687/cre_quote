@extends('admin.layouts.app')

@section('title', __('Homepage Hero Slider Management'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Homepage Hero Slider') }}</h2>
            <span class="badge-soft badge-soft-primary px-2 py-1 rounded-pill small">
                <i class="fas fa-sliders-h me-1"></i> {{ $slides->where('is_active', true)->count() }} {{ __('Active Slides') }}
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Manage dynamic homepage hero slides, ordering, background images, text, and button actions.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.cms.slides.create') }}" class="btn btn-primary-modern btn-sm">
            <i class="fas fa-plus me-1"></i> {{ __('Add New Slide') }}
        </a>
        <a href="{{ route('super_admin.cms.index') }}" class="btn btn-secondary-modern btn-sm">
            <i class="fas fa-arrow-left me-1"></i> {{ __('CMS Hub') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold text-dark small text-uppercase">
            <i class="fas fa-layer-group text-primary me-2"></i> {{ __('All Homepage Slides') }} ({{ $slides->count() }})
        </span>
        <span class="text-muted small">
            <i class="fas fa-info-circle me-1 text-primary"></i> {{ __('Drag & drop or edit order to arrange slides on the public landing page.') }}
        </span>
    </div>

    <div class="table-responsive">
        <table class="table-modern align-middle" id="slidesTable">
            <thead>
                <tr>
                    <th style="width: 70px;" class="text-center">{{ __('Order') }}</th>
                    <th style="width: 140px;">{{ __('Preview') }}</th>
                    <th>{{ __('Slide Title & Content') }}</th>
                    <th>{{ __('Primary Action') }}</th>
                    <th style="width: 120px;">{{ __('Duration') }}</th>
                    <th style="width: 110px;" class="text-center">{{ __('Status') }}</th>
                    <th style="width: 130px;" class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody id="sortableSlides">
                @forelse($slides as $slide)
                    <tr data-id="{{ $slide->id }}" class="slide-row">
                        <td class="text-center">
                            <span class="badge-soft badge-soft-secondary fw-bold">
                                #{{ $slide->sort_order }}
                            </span>
                        </td>
                        <td>
                            <div class="slide-thumb rounded-3 overflow-hidden border d-flex align-items-center justify-content-center" style="width: 120px; height: 68px; background: {{ $slide->bg_color ?: '#F8FAFC' }};">
                                @if($slide->bg_image)
                                    <img src="{{ asset($slide->bg_image) }}" alt="{{ $slide->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @elseif($slide->isExistingHero())
                                    <div class="text-center p-1">
                                        <i class="fas fa-cubes text-primary fs-5 d-block"></i>
                                        <span class="text-muted" style="font-size: 0.65rem;">ERP Hero Mockup</span>
                                    </div>
                                @else
                                    <div class="text-center p-1">
                                        <i class="fas fa-rocket text-success fs-5 d-block"></i>
                                        <span class="text-muted" style="font-size: 0.65rem;">Marketing Slide</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-bold text-dark">{{ $slide->title }}</span>
                                @if($slide->isExistingHero())
                                    <span class="badge-soft badge-soft-primary" style="font-size: 0.7rem;">
                                        <i class="fas fa-star me-1"></i> Slide 1 (ERP Preserved)
                                    </span>
                                @elseif($slide->opensProposalModal())
                                    <span class="badge-soft badge-soft-success" style="font-size: 0.7rem;">
                                        <i class="fas fa-file-signature me-1"></i> Proposal Slide
                                    </span>
                                @endif
                            </div>
                            <div class="text-dark fw-semibold small mb-1">
                                {{ $slide->heading }} <span class="text-primary">{{ $slide->heading_highlight }}</span>
                            </div>
                            <p class="text-muted small mb-0 text-truncate" style="max-width: 480px;">
                                {{ $slide->description }}
                            </p>
                        </td>
                        <td>
                            @if($slide->primary_btn_text)
                                <div class="small fw-bold text-dark mb-1">
                                    <i class="fas fa-mouse-pointer text-muted me-1"></i> {{ $slide->primary_btn_text }}
                                </div>
                                @if($slide->opensProposalModal())
                                    <span class="badge-soft badge-soft-warning" style="font-size: 0.72rem;">
                                        <i class="fas fa-window-restore me-1"></i> Opens 4-Step Modal
                                    </span>
                                @else
                                    <span class="text-muted small font-monospace" style="font-size: 0.75rem;">
                                        {{ $slide->primary_btn_url ?: '#' }}
                                    </span>
                                @endif
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-soft badge-soft-secondary">
                                <i class="fas fa-clock text-muted me-1"></i> {{ number_format($slide->display_duration / 1000, 1) }}s
                            </span>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('super_admin.cms.slides.toggle_active', $slide->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="border-0 bg-transparent p-0 shadow-none cursor-pointer" title="{{ $slide->is_active ? __('Click to deactivate') : __('Click to activate') }}">
                                    @if($slide->is_active)
                                        <span class="badge-soft badge-soft-success">
                                            <i class="fas fa-check-circle me-1"></i> {{ __('Active') }}
                                        </span>
                                    @else
                                        <span class="badge-soft badge-soft-secondary">
                                            <i class="fas fa-eye-slash me-1"></i> {{ __('Inactive') }}
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('super_admin.cms.slides.edit', $slide->id) }}" class="btn-icon-action" title="{{ __('Edit Slide') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($slides->count() > 1)
                                    <form action="{{ route('super_admin.cms.slides.destroy', $slide->id) }}" method="POST" class="d-inline delete-form" data-confirm="{{ __('Are you sure you want to delete this slide?') }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon-action btn-action-delete" title="{{ __('Delete Slide') }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-sliders-h fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-0">{{ __('No homepage slides found. Click "Add New Slide" to create one.') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Drag & Drop Sorting Info Banner -->
<div class="card border-0 shadow-sm rounded-4 bg-light">
    <div class="card-body p-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <div class="p-2 rounded-circle bg-white text-primary border">
                <i class="fas fa-arrows-alt"></i>
            </div>
            <div>
                <strong class="text-dark small d-block">{{ __('Drag & Drop Reordering') }}</strong>
                <span class="text-muted small">{{ __('Drag slide rows up or down to update display order on the public homepage slider.') }}</span>
            </div>
        </div>
        <button type="button" class="btn btn-secondary-modern btn-sm" id="saveOrderBtn" style="display: none;">
            <i class="fas fa-save me-1"></i> {{ __('Save Order') }}
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var sortableEl = document.getElementById('sortableSlides');
    if (!sortableEl) return;

    var sortable = new Sortable(sortableEl, {
        animation: 150,
        handle: '.slide-row',
        ghostClass: 'bg-light-primary',
        onEnd: function() {
            var order = [];
            document.querySelectorAll('#sortableSlides .slide-row').forEach(function(row) {
                order.push(row.getAttribute('data-id'));
            });

            fetch("{{ route('super_admin.cms.slides.reorder') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ order: order })
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message || 'Slide order saved!',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    }
                }
            })
            .catch(function(err) {
                console.error('Error saving slide order:', err);
            });
        }
    });
});
</script>
@endpush
