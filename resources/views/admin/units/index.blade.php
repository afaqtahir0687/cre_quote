@extends('admin.layouts.app')

@section('title', __('Product Units'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">{{ __('Units') }}</h2>
    <div class="d-flex align-items-center gap-2">
        <form action="{{ route('units.index') }}" method="GET" class="d-flex gap-2 mb-0">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('Search unit...') }}" value="{{ request('search') }}">
            </div>
            @if(request('search'))
                <a href="{{ route('units.index') }}" class="btn btn-light border"><i class="fas fa-times"></i></a>
            @endif
        </form>

        <a href="{{ route('units.create') }}" class="btn btn-primary-modern text-nowrap">
            <i class="fas fa-plus me-2"></i> {{ __('Add Unit') }}
        </a>
    </div>
</div>

<x-card :padding="false">
    <x-table>
        <x-slot name="thead">
            <th style="min-width: 180px;">{{ __('Unit Name') }}</th>
            <th class="text-center text-nowrap">{{ __('Code / Symbol') }}</th>
            <th class="text-center text-nowrap">{{ __('Products Count') }}</th>
            <th class="text-center text-nowrap">{{ __('Status') }}</th>
            <th class="text-end pe-4 text-nowrap">{{ __('Action') }}</th>
        </x-slot>

        @forelse($units as $unit)
        <tr class="align-middle">
            <td>
                <div class="fw-semibold text-dark" style="font-size: 0.88rem;">{{ tdb($unit->name) }}</div>
            </td>
            <td class="text-center">
                <span class="badge-soft badge-soft-primary fw-bold font-monospace">{{ $unit->code ?: '---' }}</span>
            </td>
            <td class="text-center">
                <span class="badge-soft badge-soft-info">
                    {{ $unit->products_count }} {{ __('items') }}
                </span>
            </td>
            <td class="text-center">
                @if($unit->status)
                    <span class="badge-soft badge-soft-success">{{ __('Active') }}</span>
                @else
                    <span class="badge-soft badge-soft-secondary">{{ __('Inactive') }}</span>
                @endif
            </td>
            <td class="text-end pe-4">
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('units.edit', $unit) }}">
                                <i class="fas fa-edit me-2 text-info"></i> {{ __('Edit') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="fas fa-trash-alt me-2"></i> {{ __('Delete') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center py-5 text-muted">
                <i class="fas fa-balance-scale fa-3x text-muted mb-3 d-block opacity-25"></i>
                <h5 class="text-muted">{{ __('No units found.') }}</h5>
                <div class="mt-3">
                    <a href="{{ route('units.create') }}" class="btn btn-primary-modern btn-sm">
                        <i class="fas fa-plus me-1"></i> {{ __('Add Unit') }}
                    </a>
                </div>
            </td>
        </tr>
        @endforelse
    </x-table>

    @if($units->hasPages())
        <div class="card-footer bg-transparent border-top p-4">
            {{ $units->links('pagination::bootstrap-5') }}
        </div>
    @endif
</x-card>
@endsection
