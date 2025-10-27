@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Dynamic Fields Pending Test')

@section('content')
    <section class="section-py">
        <div class="container">
            <h1 class="mb-4">Dynamic Fields Pending Values Test</h1>
            
            <div class="row">
                <div class="col-12 mb-4">
                    <h3>Test der zwischengespeicherten dynamischen Feldwerte</h3>
                    <p class="text-muted">Test der Speicherung und des Ladens von zwischengespeicherten Feldwerten ohne Rental.</p>
                    
                    @php
                        // Finde eine Kategorie mit dynamischen Feldern
                        $categoryWithFields = \App\Models\Category::whereHas('rentalFieldTemplates.fields')
                            ->where('status', 'online')
                            ->first();
                    @endphp
                    
                    @if($categoryWithFields)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Kategorie: {{ $categoryWithFields->name }} (ID: {{ $categoryWithFields->id }})</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    $templates = \App\Helpers\DynamicRentalFields::getActiveTemplatesForCategory($categoryWithFields->id);
                                    $fields = \App\Helpers\DynamicRentalFields::getTemplateFieldsForCategory($categoryWithFields->id);
                                    $pendingValues = \App\Helpers\DynamicRentalFields::getPendingValues($categoryWithFields->id);
                                @endphp
                                
                                <h6>Verfügbare Templates ({{ $templates->count() }}):</h6>
                                <ul>
                                    @foreach($templates as $template)
                                        <li>{{ $template->name }} ({{ $template->fields->count() }} Felder)</li>
                                    @endforeach
                                </ul>
                                
                                <h6>Verfügbare Felder ({{ $fields->count() }}):</h6>
                                <ul>
                                    @foreach($fields as $field)
                                        <li>{{ $field->field_label }} ({{ $field->field_type }}) - {{ $field->field_name }}</li>
                                    @endforeach
                                </ul>
                                
                                <h6 class="mt-4">Zwischengespeicherte Werte ({{ count($pendingValues) }}):</h6>
                                @if(count($pendingValues) > 0)
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
                                                @foreach($pendingValues as $fieldName => $value)
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
                                    <p class="text-muted">Keine zwischengespeicherten Werte vorhanden.</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Test der Session-Speicherung</h5>
                            </div>
                            <div class="card-body">
                                @php
                                    // Test-Daten für verschiedene Feldtypen
                                    $testValues = [
                                        'test_text_field' => 'Test Text Value',
                                        'test_number_field' => 42,
                                        'test_checkbox_field' => ['option1', 'option3'],
                                        'test_select_field' => 'option2',
                                        'test_date_field' => '2024-01-15'
                                    ];
                                    
                                    // Speichere Test-Werte in Session
                                    $sessionKey = "pending_field_values_category_{$categoryWithFields->id}";
                                    session([$sessionKey => $testValues]);
                                    
                                    // Lade gespeicherte Werte
                                    $savedPendingValues = \App\Helpers\DynamicRentalFields::getPendingValues($categoryWithFields->id);
                                @endphp
                                
                                <div class="alert alert-success">
                                    <strong>Test-Werte in Session gespeichert!</strong>
                                    <br>
                                    <small>Test-Werte wurden für Kategorie ID {{ $categoryWithFields->id }} in der Session gespeichert.</small>
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
                                            @foreach($savedPendingValues as $fieldName => $value)
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
                                    <a href="{{ route('test.dynamic.fields.pending') }}" class="btn btn-primary btn-sm">
                                        <i class="ti ti-refresh me-2"></i>Test wiederholen
                                    </a>
                                    <button type="button" onclick="clearPendingValues()" class="btn btn-outline-danger btn-sm">
                                        <i class="ti ti-trash me-2"></i>Session löschen
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header">
                                <h5>Auto-Save Simulation</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">Simulation der automatischen Speicherung, wenn ein Rental erstellt wird:</p>
                                
                                @php
                                    // Simuliere ein neues Rental
                                    $newRentalId = 99999; // Dummy ID für Test
                                    
                                    // Versuche Auto-Save (wird nur funktionieren, wenn Session-Werte vorhanden sind)
                                    try {
                                        \App\Helpers\DynamicRentalFields::autoSavePendingValues($newRentalId, $categoryWithFields->id);
                                        $autoSaveSuccess = true;
                                    } catch (Exception $e) {
                                        $autoSaveSuccess = false;
                                        $autoSaveError = $e->getMessage();
                                    }
                                    
                                    // Prüfe, ob Session-Werte noch vorhanden sind
                                    $remainingPendingValues = \App\Helpers\DynamicRentalFields::getPendingValues($categoryWithFields->id);
                                @endphp
                                
                                @if($autoSaveSuccess)
                                    <div class="alert alert-success">
                                        <strong>Auto-Save erfolgreich simuliert!</strong>
                                        <br>
                                        <small>Zwischengespeicherte Werte wurden für Rental ID {{ $newRentalId }} gespeichert.</small>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <strong>Auto-Save Simulation fehlgeschlagen</strong>
                                        <br>
                                        <small>{{ $autoSaveError ?? 'Keine zwischengespeicherten Werte vorhanden.' }}</small>
                                    </div>
                                @endif
                                
                                <h6>Verbleibende Session-Werte ({{ count($remainingPendingValues) }}):</h6>
                                @if(count($remainingPendingValues) > 0)
                                    <ul>
                                        @foreach($remainingPendingValues as $fieldName => $value)
                                            <li><code>{{ $fieldName }}</code>: {{ is_array($value) ? implode(', ', $value) : $value }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">Keine verbleibenden Session-Werte (Auto-Save erfolgreich).</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <strong>Keine Kategorie mit dynamischen Feldern gefunden</strong>
                            <br>
                            <small>Erstellen Sie zuerst eine Kategorie mit dynamischen Feldern.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    
    <script>
        function clearPendingValues() {
            if (confirm('Möchten Sie alle zwischengespeicherten Werte löschen?')) {
                fetch('{{ route("test.dynamic.fields.pending.clear") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                }).then(() => {
                    window.location.reload();
                });
            }
        }
    </script>
@endsection 