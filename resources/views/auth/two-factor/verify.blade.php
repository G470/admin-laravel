@extends('layouts/blankLayout')

@section('title', '2FA Verifizierung')

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">
        <div class="card">
            <div class="card-body">
                <div class="app-brand justify-content-center mb-4">
                    <a href="{{ url('/') }}" class="app-brand-link gap-2">
                        <span class="app-brand-logo demo">
                            <!-- Logo here -->
                        </span>
                        <span class="app-brand-text demo text-body fw-bold">{{ config('app.name') }}</span>
                    </a>
                </div>

                <h4 class="mb-1">Zwei-Faktor-Authentifizierung</h4>
                <p class="mb-4">Geben Sie den 6-stelligen Code aus Ihrer Authenticator-App ein</p>

                <form method="POST" action="{{ route('two-factor.verify.post') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="code" class="form-label">Authentifizierungscode</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" 
                               id="code" name="code" maxlength="8" 
                               placeholder="123456 oder Wiederherstellungscode" 
                               autofocus required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Geben Sie einen 6-stelligen Code oder einen 8-stelligen Wiederherstellungscode ein
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary d-grid w-100">
                        Verifizieren
                    </button>
                </form>

                <div class="text-center mt-3">
                    <p class="text-muted">
                        Probleme beim Anmelden? 
                        <a href="{{ route('password.request') }}" class="fw-medium">Hilfe erhalten</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const codeInput = document.getElementById('code');
    if (codeInput) {
        codeInput.addEventListener('input', function(e) {
            // Allow numbers and letters for recovery codes
            e.target.value = e.target.value.replace(/[^0-9A-Za-z]/g, '').toUpperCase();
            
            // Auto-submit when 6 digits entered (TOTP)
            if (e.target.value.length === 6 && /^\d{6}$/.test(e.target.value)) {
                e.target.form.submit();
            }
        });
    }
});
</script>
@endsection
