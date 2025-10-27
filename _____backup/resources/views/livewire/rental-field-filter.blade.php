<div class="rental-field-filter">
    @if($availableFields && $availableFields->count() > 0)
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Filter</h5>
                @if(count($filters) > 0)
                    <button wire:click="clearFilters" class="btn btn-sm btn-outline-secondary">
                        <i class="ti ti-x me-1"></i>Alle löschen
                    </button>
                @endif
            </div>
            <div class="card-body">
                @foreach($availableFields as $field)
                    <div class="mb-3">
                        <label class="form-label">{{ $field->field_label }}</label>
                        
                        @switch($field->field_type)
                            @case('select')
                                <select class="form-select" wire:model.live="filters.{{ $field->id }}" 
                                        wire:change="applyFilter({{ $field->id }}, $event.target.value)">
                                    <option value="">Alle auswählen</option>
                                    @foreach($field->options ?? [] as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                                @break
                                
                            @case('radio')
                                <div class="form-check-group">
                                    @foreach($field->options ?? [] as $option)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" 
                                                   name="filter_{{ $field->id }}" 
                                                   id="filter_{{ $field->id }}_{{ $loop->index }}"
                                                   value="{{ $option }}"
                                                   wire:model.live="filters.{{ $field->id }}"
                                                   wire:change="applyFilter({{ $field->id }}, '{{ $option }}')">
                                            <label class="form-check-label" for="filter_{{ $field->id }}_{{ $loop->index }}">
                                                {{ $option }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @break
                                
                            @case('checkbox')
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="filter_{{ $field->id }}"
                                           wire:model.live="filters.{{ $field->id }}"
                                           wire:change="applyFilter({{ $field->id }}, $event.target.checked ? '1' : '')">
                                    <label class="form-check-label" for="filter_{{ $field->id }}">
                                        {{ $field->label }}
                                    </label>
                                </div>
                                @break
                                
                            @case('number')
                                <div class="row">
                                    <div class="col-6">
                                        <input type="number" class="form-control" 
                                               placeholder="Min"
                                               wire:model.live.debounce.300ms="filters.{{ $field->id }}_min"
                                               wire:change="applyFilter('{{ $field->id }}_min', $event.target.value)">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control" 
                                               placeholder="Max"
                                               wire:model.live.debounce.300ms="filters.{{ $field->id }}_max"
                                               wire:change="applyFilter('{{ $field->id }}_max', $event.target.value)">
                                    </div>
                                </div>
                                @break
                                
                            @default
                                <input type="text" class="form-control" 
                                       placeholder="{{ $field->placeholder }}"
                                       wire:model.live.debounce.300ms="filters.{{ $field->id }}"
                                       wire:change="applyFilter({{ $field->id }}, $event.target.value)">
                        @endswitch
                        
                        @if($field->help_text)
                            <small class="form-text text-muted">{{ $field->help_text }}</small>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
