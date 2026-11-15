@extends('admin.layouts.app')

@section('title', __('Shop Business Categories & Industries'))

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h2 class="fw-bold text-dark mb-0">{{ __('Shop Business Categories & Industries') }}</h2>
            <span class="badge bg-primary text-white rounded-pill px-3 py-1">
                {{ $businessTypes->count() }} {{ __('Categories') }}
            </span>
        </div>
        <p class="text-muted small mb-0">
            {{ __('Manage custom business categories, default module sets, and starter departments. When creating a shop, these categories adapt the ERP workflow automatically.') }}
        </p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('super_admin.shops.create') }}" class="btn btn-outline-secondary">
            <i class="fas fa-store me-1"></i> {{ __('Create Shop') }}
        </a>
        <a href="{{ route('super_admin.business_types.create') }}" class="btn btn-primary d-flex align-items-center">
            <i class="fas fa-plus me-1"></i> {{ __('Add New Category') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0">
            <i class="fas fa-layer-group text-primary me-2"></i>{{ __('Configured Business Industries') }}
        </h5>
        <span class="text-muted extra-small">
            {{ __('Categories appear as cards on the Shop Create/Edit screens') }}
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-muted extra-small text-uppercase fw-bold" style="letter-spacing: 0.05em;">
                    <th class="ps-4" style="width: 70px;">{{ __('Order') }}</th>
                    <th>{{ __('Category / Industry') }}</th>
                    <th>{{ __('Code Slug') }}</th>
                    <th>{{ __('Default Enabled Modules') }}</th>
                    <th>{{ __('Default Departments') }}</th>
                    <th class="text-center">{{ __('Active Shops') }}</th>
                    <th class="text-center">{{ __('Status') }}</th>
                    <th class="text-end pe-4">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($businessTypes as $type)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-light text-dark border font-monospace">#{{ $type->sort_order }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" 
                                     style="width: 44px; height: 44px; background: #EEF2FF; color: #4F46E5; font-size: 1.2rem;">
                                    <i class="{{ $type->icon ?: 'fas fa-briefcase' }}"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-6">{{ $type->name }}</div>
                                    @if($type->name_ar)
                                        <div class="text-muted extra-small font-arabic">{{ $type->name_ar }}</div>
                                    @endif
                                    @if($type->description)
                                        <p class="text-muted extra-small mb-0 text-truncate" style="max-width: 280px;">
                                            {{ $type->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary border font-monospace">
                                {{ $type->slug }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1" style="max-width: 320px;">
                                @php
                                    $mods = is_array($type->default_modules) ? $type->default_modules : [];
                                @endphp
                                @forelse(array_slice($mods, 0, 5) as $mKey)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle extra-small" style="font-size: 0.7rem;">
                                        {{ $allModules[$mKey]['name'] ?? $mKey }}
                                    </span>
                                @empty
                                    <span class="text-muted extra-small">{{ __('No default modules') }}</span>
                                @endforelse
                                @if(count($mods) > 5)
                                    <span class="badge bg-secondary-subtle text-secondary extra-small" style="font-size: 0.7rem;">
                                        +{{ count($mods) - 5 }} {{ __('more') }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @php
                                $depts = is_array($type->default_departments) ? $type->default_departments : [];
                            @endphp
                            @if(count($depts) > 0)
                                <span class="badge bg-info-subtle text-info border border-info-subtle extra-small">
                                    <i class="fas fa-sitemap me-1"></i> {{ count($depts) }} {{ __('Departments') }}
                                </span>
                            @else
                                <span class="text-muted extra-small">{{ __('None') }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($type->shops_count > 0)
                                <a href="{{ route('super_admin.shops.index') }}?business_type={{ $type->slug }}" class="badge bg-success text-white text-decoration-none px-3 py-1">
                                    {{ $type->shops_count }} {{ __('Shops') }}
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">0</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('super_admin.business_types.toggle_status', $type->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent" title="{{ $type->is_active ? __('Click to Deactivate') : __('Click to Activate') }}">
                                    @if($type->is_active)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="fas fa-check-circle me-1"></i> {{ __('Active') }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                            <i class="fas fa-times-circle me-1"></i> {{ __('Disabled') }}
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group">
                                <a href="{{ route('super_admin.business_types.edit', $type->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Edit Category') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($type->shops_count == 0)
                                    <form action="{{ route('super_admin.business_types.destroy', $type->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete category \":name\"?', ['name' => $type->name]) }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete Category') }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-outline-secondary disabled" title="{{ __('Cannot delete: In use by active shops') }}">
                                        <i class="fas fa-lock"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fas fa-folder-open text-muted fa-3x mb-3 d-block opacity-50"></i>
                            <h5 class="text-muted fw-bold">{{ __('No Business Categories Found') }}</h5>
                            <p class="text-muted small">{{ __('Click "Add New Category" above to configure your first industry preset.') }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4 p-4 rounded-4 bg-light border d-flex align-items-center gap-3">
    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.2rem;">
        <i class="fas fa-info"></i>
    </div>
    <div>
        <h6 class="fw-bold text-dark mb-1">{{ __('How Business Categories Work:') }}</h6>
        <p class="text-muted small mb-0">
            {{ __('Each category defined here automatically appears on the "Create Shop" screen as an interactive card. Selecting a category immediately auto-selects its default modules in the switchboard and auto-seeds its starter departments upon shop creation.') }}
        </p>
    </div>
</div>
@endsection
