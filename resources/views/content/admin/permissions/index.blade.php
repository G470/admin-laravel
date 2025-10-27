@extends('layouts/contentNavbarLayout')

@section('title', 'Permission Management - Admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="ti ti-key me-2"></i>Permission Management
                        </h5>
                        <small class="text-muted">Manage system permissions and access control</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.permissions.statistics') }}" class="btn btn-outline-info">
                            <i class="ti ti-chart-bar me-1"></i>Statistics
                        </a>
                        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Create Permission
                        </a>
                    </div>
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
                    <form method="GET" action="{{ route('admin.permissions.index') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" name="search" 
                                       value="{{ request('search') }}" placeholder="Permission name or description...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Permission Group</label>
                                <select class="form-select" name="group_filter">
                                    <option value="">All Groups</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group }}" {{ request('group_filter') === $group ? 'selected' : '' }}>
                                            {{ $group }}
                                        </option>
                                    @endforeach
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
                                <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="ti ti-list me-2"></i>Permissions ({{ is_object($permissions) ? $permissions->total() : 0 }})
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if(is_object($permissions) && $permissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Permission</th>
                                        <th>Group</th>
                                        <th>Guard</th>
                                        <th>Roles</th>
                                        <th style="width: 120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($permissions as $permission)
                                        <tr>
                                            <td>
                                                <div>
                                                    <div class="fw-medium">{{ $permission->name }}</div>
                                                    @if($permission->description)
                                                        <small class="text-muted">{{ Str::limit($permission->description, 60) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($permission->group)
                                                    @php
                                                        $groupColors = [
                                                            'Admin Management' => 'danger',
                                                            'Vendor Management' => 'success', 
                                                            'Content Management' => 'info',
                                                            'System Operations' => 'warning'
                                                        ];
                                                        $badgeColor = $groupColors[$permission->group] ?? 'primary';
                                                    @endphp
                                                    <span class="badge bg-{{ $badgeColor }}">{{ $permission->group }}</span>
                                                @else
                                                    <span class="badge bg-secondary">No Group</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-label-primary">{{ $permission->guard_name }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-info">{{ $permission->roles_count ?? 0 }} roles</span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.permissions.show', $permission) }}" 
                                                       class="btn btn-outline-info" title="View Details">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.permissions.edit', $permission) }}" 
                                                       class="btn btn-outline-primary" title="Edit Permission">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="deletePermission({{ $permission->id }}, '{{ $permission->name }}')"
                                                            title="Delete Permission">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(is_object($permissions) && $permissions->hasPages())
                            <div class="card-footer">
                                {{ $permissions->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="ti ti-key-off text-muted mb-3" style="font-size: 3rem;"></i>
                            <h6 class="text-muted mb-2">No permissions found</h6>
                            <p class="text-muted mb-3">No permissions match your current filter criteria.</p>
                            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i>Create First Permission
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function deletePermission(permissionId, permissionName) {
    if (confirm(`Are you sure you want to delete the permission "${permissionName}"?\n\nThis action cannot be undone and may affect role assignments.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/permissions/${permissionId}`;
        
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
</script>
@endsection