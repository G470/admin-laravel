<div class="rental-field-display">
    @if($showFields && $fieldValues->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ti ti-list-details me-2"></i>
                    Zusätzliche Eigenschaften
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($fieldValues as $fieldValue)
                        @php
                            $field = $fieldValue->field;
                            $displayValue = $this->getFieldDisplayValue($fieldValue);
                        @endphp

                        <div class="col-md-6 mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold text-muted">{{ $field->label }}:</span>
                                <span class="text-body">{{ $displayValue }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>