@extends('admin.layouts.app')

@section('title', __('Application Icon Grid Manager'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Application Icon Grid Manager') }}</h2>
            <span class="badge-soft badge-soft-info px-2 py-1 rounded-pill small">
                Interactive Grid
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Add, reorder, and customize the application launcher boxes displayed in the dynamic horizontal track.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.cms.modules.create') }}" class="btn btn-primary-modern btn-sm">
            <i class="fas fa-plus me-1"></i> {{ __('Add Application') }}
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
        <h5 class="fw-bold text-dark mb-0">
            <i class="fas fa-cubes me-2 text-primary"></i> {{ __('Registered Applications (' . $modules->count() . ')') }}
        </h5>
    </div>
    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 80px;" class="text-center">{{ __('Icon') }}</th>
                    <th>{{ __('Application Name') }}</th>
                    <th>{{ __('Destination URL / Anchor') }}</th>
                    <th style="width: 100px;" class="text-center">{{ __('Order') }}</th>
                    <th style="width: 100px;" class="text-center">{{ __('Status') }}</th>
                    <th style="width: 130px;" class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($modules as $m)
                    <tr>
                        <td class="text-center">
                            <div style="width: 44px; height: 44px; margin: 0 auto; display: flex; align-items: center; justify-content: center; background: #FFFFFF; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #E2E8F0;">
                                {!! $m->icon_svg !!}
                            </div>
                        </td>
                        <td>
                            <strong class="text-dark">{{ $m->name }}</strong>
                            <div class="small text-muted font-monospace">{{ $m->slug }}</div>
                        </td>
                        <td>
                            <code class="text-primary bg-light px-2 py-1 rounded">{{ $m->destination_url ?: '#modules' }}</code>
                        </td>
                        <td class="text-center fw-bold">{{ $m->sort_order }}</td>
                        <td class="text-center">
                            @if($m->is_active)
                                <span class="badge-soft badge-soft-success">Active</span>
                            @else
                                <span class="badge-soft badge-soft-secondary">Hidden</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('super_admin.cms.modules.edit', $m->id) }}" class="btn-icon-action me-1" title="Edit Application">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('super_admin.cms.modules.destroy', $m->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon-action btn-action-delete" title="Delete Application">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No applications found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
