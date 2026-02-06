@extends('layouts.admin')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0 fw-bold text-gray-800">Create Role</h1>
            <p class="text-muted mb-0">Create a new role and assign permissions</p>
        </div>
        <div>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid p-0">
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

        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf

            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card shadow">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Role Info</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-save me-2"></i>Create Role
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="card shadow">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Permissions</h6>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAllPerms(true)">Select all</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAllPerms(false)">Clear</button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if ($permissions->count() === 0)
                                <div class="text-muted">No permissions found. Run PermissionSeeder first.</div>
                            @else
                                @php($selected = old('permissions', []))
                                @php($grouped = $permissions->groupBy(fn ($p) => \Illuminate\Support\Str::before($p->name, '.')))

                                <div class="d-flex flex-column gap-3">
                                    @foreach ($grouped as $module => $modulePermissions)
                                        <div class="border rounded p-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="fw-bold text-uppercase">{{ $module }}</div>
                                                <div class="form-check m-0">
                                                    <input class="form-check-input js-module-toggle" type="checkbox" id="mod_{{ $module }}"
                                                        data-module="{{ $module }}">
                                                    <label class="form-check-label" for="mod_{{ $module }}">Select All</label>
                                                </div>
                                            </div>

                                            <div class="row">
                                                @foreach ($modulePermissions as $permission)
                                                    @php($action = \Illuminate\Support\Str::after($permission->name, $module . '.'))
                                                    <div class="col-6 col-md-3 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input perm-checkbox js-perm" type="checkbox" name="permissions[]"
                                                                value="{{ $permission->id }}" id="perm_{{ $permission->id }}"
                                                                data-module="{{ $module }}"
                                                                {{ in_array($permission->id, $selected) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                                {{ \Illuminate\Support\Str::headline(str_replace('.', ' ', $action)) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleAllPerms(checked) {
            document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = !!checked);
        }

        function syncModuleToggle(module) {
            const perms = document.querySelectorAll('.js-perm[data-module="' + module + '"]');
            const toggle = document.querySelector('.js-module-toggle[data-module="' + module + '"]');
            if (!toggle || perms.length === 0) return;

            const allChecked = Array.from(perms).every(p => p.checked);
            const anyChecked = Array.from(perms).some(p => p.checked);

            toggle.checked = allChecked;
            toggle.indeterminate = !allChecked && anyChecked;
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.js-module-toggle').forEach((toggle) => {
                const module = toggle.getAttribute('data-module');
                syncModuleToggle(module);

                toggle.addEventListener('change', () => {
                    document.querySelectorAll('.js-perm[data-module="' + module + '"]').forEach((p) => {
                        p.checked = toggle.checked;
                    });
                    syncModuleToggle(module);
                });
            });

            document.querySelectorAll('.js-perm').forEach((perm) => {
                perm.addEventListener('change', () => {
                    const module = perm.getAttribute('data-module');
                    syncModuleToggle(module);
                });
            });
        });
    </script>
@endpush
