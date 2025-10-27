<div class="dynamic-rental-fields-form">
    @if($showFields && $fields->count() > 0)
        <div class="col-12 mb-4">
            <h6 class="mb-3">5. Zusätzliche Eigenschaften</h6>
            <div class="row">
                @foreach($fields as $field)
                    <div class="col-md-6 mb-3">
                        @switch($field->type)
                            @case('text')
                                <label for="field_{{ $field->id }}" class="form-label">
                                    {{ $field->label }}
                                    @if($field->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="text" 
                                       class="form-control @error("fieldValues.{$field->id}") is-invalid @enderror" 
                                       id="field_{{ $field->id }}"
                                       wire:model="fieldValues.{{ $field->id }}"
                                       placeholder="{{ $field->placeholder }}"
                                       @if($field->is_required) required @endif>
                                @break
                                
                            @case('number')
                                <label for="field_{{ $field->id }}" class="form-label">
                                    {{ $field->label }}
                                    @if($field->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="number" 
                                       class="form-control @error("fieldValues.{$field->id}") is-invalid @enderror" 
                                       id="field_{{ $field->id }}"
                                       wire:model="fieldValues.{{ $field->id }}"
                                       placeholder="{{ $field->placeholder }}"
                                       @if($field->is_required) required @endif>
                                @break
                                
                            @case('textarea')
                                <label for="field_{{ $field->id }}" class="form-label">
                                    {{ $field->label }}
                                    @if($field->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <textarea class="form-control @error("fieldValues.{$field->id}") is-invalid @enderror" 
                                          id="field_{{ $field->id }}"
                                          wire:model="fieldValues.{{ $field->id }}"
                                          rows="3"
                                          placeholder="{{ $field->placeholder }}"
                                          @if($field->is_required) required @endif></textarea>
                                @break
                                
                            @case('select')
                                <label for="field_{{ $field->id }}" class="form-label">
                                    {{ $field->label }}
                                    @if($field->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <select class="form-select @error("fieldValues.{$field->id}") is-invalid @enderror" 
                                        id="field_{{ $field->id }}"
                                        wire:model="fieldValues.{{ $field->id }}"
                                        @if($field->is_required) required @endif>
                                    <option value="">Bitte wählen...</option>
                                    @foreach($field->options ?? [] as $option)
                                        <option value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                </select>
                                @break
                                
                            @case('checkbox')
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input @error("fieldValues.{$field->id}") is-invalid @enderror" 
                                           id="field_{{ $field->id }}"
                                           wire:model="fieldValues.{{ $field->id }}"
                                           value="1"
                                           @if($field->is_required) required @endif>
                                    <label class="form-check-label" for="field_{{ $field->id }}">
                                        {{ $field->label }}
                                        @if($field->is_required)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                </div>
                                @break
                                
                            @case('radio')
                                <label class="form-label">
                                    {{ $field->label }}
                                    @if($field->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="form-check-group">
                                    @foreach($field->options ?? [] as $option)
                                        <div class="form-check">
                                            <input type="radio" 
                                                   class="form-check-input @error("fieldValues.{$field->id}") is-invalid @enderror" 
                                                   id="field_{{ $field->id }}_{{ $loop->index }}"
                                                   wire:model="fieldValues.{{ $field->id }}"
                                                   value="{{ $option }}"
                                                   @if($field->is_required) required @endif>
                                            <label class="form-check-label" for="field_{{ $field->id }}_{{ $loop->index }}">
                                                {{ $option }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @break
                                
                            @default
                                <label for="field_{{ $field->id }}" class="form-label">
                                    {{ $field->label }}
                                    @if($field->is_required)
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="text" 
                                       class="form-control @error("fieldValues.{$field->id}") is-invalid @enderror" 
                                       id="field_{{ $field->id }}"
                                       wire:model="fieldValues.{{ $field->id }}"
                                       placeholder="{{ $field->placeholder }}"
                                       @if($field->is_required) required @endif>
                        @endswitch
                        
                        @if($field->help_text)
                            <div class="form-text">{{ $field->help_text }}</div>
                        @endif
                        
                        @error("fieldValues.{$field->id}")
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Hidden inputs for form submission -->
        @foreach($fieldValues as $fieldId => $value)
            <input type="hidden" name="dynamic_fields[{{ $fieldId }}]" value="{{ $value }}">
        @endforeach
    @endif
</div>
