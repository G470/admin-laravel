@extends('layouts/contentNavbarLayout')

@section('title', 'Dynamic Rental Fields')

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Dynamic Rental Fields
    </h4>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Field Templates</h5>
                    <a href="{{ route('admin.rental-field-templates.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>Neues Template
                    </a>
                </div>
                <div class="card-body">
                    @if($templates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Beschreibung</th>
                                        <th>Kategorien</th>
                                        <th>Felder</th>
                                        <th>Status</th>
                                        <th>Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($templates as $template)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-initial rounded bg-label-primary">
                                                            <i class="ti ti-template"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $template->name }}</h6>
                                                        <small class="text-muted">Sortierung: {{ $template->sort_order }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                                    {{ $template->description ?: 'Keine Beschreibung' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($template->categories->count() > 0)
                                                    @foreach($template->categories as $category)
                                                        <span class="badge bg-label-info me-1">{{ $category->name }}</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">Keine Kategorien</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-label-success">{{ $template->fields->count() }} Felder</span>
                                            </td>
                                            <td>
                                                @if($template->is_active)
                                                    <span class="badge bg-label-success">Aktiv</span>
                                                @else
                                                    <span class="badge bg-label-danger">Inaktiv</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('admin.rental-field-templates.show', $template) }}"
                                                        class="btn btn-sm btn-icon btn-outline-info" title="Anzeigen">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.rental-field-templates.edit', $template) }}"
                                                        class="btn btn-sm btn-icon btn-outline-primary" title="Bearbeiten">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-icon btn-outline-secondary"
                                                        onclick="toggleStatus({{ $template->id }})" title="Status umschalten">
                                                        <i class="ti ti-toggle-{{ $template->is_active ? 'right' : 'left' }}"></i>
                                                    </button>
                                                    @if($template->canBeDeleted())
                                                        <form action="{{ route('admin.rental-field-templates.destroy', $template) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-icon btn-outline-danger"
                                                                onclick="return confirm('Sind Sie sicher, dass Sie dieses Template löschen möchten?')"
                                                                title="Löschen">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
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
                                    <i class="ti ti-template-off"></i>
                                </span>
                            </div>
                            <h5>Keine Templates vorhanden</h5>
                            <p class="text-muted">Erstellen Sie Ihr erstes Template für dynamische Mietobjekt-Felder.</p>
                            <a href="{{ route('admin.rental-field-templates.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i>Erstes Template erstellen
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleStatus(templateId) {
            fetch(`/admin/rental-field-templates/${templateId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to update status display
                    location.reload();
                } else {
                    alert('Fehler beim Ändern des Status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Fehler beim Ändern des Status');
            });
        }
    </script>
    @endpush
@endsection