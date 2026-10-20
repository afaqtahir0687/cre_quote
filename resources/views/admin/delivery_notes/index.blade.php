@extends('admin.layouts.app')

@section('title', 'Manage Delivery Notes')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0">Delivery Notes</h2>
            <div class="d-flex align-items-center gap-3">
        <form action="{{ url()->current() }}" method="GET" class="d-flex gap-2 mb-0">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Search DN..." onkeypress="if(event.keyCode == 13) this.form.submit()">
            </div>
            <input type="date" name="date" value="{{ request('date') }}" class="form-control" style="max-width: 150px; cursor: pointer;" title="Filter by date" onclick="this.showPicker()" onchange="this.form.submit()">
            @if(request()->has('search') || request()->has('date'))
                <a href="{{ url()->current() }}" class="btn btn-light border" title="Clear Filters"><i class="fas fa-times"></i></a>
            @endif
        </form>

        <a href="{{ route('delivery_notes.create') }}" class="btn btn-primary d-flex align-items-center">
            <i class="fas fa-plus me-2"></i> Create Delivery Note
        </a>
    </div>
    </div>

    <x-card :padding="false">
        <x-table>
            <x-slot name="thead">
                <th>Delivery #</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Invoice #</th>
                <th>Employee</th>
                <th>Status</th>
                <th class="text-end">Action</th>
            </x-slot>

            @foreach($deliveryNotes as $deliveryNote)
                <tr>
                    <td class="fw-bold text-primary">{{ $deliveryNote->delivery_number }}</td>
                    <td class="text-muted">{{ $deliveryNote->delivery_date }}</td>
                    <td>
                        <div class="fw-bold">{{ $deliveryNote->customer->name ?? '' }}</div>
                        @if(!empty($deliveryNote->customer->company_name))
                            <div class="small text-muted"><i class="fas fa-building me-1"></i>{{ $deliveryNote->customer->company_name }}</div>
                        @endif
                        @if(!empty($deliveryNote->customer->phone))
                            <div class="small text-muted mt-1"><i class="fas fa-phone-alt me-1 text-primary"></i>{{ $deliveryNote->customer->phone }}</div>
                        @endif
                    </td>
                    <td class="fw-semibold text-dark">{{ $deliveryNote->invoice->invoice_number }}</td>
                    <td>
                        @if($deliveryNote->employee)
                            <div class="d-flex align-items-center">
                                
                                <span class="text-muted small">{{ $deliveryNote->employee->name }}</span>
                            </div>
                        @else
                            <span class="badge bg-light text-muted border" style="font-size: 0.75rem;">N/A</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statusClass = match ($deliveryNote->status) {
                                'Delivered' => 'badge-soft-success',
                                'Pending' => 'badge-soft-warning',
                                'Cancelled' => 'badge-soft-danger',
                                default => 'badge-soft-secondary'
                            };
                        @endphp
                        <span class="badge-soft {{ $statusClass }}">
                            {{ $deliveryNote->status }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle shadow-none" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item py-2" href="{{ route('delivery_notes.show', $deliveryNote) }}"><i
                                            class="fas fa-eye me-2 text-primary"></i> View Detail</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('delivery_notes.edit', $deliveryNote) }}"><i
                                            class="fas fa-edit me-2 text-info"></i> Edit</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('delivery_notes.pdf', $deliveryNote) }}"><i
                                            class="fas fa-file-pdf me-2 text-danger"></i> Download PDF</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('delivery_notes.update_status', $deliveryNote) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="Delivered">
                                        <button type="submit" class="dropdown-item py-2 text-success"
                                            onclick="return confirm('Mark as Delivered?')">
                                            <i class="fas fa-check me-2"></i> Mark Delivered
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-table>

        @if($deliveryNotes->hasPages())
            <div class="card-footer bg-transparent border-top p-4">
                {{ $deliveryNotes->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </x-card>
@endsection


