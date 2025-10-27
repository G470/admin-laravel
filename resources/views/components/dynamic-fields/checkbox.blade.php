@if(!empty($field['options']))
    <div class="checkbox-group">
        @foreach($field['options'] as $optionValue => $optionLabel)
            <div class="form-check mb-2">
                <input class="form-check-input @if($error) is-invalid @endif" type="checkbox"
                    id="{{ $field['field_name'] }}_{{ $loop->index }}" wire:model.live="{{ $wireModel }}"
                    value="{{ $optionValue }}" />
                <label class="form-check-label" for="{{ $field['field_name'] }}_{{ $loop->index }}">
                    {{ $optionLabel }}
                </label>
            </div>
        @endforeach
    </div>
@else
    <div class="form-check">
        <input class="form-check-input @if($error) is-invalid @endif" type="checkbox" id="{{ $field['field_name'] }}"
            wire:model.live="{{ $wireModel }}" value="1" />
        <label class="form-check-label" for="{{ $field['field_name'] }}">
            {{ $field['field_label'] }}
        </label>
    </div>
@endif