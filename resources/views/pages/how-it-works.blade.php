@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Wie es funktioniert')

@section('content')
    <!-- Hero Section -->
    <div class="bg-primary text-white py-5 mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-3">Wie Inlando funktioniert</h1>
                    <p class="lead mb-4">Ihre Plattform für einfaches Mieten und Vermieten. Verbinden Sie sich mit lokalen
                        Anbietern oder bieten Sie Ihre eigenen Artikel zur Miete an.</p>
                </div>
                <div class="col-lg-4">
                    <div class="text-center">
                        <i class="ti ti-users display-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Platform Overview -->
        <div class="row mb-5">
            <div class="col-lg-12 text-center">
                <h2 class="mb-4">Eine Plattform, zwei Möglichkeiten</h2>
                <p class="lead text-muted mb-5">Ob Sie etwas mieten oder vermieten möchten – Inlando macht es einfach und
                    sicher.</p>
            </div>
        </div>

        <!-- For Customers Section -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0"><i class="ti ti-search me-2"></i>Für Mieter</h3>
                                <small>Finden Sie genau das, was Sie brauchen</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <!-- Step 1 -->
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="text-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width: 80px; height: 80px;">
                                        <i class="ti ti-map-pin text-success" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="fw-bold">1. Standort eingeben</h5>
                                    <p class="text-muted small">Geben Sie Ihre Postleitzahl oder Stadt ein, um Angebote in
                                        Ihrer Nähe zu finden.</p>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="text-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width: 80px; height: 80px;">
                                        <i class="ti ti-category text-success" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="fw-bold">2. Kategorie wählen</h5>
                                    <p class="text-muted small">Durchsuchen Sie unsere Kategorien: Fahrzeuge, Baumaschinen,
                                        Events, Garten & mehr.</p>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="text-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width: 80px; height: 80px;">
                                        <i class="ti ti-calendar text-success" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="fw-bold">3. Anfrage senden</h5>
                                    <p class="text-muted small">Wählen Sie Ihre Wunschdaten und senden Sie eine
                                        unverbindliche Anfrage an den Vermieter.</p>
                                </div>
                            </div>

                            <!-- Step 4 -->
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="text-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width: 80px; height: 80px;">
                                        <i class="ti ti-check text-success" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="fw-bold">4. Mieten & nutzen</h5>
                                    <p class="text-muted small">Nach der Bestätigung können Sie den Artikel zur vereinbarten
                                        Zeit abholen und nutzen.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Benefits -->
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <h5 class="fw-bold mb-3">Ihre Vorteile als Mieter:</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="ti ti-check text-success me-2"></i>Lokale Anbieter in
                                                Ihrer Nähe</li>
                                            <li class="mb-2"><i class="ti ti-check text-success me-2"></i>Faire und
                                                transparente Preise</li>
                                            <li class="mb-2"><i class="ti ti-check text-success me-2"></i>Große Auswahl an
                                                Kategorien</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="ti ti-check text-success me-2"></i>Sichere Abwicklung
                                            </li>
                                            <li class="mb-2"><i class="ti ti-check text-success me-2"></i>Bewertungen und
                                                Erfahrungen</li>
                                            <li class="mb-2"><i class="ti ti-check text-success me-2"></i>Kostenlose
                                                Anfragen</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- For Vendors Section -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="mb-0"><i class="ti ti-building-store me-2"></i>Für Vermieter</h3>
                                <small>Verdienen Sie Geld mit Ihren ungenutzten Gegenständen</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <!-- Step 1 -->
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="text-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width: 80px; height: 80px;">
                                        <i class="ti ti-user-plus text-primary" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="fw-bold">1. Kostenlos registrieren</h5>
                                    <p class="text-muted small">Erstellen Sie Ihr Vermieter-Konto und verifizieren Sie Ihre
                                        Identität.</p>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="text-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width: 80px; height: 80px;">
                                        <i class="ti ti-plus text-primary" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="fw-bold">2. Artikel einstellen</h5>
                                    <p class="text-muted small">Laden Sie Fotos hoch, beschreiben Sie Ihren Artikel und
                                        legen Sie faire Preise fest.</p>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="text-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width: 80px; height: 80px;">
                                        <i class="ti ti-messages text-primary" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="fw-bold">3. Anfragen bearbeiten</h5>
                                    <p class="text-muted small">Erhalten Sie Mietanfragen und kommunizieren Sie direkt mit
                                        interessierten Mietern.</p>
                                </div>
                            </div>

                            <!-- Step 4 -->
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="text-center">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width: 80px; height: 80px;">
                                        <i class="ti ti-coin text-primary" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="fw-bold">4. Geld verdienen</h5>
                                    <p class="text-muted small">Vermieten Sie Ihre Artikel und erhalten Sie regelmäßige
                                        Einnahmen.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Vendor Benefits -->
                        <div class="row mt-4">
                            <div class="col-lg-12">
                                <h5 class="fw-bold mb-3">Ihre Vorteile als Vermieter:</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="ti ti-check text-primary me-2"></i>Zusätzliches
                                                Einkommen generieren</li>
                                            <li class="mb-2"><i class="ti ti-check text-primary me-2"></i>Einfache
                                                Verwaltung Ihrer Artikel</li>
                                            <li class="mb-2"><i class="ti ti-check text-primary me-2"></i>Lokale Kunden
                                                erreichen</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li class="mb-2"><i class="ti ti-check text-primary me-2"></i>Sichere
                                                Zahlungsabwicklung</li>
                                            <li class="mb-2"><i class="ti ti-check text-primary me-2"></i>Umfassende
                                                Verwaltungstools</li>
                                            <li class="mb-2"><i class="ti ti-check text-primary me-2"></i>Support und
                                                Beratung</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-4">Unsere Kategorien</h2>
                <p class="text-center text-muted mb-5">Von Fahrzeugen bis Elektronik – finden Sie alles, was Sie brauchen
                </p>

                <div class="row">
                    <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                        <div class="card border-0 text-center h-100 shadow-sm">
                            <div class="card-body">
                                <i class="ti ti-car display-4 text-primary mb-3"></i>
                                <h6 class="fw-bold">Fahrzeuge</h6>
                                <small class="text-muted">PKW, LKW, Anhänger</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                        <div class="card border-0 text-center h-100 shadow-sm">
                            <div class="card-body">
                                <i class="ti ti-tools display-4 text-warning mb-3"></i>
                                <h6 class="fw-bold">Baumaschinen</h6>
                                <small class="text-muted">Geräte & Maschinen</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                        <div class="card border-0 text-center h-100 shadow-sm">
                            <div class="card-body">
                                <i class="ti ti-confetti display-4 text-success mb-3"></i>
                                <h6 class="fw-bold">Events</h6>
                                <small class="text-muted">Party & Veranstaltung</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                        <div class="card border-0 text-center h-100 shadow-sm">
                            <div class="card-body">
                                <i class="ti ti-leaf display-4 text-info mb-3"></i>
                                <h6 class="fw-bold">Garten</h6>
                                <small class="text-muted">Gartengeräte & mehr</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                        <div class="card border-0 text-center h-100 shadow-sm">
                            <div class="card-body">
                                <i class="ti ti-device-laptop display-4 text-secondary mb-3"></i>
                                <h6 class="fw-bold">Elektronik</h6>
                                <small class="text-muted">Technik & Geräte</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                        <div class="card border-0 text-center h-100 shadow-sm">
                            <div class="card-body">
                                <i class="ti ti-ball-basketball display-4 text-danger mb-3"></i>
                                <h6 class="fw-bold">Sport</h6>
                                <small class="text-muted">Sport & Freizeit</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trust & Security Section -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card bg-light border-0">
                    <div class="card-body p-5">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <h3 class="fw-bold mb-3">Sicherheit & Vertrauen</h3>
                                <p class="mb-4">Ihre Sicherheit steht bei uns an erster Stelle. Deshalb setzen wir auf
                                    bewährte Sicherheitsmaßnahmen und transparente Bewertungssysteme.</p>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-flex mb-3">
                                            <i class="ti ti-shield-check text-success fs-4 me-3"></i>
                                            <div>
                                                <h6 class="fw-bold mb-1">Verifizierte Nutzer</h6>
                                                <small class="text-muted">Alle Vermieter werden von uns geprüft</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex mb-3">
                                            <i class="ti ti-star text-warning fs-4 me-3"></i>
                                            <div>
                                                <h6 class="fw-bold mb-1">Bewertungssystem</h6>
                                                <small class="text-muted">Transparente Bewertungen aller Nutzer</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex mb-3">
                                            <i class="ti ti-lock text-primary fs-4 me-3"></i>
                                            <div>
                                                <h6 class="fw-bold mb-1">Sichere Zahlung</h6>
                                                <small class="text-muted">Verschlüsselte und sichere Abwicklung</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex mb-3">
                                            <i class="ti ti-headset text-info fs-4 me-3"></i>
                                            <div>
                                                <h6 class="fw-bold mb-1">24/7 Support</h6>
                                                <small class="text-muted">Wir helfen Ihnen bei allen Fragen</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 text-center">
                                <i class="ti ti-shield-check display-1 text-success opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <div class="card bg-primary text-white border-0">
                    <div class="card-body p-5 text-center">
                        <h2 class="fw-bold mb-3">Bereit zum Starten?</h2>
                        <p class="lead mb-4">Schließen Sie sich Tausenden zufriedener Nutzer an und entdecken Sie die
                            Vorteile von Inlando.</p>

                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <a href="{{ route('register') }}" class="btn btn-light btn-lg w-100">
                                            <i class="ti ti-search me-2"></i>
                                            Als Mieter starten
                                        </a>
                                        <small class="d-block mt-2 opacity-75">Finden Sie, was Sie brauchen</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <a href="{{ route('rent-out') }}" class="btn btn-outline-light btn-lg w-100">
                                            <i class="ti ti-building-store me-2"></i>
                                            Vermieter werden
                                        </a>
                                        <small class="d-block mt-2 opacity-75">Geld verdienen mit Ihren Artikeln</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="row mb-5">
            <div class="col-lg-12">
                <h2 class="text-center mb-5">Häufig gestellte Fragen</h2>

                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Wie funktioniert die Zahlung?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Die Zahlung erfolgt sicher über unsere Plattform. Mieter zahlen vor der Nutzung, Vermieter
                                erhalten ihre Einnahmen nach erfolgreicher Vermietung.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq2">
                                Was passiert bei Schäden?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Für alle Vermietungen kann eine Kaution hinterlegt werden. Bei Schäden vermitteln wir
                                zwischen den Parteien und sorgen für eine faire Lösung.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq3">
                                Wie wird die Qualität sichergestellt?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Alle Vermieter werden von uns verifiziert. Zusätzlich sorgt unser Bewertungssystem für
                                Transparenz und Qualität bei allen Vermietungen.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq4">
                                Kann ich als Privatperson vermieten?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Ja, sowohl Privatpersonen als auch Unternehmen können über Inlando vermieten. Sie benötigen
                                lediglich ein verifiziertes Konto.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq5">
                                Welche Gebühren fallen an?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Die Registrierung ist kostenlos. Wir erheben nur eine kleine Servicegebühr bei erfolgreichen
                                Vermietungen, die transparent kommuniziert wird.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#faq6">
                                Wie funktioniert die Versicherung?
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Alle Vermietungen können optional versichert werden. Details zur Versicherungsdeckung finden
                                Sie in den jeweiligen Angeboten oder kontaktieren Sie unseren Support.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection