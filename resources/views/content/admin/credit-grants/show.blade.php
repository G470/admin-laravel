@extends('layouts/contentNavbarLayout')

@section('title', 'Credit-Vergabe Details')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Credit-Vergabe Details</h4>
                    <div class="page-title-right">
                        <a href="{{ route('admin.credit-grants.index') }}" class="btn btn-secondary">
                            <i class="ti ti-arrow-left me-1"></i>Zurück zur Übersicht
                        </a>
                        @if($creditGrant->canBeEdited())
                            <a href="{{ route('admin.credit-grants.edit', $creditGrant->id) }}" class="btn btn-primary">
                                <i class="ti ti-edit me-1"></i>Bearbeiten
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Details -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-gift text-primary me-2"></i>
                            Credit-Vergabe #{{ $creditGrant->id }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Vendor</label>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-wrapper me-3">
                                            <div class="avatar">
                                                <img src="{{ asset('assets/img/avatars/default.png') }}" alt="Vendor Avatar"
                                                    class="rounded">
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $creditGrant->vendor->name }}</h6>
                                            <small class="text-muted">{{ $creditGrant->vendor->email }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Vergebender Admin</label>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-wrapper me-3">
                                            <div class="avatar">
                                                <img src="{{ asset('assets/img/avatars/default.png') }}" alt="Admin Avatar"
                                                    class="rounded">
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $creditGrant->admin->name }}</h6>
                                            <small class="text-muted">Admin</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Credits vergeben</label>
                                    <div class="d-flex align-items-center">
                                        <span class="fs-3 fw-bold text-primary">{{ $creditGrant->credits_granted }}</span>
                                        <small class="text-muted ms-2">Credits</small>
                                    </div>
                                    @if($creditGrant->formatted_monetary_value)
                                        <small class="text-muted">{{ $creditGrant->formatted_monetary_value }}</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Vergabe-Typ</label>
                                    <div>
                                        <span class="badge bg-label-{{ $creditGrant->grant_type_color }} fs-6">
                                            {{ $creditGrant->grant_type_label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Status</label>
                                    <div>
                                        <span class="badge bg-label-{{ $creditGrant->status_color }} fs-6">
                                            {{ $creditGrant->status_label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Credit-Paket</label>
                                    <p class="mb-0">{{ $creditGrant->creditPackage->name }}</p>
                                    <small class="text-muted">
                                        {{ $creditGrant->creditPackage->credits_amount }} Credits -
                                        {{ number_format($creditGrant->creditPackage->standard_price, 2, ',', '.') }}€
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Vergabe-Datum</label>
                                    <p class="mb-0">{{ $creditGrant->formatted_grant_date }}</p>
                                    <small class="text-muted">Erstellt am {{ $creditGrant->formatted_created_at }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Grund für Vergabe</label>
                            <div class="p-3 bg-light rounded">
                                {{ $creditGrant->reason }}
                            </div>
                        </div>

                        @if($creditGrant->internal_note)
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Interne Notiz</label>
                                <div class="p-3 bg-light rounded">
                                    <i class="ti ti-note text-muted me-2"></i>
                                    {{ $creditGrant->internal_note }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Actions Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Aktionen</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($creditGrant->canBeEdited())
                                <a href="{{ route('admin.credit-grants.edit', $creditGrant->id) }}" class="btn btn-primary">
                                    <i class="ti ti-edit me-1"></i>Bearbeiten
                                </a>
                            @endif

                            @if($creditGrant->canBeDeleted())
                                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                    <i class="ti ti-trash me-1"></i>Löschen
                                </button>
                            @endif

                            <a href="{{ route('admin.credit-grants.index') }}" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i>Zurück zur Übersicht
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Vendor Info Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Vendor Informationen</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-wrapper me-3">
                                <div class="avatar">
                                    <img src="{{ asset('assets/img/avatars/default.png') }}" alt="Vendor Avatar"
                                        class="rounded">
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $creditGrant->vendor->name }}</h6>
                                <small class="text-muted">{{ $creditGrant->vendor->email }}</small>
                            </div>
                        </div>

                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h5 class="mb-0 text-primary">{{ $creditGrant->vendor->rentals_count ?? 0 }}</h5>
                                    <small class="text-muted">Rentals</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h5 class="mb-0 text-success">{{ $creditGrant->vendor->bookings_count ?? 0 }}</h5>
                                <small class="text-muted">Buchungen</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Info Card -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">System Informationen</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">ID:</small>
                            <div class="fw-semibold">{{ $creditGrant->id }}</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Erstellt:</small>
                            <div class="fw-semibold">{{ $creditGrant->formatted_created_at }}</div>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Aktualisiert:</small>
                            <div class="fw-semibold">{{ $creditGrant->updated_at->format('d.m.Y H:i') }}</div>
                        </div>
                        @if($creditGrant->status === 'completed')
                            <div class="alert alert-success mb-0">
                                <i class="ti ti-check-circle me-1"></i>
                                Credits wurden erfolgreich gutgeschrieben
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($creditGrant->canBeDeleted())
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Credit-Vergabe löschen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Sind Sie sicher, dass Sie diese Credit-Vergabe löschen möchten?</p>
                        <div class="alert alert-warning">
                            <i class="ti ti-alert-triangle me-1"></i>
                            <strong>Achtung:</strong> Diese Aktion kann nicht rückgängig gemacht werden.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                        <form action="{{ route('admin.credit-grants.destroy', $creditGrant->id) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti ti-trash me-1"></i>Löschen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            function confirmDelete() {
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            }
        </script>
    @endpush
@endsection