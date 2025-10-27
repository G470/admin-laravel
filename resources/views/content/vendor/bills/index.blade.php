@extends('layouts/contentNavbarLayout')

@section('title', 'Rechnungen')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            // Initialisierung von Select2
            $('.select2').select2();

            // Filter-Funktion
            $('#bill-status-filter').change(function () {
                var status = $(this).val();
                if (status === 'all') {
                    $('.bill-row').show();
                } else {
                    $('.bill-row').hide();
                    $('.bill-row[data-status="' + status + '"]').show();
                }
            });

            // Download-Beispiel
            $('.download-invoice').click(function (e) {
                e.preventDefault();
                alert('Die Rechnung wird heruntergeladen...');
            });
        });
    </script>
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Vendor /</span> Rechnungen
    </h4>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Rechnungen</h5>
            <div class="d-flex align-items-center">
                <select id="bill-status-filter" class="form-select me-2" style="width: 150px;">
                    <option value="all">Alle Status</option>
                    <option value="paid">Bezahlt</option>
                    <option value="pending">Ausstehend</option>
                    <option value="overdue">Überfällig</option>
                </select>
                <button class="btn btn-primary">
                    <i class="ti ti-plus me-1"></i>
                    Neue Rechnung
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Datum</th>
                            <th>Beschreibung</th>
                            <th>Betrag</th>
                            <th>Status</th>
                            <th>Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bill-row" data-status="paid">
                            <td>INV-2023-001</td>
                            <td>05.10.2023</td>
                            <td>Mitgliedsbeitrag Oktober 2023</td>
                            <td>29,99 €</td>
                            <td><span class="badge bg-success">Bezahlt</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="javascript:void(0);" class="dropdown-item download-invoice">
                                            <i class="ti ti-download me-1"></i> Download
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item">
                                            <i class="ti ti-eye me-1"></i> Ansehen
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="bill-row" data-status="paid">
                            <td>INV-2023-002</td>
                            <td>05.09.2023</td>
                            <td>Mitgliedsbeitrag September 2023</td>
                            <td>29,99 €</td>
                            <td><span class="badge bg-success">Bezahlt</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="javascript:void(0);" class="dropdown-item download-invoice">
                                            <i class="ti ti-download me-1"></i> Download
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item">
                                            <i class="ti ti-eye me-1"></i> Ansehen
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="bill-row" data-status="pending">
                            <td>INV-2023-003</td>
                            <td>05.11.2023</td>
                            <td>Mitgliedsbeitrag November 2023</td>
                            <td>29,99 €</td>
                            <td><span class="badge bg-label-warning">Ausstehend</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="javascript:void(0);" class="dropdown-item download-invoice">
                                            <i class="ti ti-download me-1"></i> Download
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item">
                                            <i class="ti ti-eye me-1"></i> Ansehen
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item">
                                            <i class="ti ti-credit-card me-1"></i> Bezahlen
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="bill-row" data-status="overdue">
                            <td>INV-2023-004</td>
                            <td>15.08.2023</td>
                            <td>Zusatzleistung: Premium-Listung</td>
                            <td>49,99 €</td>
                            <td><span class="badge bg-danger">Überfällig</span></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="javascript:void(0);" class="dropdown-item download-invoice">
                                            <i class="ti ti-download me-1"></i> Download
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item">
                                            <i class="ti ti-eye me-1"></i> Ansehen
                                        </a>
                                        <a href="javascript:void(0);" class="dropdown-item">
                                            <i class="ti ti-credit-card me-1"></i> Bezahlen
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>Seite 1 von 1</div>
                <div>
                    <button class="btn btn-outline-secondary btn-sm me-2" disabled>Zurück</button>
                    <button class="btn btn-outline-secondary btn-sm" disabled>Weiter</button>
                </div>
            </div>
        </div>
    </div>
@endsection