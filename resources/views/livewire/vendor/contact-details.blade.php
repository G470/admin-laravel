<div>
    <!-- Success Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Default Contact Details Card -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Deine Standard-Kontaktdaten</h5>
            @if(!$isEditingDefault)
                <button class="btn btn-light btn-sm" wire:click="toggleDefaultEdit">
                    <i class="ti ti-edit me-1"></i>BEARBEITEN
                </button>
            @endif
        </div>
        <div class="card-body">
            <p class="mb-3">
                Wenn an einem Standort keine eigenen Kontaktdaten angegeben sind oder eine Suche ohne Ortsangabe
                erfolgt,
                werden automatisch deine Standard-Kontaktdaten angezeigt.
            </p>

            @if($isEditingDefault)
                @include('livewire.vendor.contact-details.default-form')
            @else
                @include('livewire.vendor.contact-details.default-display')
            @endif
        </div>
    </div>

    <!-- Locations Contact Details -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Kontaktdaten Ihrer Standorte</h5>
        </div>
        <div class="card-body">
            @if($locations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>STANDORT</th>
                                <th>PLZ</th>
                                <th>ORT</th>
                                <th>STRASSE, NR.</th>
                                <th>KONTAKTDATEN</th>
                                <th>AKTIONEN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($locations as $location)
                                <tr>
                                    <td>{{ $location['name'] ?: '-' }}</td>
                                    <td>{{ $location['postal_code'] }}</td>
                                    <td>{{ $location['city'] }}</td>
                                    <td>
                                        @if($location['street_address'])
                                            {{ $location['street_address'] }}
                                            @if($location['additional_address'])
                                                {{ $location['additional_address'] }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($location['has_custom_contact'])
                                            <span class="badge bg-success">Eigene</span>
                                        @else
                                            <span class="badge bg-info">Standard</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-outline-primary"
                                                wire:click="editLocation({{ $location['id'] }})" title="Bearbeiten">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            @if($location['has_custom_contact'])
                                                <button class="btn btn-sm btn-outline-danger"
                                                    wire:click="resetLocationToDefault({{ $location['id'] }})"
                                                    title="Auf Standard zurücksetzen"
                                                    onclick="return confirm('Möchten Sie diese Standort-Kontaktdaten wirklich auf Standard zurücksetzen?')">
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
        @include('livewire.vendor.contact-details.location-modal')
    @endif
</div>