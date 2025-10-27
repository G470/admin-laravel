<div>
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            <i class="ti ti-map-pin me-2"></i>Standorte verwalten
                        </h5>
                        <small class="text-muted">{{ $locations->total() }} Standorte insgesamt</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button wire:click="resetFilters" class="btn btn-outline-secondary" title="Filter zurücksetzen">
                            <i class="ti ti-refresh me-1"></i>Zurücksetzen
                        </button>
                        <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Neuer Standort
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="ti ti-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <i class="ti ti-alert-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="ti ti-map-pin text-primary fs-2 mb-2"></i>
                    <h6 class="card-title">Gesamt Standorte</h6>
                    <h4 class="text-primary">{{ number_format($statistics['total']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="ti ti-check text-success fs-2 mb-2"></i>
                    <h6 class="card-title">Aktive Standorte</h6>
                    <h4 class="text-success">{{ number_format($statistics['active']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="ti ti-building text-info fs-2 mb-2"></i>
                    <h6 class="card-title">Mit Vermietungen</h6>
                    <h4 class="text-info">{{ number_format($statistics['with_rentals']) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="ti ti-clock text-warning fs-2 mb-2"></i>
                    <h6 class="card-title">Neue (30 Tage)</h6>
                    <h4 class="text-warning">{{ number_format($statistics['new_this_month']) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="ti ti-filter me-2"></i>Filter & Suche
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Suche</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-search"></i></span>
                                <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                                    placeholder="Name, Stadt, Adresse, PLZ...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Vendor</label>
                            <select class="form-select" wire:model.live="vendorFilter">
                                <option value="">Alle Vendors</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Land</label>
                            <select class="form-select" wire:model.live="countryFilter">
                                <option value="">Alle Länder</option>
                                @foreach($countries as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Status</label>
                            <select class="form-select" wire:model.live="statusFilter">
                                <option value="">Alle Status</option>
                                <option value="active">Aktiv</option>
                                <option value="inactive">Inaktiv</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Pro Seite</label>
                            <select class="form-select" wire:model.live="perPage">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Locations Table -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0">
                        <i class="ti ti-list me-2"></i>Standorte ({{ $locations->count() }} von
                        {{ $locations->total() }})
                    </h6>
                    <div class="text-muted small">
                        Seite {{ $locations->currentPage() }} von {{ $locations->lastPage() }}
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($locations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">ID</th>
                                        <th wire:click="sortBy('name')" style="cursor: pointer;">
                                            Name
                                            @if($sortField === 'name')
                                                <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th>Vendor</th>
                                        <th wire:click="sortBy('city')" style="cursor: pointer;">
                                            Stadt
                                            @if($sortField === 'city')
                                                <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        <th>PLZ</th>
                                        <th>Land</th>
                                        <th>Koordinaten</th>
                                        <th>Anzahl Artikel</th>
                                        <th>Status</th>
                                        <th style="width: 120px;">Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($locations as $location)
                                        <tr>
                                            <td class="text-muted small">{{ $location->id }}</td>
                                            <td>
                                                <div>
                                                    <strong>{{ $location->name }}</strong>
                                                    @if($location->street_address)
                                                        <br><small class="text-muted">{{ $location->street_address }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($location->vendor)
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-xs me-2">
                                                            <span class="avatar-initial bg-label-primary rounded-circle">
                                                                {{ substr($location->vendor->name, 0, 1) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">{{ $location->vendor->name }}</div>
                                                            <small class="text-muted">{{ $location->vendor->email }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Kein Vendor</span>
                                                @endif
                                            </td>
                                            <td>{{ $location->city }}</td>
                                            <td>
                                                @if($location->postal_code)
                                                    <span class="badge bg-label-primary">{{ $location->postal_code }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($location->country)
                                                    @php
                                                        $flagCode = $location->country;
                                                        // to lowercase
                                                        $flagCode = strtolower($flagCode);
                                                    @endphp
                                                    <span class="fi fi-{{ $flagCode }} me-1"></span>{{ $location->country }}
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($location->latitude && $location->longitude)
                                                    <small class="text-muted">{{ number_format($location->latitude, 4) }},
                                                        {{ number_format($location->longitude, 4) }}</small>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php $rentalCount = $location->rentals->count(); @endphp
                                                @if($rentalCount > 0)
                                                    <span class="badge bg-label-info">{{ $rentalCount }}</span>
                                                @else
                                                    <span class="badge bg-label-secondary">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" {{ $location->is_active ? 'checked' : '' }} wire:click="toggleStatus({{ $location->id }})"
                                                        title="{{ $location->is_active ? 'Deaktivieren' : 'Aktivieren' }}">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.locations.edit', $location) }}"
                                                        class="btn btn-outline-primary" title="Bearbeiten">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    @if($location->latitude && $location->longitude)
                                                        <button type="button" class="btn btn-outline-info"
                                                            onclick="viewOnMap({{ $location->latitude }}, {{ $location->longitude }})"
                                                            title="Auf Karte anzeigen">
                                                            <i class="ti ti-map"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-outline-danger"
                                                        wire:click="confirmDelete({{ $location->id }})" title="Löschen">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($locations->hasPages())
                            <div class="card-footer">
                                {{ $locations->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="ti ti-map-pin-off text-muted mb-3" style="font-size: 3rem;"></i>
                            <h6 class="text-muted mb-2">Keine Standorte gefunden</h6>
                            @if($search || $countryFilter || $statusFilter || $vendorFilter)
                                <p class="text-muted mb-3">Keine Standorte entsprechen den aktuellen Filterkriterien.</p>
                                <button wire:click="resetFilters" class="btn btn-outline-primary">
                                    <i class="ti ti-refresh me-1"></i>Filter zurücksetzen
                                </button>
                            @else
                                <p class="text-muted mb-3">Noch keine Standorte vorhanden.</p>
                                <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-1"></i>Ersten Standort erstellen
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-alert-triangle text-warning me-2"></i>Standort löschen
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeDeleteModal"></button>
                    </div>
                    <div class="modal-body">
                        @if($locationToDelete)
                            @php $location = \App\Models\Location::find($locationToDelete); @endphp
                            @if($location)
                                <p>Möchten Sie den Standort <strong>"{{ $location->name }}"</strong> wirklich löschen?</p>

                                @php $rentalCount = $location->rentals()->count(); @endphp
                                @if($rentalCount > 0)
                                    <div class="alert alert-warning">
                                        <i class="ti ti-alert-triangle me-2"></i>
                                        <strong>Achtung:</strong> Dieser Standort hat {{ $rentalCount }} verknüpfte Vermietungen.
                                        Das Löschen ist nicht möglich.
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle me-2"></i>
                                        Dieser Standort hat keine verknüpften Vermietungen und kann sicher gelöscht werden.
                                    </div>
                                @endif
                            @endif
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeDeleteModal">
                            Abbrechen
                        </button>
                        @if($locationToDelete)
                            @php $location = \App\Models\Location::find($locationToDelete); @endphp
                            @if($location && $location->rentals()->count() === 0)
                                <button type="button" class="btn btn-danger" wire:click="deleteLocation">
                                    <i class="ti ti-trash me-1"></i>Endgültig löschen
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <script>
        function viewOnMap(lat, lng) {
            const mapUrl = `https://www.google.com/maps?q=${lat},${lng}&z=15`;
            window.open(mapUrl, '_blank');
        }
    </script>
</div>