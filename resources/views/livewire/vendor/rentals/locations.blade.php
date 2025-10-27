<div>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Standorte auswählen</h5>
            <div class="d-flex gap-2">
                <!-- Actions Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ti ti-settings me-1"></i>
                        Aktionen
                    </button>
                    <ul class="dropdown-menu">
                        <li><h6 class="dropdown-header">Alle Standorte</h6></li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0);" wire:click="selectAllLocations">
                                <i class="ti ti-check-circle me-2"></i>Alle auswählen
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0);" wire:click="deselectAllLocations">
                                <i class="ti ti-x-circle me-2"></i>Alle abwählen
                            </a>
                        </li>
                        @if(count($locationsByCountry) > 1)
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Nach Land</h6></li>
                            @foreach($locationsByCountry as $countryGroup)
                                <li>
                                    <a class="dropdown-item" href="javascript:void(0);" wire:click="selectLocationsByCountry('{{ $countryGroup['country_code'] }}')">
                                        <i class="ti ti-check me-2"></i>{{ $countryGroup['country_name'] }} auswählen
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                
                <button type="button" class="btn btn-outline-primary" wire:click="showCreateLocationForm">
                    <i class="ti ti-plus me-1"></i>
                    Neuen Standort erstellen
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(count($selectedLocations) > 0)
                <div class="alert alert-success mb-3">
                    <i class="ti ti-check-circle me-2"></i>
                    <strong>{{ count($selectedLocations) }} Standort(e) ausgewählt</strong>
                </div>
            @endif

            @if(count($userLocations) > 0)
                <!-- Locations grouped by country -->
                @foreach($locationsByCountry as $countryGroup)
                    <div class="mb-4">
                        <!-- Country Header -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-primary mb-0">
                                <i class="ti ti-map-pin me-1"></i>
                                {{ $countryGroup['country_name'] }}
                                <span class="badge bg-light text-dark ms-2">{{ $countryGroup['count'] }} Standort(e)</span>
                            </h6>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        wire:click="selectLocationsByCountry('{{ $countryGroup['country_code'] }}')">
                                    <i class="ti ti-check me-1"></i>Alle
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                        wire:click="deselectLocationsByCountry('{{ $countryGroup['country_code'] }}')">
                                    <i class="ti ti-x me-1"></i>Keine
                                </button>
                            </div>
                        </div>

                        <!-- Country Locations -->
                        <div class="row">
                            @foreach($countryGroup['locations'] as $location)
                                <div class="col-md-6 mb-3">
                                    <div class="card {{ in_array('location-' . $location['id'], $selectedLocations) ? 'border-primary' : '' }}">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="card-title">
                                                        {{ $location['name'] }}
                                                        @if($location['is_main'])
                                                            <span class="badge bg-primary ms-1">Hauptstandort</span>
                                                        @endif
                                                    </h6>
                                                    <p class="card-text text-muted small mb-2">
                                                        {{ $location['full_address'] }}
                                                    </p>
                                                    <small class="text-muted">
                                                        <i class="ti ti-flag me-1"></i>{{ $countryGroup['country_name'] }}
                                                    </small>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                        wire:click="toggleLocation('location-{{ $location['id'] }}')"
                                                        {{ in_array('location-' . $location['id'], $selectedLocations) ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Noch keine Standorte vorhanden.</strong>
                    <br>Erstellen Sie zuerst einen Standort, um ihn für Vermietungsobjekte verwenden zu können.
                </div>
            @endif

            <!-- Create Location Form Modal -->
            @if($showCreateForm)
                <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="ti ti-plus me-2"></i>Neuen Standort erstellen
                                </h5>
                                <button type="button" class="btn-close" wire:click="hideCreateLocationForm"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Location Creation Form -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="locationName" class="form-label">Standortname</label>
                                        <input type="text" class="form-control" id="locationName" 
                                               wire:model="newLocation.name" placeholder="z.B. Büro Berlin">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="locationPhone" class="form-label">Telefon</label>
                                        <input type="text" class="form-control" id="locationPhone" 
                                               wire:model="newLocation.phone" placeholder="+49 30 12345678">
                                    </div>

                                    <div class="col-md-8 mb-3">
                                        <label for="streetAddress" class="form-label">Straße & Hausnummer</label>
                                        <input type="text" class="form-control" id="streetAddress" 
                                               wire:model="newLocation.street_address" placeholder="Musterstraße 123">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="additionalAddress" class="form-label">Zusatz</label>
                                        <input type="text" class="form-control" id="additionalAddress" 
                                               wire:model="newLocation.additional_address" placeholder="2. OG">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="country" class="form-label">Land</label>
                                        <select class="form-select" id="country" wire:model="newLocation.country">
                                            <option value="DE">Deutschland</option>
                                            <option value="AT">Österreich</option>
                                            <option value="CH">Schweiz</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="postalCode" class="form-label">PLZ</label>
                                        <input type="text" class="form-control" id="postalCode" 
                                               wire:model="newLocation.postal_code" placeholder="12345">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">Ort</label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control" id="city" 
                                                   wire:model.live="searchTerm" placeholder="Ort eingeben...">
                                            
                                            @if(count($filteredLocations) > 0)
                                                <div class="position-absolute w-100 border rounded mt-1 bg-white shadow-sm" style="z-index: 1000;">
                                                    @foreach($filteredLocations as $masterLocation)
                                                        <div class="p-2 border-bottom cursor-pointer hover-bg-light" 
                                                             wire:click="selectMasterLocation({{ $masterLocation['id'] }})">
                                                            <small class="text-primary">{{ $masterLocation['display'] }}</small>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="description" class="form-label">Beschreibung (optional)</label>
                                        <textarea class="form-control" id="description" rows="2" 
                                                  wire:model="newLocation.description" 
                                                  placeholder="Zusätzliche Informationen zum Standort..."></textarea>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="isMain" 
                                                   wire:model="newLocation.is_main">
                                            <label class="form-check-label" for="isMain">
                                                Als Hauptstandort festlegen
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" wire:click="hideCreateLocationForm">
                                    Abbrechen
                                </button>
                                <button type="button" class="btn btn-primary" wire:click="createLocation">
                                    <i class="ti ti-device-floppy me-1"></i>Standort erstellen
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
    document.addEventListener('livewire:initialized', function () {
        console.log('Locations component initialized');

        // Listen for location updates
        Livewire.on('locationsUpdated', (event) => {
            console.log('🔵 Locations updated event in locations component:', event);
        });

        // Listen for location creation success  
        Livewire.on('locationCreated', (event) => {
            console.log('🟣 Location created in locations component:', event);
        });
    });
    </script>

    <style>
        .hover-bg-light:hover {
            background-color: #f8f9fa !important;
            cursor: pointer;
        }
        
        .cursor-pointer {
            cursor: pointer;
        }
        
        .card.border-primary {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
        }
        
        .dropdown-header {
            padding: 0.5rem 1rem;
            margin-bottom: 0;
            font-size: 0.875rem;
            color: #6c757d;
            background-color: #f8f9fa;
        }
    </style>

    <!-- add bottom space -->
    <div class="mb-5"></div>
</div>
