@extends('admin.layouts.app')

@section('title', __('Customer Testimonials & Approval Manager'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Customer Testimonials & Approvals') }}</h2>
            <span class="badge-soft badge-soft-primary px-2 py-1 rounded-pill small">
                Client Social Proof
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Only approved testimonials appear on the public landing page.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.cms.testimonials.create') }}" class="btn btn-primary-modern btn-sm">
            <i class="fas fa-plus me-1"></i> {{ __('Add Testimonial') }}
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
        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-quote-left me-2 text-primary"></i> {{ __('All Testimonials (' . $testimonials->count() . ')') }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>{{ __('Client & Company') }}</th>
                    <th>{{ __('Review Text') }}</th>
                    <th style="width: 110px;" class="text-center">{{ __('Rating') }}</th>
                    <th style="width: 120px;" class="text-center">{{ __('Approval') }}</th>
                    <th style="width: 90px;" class="text-center">{{ __('Active') }}</th>
                    <th style="width: 120px;" class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($testimonials as $t)
                    <tr>
                        <td>
                            <strong class="text-dark">{{ $t->name }}</strong>
                            <div class="small text-muted">{{ $t->designation ? $t->designation . ' • ' : '' }}{{ $t->company }}</div>
                        </td>
                        <td class="small text-muted" style="max-width: 350px;">"{{ Str::limit($t->content, 90) }}"</td>
                        <td class="text-center text-warning">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $t->rating ? '' : 'text-muted opacity-25' }}"></i>
                            @endfor
                        </td>
                        <td class="text-center">
                            <form action="{{ route('super_admin.cms.testimonials.toggle_approval', $t->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $t->is_approved ? 'btn-outline-success' : 'btn-outline-warning' }} py-1 px-3 rounded-pill" style="font-size: 0.75rem;">
                                    <i class="fas {{ $t->is_approved ? 'fa-check' : 'fa-clock' }} me-1"></i>
                                    {{ $t->is_approved ? 'Approved' : 'Pending' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-center">
                            @if($t->is_active)
                                <span class="badge-soft badge-soft-success">Active</span>
                            @else
                                <span class="badge-soft badge-soft-secondary">Hidden</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('super_admin.cms.testimonials.edit', $t->id) }}" class="btn-icon-action me-1" title="Edit Review">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('super_admin.cms.testimonials.destroy', $t->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon-action btn-action-delete" title="Delete Review">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No testimonials found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
