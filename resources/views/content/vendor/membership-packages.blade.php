@extends('layouts/contentNavbarLayout')

@section('title', 'Mitgliedschaft & Pakete')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-credit-card me-2"></i>
                        Mitgliedschaft & Pakete
                    </h5>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible m-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible m-3" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info alert-dismissible m-3" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card-body">
                    <p class="text-muted mb-4">
                        Hier kannst du deine Mitgliedschaft und deine gebuchten Pakete verwalten.
                    </p>

                    {{-- Cancellation Warning --}}
                    <div class="alert alert-warning mb-4" role="alert">
                        <h6 class="alert-heading">
                            <i class="ti ti-alert-triangle me-2"></i>
                            Wichtiger Hinweis zur Kündigung
                        </h6>
                        <p class="mb-0">
                            Wenn man auf den Button "Abonnement kündigen" klickt: Möchtest du dein Abonnement wirklich
                            kündigen?
                            In diesem Fall kannst du unseren Service nicht mehr nutzen. Alle von dir erstellten Artikel
                            werden
                            deaktiviert und können nicht mehr vermietet werden.
                        </p>
                    </div>

                    @if($subscription)
                        {{-- Subscription Details --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Abonnement Details</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Laufzeit:</span>
                                    <span class="fw-bold">3 Monate</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Interval:</span>
                                    <span class="fw-bold">Monatlich</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Beginn am:</span>
                                    <span class="fw-bold">{{ $subscription->start_date->format('d.m.Y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Endet am:</span>
                                    <span class="fw-bold">Ablaufdatum: {{ $subscription->end_date->format('d.m.Y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Kündigungsfrist:</span>
                                    <span class="fw-bold">{{ $subscription->cancellation_deadline->format('d.m.Y') }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Nutzung</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Artikel:</span>
                                    <span class="fw-bold">{{ $subscription->rental_count }} Mietobjekte</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Standorte inkl:</span>
                                    <span class="fw-bold">= Anzahl der Mietobjekte {{ $subscription->location_count }}
                                        Standorte</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Gebuchte Standorte:</span>
                                    <span class="fw-bold">{{ $subscription->location_count }} Standorte</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Standorte gesamt:</span>
                                    <span class="fw-bold">= Mietobjekte + gebuchte Standorte {{ $subscription->location_count }}
                                        Standorte</span>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Pricing Breakdown --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Preisaufschlüsselung</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Zwischensumme:</span>
                                    <span class="fw-bold">{{ number_format($subscription->monthly_price, 2, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>zzgl. 19% Mwst.:</span>
                                    <span class="fw-bold">{{ number_format($subscription->vat_amount, 2, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Gesamtsumme:</span>
                                    <span class="fw-bold">{{ $subscription->formatted_total_with_vat }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h6 class="text-muted mb-3">Abonnement Übersicht</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Abonnement:</span>
                                    <span class="badge bg-success">Aktiv</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Laufzeit:</span>
                                    <span class="fw-bold">3 Monate</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Ablaufdatum:</span>
                                    <span class="fw-bold">{{ $subscription->end_date->format('d.m.Y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Gebuchte Artikelslots:</span>
                                    <span class="fw-bold">{{ $subscription->rental_count }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Gebuchte Standortslots:</span>
                                    <span class="fw-bold">{{ $subscription->location_count }}</span>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Final Pricing --}}
                        <div class="row mb-4">
                            <div class="col-md-6 offset-md-6">
                                <div class="text-end">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>{{ number_format($currentPricing['variable_costs']['rental_submissions']['subtotal'], 2, ',', '.') }}
                                            €</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>{{ number_format($currentPricing['variable_costs']['booked_locations']['subtotal'], 2, ',', '.') }}
                                            €</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Gesamt:</span>
                                        <span class="fw-bold">{{ number_format($subscription->monthly_price, 2, ',', '.') }}
                                            €</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>zzgl. MwSt.:</span>
                                        <span class="fw-bold">{{ number_format($subscription->vat_amount, 2, ',', '.') }}
                                            €</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Insgesamt (pro Monat):</span>
                                        <span class="fw-bold">{{ number_format($subscription->monthly_price, 2, ',', '.') }}
                                            €</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-primary" onclick="changeSubscription()">
                                        <i class="ti ti-edit me-2"></i>
                                        ABONNEMENT ÄNDERN
                                    </button>

                                    <button type="button" class="btn btn-danger" onclick="confirmCancellation()">
                                        <i class="ti ti-x me-2"></i>
                                        ABONNEMENT KÜNDIGEN
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Cancellation Modal --}}
                        <div class="modal fade" id="cancellationModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Abonnement kündigen</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Sind Sie sicher, dass Sie Ihr Abonnement kündigen möchten?</p>
                                        <div class="alert alert-warning">
                                            <strong>Achtung:</strong> Nach der Kündigung können Sie unseren Service nicht mehr
                                            nutzen.
                                            Alle Ihre Artikel werden deaktiviert und können nicht mehr vermietet werden.
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Abbrechen</button>
                                        <form action="{{ route('vendor.membership.cancel') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="confirmation" value="1">
                                            <button type="submit" class="btn btn-danger">Ja, kündigen</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ti ti-credit-card-off display-1 text-muted mb-3"></i>
                            <h5>Kein aktives Abonnement</h5>
                            <p class="text-muted">Sie haben derzeit kein aktives Abonnement.</p>
                            <button type="button" class="btn btn-primary" onclick="changeSubscription()">
                                <i class="ti ti-plus me-2"></i>
                                Abonnement erstellen
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmCancellation() {
            const modal = new bootstrap.Modal(document.getElementById('cancellationModal'));
            modal.show();
        }

        function changeSubscription() {
            // Future implementation for subscription changes
            alert('Funktion wird in Kürze verfügbar sein.');
        }
    </script>
@endsection