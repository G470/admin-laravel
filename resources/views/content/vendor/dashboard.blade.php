@extends('layouts/contentNavbarLayout')

@section('title', 'Vendor Dashboard')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}">
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('assets/js/dashboards-analytics.js')}}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Übersicht Vermietungen</h5>
                </div>
                <div class="card-body">
                    <div id="revenueChart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Statistik</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex flex-column align-items-start">
                            <h6 class="mb-1">Aktive Objekte</h6>
                            <small class="text-muted">Gesamt verfügbar</small>
                        </div>
                        <h4 class="mb-0">12</h4>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex flex-column align-items-start">
                            <h6 class="mb-1">Anfragen</h6>
                            <small class="text-muted">Diesen Monat</small>
                        </div>
                        <h4 class="mb-0">35</h4>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex flex-column align-items-start">
                            <h6 class="mb-1">Umsatz</h6>
                            <small class="text-muted">Diesen Monat</small>
                        </div>
                        <h4 class="mb-0">€2.450</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title mb-0">Neueste Anfragen</h5>
                    <a href="{{ route('vendor-messages') }}" class="btn btn-sm btn-primary">Alle anzeigen</a>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @for ($i = 0; $i < 5; $i++)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-0">Max Mustermann</h6>
                                    <small class="text-muted">Ferienhaus am See</small>
                                </div>
                                <small class="text-muted">vor {{ rand(1, 3) }}
                                    {{ rand(1, 3) == 1 ? 'Stunde' : 'Stunden' }}</small>
                            </li>
                        @endfor
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title mb-0">Aufgaben</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="task1">
                                <label class="form-check-label" for="task1">Neue Bilder für Ferienhaus hochladen</label>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="task2">
                                <label class="form-check-label" for="task2">Preise für Sommersaison aktualisieren</label>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="task3">
                                <label class="form-check-label" for="task3">Auf Anfragen antworten</label>
                            </div>
                        </li>
                        <li class="list-group-item px-0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="task4">
                                <label class="form-check-label" for="task4">Öffnungszeiten aktualisieren</label>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection