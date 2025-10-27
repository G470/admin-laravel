@if(!empty($field['options']))
    <div class="radio-group">
        @foreach($field['options'] as $optionValue => $optionLabel)
            <div class="form-check mb-2">
                <input class="form-check-input @if($error) is-invalid @endif" type="radio"
                    id="{{ $field['field_name'] }}_{{ $loop->index }}" name="{{ $field['field_name'] }}"
                    wire:model.live="{{ $wireModel }}" value="{{ $optionValue }}" />
                <label class="form-check-label" for="{{ $field['field_name'] }}_{{ $loop->index }}">
                    {{ $optionLabel }}
                </label>
            </div>
        @endforeach
    </div>
@else
    <div class="alert alert-warning">
        <i class="ti ti-alert-triangle me-2"></i>
        Keine Optionen für dieses Radio-Feld definiert.
    </div>
@endif