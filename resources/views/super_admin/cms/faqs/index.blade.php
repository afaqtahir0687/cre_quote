@extends('admin.layouts.app')

@section('title', __('Frequently Asked Questions (FAQ) Manager'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Frequently Asked Questions Manager') }}</h2>
            <span class="badge-soft badge-soft-success px-2 py-1 rounded-pill small">
                Help & FAQs
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Manage questions and answers displayed in the accordion on the landing page.') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.cms.faqs.create') }}" class="btn btn-primary-modern btn-sm">
            <i class="fas fa-plus me-1"></i> {{ __('Add FAQ') }}
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
        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-question-circle me-2 text-success"></i> {{ __('Registered FAQs (' . $faqs->count() . ')') }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 320px;">{{ __('Question') }}</th>
                    <th>{{ __('Answer') }}</th>
                    <th style="width: 100px;" class="text-center">{{ __('Order') }}</th>
                    <th style="width: 100px;" class="text-center">{{ __('Status') }}</th>
                    <th style="width: 120px;" class="text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faqs as $f)
                    <tr>
                        <td><strong class="text-dark">{{ $f->question }}</strong></td>
                        <td class="small text-muted">{{ Str::limit($f->answer, 100) }}</td>
                        <td class="text-center fw-bold">{{ $f->sort_order }}</td>
                        <td class="text-center">
                            @if($f->is_active)
                                <span class="badge-soft badge-soft-success">Active</span>
                            @else
                                <span class="badge-soft badge-soft-secondary">Hidden</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('super_admin.cms.faqs.edit', $f->id) }}" class="btn-icon-action me-1" title="Edit FAQ">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('super_admin.cms.faqs.destroy', $f->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon-action btn-action-delete" title="Delete FAQ">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No FAQs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
