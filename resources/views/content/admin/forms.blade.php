@extends('layouts/contentNavbarLayout')

@section('title', 'Formularverwaltung')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            $('.select2').select2();

            // DataTable initialisieren
            $('#formsTable').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/de-DE.json'
                }
            });

            // Formularfeld hinzufügen
            $('#addFieldBtn').click(function () {
                const fieldHtml = `
                            <div class="form-field mb-3 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label">Feldname</label>
                                        <input type="text" class="form-control" name="fields[][name]" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Typ</label>
                                        <select class="form-select" name="fields[][type]" required>
                                            <option value="text">Text</option>
                                            <option value="email">E-Mail</option>
                                            <option value="number">Zahl</option>
                                            <option value="select">Auswahl</option>
                                            <option value="checkbox">Checkbox</option>
                                            <option value="radio">Radio</option>
                                            <option value="textarea">Textbereich</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Erforderlich</label>
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="fields[][required]" value="1">
                                            <label class="form-check-label">Ja</label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-danger d-block w-100 remove-field">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                $('#formFields').append(fieldHtml);
            });

            // Formularfeld entfernen
            $(document).on('click', '.remove-field', function () {
                $(this).closest('.form-field').remove();
            });
        });
    </script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Admin /</span> Formularverwaltung
        </h4>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Formulare -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Verfügbare Formulare</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newFormModal">
                    <i class="ti ti-plus me-1"></i>
                    Neues Formular
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="formsTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Typ</th>
                                <th>Status</th>
                                <th>Erstellt am</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($forms as $form)
                                <tr>
                                    <td>{{ $form->name }}</td>
                                    <td>{{ $form->type }}</td>
                                    <td>
                                        <span class="badge bg-{{ $form->status === 'active' ? 'success' : 'danger' }}">
                                            {{ $form->status === 'active' ? 'Aktiv' : 'Inaktiv' }}
                                        </span>
                                    </td>
                                    <td>{{ $form->created_at->format('d.m.Y') }}</td>
                                    <td>
                                        <div class="d-inline-block">
                                            <button class="btn btn-sm btn-icon" data-bs-toggle="tooltip" title="Bearbeiten"
                                                onclick="editForm({{ $form->id }})">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.forms.toggle-status', $form) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-icon" data-bs-toggle="tooltip"
                                                    title="{{ $form->status === 'active' ? 'Deaktivieren' : 'Aktivieren' }}">
                                                    <i class="ti ti-{{ $form->status === 'active' ? 'ban' : 'check' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.forms.destroy', $form) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-icon" data-bs-toggle="tooltip"
                                                    title="Löschen"
                                                    onclick="return confirm('Sind Sie sicher, dass Sie dieses Formular löschen möchten?')">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="alert alert-info mb-0">
                                            <i class="ti ti-info-circle me-1"></i>
                                            Keine Formulare verfügbar. Erstellen Sie ein neues Formular mit dem Button oben.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal für neues Formular -->
    <div class="modal fade" id="newFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.forms.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Neues Formular erstellen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="formName" class="form-label">Name</label>
                                <input type="text" class="form-control" id="formName" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="formType" class="form-label">Typ</label>
                                <select class="select2 form-select" id="formType" name="type" required>
                                    <option value="">Typ auswählen</option>
                                    <option value="contact">Kontakt</option>
                                    <option value="request">Anfrage</option>
                                    <option value="custom">Benutzerdefiniert</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="formDescription" class="form-label">Beschreibung</label>
                            <textarea class="form-control" id="formDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Formularfelder</label>
                            <div id="formFields">
                                <!-- Hier werden die Formularfelder dynamisch hinzugefügt -->
                            </div>
                            <button type="button" class="btn btn-outline-primary mt-2" id="addFieldBtn">
                                <i class="ti ti-plus me-1"></i>
                                Feld hinzufügen
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-primary">Speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal für Formular bearbeiten -->
    <div class="modal fade" id="editFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editFormForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Formular bearbeiten</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Formularfelder werden dynamisch eingefügt -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-primary">Speichern</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection