<div class="container">
    <h1>Abonnementplan-Verwaltung</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Abonnementpläne suchen..."
                wire:model.debounce.500ms="search">
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-primary" wire:click="showCreateModal">
                <i class="ti ti-plus me-1"></i>Neuen Abonnementplan hinzufügen
            </button>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover border-top" id="sortable-table">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Preis</th>
                        <th>Abrechnungszyklus</th>
                        <th>Status</th>
                        <th>Hervorgehoben</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody id="sortable-body">
                    @forelse($subscriptionPlans as $plan)
                        <tr data-id="{{ $plan->id }}">
                            <td>{{ $plan->id }}</td>
                            <td><strong>{{ $plan->name }}</strong></td>
                            <td>{{ $plan->formatted_price }}</td>
                            <td>{{ $plan->billing_cycle_text }}</td>
                            <td>
                                <button class="btn btn-sm {{ $plan->status == 'active' ? 'btn-success' : 'btn-secondary' }}"
                                    wire:click="toggleStatus({{ $plan->id }})">
                                    {{ $plan->status == 'active' ? 'Aktiv' : 'Inaktiv' }}
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-sm {{ $plan->is_featured ? 'btn-info' : 'btn-outline-info' }}"
                                    wire:click="toggleFeatured({{ $plan->id }})">
                                    {{ $plan->is_featured ? 'Ja' : 'Nein' }}
                                </button>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="javascript:void(0)" class="dropdown-item" wire:click="showEditModal({{ $plan->id }})">
                                                <i class="ti ti-edit me-1"></i> Bearbeiten
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)" class="dropdown-item text-danger"
                                                onclick="confirm('Sind Sie sicher, dass Sie diesen Abonnementplan löschen möchten?') || event.stopImmediatePropagation()"
                                                wire:click="deletePlan({{ $plan->id }})">
                                                <i class="ti ti-trash me-1"></i> Löschen
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Keine Abonnementpläne gefunden</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $subscriptionPlans->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editMode ? 'Abonnementplan bearbeiten' : 'Neuen Abonnementplan erstellen' }}</h5>
                        <button type="button" class="btn-close" wire:click="hideModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit="save">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Beschreibung</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" wire:model="description" rows="3"></textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="price" class="form-label">Preis (€) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" wire:model="price">
                                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="billing_cycle" class="form-label">Abrechnungszyklus <span class="text-danger">*</span></label>
                                    <select class="form-select @error('billing_cycle') is-invalid @enderror" id="billing_cycle" wire:model="billing_cycle">
                                        <option value="monthly">Monatlich</option>
                                        <option value="quarterly">Vierteljährlich</option>
                                        <option value="annually">Jährlich</option>
                                    </select>
                                    @error('billing_cycle') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="trial_days" class="form-label">Testphase (Tage)</label>
                                    <input type="number" class="form-control @error('trial_days') is-invalid @enderror" id="trial_days" wire:model="trial_days">
                                    @error('trial_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Funktionen</label>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" wire:model="featureInput" placeholder="Neue Funktion hinzufügen">
                                    <button class="btn btn-outline-secondary" type="button" wire:click="addFeature">
                                        <i class="ti ti-plus"></i>
                                    </button>
                                </div>

                                <div class="mt-2">
                                    @foreach($features as $index => $feature)
                                        <div class="badge bg-primary d-inline-flex align-items-center me-1 mb-1 p-2">
                                            {{ $feature }}
                                            <button type="button" class="btn-close btn-close-white ms-2" wire:click="removeFeature({{ $index }})"></button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" wire:model="status">
                                        <option value="active">Aktiv</option>
                                        <option value="inactive">Inaktiv</option>
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label d-block">Hervorgehoben</label>
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="is_featured" wire:model="is_featured">
                                        <label class="form-check-label" for="is_featured">Als empfohlen markieren</label>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="sort_order" class="form-label">Sortierreihenfolge</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" id="sort_order" wire:model="sort_order">
                                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" wire:click="hideModal">Abbrechen</button>
                                <button type="submit" class="btn btn-primary">Speichern</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', function () {
            const sortableTable = document.getElementById('sortable-body');

            if (sortableTable) {
                new Sortable(sortableTable, {
                    animation: 150,
                    ghostClass: 'bg-light',
                    handle: 'tr',
                    onEnd: function(evt) {
                        const items = [...evt.to.children].map(item => item.getAttribute('data-id'));
                        @this.updateSortOrder(items);
                    }
                });
            }
        });
    </script>
    @endpush
</div>
