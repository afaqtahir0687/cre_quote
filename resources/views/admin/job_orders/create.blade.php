@extends('admin.layouts.app')

@section('title', 'Create Job Order')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('quotes.index') }}" class="btn btn-light btn-sm rounded-circle shadow-sm">
            <i class="fas fa-arrow-left text-muted"></i>
        </a>
        <h2 class="fw-bold mb-0">Create Job Order from Quote #{{ $quote->quote_number }}</h2>
    </div>
    <div class="d-flex align-items-center bg-white px-3 py-2 rounded-3 shadow-sm border">
        <span class="text-muted me-2 small fw-medium">Created By:</span>
        <i class="fas fa-user-circle text-primary me-1"></i>
        <span class="fw-semibold text-dark">{{ auth()->user()->name }}</span>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <p class="mb-3"><strong>Customer:</strong> {{ $quote->customer->name }}</p>
        <p class="mb-3"><strong>Quote Total:</strong> {{ number_format($quote->grand_total, 2) }}</p>
        <form action="{{ route('job-orders.from-quote', $quote) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success-modern">
                <i class="fas fa-tasks me-2"></i> Create Job Order
            </button>
        </form>
    </div>
</div>
@endsection
