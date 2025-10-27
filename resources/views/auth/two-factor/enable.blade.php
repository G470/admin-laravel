@extends('layouts/layoutMaster')

@section('title', '2FA einrichten')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Zwei-Faktor-Authentifizierung einrichten</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>1. QR-Code scannen</h6>
                        <p>Scannen Sie diesen QR-Code mit Ihrer Authenticator-App:</p>
                        <div class="text-center mb-3">
                            {!! $qrCode !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>2. Manueller Setup (optional)</h6>
                        <p>Falls Sie den QR-Code nicht scannen können, geben Sie diesen Schlüssel manuell ein:</p>
                        <div class="alert alert-secondary">
                            <code>{{ $secret }}</code>
                        </div>
                        
                        <h6>3. Code eingeben</h6>
                        <p>Geben Sie den 6-stelligen Code aus Ihrer Authenticator-App ein:</p>
                        
                        <form method="POST" action="{{ route('two-factor.confirm') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="code" class="form-label">Bestätigungscode</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" maxlength="6" pattern="[0-9]{6}" 
                                       placeholder="123456" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-check me-2"></i>
                                2FA aktivieren
                            </button>
                            <a href="{{ route('two-factor.index') }}" class="btn btn-secondary">
                                Abbrechen
                            </a>
                        </form>
                    </div>
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
            // Only allow numbers
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            
            // Auto-submit when 6 digits entered
            if (e.target.value.length === 6) {
                e.target.form.submit();
            }
        });
    }
});
</script>
@endsection
