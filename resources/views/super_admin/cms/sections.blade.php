@extends('admin.layouts.app')

@section('title', __('Homepage Sections & Layout Manager'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Homepage Sections & Layout Manager') }}</h2>
            <span class="badge-soft badge-soft-success px-2 py-1 rounded-pill small">
                Drag / Reorder Sections
            </span>
        </div>
        <p class="text-muted small mb-0">{{ __('Enable or disable sections, change their display order on the landing page, and customize section titles.') }}</p>
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

<form action="{{ route('super_admin.cms.sections.update') }}" method="POST">
    @csrf

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="fw-bold text-dark mb-0"><i class="fas fa-layer-group me-2 text-primary"></i> {{ __('Homepage Sections Configuration') }}</h5>
            <button type="submit" class="btn btn-primary-modern btn-sm px-4">
                <i class="fas fa-save me-1"></i> {{ __('Save Section Order & Settings') }}
            </button>
        </div>
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th style="width: 90px;" class="text-center">{{ __('Order') }}</th>
                        <th style="width: 100px;" class="text-center">{{ __('Active') }}</th>
                        <th style="width: 220px;">{{ __('Section Name') }}</th>
                        <th>{{ __('Headline / Title') }}</th>
                        <th>{{ __('Subtitle / Lead Description') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sections as $index => $section)
                        <tr>
                            <td class="text-center">
                                <input type="hidden" name="sections[{{ $index }}][id]" value="{{ $section->id }}">
                                <input type="number" name="sections[{{ $index }}][sort_order]" class="form-control form-control-sm text-center fw-bold mx-auto" value="{{ $section->sort_order }}" style="width: 65px;">
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-inline-block">
                                    <input type="checkbox" name="sections[{{ $index }}][is_active]" class="form-check-input" value="1" {{ $section->is_active ? 'checked' : '' }}>
                                </div>
                            </td>
                            <td>
                                <strong class="text-dark">{{ $section->name }}</strong>
                                <div class="small text-muted font-monospace">#{{ $section->section_key }}</div>
                            </td>
                            <td>
                                <input type="text" name="sections[{{ $index }}][title]" class="form-control form-control-sm" value="{{ $section->title }}" placeholder="Optional section heading">
                            </td>
                            <td>
                                <input type="text" name="sections[{{ $index }}][subtitle]" class="form-control form-control-sm" value="{{ $section->subtitle }}" placeholder="Optional description">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white py-3 text-end border-top">
            <button type="submit" class="btn btn-primary-modern px-5">
                <i class="fas fa-save me-1"></i> {{ __('Save Section Order & Settings') }}
            </button>
        </div>
    </div>
</form>
@endsection
