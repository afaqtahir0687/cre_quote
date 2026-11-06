@extends('admin.layouts.app')

@section('title', __('Manage Branches / Multi-Shops'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">{{ __('Branches & Multi-Shops') }}</h2>
        <p class="text-muted small mb-0">{{ __('Manage multiple shop locations and switch between branches under your Business Account.') }}</p>
    </div>
    @if($isBusiness && $canAddMore)
        <a href="{{ route('branches.create') }}" class="btn btn-primary-modern">
            <i class="fas fa-plus me-1"></i> {{ __('Add New Branch') }}
        </a>
    @endif
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(!$isBusiness)
    <!-- Non-Business Package Notice -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white border-start border-4 border-warning">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-lock text-warning fs-4"></i>
                        <h5 class="fw-bold text-dark mb-0">{{ __('Multi-Shop Feature Locked') }}</h5>
                    </div>
                    <p class="text-muted small mb-0">
                        Your shop <strong>{{ $mainShop->name }}</strong> is currently on the <strong>{{ $package->name ?? 'Single Shop Plan' }}</strong>, which allows only 1 location. To create and manage multiple shops/branches, please upgrade to the <strong>Business Multi-Shop Package (Up to {{ \App\Models\Package::where('slug', 'business')->first()?->max_shops ?? 5 }} Shops)</strong> via Cash By Hand.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('subscription.index') }}" class="btn btn-primary-modern">
                        <i class="fas fa-arrow-circle-up me-1"></i> {{ __('Upgrade to Business Plan') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Allocation Banner for Business Plan -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                            <i class="fas fa-building me-1"></i> {{ __('Business Multi-Shop Account') }}
                        </span>
                        <span class="badge bg-light text-muted border px-2 py-1">
                            {{ $usedShops }} {{ __('of') }} {{ $maxShops }} {{ __('Shops Used') }}
                        </span>
                    </div>
                    <h5 class="fw-bold text-dark mt-2 mb-0">{{ __('Central Business:') }} {{ tdb($mainShop->name) }}</h5>
                </div>
                @if($canAddMore)
                    <a href="{{ route('branches.create') }}" class="btn btn-primary-modern">
                        <i class="fas fa-plus me-1"></i> {{ __('Add Branch') }} ({{ $maxShops - $usedShops }} {{ __('Remaining') }})
                    </a>
                @else
                    <span class="badge bg-secondary p-2">
                        <i class="fas fa-info-circle me-1"></i> {{ __('Maximum Branches Limit Reached') }} ({{ $maxShops }}/{{ $maxShops }})
                    </span>
                @endif
            </div>
        </div>
    </div>
@endif

<!-- List of Shops / Branches -->
<div class="row g-4">
    <!-- Main / HQ Shop Card -->
    @php
        $currentActiveShopId = current_shop_id();
        $isMainActive = ($currentActiveShopId == $mainShop->id);
    @endphp
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 position-relative bg-white {{ $isMainActive ? 'border border-2 border-primary' : '' }}">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center gap-3">
                        @if($mainShop->logo)
                            <img src="{{ asset($mainShop->logo) }}" alt="{{ $mainShop->name }}" class="rounded border p-1" style="width: 48px; height: 48px; object-fit: contain;">
                        @else
                            <div class="rounded bg-light text-primary d-flex align-items-center justify-content-center border" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                <i class="fas fa-store"></i>
                            </div>
                        @endif
                        <div>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill small mb-1">
                                {{ __('Main HQ / Primary') }}
                            </span>
                            <h5 class="fw-bold text-dark mb-0">{{ tdb($mainShop->name) }}</h5>
                        </div>
                    </div>
                </div>

                <div class="small text-muted mb-3">
                    <div><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $mainShop->city ? tdb($mainShop->city) : __('Main Location') }} &bull; {{ $mainShop->country }}</div>
                    @if($mainShop->phone)
                        <div class="mt-1"><i class="fas fa-phone-alt text-success me-1"></i> {{ $mainShop->phone }}</div>
                    @endif
                </div>

                <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                    @if($isMainActive)
                        <span class="badge bg-success text-white px-3 py-2 rounded-pill">
                            <i class="fas fa-check me-1"></i> {{ __('Currently Active') }}
                        </span>
                    @else
                        <form action="{{ route('branches.switch', $mainShop->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary shadow-none">
                                <i class="fas fa-exchange-alt me-1"></i> {{ __('Switch to This Shop') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sub Branches -->
    @foreach($branches as $branch)
        @php
            $isBranchActive = ($currentActiveShopId == $branch->id);
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 position-relative bg-white {{ $isBranchActive ? 'border border-2 border-primary' : '' }}">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            @if($branch->logo)
                                <img src="{{ asset($branch->logo) }}" alt="{{ $branch->name }}" class="rounded border p-1" style="width: 48px; height: 48px; object-fit: contain;">
                            @else
                                <div class="rounded bg-light text-primary d-flex align-items-center justify-content-center border" style="width: 48px; height: 48px; font-size: 1.25rem;">
                                    <i class="fas fa-store"></i>
                                </div>
                            @endif
                            <div>
                                <span class="badge bg-secondary-subtle text-secondary border rounded-pill small mb-1">
                                    {{ __('Branch') }} #{{ $branch->id }}
                                </span>
                                <h5 class="fw-bold text-dark mb-0">{{ tdb($branch->name) }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="small text-muted mb-3">
                        <div><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $branch->city ? tdb($branch->city) : __('Branch Location') }}</div>
                        @if($branch->phone)
                            <div class="mt-1"><i class="fas fa-phone-alt text-success me-1"></i> {{ $branch->phone }}</div>
                        @endif
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                        @if($isBranchActive)
                            <span class="badge bg-success text-white px-3 py-2 rounded-pill">
                                <i class="fas fa-check me-1"></i> {{ __('Currently Active') }}
                            </span>
                        @else
                            <form action="{{ route('branches.switch', $branch->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary shadow-none">
                                    <i class="fas fa-exchange-alt me-1"></i> {{ __('Switch to This Branch') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
