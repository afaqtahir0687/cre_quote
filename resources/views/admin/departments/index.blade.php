@extends('admin.layouts.app')

@section('title', 'Manage Departments & Machines')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Departments & Machine Units</h2>
        <p class="text-muted small mb-0">Manage work departments (Workshop, Banner Machine, Flex, Sticker Print, Sign Board, etc.)</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <form action="{{ route('departments.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search department..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            </div>
            @if(request('search'))
                <a href="{{ route('departments.index') }}" class="btn btn-outline-danger"><i class="fas fa-times"></i></a>
            @endif
        </form>

        <button class="btn btn-primary-modern d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
            <i class="fas fa-plus me-2"></i> Add New Department
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<x-card :padding="false">
    <x-table>
        <x-slot name="thead">
            <th>Department / Unit Name</th>
            <th>Description</th>
            <th>Active Assignments</th>
            <th>Status</th>
            <th class="text-end pe-4">Action</th>
        </x-slot>

        @foreach($departments as $dept)
        <tr class="align-middle">
            <td>
                <div class="d-flex align-items-center">
                    
                    <div>
                        <div class="fw-bold text-dark">{{ $dept->name }}</div>
                        <div class="text-muted small">ID: #{{ str_pad($dept->id, 3, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
            </td>
            <td class="text-muted">{{ $dept->description ?? 'No description provided' }}</td>
            <td>
                <span class="badge bg-light text-primary border px-3 py-2 fw-semibold">
                    <i class="fas fa-tasks me-1"></i> {{ $dept->assignments_count }} Tasks
                </span>
            </td>
            <td>
                @if($dept->is_active)
                    <span class="badge-soft badge-soft-success">Active</span>
                @else
                    <span class="badge-soft badge-soft-secondary">Inactive</span>
                @endif
            </td>
            <td class="text-end pe-4">
                <button class="btn btn-sm btn-light rounded-circle me-1" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editDepartmentModal{{ $dept->id }}" 
                        title="Edit Department">
                    <i class="fas fa-edit text-info"></i>
                </button>
                <form action="{{ route('departments.destroy', $dept) }}" method="POST" class="d-inline delete-form">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-light rounded-circle" title="Delete Department">
                        <i class="fas fa-trash-alt text-danger"></i>
                    </button>
                </form>
            </td>
        </tr>

        <!-- Edit Department Modal -->
        <div class="modal fade" id="editDepartmentModal{{ $dept->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('departments.update', $dept) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Edit Department</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Department / Unit Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ $dept->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ $dept->description }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Status</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ $dept->is_active ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$dept->is_active ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Department</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </x-table>

    @if($departments->hasPages())
    <div class="card-footer bg-transparent border-top p-4">
        {{ $departments->links('pagination::bootstrap-5') }}
    </div>
    @endif
</x-card>

<!-- Create Department Modal -->
<div class="modal fade" id="createDepartmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Department / Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Department / Unit Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Workshop, Banner Machine, Flex, Sticker Print, Sign Board" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief description of work done by this unit..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Department</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting this department cannot be undone!",
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

