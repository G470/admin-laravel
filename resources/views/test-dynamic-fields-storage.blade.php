@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Dynamic Fields Storage Test')

@section('content')
    <section class="section-py">
        <div class="container">
            <h1 class="mb-4">Dynamic Fields Storage Test</h1>
            
            <div class="row">
                <div class="col-12 mb-4">
                    <h3>Test der dynamischen Feld-Speicherung</h3>
                    <p class="text-muted">Test der Speicherung und des Ladens von dynamischen Feldwerten.</p>
                    
                    @php
                        // Finde eine Kategorie mit dynamischen Feldern
                        $categoryWithFields = \App\Models\Category::whereHas('rentalFieldTemplates.fields')
                            ->where('status', 'online')
                            ->first();
                        
                        // Finde ein Rental mit dynamischen Feldern
                        $rentalWithFields = \App\Models\Rental::whereHas('fieldValues')
                            ->where('status', 'active')
                            ->first();
                    @endphp
                    
                    @if($categoryWithFields)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Kategorie mit dynamischen Feldern: {{ $categoryWithFields->name }}</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $templates = \App\Helpers\DynamicRentalFields::getActiveTemplatesForCategory($categoryWithFields->id);
                                    $fields = \App\Helpers\DynamicRentalFields::getTemplateFieldsForCategory($categoryWithFields->id);
                                @endphp
                                
                                <h6>Templates ({{ $templates->count() }}):</h6>
                                <ul>
                                    @foreach($templates as $template)
                                        <li>{{ $template->name }} ({{ $template->fields->count() }} Felder)</li>
                                    @endforeach
                                </ul>
                                
                                <h6>Felder ({{ $fields->count() }}):</h6>
                                <ul>
                                    @foreach($fields as $field)
                                        <li>{{ $field->field_label }} ({{ $field->field_type }}) - {{ $field->field_name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    
                    @if($rentalWithFields)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Rental mit gespeicherten Feldwerten: {{ $rentalWithFields->title }}</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $fieldValues = \App\Helpers\DynamicRentalFields::getFieldValuesForRental($rentalWithFields->id);
                                    $formattedValues = \App\Helpers\DynamicRentalFields::getFormattedFieldValuesForRental($rentalWithFields->id);
                                @endphp
                                
                                <h6>Gespeicherte Feldwerte ({{ count($fieldValues) }}):</h6>
                                @if(count($fieldValues) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Feldname</th>
                                                    <th>Wert</th>
                                                    <th>Typ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($fieldValues as $fieldName => $value)
                                                    <tr>
                                                        <td><code>{{ $fieldName }}</code></td>
                                                        <td>
                                                            @if(is_array($value))
                                                                <span class="badge bg-info">{{ implode(', ', $value) }}</span>
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </td>
                                                        <td><small class="text-muted">{{ gettype($value) }}</small></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">Keine Feldwerte gespeichert.</p>
                                @endif
                                
                                <h6 class="mt-4">Formatted Values ({{ $formattedValues->count() }}):</h6>
                                @if($formattedValues->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Feld</th>
                                                    <th>Template</th>
                                                    <th>Wert</th>
                                                    <th>Formatiert</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($formattedValues as $value)
                                                    <tr>
                                                        <td>{{ $value['field_label'] }}</td>
                                                        <td><small>{{ $value['template_name'] }}</small></td>
                                                        <td>{{ $value['value'] }}</td>
                                                        <td>{{ $value['formatted_value'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">Keine formatierten Werte verfügbar.</p>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    <div class="card">
                        <div class="card-header">
                            <h5>Speicherungstest</h5>
                        </div>
                        <div class="card-body">
                            @if($rentalWithFields)
                                <h6>Test der Speicherung für Rental ID: {{ $rentalWithFields->id }}</h6>
                                
                                @php
                                    // Test-Daten für verschiedene Feldtypen
                                    $testValues = [
                                        'test_text' => 'Test Text Value',
                                        'test_number' => 42,
                                        'test_checkbox' => ['option1', 'option3'],
                                        'test_select' => 'option2',
                                        'test_date' => '2024-01-15'
                                    ];
                                    
                                    // Speichere Test-Werte
                                    \App\Helpers\DynamicRentalFields::saveFieldValues($rentalWithFields->id, $testValues);
                                    
                                    // Lade gespeicherte Werte
                                    $savedValues = \App\Helpers\DynamicRentalFields::getFieldValuesForRental($rentalWithFields->id);
                                @endphp
                                
                                <div class="alert alert-success">
                                    <strong>Test-Werte gespeichert!</strong>
                                    <br>
                                    <small>Test-Werte wurden für Rental ID {{ $rentalWithFields->id }} gespeichert.</small>
                                </div>
                                
                                <h6>Gespeicherte Test-Werte:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Feldname</th>
                                                <th>Gespeicherter Wert</th>
                                                <th>Typ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($savedValues as $fieldName => $value)
                                                <tr>
                                                    <td><code>{{ $fieldName }}</code></td>
                                                    <td>
                                                        @if(is_array($value))
                                                            <span class="badge bg-info">{{ implode(', ', $value) }}</span>
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </td>
                                                    <td><small class="text-muted">{{ gettype($value) }}</small></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-3">
                                    <a href="{{ route('test.dynamic.fields.storage') }}" class="btn btn-primary btn-sm">
                                        <i class="ti ti-refresh me-2"></i>Test wiederholen
                                    </a>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <strong>Kein Rental mit Feldwerten gefunden</strong>
                                    <br>
                                    <small>Erstellen Sie zuerst ein Rental mit dynamischen Feldern.</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection 