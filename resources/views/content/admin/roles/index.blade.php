@extends('layouts/contentNavbarLayout')

@section('title', 'Role Management - Admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="ti ti-shield-check me-2"></i>Role Management
                        </h5>
                        <small class="text-muted">Manage user roles and permissions</small>
                    </div>
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>Create Role
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="ti ti-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="ti ti-alert-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="ti ti-filter me-2"></i>Filters
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.roles.index') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" name="search" 
                                       value="{{ request('search') }}" placeholder="Role name or description...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Protection Status</label>
                                <select class="form-select" name="protection_filter">
                                    <option value="">All Roles</option>
                                    <option value="protected" {{ request('protection_filter') === 'protected' ? 'selected' : '' }}>
                                        Protected Roles
                                    </option>
                                    <option value="custom" {{ request('protection_filter') === 'custom' ? 'selected' : '' }}>
                                        Custom Roles
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Guard</label>
                                <select class="form-select" name="guard_filter">
                                    <option value="">All Guards</option>
                                    @foreach($guards as $guard)
                                        <option value="{{ $guard }}" {{ request('guard_filter') === $guard ? 'selected' : '' }}>
                                            {{ ucfirst($guard) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ti ti-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="ti ti-list me-2"></i>Roles ({{ $roles->total() }})
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($roles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Role</th>
                                        <th>Users</th>
                                        <th>Permissions</th>
                                        <th>Guard</th>
                                        <th>Status</th>
                                        <th style="width: 120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                        @php
                                            $isProtected = in_array($role->name, ['admin', 'vendor', 'user', 'guest']);
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-initial rounded" 
                                                              style="background-color: {{ $role->color ?? '#007bff' }};">
                                                            @if($isProtected)
                                                                <i class="ti ti-shield-check"></i>
                                                            @else
                                                                <i class="ti ti-user-check"></i>
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $role->name }}</div>
                                                        @if($role->description)
                                                            <small class="text-muted">{{ $role->description }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-info">{{ $role->users_count ?? 0 }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-success">{{ $role->permissions_count ?? 0 }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-primary">{{ $role->guard_name }}</span>
                                            </td>
                                            <td>
                                                @if($isProtected)
                                                    <span class="badge bg-label-warning">
                                                        <i class="ti ti-shield me-1"></i>Protected
                                                    </span>
                                                @else
                                                    <span class="badge bg-label-secondary">Custom</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.roles.show', $role) }}" 
                                                       class="btn btn-outline-info" title="View Details">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    @if(!$isProtected)
                                                        <a href="{{ route('admin.roles.edit', $role) }}" 
                                                           class="btn btn-outline-primary" title="Edit Role">
                                                            <i class="ti ti-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="deleteRole({{ $role->id }}, '{{ $role->name }}')"
                                                                title="Delete Role">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-outline-warning" 
                                                                onclick="duplicateRole({{ $role->id }})"
                                                                title="Duplicate Role">
                                                            <i class="ti ti-copy"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($roles->hasPages())
                            <div class="card-footer">
                                {{ $roles->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="ti ti-shield-off text-muted mb-3" style="font-size: 3rem;"></i>
                            <h6 class="text-muted mb-2">No roles found</h6>
                            <p class="text-muted mb-3">No roles match your current filter criteria.</p>
                            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i>Create First Role
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deleteRole(roleId, roleName) {
    if (confirm(`Are you sure you want to delete the role "${roleName}"?\n\nThis action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/roles/${roleId}`;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        
        form.appendChild(csrfInput);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function duplicateRole(roleId) {
    if (confirm('Create a copy of this role?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/roles/${roleId}/duplicate`;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
