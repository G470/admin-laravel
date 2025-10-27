@extends('layouts/contentNavbarLayout')

@section('title', 'View Role - Admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.roles.index') }}">Role Management</a>
                    </li>
                    <li class="breadcrumb-item active">View Role</li>
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
                            <i class="ti ti-shield me-2"></i>Role Details
                        </h5>
                        <small class="text-muted">View role information and permissions</small>
                    </div>
                    <div>
                        @if(!$isProtected)
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary me-2">
                                <i class="ti ti-edit me-1"></i>Edit Role
                            </a>
                        @endif
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i>Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Role Basic Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-medium">Role Name</label>
                                <div class="d-flex align-items-center">
                                    @if($role->color)
                                        <div class="badge me-2" style="background-color: {{ $role->color }}; color: white;">
                                            {{ $role->name }}
                                        </div>
                                    @else
                                        <span class="fw-medium">{{ $role->name }}</span>
                                    @endif
                                    @if($isProtected)
                                        <span class="badge bg-warning ms-2">
                                            <i class="ti ti-shield-lock me-1"></i>Protected
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-medium">Guard</label>
                                <div>
                                    <span class="badge bg-info">{{ ucfirst($role->guard_name) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($role->description)
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label fw-medium">Description</label>
                                <div class="p-3 bg-light rounded">
                                    {{ $role->description }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Role Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $role->permissions->count() }}</h4>
                                            <small>Permissions</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="ti ti-key fs-1"></i>
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
                                            <h4 class="mb-0">{{ $role->users->count() }}</h4>
                                            <small>Assigned Users</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="ti ti-users fs-1"></i>
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
                                            <h4 class="mb-0">{{ $permissionsByGroup->count() }}</h4>
                                            <small>Permission Groups</small>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="ti ti-category fs-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Permissions by Group -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-medium mb-3">
                                <i class="ti ti-key me-2"></i>Permissions by Group
                            </h6>
                            
                            @if($permissionsByGroup->count() > 0)
                                @foreach($permissionsByGroup as $group => $permissions)
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h6 class="mb-0">
                                                <i class="ti ti-category me-2"></i>{{ ucfirst($group) }}
                                                <span class="badge bg-primary ms-2">{{ $permissions->count() }}</span>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach($permissions as $permission)
                                                    <div class="col-md-6 col-lg-4 mb-2">
                                                        <div class="d-flex align-items-center p-2 bg-light rounded">
                                                            <i class="ti ti-check text-success me-2"></i>
                                                            <span class="small">{{ $permission->name }}</span>
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
                                    This role has no permissions assigned.
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Assigned Users -->
                    @if($role->users->count() > 0)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="fw-medium mb-3">
                                    <i class="ti ti-users me-2"></i>Assigned Users
                                    <span class="badge bg-success ms-2">{{ $role->users->count() }}</span>
                                </h6>
                                
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>User</th>
                                                        <th>Email</th>
                                                        <th>Status</th>
                                                        <th>Joined</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($role->users as $user)
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar avatar-sm me-3">
                                                                        @if($user->profile_photo_url)
                                                                            <img src="{{ $user->profile_photo_url }}" 
                                                                                 alt="{{ $user->name }}" 
                                                                                 class="rounded-circle">
                                                                        @else
                                                                            <div class="avatar-initial rounded-circle bg-label-primary">
                                                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                    <div>
                                                                        <h6 class="mb-0">{{ $user->name }}</h6>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>{{ $user->email }}</td>
                                                            <td>
                                                                @if($user->email_verified_at)
                                                                    <span class="badge bg-success">Verified</span>
                                                                @else
                                                                    <span class="badge bg-warning">Unverified</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Role Metadata -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="fw-medium mb-3">
                                        <i class="ti ti-info-circle me-2"></i>Role Information
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">Created:</small>
                                            <div>{{ $role->created_at->format('F d, Y \a\t g:i A') }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Last Updated:</small>
                                            <div>{{ $role->updated_at->format('F d, Y \a\t g:i A') }}</div>
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