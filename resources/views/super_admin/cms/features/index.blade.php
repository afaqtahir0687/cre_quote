@extends('admin.layouts.app')

@section('title', __('Features Grid Manager'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Features Grid Manager') }}</h2>
            <span class="badge-soft badge-soft-warning px-2 py-1 rounded-pill small">
                Feature Highlights
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Manage the 8 core feature cards, descriptions, icons, and highlight checklists.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.cms.features.create') }}" class="btn btn-primary-modern btn-sm">
            <i class="fas fa-plus me-1"></i> {{ __('Add Feature') }}
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

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-star me-2 text-warning"></i> {{ __('Registered Features (' . $features->count() . ')') }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 70px;" class="text-center">{{ __('Icon') }}</th>
                    <th>{{ __('Feature Title') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Highlights') }}</th>
                    <th style="width: 90px;" class="text-center">{{ __('Order') }}</th>
                    <th style="width: 90px;" class="text-center">{{ __('Status') }}</th>
                    <th style="width: 120px;" class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($features as $f)
                    <tr>
                        <td class="text-center">
                            <div class="p-2 rounded-3 {{ $f->icon_color_class }}" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                                <i class="{{ $f->icon_class }}"></i>
                            </div>
                        </td>
                        <td><strong class="text-dark">{{ $f->title }}</strong></td>
                        <td class="small text-muted" style="max-width: 280px;">{{ Str::limit($f->description, 80) }}</td>
                        <td>
                            @if(is_array($f->highlights))
                                @foreach($f->highlights as $h)
                                    <span class="badge bg-light text-dark border me-1">{{ $h }}</span>
                                @endforeach
                            @endif
                        </td>
                        <td class="text-center fw-bold">{{ $f->sort_order }}</td>
                        <td class="text-center">
                            @if($f->is_active)
                                <span class="badge-soft badge-soft-success">Active</span>
                            @else
                                <span class="badge-soft badge-soft-secondary">Hidden</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('super_admin.cms.features.edit', $f->id) }}" class="btn-icon-action me-1" title="Edit Feature">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('super_admin.cms.features.destroy', $f->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon-action btn-action-delete" title="Delete Feature">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No features found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
