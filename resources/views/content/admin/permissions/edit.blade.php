@extends('layouts/contentNavbarLayout')

@section('title', 'Edit Permission - Admin')

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
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.permissions.show', $permission) }}">{{ $permission->name }}</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Permission</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="ti ti-key-edit me-2"></i>Edit Permission
                        </h5>
                        <small class="text-muted">Modify permission details and settings</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Permission Basic Information -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Permission Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name', $permission->name) }}"
                                        placeholder="e.g., admin.users.create" required>
                                    <div class="form-text">Use dot notation: module.resource.action</div>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="guard_name" class="form-label">Guard <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('guard_name') is-invalid @enderror" id="guard_name"
                                        name="guard_name" required>
                                        @foreach($guards as $key => $label)
                                            <option value="{{ $key }}" {{ old('guard_name', $permission->guard_name) === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('guard_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="group" class="form-label">Permission Group</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('group') is-invalid @enderror"
                                            id="group" name="group" value="{{ old('group', $permission->group) }}"
                                            list="existing-groups" placeholder="Select or create group">
                                        <datalist id="existing-groups">
                                            @foreach($existingGroups as $group)
                                                <option value="{{ $group }}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                    @error('group')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                        id="description" name="description" rows="3"
                                        placeholder="Describe what this permission allows users to do...">{{ old('description', $permission->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Permission Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h6 class="alert-heading">
                                            <i class="ti ti-info-circle me-2"></i>Permission Information
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <small class="text-muted">Created:</small>
                                                <div>{{ $permission->created_at->format('M d, Y g:i A') }}</div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Last Updated:</small>
                                                <div>{{ $permission->updated_at->format('M d, Y g:i A') }}</div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Assigned Roles:</small>
                                                <div>{{ $permission->roles->count() }} roles</div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Current Group:</small>
                                                <div>{{ $permission->group ?: 'No group' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Permission Examples -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="fw-medium mb-3">
                                        <i class="ti ti-info-circle me-2"></i>Permission Examples
                                    </h6>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="card border-primary">
                                                <div class="card-body">
                                                    <h6 class="card-title text-primary">User Management</h6>
                                                    <ul class="list-unstyled mb-0">
                                                        <li><code>admin.users.view</code></li>
                                                        <li><code>admin.users.create</code></li>
                                                        <li><code>admin.users.edit</code></li>
                                                        <li><code>admin.users.delete</code></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-success">
                                                <div class="card-body">
                                                    <h6 class="card-title text-success">Content Management</h6>
                                                    <ul class="list-unstyled mb-0">
                                                        <li><code>content.posts.view</code></li>
                                                        <li><code>content.posts.create</code></li>
                                                        <li><code>content.posts.edit</code></li>
                                                        <li><code>content.posts.publish</code></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card border-warning">
                                                <div class="card-body">
                                                    <h6 class="card-title text-warning">System Settings</h6>
                                                    <ul class="list-unstyled mb-0">
                                                        <li><code>settings.view</code></li>
                                                        <li><code>settings.edit</code></li>
                                                        <li><code>system.logs.view</code></li>
                                                        <li><code>system.backup</code></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <a href="{{ route('admin.permissions.show', $permission) }}"
                                                class="btn btn-outline-secondary me-2">
                                                <i class="ti ti-eye me-1"></i>View Permission
                                            </a>
                                            <a href="{{ route('admin.permissions.index') }}"
                                                class="btn btn-outline-secondary">
                                                <i class="ti ti-arrow-left me-1"></i>Back to List
                                            </a>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ti ti-device-floppy me-1"></i>Update Permission
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection