@extends('admin.layouts.app')

@section('title', 'Add Platform User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Add Platform User</h2>
        <p class="text-muted small mb-0">Create a Super Admin, Shop Owner/Admin, or Staff member and assign them to a branch.</p>
    </div>
    <a href="{{ route('super_admin.users.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Users
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <x-card title="User Account Details">
            <form action="{{ route('super_admin.users.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Full Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Ahmad Khan" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Username *</label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="e.g. ahmad.khan" value="{{ old('username') }}" required>
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Email Address *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="ahmad@example.com" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Password *</label>
                        <div class="input-group">
                            <input type="password" name="password" id="user_password" class="form-control @error('password') is-invalid @enderror" placeholder="Min. 6 characters" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility()">
                                <i class="fas fa-eye" id="pwdToggleIcon"></i>
                            </button>
                        </div>
                        @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark">Role *</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required id="roleSelector">
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Shop Admin (Branch Owner / Manager)</option>
                            <option value="employee" {{ old('role') === 'employee' ? 'selected' : '' }}>Employee (Shop Staff)</option>
                            <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin (Full Platform Control)</option>
                        </select>
                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6" id="shopSelectContainer">
                        <label class="form-label fw-bold text-dark">Assign to Shop <span class="text-danger" id="shopReqStar">*</span></label>
                        <select name="shop_id" class="form-select @error('shop_id') is-invalid @enderror" id="shopSelector">
                            <option value="">Select Shop...</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}" {{ old('shop_id', request('shop_id')) == $shop->id ? 'selected' : '' }}>
                                    {{ $shop->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('shop_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12" id="designationContainer">
                        <label class="form-label fw-bold text-dark">Staff Designation / Job Title</label>
                        <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" placeholder="e.g. Sales Manager, Graphic Designer" value="{{ old('designation') }}">
                        @error('designation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('super_admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary-modern">
                        <i class="fas fa-check-circle me-1"></i> Create User
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</div>

@section('scripts')
<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('user_password');
        const icon = document.getElementById('pwdToggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function updateShopRequirement() {
        const role = document.getElementById('roleSelector').value;
        const shopReqStar = document.getElementById('shopReqStar');
        if (role === 'super_admin') {
            shopReqStar.style.display = 'none';
        } else {
            shopReqStar.style.display = 'inline';
        }
    }
    document.getElementById('roleSelector')?.addEventListener('change', updateShopRequirement);
    updateShopRequirement();
</script>
@endsection
