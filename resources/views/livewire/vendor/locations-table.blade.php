<div>
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Add New Location Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div></div>
        <a href="{{ route('vendor-location-create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>Neuen Standort hinzufügen
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label for="search" class="form-label">Suchen</label>
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Adresse, Stadt, PLZ oder Name suchen...">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="perPage" class="form-label">Pro Seite</label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end mb-2">
                    <button wire:click="clearSearch" class="btn btn-secondary">
                        <i class="ti ti-refresh me-1"></i>Zurücksetzen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Locations Table -->
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th style="width: 60px;">
                            <button wire:click="sortBy('id')" class="btn btn-link p-0 text-start">
                                ID
                                @if($sortField === 'id')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>Status</th>
                        <th>
                            <button wire:click="sortBy('street_address')" class="btn btn-link p-0 text-start">
                                Adresse
                                @if($sortField === 'street_address')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('city')" class="btn btn-link p-0 text-start">
                                Stadt
                                @if($sortField === 'city')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('country')" class="btn btn-link p-0 text-start">
                                Land
                                @if($sortField === 'country')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('is_active')" class="btn btn-link p-0 text-start">
                                Aktiv
                                @if($sortField === 'is_active')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th style="width: 120px;">Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $location)
                        <tr>
                            <td>{{ $location->id }}</td>
                            <td>
                                @if($location->is_main)
                                    <span class="badge bg-label-primary">
                                        <i class="ti ti-star-filled me-1"></i>Hauptstandort
                                    </span>
                                @else
                                    <span class="badge bg-label-secondary">Standort</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">{{ $location->street_address }}</span>
                                    @if($location->name)
                                        <small class="text-muted">{{ $location->name }}</small>
                                    @endif
                                    @if($location->additional_address)
                                        <small class="text-muted">{{ $location->additional_address }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ $location->postal_code }} {{ $location->city }}</span>
                                    @if($location->phone)
                                        <small class="text-muted">
                                            <i class="ti ti-phone me-1"></i>{{ $location->phone }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php
                                    $countries = [
                                        'DE' => ['name' => 'Deutschland', 'flag' => 'de'],
                                        'AT' => ['name' => 'Österreich', 'flag' => 'at'],
                                        'CH' => ['name' => 'Schweiz', 'flag' => 'ch'],
                                        'FR' => ['name' => 'Frankreich', 'flag' => 'fr'],
                                        'IT' => ['name' => 'Italien', 'flag' => 'it'],
                                    ];
                                    $country = $countries[$location->country] ?? ['name' => $location->country, 'flag' => strtolower($location->country)];
                                @endphp
                                <div class="d-flex align-items-center">
                                    <i class="flag-icon flag-icon-{{ $country['flag'] }} me-2"></i>
                                    {{ $country['name'] }}
                                </div>
                            </td>
                            <td>
                                @if($location->is_active)
                                    <span class="badge bg-label-success">Aktiv</span>
                                @else
                                    <span class="badge bg-label-danger">Inaktiv</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('vendor-location-edit', ['id' => $location->id]) }}" 
                                       class="btn btn-sm btn-icon" 
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="Bearbeiten">
                                        <i class="ti ti-edit text-primary"></i>
                                    </a>
                                    
                                    @if(!$location->is_main)
                                        <button wire:click="setMainLocation({{ $location->id }})" 
                                                wire:confirm="Möchten Sie '{{ $location->street_address }}' als Hauptstandort festlegen? Der aktuelle Hauptstandort wird zu einem normalen Standort."
                                                class="btn btn-sm btn-icon" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                title="Als Hauptstandort festlegen">
                                            <i class="ti ti-star text-warning"></i>
                                        </button>
                                        
                                        <button wire:click="deleteLocation({{ $location->id }})" 
                                                wire:confirm="Sind Sie sicher, dass Sie den Standort '{{ $location->street_address }}' löschen möchten? Diese Aktion kann nicht rückgängig gemacht werden."
                                                class="btn btn-sm btn-icon" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                title="Löschen">
                                            <i class="ti ti-trash text-danger"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column justify-content-center align-items-center">
                                    <i class="ti ti-map-pin text-muted mb-3" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted">Keine Standorte gefunden</h5>
                                    @if($search)
                                        <p class="text-muted mb-3">Keine Standorte entsprechen den aktuellen Suchkriterien.</p>
                                        <button wire:click="clearSearch" class="btn btn-primary">
                                            <i class="ti ti-refresh me-1"></i>Suche zurücksetzen
                                        </button>
                                    @else
                                        <p class="text-muted mb-3">Sie haben noch keine Standorte angelegt.</p>
                                        <a href="{{ route('vendor-location-create') }}" class="btn btn-primary">
                                            <i class="ti ti-plus me-1"></i>Ersten Standort erstellen
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($locations->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Zeige {{ $locations->firstItem() }} bis {{ $locations->lastItem() }} von {{ $locations->total() }} Einträgen
                        </small>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $locations->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
