@extends('admin.layouts.app')

@section('title', __('Marketing — Proposal Requests'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Proposal Requests') }}</h2>
            @if($stats['pending'] > 0)
                <span class="badge-soft badge-soft-warning px-2 py-1 rounded-pill small">
                    <i class="fas fa-bell me-1"></i> {{ $stats['pending'] }} {{ __('Pending Review') }}
                </span>
            @endif
        </div>
        <p class="text-muted small mb-0">{{ __('Inquiries submitted through the 4-step Get Your Free Proposal modal on the homepage.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.proposals.export_csv', request()->all()) }}" class="btn btn-secondary-modern btn-sm">
            <i class="fas fa-file-excel text-success me-1"></i> {{ __('Export to CSV') }}
        </a>
        <a href="{{ route('super_admin.cms.proposals.settings') }}" class="btn btn-primary-modern btn-sm">
            <i class="fas fa-sliders-h me-1"></i> {{ __('Form Settings & Options') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Quick Metrics Grid -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm p-3 h-100 rounded-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small text-uppercase fw-bold">{{ __('Total Proposals') }}</span>
                <div class="p-2 rounded-3" style="background: rgba(67, 97, 238, 0.1); color: #4361EE;">
                    <i class="fas fa-file-signature"></i>
                </div>
            </div>
            <div class="fs-3 fw-bold text-dark my-1">{{ $stats['total'] }}</div>
            <div class="small text-muted"><i class="fas fa-inbox text-primary me-1"></i> {{ __('All submissions') }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm p-3 h-100 rounded-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small text-uppercase fw-bold">{{ __('Pending Action') }}</span>
                <div class="p-2 rounded-3" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="fs-3 fw-bold text-dark my-1">{{ $stats['pending'] }}</div>
            <div class="small text-muted"><i class="fas fa-exclamation-circle text-warning me-1"></i> {{ __('Awaiting initial contact') }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm p-3 h-100 rounded-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small text-uppercase fw-bold">{{ __('In Discussion') }}</span>
                <div class="p-2 rounded-3" style="background: rgba(14, 165, 233, 0.1); color: #0EA5E9;">
                    <i class="fas fa-comments"></i>
                </div>
            </div>
            <div class="fs-3 fw-bold text-dark my-1">{{ $stats['in_discussion'] }}</div>
            <div class="small text-muted"><i class="fas fa-paper-plane text-info me-1"></i> {{ __('In conversation or proposal sent') }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm p-3 h-100 rounded-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small text-uppercase fw-bold">{{ __('Converted Clients') }}</span>
                <div class="p-2 rounded-3" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                    <i class="fas fa-trophy"></i>
                </div>
            </div>
            <div class="fs-3 fw-bold text-dark my-1">{{ $stats['converted'] }}</div>
            <div class="small text-muted"><i class="fas fa-check-double text-success me-1"></i> {{ __('Closed & converted') }}</div>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('super_admin.proposals.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email, phone, reference..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="service" class="form-select">
                    <option value="">{{ __('All Services') }}</option>
                    @foreach($services as $srv)
                        <option value="{{ $srv }}" {{ request('service') == $srv ? 'selected' : '' }}>{{ $srv }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">{{ __('All Statuses') }}</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary-modern w-100">
                    <i class="fas fa-filter me-1"></i> {{ __('Filter') }}
                </button>
                @if(request()->hasAny(['search', 'service', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('super_admin.proposals.index') }}" class="btn btn-secondary-modern" title="{{ __('Clear Filters') }}">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Proposals Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold text-dark small text-uppercase">
            <i class="fas fa-inbox text-primary me-2"></i> {{ __('Submitted Inquiries') }} ({{ $proposals->total() }})
        </span>
    </div>
    <div class="table-responsive">
        <table class="table-modern align-middle">
            <thead>
                <tr>
                    <th style="width: 140px;">{{ __('Ref #') }}</th>
                    <th>{{ __('Client Details') }}</th>
                    <th>{{ __('Primary Service') }}</th>
                    <th>{{ __('Target Location') }}</th>
                    <th>{{ __('Budget & Timeline') }}</th>
                    <th class="text-center" style="width: 130px;">{{ __('Status') }}</th>
                    <th style="width: 130px;">{{ __('Submitted') }}</th>
                    <th class="text-end" style="width: 100px;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proposals as $prop)
                    <tr>
                        <td>
                            <a href="{{ route('super_admin.proposals.show', $prop->id) }}" class="fw-bold text-primary font-monospace">
                                #{{ $prop->reference_no }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $prop->full_name }}</div>
                            <div class="text-muted small">
                                <a href="mailto:{{ $prop->email }}" class="text-muted text-decoration-none me-2">
                                    <i class="fas fa-envelope text-primary me-1"></i>{{ $prop->email }}
                                </a>
                            </div>
                            <div class="text-muted small">
                                <a href="tel:{{ $prop->phone }}" class="text-muted text-decoration-none">
                                    <i class="fas fa-phone-alt text-success me-1"></i>{{ $prop->phone }}
                                </a>
                            </div>
                        </td>
                        <td>
                            <span class="badge-soft badge-soft-secondary fw-bold">
                                {{ $prop->service }}
                            </span>
                            @if($prop->website_url)
                                <div class="mt-1">
                                    <a href="{{ $prop->website_url }}" target="_blank" class="small text-muted text-truncate d-inline-block" style="max-width: 180px;">
                                        <i class="fas fa-globe text-primary me-1"></i>{{ $prop->website_url }}
                                    </a>
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="text-dark small fw-semibold">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ $prop->customer_location }}
                            </span>
                            <div class="text-muted small text-truncate" style="max-width: 160px;" title="{{ $prop->campaign_goal }}">
                                Goal: {{ $prop->campaign_goal }}
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-success small">
                                {{ $prop->monthly_budget }}
                            </div>
                            <div class="text-muted small">
                                <i class="fas fa-bolt text-warning me-1"></i>{{ $prop->starting_timeline }}
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge-soft {{ $prop->statusBadgeClass() }} px-3 py-1 rounded-pill">
                                {{ $prop->status }}
                            </span>
                        </td>
                        <td>
                            <div class="small text-dark fw-semibold">{{ $prop->created_at->format('M d, Y') }}</div>
                            <div class="text-muted small" style="font-size: 0.72rem;">{{ $prop->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('super_admin.proposals.show', $prop->id) }}" class="btn-icon-action" title="{{ __('View Details & Manage') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('super_admin.proposals.destroy', $prop->id) }}" method="POST" class="d-inline delete-form" data-confirm="{{ __('Are you sure you want to delete proposal #') }}{{ $prop->reference_no }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon-action btn-action-delete" title="{{ __('Delete') }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-file-signature fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-0">{{ __('No proposal requests found matching your filters.') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($proposals->hasPages())
        <div class="p-3 border-top bg-light">
            {{ $proposals->links() }}
        </div>
    @endif
</div>
@endsection
