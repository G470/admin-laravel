<div class="mb-3">
    <label for="{{ $field['field_name'] }}" class="form-label">
        {{ is_array($field) ? $field['field_label'] : $field->label }}
        @if((is_array($field) ? $field['is_required'] : $field->is_required))
            <span class="text-danger">*</span>
        @endif
    </label>

    <input type="{{ $field['field_type'] === 'range' ? 'range' : 'number' }}" id="{{ $field['field_name'] }}"
        wire:model.live="{{ $wireModel }}" class="form-control @if($error) is-invalid @endif"
        placeholder="{{ $field['field_description'] ?? $field['field_label'] }}" @if($field['is_required']) required @endif
        @if(isset($field['validation_rules']['min_value'])) min="{{ $field['validation_rules']['min_value'] }}" @endif
        @if(isset($field['validation_rules']['max_value'])) max="{{ $field['validation_rules']['max_value'] }}" @endif
        @if(isset($field['validation_rules']['step'])) step="{{ $field['validation_rules']['step'] }}" @endif />

    @if($field['field_type'] === 'range')
        <div class="d-flex justify-content-between text-muted small mt-1">
            <span>{{ $field['validation_rules']['min_value'] ?? 0 }}</span>
            <span class="fw-medium">{{ $value ?? 0 }}</span>
            <span>{{ $field['validation_rules']['max_value'] ?? 100 }}</span>
        </div>
    @endif

    @if((is_array($field) ? ($field['help_text'] ?? null) : $field->help_text))
        <div class="form-text">{{ is_array($field) ? $field['help_text'] : $field->help_text }}</div>
    @endif

    @error($wireModel)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>