<div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">System & Communication /</span> Content Moderation
    </h4>

    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i class="ti ti-search"></i></span>
                <input type="text" class="form-control" placeholder="Badwords suchen..."
                    wire:model.debounce.500ms="search">
            </div>
        </div>
        <div class="col-md-6 text-end">
            <button class="btn btn-primary" wire:click="showCreateModal">
                <i class="ti ti-plus me-1"></i>
                Neues Badword hinzufügen
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Badwords Liste</h5>
            <p class="card-subtitle text-muted mt-1">Verwalten Sie unerwünschte Wörter und deren Ersetzungen</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Wort</th>
                            <th>Ersetzung</th>
                            <th>Status</th>
                            <th class="text-center">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($badwords as $badword)
                            <tr>
                                <td><small class="text-muted">#{{ $badword->id }}</small></td>
                                <td>
                                    <span class="badge bg-label-danger">{{ $badword->word }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-label-success">{{ $badword->replacement }}</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm {{ $badword->status === 'active' ? 'btn-success' : 'btn-secondary' }}"
                                        wire:click="toggleStatus({{ $badword->id }})">
                                        <i class="ti ti-{{ $badword->status === 'active' ? 'check' : 'x' }} me-1"></i>
                                        {{ $badword->status === 'active' ? 'Aktiv' : 'Inaktiv' }}
                                    </button>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ti ti-dots-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="javascript:void(0);" wire:click="showEditModal({{ $badword->id }})">
                                                <i class="ti ti-pencil me-1"></i>
                                                Bearbeiten
                                            </a>
                                            <a class="dropdown-item text-danger" href="javascript:void(0);" wire:click="confirmDelete({{ $badword->id }})">
                                                <i class="ti ti-trash me-1"></i>
                                                Löschen
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="ti ti-shield-check mb-2" style="font-size: 2rem; color: #8592a3;"></i>
                                        <p class="text-muted">Keine Badwords gefunden.</p>
                                        @if(empty($search))
                                            <button class="btn btn-sm btn-primary" wire:click="showCreateModal">
                                                Erstes Badword hinzufügen
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($badwords->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $badwords->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal für Anlegen/Bearbeiten -->
    <div class="modal fade @if($showModal) show d-block @endif" tabindex="-1"
        style="@if($showModal) display:block; background:rgba(0,0,0,0.5); @endif" @if($showModal) aria-modal="true"
        role="dialog" @endif>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti ti-{{ $editMode ? 'pencil' : 'plus' }} me-2"></i>
                        {{ $editMode ? 'Badword bearbeiten' : 'Neues Badword anlegen' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <form wire:submit.prevent="saveBadword">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Unerwünschtes Wort <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('word') is-invalid @enderror" 
                                wire:model.defer="word" placeholder="z.B. Schimpfwort" required>
                            @error('word') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ersetzung <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('replacement') is-invalid @enderror" 
                                wire:model.defer="replacement" placeholder="z.B. ***" required>
                            @error('replacement') 
                                <div class="invalid-feedback">{{ $message }}</div> 
                            @enderror
                            <div class="form-text">Das Wort wird durch diese Ersetzung ausgetauscht</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" wire:model.defer="status" 
                                    id="statusSwitch">
                                <label class="form-check-label" for="statusSwitch">
                                    <span class="fw-medium">Aktiv</span>
                                    <small class="text-muted d-block">Das Badword wird automatisch ersetzt</small>
                                </label>
                            </div>
                            @error('status') 
                                <div class="text-danger small">{{ $message }}</div> 
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" wire:click="$set('showModal', false)">
                            <i class="ti ti-x me-1"></i>
                            Abbrechen
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-{{ $editMode ? 'device-floppy' : 'plus' }} me-1"></i>
                            {{ $editMode ? 'Aktualisieren' : 'Erstellen' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bestätigungsdialog für Löschen -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('confirm-delete', (event) => {
                if (confirm('Soll dieses Badword wirklich gelöscht werden?')) {
                    Livewire.dispatch('delete-confirmed', { id: event.id });
                }
            });
        });
    </script>
</div>
