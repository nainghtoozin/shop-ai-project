@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Users</h1>
            <p class="text-muted mb-0">Manage users and role assignments</p>
        </div>
        <div>
            @can('user.create')
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-2"></i>Add User
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

        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="Name, email, phone">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">All Users ({{ $users->total() }})</h6>
            </div>
            <div class="card-body p-0">
                @if ($users->count() === 0)
                    <div class="text-center py-5">
                        <i class="bi bi-people fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No users found</h5>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th>Created</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td class="fw-semibold">{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if ($user->roles->count() > 0)
                                                @foreach ($user->roles as $role)
                                                    <span class="badge bg-info text-white me-1">{{ $role->name }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-muted">{{ $user->created_at?->format('M d, Y') }}</td>
                                        <td class="text-end">
                                            @can('user.edit')
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-secondary btn-sm">
                                                    <i class="bi bi-pencil me-1"></i>Edit
                                                </a>
                                            @endcan
                                            @can('user.delete')
                                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="d-inline"
                                                    onsubmit="return confirm('Delete this user?')">
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

        @if ($users->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
