@extends('admin.layouts.app')

@section('title', __('Product Categories'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">{{ __('Categories') }}</h2>
    <div class="d-flex align-items-center gap-2">
        <form action="{{ route('categories.index') }}" method="GET" class="d-flex gap-2 mb-0">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('Search category...') }}" value="{{ request('search') }}">
            </div>
            @if(request('search'))
                <a href="{{ route('categories.index') }}" class="btn btn-light border"><i class="fas fa-times"></i></a>
            @endif
        </form>

        <a href="{{ route('categories.create') }}" class="btn btn-primary-modern text-nowrap">
            <i class="fas fa-plus me-2"></i> {{ __('Add Category') }}
        </a>
    </div>
</div>

<x-card :padding="false">
    <x-table>
        <x-slot name="thead">
            <th style="min-width: 200px;">{{ __('Category Name') }}</th>
            <th class="text-nowrap" style="min-width: 110px;">{{ __('Code') }}</th>
            <th>{{ __('Description') }}</th>
            <th class="text-center text-nowrap">{{ __('Products Count') }}</th>
            <th class="text-center text-nowrap">{{ __('Status') }}</th>
            <th class="text-end pe-4 text-nowrap">{{ __('Action') }}</th>
        </x-slot>

        @forelse($categories as $category)
        <tr class="align-middle">
            <td>
                <div>
                    <div class="fw-semibold text-dark" style="font-size: 0.85rem; line-height: 1.35;">{{ tdb($category->name) }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">{{ $category->slug }}</div>
                </div>
            </td>
            <td>
                @if($category->code)
                    <span class="badge-soft badge-soft-info font-monospace fw-semibold" style="font-size: 0.78rem;">{{ $category->code }}</span>
                @else
                    <span class="text-muted small">---</span>
                @endif
            </td>
            <td>
                <span class="text-muted" style="font-size: 0.85rem;">{{ $category->description ? tdb(Str::limit($category->description, 60)) : '---' }}</span>
            </td>
            <td class="text-center">
                <span class="badge-soft badge-soft-primary">
                    {{ $category->products_count }} {{ __('items') }}
                </span>
            </td>
            <td class="text-center">
                @if($category->status)
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
                            <a class="dropdown-item py-2" href="{{ route('categories.edit', $category) }}">
                                <i class="fas fa-edit me-2 text-info"></i> {{ __('Edit') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="dropdown-item py-2 text-danger" onclick="return confirm('{{ __('Are you sure you want to delete this category?') }}')">
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
            <td colspan="6" class="text-center py-5 text-muted">
                <i class="fas fa-tags fa-3x text-muted mb-3 d-block opacity-25"></i>
                <h5 class="text-muted">{{ __('No categories found.') }}</h5>
                <div class="mt-3">
                    <a href="{{ route('categories.create') }}" class="btn btn-primary-modern btn-sm">
                        <i class="fas fa-plus me-1"></i> {{ __('Add Category') }}
                    </a>
                </div>
            </td>
        </tr>
        @endforelse
    </x-table>

    @if($categories->hasPages())
        <div class="card-footer bg-transparent border-top p-4">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
    @endif
</x-card>
@endsection
