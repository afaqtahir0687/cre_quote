@extends('admin.layouts.app')

@section('title', __('Shop Categories'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-2">
        <h2 class="fw-bold text-dark mb-0">{{ __('Shop Categories') }}</h2>
        <span class="badge bg-primary-subtle text-primary border px-2 py-1 rounded-pill small">
            {{ $businessTypes->total() }} {{ __('Total') }}
        </span>
    </div>
    <div class="d-flex gap-2">
        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2 mb-0">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="{{ __('Search Categories...') }}" onkeypress="if(event.keyCode == 13) this.form.submit()">
            </div>
            @if(request()->filled('search'))
                <a href="{{ url()->current() }}" class="btn btn-light border" title="{{ __('Clear Filters') }}"><i class="fas fa-times"></i></a>
            @endif
        </form>
        <a href="{{ route('super_admin.business_types.create') }}" class="btn btn-primary-modern text-nowrap">
            <i class="fas fa-plus me-2"></i> {{ __('Add Category') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<x-card :padding="false">
    @if($businessTypes->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-layer-group fa-3x text-muted mb-3 opacity-50"></i>
            <h5 class="text-muted">{{ __('No Business Categories Found') }}</h5>
            <p class="text-muted small">{{ __('Configure custom business categories to adapt ERP workflows for different shop types.') }}</p>
            <a href="{{ route('super_admin.business_types.create') }}" class="btn btn-primary-modern">
                <i class="fas fa-plus me-2"></i> {{ __('Add Category') }}
            </a>
        </div>
    @else
        <x-table>
            <x-slot name="thead">
                <th class="text-nowrap" style="width: 70px;">{{ __('Order') }}</th>
                <th style="min-width: 220px;">{{ __('Category / Industry') }}</th>
                <th class="text-nowrap">{{ __('Code Key') }}</th>
                <th style="min-width: 260px;">{{ __('Default Modules') }}</th>
                <th class="text-nowrap">{{ __('Starter Departments') }}</th>
                <th class="text-nowrap text-center">{{ __('Active Shops') }}</th>
                <th class="text-nowrap text-center">{{ __('Status') }}</th>
                <th class="text-end text-nowrap pe-4">{{ __('Action') }}</th>
            </x-slot>

            @foreach($businessTypes as $type)
                <tr class="align-middle">
                    <td class="text-nowrap">
                        <span class="badge bg-light text-dark border font-monospace">#{{ $type->sort_order }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" 
                                 style="width: 40px; height: 40px; background: rgba(67, 97, 238, 0.08); color: var(--clr-primary, #4361EE); font-size: 1.1rem;">
                                <i class="{{ $type->icon ?: 'fas fa-briefcase' }}"></i>
                            </div>
                            <div>
                                <div class="fw-semibold text-dark" style="font-size: 0.9rem; line-height: 1.35;">
                                    {{ $type->name }}
                                </div>
                                @if(!empty($type->name_ar))
                                    <div class="small text-muted font-arabic">{{ $type->name_ar }}</div>
                                @endif
                                @if(!empty($type->description))
                                    <div class="text-muted extra-small text-truncate" style="max-width: 260px; font-size: 0.76rem;">
                                        {{ $type->description }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <span class="font-monospace text-secondary small">{{ $type->slug }}</span>
                    </td>
                    <td>
                        @php
                            $mods = is_array($type->default_modules) ? $type->default_modules : [];
                        @endphp
                        <div class="d-flex flex-wrap gap-1" style="max-width: 320px;">
                            @forelse(array_slice($mods, 0, 4) as $mKey)
                                <span class="badge bg-light text-dark border" style="font-size: 0.72rem; font-weight: 500;">
                                    {{ $allModules[$mKey]['name'] ?? $mKey }}
                                </span>
                            @empty
                                <span class="text-muted small">—</span>
                            @endforelse
                            @if(count($mods) > 4)
                                <span class="badge bg-light text-primary border" style="font-size: 0.72rem; font-weight: 600;">
                                    +{{ count($mods) - 4 }} {{ __('more') }}
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="text-nowrap">
                        @php
                            $depts = is_array($type->default_departments) ? $type->default_departments : [];
                        @endphp
                        @if(count($depts) > 0)
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-sitemap text-primary me-1"></i> {{ count($depts) }} {{ __('Departments') }}
                            </span>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td class="text-nowrap text-center">
                        @if($type->shops_count > 0)
                            <a href="{{ route('super_admin.shops.index') }}?business_type={{ $type->slug }}" 
                               class="badge bg-success-subtle text-success border border-success-subtle text-decoration-none px-3 py-1 fw-bold">
                                {{ $type->shops_count }} {{ __('Shops') }}
                            </a>
                        @else
                            <span class="text-muted small">0</span>
                        @endif
                    </td>
                    <td class="text-nowrap text-center">
                        @if($type->is_active)
                            <span class="badge-soft badge-soft-success">
                                <i class="fas fa-check-circle me-1"></i> {{ __('Active') }}
                            </span>
                        @else
                            <span class="badge-soft badge-soft-danger">
                                <i class="fas fa-times-circle me-1"></i> {{ __('Disabled') }}
                            </span>
                        @endif
                    </td>
                    <td class="text-end pe-4 text-nowrap">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('super_admin.business_types.edit', $type->id) }}">
                                        <i class="fas fa-edit me-2 text-warning"></i> {{ __('Edit') }}
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('super_admin.business_types.toggle_status', $type->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2">
                                            @if($type->is_active)
                                                <i class="fas fa-toggle-off me-2 text-secondary"></i> {{ __('Deactivate') }}
                                            @else
                                                <i class="fas fa-toggle-on me-2 text-success"></i> {{ __('Activate') }}
                                            @endif
                                        </button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    @if($type->shops_count == 0)
                                        <form action="{{ route('super_admin.business_types.destroy', $type->id) }}" method="POST" 
                                              onsubmit="return confirm('{{ __('Are you sure you want to delete category \":name\"?', ['name' => $type->name]) }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item py-2 text-danger">
                                                <i class="fas fa-trash me-2"></i> {{ __('Delete') }}
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" class="dropdown-item py-2 text-muted disabled" title="{{ __('In use by active shops') }}">
                                            <i class="fas fa-lock me-2"></i> {{ __('Delete (In Use)') }}
                                        </button>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table>

        @if($businessTypes->hasPages())
            <div class="card-footer bg-transparent border-top py-3 px-4">
                {{ $businessTypes->links() }}
            </div>
        @endif
    @endif
</x-card>
@endsection
