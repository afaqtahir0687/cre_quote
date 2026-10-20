@extends('admin.layouts.app')

@section('title', 'Manage Customers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">Customers</h2>
        <div class="d-flex align-items-center gap-3">
        <form action="{{ route('customers.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group" style="min-width: 290px;">
                <input type="text" name="search" class="form-control" placeholder="Search name, phone, company..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            </div>
            @if(request('search'))
                <a href="{{ route('customers.index') }}" class="btn btn-outline-danger"><i class="fas fa-times"></i></a>
            @endif
        </form>

        <a href="{{ route('customers.create') }}" class="btn btn-primary-modern d-flex align-items-center">
        <i class="fas fa-plus me-2"></i> Add New Customer
    </a>
    </div>
</div>

<x-card :padding="false">
    <x-table>
        <x-slot name="thead">
            <th>Name</th>
            <th>Company</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Status</th>
            <th class="text-end pe-4">Action</th>
        </x-slot>

        @foreach($customers as $customer)
        <tr class="align-middle">
            <td>
                <div class="d-flex align-items-center">
                    <div>
                        <div class="fw-bold text-dark">{{ $customer->name }}</div>
                        <div class="text-muted small">ID: #{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
            </td>
            <td class="text-muted">
                @if(!empty($customer->company_name))
                    <i class="fas fa-building me-1 text-muted"></i>{{ $customer->company_name }}
                @else
                    ---
                @endif
            </td>
            <td>
                @if(!empty($customer->email))
                    <a href="mailto:{{ $customer->email }}" class="text-decoration-none"><i class="fas fa-envelope me-1 text-muted"></i>{{ $customer->email }}</a>
                @else
                    <span class="text-muted">---</span>
                @endif
            </td>
            <td>
                @if(!empty($customer->phone))
                    <a href="tel:{{ $customer->phone }}" class="text-decoration-none text-dark fw-medium">
                        <i class="fas fa-phone-alt me-1 text-primary"></i>{{ $customer->formatted_phone ?: $customer->phone }}
                    </a>
                @else
                    <span class="text-muted">---</span>
                @endif
            </td>
            <td>
                <span class="badge-soft {{ $customer->is_active ? 'badge-soft-success' : 'badge-soft-danger' }}">
                    {{ $customer->is_active ? 'Active' : 'Inactive' }}
                </span>
            </td>
            <td class="text-end pe-4">
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item py-2" href="{{ route('customers.statement', $customer) }}"><i class="fas fa-file-invoice-dollar me-2 text-success"></i> View Statement</a></li>
                        <li><a class="dropdown-item py-2" href="{{ route('customers.edit', $customer) }}"><i class="fas fa-edit me-2 text-info"></i> Edit</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="dropdown-item py-2 text-danger">
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
    
    @if($customers->hasPages())
        <div class="card-footer bg-transparent border-top p-4">
            {{ $customers->links('pagination::bootstrap-5') }}
        </div>
    @endif
</x-card>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection

