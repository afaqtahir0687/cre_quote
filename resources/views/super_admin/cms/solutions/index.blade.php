@extends('admin.layouts.app')

@section('title', __('Industry Solutions Manager'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Industry Solutions Manager') }}</h2>
            <span class="badge-soft badge-soft-danger px-2 py-1 rounded-pill small">
                Target Industries
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Manage cards for Printing/Signage, Service Contractors, Wholesale, and Multi-Branch.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.cms.solutions.create') }}" class="btn btn-primary-modern btn-sm">
            <i class="fas fa-plus me-1"></i> {{ __('Add Solution') }}
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
        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-briefcase me-2 text-danger"></i> {{ __('Registered Solutions (' . $solutions->count() . ')') }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 140px;">{{ __('Industry Tag') }}</th>
                    <th>{{ __('Solution Title') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Checklist Items') }}</th>
                    <th style="width: 100px;" class="text-center">{{ __('Order') }}</th>
                    <th style="width: 100px;" class="text-center">{{ __('Status') }}</th>
                    <th style="width: 120px;" class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($solutions as $s)
                    <tr>
                        <td><span class="badge-soft badge-soft-danger">{{ $s->tag }}</span></td>
                        <td><strong class="text-dark">{{ $s->title }}</strong></td>
                        <td class="small text-muted" style="max-width: 250px;">{{ Str::limit($s->description, 80) }}</td>
                        <td>
                            @if(is_array($s->checklist))
                                <ul class="list-unstyled mb-0 small text-muted">
                                    @foreach($s->checklist as $item)
                                        <li><i class="fas fa-check text-success fa-xs me-1"></i> {{ Str::limit($item, 40) }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </td>
                        <td class="text-center fw-bold">{{ $s->sort_order }}</td>
                        <td class="text-center">
                            @if($s->is_active)
                                <span class="badge-soft badge-soft-success">Active</span>
                            @else
                                <span class="badge-soft badge-soft-secondary">Hidden</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('super_admin.cms.solutions.edit', $s->id) }}" class="btn-icon-action me-1" title="Edit Solution">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('super_admin.cms.solutions.destroy', $s->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon-action btn-action-delete" title="Delete Solution">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No industry solutions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
