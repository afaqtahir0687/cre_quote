@extends('admin.layouts.app')

@section('title', 'Platform Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Platform Users</h2>
        <p class="text-muted small mb-0">Manage global super admins, branch owners, and shop employees across the platform.</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <form action="{{ route('super_admin.users.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group" style="min-width: 220px;">
                <input type="text" name="search" class="form-control" placeholder="Search name, username, email..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
            </div>
            <select name="shop_id" class="form-select" style="min-width: 130px;" onchange="this.form.submit()">
                <option value="">All Shops</option>
                @foreach($shops as $s)
                    <option value="{{ $s->id }}" {{ request('shop_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
            <select name="role" class="form-select" style="min-width: 120px;" onchange="this.form.submit()">
                <option value="">All Roles</option>
                <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Shop Admin</option>
                <option value="employee" {{ request('role') === 'employee' ? 'selected' : '' }}>Employee</option>
            </select>
            @if(request()->hasAny(['search', 'shop_id', 'role']))
                <a href="{{ route('super_admin.users.index') }}" class="btn btn-outline-danger" title="Clear Filters"><i class="fas fa-times"></i></a>
            @endif
        </form>

        <a href="{{ route('super_admin.users.create') }}" class="btn btn-primary-modern text-nowrap d-flex align-items-center">
            <i class="fas fa-user-plus me-2"></i> Add Users
        </a>
    </div>
</div>

<x-card :padding="false">
    <x-table>
        <x-slot name="thead">
            <th>User</th>
            <th>Username</th>
            <th>Role</th>
            <th>Assigned Shop</th>
            <th>Created At</th>
            <th class="text-end pe-4">Action</th>
        </x-slot>

        @forelse($users as $user)
        <tr class="align-middle">
            <td>
                <div>
                    <div class="fw-bold text-dark">{{ $user->name }}</div>
                    <div class="text-muted small"><a href="mailto:{{ $user->email }}" class="text-decoration-none text-muted">{{ $user->email }}</a></div>
                </div>
            </td>
            <td>
                <code class="text-dark bg-light px-2 py-1 rounded">{{ $user->username }}</code>
            </td>
            <td>
                @if($user->isSuperAdmin())
                    <span class="badge-soft badge-soft-warning"><i class="fas fa-crown me-1"></i> Super Admin</span>
                @elseif($user->isAdmin())
                    <span class="badge-soft badge-soft-primary"><i class="fas fa-user-shield me-1"></i> Shop Admin</span>
                @else
                    <span class="badge-soft badge-soft-secondary"><i class="fas fa-user me-1"></i> Employee</span>
                @endif
            </td>
            <td>
                @if($user->shop)
                    <a href="{{ route('super_admin.shops.edit', $user->shop->id) }}" class="text-decoration-none fw-semibold">
                        <i class="fas fa-store me-1 text-primary"></i>{{ $user->shop->name }}
                    </a>
                @else
                    <span class="text-muted small">Global (Platform Wide)</span>
                @endif
            </td>
            <td class="small text-muted">
                {{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}
            </td>
            <td class="text-end pe-4">
                <div class="dropdown">
                    <button class="btn btn-light btn-sm rounded-circle shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('super_admin.users.edit', $user->id) }}">
                                <i class="fas fa-edit me-2 text-info"></i> Edit User
                            </a>
                        </li>
                        @if(auth()->id() !== $user->id)
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('super_admin.users.destroy', $user->id) }}" method="POST" class="d-inline delete-user-form" data-user-name="{{ $user->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="fas fa-trash-alt me-2"></i> Delete User
                                    </button>
                                </form>
                            </li>
                        @endif
                    </ul>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center py-5 text-muted">
                No users found.
            </td>
        </tr>
        @endforelse
    </x-table>

    @if($users->hasPages())
        <div class="card-footer bg-transparent border-top p-4">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    @endif
</x-card>
@endsection

@section('scripts')
<script>
    // SweetAlert2 for Delete User Form
    document.querySelectorAll('.delete-user-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const userName = this.dataset.userName || 'this user';
            Swal.fire({
                title: 'Delete User?',
                text: `Are you sure you want to delete "${userName}"? This cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
