<div class="mb-3">
    <label for="{{ $field['field_name'] }}" class="form-label">
        {{ is_array($field) ? $field['field_label'] : $field->label }}
        @if((is_array($field) ? $field['is_required'] : $field->is_required))
            <span class="text-danger">*</span>
        @endif
    </label>

    <select id="{{ $field['field_name'] }}" wire:model.live="{{ $wireModel }}"
        class="form-select @if($error) is-invalid @endif" @if((is_array($field) ? $field['is_required'] : $field->is_required)) required @endif>
        <option value="">{{ $field['field_description'] ?? 'Bitte wählen...' }}</option>
        @if(!empty($field['options']))
            @foreach($field['options'] as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
            @endforeach
        @endif
    </select>

    @if((is_array($field) ? ($field['help_text'] ?? null) : $field->help_text))
        <div class="form-text">{{ is_array($field) ? $field['help_text'] : $field->help_text }}</div>
    @endif

    @error($wireModel)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>