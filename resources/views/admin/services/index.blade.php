@extends('admin.layouts.app')

@section('title', __('Services Management'))

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">{{ __('Services Catalog') }}</h2>
        <p class="text-muted small mb-0">{{ __('Manage company services portfolio across Creativity, Marketing, and Advertising.') }}</p>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2">
        <form action="{{ route('services.index') }}" method="GET" class="d-flex gap-2 mb-0">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('Search service...') }}" value="{{ request('search') }}">
            </div>
            @if(request('search'))
                <a href="{{ route('services.index', ['category' => request('category')]) }}" class="btn btn-light border"><i class="fas fa-times"></i></a>
            @endif
        </form>

        <a href="{{ route('services.create') }}" class="btn btn-primary-modern text-nowrap" style="background: #FA7436; border-color: #FA7436;">
            <i class="fas fa-plus me-1"></i> {{ __('Add New Service') }}
        </a>
    </div>
</div>

<!-- Category Tabs Filter -->
<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('services.index', ['search' => request('search')]) }}" 
       class="btn btn-sm rounded-pill px-3 py-2 fw-semibold {{ !request('category') || request('category') === 'all' ? 'btn-primary text-white' : 'btn-light border text-muted' }}">
        <i class="fas fa-th-large me-1"></i> {{ __('All Services') }} ({{ $counts['all'] }})
    </a>
    <a href="{{ route('services.index', ['category' => 'Creativity', 'search' => request('search')]) }}" 
       class="btn btn-sm rounded-pill px-3 py-2 fw-semibold {{ request('category') === 'Creativity' ? 'text-white' : 'btn-light border text-muted' }}"
       style="{{ request('category') === 'Creativity' ? 'background: #0A6FB7; border-color: #0A6FB7;' : '' }}">
        <i class="fas fa-paint-brush me-1 text-primary"></i> {{ __('Creativity') }} ({{ $counts['Creativity'] }})
    </a>
    <a href="{{ route('services.index', ['category' => 'Marketing', 'search' => request('search')]) }}" 
       class="btn btn-sm rounded-pill px-3 py-2 fw-semibold {{ request('category') === 'Marketing' ? 'text-white' : 'btn-light border text-muted' }}"
       style="{{ request('category') === 'Marketing' ? 'background: #00B894; border-color: #00B894;' : '' }}">
        <i class="fas fa-chart-line me-1 text-success"></i> {{ __('Marketing') }} ({{ $counts['Marketing'] }})
    </a>
    <a href="{{ route('services.index', ['category' => 'Advertising', 'search' => request('search')]) }}" 
       class="btn btn-sm rounded-pill px-3 py-2 fw-semibold {{ request('category') === 'Advertising' ? 'text-white' : 'btn-light border text-muted' }}"
       style="{{ request('category') === 'Advertising' ? 'background: #FA7436; border-color: #FA7436;' : '' }}">
        <i class="fas fa-bullhorn me-1" style="color: #FA7436;"></i> {{ __('Advertising') }} ({{ $counts['Advertising'] }})
    </a>
</div>

<x-card :padding="false">
    <x-table>
        <x-slot name="thead">
            <th style="min-width: 250px;">{{ __('Service Name') }}</th>
            <th class="text-nowrap" style="min-width: 140px;">{{ __('Category') }}</th>
            <th style="min-width: 260px;">{{ __('Scope & Features') }}</th>
            <th class="text-nowrap" style="min-width: 130px;">{{ __('Starting Rate') }}</th>
            <th class="text-center text-nowrap">{{ __('Status') }}</th>
            <th class="text-end pe-4 text-nowrap">{{ __('Action') }}</th>
        </x-slot>

        @forelse($services as $srv)
        <tr class="align-middle">
            <td>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px; background: linear-gradient(135deg, #0A6FB7, #FA7436); flex-shrink: 0; font-size: 1.1rem;">
                        <i class="{{ $srv->icon ?: 'fas fa-concierge-bell' }}"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.92rem; line-height: 1.35;">{{ $srv->name }}</div>
                        <div class="text-muted font-monospace" style="font-size: 0.75rem;">/services#{{ $srv->slug }}</div>
                    </div>
                </div>
            </td>
            <td>
                @if($srv->category === 'Creativity')
                    <span class="badge px-2 py-1 rounded-pill" style="background: rgba(10, 111, 183, 0.12); color: #0A6FB7; font-weight: 600; font-size: 0.8rem;">
                        <i class="fas fa-paint-brush me-1"></i> Creativity
                    </span>
                @elseif($srv->category === 'Marketing')
                    <span class="badge px-2 py-1 rounded-pill" style="background: rgba(0, 184, 148, 0.12); color: #00B894; font-weight: 600; font-size: 0.8rem;">
                        <i class="fas fa-chart-line me-1"></i> Marketing
                    </span>
                @else
                    <span class="badge px-2 py-1 rounded-pill" style="background: rgba(250, 116, 54, 0.12); color: #FA7436; font-weight: 600; font-size: 0.8rem;">
                        <i class="fas fa-bullhorn me-1"></i> Advertising
                    </span>
                @endif
            </td>
            <td>
                <div class="text-muted mb-1" style="font-size: 0.82rem; line-height: 1.4;">
                    {{ Str::limit($srv->description, 80) }}
                </div>
                @if(is_array($srv->features) && count($srv->features) > 0)
                    <div class="d-flex flex-wrap gap-1">
                        @foreach(array_slice($srv->features, 0, 2) as $f)
                            <span class="badge bg-light text-muted border extra-small" style="font-size: 0.7rem;">
                                <i class="fas fa-check text-success me-1"></i> {{ $f }}
                            </span>
                        @endforeach
                        @if(count($srv->features) > 2)
                            <span class="badge bg-light text-muted border extra-small" style="font-size: 0.7rem;">+{{ count($srv->features) - 2 }} more</span>
                        @endif
                    </div>
                @endif
            </td>
            <td>
                @if($srv->starting_price)
                    <strong class="text-dark" style="color: #0A6FB7 !important; font-size: 0.95rem;">
                        {{ format_currency($srv->starting_price) }}
                    </strong>
                    <span class="text-muted extra-small">/ {{ $srv->price_unit ?: 'Project' }}</span>
                @else
                    <span class="text-muted small">Custom Quote</span>
                @endif
            </td>
            <td class="text-center">
                @if($srv->is_active)
                    <span class="badge-soft badge-soft-success">{{ __('Active') }}</span>
                @else
                    <span class="badge-soft badge-soft-secondary">{{ __('Inactive') }}</span>
                @endif
            </td>
            <td class="text-end pe-4">
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('services.edit', $srv) }}">
                                <i class="fas fa-pencil-alt text-primary me-2"></i> {{ __('Edit Service') }}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ url('/services#' . $srv->slug) }}" target="_blank">
                                <i class="fas fa-external-link-alt text-info me-2"></i> {{ __('View on Website') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('services.destroy', $srv) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this service?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="fas fa-trash-alt me-2"></i> {{ __('Delete Service') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center py-5 text-muted">
                <i class="fas fa-concierge-bell fa-3x mb-3 text-muted opacity-50"></i>
                <p class="mb-2">{{ __('No services found in this category.') }}</p>
                <a href="{{ route('services.create') }}" class="btn btn-sm btn-primary-modern">
                    {{ __('Create Your First Service') }}
                </a>
            </td>
        </tr>
        @endforelse
    </x-table>

    @if($services->hasPages())
        <div class="p-3 border-top">
            {{ $services->links() }}
        </div>
    @endif
</x-card>
@endsection

