@extends('layouts/layoutMaster')

@section('title', 'Zwei-Faktor-Authentifizierung')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-account-settings.scss'])
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="nav-align-top">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="navs-pills-security" role="tabpanel">
                    <!-- Change Password -->
                    <div class="card mb-4">
                        <h5 class="card-header">Zwei-Faktor-Authentifizierung</h5>
                        <div class="card-body">
                            @if($hasEnabled)
                                <div class="alert alert-success" role="alert">
                                    <h6 class="alert-heading mb-1">✅ 2FA ist aktiviert</h6>
                                    <span>Ihr Konto ist durch Zwei-Faktor-Authentifizierung geschützt.</span>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12 col-lg-6 mb-3">
                                        <a href="{{ route('two-factor.recovery-codes') }}" class="btn btn-primary">
                                            <i class="ti ti-key me-2"></i>
                                            Wiederherstellungscodes anzeigen
                                        </a>
                                    </div>
                                    <div class="col-12 col-lg-6 mb-3">
                                        <form method="POST" action="{{ route('two-factor.disable') }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Passwort zur Bestätigung</label>
                                                <input type="password" class="form-control" id="password" name="password" required>
                                            </div>
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Sind Sie sicher, dass Sie 2FA deaktivieren möchten?')">
                                                <i class="ti ti-shield-off me-2"></i>
                                                2FA deaktivieren
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning" role="alert">
                                    <h6 class="alert-heading mb-1">⚠️ 2FA ist nicht aktiviert</h6>
                                    <span>Aktivieren Sie die Zwei-Faktor-Authentifizierung für zusätzliche Sicherheit.</span>
                                </div>
                                
                                <p class="mb-3">
                                    Die Zwei-Faktor-Authentifizierung (2FA) fügt eine zusätzliche Sicherheitsebene zu Ihrem Konto hinzu. 
                                    Sie benötigen eine Authenticator-App wie Google Authenticator oder Authy.
                                </p>
                                
                                <form method="POST" action="{{ route('two-factor.enable') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-shield-check me-2"></i>
                                        2FA einrichten
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
