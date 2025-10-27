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
                <div class="mb-3">
                    <input type="text" class="form-control" wire:model.live="searchTerm" placeholder="Kategorie suchen...">
                </div>
                @if($searchTerm)
                    <div class="list-group">
                        @foreach($filteredCategories as $category)
                            <button type="button" class="list-group-item list-group-item-action"
                                wire:click="selectCategory({{ $category['id'] }})">
                                {{ $category['name'] }}
                            </button>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="row">
                    <div class="col-md-4">
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
            @endif
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