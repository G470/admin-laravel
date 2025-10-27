@php
    // import for Str
    use Illuminate\Support\Str;
@endphp
@extends('layouts/contentNavbarLayout')

@section('title', 'Create Role - Admin')

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
                    <li class="breadcrumb-item active">Create Role</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="ti ti-shield-plus me-2"></i>Create New Role
                    </h5>
                    <small class="text-muted">Create a custom role with specific permissions</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.roles.store') }}" method="POST">
                        @csrf
                        
                        <!-- Role Basic Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Role Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="guard_name" class="form-label">Guard <span class="text-danger">*</span></label>
                                <select class="form-select @error('guard_name') is-invalid @enderror" 
                                        id="guard_name" name="guard_name" required>
                                    @foreach($guards as $key => $label)
                                        <option value="{{ $key }}" {{ old('guard_name', 'web') === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('guard_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="color" class="form-label">Role Color</label>
                                <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                       id="color" name="color" value="{{ old('color', '#007bff') }}">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Describe the purpose and scope of this role...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Permission Matrix -->
                        <div class="row">
                            <div class="col-12">
                                <h6 class="fw-medium mb-3">
                                    <i class="ti ti-key me-2"></i>Permissions
                                    <small class="text-muted fw-normal">Select the permissions for this role</small>
                                </h6>
                                
                                @if(is_object($permissions) && method_exists($permissions, 'count') && $permissions->count() > 0)
                                    <!-- Select All Options -->
                                    <div class="mb-3">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" onclick="selectAllPermissions()">
                                                <i class="ti ti-check-all me-1"></i>Select All
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" onclick="deselectAllPermissions()">
                                                <i class="ti ti-square me-1"></i>Deselect All
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Permission Groups -->
                                    @if(is_iterable($permissions))
                                        @foreach($permissions as $group => $groupPermissions)
                                        <div class="card mb-3">
                                            <div class="card-header py-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0">
                                                        @php
                                                            $groupColors = [
                                                                'Admin Management' => 'danger',
                                                                'Vendor Management' => 'success', 
                                                                'Content Management' => 'info',
                                                                'System Operations' => 'warning'
                                                            ];
                                                            $badgeColor = $groupColors[$group] ?? 'primary';
                                                        @endphp
                                                        <span class="badge bg-{{ $badgeColor }} me-2">{{ $group }}</span>
                                                        <small class="text-muted">({{ $groupPermissions->count() }} permissions)</small>
                                                    </h6>
                                                    <div class="form-check">
                                                        <input class="form-check-input group-checkbox" type="checkbox" 
                                                               id="group_{{ $loop->index }}" 
                                                               onchange="toggleGroupPermissions('{{ $group }}', this.checked)">
                                                        <label class="form-check-label small" for="group_{{ $loop->index }}">
                                                            Select All
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body py-2">
                                                <div class="row">
                                                    @foreach($groupPermissions->chunk(3) as $chunk)
                                                        @foreach($chunk as $permission)
                                                            <div class="col-md-4 mb-2">
                                                                <div class="form-check">
                                                                    <input class="form-check-input permission-{{ Str::slug($group) }}" 
                                                                           type="checkbox" 
                                                                           name="permissions[]" 
                                                                           value="{{ $permission->id }}" 
                                                                           id="permission_{{ $permission->id }}"
                                                                           {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                                    <label class="form-check-label small" for="permission_{{ $permission->id }}">
                                                                        <span class="fw-medium">{{ $permission->name }}</span>
                                                                        @if($permission->description)
                                                                            <br><small class="text-muted">{{ $permission->description }}</small>
                                                                        @endif
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif
                                @else
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle me-2"></i>
                                        No permissions available. Please run the system permissions seeder first.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-arrow-left me-1"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-check me-1"></i>Create Role
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
    document.querySelectorAll('.group-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.querySelectorAll('.group-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function toggleGroupPermissions(group, checked) {
    const groupSlug = group.toLowerCase().replace(/\s+/g, '-');
    document.querySelectorAll(`.permission-${groupSlug}`).forEach(checkbox => {
        checkbox.checked = checked;
    });
}

// Update group checkboxes when individual permissions change
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateGroupCheckboxes();
        });
    });
});

function updateGroupCheckboxes() {
    @foreach($permissions as $group => $groupPermissions)
        const groupSlug = '{{ Str::slug($group) }}';
        const groupCheckbox = document.getElementById('group_{{ $loop->index }}');
        const groupPermissions = document.querySelectorAll('.permission-' + groupSlug);
        const checkedCount = document.querySelectorAll('.permission-' + groupSlug + ':checked').length;
        
        if (checkedCount === 0) {
            groupCheckbox.checked = false;
            groupCheckbox.indeterminate = false;
        } else if (checkedCount === groupPermissions.length) {
            groupCheckbox.checked = true;
            groupCheckbox.indeterminate = false;
        } else {
            groupCheckbox.checked = false;
            groupCheckbox.indeterminate = true;
        }
    @endforeach
}
</script>
@endsection
