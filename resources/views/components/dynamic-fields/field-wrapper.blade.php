<div class="dynamic-field-wrapper">
    <!-- if fieldValues is not empty, then show the value -->
    <!-- fieldValues looks like this:
     array:10 [
  "fahrzeugmarke" => "bmw"
  "baujahr" => 2020.0
  "kraftstoff" => "hybrid"
  "getriebe" => "automatik"
  "sitzplaetze" => 5.0
  "ausstattung" => array:3 [
    0 => "navigation"
    1 => "usb"
    2 => "aux"
  ]
  "rooms" => 6.0
  "floor" => 3.0
  "elevator" => array:1 [
    0 => "1"
  ]
  "balcony" => "2"
]
 we need to show the value of the field in the field_name key    -->
    @if($fieldValues && $fieldValues[$field['field_name']])
    <!-- if $fieldValues[$field['field_name']] is not array, then show the value -->
        @if(!is_array($fieldValues[$field['field_name']]))
            <p>TEST {{ $field['field_name'] }} {{ $fieldValues[$field['field_name']] }}</p>
        @endif
    @endif
    
    <label for="{{ $field['field_name'] }}" class="form-label">
        {{ $field['field_label'] }}
        @if($field['is_required'])
            <span class="text-danger">*</span>
        @endif
    </label>
    
    @if($field['field_description'])
        <small class="text-muted d-block mb-2">{{ $field['field_description'] }}</small>
    @endif

    @switch($field['field_type'])
        @case('text')
        @case('email')
        @case('url')
            @include('components.dynamic-fields.text', [
                'field' => $field,
                'wireModel' => $wireModel,
                'value' => $value,
                'error' => $error
            ])
            @break

        @case('textarea')
            @include('components.dynamic-fields.textarea', [
                'field' => $field,
                'wireModel' => $wireModel,
                'value' => $value,
                'error' => $error
            ])
            @break

        @case('number')
        @case('range')
            @include('components.dynamic-fields.number', [
                'field' => $field,
                'wireModel' => $wireModel,
                'value' => $value,
                'error' => $error
            ])
            @break

        @case('date')
            @include('components.dynamic-fields.date', [
                'field' => $field,
                'wireModel' => $wireModel,
                'value' => $value,
                'error' => $error
            ])
            @break

        @case('select')
            @include('components.dynamic-fields.select', [
                'field' => $field,
                'wireModel' => $wireModel,
                'value' => $value,
                'error' => $error
            ])
            @break

        @case('radio')
            @include('components.dynamic-fields.radio', [
                'field' => $field,
                'wireModel' => $wireModel,
                'value' => $value,
                'error' => $error
            ])
            @break

        @case('checkbox')
            @include('components.dynamic-fields.checkbox', [
                'field' => $field,
                'wireModel' => $wireModel,
                'value' => $value,
                'error' => $error
            ])
            @break

        @default
            @include('components.dynamic-fields.text', [
                'field' => $field,
                'wireModel' => $wireModel,
                'value' => $value,
                'error' => $error
            ])
    @endswitch

    @if($error)
        <div class="invalid-feedback d-block">
            {{ $error }}
        </div>
    @endif

    @if($field['is_filterable'])
        <small class="text-info">
            <i class="ti ti-filter ti-xs me-1"></i>Filterbar
        </small>
    @endif
</div> 