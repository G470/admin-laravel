<div>
    <!-- Search and Filter Controls -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i class="ti ti-search"></i></span>
                <input type="text" class="form-control" placeholder="Nach Name oder Code suchen..."
                    wire:model.live="search">
            </div>
        </div>
        <div class="col-md-3">
            <select class="form-select" wire:model.live="statusFilter">
                <option value="">Alle Status</option>
                <option value="1">Aktiv</option>
                <option value="0">Inaktiv</option>
            </select>
        </div>
        <div class="col-md-3 text-end">
            <button type="button" class="btn btn-primary" wire:click="openCreateModal">
                <i class="ti ti-plus me-1"></i>
                Neues Land
            </button>
        </div>
    </div>

    <!-- Countries Table -->
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>
                        <button wire:click="sortBy('name')" class="btn btn-link p-0 text-start">
                            Name
                            @if($sortField === 'name')
                                <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </button>
                    </th>
                    <th>
                        <button wire:click="sortBy('code')" class="btn btn-link p-0 text-start">
                            Code
                            @if($sortField === 'code')
                                <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                            @endif
                        </button>
                    </th>
                    <th>Telefonvorwahl</th>
                    <th>Status</th>
                    <th>Standorte</th>
                    <th>PLZ-Daten</th>
                    <th>Erstellt</th>
                    <th class="text-end">Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($countries as $country)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if(file_exists(public_path('assets/img/flags/' . strtolower($country->code) . '.svg')))
                                    <img src="{{ asset('assets/img/flags/' . strtolower($country->code) . '.svg') }}"
                                        alt="{{ $country->name }}" class="me-2" style="width: 24px; height: 18px;">
                                @else
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial bg-label-secondary rounded">
                                            {{ strtoupper(substr($country->code, 0, 2)) }}
                                        </span>
                                    </div>
                                @endif
                                <span class="fw-medium">{{ $country->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-dark">{{ $country->code }}</span>
                        </td>
                        <td>
                            @if($country->phone_code)
                                <span class="text-muted">{{ $country->phone_code }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <button wire:click="toggleStatus({{ $country->id }})"
                                class="btn btn-sm btn-outline-{{ $country->is_active ? 'success' : 'secondary' }}">
                                @if($country->is_active)
                                    <i class="ti ti-check me-1"></i>Aktiv
                                @else
                                    <i class="ti ti-x me-1"></i>Inaktiv
                                @endif
                            </button>
                        </td>
                        <td>
                            <span
                                class="badge bg-label-info">{{ $country->locations_count ?? $country->locations()->count() }}</span>
                        </td>
                        <td>
                            @php
                                $stats = (new \App\Services\CountryDataImportService())->getImportStats($country);
                            @endphp
                            @if($stats['table_exists'])
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-database text-success me-1"></i>
                                    <span class="badge bg-label-success">{{ number_format($stats['total_records']) }}</span>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $country->created_at->format('d.m.Y') }}</small>
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);"
                                            wire:click="openEditModal({{ $country->id }})">
                                            <i class="ti ti-edit me-2"></i>Bearbeiten
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);"
                                            wire:click="openImportModal({{ $country->id }})">
                                            <i class="ti ti-upload me-2"></i>Daten importieren
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);"
                                            wire:click="viewCountryData({{ $country->id }})">
                                            <i class="ti ti-eye me-2"></i>Daten anzeigen
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0);"
                                            wire:click="exportCountryData({{ $country->id }})">
                                            <i class="ti ti-download me-2"></i>Daten exportieren
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-warning" href="javascript:void(0);"
                                            wire:click="clearCountryData({{ $country->id }})"
                                            onclick="return confirm('Sind Sie sicher, dass Sie alle Postleitzahlen-Daten für dieses Land löschen möchten?')">
                                            <i class="ti ti-trash-x me-2"></i>Daten löschen
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="javascript:void(0);"
                                            wire:click="delete({{ $country->id }})"
                                            onclick="return confirm('Sind Sie sicher, dass Sie dieses Land löschen möchten?')">
                                            <i class="ti ti-trash me-2"></i>Land löschen
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center">
                                <i class="ti ti-world-off text-muted mb-2" style="font-size: 2rem;"></i>
                                <span class="text-muted">Keine Länder gefunden</span>
                                @if($search)
                                    <small class="text-muted mt-1">Suchbegriff: "{{ $search }}"</small>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($countries->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $countries->links() }}
        </div>
    @endif

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @if($editMode)
                                <i class="ti ti-edit me-2"></i>Land bearbeiten
                            @else
                                <i class="ti ti-plus me-2"></i>Neues Land erstellen
                            @endif
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="name" class="form-label">Ländername *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                        wire:model="name" placeholder="z.B. Deutschland">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="code" class="form-label">Ländercode *</label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                                        wire:model="code" placeholder="DE" maxlength="2" style="text-transform: uppercase;">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">ISO 3166-1 Alpha-2 (2 Zeichen)</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone_code" class="form-label">Telefonvorwahl</label>
                                    <input type="text" class="form-control @error('phone_code') is-invalid @enderror"
                                        id="phone_code" wire:model="phone_code" placeholder="+49">
                                    @error('phone_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Optional, z.B. +49, +43, +41</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="is_active"
                                            wire:model="is_active">
                                        <label class="form-check-label" for="is_active">
                                            Land ist aktiv
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">Inaktive Länder werden in Formularen nicht
                                        angezeigt</small>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">
                            Abbrechen
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="save">
                            @if($editMode)
                                <i class="ti ti-device-floppy me-1"></i>Aktualisieren
                            @else
                                <i class="ti ti-plus me-1"></i>Erstellen
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Import Modal -->
    @if($showImportModal && $selectedCountry)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-upload me-2"></i>Daten Import - {{ $selectedCountry['name'] }}
                            ({{ $selectedCountry['code'] }})
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeImportModal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Import Statistics -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">
                                            <i class="ti ti-chart-bar me-2"></i>Aktuelle Statistiken
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-initial bg-label-primary rounded">
                                                            <i class="ti ti-database"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">Datensätze</small>
                                                        <h6 class="mb-0">
                                                            {{ number_format($importStats['total_records'] ?? 0) }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-initial bg-label-info rounded">
                                                            <i class="ti ti-map-pin"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">Städte</small>
                                                        <h6 class="mb-0">
                                                            {{ number_format($importStats['unique_cities'] ?? 0) }}</h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-initial bg-label-success rounded">
                                                            <i class="ti ti-location"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">Mit Koordinaten</small>
                                                        <h6 class="mb-0">
                                                            {{ number_format($importStats['records_with_coordinates'] ?? 0) }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-initial bg-label-warning rounded">
                                                            <i class="ti ti-users"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">Mit Bevölkerung</small>
                                                        <h6 class="mb-0">
                                                            {{ number_format($importStats['records_with_population'] ?? 0) }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Import Form -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="ti ti-file-upload me-2"></i>Neue Daten importieren
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="import-form-{{ $selectedCountry['id'] }}">
                                            <!-- This will be populated by JavaScript -->
                                            <div class="text-center py-4">
                                                <i class="ti ti-upload text-muted mb-3" style="font-size: 3rem;"></i>
                                                <p class="text-muted">Import-Interface wird geladen...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Format Guidelines -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="ti ti-info-circle me-2"></i>Dateformat-Richtlinien
                                    </h6>
                                    <ul class="mb-0 small">
                                        <li><strong>Unterstützte Formate:</strong> CSV, XLSX, XLS</li>
                                        <li><strong>Erforderliche Spalten:</strong> postal_code, city</li>
                                        <li><strong>Optionale Spalten:</strong> sub_city, region, latitude, longitude,
                                            population</li>
                                        <li><strong>Maximale Dateigröße:</strong> 50 MB</li>
                                        <li><strong>Encoding:</strong> UTF-8 empfohlen</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeImportModal">
                            Schließen
                        </button>
                        <a href="{{ route('admin.countries.import', $selectedCountry['id']) }}" class="btn btn-primary">
                            <i class="ti ti-external-link me-1"></i>Erweiterte Import-Seite
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Data View Modal -->
    @if($showDataModal && $selectedCountry)
        <div class="modal fade show" style="display: block;" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-eye me-2"></i>Daten Übersicht - {{ $selectedCountry['name'] }}
                            ({{ $selectedCountry['code'] }})
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeDataModal"></button>
                    </div>
                    <div class="modal-body">
                        @if($importStats['table_exists'] ?? false)
                            <!-- Statistics Row -->
                            <div class="row mb-4">
                                <div class="col-md-2">
                                    <div class="card text-center">
                                        <div class="card-body py-3">
                                            <i class="ti ti-database text-primary mb-2" style="font-size: 1.5rem;"></i>
                                            <h5 class="mb-0">{{ number_format($importStats['total_records']) }}</h5>
                                            <small class="text-muted">Datensätze</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card text-center">
                                        <div class="card-body py-3">
                                            <i class="ti ti-map-pin text-info mb-2" style="font-size: 1.5rem;"></i>
                                            <h5 class="mb-0">{{ number_format($importStats['unique_cities']) }}</h5>
                                            <small class="text-muted">Städte</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card text-center">
                                        <div class="card-body py-3">
                                            <i class="ti ti-world text-success mb-2" style="font-size: 1.5rem;"></i>
                                            <h5 class="mb-0">{{ number_format($importStats['unique_regions']) }}</h5>
                                            <small class="text-muted">Regionen</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card text-center">
                                        <div class="card-body py-3">
                                            <i class="ti ti-location text-warning mb-2" style="font-size: 1.5rem;"></i>
                                            <h5 class="mb-0">{{ number_format($importStats['records_with_coordinates']) }}</h5>
                                            <small class="text-muted">Mit GPS</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card text-center">
                                        <div class="card-body py-3">
                                            <i class="ti ti-users text-danger mb-2" style="font-size: 1.5rem;"></i>
                                            <h5 class="mb-0">{{ number_format($importStats['records_with_population']) }}</h5>
                                            <small class="text-muted">Mit Bevölkerung</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="card text-center">
                                        <div class="card-body py-3">
                                            <i class="ti ti-clock text-secondary mb-2" style="font-size: 1.5rem;"></i>
                                            <h6 class="mb-0 small">
                                                {{ $importStats['last_import'] ? \Carbon\Carbon::parse($importStats['last_import'])->format('d.m.Y') : 'Nie' }}
                                            </h6>
                                            <small class="text-muted">Letzter Import</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sample Data Table -->
                            @if(!empty($countryData))
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>PLZ</th>
                                                <th>Stadt</th>
                                                <th>Teilstadt</th>
                                                <th>Region</th>
                                                <th>Koordinaten</th>
                                                <th>Bevölkerung</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($countryData as $row)
                                                <tr>
                                                    <td><span class="badge bg-label-dark">{{ $row['postal_code'] }}</span></td>
                                                    <td>{{ $row['city'] }}</td>
                                                    <td>{{ $row['sub_city'] ?? '—' }}</td>
                                                    <td>{{ $row['region'] ?? '—' }}</td>
                                                    <td>
                                                        @if($row['latitude'] && $row['longitude'])
                                                            <small class="text-success">
                                                                <i class="ti ti-location me-1"></i>
                                                                {{ number_format($row['latitude'], 4) }},
                                                                {{ number_format($row['longitude'], 4) }}
                                                            </small>
                                                        @else
                                                            <small class="text-muted">—</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($row['population'])
                                                            {{ number_format($row['population']) }}
                                                        @else
                                                            <small class="text-muted">—</small>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <p class="text-muted text-center mt-3">
                                    <small>Zeigt die 10 größten Städte nach Bevölkerung</small>
                                </p>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <i class="ti ti-database-off text-muted mb-3" style="font-size: 4rem;"></i>
                                <h5 class="text-muted">Keine Daten vorhanden</h5>
                                <p class="text-muted">Für dieses Land wurden noch keine Postleitzahlen-Daten importiert.</p>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeDataModal">
                            Schließen
                        </button>
                        @if($importStats['table_exists'] ?? false)
                            <a href="{{ route('admin.countries.data.view', $selectedCountry['id']) }}" class="btn btn-info">
                                <i class="ti ti-table me-1"></i>Vollständige Tabelle
                            </a>
                            <button type="button" class="btn btn-success"
                                wire:click="exportCountryData({{ $selectedCountry['id'] }})">
                                <i class="ti ti-download me-1"></i>Exportieren
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif

    <!-- Loading State -->
    <div wire:loading class="position-fixed top-50 start-50 translate-middle" style="z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Laden...</span>
        </div>
    </div>
</div>