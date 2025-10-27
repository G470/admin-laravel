<div>
    <!-- Success Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Default Notification Settings -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 ">Deine Standard-Benachrichtigungsoptionen</h5>
            @if(!$isEditingDefault)
                <button class="btn btn-light btn-sm" wire:click="toggleDefaultEdit">
                    <i class="ti ti-edit me-1"></i>BEARBEITEN
                </button>
            @endif
        </div>
        <div class="card-body">
            <p class=" mb-3">
                Informiere deine Mieter, wie sie dich optimal erreichen können. Gib zuerst deine Standard-Kontaktdaten
                an.
                Danach kannst du für jeden Standort spezifische Kontaktinformationen hinterlegen.
            </p>

            @if($isEditingDefault)
                <form wire:submit.prevent="saveDefaultSettings">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="defaultEmail" class="form-label text-white">Standard
                                    Benachrichtigungs-E-Mail</label>
                                <input type="email"
                                    class="form-control @error('defaultNotificationEmail') is-invalid @enderror"
                                    id="defaultEmail" wire:model.defer="defaultNotificationEmail"
                                    placeholder="notification@example.com">
                                @error('defaultNotificationEmail')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-check me-1"></i>Speichern
                        </button>
                        <button type="button" class="btn btn-outline-light" wire:click="toggleDefaultEdit">
                            <i class="ti ti-x me-1"></i>Abbrechen
                        </button>
                    </div>
                </form>
            @else
                <div class="d-flex align-items-center">
                    <i class="ti ti-mail me-2 text-white"></i>
                    <span class="text-white">
                        {{ $defaultNotificationEmail ?: 'Keine Standard-E-Mail festgelegt' }}
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- Locations Notification Settings -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Benachrichtigungsoptionen Ihrer Standorte</h5>
        </div>
        <div class="card-body">
            @if($locations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>LAND</th>
                                <th>PLZ</th>
                                <th>ORT</th>
                                <th>STRASSE, NR.</th>
                                <th>VERWENDET</th>
                                <th>AKTIONEN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($locations as $location)
                                <tr>
                                    <td>{{ $location['country'] ?? 'Deutschland' }}</td>
                                    <td>{{ $location['postal_code'] }}</td>
                                    <td>{{ $location['city'] }}</td>
                                    <td>
                                        @if($location['street'])
                                            {{ $location['street'] }}
                                            @if($location['house_number'])
                                                {{ $location['house_number'] }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($location['use_custom_notifications'])
                                            <span class="badge bg-success">Eigene</span>
                                        @else
                                            <span class="badge bg-info">Standard</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-outline-primary"
                                                wire:click="editLocation({{ $location['id'] }}, {{ $location['country_id'] }})" title="Bearbeiten">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @if($location['use_custom_notifications'])
                                                <button class="btn btn-sm btn-outline-danger"
                                                    wire:click="resetLocationToDefault({{ $location['id'] }})"
                                                    title="Auf Standard zurücksetzen"
                                                    onclick="return confirm('Möchten Sie diese Standort-Benachrichtigungen wirklich auf Standard zurücksetzen?')">
                                                    <i class="ti ti-trash"></i>
                                                </button>
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
                    <i class="ti ti-map-pin-off fs-1 text-muted mb-3"></i>
                    <h6 class="text-muted">Keine Standorte gefunden</h6>
                    <p class="text-muted">Sie haben noch keine Standorte erstellt.</p>
                    <a href="{{ route('vendor-locations') }}" class="btn btn-primary">
                        <i class="ti ti-plus me-1"></i>Ersten Standort erstellen
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Location Edit Modal -->
    @if($showLocationModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-bell me-2"></i>Benachrichtigungen bearbeiten
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeLocationModal"></button>
                    </div>
                    <form wire:submit.prevent="saveLocationSettings">
                        <div class="modal-body">
                            @if($currentLocation)
                                <div class="alert alert-info">
                                    <strong>Standort:</strong>
                                    {{ $currentLocation->city }}, {{ $currentLocation->postal_code }}
                                    @if($currentLocation->street_address)
                                        <br><small>{{ $currentLocation->street_address }}
                                        @if($currentLocation->additional_address)
                                            {{ $currentLocation->additional_address }}
                                        @endif</small>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="useCustomNotifications"
                                            wire:model="useCustomNotifications">
                                        <label class="form-check-label" for="useCustomNotifications">
                                            Spezifische Benachrichtigungs-E-Mail für diesen Standort verwenden
                                        </label>
                                    </div>
                                </div>

                                @if(!$useCustomNotifications)
                                    <div class="mb-3">
                                        <label class="form-label">Verwendete E-Mail-Adresse</label>
                                        <div class="form-control-plaintext bg-light p-2 rounded">
                                            <i class="ti ti-mail me-2"></i>
                                            {{ $defaultNotificationEmail ?: 'Keine Standard-E-Mail festgelegt' }}
                                            <small class="text-muted d-block">Standard-Einstellung</small>
                                        </div>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <label for="locationEmail" class="form-label">Benachrichtigungs-E-Mail für diesen
                                            Standort</label>
                                        <input type="email"
                                            class="form-control @error('locationNotificationEmail') is-invalid @enderror"
                                            id="locationEmail" wire:model.defer="locationNotificationEmail"
                                            placeholder="standort@example.com">
                                        @error('locationNotificationEmail')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            Diese E-Mail-Adresse wird für alle Benachrichtigungen zu diesem Standort verwendet.
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeLocationModal">
                                <i class="ti ti-x me-1"></i>Abbrechen
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-check me-1"></i>Speichern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <style>
        .card:first-child {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        .modal.show {
            display: block !important;
        }

        .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .text-white .form-control {
            background-color: rgba(255, 255, 255, 0.9);
        }

        .form-control-plaintext {
            display: flex;
            align-items-center;
        }
    </style>
</div>