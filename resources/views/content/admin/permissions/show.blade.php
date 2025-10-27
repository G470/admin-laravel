@extends('layouts/contentNavbarLayout')

@section('title', 'View Permission - Admin')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.permissions.index') }}">Permission Management</a>
                        </li>
                        <li class="breadcrumb-item active">View Permission</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="ti ti-key me-2"></i>Permission Details
                            </h5>
                            <small class="text-muted">View permission information and assigned roles</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-primary me-2">
                                <i class="ti ti-edit me-1"></i>Edit Permission
                            </a>
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-arrow-left me-1"></i>Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Permission Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Permission Name</label>
                                    <div>
                                        <code class="fs-6 bg-light p-2 rounded">{{ $permission->name }}</code>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Guard</label>
                                    <div>
                                        <span class="badge bg-info">{{ ucfirst($permission->guard_name) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Group</label>
                                    <div>
                                        @if($permission->group)
                                            <span class="badge bg-secondary">{{ $permission->group }}</span>
                                        @else
                                            <span class="text-muted">No group assigned</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($permission->description)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-medium">Description</label>
                                    <div class="p-3 bg-light rounded">
                                        {{ $permission->description }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Permission Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0">{{ $permission->roles->count() }}</h4>
                                                <small>Assigned Roles</small>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="ti ti-shield-check fs-1"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0">
                                                    {{ $rolesByGroup->get('System Roles', collect())->count() }}</h4>
                                                <small>System Roles</small>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="ti ti-shield-lock fs-1"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4 class="mb-0">
                                                    {{ $rolesByGroup->get('Custom Roles', collect())->count() }}</h4>
                                                <small>Custom Roles</small>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="ti ti-shield fs-1"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned Roles by Group -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-medium mb-3">
                                    <i class="ti ti-shield-check me-2"></i>Assigned Roles
                                </h6>

                                @if($permission->roles->count() > 0)
                                    @foreach($rolesByGroup as $groupName => $roles)
                                        <div class="card mb-3">
                                            <div class="card-header">
                                                <h6 class="mb-0">
                                                    <i class="ti ti-category me-2"></i>{{ $groupName }}
                                                    <span class="badge bg-primary ms-2">{{ $roles->count() }}</span>
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach($roles as $role)
                                                        <div class="col-md-6 col-lg-4 mb-2">
                                                            <div class="d-flex align-items-center p-2 bg-light rounded">
                                                                @if($role->color)
                                                                    <div class="badge me-2"
                                                                        style="background-color: {{ $role->color }}; color: white;">
                                                                        {{ $role->name }}
                                                                    </div>
                                                                @else
                                                                    <span class="badge bg-secondary me-2">{{ $role->name }}</span>
                                                                @endif
                                                                <small class="text-muted">{{ $role->guard_name }}</small>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle me-2"></i>
                                        This permission is not assigned to any roles.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Permission Usage Examples -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="fw-medium mb-3">
                                    <i class="ti ti-code me-2"></i>Usage Examples
                                </h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-primary">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0">
                                                    <i class="ti ti-code me-2"></i>Blade Templates
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <pre class="mb-0"><code>@can('{{ $permission->name }}')
                                                    &lt;button&gt;Action&lt;/button&gt;
                                                @endcan</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-success">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0">
                                                    <i class="ti ti-code me-2"></i>Controllers
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <pre class="mb-0"><code>if (auth()->user()->can('{{ $permission->name }}')) {
        // Perform action
    }</code></pre>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Permission Metadata -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="fw-medium mb-3">
                                            <i class="ti ti-info-circle me-2"></i>Permission Information
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small class="text-muted">Created:</small>
                                                <div>{{ $permission->created_at->format('F d, Y \a\t g:i A') }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">Last Updated:</small>
                                                <div>{{ $permission->updated_at->format('F d, Y \a\t g:i A') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection