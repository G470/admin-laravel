<input type="date" id="{{ $field['field_name'] }}" wire:model.live="{{ $wireModel }}"
    class="form-control @if($error) is-invalid @endif" @if($field['is_required']) required @endif
    @if(isset($field['validation_rules']['min_date'])) min="{{ $field['validation_rules']['min_date'] }}" @endif
    @if(isset($field['validation_rules']['max_date'])) max="{{ $field['validation_rules']['max_date'] }}" @endif />