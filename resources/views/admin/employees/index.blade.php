@extends('admin.layouts.app')

@section('title', 'Manage Employees')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-dark mb-0">Employees</h2>
        <div class="d-flex align-items-center gap-3">
        <form action="{{ route('employees.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search employee..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            </div>
            @if(request('search'))
                <a href="{{ route('employees.index') }}" class="btn btn-outline-danger"><i class="fas fa-times"></i></a>
            @endif
        </form>

        <a href="{{ route('employees.create') }}" class="btn btn-primary-modern d-flex align-items-center">
        <i class="fas fa-plus me-2"></i> Add New Employee
    </a>
    </div>
</div>

<x-card :padding="false">
    <x-table>
        <x-slot name="thead">
            <th>Name</th>
            <th>Username</th>
            <th>Designation</th>
            <th class="text-end pe-4">Action</th>
        </x-slot>

        @foreach($employees as $employee)
        <tr class="align-middle">
            <td>
                <div class="d-flex align-items-center">
                    <div>
                        <div class="fw-bold text-dark">{{ $employee->name }}</div>
                        <div class="text-muted small">ID: #{{ str_pad($employee->id, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
            </td>
            <td class="text-muted">{{ $employee->user->username ?? 'N/A' }}</td>
            <td class="text-muted">{{ $employee->designation }}</td>
            <td class="text-end pe-4">
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item py-2" href="{{ route('employees.edit', $employee) }}"><i class="fas fa-edit me-2 text-info"></i> Edit</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline delete-form">
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
    @if($employees->hasPages())
        <div class="card-footer bg-transparent border-top p-4">
            {{ $employees->links('pagination::bootstrap-5') }}
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

