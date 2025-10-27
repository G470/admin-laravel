<div class="mb-3">
    <label for="{{ $field['field_name'] }}" class="form-label">
        {{ is_array($field) ? $field['field_label'] : $field->label }}
        @if((is_array($field) ? $field['is_required'] : $field->is_required))
            <span class="text-danger">*</span>
        @endif
    </label>

    <input type="{{ $field['field_type'] === 'email' ? 'email' : ($field['field_type'] === 'url' ? 'url' : 'text') }}"
        id="{{ $field['field_name'] }}" wire:model.live="{{ $wireModel }}"
        class="form-control @if($error) is-invalid @endif"
        placeholder="{{ $field['field_description'] ?? $field['field_label'] }}" @if($field['is_required']) required @endif
        @if(isset($field['validation_rules']['max_length']))
        maxlength="{{ $field['validation_rules']['max_length'] }}" @endif
        @if(isset($field['validation_rules']['pattern'])) pattern="{{ $field['validation_rules']['pattern'] }}"
        @endif />

    @if((is_array($field) ? ($field['help_text'] ?? null) : $field->help_text))
        <div class="form-text">{{ is_array($field) ? $field['help_text'] : $field->help_text }}</div>
    @endif

    @error($wireModel)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>