<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Template Felder verwalten</h5>
            <button wire:click="addField" class="btn btn-primary btn-sm">
                <i class="ti ti-plus me-1"></i>Feld hinzufügen
            </button>
        </div>
        <div class="card-body">
            @if(count($fields) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Feld</th>
                                <th>Typ</th>
                                <th>Erforderlich</th>
                                <th>Filterbar</th>
                                <th>Sortierung</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fields as $index => $field)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $field['label'] ?? 'Unbenannt' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $field['name'] ?? '' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-label-info">{{ $fieldTypes[$field['type'] ?? 'text'] ?? 'Text' }}</span>
                                    </td>
                                    <td>
                                        @if($field['is_required'] ?? false)
                                            <span class="badge bg-label-success">Ja</span>
                                        @else
                                            <span class="badge bg-label-secondary">Nein</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($field['is_filterable'] ?? false)
                                            <span class="badge bg-label-warning">Ja</span>
                                        @else
                                            <span class="badge bg-label-secondary">Nein</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button wire:click="moveFieldUp({{ $index }})"
                                                class="btn btn-sm btn-icon btn-outline-secondary" {{ $index === 0 ? 'disabled' : '' }}>
                                                <i class="ti ti-arrow-up"></i>
                                            </button>
                                            <button wire:click="moveFieldDown({{ $index }})"
                                                class="btn btn-sm btn-icon btn-outline-secondary" {{ $index === count($fields) - 1 ? 'disabled' : '' }}>
                                                <i class="ti ti-arrow-down"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button wire:click="editField({{ $index }})"
                                                class="btn btn-sm btn-icon btn-outline-primary">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            <button wire:click="deleteField({{ $index }})"
                                                class="btn btn-sm btn-icon btn-outline-danger"
                                                onclick="return confirm('Feld wirklich löschen?')">
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
                    <div class="avatar avatar-lg mb-3">
                        <span class="avatar-initial rounded bg-label-secondary">
                            <i class="ti ti-list"></i>
                        </span>
                    </div>
                    <h5>Keine Felder vorhanden</h5>
                    <p class="text-muted">Fügen Sie Felder zu diesem Template hinzu.</p>
                    <button wire:click="addField" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>Erstes Feld hinzufügen
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Field Modal -->
    @if($showFieldModal)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $editingField === null ? 'Neues Feld' : 'Feld bearbeiten' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showFieldModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Feld Name *</label>
                                    <input type="text" class="form-control" wire:model="editingField.name"
                                        placeholder="z.B. max_guests">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Feld Label *</label>
                                    <input type="text" class="form-control" wire:model="editingField.label"
                                        placeholder="z.B. Maximale Gäste">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Feld Typ *</label>
                                    <select class="form-select" wire:model="editingField.type">
                                        @foreach($fieldTypes as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Standardwert</label>
                                    <input type="text" class="form-control" wire:model="editingField.default_value"
                                        placeholder="Standardwert">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Placeholder</label>
                            <input type="text" class="form-control" wire:model="editingField.placeholder"
                                placeholder="Placeholder Text">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Hilfetext</label>
                            <textarea class="form-control" wire:model="editingField.help_text" rows="2"
                                placeholder="Hilfetext für Benutzer"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="editingField.is_required">
                                    <label class="form-check-label">
                                        Feld ist erforderlich
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="editingField.is_filterable">
                                    <label class="form-check-label">
                                        Als Filter verwenden
                                    </label>
                                </div>
                            </div>
                        </div>

                        @if(in_array($editingField['type'] ?? '', ['select', 'radio']))
                            <div class="mb-3">
                                <label class="form-label">Optionen (eine pro Zeile)</label>
                                <textarea class="form-control" wire:model="editingField.options" rows="4"
                                    placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                                <small class="text-muted">Geben Sie jede Option in eine neue Zeile ein</small>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showFieldModal', false)">
                            Abbrechen
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="saveField">
                            <i class="ti ti-device-floppy me-1"></i>Speichern
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>