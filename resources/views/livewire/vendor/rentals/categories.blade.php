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
            @if($selectedCategory)
                <div class="alert alert-success d-flex align-items-center mb-3">
                    <i class="ti ti-check-circle me-2"></i>
                    <div>
                        <strong>Ausgewählte Kategorie:</strong> {{ $selectedCategory['name'] }}
                        <small class="d-block text-muted">Sie können die Kategorie über den Button ändern</small>
                    </div>
                </div>
            @endif

            @if($isSearchMode)
                <div class="mb-3">
                    <input type="text" class="form-control" wire:model.live="searchTerm" placeholder="Kategorie suchen...">
                </div>
                @if($searchTerm)
                    <div class="list-group">
                        @foreach($filteredCategories as $category)
                            <button type="button" class="list-group-item list-group-item-action"
                                wire:click="selectCategory({{ $category['id'] }})">
                                {{ $category['name'] }}
                                @if(isset($category['full_path']))
                                    <small class="text-muted d-block">{{ $category['full_path'] }}</small>
                                @endif
                            </button>
                        @endforeach
                    </div>
                @endif
            @endif
            <div class="row">
                <div class="col-md-4">
                    <h6 class="text-muted mb-2">Hauptkategorien</h6>
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
                        <h6 class="text-muted mb-2">Unterkategorien</h6>
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
                        <h6 class="text-muted mb-2">Spezifische Kategorien</h6>
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
            // Initialize Select2 for any select elements in the component
            const selectElements = document.querySelectorAll('.select2');
            if (selectElements.length > 0 && typeof $ !== 'undefined' && $.fn.select2) {
                try {
                    $(selectElements).select2();
                } catch (error) {
                    console.error('Select2 initialization in categories component failed:', error);
                }
            }

            // Listen for category updates
            Livewire.on('categorySelected', (event) => {
                console.log('Category selected in component:', event);
            });

            Livewire.on('categoryRemoved', () => {
                console.log('Category selection removed in component');
            });
        });
    </script>
    <!-- add bottom space -->
    <div class="mb-5"></div>
</div>