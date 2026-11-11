@extends('admin.layouts.app')

@section('title', __('Proposal Form Settings & Options Management'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Proposal Form Settings & Dropdown Options') }}</h2>
            <span class="badge-soft badge-soft-primary px-2 py-1 rounded-pill small">
                <i class="fas fa-sliders-h me-1"></i> 4-Step Wizard Control
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Customize modal headlines, notification email, and all dynamic options for Services, Locations, Goals, and Budgets.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.proposals.index') }}" class="btn btn-secondary-modern btn-sm">
            <i class="fas fa-inbox text-primary me-1"></i> {{ __('View Proposal Requests') }}
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

<div class="row g-4">
    <!-- 1. General Modal Settings -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-3">
                <h5 class="fw-bold text-dark mb-0 small text-uppercase">
                    <i class="fas fa-window-restore text-primary me-2"></i>{{ __('Modal Text & Email Config') }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('super_admin.cms.proposals.settings.update') }}" method="POST">
                    @csrf

                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="proposal_form_enabled" id="formEnabledSwitch" value="1" {{ ($settings['proposal_form_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-dark small" for="formEnabledSwitch">
                                {{ __('Enable 4-Step Proposal Popup Form') }}
                            </label>
                        </div>
                        <div class="form-text small text-muted mt-1">When disabled, clicking proposal buttons will prompt visitors to contact via email/phone.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Modal Heading / Title') }} <span class="text-danger">*</span></label>
                        <input type="text" name="proposal_modal_title" class="form-control" value="{{ old('proposal_modal_title', $settings['proposal_modal_title'] ?? 'Get Your Free Custom Proposal') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Modal Introductory Subtitle') }}</label>
                        <textarea name="proposal_modal_subtitle" class="form-control" rows="3">{{ old('proposal_modal_subtitle', $settings['proposal_modal_subtitle'] ?? 'Answer 4 quick questions to receive a tailored digital roadmap, budget recommendations, and project timeline.') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Notification Email Address') }}</label>
                        <input type="email" name="proposal_notification_email" class="form-control" value="{{ old('proposal_notification_email', $settings['proposal_notification_email'] ?? 'creelogics@gmail.com') }}" placeholder="admin@crelogics.com">
                        <div class="form-text small">New proposal alerts will be dispatched directly to this inbox.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Submit Button Text') }} <span class="text-danger">*</span></label>
                        <input type="text" name="proposal_submit_btn_text" class="form-control" value="{{ old('proposal_submit_btn_text', $settings['proposal_submit_btn_text'] ?? 'SEND PROPOSAL') }}" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small">{{ __('Success Message') }} <span class="text-danger">*</span></label>
                        <textarea name="proposal_success_message" class="form-control" rows="3" required>{{ old('proposal_success_message', $settings['proposal_success_message'] ?? 'Thank you! Your proposal request has been submitted successfully. Our team will contact you shortly.') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary-modern w-100 py-2">
                        <i class="fas fa-save me-1"></i> {{ __('Save Proposal Settings') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. Dynamic Dropdown Options Tabs -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-bottom p-3">
                <ul class="nav nav-pills card-header-pills gap-1" id="optionsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active small py-2 px-3 fw-bold" id="services-tab" data-bs-toggle="tab" data-bs-target="#tab-services" type="button" role="tab">
                            <i class="fas fa-cubes me-1"></i> {{ __('Services') }} ({{ $services->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link small py-2 px-3 fw-bold" id="locations-tab" data-bs-toggle="tab" data-bs-target="#tab-locations" type="button" role="tab">
                            <i class="fas fa-map-marker-alt me-1"></i> {{ __('Locations') }} ({{ $locations->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link small py-2 px-3 fw-bold" id="goals-tab" data-bs-toggle="tab" data-bs-target="#tab-goals" type="button" role="tab">
                            <i class="fas fa-bullseye me-1"></i> {{ __('Goals') }} ({{ $goals->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link small py-2 px-3 fw-bold" id="budgets-tab" data-bs-toggle="tab" data-bs-target="#tab-budgets" type="button" role="tab">
                            <i class="fas fa-wallet me-1"></i> {{ __('Budgets') }} ({{ $budgets->count() }})
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link small py-2 px-3 fw-bold" id="timelines-tab" data-bs-toggle="tab" data-bs-target="#tab-timelines" type="button" role="tab">
                            <i class="fas fa-bolt me-1"></i> {{ __('Timelines') }} ({{ $timelines->count() }})
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content" id="optionsTabsContent">
                    
                    {{-- TAB 1: SERVICES --}}
                    <div class="tab-pane fade show active" id="tab-services" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0 small text-uppercase">{{ __('Step 1: Primary Services Options') }}</h6>
                            <button type="button" class="btn btn-sm btn-primary-modern" data-bs-toggle="modal" data-bs-target="#addOptionModal" data-type="service" data-title="Add New Service">
                                <i class="fas fa-plus me-1"></i> {{ __('Add Service') }}
                            </button>
                        </div>
                        @include('super_admin.proposals.partials.options_table', ['options' => $services, 'type' => 'service'])
                    </div>

                    {{-- TAB 2: LOCATIONS --}}
                    <div class="tab-pane fade" id="tab-locations" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0 small text-uppercase">{{ __('Step 2: Customer Target Locations') }}</h6>
                            <button type="button" class="btn btn-sm btn-primary-modern" data-bs-toggle="modal" data-bs-target="#addOptionModal" data-type="location" data-title="Add New Location">
                                <i class="fas fa-plus me-1"></i> {{ __('Add Location') }}
                            </button>
                        </div>
                        @include('super_admin.proposals.partials.options_table', ['options' => $locations, 'type' => 'location'])
                    </div>

                    {{-- TAB 3: GOALS --}}
                    <div class="tab-pane fade" id="tab-goals" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0 small text-uppercase">{{ __('Step 2: Campaign Goals') }}</h6>
                            <button type="button" class="btn btn-sm btn-primary-modern" data-bs-toggle="modal" data-bs-target="#addOptionModal" data-type="goal" data-title="Add New Goal">
                                <i class="fas fa-plus me-1"></i> {{ __('Add Goal') }}
                            </button>
                        </div>
                        @include('super_admin.proposals.partials.options_table', ['options' => $goals, 'type' => 'goal'])
                    </div>

                    {{-- TAB 4: BUDGETS --}}
                    <div class="tab-pane fade" id="tab-budgets" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0 small text-uppercase">{{ __('Step 3: Approximate Monthly Budgets') }}</h6>
                            <button type="button" class="btn btn-sm btn-primary-modern" data-bs-toggle="modal" data-bs-target="#addOptionModal" data-type="budget" data-title="Add New Budget Range">
                                <i class="fas fa-plus me-1"></i> {{ __('Add Budget') }}
                            </button>
                        </div>
                        @include('super_admin.proposals.partials.options_table', ['options' => $budgets, 'type' => 'budget'])
                    </div>

                    {{-- TAB 5: TIMELINES --}}
                    <div class="tab-pane fade" id="tab-timelines" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0 small text-uppercase">{{ __('Step 3: Expected Starting Timelines') }}</h6>
                            <button type="button" class="btn btn-sm btn-primary-modern" data-bs-toggle="modal" data-bs-target="#addOptionModal" data-type="timeline" data-title="Add New Timeline Option">
                                <i class="fas fa-plus me-1"></i> {{ __('Add Timeline') }}
                            </button>
                        </div>
                        @include('super_admin.proposals.partials.options_table', ['options' => $timelines, 'type' => 'timeline'])
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add / Edit Option -->
<div class="modal fade" id="addOptionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form action="{{ route('super_admin.cms.proposals.options.store') }}" method="POST" id="optionForm">
                @csrf
                <input type="hidden" name="type" id="optionType" value="service">

                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark" id="optionModalTitle">{{ __('Add Option') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Option Name / Label') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="optionName" class="form-control" required placeholder="e.g. SEO (Search Engine Optimization)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Subtitle / Short Hint (Optional)') }}</label>
                        <input type="text" name="subtitle" id="optionSubtitle" class="form-control" placeholder="e.g. Rank #1 on Google, drive organic buyers">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label fw-bold text-dark small">{{ __('Icon Class (FontAwesome)') }}</label>
                            <input type="text" name="icon" id="optionIcon" class="form-control" placeholder="fas fa-search">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-bold text-dark small">{{ __('Order') }}</label>
                            <input type="number" name="sort_order" id="optionOrder" class="form-control" placeholder="1" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3">
                    <button type="button" class="btn btn-secondary-modern btn-sm px-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary-modern btn-sm px-4">{{ __('Save Option') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('addOptionModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (button) {
                const type = button.getAttribute('data-type');
                const title = button.getAttribute('data-title') || 'Add Option';
                document.getElementById('optionType').value = type;
                document.getElementById('optionModalTitle').textContent = title;
                document.getElementById('optionName').value = '';
                document.getElementById('optionSubtitle').value = '';
                document.getElementById('optionIcon').value = '';
                document.getElementById('optionOrder').value = '';
            }
        });
    }
});
</script>
@endpush
@endsection
