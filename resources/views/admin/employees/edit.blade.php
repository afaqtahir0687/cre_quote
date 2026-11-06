@extends('admin.layouts.app')

@section('title', __('Edit Employee'))

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5>{{ __('Edit Employee') }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('employees.update', $employee) }}" method="POST" id="employee-form" novalidate>
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Name') }} *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', tdb($employee->name)) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Designation *') }}</label>
                    <input type="text" name="designation" class="form-control" value="{{ old('designation', tdb($employee->designation)) }}" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Username') }} *</label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $employee->user->username ?? '') }}" required>
                    <small class="text-muted">{{ __('This will be used for login') }}</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Password') }}</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="{{ __('Leave blank to keep current') }}">
                        <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    <small class="text-muted">{{ __('Leave blank to keep current password') }}</small>
                </div>
            </div>
            
            <script>
                function togglePassword() {
                    const passwordInput = document.getElementById('password');
                    const eyeIcon = document.getElementById('eyeIcon');
                    
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        eyeIcon.classList.remove('fa-eye');
                        eyeIcon.classList.add('fa-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        eyeIcon.classList.remove('fa-eye-slash');
                        eyeIcon.classList.add('fa-eye');
                    }
                }
            </script>
            
            <div class="text-end">
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Update Employee') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

