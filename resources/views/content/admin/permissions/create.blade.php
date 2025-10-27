@extends('layouts/contentNavbarLayout')

@section('title', 'Create Permission - Admin')

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
                        <li class="breadcrumb-item active">Create Permission</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="ti ti-key-plus me-2"></i>Create New Permission
                        </h5>
                        <small class="text-muted">Create a new system permission for access control</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.permissions.store') }}" method="POST">
                            @csrf

                            <!-- Permission Basic Information -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Permission Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name') }}" placeholder="e.g., admin.users.create"
                                        required>
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
                                    <label for="group" class="form-label">Permission Group</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('group') is-invalid @enderror"
                                            id="group" name="group" value="{{ old('group') }}" list="existing-groups"
                                            placeholder="Select or create group">
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
                                        placeholder="Describe what this permission allows users to do...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Permission Examples -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="fw-medium mb-3">
                                        <i class="ti ti-info-circle me-2"></i>Permission Examples
                                    </h6>

                                    <div class="row">
                                        @php
                                            $examples = [
                                                'Admin Management' => [
                                                    'admin.users.view' => 'View users list',
                                                    'admin.roles.create' => 'Create new roles',
                                                    'admin.settings.edit' => 'Modify system settings'
                                                ],
                                                'Vendor Management' => [
                                                    'vendor.rentals.create' => 'Create rental listings',
                                                    'vendor.bookings.manage' => 'Manage rental bookings',
                                                    'vendor.statistics.view' => 'View performance statistics'
                                                ],
                                                'Content Management' => [
                                                    'content.create' => 'Create new content',
                                                    'content.edit.all' => 'Edit all content',
                                                    'content.publish' => 'Publish content'
                                                ]
                                            ];
                                        @endphp

                                        @foreach($examples as $groupName => $groupExamples)
                                            <div class="col-md-4 mb-3">
                                                <div class="card border">
                                                    <div class="card-header py-2">
                                                        <h6 class="mb-0 small">{{ $groupName }}</h6>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        @foreach($groupExamples as $exampleName => $exampleDesc)
                                                            <div class="mb-2">
                                                                <button type="button" class="btn btn-sm btn-outline-primary me-2"
                                                                    onclick="fillExample('{{ $exampleName }}', '{{ $exampleDesc }}', '{{ $groupName }}')">
                                                                    <i class="ti ti-copy"></i>
                                                                </button>
                                                                <small class="fw-medium">{{ $exampleName }}</small><br>
                                                                <small class="text-muted ms-4">{{ $exampleDesc }}</small>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-1"></i>Create Permission
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fillExample(name, description, group) {
            document.getElementById('name').value = name;
            document.getElementById('description').value = description;
            document.getElementById('group').value = group;
        }

        // Auto-suggest group based on permission name
        document.getElementById('name').addEventListener('input', function () {
            const name = this.value.toLowerCase();
            const groupField = document.getElementById('group');

            if (name.startsWith('admin.')) {
                groupField.value = 'Admin Management';
            } else if (name.startsWith('vendor.')) {
                groupField.value = 'Vendor Management';
            } else if (name.startsWith('content.')) {
                groupField.value = 'Content Management';
            } else if (name.startsWith('system.')) {
                groupField.value = 'System Operations';
            }
        });
    </script>
@endsection