@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Edit User</h1>
            <p class="text-muted mb-0">{{ $user->email }}</p>
        </div>
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid p-0">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">Please fix the errors below.</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card shadow">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">User Info</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="leave blank">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="leave blank">
                                </div>
                            </div>
                            <div class="form-text mt-2">Password will only change if you fill both fields.</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card shadow">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Assign Roles</h6>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAllRoles(true)">Select all</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAllRoles(false)">Clear</button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($roles->count() === 0)
                                <div class="text-muted">No roles found. Create roles first.</div>
                            @else
                                @php($selected = old('roles', $userRoleIds ?? []))
                                <div class="row">
                                    @foreach ($roles as $role)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input role-checkbox" type="checkbox" name="roles[]" value="{{ $role->id }}"
                                                    id="role_{{ $role->id }}" {{ in_array($role->id, $selected) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary w-100 mt-3">
                                <i class="bi bi-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleAllRoles(checked) {
            document.querySelectorAll('.role-checkbox').forEach(cb => cb.checked = !!checked);
        }
    </script>
@endpush
