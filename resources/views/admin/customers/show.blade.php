@extends('admin.layouts.app')

@section('title', 'Customer: ' . $customer->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <a href="{{ route('customers.index') }}" class="btn btn-light btn-sm rounded-circle me-3 shadow-sm">
            <i class="fas fa-arrow-left text-muted"></i>
        </a>
        <div>
            <h2 class="fw-bold text-dark mb-0">{{ $customer->name }}</h2>
            <div class="text-muted small">
                <span class="badge {{ $customer->is_active ? 'bg-success' : 'bg-secondary' }} me-2">
                    {{ $customer->is_active ? 'Active' : 'Inactive' }}
                </span>
                Customer ID: #{{ $customer->id }}
            </div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('customers.statement', $customer) }}" class="btn btn-outline-success">
            <i class="fas fa-file-invoice-dollar me-2"></i> View Statement
        </a>
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary-modern">
            <i class="fas fa-edit me-2"></i> Edit Customer
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-user-circle text-primary me-2"></i> Primary Contact Information
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small d-block">Full Name</label>
                    <div class="fw-bold fs-6 text-dark">{{ $customer->name }}</div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Phone Number</label>
                    <div class="fw-semibold text-dark">
                        <i class="fas fa-phone-alt text-primary me-1"></i>
                        {{ $customer->formatted_phone ?: ($customer->phone ?: 'N/A') }}
                    </div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small d-block">Email Address</label>
                    <div class="fw-medium text-dark">
                        @if($customer->email)
                            <a href="mailto:{{ $customer->email }}" class="text-decoration-none">
                                <i class="fas fa-envelope text-muted me-1"></i> {{ $customer->email }}
                            </a>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                </div>
                <div class="mb-0">
                    <label class="text-muted small d-block">Status</label>
                    <span class="badge {{ $customer->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} px-2 py-1">
                        {{ $customer->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-building text-primary me-2"></i> Companies & Branches
                </h6>
                <span class="badge bg-light text-primary border">
                    <i class="fas fa-layer-group me-1"></i> {{ $customer->companies->count() }} {{ Str::plural('Company', $customer->companies->count()) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @forelse($customer->companies as $comp)
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border {{ $comp->is_default ? 'border-primary bg-primary-subtle bg-opacity-10' : 'bg-light' }} h-100 position-relative">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold text-dark mb-0">
                                        <i class="fas fa-building text-secondary me-1"></i>
                                        {{ $comp->company_name }}
                                    </h6>
                                    @if($comp->is_default)
                                        <span class="badge bg-primary">
                                            <i class="fas fa-star me-1 small"></i> Primary / Default
                                        </span>
                                    @endif
                                </div>
                                <div class="small text-muted">
                                    @if($comp->phone)
                                        <div class="mb-1"><i class="fas fa-phone-alt me-1 text-primary"></i> <strong>Phone:</strong> {{ $comp->phone }}</div>
                                    @endif
                                    @if($comp->vat_no)
                                        <div class="mb-1"><strong>VAT:</strong> {{ $comp->vat_no }}</div>
                                    @endif
                                    @if($comp->cr_no)
                                        <div class="mb-1"><strong>CR:</strong> {{ $comp->cr_no }}</div>
                                    @endif
                                    @if($comp->address)
                                        <div class="mb-1"><i class="fas fa-map-marker-alt me-1 text-danger"></i> {{ $comp->address }}</div>
                                    @endif
                                    <div>
                                        {{ collect([$comp->city, $comp->zip, $comp->country])->filter()->join(', ') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4 text-muted">
                            <i class="fas fa-building fa-2x mb-2 opacity-50"></i>
                            <p class="mb-0">No company details recorded.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
