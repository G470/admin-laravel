<div class="dynamic-rental-form">
    @if($hasFields)
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="ti ti-template me-2"></i>Kategorie Informationen 
                </h5>
                <div class="d-flex gap-2">
                    @if($fieldCount > 0)
                        <small class="badge bg-label-info">{{ $fieldCount }} Felder</small>
                    @endif
                    @if(!$isEditing)
                        <button type="button" wire:click="togglePreview" class="btn btn-sm btn-outline-secondary">
                            <i class="ti ti-eye{{ $showPreview ? '-off' : '' }} me-1"></i>
                            {{ $showPreview ? 'Bearbeiten' : 'Vorschau' }}
                        </button>
                    @endif
                </div>
            </div>

            <div class="card-body">
                @if($showPreview)
                    <!-- Preview Mode -->
                    <div class="preview-mode">
                        <h6 class="text-muted mb-3">Vorschau der ausgefüllten Felder:</h6>
                        @foreach($templateGroups as $templateName => $templateFields)
                            <div class="template-group mb-4">
                                <h6 class="fw-semibold mb-3 text-primary">
                                    <i class="ti ti-folder me-2"></i>{{ $templateName }}
                                </h6>
                                <div class="row">
                                    @foreach($templateFields as $field)
                                        @if($this->shouldShowField($field) && !empty($this->getFieldValue($field['field_name'])))
                                            <div class="col-md-6 mb-3">
                                                <div class="border rounded p-3 bg-light">
                                                    <strong class="d-block mb-1">{{ $field['field_label'] }}</strong>
                                                    <span class="text-muted">
                                                        @if($field['field_type'] === 'checkbox' && is_array($this->getFieldValue($field['field_name'])))
                                                            {{ implode(', ', $this->getFieldValue($field['field_name'])) }}
                                                        @elseif($field['field_type'] === 'select' && !empty($field['options']))
                                                            {{ $field['options'][$this->getFieldValue($field['field_name'])] ?? $this->getFieldValue($field['field_name']) }}
                                                        @else
                                                            {{ $this->getFieldValue($field['field_name']) }}
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Edit Mode -->
                    <form wire:submit.prevent="saveValues">
                        @foreach($templateGroups as $templateName => $templateFields)
                            <div class="template-group mb-5">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="ti ti-folder"></i>
                                        </span>
                                    </div>
                                    <h6 class="mb-0">{{ $templateName }}</h6>
                                    <small class="text-muted ms-2">({{ count($templateFields) }} Felder)</small>
                                </div>

                                <div class="row">
                                    @foreach($templateFields as $field)
                                        @if($this->shouldShowField($field))
                                            <div class="col-md-6 mb-3" wire:key="field-{{ $field['id'] }}">

                                                @include('components.dynamic-fields.field-wrapper', [
                                                    'field' => $field,
                                                    'rentalId' => $rentalId,
                                                    'vendorId' => $vendorId,
                                                    'fieldValues' => $fieldValues,
                                                    'categoryId' => $categoryId,
                                                    'wireModel' => 'fieldValues.' . $field['field_name'],
                                                    'value' => $this->getFieldValue($field['field_name']),
                                                    'error' => $errors->first('fieldValues.' . $field['field_name'])
                                                ])
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div class="d-flex gap-2">
                                @if($isEditing)
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-1"></i>Felder speichern
                                    </button>
                                @endif
                                <button type="button" wire:click="clearFieldValues" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh me-1"></i>Zurücksetzen
                                </button>
                            </div>

                            @if(!empty($validationErrors))
                                <div class="text-danger">
                                    <i class="ti ti-alert-circle me-1"></i>
                                    Bitte korrigieren Sie die Fehler
                                </div>
                            @endif
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- Validation Summary -->
        @if(!empty($validationErrors))
            <div class="alert alert-danger mt-3" role="alert">
                <h6 class="alert-heading">
                    <i class="ti ti-exclamation-triangle me-2"></i>Validierungsfehler
                </h6>
                <ul class="mb-0">
                    @foreach($validationErrors as $field => $errors)
                        @foreach($errors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        @endif
    @else
        <!-- No Fields Available -->
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="avatar avatar-lg mb-3">
                    <span class="avatar-initial rounded bg-label-secondary">
                        <i class="ti ti-template-off"></i>
                    </span>
                </div>
                <h6 class="mb-2">Keine zusätzlichen Felder verfügbar</h6>
                <p class="text-muted mb-3">
                    Für diese Kategorie wurden noch keine dynamischen Felder definiert.
                </p>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.rental-field-templates.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="ti ti-plus me-1"></i>Templates verwalten
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Listen for field value changes
        Livewire.on('fieldValuesSaved', (event) => {
            // Show success notification
            if (typeof showNotification === 'function') {
                showNotification('success', event.message || 'Felder erfolgreich gespeichert!');
            }
        });

        Livewire.on('fieldValuesStored', (event) => {
            // Show info notification for temporary storage
            if (typeof showNotification === 'function') {
                showNotification('info', event.message || 'Felddaten zwischengespeichert');
            }
        });

        Livewire.on('fieldValuesCleared', () => {
            // Show info notification
            if (typeof showNotification === 'function') {
                showNotification('info', 'Alle Felder wurden zurückgesetzt');
            }
        });

        Livewire.on('validationFailed', (event) => {
            // Scroll to first error
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        });

        Livewire.on('fieldsLoaded', (event) => {
            console.log(`${event.fieldCount} dynamic fields loaded`);
        });
    });

    // Helper function for notifications (can be customized)
    function showNotification(type, message) {
        // Check if Toastr is available
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        }
        // Check if Bootstrap toasts are available  
        else if (typeof bootstrap !== 'undefined') {
            // Create and show bootstrap toast
            const toastContainer = document.getElementById('toast-container') || document.body;
            const toastId = 'toast-' + Date.now();
            const toastHTML = `
                <div class="toast align-items-center text-bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0" 
                     id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                                data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            toastContainer.insertAdjacentHTML('beforeend', toastHTML);
            const toast = new bootstrap.Toast(document.getElementById(toastId));
            toast.show();
        }
        // Fallback to simple alert
        else {
            alert(message);
        }
    }
</script>
@endpush
