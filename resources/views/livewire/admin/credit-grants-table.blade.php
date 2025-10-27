<div>
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Suchen</label>
                    <input type="text" wire:model.live="search" class="form-control"
                        placeholder="Vendor, Admin oder Grund...">
                </div>
                <div class="col-md-2">
                    <label for="vendorFilter" class="form-label">Vendor</label>
                    <select wire:model.live="vendorFilter" class="form-select">
                        <option value="">Alle Vendors</option>
                        @foreach($this->vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="adminFilter" class="form-label">Admin</label>
                    <select wire:model.live="adminFilter" class="form-select">
                        <option value="">Alle Admins</option>
                        @foreach($this->admins as $admin)
                            <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="grantTypeFilter" class="form-label">Vergabe-Typ</label>
                    <select wire:model.live="grantTypeFilter" class="form-select">
                        <option value="">Alle Typen</option>
                        @foreach($this->grantTypeOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="statusFilter" class="form-label">Status</label>
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">Alle Status</option>
                        @foreach($this->statusOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button wire:click="clearFilters" class="btn btn-secondary">
                        <i class="ti ti-refresh"></i>
                    </button>
                </div>
            </div>

            <!-- Date Range Filters -->
            <div class="row g-3 mt-2">
                <div class="col-md-2">
                    <label for="dateFrom" class="form-label">Von Datum</label>
                    <input type="date" wire:model.live="dateFrom" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="dateTo" class="form-label">Bis Datum</label>
                    <input type="date" wire:model.live="dateTo" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="perPage" class="form-label">Pro Seite</label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
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
                            <button wire:click="sortBy('vendor')" class="btn btn-link p-0 text-start">
                                Vendor
                                @if($sortField === 'vendor')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('admin')" class="btn btn-link p-0 text-start">
                                Admin
                                @if($sortField === 'admin')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('credits_granted')" class="btn btn-link p-0 text-start">
                                Credits
                                @if($sortField === 'credits_granted')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortBy('grant_type')" class="btn btn-link p-0 text-start">
                                Typ
                                @if($sortField === 'grant_type')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>Grund</th>
                        <th>
                            <button wire:click="sortBy('grant_date')" class="btn btn-link p-0 text-start">
                                Vergabe-Datum
                                @if($sortField === 'grant_date')
                                    <i class="ti ti-chevron-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </button>
                        </th>
                        <th>Status</th>
                        <th style="width: 150px;">Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->creditGrants as $grant)
                        <tr>
                            <td>{{ ($this->creditGrants->currentPage() - 1) * $this->creditGrants->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-wrapper me-3">
                                        <div class="avatar">
                                            <img src="{{ asset('assets/img/avatars/default.png') }}" alt="Vendor Avatar"
                                                class="rounded">
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $grant->vendor->name }}</h6>
                                        <small class="text-muted">{{ $grant->vendor->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $grant->admin->name }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fw-semibold text-primary">{{ $grant->credits_granted }}</span>
                                    <small class="text-muted ms-1">Credits</small>
                                </div>
                                @if($grant->formatted_monetary_value)
                                    <small class="text-muted">{{ $grant->formatted_monetary_value }}</small>
                                @endif
                            </td>
                            <td>
                                <span
                                    class="badge bg-label-{{ $grant->grant_type_color }}">{{ $grant->grant_type_label }}</span>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $grant->reason }}">
                                    {{ $grant->reason }}
                                </div>
                                @if($grant->internal_note)
                                    <small class="text-muted d-block">📝 {{ $grant->internal_note}}</small>
                                @endif
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $grant->formatted_grant_date }}</span>
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $grant->status_color }}">{{ $grant->status_label }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('admin.credit-grants.show', $grant->id) }}"
                                        class="btn btn-sm btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                        title="Anzeigen">
                                        <i class="ti ti-eye text-primary"></i>
                                    </a>
                                    @if($grant->canBeEdited())
                                        <a href="{{ route('admin.credit-grants.edit', $grant->id) }}"
                                            class="btn btn-sm btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Bearbeiten">
                                            <i class="ti ti-edit text-primary"></i>
                                        </a>
                                    @endif
                                    @if($grant->canBeDeleted())
                                        <button wire:click="deleteGrant({{ $grant->id }})"
                                            wire:confirm="Sind Sie sicher, dass Sie diese Credit-Vergabe löschen möchten? Diese Aktion kann nicht rückgängig gemacht werden."
                                            class="btn btn-sm btn-icon" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Löschen">
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
                                    <i class="ti ti-gift text-muted mb-3" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted">Keine Credit-Vergaben gefunden</h5>
                                    @if($this->search || $this->vendorFilter || $this->adminFilter || $this->grantTypeFilter || $this->statusFilter || $this->dateFrom || $this->dateTo)
                                        <p class="text-muted mb-3">Keine Vergaben entsprechen den aktuellen Filterkriterien.</p>
                                        <button wire:click="clearFilters" class="btn btn-primary">
                                            <i class="ti ti-refresh me-1"></i>Filter zurücksetzen
                                        </button>
                                    @else
                                        <p class="text-muted mb-3">Es wurden noch keine Credit-Vergaben erstellt.</p>
                                        <a href="{{ route('admin.credit-grants.create') }}" class="btn btn-primary">
                                            <i class="ti ti-plus me-1"></i>Erste Vergabe erstellen
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
        @if($this->creditGrants->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Zeige {{ $this->creditGrants->firstItem() }} bis {{ $this->creditGrants->lastItem() }} von
                            {{ $this->creditGrants->total() }} Einträgen
                        </small>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $this->creditGrants->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>