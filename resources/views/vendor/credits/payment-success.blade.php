@extends('layouts/contentNavbarLayout')

@section('title', 'Zahlung erfolgreich')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card text-center">
                    <div class="card-body py-5">
                        <!-- Success Icon -->
                        <div class="avatar avatar-xl mx-auto mb-4">
                            <span class="avatar-initial bg-success rounded-circle">
                                <i class="ti ti-check" style="font-size: 2rem;"></i>
                            </span>
                        </div>

                        <h3 class="text-success mb-3">Zahlung erfolgreich!</h3>
                        <p class="text-muted mb-4">
                            Vielen Dank für Ihren Kauf. Ihre Credits wurden erfolgreich zu Ihrem Konto hinzugefügt.
                        </p>

                        <!-- Purchase Details -->
                        @if($vendorCredit->creditPackage)
                            <div class="card border mb-4">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">
                                        <i class="ti ti-receipt me-2"></i>Kaufdetails
                                    </h6>

                                    <div class="row text-start">
                                        <div class="col-6">
                                            <small class="text-muted">Bestellnummer:</small>
                                            <div class="fw-semibold">#{{ $vendorCredit->id }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Datum:</small>
                                            <div class="fw-semibold">
                                                {{ $vendorCredit->purchased_at ? $vendorCredit->purchased_at->format('d.m.Y H:i') : now()->format('d.m.Y H:i') }}
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <div class="row text-start">
                                        <div class="col-6">
                                            <small class="text-muted">Paket:</small>
                                            <div class="fw-semibold">{{ $vendorCredit->creditPackage->name }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Credits erhalten:</small>
                                            <div class="fw-semibold text-primary">
                                                {{ number_format($vendorCredit->credits_purchased) }} Credits</div>
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <div class="row text-start">
                                        <div class="col-6">
                                            <small class="text-muted">Bezahlter Betrag:</small>
                                            <div class="fw-semibold">€{{ number_format($vendorCredit->amount_paid, 2) }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Zahlungsstatus:</small>
                                            <div>
                                                <span
                                                    class="badge bg-success">{{ ucfirst($vendorCredit->payment_status) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if($vendorCredit->payment_reference)
                                        <hr class="my-3">
                                        <div class="text-start">
                                            <small class="text-muted">Transaktions-ID:</small>
                                            <div class="fw-semibold">{{ $vendorCredit->payment_reference }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Current Balance -->
                        <div class="alert alert-success">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="ti ti-coins me-2"></i>
                                <div>
                                    <strong>Ihr aktuelles Credit-Guthaben:</strong><br>
                                    <span
                                        class="h5 mb-0">{{ number_format(\App\Models\VendorCredit::getVendorBalance(auth()->id())) }}
                                        Credits</span>
                                </div>
                            </div>
                        </div>

                        <!-- Next Steps -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h6 class="text-start mb-3">
                                    <i class="ti ti-lightbulb me-2"></i>Nächste Schritte
                                </h6>
                                <div class="list-group list-group-flush text-start">
                                    <div class="list-group-item d-flex align-items-center px-0">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial bg-label-primary rounded-circle">
                                                <i class="ti ti-package"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Artikel auswählen</h6>
                                            <small class="text-muted">Wählen Sie die Artikel aus, die Sie hervorheben
                                                möchten</small>
                                        </div>
                                    </div>
                                    <div class="list-group-item d-flex align-items-center px-0">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial bg-label-primary rounded-circle">
                                                <i class="ti ti-target"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Kategorie wählen</h6>
                                            <small class="text-muted">Bestimmen Sie, in welcher Kategorie Ihre Artikel
                                                hervorgehoben werden sollen</small>
                                        </div>
                                    </div>
                                    <div class="list-group-item d-flex align-items-center px-0">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial bg-label-primary rounded-circle">
                                                <i class="ti ti-trending-up"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Hervorhebung aktivieren</h6>
                                            <small class="text-muted">Aktivieren Sie die Hervorhebung und erreichen Sie mehr
                                                Kunden</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center mt-4">
                            <a href="{{ route('vendor.credits.index') }}" class="btn btn-primary">
                                <i class="ti ti-coins me-1"></i>Credit-Übersicht
                            </a>
                            <a href="{{ route('vendor.rentals.index') }}" class="btn btn-outline-primary">
                                <i class="ti ti-package me-1"></i>Artikel verwalten
                            </a>
                            <a href="{{ route('vendor.credits.history') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-history me-1"></i>Kaufverlauf
                            </a>
                        </div>

                        <!-- Support Info -->
                        <div class="mt-4 pt-4 border-top">
                            <small class="text-muted">
                                <i class="ti ti-help-circle me-1"></i>
                                Bei Fragen zu Ihrer Bestellung kontaktieren Sie uns unter
                                <a href="mailto:support@inlando.com">support@inlando.com</a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Confetti animation for success page
            document.addEventListener('DOMContentLoaded', function () {
                // Simple confetti effect (you can replace with a proper confetti library)
                const confettiCanvas = document.createElement('canvas');
                confettiCanvas.style.position = 'fixed';
                confettiCanvas.style.top = '0';
                confettiCanvas.style.left = '0';
                confettiCanvas.style.width = '100%';
                confettiCanvas.style.height = '100%';
                confettiCanvas.style.pointerEvents = 'none';
                confettiCanvas.style.zIndex = '9999';
                document.body.appendChild(confettiCanvas);

                // Remove confetti after animation
                setTimeout(() => {
                    if (confettiCanvas.parentNode) {
                        confettiCanvas.parentNode.removeChild(confettiCanvas);
                    }
                }, 3000);
            });
        </script>
    @endpush
@endsection