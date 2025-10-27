<div>
    @if($showFields)
    <div class="mb-4">
        <h5 class="mb-3">
            <i class="ti ti-info-circle me-2"></i>Zusätzliche Informationen
        </h5>

        @foreach($templateGroups as $group)
        @if(count($templateGroups) > 1)
        <h6 class="mb-3 text-muted">{{ $group['template']->name }}</h6>
        @endif

        <div class="row mb-4">
            @foreach($group['fields'] as $fieldData)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 bg-light border-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0 me-3">
                                @switch($fieldData['field']->field_type)
                                @case('text')
                                @case('email')
                                @case('url')
                                <i class="ti ti-abc text-primary"></i>
                                @break
                                @case('number')
                                @case('range')
                                <i class="ti ti-123 text-info"></i>
                                @break
                                @case('select')
                                <i class="ti ti-list text-warning"></i>
                                @break
                                @case('checkbox')
                                <i class="ti ti-checkbox text-success"></i>
                                @break
                                @case('radio')
                                <i class="ti ti-circle-dot text-secondary"></i>
                                @break
                                @case('date')
                                <i class="ti ti-calendar text-danger"></i>
                                @break
                                @case('textarea')
                                <i class="ti ti-notes text-dark"></i>
                                @break
                                @default
                                <i class="ti ti-info-circle text-muted"></i>
                                @endswitch
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 text-dark">{{ $fieldData['field']->field_label }}</h6>
                                <p class="mb-0 text-body">{{ $fieldData['formatted_value'] }}</p>
                                @if($fieldData['field']->field_description)
                                <small class="text-muted">{{ $fieldData['field']->field_description }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if(!$loop->last)
        <hr class="my-4">
        @endif
        @endforeach
    </div>
    @endif
</div>