@extends('layouts/contentNavbarLayout')

@section('title', 'Guthaben')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Vendor /</span> Guthaben
    </h4>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">Ihr aktuelles Guthaben</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title text-white">Verfügbares Guthaben</h5>
                            <h2 class="mb-3">1.250,00 €</h2>
                            <p class="card-text">Letzte Aktualisierung: 05.10.2023</p>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mb-3">Transaktionshistorie</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Beschreibung</th>
                            <th>Betrag</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>05.10.2023</td>
                            <td>Auszahlung auf Bankkonto</td>
                            <td class="text-danger">-500,00 €</td>
                            <td><span class="badge bg-success">Abgeschlossen</span></td>
                        </tr>
                        <tr>
                            <td>30.09.2023</td>
                            <td>Buchungseinnahme #4528</td>
                            <td class="text-success">+850,00 €</td>
                            <td><span class="badge bg-success">Abgeschlossen</span></td>
                        </tr>
                        <tr>
                            <td>15.09.2023</td>
                            <td>Buchungseinnahme #4491</td>
                            <td class="text-success">+900,00 €</td>
                            <td><span class="badge bg-success">Abgeschlossen</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title">Auszahlung beantragen</h5>
        </div>
        <div class="card-body">
            <form>
                <div class="mb-3">
                    <label class="form-label" for="amount">Auszahlungsbetrag (€)</label>
                    <input type="number" class="form-control" id="amount" placeholder="Betrag eingeben" min="100"
                        max="1250">
                    <small class="text-muted">Mindestbetrag: 100,00 €</small>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="bank-account">Bankkonto</label>
                    <select class="select2 form-select" id="bank-account">
                        <option value="1">DE89 3704 0044 0532 0130 00 (Standardkonto)</option>
                        <option value="2">DE27 5001 0517 5843 2190 86</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Auszahlung beantragen</button>
            </form>
        </div>
    </div>
@endsection