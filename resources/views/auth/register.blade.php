@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Registrieren')

@section('styles')
<style>
    .auth-wrapper {
        min-height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        padding: 40px 0;
    }

    .auth-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        max-width: 900px;
        width: 100%;
    }

    .auth-left {
        background: linear-gradient(45deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)), 
                    url('{{ asset("assets/img/backgrounds/auth-bg.jpg") }}');
        background-size: cover;
        background-position: center;
        padding: 60px 40px;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
    }

    .auth-right {
        padding: 60px 40px;
    }

    .form-control {
        border-radius: 10px;
        border: 2px solid #f0f0f0;
        padding: 12px 20px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        padding: 12px 30px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .social-btn {
        border: 2px solid #f0f0f0;
        border-radius: 10px;
        padding: 12px;
        color: #666;
        transition: all 0.3s ease;
    }

    .social-btn:hover {
        border-color: #667eea;
        color: #667eea;
        transform: translateY(-2px);
    }

    .auth-illustration {
        max-width: 300px;
        margin: 0 auto 30px;
    }

    @media (max-width: 768px) {
        .auth-left {
            display: none;
        }
        
        .auth-right {
            padding: 40px 20px;
        }
    }
</style>
@endsection

@section('content')
<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="auth-card">
                    <div class="row g-0">
                        <!-- Left Side - Illustration -->
                        <div class="col-lg-6 auth-left">
                            <div class="auth-illustration">
                                <svg width="280" height="200" viewBox="0 0 280 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="140" cy="100" r="80" fill="rgba(255,255,255,0.1)" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
                                    <rect x="100" y="60" width="80" height="80" rx="10" fill="rgba(255,255,255,0.9)"/>
                                    <circle cx="130" cy="85" r="8" fill="#667eea"/>
                                    <circle cx="150" cy="85" r="8" fill="#764ba2"/>
                                    <rect x="115" y="105" width="50" height="3" rx="1" fill="#667eea"/>
                                    <rect x="115" y="115" width="30" height="3" rx="1" fill="#ccc"/>
                                    <rect x="110" y="130" width="60" height="8" rx="4" fill="#667eea"/>
                                </svg>
                            </div>
                            <h2 class="fw-bold mb-4 text-white">Werde Teil unserer Community!</h2>
                            <p class="mb-0">
                                Registriere dich kostenlos und teile oder miete Artikel in deiner Nähe.
                            </p>
                        </div>

                        <!-- Right Side - Form -->
                        <div class="col-lg-6 auth-right">
                            <div class="text-center mb-4">
                                <h3 class="fw-bold text-heading mb-2">Registrieren</h3>
                                <p class="text-muted">Erstelle dein kostenloses Konto</p>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger mb-4" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <!-- Name -->
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">Name</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           required 
                                           autofocus
                                           placeholder="Dein vollständiger Name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">E-Mail-Adresse</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required
                                           placeholder="deine@email.de">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password" class="form-label fw-semibold">Passwort</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password" 
                                               required
                                               placeholder="Mindestens 8 Zeichen">
                                        <span class="input-group-text cursor-pointer" onclick="togglePassword('password')">
                                            <i class="ti ti-eye" id="password-toggle"></i>
                                        </span>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label fw-semibold">Passwort bestätigen</label>
                                    <div class="input-group">
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation" 
                                               required
                                               placeholder="Passwort wiederholen">
                                        <span class="input-group-text cursor-pointer" onclick="togglePassword('password_confirmation')">
                                            <i class="ti ti-eye" id="password_confirmation-toggle"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Terms and Conditions -->
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                        <label class="form-check-label" for="terms">
                                            Ich stimme den <a href="#" class="text-decoration-none">Nutzungsbedingungen</a> 
                                            und der <a href="#" class="text-decoration-none">Datenschutzerklärung</a> zu
                                        </label>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary w-100 waves-effect waves-light mb-4">
                                    Konto erstellen
                                </button>

                                <!-- Divider -->
                                <div class="text-center mb-4">
                                    <span class="text-muted">oder</span>
                                </div>

                                <!-- Social Registration -->
                                <div class="row g-2 mb-4">
                                    <div class="col-6">
                                        <a href="#" class="btn social-btn w-100 text-decoration-none">
                                            <i class="ti ti-brand-google me-2"></i>Google
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="#" class="btn social-btn w-100 text-decoration-none">
                                            <i class="ti ti-brand-facebook me-2"></i>Facebook
                                        </a>
                                    </div>
                                </div>

                                <!-- Login Link -->
                                <div class="text-center">
                                    <p class="text-muted mb-0">
                                        Bereits ein Konto? 
                                        <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">
                                            Jetzt anmelden
                                        </a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggle = document.getElementById(fieldId + '-toggle');
    
    if (field.type === 'password') {
        field.type = 'text';
        toggle.classList.remove('ti-eye');
        toggle.classList.add('ti-eye-off');
    } else {
        field.type = 'password';
        toggle.classList.remove('ti-eye-off');
        toggle.classList.add('ti-eye');
    }
}
</script>
@endsection
