<div>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Kategorie auswählen</h5>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary"
                    wire:click="$set('isSearchMode', {{ !$isSearchMode }})">
                    <i class="ti ti-search me-1"></i>
                    {{ $isSearchMode ? 'Zurück zur Navigation' : 'Suchen' }}
                </button>
                @if($selectedCategory)
                    <button type="button" class="btn btn-outline-danger" wire:click="removeCategorySelection">
                        <i class="ti ti-x me-1"></i>
                        Auswahl entfernen
                    </button>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if($isSearchMode)
                <!-- Search Input -->
                <div class="mb-4">
                    <input type="text" class="form-control" wire:model.live="searchTerm" placeholder="Kategorie suchen...">
                </div>
                
                @if($searchTerm && count($filteredCategories) > 0)
                    <!-- Search Results -->
                    <div class="mb-4">
                        <h6 class="mb-2">Suchergebnisse:</h6>
                        <div class="list-group">
                            @foreach($filteredCategories as $category)
                                <button type="button" class="list-group-item list-group-item-action"
                                    wire:click="selectCategory({{ $category['id'] }})">
                                    {{ $category['name'] }}
                                    @if(isset($category['full_path']) && $category['full_path'])
                                        <small class="text-muted d-block">{{ $category['full_path'] }}</small>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if($searchTerm && count($filteredCategories) === 0)
                    <div class="alert alert-info mb-4">
                        <i class="ti ti-info-circle me-2"></i>
                        Keine Kategorien gefunden für "{{ $searchTerm }}"
                    </div>
                @endif
            @endif
            
            <!-- Always show hierarchical navigation -->
            <div class="row">
                <div class="col-md-4">
                    @if($isSearchMode)
                        <h6 class="mb-2">Hauptkategorien:</h6>
                    @endif
                    <div class="list-group">
                        @foreach($firstLevelCategories as $category)
                            <button type="button"
                                class="list-group-item list-group-item-action {{ $selectedFirstLevel && $selectedFirstLevel['id'] === $category['id'] ? 'active' : '' }}"
                                wire:click="selectFirstLevel({{ $category['id'] }})">
                                {{ $category['name'] }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-4">
                    @if($secondLevelCategories)
                        @if($isSearchMode)
                            <h6 class="mb-2">Unterkategorien:</h6>
                        @endif
                        <div class="list-group">
                            @foreach($secondLevelCategories as $category)
                                <button type="button"
                                    class="list-group-item list-group-item-action {{ $selectedSecondLevel && $selectedSecondLevel['id'] === $category['id'] ? 'active' : '' }}"
                                    wire:click="selectSecondLevel({{ $category['id'] }})">
                                    {{ $category['name'] }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="col-md-4">
                    @if($thirdLevelCategories)
                        @if($isSearchMode)
                            <h6 class="mb-2">Detailkategorien:</h6>
                        @endif
                        <div class="list-group">
                            @foreach($thirdLevelCategories as $category)
                                <button type="button"
                                    class="list-group-item list-group-item-action {{ $selectedThirdLevel && $selectedThirdLevel['id'] === $category['id'] ? 'active' : '' }}"
                                    wire:click="selectThirdLevel({{ $category['id'] }})">
                                    {{ $category['name'] }}
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', function () {
            // jQuery und Select2 initialisieren
            $(document).ready(function () {
                // Livewire-Hook für Updates
                Livewire.hook('message.processed', (message, component) => {
                    // entfernt
                });
            });
        });
    </script>
    <!-- add bottom space -->
    <div class="mb-5"></div>
</div>