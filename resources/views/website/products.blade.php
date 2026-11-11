@extends('website.layouts.app')

@section('title', 'Products & Materials Catalog - ' . ($settings['site_name'] ?? 'Crelogics ERP'))

@section('content')
<!-- Products Hero Section -->
<section class="hero-section py-5 position-relative overflow-hidden" style="background: radial-gradient(circle at 10% 20%, rgba(67, 97, 238, 0.08) 0%, transparent 45%), radial-gradient(circle at 90% 80%, rgba(0, 184, 148, 0.06) 0%, transparent 45%), #FFFFFF;">
    <div class="container py-4 text-center">
        <span class="badge-pill-soft mb-3">
            <i class="fas fa-boxes text-primary me-1"></i> Products & Materials Catalog
        </span>
        <h1 class="section-title text-dark fw-bold mb-3">
            Premium Signage, Print & <span class="gradient-text-primary">Raw Materials</span>
        </h1>
        <p class="section-subtitle text-muted mx-auto" style="max-width: 720px;">
            Explore our certified commercial inventory including outdoor flex, acrylic sheets, ACP panels, 3D channel letters, vinyl, and promotional displays.
        </p>

        <!-- Search Bar -->
        <div class="mx-auto mt-4" style="max-width: 620px;">
            <form action="{{ url('/products') }}" method="GET" class="d-flex gap-2">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                <div class="input-group input-group-lg shadow-sm rounded-pill overflow-hidden bg-white border">
                    <span class="input-group-text bg-white border-0 ps-3 text-muted">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control border-0 shadow-none" placeholder="Search by product name, SKU or keyword..." style="font-size: 0.95rem;">
                    <button class="btn btn-erp-primary px-4 rounded-pill m-1" type="submit">
                        <span>Search</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Category Filter Pills & Products Grid Section -->
