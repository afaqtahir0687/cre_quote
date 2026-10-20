@extends('admin.layouts.app')

@section('title', 'Manage Products & Services')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">Products & Services</h2>
        <div class="d-flex align-items-center gap-3">
        <form action="{{ route('products.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search product..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            </div>
            @if(request('search'))
                <a href="{{ route('products.index') }}" class="btn btn-outline-danger"><i class="fas fa-times"></i></a>
            @endif
        </form>

        <a href="{{ route('products.create') }}" class="btn btn-primary-modern d-flex align-items-center">
        <i class="fas fa-plus me-2"></i> Add New Item
    </a>
    </div>
</div>

<x-card :padding="false">
    <x-table>
        <x-slot name="thead">
            <th>Product Info</th>
            <th>Price</th>
            <th class="text-center">Tax %</th>
            <th>Type</th>
            <th class="text-center">Stock</th>
            <th class="text-end pe-4">Action</th>
        </x-slot>

        @foreach($products as $product)
        <tr class="align-middle">
            <td>
                <div class="d-flex align-items-center">
                    
                    <div>
                        <div class="fw-bold text-dark">{{ $product->name }}</div>
                        <div class="text-muted small">SKU: {{ $product->sku }}</div>
                    </div>
                </div>
            </td>
            <td class="fw-bold text-dark">{{ format_currency($product->price) }}</td>
            <td class="text-center">{{ $product->tax_percentage }}%</td>
            <td>
                <span class="badge-soft {{ $product->type == 'product' ? 'badge-soft-info' : 'badge-soft-warning' }}">
                    {{ ucfirst($product->type) }}
                </span>
            </td>
            <td class="text-center">
                @if($product->type == 'product')
                    <span class="fw-medium {{ ($product->stock ?? 0) < 10 ? 'text-danger' : 'text-dark' }}">
                        {{ $product->stock ?? 0 }}
                    </span>
                @else
                    <span class="text-muted">---</span>
                @endif
            </td>
            <td class="text-end pe-4">
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item py-2" href="{{ route('products.edit', $product) }}"><i class="fas fa-edit me-2 text-info"></i> Edit</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('products.destroy', $product) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="dropdown-item py-2 text-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                                    <i class="fas fa-trash-alt me-2"></i> Delete
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        @endforeach
    </x-table>
    
    @if($products->hasPages())
        <div class="card-footer bg-transparent border-top p-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @endif
</x-card>
@endsection

