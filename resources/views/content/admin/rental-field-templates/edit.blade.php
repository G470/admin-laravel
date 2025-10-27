@extends('layouts/contentNavbarLayout')

@section('title', 'Template bearbeiten')

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Dynamic Rental Fields /</span> {{ $rentalFieldTemplate->name }}
    </h4>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Template bearbeiten</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.rental-field-templates.update', $rentalFieldTemplate) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Template Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name', $rentalFieldTemplate->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sortierung</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror"
                                        id="sort_order" name="sort_order"
                                        value="{{ old('sort_order', $rentalFieldTemplate->sort_order) }}">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Beschreibung</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                                name="description"
                                rows="3">{{ old('description', $rentalFieldTemplate->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kategorien</label>
                            <div class="row">
                                @foreach($categories as $category)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="categories[]"
                                                value="{{ $category->id }}" id="category_{{ $category->id }}" {{ in_array($category->id, old('categories', $rentalFieldTemplate->categories->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="category_{{ $category->id }}">
                                                {{ $category->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $rentalFieldTemplate->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Template ist aktiv
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.rental-field-templates.index') }}" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i>Zurück
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>Änderungen speichern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Template Felder</h5>
                    <button class="btn btn-primary btn-sm" onclick="addNewField()">
                        <i class="ti ti-plus me-1"></i>Feld hinzufügen
                    </button>
                </div>
                <div class="card-body">
                    @if($rentalFieldTemplate->fields->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Feld</th>
                                        <th>Typ</th>
                                        <th>Erforderlich</th>
                                        <th>Filterbar</th>
                                        <th>Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rentalFieldTemplate->fields as $field)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $field->field_label }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $field->field_name }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-info">{{ $field->field_type }}</span>
                                            </td>
                                            <td>
                                                @if($field->is_required)
                                                    <span class="badge bg-label-success">Ja</span>
                                                @else
                                                    <span class="badge bg-label-secondary">Nein</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($field->is_filterable)
                                                    <span class="badge bg-label-warning">Ja</span>
                                                @else
                                                    <span class="badge bg-label-secondary">Nein</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-icon btn-outline-primary"
                                                        onclick="editField({{ $field->id }}, '{{ $field->field_name }}')">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-icon btn-outline-danger"
                                                        onclick="deleteField({{ $field->id }}, '{{ $field->field_label }}')">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="ti ti-list-details text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Noch keine Felder vorhanden</p>
                            <p class="text-muted">Klicken Sie auf "Feld hinzufügen" um das erste Feld zu erstellen.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Field Edit Modal --}}
    <div class="modal fade" id="editFieldModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Feld bearbeiten</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editFieldForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="editFieldId" name="field_id">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editFieldLabel" class="form-label">Field Label *</label>
                                    <input type="text" class="form-control" id="editFieldLabel" name="field_label" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editFieldName" class="form-label">Field Name *</label>
                                    <input type="text" class="form-control" id="editFieldName" name="field_name" required
                                        readonly>
                                    <small class="text-muted">Field Name kann nicht geändert werden</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="editFieldDescription" class="form-label">Beschreibung</label>
                            <textarea class="form-control" id="editFieldDescription" name="field_description"
                                rows="2"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="editIsRequired" name="is_required">
                                    <label class="form-check-label" for="editIsRequired">Pflichtfeld</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="editIsFilterable"
                                        name="is_filterable">
                                    <label class="form-check-label" for="editIsFilterable">Filterbar</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="editIsSearchable"
                                        name="is_searchable">
                                    <label class="form-check-label" for="editIsSearchable">Suchbar</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editSortOrder" class="form-label">Sortierung</label>
                                    <input type="number" class="form-control" id="editSortOrder" name="sort_order">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editFieldType" class="form-label">Field Type</label>
                                    <input type="text" class="form-control" id="editFieldType" name="field_type" readonly>
                                    <small class="text-muted">Field Type kann nicht geändert werden</small>
                                </div>
                            </div>
                        </div>

                        <div id="editOptionsSection" class="mb-3" style="display: none;">
                            <label class="form-label">Optionen (eine pro Zeile im Format "value|label")</label>
                            <textarea class="form-control" id="editOptions" name="options" rows="4"
                                placeholder="option1|Option 1&#10;option2|Option 2"></textarea>
                            <small class="text-muted">Format: wert|anzeigetext (z.B. "car|Auto")</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                    <button type="button" class="btn btn-primary" onclick="saveField()">Speichern</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Add New Field Modal --}}
    <div class="modal fade" id="addNewFieldModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Neues Feld hinzufügen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addNewFieldForm">
                        @csrf
                        <input type="hidden" id="newFieldTemplateId" name="rental_field_template_id"
                            value="{{ $rentalFieldTemplate->id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="newFieldLabel" class="form-label">Field Label *</label>
                                    <input type="text" class="form-control" id="newFieldLabel" name="field_label" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="newFieldName" class="form-label">Field Name *</label>
                                    <input type="text" class="form-control" id="newFieldName" name="field_name" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="newFieldDescription" class="form-label">Beschreibung</label>
                            <textarea class="form-control" id="newFieldDescription" name="field_description"
                                rows="2"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newIsRequired" name="is_required">
                                    <label class="form-check-label" for="newIsRequired">Pflichtfeld</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newIsFilterable"
                                        name="is_filterable">
                                    <label class="form-check-label" for="newIsFilterable">Filterbar</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newIsSearchable"
                                        name="is_searchable">
                                    <label class="form-check-label" for="newIsSearchable">Suchbar</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="newSortOrder" class="form-label">Sortierung</label>
                                    <input type="number" class="form-control" id="newSortOrder" name="sort_order">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="newFieldType" class="form-label">Field Type</label>
                                    <select class="form-control" id="newFieldType" name="field_type" required>
                                        <option value="">Bitte Feldtyp auswählen</option>
                                        <option value="text">Text</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="number">Zahl</option>
                                        <option value="select">Select</option>
                                        <option value="radio">Radio</option>
                                        <option value="checkbox">Checkbox</option>
                                        <option value="date">Datum</option>
                                        <option value="time">Uhrzeit</option>
                                        <option value="datetime">Datum & Uhrzeit</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="newOptionsSection" class="mb-3" style="display: none;">
                            <label class="form-label">Optionen (eine pro Zeile im Format "value|label")</label>
                            <textarea class="form-control" id="newOptions" name="options" rows="4"
                                placeholder="option1|Option 1&#10;option2|Option 2"></textarea>
                            <small class="text-muted">Format: wert|anzeigetext (z.B. "car|Auto")</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                    <button type="button" class="btn btn-primary" onclick="saveNewField()">Speichern</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editField(fieldId, fieldName) {
            // Load field data via AJAX
            fetch(`/admin/rental-field-templates/{{ $rentalFieldTemplate->id }}/fields/${fieldId}`)
                .then(response => response.json())
                .then(field => {
                    // Populate modal form
                    document.getElementById('editFieldId').value = field.id;
                    document.getElementById('editFieldLabel').value = field.field_label || '';
                    document.getElementById('editFieldName').value = field.field_name || '';
                    document.getElementById('editFieldDescription').value = field.field_description || '';
                    document.getElementById('editIsRequired').checked = field.is_required;
                    document.getElementById('editIsFilterable').checked = field.is_filterable;
                    document.getElementById('editIsSearchable').checked = field.is_searchable;
                    document.getElementById('editSortOrder').value = field.sort_order || '';
                    document.getElementById('editFieldType').value = field.field_type || '';

                    // Handle options for select/radio/checkbox fields
                    const optionsSection = document.getElementById('editOptionsSection');
                    const optionsTextarea = document.getElementById('editOptions');

                    if (['select', 'radio', 'checkbox'].includes(field.field_type)) {
                        optionsSection.style.display = 'block';
                        if (field.options) {
                            const optionsText = Object.entries(field.options)
                                .map(([key, value]) => `${key}|${value}`)
                                .join('\n');
                            optionsTextarea.value = optionsText;
                        }
                    } else {
                        optionsSection.style.display = 'none';
                    }

                    // Show modal
                    new bootstrap.Modal(document.getElementById('editFieldModal')).show();
                })
                .catch(error => {
                    console.error('Error loading field:', error);
                    alert('Fehler beim Laden der Field-Daten');
                });
        }

        function saveField() {
            const formData = new FormData(document.getElementById('editFieldForm'));
            const fieldId = document.getElementById('editFieldId').value;

            // Convert FormData to JSON
            const data = {};
            formData.forEach((value, key) => {
                if (key === 'options') {
                    // Parse options from textarea
                    const options = {};
                    if (value.trim()) {
                        value.split('\n').forEach(line => {
                            const [key_part, ...label_parts] = line.split('|');
                            if (key_part && label_parts.length > 0) {
                                options[key_part.trim()] = label_parts.join('|').trim();
                            }
                        });
                    }
                    data[key] = options;
                } else if (key === 'is_required' || key === 'is_filterable' || key === 'is_searchable') {
                    data[key] = formData.get(key) === 'on';
                } else {
                    data[key] = value;
                }
            });

            fetch(`/admin/rental-field-templates/{{ $rentalFieldTemplate->id }}/fields/${fieldId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('editFieldModal')).hide();
                        location.reload();
                    } else {
                        alert('Fehler beim Speichern: ' + (data.message || 'Unbekannter Fehler'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Fehler beim Speichern des Feldes');
                });
        }

        function deleteField(fieldId, fieldLabel) {
            if (confirm(`Möchten Sie das Feld "${fieldLabel}" wirklich löschen?`)) {
                // Send DELETE request to remove field
                fetch(`/admin/rental-field-templates/{{ $rentalFieldTemplate->id }}/fields/${fieldId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Fehler beim Löschen des Feldes');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Fehler beim Löschen des Feldes');
                    });
            }
        }

        function addNewField() {
            // Reset form
            document.getElementById('addNewFieldForm').reset();
            document.getElementById('newOptionsSection').style.display = 'none';
            
            // Add event listener for field type change
            document.getElementById('newFieldType').addEventListener('change', function() {
                const optionsSection = document.getElementById('newOptionsSection');
                if (['select', 'radio', 'checkbox'].includes(this.value)) {
                    optionsSection.style.display = 'block';
                } else {
                    optionsSection.style.display = 'none';
                }
            });
            
            new bootstrap.Modal(document.getElementById('addNewFieldModal')).show();
        }

        function saveNewField() {
            const formData = new FormData(document.getElementById('addNewFieldForm'));
            const templateId = document.getElementById('newFieldTemplateId').value;

            const data = {};
            formData.forEach((value, key) => {
                if (key === 'options') {
                    const options = {};
                    if (value.trim()) {
                        value.split('\n').forEach(line => {
                            const [key_part, ...label_parts] = line.split('|');
                            if (key_part && label_parts.length > 0) {
                                options[key_part.trim()] = label_parts.join('|').trim();
                            }
                        });
                    }
                    data[key] = options;
                } else if (key === 'is_required' || key === 'is_filterable' || key === 'is_searchable') {
                    data[key] = formData.get(key) === 'on';
                } else {
                    data[key] = value;
                }
            });

            fetch(`/admin/rental-field-templates/{{ $rentalFieldTemplate->id }}/fields`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('addNewFieldModal')).hide();
                        location.reload();
                    } else {
                        alert('Fehler beim Hinzufügen des Feldes: ' + (data.message || 'Unbekannter Fehler'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Fehler beim Hinzufügen des Feldes');
                });
        }
    </script>
@endsection