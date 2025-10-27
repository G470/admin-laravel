@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Über uns - Inlando')

@section('styles')
    <style>
        .hero-about {
            background: linear-gradient(135deg, #696cff 0%, #5a67d8 100%);
            color: white;
            padding: 80px 0;
            margin-bottom: 0;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #696cff 0%, #5a67d8 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .feature-icon i {
            font-size: 24px;
            color: white;
        }

        .stats-card {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: none;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #696cff;
            line-height: 1;
        }

        .team-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-5px);
        }

        .team-avatar {
            width: 100%;
            height: 250px;
            background: linear-gradient(135deg, #f8f9ff 0%, #e8ecff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: #696cff;
        }

        .cta-section {
            background: linear-gradient(135deg, #696cff 0%, #5a67d8 100%);
            color: white;
            padding: 80px 0;
            margin-top: 80px;
        }

        .section-padding {
            padding: 80px 0;
        }

        .text-primary-custom {
            color: #696cff !important;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #696cff 0%, #5a67d8 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(105, 108, 255, 0.3);
        }
    </style>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero-about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold mb-4 text-white">Über Inlando</h1>
                    <p class="lead mb-4">
                        Wir verbinden Menschen mit den Dingen, die sie brauchen – einfach, sicher und nachhaltig.
                        Inlando ist Deutschlands führende Plattform für Vermietung und Sharing.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="/" class="btn btn-light btn-lg waves-effect">
                            <i class="ti ti-search me-2"></i>Artikel finden
                        </a>
                        <a href="{{ route('rent-out') }}" class="btn btn-outline-light btn-lg waves-effect">
                            <i class="ti ti-plus me-2"></i>Vermieten
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <h2 class="h1 fw-bold text-primary-custom mb-4">Unsere Mission</h2>
                    <p class="lead mb-4">
                        Wir glauben an eine Welt, in der Ressourcen optimal genutzt werden. Statt alles zu kaufen,
                        ermöglichen wir es Menschen, Gegenstände zu teilen und zu mieten.
                    </p>
                    <p class="mb-4">
                        Von Baumaschinen über Fahrzeuge bis hin zu Event-Equipment – auf Inlando finden Sie alles,
                        was Sie temporär benötigen. Gleichzeitig können Sie Ihre eigenen ungenutzten Gegenstände
                        vermieten und damit Geld verdienen.
                    </p>
                    <div class="d-flex align-items-center">
                        <div class="feature-icon me-3">
                            <i class="ti ti-leaf"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Nachhaltig & Umweltbewusst</h5>
                            <p class="text-muted mb-0">Weniger Ressourcenverbrauch durch intelligente Nutzung</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="stats-card">
                                <div class="stats-number">10K+</div>
                                <h6 class="mb-0">Aktive Nutzer</h6>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="stats-card">
                                <div class="stats-number">5K+</div>
                                <h6 class="mb-0">Verfügbare Artikel</h6>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="stats-card">
                                <div class="stats-number">500+</div>
                                <h6 class="mb-0">Städte</h6>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="stats-card">
                                <div class="stats-number">98%</div>
                                <h6 class="mb-0">Zufriedenheit</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="h1 fw-bold text-primary-custom mb-4">So funktioniert's</h2>
                    <p class="lead">
                        Mieten und Vermieten war noch nie so einfach. In nur wenigen Schritten finden Sie,
                        was Sie brauchen, oder verdienen Geld mit Ihren ungenutzten Gegenständen.
                    </p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="feature-icon mx-auto">
                            <i class="ti ti-search"></i>
                        </div>
                        <h4 class="fw-bold mb-3">1. Suchen & Finden</h4>
                        <p class="text-muted">
                            Durchsuchen Sie tausende verfügbare Artikel in Ihrer Nähe.
                            Von Werkzeugen bis Fahrzeugen – alles an einem Ort.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="feature-icon mx-auto">
                            <i class="ti ti-message-circle"></i>
                        </div>
                        <h4 class="fw-bold mb-3">2. Kontakt & Buchung</h4>
                        <p class="text-muted">
                            Kontaktieren Sie den Vermieter direkt über unsere Plattform.
                            Vereinbaren Sie Termine und buchen Sie sicher online.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="feature-icon mx-auto">
                            <i class="ti ti-check-circle"></i>
                        </div>
                        <h4 class="fw-bold mb-3">3. Abholen & Nutzen</h4>
                        <p class="text-muted">
                            Holen Sie den Artikel zum vereinbarten Termin ab und nutzen Sie ihn.
                            Nach der Rückgabe bewerten Sie Ihre Erfahrung.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="h1 fw-bold text-primary-custom mb-4">Warum Inlando?</h2>
                    <p class="lead">
                        Vertrauen, Sicherheit und einfache Bedienung stehen bei uns im Mittelpunkt.
                        Entdecken Sie die Vorteile unserer Plattform.
                    </p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex">
                        <div class="feature-icon me-4">
                            <i class="ti ti-shield-check"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-3">Sicher & Vertrauensvoll</h5>
                            <p class="text-muted">
                                Alle Nutzer werden verifiziert. Bewertungssystem und
                                sichere Zahlungsabwicklung für maximales Vertrauen.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex">
                        <div class="feature-icon me-4">
                            <i class="ti ti-map-pin"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-3">Lokal & Nachhaltig</h5>
                            <p class="text-muted">
                                Finden Sie Artikel in Ihrer direkten Umgebung.
                                Kurze Wege bedeuten weniger CO₂ und mehr Nachhaltigkeit.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex">
                        <div class="feature-icon me-4">
                            <i class="ti ti-headset"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-3">Persönlicher Support</h5>
                            <p class="text-muted">
                                Unser deutschsprachiges Support-Team hilft Ihnen
                                bei Fragen gerne weiter – schnell und unkompliziert.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex">
                        <div class="feature-icon me-4">
                            <i class="ti ti-coins"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-3">Geld Sparen & Verdienen</h5>
                            <p class="text-muted">
                                Mieten statt kaufen spart Geld. Gleichzeitig können Sie
                                mit Ihren ungenutzten Gegenständen Geld verdienen.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex">
                        <div class="feature-icon me-4">
                            <i class="ti ti-device-mobile"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-3">Einfach & Mobil</h5>
                            <p class="text-muted">
                                Unsere Plattform ist intuitiv bedienbar und funktioniert
                                perfekt auf allen Geräten – vom Smartphone bis Desktop.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex">
                        <div class="feature-icon me-4">
                            <i class="ti ti-users"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-3">Starke Community</h5>
                            <p class="text-muted">
                                Werden Sie Teil einer wachsenden Gemeinschaft von Menschen,
                                die Nachhaltigkeit und Sharing-Economy leben.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="section-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="h1 fw-bold text-primary-custom mb-4">Unser Team</h2>
                    <p class="lead">
                        Hinter Inlando steht ein leidenschaftliches Team aus Entwicklern, Designern und
                        Sharing-Economy-Experten, die an eine nachhaltigere Zukunft glauben.
                    </p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="team-card">
                        <div class="team-avatar">
                            <i class="ti ti-user-circle"></i>
                        </div>
                        <div class="p-4">
                            <h5 class="fw-bold mb-1">Max Mustermann</h5>
                            <p class="text-primary-custom mb-2">Gründer & CEO</p>
                            <p class="text-muted small">
                                Experte für Sharing-Economy mit über 10 Jahren Erfahrung
                                in der Entwicklung nachhaltiger Plattformen.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-card">
                        <div class="team-avatar">
                            <i class="ti ti-user-circle"></i>
                        </div>
                        <div class="p-4">
                            <h5 class="fw-bold mb-1">Sarah Schmidt</h5>
                            <p class="text-primary-custom mb-2">CTO</p>
                            <p class="text-muted small">
                                Technologie-Expertin mit Fokus auf sichere und
                                skalierbare Web-Plattformen.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="team-card">
                        <div class="team-avatar">
                            <i class="ti ti-user-circle"></i>
                        </div>
                        <div class="p-4">
                            <h5 class="fw-bold mb-1">Tom Weber</h5>
                            <p class="text-primary-custom mb-2">Head of Marketing</p>
                            <p class="text-muted small">
                                Marketing-Spezialist mit Leidenschaft für Community-Building
                                und nachhaltiges Wachstum.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-5 fw-bold mb-4">Bereit für nachhaltige Vermietung?</h2>
                    <p class="lead mb-5">
                        Schließen Sie sich tausenden zufriedenen Nutzern an und entdecken Sie
                        die Vorteile der Sharing-Economy. Kostenlos registrieren und sofort loslegen!
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg waves-effect">
                            <i class="ti ti-user-plus me-2"></i>Jetzt registrieren
                        </a>
                        <a href="/" class="btn btn-outline-light btn-lg waves-effect">
                            <i class="ti ti-search me-2"></i>Artikel entdecken
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Info Section -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="h1 fw-bold text-primary-custom mb-4">Kontakt</h2>
                    <p class="lead ">
                        Haben Sie Fragen oder möchten Sie mehr über Inlando erfahren?
                        Wir freuen uns auf Ihre Nachricht!
                    </p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="feature-icon mx-auto">
                            <i class="ti ti-mail"></i>
                        </div>
                        <h5 class="fw-bold mb-3">E-Mail</h5>
                        <p class="text-muted">
                            <a href="mailto:info@inlando.de" class="text-decoration-none">info@inlando.de</a>
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="feature-icon mx-auto">
                            <i class="ti ti-phone"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Telefon</h5>
                        <p class="text-muted">
                            <a href="tel:+49301234567" class="text-decoration-none">+49 (0) 30 123 456 7</a>
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="feature-icon mx-auto">
                            <i class="ti ti-map-pin"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Adresse</h5>
                        <p class="text-muted">
                            Inlando GmbH<br>
                            Musterstraße 123<br>
                            10115 Berlin, Deutschland
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Add smooth scrolling to anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add animation on scroll for stats cards
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe stats cards
            document.querySelectorAll('.stats-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });

            // Observe team cards
            document.querySelectorAll('.team-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
        });
    </script>
@endsection