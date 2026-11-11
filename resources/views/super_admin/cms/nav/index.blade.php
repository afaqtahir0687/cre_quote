@extends('admin.layouts.app')

@section('title', __('Navigation & Footer Menu Links'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Navigation Menu & Footer Links') }}</h2>
            <span class="badge-soft badge-soft-info px-2 py-1 rounded-pill small">
                Links & Anchors
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Manage header navigation links and footer module/company links without editing Blade code.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
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
    <!-- Add New Menu Item -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-plus-circle me-1 text-primary"></i> {{ __('Add New Menu Link') }}</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('super_admin.cms.nav.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Link Label') }} *</label>
                        <input type="text" name="label" class="form-control" placeholder="e.g. Features or Pricing" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Destination URL or Anchor') }} *</label>
                        <input type="text" name="url" class="form-control" placeholder="e.g. #pricing or /register" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Placement Location') }} *</label>
                        <select name="location" class="form-select">
                            <option value="header">Header Navigation Bar</option>
                            <option value="footer_nav">Footer: Quick Links</option>
                            <option value="footer_modules">Footer: Business Modules</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">{{ __('Display Order') }}</label>
                            <input type="number" name="sort_order" class="form-control" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">{{ __('Target') }}</label>
                            <select name="target" class="form-select">
                                <option value="_self">Same Tab</option>
                                <option value="_blank">New Tab</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-4">
                        <input type="checkbox" name="is_active" class="form-check-input" value="1" checked id="navAddActive">
                        <label class="form-check-label fw-bold" for="navAddActive">Active (Visible)</label>
                    </div>
                    <button type="submit" class="btn btn-primary-modern w-100">
                        <i class="fas fa-save me-1"></i> Add Link
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Existing Menu Items Tables -->
    <div class="col-lg-8">
        <!-- 1. Header Navigation -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-compass me-2 text-primary"></i> {{ __('Header Navbar Links') }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Label</th>
                            <th>Target URL</th>
                            <th style="width: 80px;" class="text-center">Order</th>
                            <th style="width: 80px;" class="text-center">Active</th>
                            <th style="width: 90px;" class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($headerItems as $item)
                            <tr>
                                <td><strong>{{ $item->label }}</strong></td>
                                <td><code>{{ $item->url }}</code></td>
                                <td class="text-center fw-bold">{{ $item->sort_order }}</td>
                                <td class="text-center">
                                    <span class="badge-soft {{ $item->is_active ? 'badge-soft-success' : 'badge-soft-secondary' }}">
                                        {{ $item->is_active ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('super_admin.cms.nav.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon-action btn-action-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-3 text-muted">No header links found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. Footer Links -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-shoe-prints me-2 text-primary"></i> {{ __('Footer Quick Links') }}</h5>
            </div>
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Label</th>
                            <th>Target URL</th>
                            <th style="width: 80px;" class="text-center">Order</th>
                            <th style="width: 90px;" class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($footerNavItems as $item)
                            <tr>
                                <td><strong>{{ $item->label }}</strong></td>
                                <td><code>{{ $item->url }}</code></td>
                                <td class="text-center fw-bold">{{ $item->sort_order }}</td>
                                <td class="text-end">
                                    <form action="{{ route('super_admin.cms.nav.destroy', $item->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon-action btn-action-delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-3 text-muted">No footer links found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
