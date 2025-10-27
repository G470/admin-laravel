@extends('layouts/contentNavbarLayout')

@section('title', 'Credit-Kauf - Zahlung')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('vendor.credits.index') }}">Credits</a>
                        </li>
                        <li class="breadcrumb-item active">Zahlung</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-credit-card text-primary me-2"></i>Credit-Kauf abschließen
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Order Summary -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="ti ti-package me-2"></i>Ihre Bestellung
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($vendorCredit->creditPackage)
                                            <div class="text-center mb-3">
                                                <div class="avatar avatar-lg mx-auto mb-3">
                                                    <span class="avatar-initial bg-label-primary rounded">
                                                        <i class="ti ti-coins"></i>
                                                    </span>
                                                </div>
                                                <h5>{{ $vendorCredit->creditPackage->name }}</h5>
                                                <p class="text-muted">{{ number_format($vendorCredit->credits_purchased) }}
                                                    Credits</p>

                                                @if($vendorCredit->creditPackage->description)
                                                    <p class="text-muted small">{{ $vendorCredit->creditPackage->description }}</p>
                                                @endif
                                            </div>

                                            <hr>

                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Credits:</span>
                                                <strong>{{ number_format($vendorCredit->credits_purchased) }}</strong>
                                            </div>

                                            @if($vendorCredit->creditPackage->is_discounted)
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Regulärer Preis:</span>
                                                    <span
                                                        class="text-decoration-line-through text-muted">€{{ number_format($vendorCredit->creditPackage->standard_price, 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-success">Rabatt
                                                        ({{ $vendorCredit->creditPackage->discount_percentage }}%):</span>
                                                    <span
                                                        class="text-success">-€{{ number_format($vendorCredit->creditPackage->standard_price - $vendorCredit->creditPackage->offer_price, 2) }}</span>
                                                </div>
                                            @endif

                                            <hr>

                                            <div class="d-flex justify-content-between">
                                                <strong>Gesamtbetrag:</strong>
                                                <strong
                                                    class="text-primary">€{{ number_format($vendorCredit->amount_paid, 2) }}</strong>
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="ti ti-alert-triangle me-2"></i>
                                                Das ausgewählte Credit-Paket ist nicht mehr verfügbar.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="ti ti-credit-card me-2"></i>Zahlungsmethode wählen
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @if($vendorCredit->creditPackage)
                                            <!-- Payment Status -->
                                            <div class="alert alert-info">
                                                <div class="d-flex align-items-center">
                                                    <i class="ti ti-info-circle me-2"></i>
                                                    <div>
                                                        <strong>Bestellung: #{{ $vendorCredit->id }}</strong><br>
                                                        <small>Status: {{ ucfirst($vendorCredit->payment_status) }}</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Payment Options -->
                                            <div class="payment-options">
                                                <!-- Stripe Payment -->
                                                <div class="border rounded p-3 mb-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ti ti-credit-card text-primary me-2"></i>
                                                            <div>
                                                                <h6 class="mb-0">Kreditkarte</h6>
                                                                <small class="text-muted">Visa, Mastercard, American
                                                                    Express</small>
                                                            </div>
                                                        </div>
                                                        <button class="btn btn-primary" onclick="initiateStripePayment()">
                                                            Zahlen
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- PayPal Payment -->
                                                <div class="border rounded p-3 mb-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ti ti-brand-paypal text-primary me-2"></i>
                                                            <div>
                                                                <h6 class="mb-0">PayPal</h6>
                                                                <small class="text-muted">Sicher bezahlen mit PayPal</small>
                                                            </div>
                                                        </div>
                                                        <button class="btn btn-primary" onclick="initiatePayPalPayment()">
                                                            Zahlen
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Bank Transfer (for demonstration) -->
                                                <div class="border rounded p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ti ti-building-bank text-primary me-2"></i>
                                                            <div>
                                                                <h6 class="mb-0">Überweisung</h6>
                                                                <small class="text-muted">Manuell per Banküberweisung</small>
                                                            </div>
                                                        </div>
                                                        <button class="btn btn-outline-primary"
                                                            onclick="showBankTransferInfo()">
                                                            Info
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Security Info -->
                                            <div class="mt-4">
                                                <div class="d-flex align-items-center text-muted">
                                                    <i class="ti ti-shield-check text-success me-2"></i>
                                                    <small>Sichere Zahlung - SSL-verschlüsselt</small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('vendor.credits.index') }}" class="btn btn-outline-secondary">
                                        <i class="ti ti-arrow-left me-1"></i>Zurück
                                    </a>

                                    @if($vendorCredit->payment_status === 'completed')
                                        <a href="{{ route('vendor.credits.payment.success', $vendorCredit) }}"
                                            class="btn btn-success">
                                            <i class="ti ti-check me-1"></i>Zahlung erfolgreich
                                        </a>
                                    @elseif($vendorCredit->payment_status === 'failed')
                                        <button class="btn btn-danger" disabled>
                                            <i class="ti ti-x me-1"></i>Zahlung fehlgeschlagen
                                        </button>
                                    @else
                                        <div class="text-muted">
                                            <i class="ti ti-clock me-1"></i>Warten auf Zahlung...
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Processing Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5>Zahlung wird verarbeitet...</h5>
                    <p class="text-muted">Bitte warten Sie, während Ihre Zahlung verarbeitet wird.</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Placeholder functions for payment integration
            function initiateStripePayment() {
                // Show processing modal
                const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
                modal.show();

                // Simulate payment processing (replace with actual Stripe integration)
                setTimeout(() => {
                    modal.hide();
                    // Redirect to success page or show error
                    // In real implementation, this would be handled by Stripe's callback
                    alert('Stripe-Integration ist noch nicht konfiguriert. Dies ist nur eine Demo.');
                }, 3000);
            }

            function initiatePayPalPayment() {
                // Show processing modal
                const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
                modal.show();

                // Simulate payment processing (replace with actual PayPal integration)
                setTimeout(() => {
                    modal.hide();
                    // Redirect to success page or show error
                    alert('PayPal-Integration ist noch nicht konfiguriert. Dies ist nur eine Demo.');
                }, 3000);
            }

            function showBankTransferInfo() {
                alert(`Banküberweisung-Details:\n\nEmpfänger: Inlando GmbH\nIBAN: DE89 3704 0044 0532 0130 00\nBIC: COBADEFFXXX\nVerwendungszweck: Credit-Kauf #{{ $vendorCredit->id }}\n\nBetrag: €{{ number_format($vendorCredit->amount_paid, 2) }}\n\nNach Zahlungseingang werden Ihre Credits automatisch gutgeschrieben.`);
            }

            // Auto-refresh for payment status (in real implementation, use websockets or webhooks)
            @if($vendorCredit->payment_status === 'pending')
                setInterval(() => {
                    fetch(`{{ route('vendor.credits.payment', $vendorCredit) }}`)
                        .then(response => {
                            if (response.redirected) {
                                window.location.href = response.url;
                            }
                        })
                        .catch(error => console.log('Status check failed:', error));
                }, 10000); // Check every 10 seconds
            @endif
        </script>
    @endpush
@endsection