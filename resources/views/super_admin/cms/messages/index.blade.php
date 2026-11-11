@extends('admin.layouts.app')

@section('title', __('Landing Page Contact Inquiries'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Landing Page Contact Inquiries') }}</h2>
            <span class="badge-soft badge-soft-danger px-2 py-1 rounded-pill small">
                Visitor Leads
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Inquiries and leads submitted by visitors through the public landing page contact form.') }}</p>
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

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-inbox me-2 text-primary"></i> {{ __('All Inquiries (' . $messages->count() . ')') }}</h5>
    </div>
    <div class="table-responsive">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 80px;">Status</th>
                    <th>Sender & Contact</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th style="width: 120px;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $msg)
                    <tr class="{{ $msg->is_read ? '' : 'table-light fw-semibold' }}">
                        <td>
                            @if(!$msg->is_read)
                                <span class="badge-soft badge-soft-danger">New</span>
                            @else
                                <span class="badge-soft badge-soft-secondary">Read</span>
                            @endif
                        </td>
                        <td>
                            <strong class="text-dark">{{ $msg->name }}</strong>
                            <div class="small text-muted">
                                <a href="mailto:{{ $msg->email }}" class="text-primary">{{ $msg->email }}</a>
                                @if($msg->phone) &bull; {{ $msg->phone }} @endif
                            </div>
                        </td>
                        <td><span class="text-dark">{{ $msg->subject ?: 'General Inquiry' }}</span></td>
                        <td class="small text-muted" style="max-width: 380px;">{{ $msg->message }}</td>
                        <td class="small text-muted text-nowrap">{{ $msg->created_at->diffForHumans() }}</td>
                        <td class="text-end">
                            @if(!$msg->is_read)
                                <form action="{{ route('super_admin.cms.messages.read', $msg->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-icon-action btn-action-success me-1" title="Mark as Read">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('super_admin.cms.messages.destroy', $msg->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon-action btn-action-delete" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No messages received yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
