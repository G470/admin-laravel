@extends('layouts/contentNavbarLayout')

@section('title', 'Daten Ansicht - ' . $country->name)

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/datatables-responsive/datatables.responsive.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.js')}}"></script>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize DataTable
            const table = $('#postal-codes-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.countries.data.table', $country) }}",
                    data: function (d) {
                        d.search = d.search.value;
                        d.region = $('#region-filter').val();
                        d.has_coordinates = $('#coordinates-filter').is(':checked');
                        d.has_population = $('#population-filter').is(':checked');
                        d.sort_field = d.columns[d.order[0].column].data;
                        d.sort_direction = d.order[0].dir;
                        d.per_page = d.length;
                    }
                },
                columns: [
                    { data: 'postal_code', name: 'postal_code' },
                    { data: 'city', name: 'city' },
                    { data: 'sub_city', name: 'sub_city', orderable: false },
                    { data: 'region', name: 'region' },
                    {
                        data: 'coordinates',
                        name: 'coordinates',
                        orderable: false,
                        render: function (data, type, row) {
                            if (row.latitude && row.longitude) {
                                return `<small class="text-success">
                                    <i class="ti ti-location me-1"></i>
                                    ${parseFloat(row.latitude).toFixed(4)}, ${parseFloat(row.longitude).toFixed(4)}
                                </small>`;
                            }
                            return '<small class="text-muted">—</small>';
                        }
                    },
                    {
                        data: 'population',
                        name: 'population',
                        render: function (data, type, row) {
                            if (row.population) {
                                return parseInt(row.population).toLocaleString();
                            }
                            return '<small class="text-muted">—</small>';
                        }
                    }
                ],
                order: [[0, 'asc']],
                pageLength: 25,
                lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Alle"]],
                language: {
                    url: '/assets/vendor/libs/datatables-bs5/i18n/de-DE.json'
                },
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            });

            // Filter event handlers
            $('#region-filter, #coordinates-filter, #population-filter').on('change', function () {
                table.ajax.reload();
            });

            // Export function
            window.exportData = function () {
                window.location.href = "{{ route('admin.countries.data.export', $country) }}";
            };

            // Clear data function
            window.clearData = function () {
                if (confirm('Sind Sie sicher, dass Sie alle Postleitzahlen-Daten für {{ $country->name }} löschen möchten?')) {
                    fetch("{{ route('admin.countries.data.clear', $country) }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Daten wurden erfolgreich gelöscht.');
                                location.reload();
                            } else {
                                alert('Fehler beim Löschen: ' + data.message);
                            }
                        })
                        .catch(error => {
                            alert('Netzwerk-Fehler: ' + error.message);
                        });
                }
            };
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
                        <li class="breadcrumb-item active">Daten Ansicht - {{ $country->name }}</li>
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
                                @if(file_exists(public_path('assets/img/flags/' . strtolower($country->code) . '.svg')))
                                    <img src="{{ asset('assets/img/flags/' . strtolower($country->code) . '.svg') }}"
                                        alt="{{ $country->name }}" class="me-2" style="width: 24px; height: 18px;">
                                @endif
                                <i class="ti ti-eye text-primary me-2"></i>Daten Ansicht - {{ $country->name }}
                            </h5>
                            <small class="text-muted">Postleitzahlen-Daten für {{ $country->name }}
                                ({{ $country->code }})</small>
                        </div>
                        <div>
                            <span class="badge bg-label-primary">{{ $country->code }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($stats['table_exists'])
            <!-- Statistics Overview -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="ti ti-chart-bar me-2"></i>Statistik-Übersicht
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
                                            <h6 class="mb-0">{{ number_format($stats['total_records']) }}</h6>
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
                                            <h6 class="mb-0">{{ number_format($stats['unique_cities']) }}</h6>
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
                                            <h6 class="mb-0">{{ number_format($stats['unique_regions']) }}</h6>
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
                                            <h6 class="mb-0">{{ number_format($stats['records_with_coordinates']) }}</h6>
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
                                            <h6 class="mb-0">{{ number_format($stats['records_with_population']) }}</h6>
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
                                            <h6 class="mb-0 small">
                                                {{ $stats['last_import'] ? \Carbon\Carbon::parse($stats['last_import'])->format('d.m.Y H:i') : 'Nie' }}
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">
                                <i class="ti ti-table me-2"></i>Postleitzahlen-Daten
                            </h6>
                            <div class="btn-group">
                                <a href="{{ route('admin.countries.import', $country) }}" class="btn btn-primary btn-sm">
                                    <i class="ti ti-upload me-1"></i>Import
                                </a>
                                <button type="button" class="btn btn-success btn-sm" onclick="exportData()">
                                    <i class="ti ti-download me-1"></i>Export
                                </button>
                                <button type="button" class="btn btn-warning btn-sm" onclick="clearData()">
                                    <i class="ti ti-trash-x me-1"></i>Löschen
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filters -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="region-filter" class="form-label">Region filtern</label>
                                    <select class="form-select" id="region-filter">
                                        <option value="">Alle Regionen</option>
                                        <!-- Options will be populated by DataTable -->
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Datenfilter</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="coordinates-filter">
                                        <label class="form-check-label" for="coordinates-filter">
                                            Nur mit GPS-Koordinaten
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="population-filter">
                                        <label class="form-check-label" for="population-filter">
                                            Nur mit Bevölkerungsdaten
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Data Table -->
                            <div class="table-responsive">
                                <table class="table table-hover" id="postal-codes-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>PLZ</th>
                                            <th>Stadt</th>
                                            <th>Teilstadt</th>
                                            <th>Region</th>
                                            <th>Koordinaten</th>
                                            <th>Bevölkerung</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- No Data State -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="ti ti-database-off text-muted mb-3" style="font-size: 4rem;"></i>
                            <h5 class="text-muted">Keine Daten vorhanden</h5>
                            <p class="text-muted mb-4">Für {{ $country->name }} wurden noch keine Postleitzahlen-Daten
                                importiert.</p>
                            <a href="{{ route('admin.countries.import', $country) }}" class="btn btn-primary">
                                <i class="ti ti-upload me-1"></i>Ersten Import starten
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Aktionen</h6>
                                <small class="text-muted">Verwalten Sie die Daten für {{ $country->name }}</small>
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('admin.countries.import', $country) }}" class="btn btn-primary">
                                    <i class="ti ti-upload me-1"></i>Daten importieren
                                </a>
                                <a href="{{ route('admin.countries.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Zurück zur Übersicht
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection