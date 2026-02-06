@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Roles</h1>
            <p class="text-muted mb-0">Manage roles and permissions</p>
        </div>
        <div>
            @can('role.create')
                <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-2"></i>Add Role
                </a>
            @endcan
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
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">All Roles ({{ $roles->total() }})</h6>
            </div>
            <div class="card-body p-0">
                @if ($roles->count() === 0)
                    <div class="text-center py-5">
                        <i class="bi bi-shield-lock fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No roles found</h5>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th class="text-center">Users</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td class="fw-semibold">{{ $role->name }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-info text-white">{{ $role->users_count }}</span>
                                        </td>
                                        <td class="text-end">
                                            @can('role.edit')
                                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-pencil me-1"></i>Edit
                                                </a>
                                            @endcan
                                            @can('role.delete')
                                                <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}" class="d-inline"
                                                    onsubmit="return confirm('Delete this role?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="bi bi-trash me-1"></i>Delete
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        @if ($roles->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
@endsection
