@extends('layouts/contentNavbarLayout')

@section('title', 'Artikel-Push Verwaltung')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Page Header -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-container">
                    <div class="row">
                        <div
                            class="col-12 col-md-7 d-flex align-items-center justify-content-md-start justify-content-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="ti ti-rocket"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="mb-0">Artikel-Push Verwaltung</h4>
                                    <p class="text-muted mb-0">Steigern Sie Ihre Sichtbarkeit mit automatischen Pushes</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-12 col-md-5 d-flex align-items-center justify-content-md-end justify-content-center mt-3 mt-md-0">
                            <div class="d-flex gap-2">
                                <a href="{{ route('vendor.credits.index') }}" class="btn btn-outline-primary">
                                    <i class="ti ti-currency-euro me-1"></i>Credits kaufen
                                </a>
                                <a href="{{ route('vendor.rental-pushes.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-1"></i>Neuer Push
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text">Aktive Pushes</p>
                                <div class="d-flex align-items-end mb-2">
                                    <h4 class="card-title mb-0" id="activePushes">-</h4>
                                </div>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-primary rounded p-2">
                                    <i class="ti ti-rocket ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text">Verbrauchte Credits</p>
                                <div class="d-flex align-items-end mb-2">
                                    <h4 class="card-title mb-0" id="usedCredits">-</h4>
                                </div>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-success rounded p-2">
                                    <i class="ti ti-currency-euro ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text">Credits verfügbar</p>
                                <div class="d-flex align-items-end mb-2">
                                    <h4 class="card-title mb-0" id="availableCredits">-</h4>
                                </div>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-warning rounded p-2">
                                    <i class="ti ti-wallet ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="card-info">
                                <p class="card-text">Pushes diesen Monat</p>
                                <div class="d-flex align-items-end mb-2">
                                    <h4 class="card-title mb-0" id="monthlyPushes">-</h4>
                                </div>
                            </div>
                            <div class="card-icon">
                                <span class="badge bg-label-info rounded p-2">
                                    <i class="ti ti-calendar ti-sm"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="card-title text-white">
                                    <i class="ti ti-info-circle me-2"></i>Wie funktioniert Artikel-Push?
                                </h5>
                                <p class="card-text mb-0">
                                    Mit der Artikel-Push-Funktion können Sie Ihre Vermietungsobjekte automatisch in den
                                    kategoriebezogenen Suchergebnissen nach oben schieben.
                                    Wählen Sie eine Frequenz von 1-7x täglich und profitieren Sie von mehr Kundenanfragen.
                                    <strong>1x Hochschieben = 1 Credit</strong>
                                </p>
                            </div>
                            <div class="col-md-4 d-flex align-items-center justify-content-end">
                                <div class="text-end">
                                    <h6 class="text-white mb-1">Empfohlene Frequenz</h6>
                                    <p class="text-white-50 mb-0">7x am Tag für maximale Sichtbarkeit</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Livewire Component -->
        <div class="row">
            <div class="col-12">
                @livewire('vendor.rental-push-table')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Load statistics on page load
        document.addEventListener('DOMContentLoaded', function () {
            loadStatistics();
        });

        function loadStatistics() {
            fetch('{{ route("vendor.rental-pushes.statistics") }}')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('activePushes').textContent = data.active_pushes;
                    document.getElementById('usedCredits').textContent = data.total_credits_used;
                    document.getElementById('availableCredits').textContent = data.current_balance;
                    document.getElementById('monthlyPushes').textContent = data.pushes_this_month;
                })
                .catch(error => {
                    console.error('Error loading statistics:', error);
                });
        }

        // Refresh statistics when Livewire updates
        document.addEventListener('livewire:load', function () {
            Livewire.hook('message.processed', (message, component) => {
                if (message.updateQueue.length > 0) {
                    loadStatistics();
                }
            });
        });
    </script>
@endpush