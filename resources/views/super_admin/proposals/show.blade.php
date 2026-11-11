@extends('admin.layouts.app')

@section('title', __('Proposal Request') . ' #' . $proposal->reference_no)

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Proposal Request') }} #{{ $proposal->reference_no }}</h2>
            <span class="badge-soft {{ $proposal->statusBadgeClass() }} px-3 py-1 rounded-pill">
                {{ $proposal->status }}
            </span>
        </div>
        <p class="text-muted small mb-0">
            <i class="fas fa-calendar-alt me-1 text-primary"></i> Submitted on {{ $proposal->created_at->format('F d, Y \a\t h:i A') }} (IP: {{ $proposal->ip_address ?: 'N/A' }})
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.proposals.index') }}" class="btn btn-secondary-modern">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Proposals') }}
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
    <!-- Left Column: The 4 Steps Information -->
    <div class="col-lg-8">
        <!-- Step 1: Primary Service Interested In -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark small text-uppercase">
                    <span class="badge bg-primary text-white rounded-circle me-2" style="width: 22px; height: 22px; line-height: 14px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                    {{ __('Step 1 — Primary Service Requested') }}
                </span>
                <span class="badge bg-primary-subtle text-primary border small">Primary Interest</span>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light border">
                    <div class="p-3 rounded-circle bg-white border text-primary fs-3">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div>
                        <div class="text-muted small text-uppercase">{{ __('Service Selected') }}</div>
                        <h4 class="fw-bold text-dark mb-0">{{ $proposal->service }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Customer Location & Campaign Goals -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark small text-uppercase">
                    <span class="badge bg-primary text-white rounded-circle me-2" style="width: 22px; height: 22px; line-height: 14px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                    {{ __('Step 2 — Location & Campaign Goals') }}
                </span>
                <span class="badge bg-info-subtle text-info border small">Target Audience</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <div class="text-muted small mb-1">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ __('Where Customers Are Located') }}
                            </div>
                            <div class="fs-5 fw-bold text-dark">{{ $proposal->customer_location }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <div class="text-muted small mb-1">
                                <i class="fas fa-bullseye text-primary me-1"></i> {{ __('Campaign Goal') }}
                            </div>
                            <div class="fs-5 fw-bold text-dark">{{ $proposal->campaign_goal }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Budget & Timeline -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark small text-uppercase">
                    <span class="badge bg-primary text-white rounded-circle me-2" style="width: 22px; height: 22px; line-height: 14px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                    {{ __('Step 3 — Budget & Expected Timeline') }}
                </span>
                <span class="badge bg-success-subtle text-success border small">Financials</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <div class="text-muted small mb-1">
                                <i class="fas fa-wallet text-success me-1"></i> {{ __('Monthly Budget Range') }}
                            </div>
                            <div class="fs-5 fw-bold text-success">{{ $proposal->monthly_budget }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <div class="text-muted small mb-1">
                                <i class="fas fa-bolt text-warning me-1"></i> {{ __('Looking to Get Started') }}
                            </div>
                            <div class="fs-5 fw-bold text-dark">{{ $proposal->starting_timeline }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4: Contact Information -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark small text-uppercase">
                    <span class="badge bg-primary text-white rounded-circle me-2" style="width: 22px; height: 22px; line-height: 14px; display: inline-flex; align-items: center; justify-content: center;">4</span>
                    {{ __('Step 4 — Client Contact Information') }}
                </span>
                <span class="badge bg-secondary-subtle text-secondary border small">Client Info</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <div class="text-muted small mb-1">{{ __('Full Name') }}</div>
                            <div class="fs-5 fw-bold text-dark">{{ $proposal->full_name }}</div>
                            <div class="text-muted small">First: {{ $proposal->first_name }} | Last: {{ $proposal->last_name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <div class="text-muted small mb-1">{{ __('Email Address') }}</div>
                            <div class="fs-5 fw-bold">
                                <a href="mailto:{{ $proposal->email }}" class="text-primary text-decoration-none">
                                    {{ $proposal->email }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <div class="text-muted small mb-1">{{ __('Phone / WhatsApp') }}</div>
                            <div class="fs-5 fw-bold">
                                <a href="tel:{{ $proposal->phone }}" class="text-success text-decoration-none">
                                    {{ $proposal->phone }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <div class="text-muted small mb-1">{{ __('Website URL') }}</div>
                            @if($proposal->website_url)
                                <div class="fs-6 fw-bold">
                                    <a href="{{ $proposal->website_url }}" target="_blank" class="text-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>{{ $proposal->website_url }}
                                    </a>
                                </div>
                            @else
                                <div class="text-muted small">None provided</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Status & Internal Notes -->
    <div class="col-lg-4">
        <!-- Update Proposal Status & Notes Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-4">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fas fa-tasks text-primary me-2"></i>{{ __('Lead Status & Notes') }}
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('super_admin.proposals.status.update', $proposal->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Proposal Status') }}</label>
                        <select name="status" class="form-select form-select-lg fw-bold" required>
                            @foreach($statuses as $st)
                                <option value="{{ $st }}" {{ $proposal->status === $st ? 'selected' : '' }}>
                                    {{ $st }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">{{ __('Internal Admin Notes') }}</label>
                        <textarea name="internal_notes" class="form-control" rows="6" placeholder="Add confidential internal team notes, follow-up calls, quote links...">{{ old('internal_notes', $proposal->internal_notes) }}</textarea>
                        @if($proposal->notes_updated_at)
                            <div class="form-text small text-muted mt-1">
                                <i class="fas fa-clock me-1"></i> Last updated: {{ $proposal->notes_updated_at->format('M d, Y h:i A') }}
                                @if($proposal->user)
                                    by {{ $proposal->user->name }}
                                @endif
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary-modern w-100 py-2">
                        <i class="fas fa-save me-1"></i> {{ __('Save Status & Notes') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Quick Client Actions Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom p-3">
                <span class="fw-bold text-dark small text-uppercase">{{ __('Quick Client Actions') }}</span>
            </div>
            <div class="card-body p-3 d-flex flex-column gap-2">
                <a href="mailto:{{ $proposal->email }}?subject=Your%20Proposal%20Request%20%23{{ $proposal->reference_no }}%20—%20CreLogics" class="btn btn-outline-primary text-start">
                    <i class="fas fa-envelope me-2"></i> {{ __('Send Email to Client') }}
                </a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $proposal->phone) }}?text=Hello%20{{ urlencode($proposal->first_name) }},%20thank%20you%20for%20contacting%20CreLogics%20regarding%20proposal%20%23{{ $proposal->reference_no }}." target="_blank" class="btn btn-outline-success text-start">
                    <i class="fab fa-whatsapp me-2"></i> {{ __('Message on WhatsApp') }}
                </a>
                <a href="tel:{{ $proposal->phone }}" class="btn btn-outline-secondary text-start">
                    <i class="fas fa-phone-alt me-2"></i> {{ __('Call Phone Number') }}
                </a>
            </div>
        </div>

        <!-- Delete Card -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3">
                <form action="{{ route('super_admin.proposals.destroy', $proposal->id) }}" method="POST" class="delete-form" data-confirm="{{ __('Are you sure you want to permanently delete proposal #') }}{{ $proposal->reference_no }}?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100 btn-sm">
                        <i class="fas fa-trash-alt me-1"></i> {{ __('Delete This Proposal') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

