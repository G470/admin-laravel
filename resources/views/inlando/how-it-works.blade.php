@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Wie es funktioniert')

@section('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 80px 0;
    }

    .step-card {
        transition: all 0.25s ease;
        border: none;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        position: relative;
    }

    .step-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }

    .step-number {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: bold;
        margin: 0 auto 20px;
        position: relative;
        z-index: 2;
    }

    .step-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 32px;
        margin: 0 auto 20px;
    }

    .process-arrow {
        position: absolute;
        top: 50%;
        right: -25px;
        transform: translateY(-50%);
        font-size: 24px;
        color: #667eea;
        z-index: 1;
    }

    .tab-content-custom {
        min-height: 400px;
    }

    .nav-tabs-custom .nav-link {
        border: none;
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: 600;
        color: #667eea;
        background: rgba(102, 126, 234, 0.1);
        margin-right: 10px;
    }

    .nav-tabs-custom .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    @media (max-width: 768px) {
        .process-arrow {
            display: none;
        }
    }
</style>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4 text-white">Wie es funktioniert</h1>
                    <p class="lead mb-4">
                        Mieten und vermieten war noch nie so einfach. Folge diesen einfachen Schritten und werde Teil unserer Community.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Navigation -->
    <section class="section-py">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-6">
                    <ul class="nav nav-tabs nav-tabs-custom justify-content-center" id="processTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="renter-tab" data-bs-toggle="tab" data-bs-target="#renter" type="button" role="tab" aria-controls="renter" aria-selected="true">
                                <i class="ti ti-search me-2"></i>Als Mieter
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="landlord-tab" data-bs-toggle="tab" data-bs-target="#landlord" type="button" role="tab" aria-controls="landlord" aria-selected="false">
                                <i class="ti ti-home me-2"></i>Als Vermieter
                            </button>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tab-content tab-content-custom" id="processTabContent">
                <!-- Renter Process -->
                <div class="tab-pane fade show active" id="renter" role="tabpanel" aria-labelledby="renter-tab">
                    <div class="row g-4">
                        @foreach($renterSteps as $index => $step)
                            <div class="col-lg-3 col-md-6 position-relative">
                                <div class="card step-card h-100">
                                    <div class="card-body text-center p-4">
                                        <div class="step-number">{{ $step->step }}</div>
                                        <div class="step-icon mb-3">
                                            <i class="{{ $step->icon }}"></i>
                                        </div>
                                        <h5 class="fw-semibold text-heading mb-3">{{ $step->title }}</h5>
                                        <p class="text-body">{{ $step->description }}</p>
                                    </div>
                                </div>
                                @if($index < count($renterSteps) - 1)
                                    <div class="process-arrow d-none d-lg-block">
                                        <i class="ti ti-arrow-right"></i>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="text-center mt-5">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg waves-effect waves-light">
                            <i class="ti ti-search me-2"></i>Jetzt suchen
                        </a>
                    </div>
                </div>

                <!-- Landlord Process -->
                <div class="tab-pane fade" id="landlord" role="tabpanel" aria-labelledby="landlord-tab">
                    <div class="row g-4">
                        @foreach($landlordSteps as $index => $step)
                            <div class="col-lg-3 col-md-6 position-relative">
                                <div class="card step-card h-100">
                                    <div class="card-body text-center p-4">
                                        <div class="step-number">{{ $step->step }}</div>
                                        <div class="step-icon mb-3">
                                            <i class="{{ $step->icon }}"></i>
                                        </div>
                                        <h5 class="fw-semibold text-heading mb-3">{{ $step->title }}</h5>
                                        <p class="text-body">{{ $step->description }}</p>
                                    </div>
                                </div>
                                @if($index < count($landlordSteps) - 1)
                                    <div class="process-arrow d-none d-lg-block">
                                        <i class="ti ti-arrow-right"></i>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="text-center mt-5">
                        <a href="{{ route('rent-out') }}" class="btn btn-primary btn-lg waves-effect waves-light">
                            <i class="ti ti-plus me-2"></i>Artikel einstellen
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section-py bg-body">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="display-6 fw-bold mb-4">Warum Inlando?</h2>
                    <p class="text-body">
                        Entdecke die Vorteile unserer Plattform für Mieter und Vermieter.
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="step-icon mb-3">
                                <i class="ti ti-shield-check"></i>
                            </div>
                            <h5 class="fw-semibold mb-3">100% Sicher</h5>
                            <p class="text-body">
                                Alle Transaktionen sind versichert und wir überprüfen jeden Nutzer sorgfältig.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="step-icon mb-3">
                                <i class="ti ti-clock"></i>
                            </div>
                            <h5 class="fw-semibold mb-3">24/7 Support</h5>
                            <p class="text-body">
                                Unser Kundenservice steht dir rund um die Uhr zur Verfügung bei Fragen oder Problemen.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="step-icon mb-3">
                                <i class="ti ti-map-pin"></i>
                            </div>
                            <h5 class="fw-semibold mb-3">Lokale Angebote</h5>
                            <p class="text-body">
                                Finde Artikel in deiner unmittelbaren Nähe und spare Transportkosten.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="step-icon mb-3">
                                <i class="ti ti-credit-card"></i>
                            </div>
                            <h5 class="fw-semibold mb-3">Einfache Bezahlung</h5>
                            <p class="text-body">
                                Sichere Online-Bezahlung mit allen gängigen Zahlungsmethoden.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="step-icon mb-3">
                                <i class="ti ti-star"></i>
                            </div>
                            <h5 class="fw-semibold mb-3">Bewertungssystem</h5>
                            <p class="text-body">
                                Transparente Bewertungen helfen dir bei der Auswahl vertrauensvoller Partner.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="step-icon mb-3">
                                <i class="ti ti-leaf"></i>
                            </div>
                            <h5 class="fw-semibold mb-3">Umweltfreundlich</h5>
                            <p class="text-body">
                                Durch das Teilen von Ressourcen trägst du aktiv zum Umweltschutz bei.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section-py">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="display-6 fw-bold mb-4">Häufige Fragen</h2>
                    <p class="text-body">
                        Hier findest du Antworten auf die am häufigsten gestellten Fragen.
                    </p>
                </div>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Was passiert, wenn ein Artikel beschädigt wird?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Alle Vermietungen sind über unsere Versicherung abgedeckt. Bei Schäden kontaktiere einfach unseren Kundenservice, und wir kümmern uns um die Abwicklung.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Wie funktioniert die Bezahlung?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Die Bezahlung erfolgt sicher über unsere Plattform. Der Betrag wird erst nach erfolgreicher Übergabe an den Vermieter weitergeleitet.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Kann ich eine Buchung stornieren?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Ja, Stornierungen sind bis 24 Stunden vor Mietbeginn kostenlos möglich. Bei kurzfristigeren Stornierungen können Gebühren anfallen.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section-py bg-primary">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="display-6 fw-bold text-white mb-4">Bereit loszulegen?</h2>
                    <p class="text-white mb-4">
                        Schließe dich tausenden zufriedenen Nutzern an und entdecke die Vorteile des Teilens.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg waves-effect waves-light">
                            Kostenlos registrieren
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-light btn-lg waves-effect">
                            Artikel finden
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
