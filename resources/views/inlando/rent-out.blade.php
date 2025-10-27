@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Jetzt vermieten')

@section('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 80px 0;
    }

    .benefit-card {
        transition: all 0.25s ease;
        border: none;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        height: 100%;
    }

    .benefit-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }

    .benefit-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 32px;
        margin: 0 auto 20px;
    }

    .benefit-icon.success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
    .benefit-icon.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .benefit-icon.info { background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%); }
    .benefit-icon.warning { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); }

    .earnings-card {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border: 2px solid rgba(102, 126, 234, 0.2);
        transition: all 0.25s ease;
    }

    .earnings-card:hover {
        border-color: #667eea;
        transform: translateY(-3px);
    }

    .earnings-amount {
        font-size: 1.25rem;
        font-weight: bold;
        color: #667eea;
    }

    .step-counter {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .stats-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: #667eea;
    }

    .calculator-section {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    }
</style>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4 text-white">Verdiene Geld mit ungenutzten Gegenständen</h1>
                    <p class="lead mb-4">
                        Verwandle deine ungenutzten Artikel in eine zusätzliche Einkommensquelle. 
                        Einfach, sicher und profitabel.
                    </p>
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg waves-effect waves-light">
                        <i class="ti ti-plus me-2"></i>Jetzt kostenlos starten
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="section-py">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="display-6 fw-bold mb-4">Warum bei Inlando vermieten?</h2>
                    <p class="text-body">
                        Entdecke die Vorteile unserer Plattform für Vermieter.
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                @foreach($benefits as $benefit)
                    <div class="col-lg-3 col-md-6">
                        <div class="card benefit-card">
                            <div class="card-body text-center p-4">
                                <div class="benefit-icon {{ $benefit->color }}">
                                    <i class="{{ $benefit->icon }}"></i>
                                </div>
                                <h5 class="fw-semibold text-heading mb-3">{{ $benefit->title }}</h5>
                                <p class="text-body">{{ $benefit->description }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Earnings Potential Section -->
    <section class="section-py bg-body">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="display-6 fw-bold mb-4">Verdienstpotenzial</h2>
                    <p class="text-body">
                        Siehe, wie viel unsere Vermieter in verschiedenen Kategorien verdienen.
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                @foreach($categories as $category)
                    <div class="col-lg-3 col-md-6">
                        <div class="card earnings-card h-100">
                            <div class="card-body text-center p-4">
                                <img src="{{ $category->image }}" alt="{{ $category->name }}" class="mb-3" style="width: 80px; height: 80px; object-fit: contain;">
                                <h5 class="fw-semibold text-heading mb-2">{{ $category->name }}</h5>
                                <div class="earnings-amount">{{ $category->earnings }}</div>
                                <p class="text-muted small mb-0">Durchschnittliche Einnahmen</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="text-center mt-5">
                <p class="text-muted mb-0">
                    <i class="ti ti-info-circle me-1"></i>
                    Basierend auf Daten unserer aktiven Vermieter im Jahr 2024
                </p>
            </div>
        </div>
    </section>

    <!-- How to Start Section -->
    <section class="section-py">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="display-6 fw-bold mb-4">So einfach geht's</h2>
                    <p class="text-body">
                        In nur wenigen Schritten zu deinem ersten Vermietungserfolg.
                    </p>
                </div>
            </div>
            
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <div class="d-flex align-items-start mb-4">
                        <div class="step-counter">1</div>
                        <div>
                            <h5 class="fw-semibold mb-2">Artikel fotografieren</h5>
                            <p class="text-body mb-0">Mache aussagekräftige Fotos von deinem Artikel aus verschiedenen Blickwinkeln.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-4">
                        <div class="step-counter">2</div>
                        <div>
                            <h5 class="fw-semibold mb-2">Inserat erstellen</h5>
                            <p class="text-body mb-0">Beschreibe deinen Artikel detailliert und setze einen fairen Mietpreis fest.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start mb-4">
                        <div class="step-counter">3</div>
                        <div>
                            <h5 class="fw-semibold mb-2">Anfragen erhalten</h5>
                            <p class="text-body mb-0">Erhalte Buchungsanfragen von interessierten Mietern in deiner Nähe.</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-start">
                        <div class="step-counter">4</div>
                        <div>
                            <h5 class="fw-semibold mb-2">Verdienen</h5>
                            <p class="text-body mb-0">Übergib den Artikel und erhalte deine Vergütung automatisch nach der Rückgabe.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-4">
                            <h5 class="fw-semibold mb-4">Erfolgsstatistiken</h5>
                            
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="stats-number">95%</div>
                                    <p class="text-muted small mb-3">Erfolgreiche Vermietungen</p>
                                </div>
                                <div class="col-4">
                                    <div class="stats-number">€180</div>
                                    <p class="text-muted small mb-3">Ø Monatseinkommen</p>
                                </div>
                                <div class="col-4">
                                    <div class="stats-number">4.8</div>
                                    <p class="text-muted small mb-3">Ø Bewertung</p>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h6 class="fw-semibold mb-3">Meistgefragte Kategorien:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-primary">Werkzeuge</span>
                                <span class="badge bg-success">Gartengeräte</span>
                                <span class="badge bg-info">Elektronik</span>
                                <span class="badge bg-warning">Sportausrüstung</span>
                                <span class="badge bg-secondary">Baumaschinen</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Earnings Calculator Section -->
    <section class="section-py calculator-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <h3 class="fw-bold mb-3">Verdienstrechner</h3>
                                <p class="text-body">Berechne dein mögliches Einkommen mit unseren durchschnittlichen Mietpreisen.</p>
                            </div>
                            
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Kategorie</label>
                                    <select class="form-select" id="categorySelect">
                                        <option value="50">Werkzeuge (€50/Monat)</option>
                                        <option value="75">Gartengeräte (€75/Monat)</option>
                                        <option value="120">Elektronik (€120/Monat)</option>
                                        <option value="90">Sportausrüstung (€90/Monat)</option>
                                        <option value="200">Baumaschinen (€200/Monat)</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Anzahl Artikel</label>
                                    <input type="number" class="form-control" id="itemCount" value="1" min="1" max="10">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Monate</label>
                                    <input type="number" class="form-control" id="monthCount" value="12" min="1" max="12">
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <h4 class="fw-bold text-primary mb-2">Geschätztes Jahreseinkommen:</h4>
                                <div class="display-5 fw-bold text-success" id="calculatedEarnings">€600</div>
                                <p class="text-muted small mt-2">* Basierend auf durchschnittlichen Mietpreisen und 70% Auslastung</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Stories Section -->
    <section class="section-py">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="display-6 fw-bold mb-4">Erfolgsgeschichten</h2>
                    <p class="text-body">
                        Höre, was unsere Vermieter über ihre Erfahrungen sagen.
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary rounded-circle p-2 me-3">
                                    <i class="ti ti-user text-white"></i>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-0">Marcus K.</h6>
                                    <small class="text-muted">Vermieter seit 2022</small>
                                </div>
                            </div>
                            <p class="text-body mb-3">
                                "Mit meiner Bohrmaschine und dem Akkuschrauber verdiene ich jeden Monat etwa €80. 
                                Perfekt für die Artikel, die sonst nur im Keller stehen würden!"
                            </p>
                            <div class="text-warning">
                                <i class="ti ti-star-filled"></i>
                                <i class="ti ti-star-filled"></i>
                                <i class="ti ti-star-filled"></i>
                                <i class="ti ti-star-filled"></i>
                                <i class="ti ti-star-filled"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success rounded-circle p-2 me-3">
                                    <i class="ti ti-user text-white"></i>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-0">Sarah M.</h6>
                                    <small class="text-muted">Vermieterin seit 2021</small>
                                </div>
                            </div>
                            <p class="text-body mb-3">
                                "Mein Rasenmäher wird den ganzen Sommer über vermietet. 
                                Das bringt mir fast €150 pro Monat ein - mehr als ich erwartet hatte!"
                            </p>
                            <div class="text-warning">
                                <i class="ti ti-star-filled"></i>
                                <i class="ti ti-star-filled"></i>
                                <i class="ti ti-star-filled"></i>
                                <i class="ti ti-star-filled"></i>
                                <i class="ti ti-star-filled"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info rounded-circle p-2 me-3">
                                    <i class="ti ti-user text-white"></i>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-0">Thomas W.</h6>
                                    <small class="text-muted">Vermieter seit 2023</small>
                                </div>
                            </div>
                            <p class="text-body mb-3">
                                "Die Plattform ist super einfach zu bedienen und der Support hilft bei Fragen sofort. 
                                Ich kann Inlando jedem weiterempfehlen!"
                            </p>
                            <div class="text-warning">
                                <i class="ti ti-star-filled"></i>
                                <i class="ti ti-star-filled"></i>
                                <i class="ti ti-star-filled"></i>
                                <i class="ti ti-star-filled"></i>
                                <i class="ti ti-star-filled"></i>
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
                    <h2 class="display-6 fw-bold text-white mb-4">Starte noch heute</h2>
                    <p class="text-white mb-4">
                        Melde dich kostenlos an und beginne innerhalb von Minuten mit dem Vermieten deiner Artikel.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg waves-effect waves-light">
                            <i class="ti ti-user-plus me-2"></i>Kostenlos registrieren
                        </a>
                        <a href="{{ route('how-it-works') }}" class="btn btn-outline-light btn-lg waves-effect">
                            <i class="ti ti-help me-2"></i>Mehr erfahren
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    // Earnings Calculator
    function calculateEarnings() {
        const categoryValue = parseInt(document.getElementById('categorySelect').value);
        const itemCount = parseInt(document.getElementById('itemCount').value);
        const monthCount = parseInt(document.getElementById('monthCount').value);
        
        // Calculate with 70% average utilization
        const totalEarnings = categoryValue * itemCount * monthCount * 0.7;
        
        document.getElementById('calculatedEarnings').textContent = '€' + Math.round(totalEarnings);
    }
    
    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        calculateEarnings();
        
        document.getElementById('categorySelect').addEventListener('change', calculateEarnings);
        document.getElementById('itemCount').addEventListener('input', calculateEarnings);
        document.getElementById('monthCount').addEventListener('input', calculateEarnings);
    });
</script>
@endsection
