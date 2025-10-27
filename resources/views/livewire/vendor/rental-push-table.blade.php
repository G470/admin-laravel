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

    <!-- Add New Push Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div></div>
        <a href="{{ route('vendor.rental-pushes.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>Neuer Artikel-Push
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label for="search" class="form-label">Suchen</label>
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Artikel suchen...">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="categoryFilter" class="form-label">Kategorie</label>
                    <select wire:model.live="categoryFilter" class="form-select">
                        <option value="">Alle Kategorien</option>
                        @foreach($this->categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="locationFilter" class="form-label">Standort</label>
                    <select wire:model.live="locationFilter" class="form-select">
                        <option value="">Alle Standorte</option>
                        @foreach($this->locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">Alle Status</option>
                        @foreach($this->statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="perPage" class="form-label">Pro Seite</label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end mb-2">
                    <button wire:click="clearFilters" class="btn btn-secondary">
                        <i class="ti ti-refresh"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>
                            <button wire:click="sortBy('rental')" class="btn btn-link p-0 text-start">
                                Artikel
                                @if($sortField === 'rental')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('category')" class="btn btn-link p-0 text-start">
                                Kategorie
                                @if($sortField === 'category')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('location')" class="btn btn-link p-0 text-start">
                                Standort
                                @if($sortField === 'location')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>Frequenz</th>
                        <th>Zeitraum</th>
                        <th>Credits</th>
                        <th>Status</th>
                        <th style="width: 150px;">Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->rentalPushes as $push)
                        <tr>
                            <td>{{ ($this->rentalPushes->currentPage() - 1) * $this->rentalPushes->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-wrapper me-3">
                                        <div class="avatar">
                                            @if($push->rental->images && count($push->rental->images) > 0)
                                                <img src="{{ asset('storage/' . $push->rental->images[0]) }}" alt="Rental Image"
                                                    class="rounded">
                                            @else
                                                <img src="{{asset('assets/img/backgrounds/' . (($loop->iteration % 5) + 1) . '.jpg')}}"
                                                    alt="Default Image" class="rounded">
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $push->rental->title }}</h6>
                                        <small class="text-muted">ID: #{{ $push->rental->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-info">{{ $push->category->name }}</span>
                            </td>
                            <td>
                                <span class="badge bg-label-primary">{{ $push->location->name }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $push->frequency_label }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <small class="text-muted">Von:</small>
                                    <span class="fw-semibold">{{ $push->start_date->format('d.m.Y') }}</span>
                                    <small class="text-muted">Bis:</small>
                                    <span class="fw-semibold">{{ $push->end_date->format('d.m.Y') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span
                                        class="fw-semibold text-primary">{{ $push->credits_used }}/{{ $push->total_credits_needed }}</span>
                                    <div class="progress mt-1" style="height: 4px;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $push->progress_percentage }}%"
                                            aria-valuenow="{{ $push->progress_percentage }}" aria-valuemin="0"
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $push->progress_percentage }}%</small>
                                </div>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'active' => 'success',
                                        'paused' => 'warning',
                                        'completed' => 'info',
                                        'cancelled' => 'danger'
                                    ];
                                    $statusColor = $statusColors[$push->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-label-{{ $statusColor }}">{{ $push->status_label }}</span>
                                @if($push->status === 'active' && $push->next_push_at)
                                    <br><small class="text-muted">Nächster Push: {{ $push->time_until_next_push }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('vendor.rental-pushes.show', $push->id) }}"
                                        class="btn btn-sm btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Anzeigen">
                                        <i class="ti ti-eye text-primary"></i>
                                    </a>
                                    <a href="{{ route('vendor.rental-pushes.edit', $push->id) }}"
                                        class="btn btn-sm btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Bearbeiten">
                                        <i class="ti ti-edit text-primary"></i>
                                    </a>
                                    @if($push->status === 'active' || $push->status === 'paused')
                                        <button wire:click="toggleStatus({{ $push->id }})" class="btn btn-sm btn-icon"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="{{ $push->status === 'active' ? 'Pausieren' : 'Aktivieren' }}">
                                            <i
                                                class="ti ti-{{ $push->status === 'active' ? 'pause' : 'play' }} text-warning"></i>
                                        </button>
                                    @endif
                                    @if($push->status !== 'cancelled')
                                        <button wire:click="deletePush({{ $push->id }})"
                                            wire:confirm="Sind Sie sicher, dass Sie diesen Artikel-Push abbrechen möchten? Diese Aktion kann nicht rückgängig gemacht werden."
                                            class="btn btn-sm btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Abbrechen">
                                            <i class="ti ti-trash text-danger"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="d-flex flex-column justify-content-center align-items-center">
                                    <i class="ti ti-rocket text-muted mb-3" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted">Keine Artikel-Pushes gefunden</h5>
                                    @if($this->search || $this->categoryFilter || $this->locationFilter || $this->statusFilter)
                                        <p class="text-muted mb-3">Keine Pushes entsprechen den aktuellen Filterkriterien.</p>
                                        <button wire:click="clearFilters" class="btn btn-primary">
                                            <i class="ti ti-refresh me-1"></i>Filter zurücksetzen
                                        </button>
                                    @else
                                        <p class="text-muted mb-3">Sie haben noch keine Artikel-Pushes erstellt.</p>
                                        <a href="{{ route('vendor.rental-pushes.create') }}" class="btn btn-primary">
                                            <i class="ti ti-plus me-1"></i>Ersten Push erstellen
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
        @if($this->rentalPushes->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Zeige {{ $this->rentalPushes->firstItem() }} bis {{ $this->rentalPushes->lastItem() }} von
                            {{ $this->rentalPushes->total() }} Einträgen
                        </small>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $this->rentalPushes->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>