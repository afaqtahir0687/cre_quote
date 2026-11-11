@extends('admin.layouts.app')

@section('title', __('Product Workflow Tabs'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Product Workflow Showcase Tabs') }}</h2>
            <span class="badge-soft badge-soft-primary px-2 py-1 rounded-pill small">
                Interactive Tabs
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Manage the 4 interactive workflow tabs on the public website.') }}</p>
    </div>
    <a href="{{ route('super_admin.cms.index') }}" class="btn btn-secondary-modern btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('CMS Hub') }}
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>{{ __('Tab Label & Icon') }}</th>
                    <th>{{ __('Badge') }}</th>
                    <th>{{ __('Main Heading') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th style="width: 100px;" class="text-center">{{ __('Order') }}</th>
                    <th style="width: 100px;" class="text-center">{{ __('Status') }}</th>
                    <th style="width: 100px;" class="text-end">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tabs as $tab)
                    <tr>
                        <td>
                            <i class="{{ $tab->tab_icon }} text-primary me-2"></i>
                            <strong class="text-dark">{{ $tab->tab_label }}</strong>
                        </td>
                        <td><span class="badge-soft badge-soft-primary">{{ $tab->badge_text }}</span></td>
                        <td><span class="fw-bold text-dark">{{ $tab->title }}</span></td>
                        <td class="small text-muted">{{ Str::limit($tab->description, 70) }}</td>
                        <td class="text-center fw-bold">{{ $tab->sort_order }}</td>
                        <td class="text-center">
                            @if($tab->is_active)
                                <span class="badge-soft badge-soft-success">Active</span>
                            @else
                                <span class="badge-soft badge-soft-secondary">Hidden</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('super_admin.cms.showcase.edit', $tab->id) }}" class="btn btn-sm btn-outline-primary" style="padding: 0.35rem 0.75rem; border-radius: 8px;">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
