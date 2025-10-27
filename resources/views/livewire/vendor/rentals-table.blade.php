<div>
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="card mb-4">
        <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="d-flex gap-2 align-items-center">
                <select class="form-select" wire:model.live="bulkAction" style="min-width: 200px;"
                    @if(count($selected) === 0) disabled @endif>
                    <option value="">Aktion wählen...</option>
                    <option value="activate">✓ Aktivieren</option>
                    <option value="deactivate">✗ Deaktivieren</option>
                    <option value="duplicate">📋 Duplizieren</option>
                    <option value="change_category">📂 Kategorie ändern</option>
                    <option value="change_location">📍 Standort ändern</option>
                    <option value="export">📤 Exportieren</option>
                    <option value="delete">🗑️ Löschen</option>
                </select>

                <button class="btn btn-primary" onclick="handleBulkAction()" @if(count($selected) === 0 || !$bulkAction)
                disabled @endif>
                    <i class="ti ti-check me-1"></i>Ausführen
                </button>

                @if (count($selected) > 0)
                    <span class="badge bg-primary">{{ count($selected) }} ausgewählt</span>
                @endif
            </div>
            <div>
                {{ $rentals->links() }}
            </div>
        </div>
        <!-- Bulk Category Modal -->
        @if ($showBulkCategoryModal)
            <div class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Kategorie für ausgewählte Objekte ändern</h5>
                            <button type="button" class="btn-close" wire:click="closeBulkCategoryModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Neue Kategorie wählen:</label>
                                <select class="form-select" wire:model="bulkCategoryId">
                                    <option value="">Kategorie wählen...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-muted">{{ count($selected) }} Objekt(e) ausgewählt</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                wire:click="closeBulkCategoryModal">Abbrechen</button>
                            <button type="button" class="btn btn-primary" wire:click="bulkChangeCategory">Kategorie
                                ändern</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Bulk Location Modal -->
        @if ($showBulkLocationModal)
            <div class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Standort für ausgewählte Objekte ändern</h5>
                            <button type="button" class="btn-close" wire:click="closeBulkLocationModal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Neuen Standort wählen:</label>
                                <select class="form-select" wire:model="bulkLocationId">
                                    <option value="">Standort wählen...</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-muted">{{ count($selected) }} Objekt(e) ausgewählt</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                wire:click="closeBulkLocationModal">Abbrechen</button>
                            <button type="button" class="btn btn-primary" wire:click="bulkChangeLocation">Standort
                                ändern</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="mb-3 d-flex flex-wrap gap-2 align-items-end justify-content-between">
            <div class="flex-grow-1">
                <label class="form-label mb-0">Suche</label>
                <input type="text" class="form-control" placeholder="Titel suchen..."
                    wire:model.live.debounce.300ms="search">
            </div>
            <div class="flex-grow-1">
                <label class="form-label mb-0">Kategorie</label>
                <select class="form-select" wire:model.live="filterCategory">
                    <option value="">Alle Kategorien</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow-1">
                <label class="form-label mb-0">Standort</label>
                <select class="form-select" wire:model.live="filterLocation">
                    <option value="">Alle Standorte</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-grow-1">
                <label class="form-label mb-0">Status</label>
                <select class="form-select" wire:model.live="filterStatus">
                    <option value="">Alle</option>
                    <option value="active">Aktiv</option>
                    <option value="inactive">Inaktiv</option>
                </select>
            </div>
            <!-- clear filter button -->
            <div>
                <button class="btn btn-secondary" wire:click="clearFilters"><i class="ti ti-refresh"></i></button>
            </div>
        </div>
        </div>
    </div>

    <div class="card mb-4">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th style="width:40px">
                        <input id="selectAllRentals" type="checkbox" wire:model.live="selectAll"
                            class="form-check-input" />
                    </th>
                    <th style="min-width:220px">KATEGORIE</th>
                    <th>ERSTELLT</th>
                    <th>BEARBEITET</th>
                    <th>STATUS</th>
                    <th>AKTIONEN</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rentals as $rental)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input rental-checkbox" value="{{ $rental->id }}"
                                wire:model.live="selected" />
                        </td>
                        <td>
                            <div class="d-flex align-items-start gap-2">
                                <div class="position-relative me-2">
                                    @php
                                        // Use relation method to avoid attribute cast conflict
                                        $firstImage = $rental->images()->first();
                                        $imageUrl = $firstImage && $firstImage->path
                                            ? \Illuminate\Support\Facades\Storage::url($firstImage->path)
                                            : asset('assets/img/placeholder.png');
                                        $imageCount = $rental->images()->count();
                                    @endphp
                                    <img src="{{ $imageUrl }}" alt="Bild" class="rounded"
                                        style="width: 115px; height: 115px; object-fit: cover;">
                                    <span
                                        class="position-absolute bottom-0 start-0 bg-dark text-white rounded px-2 py-1 small"
                                        style="font-size: 0.8em;">
                                        <i class="ti ti-photo"></i> {{ $imageCount }}
                                    </span>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $rental->title }}</div>
                                    <div>
                                        Artikel Nr.: <a
                                            href="{{ route('vendor-rental-edit', ['id' => $rental->id]) }}">{{ $rental->number }}</a>
                                    </div>
                                    <div>Kategorie: {{ $rental->category->name ?? '-' }}</div>
                                    <div>
                                        Standort:
                                        @if($rental->has_multiple_locations ?? false)
                                            <i class="ti ti-map-pin" title="Mehrere Standorte"></i>
                                        @else
                                            <span>{{ $rental->locations->first()->name ?? '-' }}</span>
                                        @endif
                                    </div>
                                                                    <div>
                                    <strong>Preis:</strong> 
                                    <span class="text-primary fw-bold">{{ $rental->price_display }}</span>
                                    @if($rental->price_type)
                                        <br><small class="text-muted">
                                            Typ: 
                                            @switch($rental->price_type)
                                                @case('hour') Stündlich @break
                                                @case('day') Täglich @break  
                                                @case('once') Einmalig @break
                                                @case('fixed') Festpreis @break
                                                @default {{ $rental->price_type }}
                                            @endswitch
                                        </small>
                                    @endif
                                </div>
                                </div>
                            </div>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($rental->created_at)->format('d.m.Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($rental->updated_at)->format('d.m.Y') }}</td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                    wire:click="toggleRentalStatus({{ $rental->id }})" {{ $rental->status == 'active' ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('vendor-rental-edit', ['id' => $rental->id]) }}"
                                    class="btn btn-sm btn-outline-primary" title="Bearbeiten">
                                    <i class="ti ti-edit"></i>
                                </a>
                                <a href="{{ route('vendor-rental-preview', ['id' => $rental->id]) }}"
                                    class="btn btn-sm btn-outline-secondary" title="Vorschau">
                                    <i class="ti ti-eye"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-info" title="Duplizieren"
                                    wire:click="duplicateRental({{ $rental->id }})">
                                    <i class="ti ti-copy"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning"
                                    title="{{ $rental->status === 'active' ? 'Deaktivieren' : 'Aktivieren' }}"
                                    wire:click="toggleRentalStatus({{ $rental->id }})">
                                    <i class="ti ti-power"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="Link kopieren"
                                    onclick="window.navigator.clipboard.writeText('{{ url('/rental') }}/{{ $rental->id }}').then(()=>alert('Link wurde in die Zwischenablage kopiert!'))">
                                    <i class="ti ti-link"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Löschen"
                                    wire:click="deleteRental({{ $rental->id }})">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Keine Vermietungsobjekte gefunden.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Bulk Actions -->
    @if (count($selected) > 0)
        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <span><strong>{{ count($selected) }}</strong> Objekt(e) ausgewählt</span>
            <button class="btn btn-sm btn-outline-secondary" wire:click="resetBulkSelection">
                <i class="ti ti-x"></i> Auswahl aufheben
            </button>
        </div>
    @endif

    <script>
        function handleBulkAction() {
            const action = document.querySelector('select[wire\\:model\\.live="bulkAction"]').value;
            const selectedBadge = document.querySelector('.badge.bg-primary');
            const selectedCount = selectedBadge ? selectedBadge.textContent.match(/\d+/)[0] : 0;

            if (action === 'delete') {
                if (confirm(`⚠️ WARNUNG: Sind Sie sicher, dass Sie ${selectedCount} Objekt(e) PERMANENT löschen möchten?\n\nDiese Aktion kann nicht rückgängig gemacht werden!`)) {
                    @this.call('executeBulkAction');
                }
            } else {
                @this.call('executeBulkAction');
            }
        }
    </script>
</div>