@extends('layouts/contentNavbarLayoutBackend')

@section('title', 'Nachrichten - Admin')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css'
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'
])
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-mail me-2"></i>Nachrichten & Kontaktanfragen
                </h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#composeModal">
                    <i class="ti ti-plus me-1"></i>Neue Nachricht
                </button>
            </div>
            
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Messages Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Status</th>
                                <th>Von</th>
                                <th>Betreff</th>
                                <th>Datum</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Sample data - replace with actual data -->
                            <tr>
                                <td>
                                    <span class="badge bg-primary">Neu</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <div class="avatar-initial bg-label-info rounded-circle">
                                                <i class="ti ti-user"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <strong>Max Mustermann</strong><br>
                                            <small class="text-muted">max@example.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>Anfrage zu Vermietung</strong><br>
                                    <small class="text-muted">Ich interessiere mich für...</small>
                                </td>
                                <td>
                                    <span>21.07.2025</span><br>
                                    <small class="text-muted">14:30</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                            onclick="viewMessage(1)" title="Anzeigen">
                                            <i class="ti ti-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success" 
                                            onclick="markAsRead(1)" title="Als gelesen markieren">
                                            <i class="ti ti-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteMessage(1)" title="Löschen">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="badge bg-success">Gelesen</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <div class="avatar-initial bg-label-warning rounded-circle">
                                                <i class="ti ti-user"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <strong>Anna Schmidt</strong><br>
                                            <small class="text-muted">anna@example.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span>Support Anfrage</span><br>
                                    <small class="text-muted">Ich habe ein Problem mit...</small>
                                </td>
                                <td>
                                    <span>20.07.2025</span><br>
                                    <small class="text-muted">09:15</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" 
                                            onclick="viewMessage(2)" title="Anzeigen">
                                            <i class="ti ti-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-warning" 
                                            onclick="archiveMessage(2)" title="Archivieren">
                                            <i class="ti ti-archive"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" 
                                            onclick="deleteMessage(2)" title="Löschen">
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

<!-- Compose Message Modal -->
<div class="modal fade" id="composeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-mail me-2"></i>Neue Nachricht verfassen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.messages.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="recipient_id" class="form-label">Empfänger</label>
                        <select class="form-select" id="recipient_id" name="recipient_id" required>
                            <option value="">Empfänger auswählen...</option>
                            <!-- Add users from database -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Betreff</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Nachricht</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-send me-1"></i>Senden
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function viewMessage(id) {
    window.location.href = `/admin/messages/${id}`;
}

function markAsRead(id) {
    if (confirm('Nachricht als gelesen markieren?')) {
        fetch(`/admin/messages/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: 'read' })
        }).then(() => location.reload());
    }
}

function archiveMessage(id) {
    if (confirm('Nachricht archivieren?')) {
        fetch(`/admin/messages/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: 'archived' })
        }).then(() => location.reload());
    }
}

function deleteMessage(id) {
    if (confirm('Nachricht wirklich löschen?')) {
        fetch(`/admin/messages/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(() => location.reload());
    }
}
</script>
@endsection