<section class="py-5" style="background-color: #F8FAFC;">
    <div class="container">
        <!-- Category Filter Pills -->
        <div class="d-flex flex-wrap justify-content-center gap-2 mb-5">
            <a href="{{ url('/products' . (request('search') ? '?search=' . urlencode(request('search')) : '')) }}" 
               class="btn btn-sm rounded-pill px-3 py-2 fw-semibold {{ !request('category') ? 'btn-primary' : 'btn-outline-secondary bg-white' }}"
               style="font-size: 0.85rem;">
                All Categories
            </a>
            @foreach($categories as $cat)
                <a href="{{ url('/products?category=' . $cat->id . (request('search') ? '&search=' . urlencode(request('search')) : '')) }}" 
                   class="btn btn-sm rounded-pill px-3 py-2 fw-semibold {{ request('category') == $cat->id ? 'btn-primary' : 'btn-outline-secondary bg-white' }}"
                   style="font-size: 0.85rem;">
                    {{ $cat->name }}
                    <span class="badge bg-light text-muted border rounded-pill ms-1">{{ $cat->products_count }}</span>
                </a>
            @endforeach
        </div>

        @if(request('search') || request('category'))
            <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 border">
                <span class="text-muted small">
                    Showing results for: 
                    @if(request('search')) <strong>"{{ request('search') }}"</strong> @endif
                    @if(request('category')) 
                        @php $activeCat = $categories->firstWhere('id', request('category')); @endphp
                        in <strong>{{ $activeCat->name ?? 'Category' }}</strong>
                    @endif
                </span>
                <a href="{{ url('/products') }}" class="btn btn-sm btn-outline-danger py-1 px-3 rounded-pill" style="font-size: 0.78rem;">
                    <i class="fas fa-times me-1"></i> Clear Filters
                </a>
            </div>
        @endif

        <!-- Products Grid -->
        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card border rounded-4 h-100 shadow-sm bg-white overflow-hidden d-flex flex-column" style="transition: transform 0.25s ease, box-shadow 0.25s ease;">
                        <!-- Product Preview Box -->
                        <div class="position-relative overflow-hidden" style="background: #F8FAFC; height: 200px; border-bottom: 1px solid #EDF2F7;">
                            @if(!empty($product->image) && file_exists(public_path($product->image)))
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.35s ease;" class="w-100 h-100">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100">
                                    <div class="p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 74px; height: 74px; background: rgba(67, 97, 238, 0.1); color: #4361EE; font-size: 2rem;">
                                        <i class="fas fa-box-open"></i>
                                    </div>
                                </div>
                            @endif
                            <span class="badge-soft badge-soft-primary position-absolute top-0 end-0 m-3" style="font-size: 0.72rem; z-index: 2;">
                                {{ $product->category->name ?? 'Materials' }}
                            </span>
                        </div>

                        <!-- Card Body -->
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-light text-muted border font-monospace extra-small" style="font-size: 0.72rem;">
                                    {{ $product->sku }}
                                </span>
                                @if($product->stock > 0)
                                    <span class="badge-soft badge-soft-success" style="font-size: 0.72rem;">
                                        <i class="fas fa-check-circle me-1"></i> In Stock ({{ $product->stock }})
                                    </span>
                                @else
                                    <span class="badge-soft badge-soft-warning" style="font-size: 0.72rem;">
                                        Custom Order
                                    </span>
                                @endif
                            </div>

                            <h5 class="fw-bold text-dark mt-2 mb-2 fs-6" style="line-height: 1.35;">{{ $product->name }}</h5>
                            <p class="text-muted small mb-3 flex-grow-1" style="font-size: 0.84rem; line-height: 1.5;">
                                {{ Str::limit($product->description, 75) ?: 'Industrial grade certified material engineered for architectural and commercial signage.' }}
                            </p>

                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted extra-small d-block" style="font-size: 0.72rem;">Rate / Unit:</span>
                                    <strong class="text-primary fs-5">{{ format_currency($product->sale_price ?: $product->price) }}</strong>
                                    <span class="text-muted extra-small" style="font-size: 0.75rem;">/ {{ $product->unit->name ?? 'pc' }}</span>
                                </div>
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? '996598500143') }}?text={{ urlencode('Hello, I would like to order / inquire about: ' . $product->name . ' (SKU: ' . $product->sku . ')') }}" 
                                   target="_blank" 
                                   class="btn-erp-secondary btn-sm px-3 py-2"
                                   title="Inquire via WhatsApp">
                                    <i class="fab fa-whatsapp text-success me-1"></i> Inquire
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="p-4 rounded-4 bg-white border mx-auto shadow-sm" style="max-width: 480px;">
                        <i class="fas fa-search fa-3x text-muted opacity-50 mb-3"></i>
                        <h5 class="fw-bold text-dark">No Products Found</h5>
                        <p class="text-muted small mb-3">No products matched your selected criteria or search term.</p>
                        <a href="{{ url('/products') }}" class="btn-erp-primary btn-sm px-4">
                            View All Products
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-5 d-flex justify-content-center">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</section>

<!-- Custom Dimension Sizing & Bulk Order Box -->
<section class="py-5 bg-white border-top">
    <div class="container">
        <div class="p-4 p-lg-5 rounded-4 border shadow-sm" style="background: linear-gradient(135deg, #F8FAFC 0%, #EDF2F7 100%);">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="badge-soft badge-soft-primary mb-2">Custom Dimensions Supported</span>
                    <h3 class="fw-bold text-dark mb-2">Need Custom Cut Sizing or Square Feet Billing?</h3>
                    <p class="text-muted mb-0" style="line-height: 1.6;">
                        Our CreLogics automated dimension calculator calculates width &times; height area formulas, waste margins, edge welding, and frame fabrication automatically on every quotation.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ url('/#contact') }}" class="btn-erp-primary px-4 py-3">
                        <i class="fas fa-calculator me-2"></i> Request Custom Cut Quote
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

