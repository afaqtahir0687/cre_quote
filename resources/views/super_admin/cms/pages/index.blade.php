@extends('admin.layouts.app')

@section('title', __('Dynamic CMS & Footer Pages Manager'))

@section('content')
<!-- Header & Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Footer & Dynamic Pages Manager') }}</h2>
            <span class="badge-soft badge-soft-primary px-2 py-1 rounded-pill small">
                CMS Content
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Manage company pages, legal policies, and footer links (About Us, Privacy Policy, Terms, Refund Policy, Contact Us, etc.).') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.cms.pages.create') }}" class="btn btn-primary-modern btn-sm">
            <i class="fas fa-plus me-1"></i> {{ __('Add New Page') }}
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

<!-- Quick Metrics -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted small text-uppercase fw-bold">{{ __('Total Pages') }}</span>
                <div class="p-2 rounded-3" style="background: rgba(67, 97, 238, 0.1); color: #4361EE;">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold text-dark my-1">{{ $stats['total'] }}</div>
            <div class="small text-muted"><i class="fas fa-check text-success me-1"></i> {{ $stats['active'] }} Active</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted small text-uppercase fw-bold">{{ __('Company Pages') }}</span>
                <div class="p-2 rounded-3" style="background: rgba(16, 185, 129, 0.1); color: #10B981;">
                    <i class="fas fa-building"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold text-dark my-1">{{ $stats['company'] }}</div>
            <div class="small text-muted">{{ __('About Us, Contact, etc.') }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted small text-uppercase fw-bold">{{ __('Legal Policies') }}</span>
                <div class="p-2 rounded-3" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                    <i class="fas fa-balance-scale"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold text-dark my-1">{{ $stats['legal'] }}</div>
            <div class="small text-muted">{{ __('Privacy, Terms, Refund, etc.') }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm p-3 rounded-4 bg-white">
            <div class="d-flex align-items-center justify-content-between">
                <span class="text-muted small text-uppercase fw-bold">{{ __('Footer Sync') }}</span>
                <div class="p-2 rounded-3" style="background: rgba(250, 116, 54, 0.1); color: #FA7436;">
                    <i class="fas fa-link"></i>
                </div>
            </div>
            <div class="fs-4 fw-bold text-dark my-1">{{ __('100%') }}</div>
            <div class="small text-muted">{{ __('Live dynamic rendering') }}</div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <h5 class="fw-bold text-dark mb-0">
            <i class="fas fa-layer-group me-2 text-primary"></i> {{ __('All CMS Pages') }} ({{ $pages->count() }})
        </h5>
        <!-- Search & Filter Form -->
        <form method="GET" action="{{ route('super_admin.cms.pages.index') }}" class="d-flex align-items-center gap-2">
            <select name="column" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">{{ __('All Categories') }}</option>
                <option value="company" {{ request('column') === 'company' ? 'selected' : '' }}>{{ __('Company') }}</option>
                <option value="legal" {{ request('column') === 'legal' ? 'selected' : '' }}>{{ __('Legal & Trust') }}</option>
                <option value="quick_links" {{ request('column') === 'quick_links' ? 'selected' : '' }}>{{ __('Quick Links') }}</option>
                <option value="none" {{ request('column') === 'none' ? 'selected' : '' }}>{{ __('Hidden from Footer') }}</option>
            </select>
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">{{ __('All Status') }}</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Hidden') }}</option>
            </select>
            @if(request()->hasAny(['column', 'status', 'search']))
                <a href="{{ route('super_admin.cms.pages.index') }}" class="btn btn-outline-secondary btn-sm" title="Clear Filters">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 280px;">{{ __('Page Title & Subtitle') }}</th>
                    <th>{{ __('Slug / Public URL') }}</th>
                    <th style="width: 140px;" class="text-center">{{ __('Footer Group') }}</th>
                    <th style="width: 90px;" class="text-center">{{ __('Order') }}</th>
                    <th style="width: 100px;" class="text-center">{{ __('Status') }}</th>
                    <th style="width: 150px;" class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pages as $page)
                    <tr>
                        <td>
                            <div class="d-flex align-items-start gap-2">
                                <div class="mt-1" style="color: #4361EE;">
                                    @if($page->footer_column === 'legal')
                                        <i class="fas fa-balance-scale"></i>
                                    @elseif($page->slug === 'contact-us')
                                        <i class="fas fa-headset"></i>
                                    @else
                                        <i class="fas fa-file-alt"></i>
                                    @endif
                                </div>
                                <div>
                                    <strong class="text-dark d-block">{{ $page->title }}</strong>
                                    @if($page->subtitle)
                                        <span class="text-muted small">{{ Str::limit($page->subtitle, 65) }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <code class="small text-primary">/page/{{ $page->slug }}</code>
                                <a href="{{ route('website.page', $page->slug) }}" target="_blank" class="text-muted" title="View live page">
                                    <i class="fas fa-external-link-alt fa-xs"></i>
                                </a>
                            </div>
                        </td>
                        <td class="text-center">
                            @if($page->footer_column === 'company')
                                <span class="badge-soft badge-soft-primary px-2 py-1 rounded-pill small">Company</span>
                            @elseif($page->footer_column === 'legal')
                                <span class="badge-soft badge-soft-warning px-2 py-1 rounded-pill small">Legal & Trust</span>
                            @elseif($page->footer_column === 'quick_links')
                                <span class="badge-soft badge-soft-info px-2 py-1 rounded-pill small">Quick Links</span>
                            @else
                                <span class="badge-soft badge-soft-secondary px-2 py-1 rounded-pill small">Hidden</span>
                            @endif
                        </td>
                        <td class="text-center fw-bold">{{ $page->sort_order }}</td>
                        <td class="text-center">
                            <form action="{{ route('super_admin.cms.pages.toggle_active', $page->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm border-0 p-0" title="Click to toggle status">
                                    @if($page->is_active)
                                        <span class="badge-soft badge-soft-success">Active</span>
                                    @else
                                        <span class="badge-soft badge-soft-secondary">Hidden</span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('website.page', $page->slug) }}" target="_blank" class="btn-icon-action me-1 text-info" title="Preview Public Page">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('super_admin.cms.pages.edit', $page->id) }}" class="btn-icon-action me-1" title="Edit Page Content">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('super_admin.cms.pages.destroy', $page->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon-action btn-action-delete" title="Delete Page" data-name="{{ $page->title }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-file-alt fs-2 text-muted mb-2 d-block"></i>
                            {{ __('No CMS pages found. Click "Add New Page" to create one.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

