@extends('admin.layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">Activity Logs (Deletions)</h2>
    <div class="d-flex align-items-center gap-3">
        <form action="{{ route('system_logs.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search logs..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            </div>
            @if(request('search'))
                <a href="{{ route('system_logs.index') }}" class="btn btn-outline-danger"><i class="fas fa-times"></i></a>
            @endif
        </form>
    </div>
</div>

<div class="card-modern">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Date & Time</th>
                        <th>User / Employee</th>
                        <th>Module</th>
                        <th>Action Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="ps-4 text-muted small">
                            {{ $log->created_at->format('M d, Y h:i A') }}
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $log->user->name ?? 'System/Unknown' }}</div>
                            <div class="text-muted small">{{ $log->user->email ?? '' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-danger text-white border">{{ $log->model_type }}</span>
                        </td>
                        <td>
                            <div class="text-dark">{{ $log->description }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="fas fa-clipboard-list fa-3x mb-3 text-light"></i>
                            <p class="mb-0">No deletion logs found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($logs->hasPages())
    <div class="card-footer bg-white border-top p-3">
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection


