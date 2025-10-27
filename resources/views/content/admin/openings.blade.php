@extends('layouts/contentNavbarLayoutBackend')

@section('title', 'Öffnungszeiten verwalten - Admin')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/select2/select2.css',
    'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css'
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/select2/select2.js',
    'resources/assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js'
])
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-clock me-2"></i>Öffnungszeiten verwalten
                </h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="ti ti-plus me-1"></i>Neue Öffnungszeit
                </button>
            </div>
            
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Template Selector -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Standard Template anwenden</h6>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="applyTemplate('business')">
                                        Geschäftszeiten (Mo-Fr 9-18)
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="applyTemplate('shop')">
                                        Ladenzeiten (Mo-Sa 10-20)
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="applyTemplate('24h')">
                                        24/7 Service
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Statistik</h6>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <strong>Aktive</strong><br>
                                        <span class="text-success">42</span>
                                    </div>
                                    <div class="col-4">
                                        <strong>Inaktive</strong><br>
                                        <span class="text-danger">3</span>
                                    </div>
                                    <div class="col-4">
                                        <strong>Gesamt</strong><br>
                                        <span class="text-primary">45</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Opening Hours Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name/Typ</th>
                                <th>Wochentage</th>
                                <th>Zeiten</th>
                                <th>Feiertage</th>
                                <th>Status</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Sample data -->
                            <tr>
                                <td class="text-muted small">1</td>
                                <td>
                                    <div>
                                        <strong>Standard Geschäftszeiten</strong><br>
                                        <small class="text-muted">Bürozeiten für Vermietungen</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-primary">Mo</span>
                                        <span class="badge bg-primary">Di</span>
                                        <span class="badge bg-primary">Mi</span>
                                        <span class="badge bg-primary">Do</span>
                                        <span class="badge bg-primary">Fr</span>
                                        <span class="badge bg-light text-muted">Sa</span>
                                        <span class="badge bg-light text-muted">So</span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>09:00 - 18:00</strong><br>
                                        <small class="text-muted">Mittagspause: 12:00-13:00</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-warning">Berücksichtigt</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">Aktiv</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                            onclick="editOpening(1)" title="Bearbeiten">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-info" 
                                            onclick="copyOpening(1)" title="Kopieren">
                                            <i class="ti ti-copy"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteOpening(1)" title="Löschen">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">2</td>
                                <td>
                                    <div>
                                        <strong>Wochenend Service</strong><br>
                                        <small class="text-muted">Abholung/Rückgabe am Wochenende</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-light text-muted">Mo</span>
                                        <span class="badge bg-light text-muted">Di</span>
                                        <span class="badge bg-light text-muted">Mi</span>
                                        <span class="badge bg-light text-muted">Do</span>
                                        <span class="badge bg-light text-muted">Fr</span>
                                        <span class="badge bg-success">Sa</span>
                                        <span class="badge bg-success">So</span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>10:00 - 16:00</strong><br>
                                        <small class="text-muted">Keine Pause</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">Ignoriert</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">Aktiv</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                            onclick="editOpening(2)" title="Bearbeiten">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-info" 
                                            onclick="copyOpening(2)" title="Kopieren">
                                            <i class="ti ti-copy"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteOpening(2)" title="Löschen">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">3</td>
                                <td>
                                    <div>
                                        <strong>24h Notfall Service</strong><br>
                                        <small class="text-muted">Für dringende Reparaturen</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span class="badge bg-danger">Mo</span>
                                        <span class="badge bg-danger">Di</span>
                                        <span class="badge bg-danger">Mi</span>
                                        <span class="badge bg-danger">Do</span>
                                        <span class="badge bg-danger">Fr</span>
                                        <span class="badge bg-danger">Sa</span>
                                        <span class="badge bg-danger">So</span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>00:00 - 23:59</strong><br>
                                        <small class="text-muted">24/7 verfügbar</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-danger">Auch Feiertags</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">Inaktiv</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                            onclick="editOpening(3)" title="Bearbeiten">
                                            <i class="ti ti-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-info" 
                                            onclick="copyOpening(3)" title="Kopieren">
                                            <i class="ti ti-copy"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteOpening(3)" title="Löschen">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-clock me-2"></i>Öffnungszeiten erstellen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.openings.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label required">Name/Bezeichnung</label>
                                <input type="text" class="form-control" name="name" required
                                    placeholder="z.B. Standard Geschäftszeiten">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active">Aktiv</option>
                                    <option value="inactive">Inaktiv</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Beschreibung</label>
                        <textarea class="form-control" name="description" rows="2"
                            placeholder="Kurze Beschreibung der Öffnungszeiten"></textarea>
                    </div>

                    <hr>
                    <h6>Wochentage und Zeiten</h6>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Aktive Wochentage</label>
                            <div class="form-check-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" value="monday" id="monday">
                                    <label class="form-check-label" for="monday">Montag</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" value="tuesday" id="tuesday">
                                    <label class="form-check-label" for="tuesday">Dienstag</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" value="wednesday" id="wednesday">
                                    <label class="form-check-label" for="wednesday">Mittwoch</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" value="thursday" id="thursday">
                                    <label class="form-check-label" for="thursday">Donnerstag</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" value="friday" id="friday">
                                    <label class="form-check-label" for="friday">Freitag</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" value="saturday" id="saturday">
                                    <label class="form-check-label" for="saturday">Samstag</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" value="sunday" id="sunday">
                                    <label class="form-check-label" for="sunday">Sonntag</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Öffnungszeit</label>
                                <input type="time" class="form-control" name="open_time" value="09:00">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Schließzeit</label>
                                <input type="time" class="form-control" name="close_time" value="18:00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Pausenbeginn (optional)</label>
                                <input type="time" class="form-control" name="break_start">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pausenende (optional)</label>
                                <input type="time" class="form-control" name="break_end">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="include_holidays" id="includeHolidays">
                                <label class="form-check-label" for="includeHolidays">
                                    Feiertage berücksichtigen
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_24h" id="is24h">
                                <label class="form-check-label" for="is24h">
                                    24 Stunden Service
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function applyTemplate(type) {
    const modal = new bootstrap.Modal(document.getElementById('createModal'));
    
    // Clear all checkboxes first
    document.querySelectorAll('input[name="days[]"]').forEach(cb => cb.checked = false);
    
    switch(type) {
        case 'business':
            // Monday to Friday
            ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'].forEach(day => {
                document.getElementById(day).checked = true;
            });
            document.querySelector('input[name="open_time"]').value = '09:00';
            document.querySelector('input[name="close_time"]').value = '18:00';
            document.querySelector('input[name="break_start"]').value = '12:00';
            document.querySelector('input[name="break_end"]').value = '13:00';
            document.querySelector('input[name="name"]').value = 'Standard Geschäftszeiten';
            break;
            
        case 'shop':
            // Monday to Saturday
            ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'].forEach(day => {
                document.getElementById(day).checked = true;
            });
            document.querySelector('input[name="open_time"]').value = '10:00';
            document.querySelector('input[name="close_time"]').value = '20:00';
            document.querySelector('input[name="name"]').value = 'Ladenöffnungszeiten';
            break;
            
        case '24h':
            // All days
            ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'].forEach(day => {
                document.getElementById(day).checked = true;
            });
            document.querySelector('input[name="open_time"]').value = '00:00';
            document.querySelector('input[name="close_time"]').value = '23:59';
            document.querySelector('input[name="name"]').value = '24h Service';
            document.getElementById('is24h').checked = true;
            break;
    }
    
    modal.show();
}

function editOpening(id) {
    // Implement edit functionality
    window.location.href = `/admin/openings/${id}/edit`;
}

function copyOpening(id) {
    if (confirm('Öffnungszeit kopieren und als neue Vorlage erstellen?')) {
        // Implement copy functionality
        alert('Kopierfunktion würde hier implementiert werden.');
    }
}

function deleteOpening(id) {
    if (confirm('Öffnungszeit wirklich löschen?\n\nAlle zugehörigen Einstellungen gehen verloren!')) {
        // Create form and submit DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/openings/${id}`;
        
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

// 24h checkbox handler
document.addEventListener('DOMContentLoaded', function() {
    const is24hCheckbox = document.getElementById('is24h');
    const timeInputs = document.querySelectorAll('input[type="time"]');
    
    if (is24hCheckbox) {
        is24hCheckbox.addEventListener('change', function() {
            if (this.checked) {
                document.querySelector('input[name="open_time"]').value = '00:00';
                document.querySelector('input[name="close_time"]').value = '23:59';
                timeInputs.forEach(input => input.disabled = true);
            } else {
                timeInputs.forEach(input => input.disabled = false);
            }
        });
    }
});
</script>

<style>
.form-check-group .form-check {
    margin-bottom: 0.5rem;
}
.required::after {
    content: ' *';
    color: #dc3545;
}
</style>
@endsection
