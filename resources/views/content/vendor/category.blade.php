@extends('layouts/contentNavbarLayout')

@section('title', 'Kategorie-Ansicht')

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Vendor / <a href="{{ route('vendor-rentals') }}">Vermietungsobjekte</a> /</span>
        Kategorie
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Objekte in Kategorie "Ferienhaus"</h5>
                    <div>
                        <a href="{{ route('vendor-rental-create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i> Neues Objekt in dieser Kategorie
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped border-top">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Standort</th>
                                    <th>Preis</th>
                                    <th>Status</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 5; $i++)
                                    <tr>
                                        <td>#{{ 1000 + $i }}</td>
                                        <td>
                                            <div class="d-flex justify-content-start align-items-center">
                                                <div class="avatar-wrapper">
                                                    <div class="avatar me-2">
                                                        <img src="{{asset('assets/img/backgrounds/' . $i . '.jpg')}}"
                                                            alt="Objekt Bild" class="rounded">
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <a href="{{ route('vendor-rental-edit', ['id' => $i]) }}"
                                                        class="text-body text-truncate fw-semibold">
                                                        Ferienhaus am See {{ $i }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>München</td>
                                        <td>{{ 80 + ($i * 10) }},00 € / Tag</td>
                                        <td>
                                            <span class="badge bg-label-success">Aktiv</span>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item"
                                                        href="{{ route('vendor-rental-edit', ['id' => $i]) }}">
                                                        <i class="ti ti-edit me-1"></i> Bearbeiten
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('vendor-rental-preview', ['id' => $i]) }}">
                                                        <i class="ti ti-eye me-1"></i> Vorschau
                                                    </a>
                                                    <a class="dropdown-item" href="javascript:void(0);">
                                                        <i class="ti ti-trash me-1"></i> Löschen
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Kategorie-Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Kategorie-Name</label>
                        <p class="form-control-static">Ferienhaus</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Beschreibung</label>
                        <p class="form-control-static">Komplette Häuser zur Vermietung für Urlaub und Freizeit.</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Aktive Objekte</label>
                        <p class="form-control-static">5 von 5 aktiv</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durchschnittlicher Tagespreis</label>
                        <p class="form-control-static">110,00 €</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Buchungsstatistik</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Buchungen (letzte 30 Tage)</label>
                        <p class="form-control-static">12</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Durchschnittliche Buchungsdauer</label>
                        <p class="form-control-static">4,2 Tage</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Auslastung (letzte 30 Tage)</label>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 65%" aria-valuenow="65"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted">65% Auslastung</small>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Umsatz (letzte 30 Tage)</label>
                        <p class="form-control-static">5.280,00 €</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection