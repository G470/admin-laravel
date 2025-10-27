@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Über uns')

@section('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 80px 0;
    }

    .team-card {
        transition: all 0.25s ease;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .team-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .team-member-image {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: #667eea;
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        margin: 0 auto 20px;
    }
</style>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4 text-white">Über Inlando</h1>
                    <p class="lead mb-4">
                        Wir verbinden Menschen, die Gegenstände vermieten möchten, mit denen, die sie benötigen. 
                        Unsere Mission ist es, das Teilen von Ressourcen einfach, sicher und nachhaltig zu gestalten.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="section-py">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="display-6 fw-bold mb-4">Unsere Geschichte</h2>
                    <p class="text-body mb-4">
                        Inlando wurde 2020 mit einer einfachen Idee gegründet: Warum sollten Menschen teure Gegenstände kaufen, 
                        die sie nur selten nutzen? Gleichzeitig haben viele Menschen ungenutzte Artikel zu Hause, mit denen sie 
                        ein zusätzliches Einkommen generieren könnten.
                    </p>
                    <p class="text-body mb-4">
                        Heute sind wir Deutschlands führende Plattform für Peer-to-Peer-Vermietungen und haben bereits 
                        über 100.000 erfolgreiche Vermietungen ermöglicht.
                    </p>
                    <div class="row">
                        <div class="col-4 text-center">
                            <div class="stat-number">50k+</div>
                            <p class="small text-muted">Registrierte Nutzer</p>
                        </div>
                        <div class="col-4 text-center">
                            <div class="stat-number">100k+</div>
                            <p class="small text-muted">Erfolgreiche Vermietungen</p>
                        </div>
                        <div class="col-4 text-center">
                            <div class="stat-number">500+</div>
                            <p class="small text-muted">Städte</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5">
                            <div class="row g-4">
                                <div class="col-12 text-center">
                                    <div class="feature-icon">
                                        <i class="ti ti-handshake"></i>
                                    </div>
                                    <h5 class="fw-semibold">Vertrauen</h5>
                                    <p class="text-body small">Sichere Transaktionen und verifizierte Nutzer</p>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="feature-icon">
                                        <i class="ti ti-leaf"></i>
                                    </div>
                                    <h6 class="fw-semibold">Nachhaltigkeit</h6>
                                    <p class="text-body small">Weniger Konsum, mehr Teilen</p>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="feature-icon">
                                        <i class="ti ti-users"></i>
                                    </div>
                                    <h6 class="fw-semibold">Gemeinschaft</h6>
                                    <p class="text-body small">Lokale Verbindungen stärken</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="section-py bg-body">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="display-6 fw-bold mb-4">Unser Team</h2>
                    <p class="text-body">
                        Lerne die Menschen kennen, die Inlando jeden Tag besser machen.
                    </p>
                </div>
            </div>
            
            <div class="row g-4 justify-content-center">
                @foreach($teamMembers as $member)
                    <div class="col-lg-4 col-md-6">
                        <div class="card team-card h-100">
                            <div class="card-body text-center p-4">
                                <img src="{{ $member->image }}" alt="{{ $member->name }}" class="team-member-image rounded-circle mb-3">
                                <h5 class="fw-semibold text-heading">{{ $member->name }}</h5>
                                <p class="text-primary fw-medium mb-3">{{ $member->position }}</p>
                                <p class="text-body small">{{ $member->description }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="section-py">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="display-6 fw-bold mb-4">Unsere Werte</h2>
                    <p class="text-body">
                        Diese Prinzipien leiten uns bei allem, was wir tun.
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="ti ti-shield-check"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Sicherheit</h5>
                        <p class="text-body">
                            Alle Transaktionen sind versichert und jeder Nutzer wird verifiziert, um ein sicheres Umfeld zu schaffen.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="ti ti-heart"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Fairness</h5>
                        <p class="text-body">
                            Faire Preise für alle Beteiligten und transparente Konditionen ohne versteckte Kosten.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="ti ti-world"></i>
                        </div>
                        <h5 class="fw-semibold mb-3">Nachhaltigkeit</h5>
                        <p class="text-body">
                            Durch das Teilen von Ressourcen reduzieren wir Verschwendung und schonen die Umwelt.
                        </p>
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
                    <h2 class="display-6 fw-bold text-white mb-4">Werde Teil unserer Community</h2>
                    <p class="text-white mb-4">
                        Entdecke, was du in deiner Nähe mieten kannst oder verdiene Geld mit ungenutzten Gegenständen.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg waves-effect waves-light">
                            Jetzt registrieren
                        </a>
                        <a href="{{ route('rent-out') }}" class="btn btn-outline-light btn-lg waves-effect">
                            Artikel vermieten
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
