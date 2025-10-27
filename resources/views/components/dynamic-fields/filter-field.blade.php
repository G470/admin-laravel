<div class="filter-field-wrapper">
    <label class="filter-field-label d-block">{{ $field['field_label'] }}</label>
    
    @switch($field['field_type'])
        @case('text')
        @case('email')
        @case('url')
            <input 
                type="text"
                wire:model.live.debounce.500ms="filters.{{ $field['field_name'] }}.search"
                class="form-control form-control-sm"
                placeholder="Suchen in {{ $field['field_label'] }}..."
            />
            @break

        @case('number')
        @case('range')
            <div class="filter-range-inputs">
                <input 
                    type="number"
                    wire:model.live.debounce.500ms="filters.{{ $field['field_name'] }}.min"
                    class="form-control form-control-sm"
                    placeholder="Min"
                    @if(isset($field['validation_rules']['min_value'])) min="{{ $field['validation_rules']['min_value'] }}" @endif
                />
                <span class="text-muted">bis</span>
                <input 
                    type="number"
                    wire:model.live.debounce.500ms="filters.{{ $field['field_name'] }}.max"
                    class="form-control form-control-sm"
                    placeholder="Max"
                    @if(isset($field['validation_rules']['max_value'])) max="{{ $field['validation_rules']['max_value'] }}" @endif
                />
            </div>
            @break

        @case('date')
            <div class="filter-range-inputs">
                <input 
                    type="date"
                    wire:model.live="filters.{{ $field['field_name'] }}.from"
                    class="form-control form-control-sm"
                />
                <span class="text-muted">bis</span>
                <input 
                    type="date"
                    wire:model.live="filters.{{ $field['field_name'] }}.to"
                    class="form-control form-control-sm"
                />
            </div>
            @break

        @case('select')
        @case('radio')
            <select 
                wire:model.live="filters.{{ $field['field_name'] }}.value"
                class="form-select form-select-sm"
            >
                <option value="">Alle {{ $field['field_label'] }}</option>
                @if(!empty($field['options']))
                    @foreach($field['options'] as $optionValue => $optionLabel)
                        <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                    @endforeach
                @endif
            </select>
            @break

        @case('checkbox')
            @if(!empty($field['options']))
                <div class="filter-checkbox-group">
                    @foreach($field['options'] as $optionValue => $optionLabel)
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="filter_{{ $field['field_name'] }}_{{ $loop->index }}"
                                wire:model.live="filters.{{ $field['field_name'] }}.values"
                                value="{{ $optionValue }}"
                            />
                            <label class="form-check-label" for="filter_{{ $field['field_name'] }}_{{ $loop->index }}">
                                {{ $optionLabel }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif
            @break

        @case('textarea')
            <textarea 
                wire:model.live.debounce.500ms="filters.{{ $field['field_name'] }}.search"
                class="form-control form-control-sm"
                rows="3"
                placeholder="Suchen in {{ $field['field_label'] }}..."
            ></textarea>
            @break

        @default
            <input 
                type="text"
                wire:model.live.debounce.500ms="filters.{{ $field['field_name'] }}.search"
                class="form-control form-control-sm"
                placeholder="Suchen..."
            />
    @endswitch
    
    <!-- Clear button for this filter -->
    @if(isset($filterValue) && collect($filterValue)->filter()->isNotEmpty())
        <button 
            type="button" 
            wire:click="removeFilter('{{ $field['field_name'] }}')"
            class="btn btn-sm btn-outline-secondary mt-2 w-100"
            title="Filter zurücksetzen"
        >
            <i class="ti ti-x me-1"></i>Zurücksetzen
        </button>
    @endif
</div> 