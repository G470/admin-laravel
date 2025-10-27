<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Location SEO Management</h5>
                    <button class="btn btn-primary" wire:click="showCreateModal">
                        <i class="ti ti-plus me-1"></i>Neue Location hinzufügen
                    </button>
                </div>
                
                <!-- Filters -->
                <div class="card-body border-bottom">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" 
                                   class="form-control" 
                                   placeholder="Stadt oder Slug suchen..."
                                   wire:model.live.debounce.500ms="search">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="statusFilter">
                                <option value="">Alle Status</option>
                                @foreach($statusOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" wire:model.live="countryFilter">
                                <option value="">Alle Länder</option>
                                @foreach($countries as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" wire:model.live="categoryFilter">
                                <option value="">Alle Kategorien</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-secondary w-100" wire:click="$refresh">
                                <i class="ti ti-refresh me-1"></i>Aktualisieren
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover" id="sortable-table">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Location</th>
                                <th>Slug</th>
                                <th>Kategorie</th>
                                <th>Land</th>
                                <th>Status</th>
                                <th width="200">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-body">
                            @forelse($cities as $city)
                                <tr data-id="{{ $city->id }}">
                                    <td>{{ $city->id }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $city->name ?: $city->city }}</strong>
                                            @if($city->state)
                                                <br><small class="text-muted">{{ $city->state }}</small>
                                            @endif
                                            @if($city->population)
                                                <br><small class="text-muted">{{ number_format($city->population) }} Einwohner</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <code>{{ $city->slug }}</code>
                                    </td>
                                    <td>
                                        @if($city->category)
                                            <span class="badge bg-label-info">{{ $city->category->name }}</span>
                                        @else
                                            <span class="text-muted">Alle Kategorien</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('assets/img/flags/' . strtolower($city->country) . '.svg') }}" 
                                                 alt="{{ $city->country }}" 
                                                 style="width: 20px; height: 15px; margin-right: 8px;">
                                            {{ $countries[$city->country] ?? $city->country }}
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm {{ $city->status == 'online' ? 'btn-success' : 'btn-secondary' }}"
                                                wire:click="toggleStatus({{ $city->id }})">
                                            {{ ucfirst($city->status) }}
                                        </button>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary"
                                                    wire:click="showEditModal({{ $city->id }})"
                                                    title="Bearbeiten">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    wire:click="confirmDelete({{ $city->id }})"
                                                    title="Löschen">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="ti ti-map-pin fs-1"></i>
                                            <p class="mt-2">Keine Locations gefunden.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($cities->hasPages())
                    <div class="card-footer">
                        {{ $cities->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $editMode ? 'Location bearbeiten' : 'Neue Location erstellen' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                    </div>
                    <form wire:submit.prevent="saveCity">
                        <div class="modal-body">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Stadt/Ort *</label>
                                    <input type="text" 
                                           class="form-control @error('city') is-invalid @enderror" 
                                           wire:model.live="city"
                                           placeholder="z.B. Berlin">
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bundesland/Staat</label>
                                    <input type="text" 
                                           class="form-control @error('state') is-invalid @enderror" 
                                           wire:model="state"
                                           placeholder="z.B. Berlin">
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Land *</label>
                                    <select class="form-select @error('country') is-invalid @enderror" 
                                            wire:model.live="country">
                                        @foreach($countries as $code => $name)
                                            <option value="{{ $code }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kategorie</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" 
                                            wire:model="category_id">
                                        <option value="">Alle Kategorien</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- SEO Information -->
                                <div class="col-12 mb-3">
                                    <label class="form-label">Display Name</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           wire:model="name"
                                           placeholder="Wird automatisch generiert">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Slug (URL)</label>
                                    <input type="text" 
                                           class="form-control @error('slug') is-invalid @enderror" 
                                           wire:model="slug"
                                           placeholder="wird-automatisch-generiert">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Meta Title</label>
                                    <input type="text" 
                                           class="form-control @error('meta_title') is-invalid @enderror" 
                                           wire:model="meta_title"
                                           placeholder="SEO Titel für Suchmaschinen">
                                    @error('meta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Meta Description</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                              wire:model="meta_description"
                                              rows="3"
                                              placeholder="SEO Beschreibung für Suchmaschinen (max. 160 Zeichen)"></textarea>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">SEO Content</label>
                                    <textarea class="form-control @error('content') is-invalid @enderror" 
                                              wire:model="content"
                                              rows="5"
                                              placeholder="SEO-optimierter Content für diese Location"></textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Additional Data -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Latitude</label>
                                    <input type="number" 
                                           class="form-control @error('latitude') is-invalid @enderror" 
                                           wire:model="latitude"
                                           step="0.000001"
                                           placeholder="52.520008">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Longitude</label>
                                    <input type="number" 
                                           class="form-control @error('longitude') is-invalid @enderror" 
                                           wire:model="longitude"
                                           step="0.000001"
                                           placeholder="13.404954">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Einwohner</label>
                                    <input type="number" 
                                           class="form-control @error('population') is-invalid @enderror" 
                                           wire:model="population"
                                           placeholder="3748148">
                                    @error('population')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               wire:model="status" 
                                               value="online"
                                               id="statusSwitch" 
                                               @if($status == 'online') checked @endif>
                                        <label class="form-check-label" for="statusSwitch">
                                            Online (sichtbar für Besucher)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" 
                                    class="btn btn-outline-secondary" 
                                    wire:click="$set('showModal', false)">
                                Abbrechen
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>
                                {{ $editMode ? 'Aktualisieren' : 'Erstellen' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- JavaScript for enhanced functionality -->
@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function () {
        // Listen for events
        Livewire.on('city-created', (event) => {
            toastr.success(event.message || 'Location wurde erfolgreich erstellt!');
        });

        Livewire.on('city-updated', (event) => {
            toastr.success(event.message || 'Location wurde erfolgreich aktualisiert!');
        });

        Livewire.on('city-deleted', (event) => {
            toastr.success(event.message || 'Location wurde erfolgreich gelöscht!');
        });

        Livewire.on('status-updated', (event) => {
            toastr.success(event.message || 'Status wurde erfolgreich geändert!');
        });

        Livewire.on('delete-error', (event) => {
            toastr.error(event.message || 'Fehler beim Löschen!');
        });

        // Confirm delete dialog
        Livewire.on('confirm-delete', (event) => {
            if (confirm('Soll diese Location wirklich gelöscht werden? Diese Aktion kann nicht rückgängig gemacht werden.')) {
                Livewire.dispatch('deleteCity', event.id);
            }
        });

        // Initialize sortable (if jQuery UI is available)
        if (typeof $ !== 'undefined' && $.ui) {
            $('#sortable-body').sortable({
                handle: 'tr',
                update: function (event, ui) {
                    let orderedIds = $(this).sortable('toArray', { attribute: 'data-id' });
                    Livewire.dispatch('updateSortOrder', orderedIds);
                }
            });
        }
    });
</script>
@endpush