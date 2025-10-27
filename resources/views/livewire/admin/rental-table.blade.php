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

    <!-- Add New Rental Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div></div>
        <a href="{{ route('admin.rentals.create') }}" class="btn btn-primary">
            <i class="ti ti-plus me-1"></i>Neues Vermietungsobjekt
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label for="search" class="form-label">Suchen</label>
                    <input type="text" wire:model.live="search" class="form-control" placeholder="Name suchen...">
                </div>
                <div class="col-md-2 mb-2">
                    <label for="vendorFilter" class="form-label">Anbieter</label>
                    <select wire:model.live="vendorFilter" class="form-select">
                        <option value="">Alle Anbieter</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->name }}">{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="categoryFilter" class="form-label">Kategorie</label>
                    <select wire:model.live="categoryFilter" class="form-select">
                        <option value="">Alle Kategorien</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->name }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">Alle Status</option>
                        @foreach($statusOptions as $value => $label)
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
                            <button wire:click="sortBy('name')" class="btn btn-link p-0 text-start">
                                Name 
                                @if($sortField === 'name')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('vendor_id')" class="btn btn-link p-0 text-start">
                                Anbieter
                                @if($sortField === 'vendor_id')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('category_id')" class="btn btn-link p-0 text-start">
                                Kategorie
                                @if($sortField === 'category_id')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>Standort</th>
                        <th>
                            <button wire:click="sortBy('price')" class="btn btn-link p-0 text-start">
                                Preis (pro Stunde)
                                @if($sortField === 'price')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('status')" class="btn btn-link p-0 text-start">
                                Status
                                @if($sortField === 'status')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th style="width: 150px;">Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rentals as $rental)
                        <tr>
                            <td>{{ ($rentals->currentPage() - 1) * $rentals->perPage() + $loop->iteration }}</td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="avatar-wrapper">
                                        <div class="avatar me-2">
                                           
                                            @if($rental->images && count($rental->images) > 0)
                                                <img src="{{ asset('storage/' . $rental->images[0]) }}" alt="Rental Image" class="rounded">
                                            @else
                                                <img src="{{asset('assets/img/backgrounds/' . (($loop->iteration % 5) + 1) . '.jpg')}}" alt="Default Image" class="rounded">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        @if($editingRentalId === $rental->id && $editingField === 'name')
                                            <div class="d-flex align-items-center">
                                                <input type="text" wire:model="editingValue" class="form-control form-control-sm me-2" style="width: 200px;">
                                                <button wire:click="saveEdit" class="btn btn-sm btn-success me-1">
                                                    <i class="ti ti-check"></i>
                                                </button>
                                                <button wire:click="cancelEditing" class="btn btn-sm btn-secondary">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            </div>
                                        @else
                                            <a href="javascript:void(0);" 
                                               wire:click="startEditing({{ $rental->id }}, 'name', '{{ $rental->title }}')"
                                               class="text-body text-truncate fw-semibold"
                                               style="cursor: pointer;"
                                               data-bs-toggle="tooltip" 
                                               data-bs-placement="top" 
                                               title="Klicken zum Bearbeiten">
                                                {{ $rental->title }}
                                            </a>
                                        @endif
                                        <small class="text-muted">ID: #{{ $rental->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($editingRentalId === $rental->id && $editingField === 'vendor_id')
                                    <div class="d-flex align-items-center">
                                        <select wire:model="editingValue" class="form-select form-select-sm me-2" style="width: 150px;">
                                            @foreach($vendors as $vendor)
                                                <option value="{{ $vendor->id }}" {{ $editingValue == $vendor->id ? 'selected' : '' }}>
                                                    {{ $vendor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button wire:click="saveEdit" class="btn btn-sm btn-success me-1">
                                            <i class="ti ti-check"></i>
                                        </button>
                                        <button wire:click="cancelEditing" class="btn btn-sm btn-secondary">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                @else
                                    <a href="javascript:void(0);" 
                                       wire:click="startEditing({{ $rental->id }}, 'vendor_id', '{{ $rental->vendor_id }}')"
                                       style="cursor: pointer;"
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="Klicken zum Bearbeiten">
                                        {{ $rental->vendor ? $rental->vendor->name : '-' }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if($editingRentalId === $rental->id && $editingField === 'category_id')
                                    <div class="d-flex align-items-center">
                                        <select wire:model="editingValue" class="form-select form-select-sm me-2" style="width: 150px;">
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $editingValue == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button wire:click="saveEdit" class="btn btn-sm btn-success me-1">
                                            <i class="ti ti-check"></i>
                                        </button>
                                        <button wire:click="cancelEditing" class="btn btn-sm btn-secondary">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                @else
                                    <a href="javascript:void(0);" 
                                       wire:click="startEditing({{ $rental->id }}, 'category_id', '{{ $rental->category_id }}')"
                                       style="cursor: pointer;"
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="Klicken zum Bearbeiten">
                                        {{ $rental->category ? $rental->category->name : '-' }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if($rental->city)
                                    {{ $rental->city->city }}
                                @elseif($rental->address)
                                    {{ Str::limit($rental->address, 50) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($editingRentalId === $rental->id && $editingField === 'price')
                                    <div class="d-flex align-items-center">
                                        <input type="number" step="0.01" wire:model="editingValue" class="form-control form-control-sm me-2" style="width: 100px;">
                                        <span class="me-2">{{ $rental->currency ?? '€' }}</span>
                                        <button wire:click="saveEdit" class="btn btn-sm btn-success me-1">
                                            <i class="ti ti-check"></i>
                                        </button>
                                        <button wire:click="cancelEditing" class="btn btn-sm btn-secondary">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                @else
                                    <a href="javascript:void(0);" 
                                       wire:click="startEditing({{ $rental->id }}, 'price', '{{ $rental->price_range_hour }}')"
                                       style="cursor: pointer;"
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="Klicken zum Bearbeiten">
                                        {{ number_format($rental->price_range_hour, 2) }} {{ $rental->currency ?? '€' }}
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if($editingRentalId === $rental->id && $editingField === 'status')
                                    <div class="d-flex align-items-center">
                                        <select wire:model="editingValue" class="form-select form-select-sm me-2" style="width: 150px;">
                                            @foreach($statusOptions as $value => $label)
                                                <option value="{{ $value }}" {{ $editingValue == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button wire:click="saveEdit" class="btn btn-sm btn-success me-1">
                                            <i class="ti ti-check"></i>
                                        </button>
                                        <button wire:click="cancelEditing" class="btn btn-sm btn-secondary">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                @else
                                    @php
                                        $statusLabels = [
                                            'active' => ['label' => 'Aktiv', 'color' => 'success'],
                                            'inactive' => ['label' => 'Inaktiv', 'color' => 'secondary'],
                                            'pending' => ['label' => 'Prüfung ausstehend', 'color' => 'warning'],
                                            'rejected' => ['label' => 'Abgelehnt', 'color' => 'danger']
                                        ];
                                        $statusInfo = $statusLabels[$rental->status] ?? ['label' => $rental->status, 'color' => 'secondary'];
                                    @endphp
                                    <button wire:click="startEditing({{ $rental->id }}, 'status', '{{ $rental->status }}')" 
                                            class="badge bg-label-{{ $statusInfo['color'] }} border-0" 
                                            style="cursor: pointer;"
                                            data-bs-toggle="tooltip" 
                                            data-bs-placement="top" 
                                            title="Status bearbeiten">
                                        {{ $statusInfo['label'] }}
                                    </button>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('admin.rentals.show', $rental->id) }}" 
                                       class="btn btn-sm btn-icon" 
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="Anzeigen">
                                        <i class="ti ti-eye text-primary"></i>
                                    </a>
                                    <a href="{{ route('admin.rentals.edit', $rental->id) }}" 
                                       class="btn btn-sm btn-icon" 
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top" 
                                       title="Bearbeiten">
                                        <i class="ti ti-edit text-primary"></i>
                                    </a>
                                    <button wire:click="toggleFeatured({{ $rental->id }})" 
                                            class="btn btn-sm btn-icon" 
                                            data-bs-toggle="tooltip" 
                                            data-bs-placement="top" 
                                            title="{{ $rental->featured ? 'Von Featured entfernen' : 'Als Featured markieren' }}">
                                        <i class="ti ti-star{{ $rental->featured ? '-filled' : '' }} text-warning"></i>
                                    </button>
                                    <button wire:click="deleteRental({{ $rental->id }})" 
                                            wire:confirm="Sind Sie sicher, dass Sie das Vermietungsobjekt '{{ $rental->name }}' löschen möchten? Diese Aktion kann nicht rückgängig gemacht werden."
                                            class="btn btn-sm btn-icon" 
                                            data-bs-toggle="tooltip" 
                                            data-bs-placement="top" 
                                            title="Löschen">
                                        <i class="ti ti-trash text-danger"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="d-flex flex-column justify-content-center align-items-center">
                                    <i class="ti ti-home-2 text-muted mb-3" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted">Keine Vermietungsobjekte gefunden</h5>
                                    @if($search || $vendorFilter || $categoryFilter || $statusFilter)
                                        <p class="text-muted mb-3">Keine Objekte entsprechen den aktuellen Filterkriterien.</p>
                                        <button wire:click="clearFilters" class="btn btn-primary">
                                            <i class="ti ti-refresh me-1"></i>Filter zurücksetzen
                                        </button>
                                    @else
                                        <p class="text-muted mb-3">Es sind noch keine Vermietungsobjekte in der Datenbank vorhanden.</p>
                                        <a href="{{ route('admin.rentals.create') }}" class="btn btn-primary">
                                            <i class="ti ti-plus me-1"></i>Erstes Objekt erstellen
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
        @if($rentals->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Zeige {{ $rentals->firstItem() }} bis {{ $rentals->lastItem() }} von {{ $rentals->total() }} Einträgen
                        </small>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $rentals->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Edit Modal -->
    @if($showEditModal && $editRental)
        <div class="modal fade show" style="display: block;" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Vermietungsobjekt bearbeiten: {{ $editRental->name }}</h5>
                        <button type="button" class="btn-close" wire:click="closeEditModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="updateRental">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label">Name *</label>
                                    <input type="text" wire:model="editRental.title" class="form-control" required>
                                    @error('editRental.title') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="price_range_hour" class="form-label">Preis (pro Stunde) *</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" wire:model="editRental.price_range_hour" class="form-control" required>
                                        <span class="input-group-text">{{ $editRental->currency ?? '€' }}</span>
                                    </div>
                                    @error('editRental.price_range_hour') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="vendor_id" class="form-label">Anbieter *</label>
                                    <select wire:model="editRental.vendor_id" class="form-select" required>
                                        <option value="">Anbieter auswählen</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ $editRental->vendor_id == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('editRental.vendor_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Kategorie *</label>
                                    <select wire:model="editRental.category_id" class="form-select" required>
                                        <option value="">Kategorie auswählen</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $editRental->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('editRental.category_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select wire:model="editRental.status" class="form-select" required>
                                        @foreach($statusOptions as $value => $label)
                                            <option value="{{ $value }}" {{ $editRental->status == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('editRental.status') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="price_range_day" class="form-label">Preis (pro Tag)</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" wire:model="editRental.price_range_day" class="form-control">
                                        <span class="input-group-text">{{ $editRental->currency ?? '€' }}</span>
                                    </div>
                                    @error('editRental.price_range_day') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Beschreibung</label>
                                <textarea wire:model="editRental.description" class="form-control" rows="3"></textarea>
                                @error('editRental.description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeEditModal">Abbrechen</button>
                        <button type="button" class="btn btn-primary" wire:click="updateRental">Speichern</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
