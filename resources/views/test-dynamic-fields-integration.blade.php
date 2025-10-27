@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Dynamic Fields Integration Test')

@section('content')
    <section class="section-py">
        <div class="container">
            <h1 class="mb-4">Dynamic Fields Integration Test</h1>
            
            <div class="row">
                <div class="col-12 mb-4">
                    <h3>Test der Integration zwischen Kategorieauswahl und dynamischen Feldern</h3>
                    <p class="text-muted">Test der Livewire-Events zwischen der Kategorieauswahl und der dynamischen Rental-Form.</p>
                    
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        <strong>Anleitung:</strong> Wählen Sie eine Kategorie aus, um zu sehen, wie die dynamischen Felder automatisch geladen werden.
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>1. Kategorieauswahl</h5>
                        </div>
                        <div class="card-body">
                            <!-- Livewire Kategorieauswahl Komponente -->
                            @livewire('vendor.rentals.categories', [
                                'initial-data' => [
                                    'category_id' => null
                                ]
                            ])
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>2. Dynamische Felder</h5>
                        </div>
                        <div class="card-body">
                            <!-- Livewire Dynamic Rental Form Komponente -->
                            @livewire('vendor.dynamic-rental-form', [
                                'rental' => null, 
                                'categoryId' => null
                            ])
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>3. Event-Log</h5>
                        </div>
                        <div class="card-body">
                            <div id="event-log" class="bg-light p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                                <small class="text-muted">Event-Log wird hier angezeigt...</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const eventLog = document.getElementById('event-log');
            
            function addLogEntry(message, type = 'info') {
                const timestamp = new Date().toLocaleTimeString();
                const logEntry = document.createElement('div');
                logEntry.className = `mb-2 p-2 rounded ${type === 'error' ? 'bg-danger text-white' : type === 'success' ? 'bg-success text-white' : 'bg-light'}`;
                logEntry.innerHTML = `<small><strong>[${timestamp}]</strong> ${message}</small>`;
                
                eventLog.appendChild(logEntry);
                eventLog.scrollTop = eventLog.scrollHeight;
            }

            // Listen for Livewire events
            document.addEventListener('livewire:initialized', function () {
                addLogEntry('Livewire initialisiert', 'success');

                // Category selection events
                Livewire.on('categorySelected', (category) => {
                    addLogEntry(`Kategorie ausgewählt: ${category.name} (ID: ${category.id})`, 'success');
                });

                Livewire.on('categoryRemoved', () => {
                    addLogEntry('Kategorie entfernt', 'info');
                });

                // Dynamic fields events
                Livewire.on('categoryUpdated', (event) => {
                    addLogEntry(`Kategorie aktualisiert: ID ${event.category_id}, ${event.field_count} Felder geladen`, 'success');
                });

                Livewire.on('categoryCleared', () => {
                    addLogEntry('Kategorie gelöscht - Formular zurückgesetzt', 'info');
                });

                Livewire.on('fieldsLoaded', (event) => {
                    addLogEntry(`${event.fieldCount} dynamische Felder geladen`, 'success');
                });

                Livewire.on('fieldValuesStored', (event) => {
                    addLogEntry(`Feldwerte zwischengespeichert für Kategorie ${event.category_id}`, 'success');
                });

                Livewire.on('fieldValuesSaved', (event) => {
                    addLogEntry(`Feldwerte gespeichert für Rental ${event.rental_id}`, 'success');
                });

                Livewire.on('fieldValuesAutoSaved', (event) => {
                    addLogEntry(`Feldwerte automatisch gespeichert für Rental ${event.rental_id}`, 'success');
                });

                Livewire.on('fieldValuesCleared', () => {
                    addLogEntry('Feldwerte gelöscht', 'info');
                });

                Livewire.on('validationFailed', (event) => {
                    addLogEntry(`Validierungsfehler: ${Object.keys(event.errors).length} Fehler`, 'error');
                });
            });

            // Listen for custom events
            window.addEventListener('categorySelectedFromLivewire', function (event) {
                addLogEntry(`Custom Event: Kategorie ${event.detail.name} ausgewählt`, 'success');
            });

            // Clear log button
            const clearLogBtn = document.createElement('button');
            clearLogBtn.className = 'btn btn-sm btn-outline-secondary mt-2';
            clearLogBtn.innerHTML = '<i class="ti ti-trash me-1"></i>Log löschen';
            clearLogBtn.onclick = function() {
                eventLog.innerHTML = '<small class="text-muted">Event-Log gelöscht...</small>';
            };
            eventLog.parentNode.appendChild(clearLogBtn);
        });
    </script>
@endsection 