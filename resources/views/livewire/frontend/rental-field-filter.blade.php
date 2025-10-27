<div class="rental-field-filter">
    @if($hasFields)
        <div class="filter-container">
            <!-- Filter Header -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">
                    <i class="ti ti-filter me-2"></i>Erweiterte Filter
                    @if($activeFiltersCount > 0)
                        <span class="badge bg-primary ms-2">{{ $activeFiltersCount }}</span>
                    @endif
                </h6>
                <div class="d-flex gap-2">
                    @if($activeFiltersCount > 0)
                        <button type="button" wire:click="clearAllFilters" class="btn btn-sm btn-outline-secondary">
                            <i class="ti ti-x me-1"></i>Alle löschen
                        </button>
                    @endif
                    <button type="button" wire:click="toggleFilters" class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-chevron-{{ $isCollapsed ? 'down' : 'up' }}"></i>
                    </button>
                </div>
            </div>

            <!-- Active Filters Display -->
            @if($activeFiltersCount > 0)
                <div class="active-filters mb-3">
                    <small class="text-muted d-block mb-2">Aktive Filter:</small>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($activeFilters as $activeFilter)
                            <span class="badge bg-light text-dark border d-flex align-items-center">
                                <strong class="me-1">{{ $activeFilter['field_label'] }}:</strong>
                                {{ $activeFilter['value'] }}
                                <button type="button" 
                                        wire:click="removeFilter('{{ $activeFilter['field_name'] }}')"
                                        class="btn-close btn-close-sm ms-2"
                                        style="font-size: 0.7em;"
                                        title="Filter entfernen"></button>
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Filter Fields -->
            <div class="filter-fields @if($isCollapsed) d-none @endif">
                @foreach($templateGroups as $templateName => $fields)
                    <div class="filter-group mb-4">
                        <h6 class="filter-group-title text-primary mb-3">
                            <i class="ti ti-folder me-2"></i>{{ $templateName }}
                        </h6>
                        
                        <div class="row g-3">
                            @foreach($fields as $field)
                                <div class="col-md-6 col-lg-4" wire:key="filter-{{ $field['id'] }}">
                                    @include('components.dynamic-fields.filter-field', [
                                        'field' => $field,
                                        'filterValue' => $filters[$field['field_name']] ?? []
                                    ])
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- No Filters Available -->
        <div class="text-center text-muted py-3">
            <i class="ti ti-filter-off me-2"></i>
            <small>Keine Filter für diese Kategorie verfügbar</small>
        </div>
    @endif
</div>

@push('styles')
<style>
.rental-field-filter {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    border: 1px solid #e3e6f0;
}

.filter-group-title {
    font-size: 0.9rem;
    font-weight: 600;
    border-bottom: 2px solid rgba(var(--bs-primary-rgb), 0.1);
    padding-bottom: 0.5rem;
}

.active-filters .badge {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
}

.filter-field-wrapper {
    background: white;
    border-radius: 6px;
    padding: 1rem;
    border: 1px solid #e3e6f0;
    transition: all 0.2s ease;
}

.filter-field-wrapper:hover {
    border-color: rgba(var(--bs-primary-rgb), 0.3);
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.filter-field-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #5a5c69;
    margin-bottom: 0.5rem;
}

.filter-range-inputs {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.filter-range-inputs .form-control {
    flex: 1;
    font-size: 0.85rem;
}

.filter-checkbox-group,
.filter-radio-group {
    max-height: 120px;
    overflow-y: auto;
}

.filter-checkbox-group .form-check,
.filter-radio-group .form-check {
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

@media (max-width: 768px) {
    .rental-field-filter {
        padding: 0.75rem;
    }
    
    .filter-fields .row {
        --bs-gutter-x: 0.5rem;
    }
    
    .filter-field-wrapper {
        padding: 0.75rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        // Listen for filter events
        Livewire.on('filtersChanged', (event) => {
            console.log('Filters changed:', event.filters);
            console.log('Active filters count:', event.activeFiltersCount);
        });

        Livewire.on('filtersCleared', () => {
            console.log('All filters cleared');
        });

        Livewire.on('filtersLoaded', (event) => {
            console.log(`${event.fieldCount} filter fields loaded`);
        });
    });
</script>
@endpush
