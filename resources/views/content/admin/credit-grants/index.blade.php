@extends('layouts/contentNavbarLayout')

@section('title', 'Admin Credit Vergaben')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row">
            <div class="col-12 mb-3">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Admin Credit Vergaben</h4>
                    <div class="page-title-right">
                        <a href="{{ route('admin.credit-grants.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>Neue Credit-Vergabe
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-primary text-primary fs-3 rounded">
                                    <i class="ti ti-gift"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-1">Gesamt Vergaben</p>
                                <h4 class="fs-4 fw-semibold mb-0" id="totalGrants">-</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-success text-success fs-3 rounded">
                                    <i class="ti ti-coins"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-1">Gesamt Credits</p>
                                <h4 class="fs-4 fw-semibold mb-0" id="totalCredits">-</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-warning text-warning fs-3 rounded">
                                    <i class="ti ti-calendar"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-1">Dieser Monat</p>
                                <h4 class="fs-4 fw-semibold mb-0" id="grantsThisMonth">-</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-soft-info text-info fs-3 rounded">
                                    <i class="ti ti-trending-up"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="text-uppercase fw-medium text-muted mb-1">Credits/Monat</p>
                                <h4 class="fs-4 fw-semibold mb-0" id="creditsThisMonth">-</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="ti ti-info-circle text-info fs-3"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="card-title mb-1">Admin Credit Vergaben</h5>
                                <p class="card-text text-muted mb-0">
                                    Hier können Sie Credits manuell an Vendors vergeben. Diese Credits werden ohne Zahlung
                                    gutgeschrieben
                                    und können für verschiedene Zwecke verwendet werden (Entschädigung, Bonus, Korrektur,
                                    etc.).
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Livewire Table -->
        <div class="row">
            <div class="col-12 mb-3 ">
                @livewire('admin.credit-grants-table')
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Load statistics
                loadStatistics();

                // Refresh statistics every 30 seconds
                setInterval(loadStatistics, 30000);
            });

            function loadStatistics() {
                fetch('{{ route("admin.credit-grants.statistics") }}')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('totalGrants').textContent = data.total_grants || 0;
                        document.getElementById('totalCredits').textContent = data.total_credits_granted || 0;
                        document.getElementById('grantsThisMonth').textContent = data.grants_this_month || 0;
                        document.getElementById('creditsThisMonth').textContent = data.credits_this_month || 0;
                    })
                    .catch(error => {
                        console.error('Error loading statistics:', error);
                    });
            }
        </script>
    @endpush
@endsection