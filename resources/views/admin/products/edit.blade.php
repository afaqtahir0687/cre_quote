@extends('admin.layouts.app')

@section('title', isset($product) ? 'Edit Item' : 'Add Item')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5>{{ isset($product) ? 'Edit Item' : 'Add Item' }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" method="POST">
            @csrf
            @if(isset($product)) @method('PUT') @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Item Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name ?? '') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku ?? '') }}">
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description', $product->description ?? '') }}</textarea>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Price *</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price ?? '') }}" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tax (%)</label>
                    <input type="number" step="0.01" name="tax_percentage" class="form-control" value="{{ old('tax_percentage', $product->tax_percentage ?? 0) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="product" {{ old('type', $product->type ?? 'product') == 'product' ? 'selected' : '' }}>Product</option>
                        <option value="service" {{ old('type', $product->type ?? 'product') == 'service' ? 'selected' : '' }}>Service</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stock (Optional)</label>
                    <input type="number" name="stock" class="form-control" value="{{ old('stock', $product->stock ?? '') }}">
                </div>
            </div>
            
            <div class="text-end">
                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ isset($product) ? 'Update' : 'Save' }} Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
