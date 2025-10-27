<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Kategorienverwaltung</h4>
        <button class="btn btn-primary" wire:click="showCreateModal">
            <i class="ti ti-plus me-1"></i>Neue Kategorie hinzufügen
        </button>
    </div>
    
    <!-- Success/Error Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Search -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text"><i class="ti ti-search"></i></span>
                <input type="text" class="form-control" placeholder="Kategorien durchsuchen..."
                    wire:model.live.debounce.500ms="search">
                @if($search)
                    <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')">
                        <i class="ti ti-x"></i>
                    </button>
                @endif
            </div>
            @if($search)
                <div class="form-text mt-1">
                    <i class="ti ti-search me-1"></i>Suche aktiv: "{{ $search }}"
                    @if($categories->total() > 0)
                        <span class="text-success ms-2">{{ $categories->total() }} {{ $categories->total() == 1 ? 'Kategorie' : 'Kategorien' }} gefunden</span>
                    @else
                        <span class="text-muted ms-2">Keine Kategorien gefunden</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;"></th>
                        <th style="width: 80px;">ID</th>
                        <th>Kategoriename</th>
                        <th>Eltern-Kategoriename</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 150px;">Aktion</th>
                    </tr>
                </thead>
                <tbody id="sortable-body">
                    @forelse($categories as $category)
                        @include('livewire.admin.partials.category-row', [
                            'category' => $category, 
                            'level' => 0,
                            'parentName' => 'Hauptkategorie'
                        ])
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                @if($search)
                                    <i class="ti ti-search-off fs-3 mb-2 d-block"></i>
                                    Keine Kategorien gefunden für "{{ $search }}"
                                @else
                                    <i class="ti ti-category fs-3 mb-2 d-block"></i>
                                    Noch keine Kategorien erstellt
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($categories->hasPages())
            <div class="card-footer">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    <!-- Modal für Anlegen/Bearbeiten -->
    <div class="modal fade @if($showModal) show d-block @endif" tabindex="-1"
        style="@if($showModal) display:block; background:rgba(0,0,0,0.5); @endif" @if($showModal) aria-modal="true"
        role="dialog" @endif>
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ti {{ $editMode ? 'ti-edit' : 'ti-plus' }} me-2"></i>
                        {{ $editMode ? 'Kategorie bearbeiten' : 'Neue Kategorie anlegen' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <form wire:submit.prevent="saveCategory">
                    <div class="modal-body">
                        <!-- Basis Informationen -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Basis Informationen</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label required">Kategoriename</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                wire:model="name" required placeholder="z.B. Computer & Büro">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label required">Slug (URL)</label>
                                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                                wire:model.defer="slug" required placeholder="z.B. computer-buero">
                                            @error('slug')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Wird automatisch generiert, kann aber manuell angepasst werden.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Übergeordnete Kategorie</label>
                                            <select class="form-select select2-parent @error('parent_id') is-invalid @enderror" 
                                                wire:model.defer="parent_id">
                                                <option value="">Keine (Hauptkategorie)</option>
                                                @foreach($allCategories as $cat)
                                                    @if(!$editMode || $cat->id !== $categoryId)
                                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                        @if($cat->children)
                                                            @foreach($cat->children as $child)
                                                                @if(!$editMode || $child->id !== $categoryId)
                                                                    <option value="{{ $child->id }}">&nbsp;&nbsp;├─ {{ $child->name }}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('parent_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Wählen Sie eine übergeordnete Kategorie aus, um eine Unterkategorie zu erstellen.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" wire:model.defer="status" 
                                                    value="online" id="statusSwitch" 
                                                    @if($status == 'online') checked @endif>
                                                <label class="form-check-label" for="statusSwitch">
                                                    {{ $status == 'online' ? 'Online' : 'Offline' }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Beschreibung</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                        wire:model.defer="description" rows="3" 
                                        placeholder="Kurze Beschreibung der Kategorie..."></textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SEO Einstellungen -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">SEO Einstellungen</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Meta Titel</label>
                                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                        wire:model.defer="meta_title" maxlength="60" 
                                        placeholder="SEO-optimierter Titel (max. 60 Zeichen)">
                                    @error('meta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <span class="text-muted">{{ strlen($meta_title ?? '') }}/60 Zeichen</span>
                                        @if(strlen($meta_title ?? '') > 55)
                                            <span class="text-warning ms-2">⚠️ Titel wird eventuell gekürzt angezeigt</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Meta Beschreibung</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                        wire:model.defer="meta_description" rows="3" maxlength="160"
                                        placeholder="Beschreibung für Suchmaschinen (max. 160 Zeichen)"></textarea>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <span class="text-muted">{{ strlen($meta_description ?? '') }}/160 Zeichen</span>
                                        @if(strlen($meta_description ?? '') > 150)
                                            <span class="text-danger ms-2">⚠️ Beschreibung zu lang! Wird abgeschnitten.</span>
                                        @elseif(strlen($meta_description ?? '') > 140)
                                            <span class="text-warning ms-2">⚠️ Beschreibung wird eventuell gekürzt</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Erweiterte Einstellungen -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Erweiterte Einstellungen</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Standard-Textinhalt für Unterkategorien</label>
                                    <textarea class="form-control @error('default_text_content') is-invalid @enderror" 
                                        wire:model.defer="default_text_content" rows="4"
                                        placeholder="Dieser Text wird als Standardinhalt für neue Unterkategorien verwendet..."></textarea>
                                    @error('default_text_content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Text, der automatisch in Unterkategorien eingefügt wird.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Kategorie-Bild</label>
                                    
                                    <!-- Current Image Preview -->
                                    @if($current_category_image && !$category_image_upload)
                                        <div class="mb-2">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('storage/' . $current_category_image) }}" 
                                                     alt="Aktuelles Kategorie-Bild" 
                                                     class="rounded me-3" 
                                                     style="width: 80px; height: 80px; object-fit: cover;">
                                                <div>
                                                    <p class="mb-1 fw-semibold text-muted">Aktuelles Bild</p>
                                                    <small class="text-muted">{{ basename($current_category_image) }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Upload Preview -->
                                    @if($category_image_upload)
                                        <div class="mb-2">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $category_image_upload->temporaryUrl() }}" 
                                                     alt="Neues Kategorie-Bild" 
                                                     class="rounded me-3" 
                                                     style="width: 80px; height: 80px; object-fit: cover;">
                                                <div class="flex-grow-1">
                                                    <p class="mb-1 fw-semibold text-success">Neues Bild</p>
                                                    <small class="text-muted">{{ $category_image_upload->getClientOriginalName() }}</small>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    wire:click="removeImage" title="Bild entfernen">
                                                    <i class="ti ti-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- File Upload Input -->
                                    <input type="file" class="form-control @error('category_image_upload') is-invalid @enderror" 
                                        wire:model="category_image_upload" 
                                        accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                                    @error('category_image_upload')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Unterstützte Formate: JPEG, PNG, JPG, GIF, WebP | Max. Größe: 2MB
                                        @if($category_image_upload)
                                            <span class="text-success ms-2">✓ Datei ausgewählt</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Loading State -->
                                    <div wire:loading wire:target="category_image_upload" class="text-primary mt-2">
                                        <i class="ti ti-loader-2 spin me-1"></i>Bild wird hochgeladen...
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required">Anzeige-Template</label>
                                    <select class="form-select @error('form_template_display_style') is-invalid @enderror" 
                                        wire:model.defer="form_template_display_style">
                                        <option value="show_only_rentals">Nur Vermietungen anzeigen</option>
                                        <option value="show_category_details_and_subcategories">Kategorie-Details und Unterkategorien anzeigen</option>
                                        <option value="show_category_details_and_rentals">Kategorie-Details und Vermietungen anzeigen</option>
                                    </select>
                                    @error('form_template_display_style')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Bestimmt, wie die Kategorie-Seite aufgebaut wird.</div>
                                </div>
                            </div>
                        </div>

                        @if($editMode)
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Hinweis:</strong> Beim Ändern der übergeordneten Kategorie werden alle Unterkategorien mit verschoben.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            wire:click="$set('showModal', false)">
                            <i class="ti ti-x me-1"></i>Abbrechen
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti {{ $editMode ? 'ti-device-floppy' : 'ti-plus' }} me-1"></i>
                            {{ $editMode ? 'Aktualisieren' : 'Erstellen' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Custom Styles -->
    <style>
        .category-row.level-0 .category-name {
            font-weight: 600;
        }
        .category-row.level-1 .category-name {
            color: #6c757d;
        }
        .category-row.level-2 .category-name {
            color: #adb5bd;
            font-size: 0.9em;
        }
        .category-indent {
            color: #dee2e6;
            font-family: monospace;
        }
        .category-branch {
            color: #6c757d;
            font-family: monospace;
        }
        .expand-toggle {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            transition: all 0.2s ease;
        }
        .expand-toggle:hover {
            background-color: #e9ecef;
        }
        .required::after {
            content: ' *';
            color: #dc3545;
        }
        .spin {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

    <!-- Scripts -->
    <script>
        // Bestätigungsdialog für Löschen
        window.addEventListener('confirm-delete', event => {
            if (confirm('Soll diese Kategorie wirklich gelöscht werden?\n\nAchtung: Alle Unterkategorien werden ebenfalls gelöscht!')) {
                Livewire.dispatch('deleteCategory', event.detail.id);
            }
        });

        // Livewire Hooks
        document.addEventListener('livewire:load', function () {
            initializeComponents();
        });

        document.addEventListener('livewire:update', function () {
            initializeComponents();
        });

        function initializeComponents() {
            // Drag & Drop Sortierung (nur für gleiche Ebene)
            initSortable();
            
            // Select2 für Parent-Auswahl
            initSelect2();
            
            // Tooltips
            initTooltips();
        }

        function initSortable() {
            if (typeof jQuery !== 'undefined' && jQuery.ui) {
                $('#sortable-body').sortable({
                    items: '.category-row.level-0', // Nur Hauptkategorien sortierbar
                    handle: '.drag-handle', // Spezifischer Handle für Drag
                    update: function (event, ui) {
                        let orderedIds = [];
                        $(this).find('.category-row.level-0').each(function() {
                            orderedIds.push($(this).data('id'));
                        });
                        Livewire.dispatch('updateSortOrder', orderedIds);
                    },
                    placeholder: 'ui-state-highlight',
                    cursor: 'move'
                });
            }
        }

        function initSelect2() {
            if (typeof jQuery !== 'undefined' && jQuery.fn.select2) {
                $('.select2-parent').select2({
                    dropdownParent: $('.modal:visible'),
                    width: '100%',
                    placeholder: 'Übergeordnete Kategorie wählen...',
                    allowClear: true
                }).on('change', function (e) {
                    @this.set('parent_id', $(this).val());
                });
            }
        }

        function initTooltips() {
            if (typeof bootstrap !== 'undefined') {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        }

        // Status Toggle Animation
        window.addEventListener('status-changed', event => {
            const button = document.querySelector(`[wire\\:click="toggleStatus(${event.detail.id})"]`);
            if (button) {
                button.classList.add('animate__animated', 'animate__pulse');
                setTimeout(() => {
                    button.classList.remove('animate__animated', 'animate__pulse');
                }, 1000);
            }
        });
    </script>

    <!-- Required Assets -->
    @vite([
        'resources/assets/vendor/libs/jquery-ui/jquery-ui.js'
    ])
</div>