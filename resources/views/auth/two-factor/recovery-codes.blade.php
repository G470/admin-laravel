@extends('layouts/layoutMaster')

@section('title', 'Wiederherstellungscodes')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Wiederherstellungscodes</h4>
            </div>
            <div class="card-body">
                @if(isset($regenerated) && $regenerated)
                    <div class="alert alert-success">
                        <h6 class="alert-heading">✅ Neue Codes generiert</h6>
                        Ihre alten Wiederherstellungscodes sind nicht mehr gültig.
                    </div>
                @endif
                
                <div class="alert alert-warning">
                    <h6 class="alert-heading">⚠️ Wichtiger Hinweis</h6>
                    <p class="mb-0">
                        Bewahren Sie diese Codes sicher auf! Sie können jeden Code nur einmal verwenden. 
                        Falls Sie Ihr Authenticator-Gerät verlieren, können Sie sich mit diesen Codes anmelden.
                    </p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <h6>Ihre Wiederherstellungscodes:</h6>
                        <div class="recovery-codes">
                            @foreach($recoveryCodes as $code)
                                <div class="code-item mb-2">
                                    <code class="fs-5">{{ $code }}</code>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Aktionen:</h6>
                        <button onclick="printCodes()" class="btn btn-outline-primary mb-2">
                            <i class="ti ti-printer me-2"></i>
                            Codes drucken
                        </button>
                        <br>
                        <button onclick="downloadCodes()" class="btn btn-outline-secondary mb-2">
                            <i class="ti ti-download me-2"></i>
                            Als Textdatei herunterladen
                        </button>
                        <br>
                        
                        <form method="POST" action="{{ route('two-factor.regenerate-recovery-codes') }}" class="mt-3">
                            @csrf
                            <div class="mb-3">
                                <label for="password" class="form-label">Passwort zur Bestätigung</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Sind Sie sicher? Ihre aktuellen Codes werden ungültig.')">
                                <i class="ti ti-refresh me-2"></i>
                                Neue Codes generieren
                            </button>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('two-factor.index') }}" class="btn btn-primary">
                        <i class="ti ti-arrow-left me-2"></i>
                        Zurück zu 2FA-Einstellungen
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
function printCodes() {
    const codes = @json($recoveryCodes);
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Wiederherstellungscodes - {{ config('app.name') }}</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    .header { border-bottom: 2px solid #ccc; padding-bottom: 10px; margin-bottom: 20px; }
                    .code { font-family: monospace; font-size: 16px; margin: 5px 0; }
                    .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; margin: 20px 0; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>{{ config('app.name') }} - Wiederherstellungscodes</h2>
                    <p>Datum: ${new Date().toLocaleDateString('de-DE')}</p>
                </div>
                <div class="warning">
                    <strong>⚠️ Wichtig:</strong> Bewahren Sie diese Codes sicher auf. Jeder Code kann nur einmal verwendet werden.
                </div>
                <h3>Ihre Wiederherstellungscodes:</h3>
                ${codes.map(code => `<div class="code">${code}</div>`).join('')}
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function downloadCodes() {
    const codes = @json($recoveryCodes);
    const content = `{{ config('app.name') }} - Wiederherstellungscodes
Datum: ${new Date().toLocaleDateString('de-DE')}

⚠️ WICHTIG: Bewahren Sie diese Codes sicher auf. Jeder Code kann nur einmal verwendet werden.

Ihre Wiederherstellungscodes:
${codes.join('\n')}

Notizen:
- Diese Codes ermöglichen den Zugang zu Ihrem Konto, wenn Ihr Authenticator-Gerät nicht verfügbar ist
- Verwenden Sie jeden Code nur einmal
- Generieren Sie neue Codes, sobald Sie mehrere verwendet haben
`;
    
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'inlando-wiederherstellungscodes-' + new Date().toISOString().split('T')[0] + '.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
</script>
@endsection
