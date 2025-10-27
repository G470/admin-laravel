@extends('layouts/contentNavbarLayout')

@section('title', 'Template Details - ' . $rentalFieldTemplate->name)

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin / Dynamic Rental Fields /</span> {{ $rentalFieldTemplate->name }}
    </h4>

    <!-- Template Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Template Details</h5>
                    <div class="d-flex gap-2">
                        @if($rentalFieldTemplate->canBeDeleted())
                            <form action="{{ route('admin.rental-field-templates.duplicate', $rentalFieldTemplate) }}"
                                method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="ti ti-copy me-1"></i>Duplizieren
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.rental-field-templates.export', $rentalFieldTemplate) }}"
                            class="btn btn-outline-info">
                            <i class="ti ti-download me-1"></i>Export
                        </a>
                        <a href="{{ route('admin.rental-field-templates.edit', $rentalFieldTemplate) }}"
                            class="btn btn-primary">
                            <i class="ti ti-edit me-1"></i>Bearbeiten
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="fw-semibold mb-3">{{ $rentalFieldTemplate->name }}</h6>
                            <p class="text-muted mb-3">
                                {{ $rentalFieldTemplate->description ?: 'Keine Beschreibung vorhanden.' }}</p>

                            <div class="row">
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">Status</small>
                                    @if($rentalFieldTemplate->is_active)
                                        <span class="badge bg-label-success">Aktiv</span>
                                    @else
                                        <span class="badge bg-label-danger">Inaktiv</span>
                                    @endif
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted d-block">Sortierung</small>
                                    <span class="fw-medium">{{ $rentalFieldTemplate->sort_order }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="avatar avatar-xl mb-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="ti ti-template ti-lg"></i>
                                    </span>
                                </div>
                                <h6 class="mb-1">{{ $rentalFieldTemplate->fields->count() }} Felder</h6>
                                <small class="text-muted">{{ $rentalFieldTemplate->categories->count() }} Kategorien
                                    zugeordnet</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Fields -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Template Felder</h5>
                </div>
                <div class="card-body">
                    @if($rentalFieldTemplate->fields->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Reihenfolge</th>
                                        <th>Name</th>
                                        <th>Typ</th>
                                        <th>Eigenschaften</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rentalFieldTemplate->fields as $field)
                                        <tr>
                                            <td>
                                                <span class="badge bg-label-secondary">{{ $field->sort_order }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <h6 class="mb-0">{{ $field->field_label }}</h6>
                                                    <small class="text-muted">{{ $field->field_name }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-label-info">{{ $field->field_type_label }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if($field->is_required)
                                                        <span class="badge bg-label-warning" title="Pflichtfeld">P</span>
                                                    @endif
                                                    @if($field->is_filterable)
                                                        <span class="badge bg-label-success" title="Filterbar">F</span>
                                                    @endif
                                                    @if($field->is_searchable)
                                                        <span class="badge bg-label-primary" title="Durchsuchbar">S</span>
                                                    @endif
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
                                    <i class="ti ti-file-text-off"></i>
                                </span>
                            </div>
                            <h6>Keine Felder definiert</h6>
                            <p class="text-muted">Fügen Sie Felder zu diesem Template hinzu.</p>
                            <a href="{{ route('admin.rental-field-templates.edit', $rentalFieldTemplate) }}"
                                class="btn btn-primary btn-sm">
                                <i class="ti ti-plus me-1"></i>Felder hinzufügen
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Categories -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Zugeordnete Kategorien</h5>
                </div>
                <div class="card-body">
                    @if($rentalFieldTemplate->categories->count() > 0)
                        @foreach($rentalFieldTemplate->categories as $category)
                            <span class="badge bg-label-info me-1 mb-1">{{ $category->name }}</span>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">Keine Kategorien zugeordnet</p>
                    @endif
                </div>
            </div>

            <!-- Usage Statistics -->
            @if(isset($usageStats) && !empty($usageStats))
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Nutzungsstatistiken</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="mb-1">{{ $usageStats['rentals_using_template'] ?? 0 }}</h4>
                                    <small class="text-muted">Mietobjekte verwenden das Template</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="mb-1">{{ $usageStats['usage_percentage'] ?? 0 }}%</h4>
                                <small class="text-muted">Nutzungsrate</small>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Verfügbare Mietobjekte:</small>
                            <span class="fw-medium">{{ $usageStats['available_rentals'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Filterbare Felder:</small>
                            <span class="fw-medium">{{ $usageStats['filterable_fields'] ?? 0 }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Pflichtfelder:</small>
                            <span class="fw-medium">{{ $usageStats['required_fields'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.rental-field-templates.index') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left me-1"></i>Zurück zur Übersicht
                </a>

                @if(!$rentalFieldTemplate->canBeDeleted())
                    <div class="alert alert-warning d-inline-flex align-items-center mb-0">
                        <i class="ti ti-alert-triangle me-2"></i>
                        <small>Template wird verwendet und kann nicht gelöscht werden.</small>
                    </div>
                @else
                    <form action="{{ route('admin.rental-field-templates.destroy', $rentalFieldTemplate) }}" method="POST"
                        class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger"
                            onclick="return confirm('Sind Sie sicher, dass Sie dieses Template löschen möchten? Diese Aktion kann nicht rückgängig gemacht werden.')">
                            <i class="ti ti-trash me-1"></i>Template löschen
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection