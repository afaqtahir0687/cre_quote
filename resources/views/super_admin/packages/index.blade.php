@extends('admin.layouts.app')

@section('title', 'Manage System Packages')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">System Packages & Plans</h2>
        <p class="text-muted small mb-0">Configure pricing, duration, multi-shop branch limits, and features for each package.</p>
    </div>
    <a href="{{ route('super_admin.subscriptions.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Subscriptions
    </a>
</div>

<div class="row g-4">
    @foreach($packages as $pkg)
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge-soft {{ $pkg->badgeClass() }}">
                            @if($pkg->isTrial())
                                <i class="fas fa-flask me-1"></i> Trial Account
                            @elseif($pkg->isBusiness())
                                <i class="fas fa-building me-1"></i> Multi-Shop
                            @else
                                <i class="fas fa-calendar-alt me-1"></i> Single Shop
                            @endif
                        </span>
                        <span class="badge-soft badge-soft-secondary">
                            {{ $pkg->shops_count }} active {{ Str::plural('shop', $pkg->shops_count) }}
                        </span>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ $pkg->name }}</h4>
                    <p class="text-muted small mb-0">{{ $pkg->description }}</p>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('super_admin.packages.update', $pkg->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Package Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $pkg->name) }}" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark small">Price (SAR)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted fw-bold">SAR</span>
                                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $pkg->price) }}" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark small">Duration (Days)</label>
                                <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', $pkg->duration_days) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Allowed Shops / Branches</label>
                            <input type="number" name="max_shops" class="form-control" value="{{ old('max_shops', $pkg->max_shops) }}" min="1" required>
                            <div class="form-text small">Set 1 for single shop, 5 or more for Business Multi-Shop.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description', $pkg->description) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark small">Features (One per line)</label>
                            @php
                                $featuresText = is_array($pkg->features) ? implode("\n", $pkg->features) : '';
                            @endphp
                            <textarea name="features" class="form-control font-monospace small" rows="5">{{ old('features', $featuresText) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary-modern w-100 py-2">
                            <i class="fas fa-save me-1"></i> Update {{ $pkg->name }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
