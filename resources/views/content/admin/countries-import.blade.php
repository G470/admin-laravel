@extends('layouts/contentNavbarLayout')

@php
    $countryData = $country ?? null;
    $countryId = is_object($countryData) ? $countryData->id : (is_array($countryData) ? $countryData['id'] : null);
    $countryCode = is_object($countryData) ? $countryData->code : (is_array($countryData) ? $countryData['code'] : null);
    $countryName = is_object($countryData) ? $countryData->name : (is_array($countryData) ? $countryData['name'] : null);
@endphp

@section('title', 'Daten Import - ' . ($countryName ?? 'Unbekanntes Land'))


@section('page-script')
    <script>
        // Define URLs at the top of the script
        @if($countryId)
            const PREVIEW_URL = {!! json_encode(route('admin.countries.import.preview', $countryId)) !!};
            const EXECUTE_URL = {!! json_encode(route('admin.countries.import.execute', $countryId)) !!};
            const STATS_URL = {!! json_encode(route('admin.countries.import.stats', $countryId)) !!};
            const COUNTRY_ID = {{ $countryId }};
            const COUNTRY_CODE = {!! json_encode($countryCode) !!};
            const COUNTRY_NAME = {!! json_encode($countryName) !!};
        @else
            console.error('Country data is not available');
            alert('Fehler: Land-Parameter nicht verfügbar. Bitte laden Sie die Seite neu.');
        @endif

        document.addEventListener('DOMContentLoaded', function () {
            // Check if URLs are properly defined
            if (typeof PREVIEW_URL === 'undefined' || typeof EXECUTE_URL === 'undefined' || typeof STATS_URL === 'undefined') {
                console.error('Required URLs are not defined');
                showError('Fehler: URLs nicht verfügbar. Bitte laden Sie die Seite neu.');
                return;
            }

            console.log('Country ID:', COUNTRY_ID);
            console.log('Country Code:', COUNTRY_CODE);
            console.log('Preview URL:', PREVIEW_URL);
            console.log('Execute URL:', EXECUTE_URL);
            console.log('Stats URL:', STATS_URL);

            const importForm = document.getElementById('import-form');
            const previewContainer = document.getElementById('preview-container');
            const executeButton = document.getElementById('execute-import');
            let currentFilePath = null;
            let currentOptions = {};

            // Initialize file upload
            const fileInput = document.getElementById('import-file');
            const uploadButton = document.getElementById('upload-button');

            if (!fileInput || !uploadButton) {
                console.error('File input or upload button not found');
                showError('Fehler: Upload-Elemente nicht gefunden.');
                return;
            }

            fileInput.addEventListener('change', function () {
                if (this.files.length > 0) {
                    const fileName = this.files[0].name;
                    const fileExtension = fileName.split('.').pop().toLowerCase();
                    
                    uploadButton.disabled = false;
                    document.getElementById('file-selected').textContent = fileName;
                    document.getElementById('file-info-display').classList.remove('d-none');
                    
                    // Auto-configure settings for TXT files
                    if (fileExtension === 'txt') {
                        document.getElementById('delimiter').value = 'tab';
                        document.getElementById('has_header').checked = false;
                        showToast('info', 'TXT-Datei erkannt: Tabulator-Trennzeichen und "Keine Header" automatisch ausgewählt.');
                    }
                } else {
                    uploadButton.disabled = true;
                    document.getElementById('file-info-display').classList.add('d-none');
                    currentFilePath = null;
                    previewContainer.classList.add('d-none');
                    executeButton.disabled = true;
                }
            });

            uploadButton.addEventListener('click', function () {
                const file = fileInput.files[0];
                if (!file) {
                    showError('Bitte wählen Sie eine Datei aus');
                    return;
                }

                const formData = new FormData();
                formData.append('import_file', file);
                formData.append('has_header', document.getElementById('has_header').checked);

                // Convert delimiter value
                let delimiter = document.getElementById('delimiter').value;
                if (delimiter === 'tab') {
                    delimiter = '\t';
                }
                formData.append('delimiter', delimiter);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                uploadButton.disabled = true;
                uploadButton.innerHTML = '<i class="ti ti-loader"></i> Lade hoch...';

                fetch(PREVIEW_URL, {
                    method: 'POST',
                    body: formData
                })
                    .then(response => {
                        // Check if response is JSON
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            return response.json();
                        } else {
                            // Handle HTML error responses
                            return response.text().then(text => {
                                throw new Error('Server returned HTML response instead of JSON. Check server logs.');
                            });
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            currentFilePath = data.file_path;
                            currentOptions = data.options;
                            showPreview(data.preview, data.file_info);
                            executeButton.disabled = false;
                            showSuccess('Datei erfolgreich hochgeladen und analysiert');
                        } else {
                            if (data.errors) {
                                // Handle validation errors
                                const errorMessages = Object.values(data.errors).flat();
                                showError('Validierungs-Fehler: ' + errorMessages.join(', '));
                            } else {
                                showError('Upload-Fehler: ' + (data.message || 'Unbekannter Fehler'));
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Upload error:', error);
                        showError('Upload-Fehler: ' + error.message);
                    })
                    .finally(() => {
                        uploadButton.disabled = false;
                        uploadButton.innerHTML = '<i class="ti ti-upload me-1"></i>Datei hochladen';
                    });
            });

            // Execute import
            executeButton.addEventListener('click', function () {
                if (!currentFilePath) {
                    showError('Keine Datei ausgewählt');
                    return;
                }

                if (!EXECUTE_URL) {
                    showError('Execute URL nicht verfügbar');
                    return;
                }

                executeButton.disabled = true;
                executeButton.innerHTML = '<i class="ti ti-loader"></i> Importiere...';

                // Convert delimiter if needed
                let executeDelimiter = currentOptions.delimiter;
                if (executeDelimiter === 'tab') {
                    executeDelimiter = '\t';
                }

                fetch(EXECUTE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        file_path: currentFilePath,
                        has_header: currentOptions.has_header,
                        delimiter: executeDelimiter
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccess('Import erfolgreich: ' + data.message);
                            showImportStats(data.stats);
                            // Reload stats
                            loadCurrentStats();
                        } else {
                            showError('Import-Fehler: ' + data.message);
                        }
                    })
                    .catch(error => {
                        showError('Netzwerk-Fehler: ' + error.message);
                    })
                    .finally(() => {
                        executeButton.disabled = false;
                        executeButton.innerHTML = '<i class="ti ti-upload me-1"></i>Import ausführen';
                    });
            });

            function showPreview(data, fileInfo) {
                if (!data || data.length === 0) {
                    showError('Keine Daten in der Datei gefunden');
                    return;
                }

                const previewTable = document.getElementById('preview-table');
                const fileInfoDiv = document.getElementById('file-info');
                const fileInfoContainer = document.getElementById('file-info-container');

                // Show file info
                fileInfoDiv.innerHTML = `
                                    <div class="alert alert-info">
                                        <h6><i class="ti ti-file me-2"></i>Datei-Informationen</h6>
                                        <ul class="mb-0">
                                            <li><strong>Format:</strong> ${fileInfo.extension.toUpperCase()}</li>
                                            <li><strong>Größe:</strong> ${fileInfo.size_formatted}</li>
                                            <li><strong>Vorschau:</strong> ${data.length} von ca. ${Math.ceil(fileInfo.size / 100)} Datensätzen</li>
                                        </ul>
                                    </div>
                                `;

                // Build preview table
                if (data.length > 0) {
                    const headers = Object.keys(data[0]);
                    let tableHtml = '<div class="table-responsive"><table class="table table-sm"><thead class="table-light"><tr>';

                    headers.forEach(header => {
                        tableHtml += `<th>${header}</th>`;
                    });
                    tableHtml += '</tr></thead><tbody>';

                    data.forEach(row => {
                        tableHtml += '<tr>';
                        headers.forEach(header => {
                            const value = row[header] || '';
                            tableHtml += `<td><small>${value}</small></td>`;
                        });
                        tableHtml += '</tr>';
                    });

                    tableHtml += '</tbody></table></div>';
                    previewTable.innerHTML = tableHtml;
                }

                fileInfoContainer.classList.remove('d-none');
                previewContainer.classList.remove('d-none');
            }

            function showImportStats(stats) {
                const statsContainer = document.getElementById('import-stats');

                statsContainer.innerHTML = `
                                                                                        <div class="alert alert-success">
                                                                                            <h6><i class="ti ti-check me-2"></i>Import-Ergebnis</h6>
                                                                                            <div class="row">
                                                                                                <div class="col-md-3">
                                                                                                    <strong>Verarbeitet:</strong> ${stats.total_rows.toLocaleString()}
                                                                                                </div>
                                                                                                <div class="col-md-3">
                                                                                                    <strong>Eingefügt:</strong> ${stats.inserted_rows.toLocaleString()}
                                                                                                </div>
                                                                                                <div class="col-md-3">
                                                                                                    <strong>Übersprungen:</strong> ${stats.skipped_rows.toLocaleString()}
                                                                                                </div>
                                                                                                <div class="col-md-3">
                                                                                                    <strong>Tabelle:</strong> ${stats.table_name}
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    `;

                statsContainer.classList.remove('d-none');
            }

            function showSuccess(message) {
                showToast('success', message);
            }

            function showError(message) {
                showToast('error', message);
            }

            function showToast(type, message) {
                // Use the global toast function if available
                if (typeof window.showToast === 'function') {
                    window.showToast(type, message);
                } else if (typeof toastr !== 'undefined') {
                    // Use toastr if available
                    if (type === 'error') {
                        toastr.error(message);
                    } else if (type === 'success') {
                        toastr.success(message);
                    } else {
                        toastr.info(message);
                    }
                } else {
                    // Fallback to console and alert
                    console.log(`${type.toUpperCase()}: ${message}`);
                    if (type === 'error') {
                        alert('Fehler: ' + message);
                    } else {
                        alert('Erfolg: ' + message);
                    }
                }
            }

            // Load current statistics
            function loadCurrentStats() {
                if (!STATS_URL) {
                    console.error('Stats URL not available');
                    return;
                }

                fetch(STATS_URL)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateStatsDisplay(data.stats);
                        }
                    })
                    .catch(error => console.error('Error loading stats:', error));
            }

            function updateStatsDisplay(stats) {
                document.getElementById('stat-total').textContent = stats.total_records.toLocaleString();
                document.getElementById('stat-cities').textContent = stats.unique_cities.toLocaleString();
                document.getElementById('stat-regions').textContent = stats.unique_regions.toLocaleString();
                document.getElementById('stat-coordinates').textContent = stats.records_with_coordinates.toLocaleString();
                document.getElementById('stat-population').textContent = stats.records_with_population.toLocaleString();

                if (stats.last_import) {
                    document.getElementById('stat-last-import').textContent = new Date(stats.last_import).toLocaleDateString('de-DE');
                }
            }

            // Initialize stats
            loadCurrentStats();
        });
    </script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.countries.index') }}">Länder-Verwaltung</a>
                        </li>
                        <li class="breadcrumb-item active">Daten Import - {{ $countryName ?? 'Unbekanntes Land' }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">
                                @if($countryCode && file_exists(public_path('assets/img/flags/' . strtolower($countryCode) . '.svg')))
                                    <img src="{{ asset('assets/img/flags/' . strtolower($countryCode) . '.svg') }}"
                                        alt="{{ $countryName }}" class="me-2" style="width: 24px; height: 18px;">
                                @endif
                                <i class="ti ti-upload text-primary me-2"></i>Daten Import -
                                {{ $countryName ?? 'Unbekanntes Land' }}
                            </h5>
                            <small class="text-muted">Import von Postleitzahlen-Daten für
                                {{ $countryName ?? 'Unbekanntes Land' }}
                                ({{ $countryCode ?? 'N/A' }})</small>
                        </div>
                        <div>
                            <span class="badge bg-label-primary">{{ $countryCode ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Statistics -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="ti ti-chart-bar me-2"></i>Aktuelle Statistiken
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial bg-label-primary rounded">
                                            <i class="ti ti-database"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-muted">Datensätze</small>
                                        <h6 class="mb-0" id="stat-total">{{ number_format($stats['total_records'] ?? 0) }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial bg-label-info rounded">
                                            <i class="ti ti-map-pin"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-muted">Städte</small>
                                        <h6 class="mb-0" id="stat-cities">{{ number_format($stats['unique_cities'] ?? 0) }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial bg-label-success rounded">
                                            <i class="ti ti-world"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-muted">Regionen</small>
                                        <h6 class="mb-0" id="stat-regions">
                                            {{ number_format($stats['unique_regions'] ?? 0) }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial bg-label-warning rounded">
                                            <i class="ti ti-location"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-muted">Mit GPS</small>
                                        <h6 class="mb-0" id="stat-coordinates">
                                            {{ number_format($stats['records_with_coordinates'] ?? 0) }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial bg-label-danger rounded">
                                            <i class="ti ti-users"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-muted">Mit Bevölkerung</small>
                                        <h6 class="mb-0" id="stat-population">
                                            {{ number_format($stats['records_with_population'] ?? 0) }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial bg-label-secondary rounded">
                                            <i class="ti ti-clock"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-muted">Letzter Import</small>
                                        <h6 class="mb-0 small" id="stat-last-import">
                                            {{ isset($stats['last_import']) && $stats['last_import'] ? \Carbon\Carbon::parse($stats['last_import'])->format('d.m.Y') : 'Nie' }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Form -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="ti ti-file-upload me-2"></i>Neuen Import starten
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="import-form">
                            <!-- Import Options -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="has_header" checked>
                                        <label class="form-check-label" for="has_header">
                                            Datei hat Spaltenüberschriften
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="delimiter" class="form-label">CSV-Trennzeichen</label>
                                    <select class="form-select" id="delimiter">
                                        <option value=",">Komma (,)</option>
                                        <option value=";">Semikolon (;)</option>
                                        <option value="tab">Tabulator</option>
                                    </select>
                                </div>
                            </div>

                            <!-- File Upload -->
                            <div class="card mb-3">
                                <div class="card-body text-center">
                                    <div class="avatar avatar-lg mb-3">
                                        <span class="avatar-initial bg-label-primary rounded">
                                            <i class="ti ti-upload" style="font-size: 2rem;"></i>
                                        </span>
                                    </div>
                                    <h5 class="mb-3">Datei auswählen</h5>
                                    <input type="file" id="import-file" class="form-control mb-3"
                                        accept=".csv,.xlsx,.xls,.txt" style="max-width: 400px; margin: 0 auto;">
                                    <div id="file-info-display" class="d-none mb-3">
                                        <small class="text-muted">Ausgewählte Datei: <strong
                                                id="file-selected"></strong></small>
                                    </div>
                                    <button type="button" id="upload-button" class="btn btn-primary" disabled>
                                        <i class="ti ti-upload me-1"></i>Datei hochladen
                                    </button>
                                    <p class="text-muted mt-3 mb-0">
                                        Unterstützte Formate: CSV, XLSX, XLS, TXT<br>
                                        Maximale Größe: 50 MB
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Info -->
        <div class="row mt-4 d-none" id="file-info-container">
            <div class="col-12">
                <div id="file-info"></div>
            </div>
        </div>

        <!-- Preview Container -->
        <div class="row mt-4 d-none" id="preview-container">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="ti ti-eye me-2"></i>Datenvorschau
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="preview-table"></div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-primary" id="execute-import" disabled>
                                <i class="ti ti-upload me-1"></i>Import ausführen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Import Stats -->
        <div class="row mt-4 d-none" id="import-stats">
            <div class="col-12">
                <!-- Filled by JavaScript -->
            </div>
        </div>

        <!-- Guidelines -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-info">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <i class="ti ti-info-circle text-info me-3 mt-1" style="font-size: 1.2rem;"></i>
                            <div>
                                <h6 class="mb-2 text-info">Import-Richtlinien</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="mb-0 small text-muted">
                                            <li><strong>Erforderliche Spalten:</strong> postal_code, city</li>
                                            <li><strong>Optionale Spalten:</strong> sub_city, region, latitude, longitude,
                                                population</li>
                                            <li><strong>Encoding:</strong> UTF-8 empfohlen</li>
                                            <li><strong>Koordinaten:</strong> Dezimalgrad-Format (z.B. 52.5200, 13.4050)
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="mb-0 small text-muted">
                                            <li><strong>Maximale Dateigröße:</strong> 50 MB</li>
                                            <li><strong>Duplikate:</strong> Werden automatisch übersprungen</li>
                                            <li><strong>Performance:</strong> Import erfolgt in 1000er-Batches</li>
                                            <li><strong>Sicherheit:</strong> Temporäre Dateien werden automatisch gelöscht
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Weitere Aktionen</h6>
                                <small class="text-muted">Verwalten Sie die vorhandenen Daten</small>
                            </div>
                            <div class="btn-group">
                                @if(isset($stats['table_exists']) && ($stats['table_exists'] ?? false) && $countryId)
                                    <a href="{{ route('admin.countries.data.view', $countryId) }}" class="btn btn-info">
                                        <i class="ti ti-eye me-1"></i>Daten anzeigen
                                    </a>
                                    <a href="{{ route('admin.countries.data.export', $countryId) }}" class="btn btn-success">
                                        <i class="ti ti-download me-1"></i>Exportieren
                                    </a>
                                    <button type="button" class="btn btn-warning"
                                        onclick="if(confirm('Sind Sie sicher, dass Sie alle Daten löschen möchten?')) { 
                                                                                                                                                                      fetch('{{ route('admin.countries.data.clear', $countryId) }}', {
                                                                                                                                                                        method: 'POST',
                                                                                                                                                                        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')}
                                                                                                                                                                      }).then(() => location.reload());
                                                                                                                                                                    }">
                                        <i class="ti ti-trash-x me-1"></i>Daten löschen
                                    </button>
                                @endif
                                <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Zurück
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection