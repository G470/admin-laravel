<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bestätigung Ihrer Mietanfrage</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #28a745;
        }
        .header h1 {
            color: #28a745;
            margin: 0;
            font-size: 24px;
        }
        .success-badge {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }
        .rental-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #495057;
        }
        .value {
            color: #6c757d;
        }
        .next-steps {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .step {
            margin: 10px 0;
            padding-left: 20px;
            position: relative;
        }
        .step:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #28a745;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>✅ Anfrage gesendet!</h1>
            <p>Ihre Mietanfrage wurde erfolgreich übermittelt</p>
        </div>

        <div class="success-badge">
            <h3 style="margin: 0 0 10px 0; color: #28a745;">🎉 Vielen Dank!</h3>
            <p style="margin: 0;">Ihre Anfrage wurde an den Vermieter weitergeleitet. Sie erhalten in Kürze eine Antwort.</p>
        </div>

        <div class="rental-info">
            <h3 style="margin-top: 0; color: #495057;">📦 Ihre Anfrage im Überblick</h3>
            <div class="info-row">
                <span class="label">Artikel:</span>
                <span class="value">{{ $rental->title }}</span>
            </div>
            @if($rental->category)
            <div class="info-row">
                <span class="label">Kategorie:</span>
                <span class="value">{{ $rental->category->name }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="label">Vermieter:</span>
                <span class="value">{{ $rental->vendor->name }}</span>
            </div>
            <div class="info-row">
                <span class="label">Von:</span>
                <span class="value">{{ \Carbon\Carbon::parse($request_details['date_from'])->format('d.m.Y') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Bis:</span>
                <span class="value">{{ \Carbon\Carbon::parse($request_details['date_to'])->format('d.m.Y') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Mietart:</span>
                <span class="value">
                    @switch($request_details['rental_type'])
                        @case('hourly')
                            Stundenweise
                            @break
                        @case('daily')
                            Täglich
                            @break
                        @case('once')
                            Pauschal
                            @break
                        @default
                            {{ $request_details['rental_type'] }}
                    @endswitch
                </span>
            </div>
            @if($request_details['estimated_price'] > 0)
            <div class="info-row">
                <span class="label">Geschätzter Preis:</span>
                <span class="value">{{ number_format($request_details['estimated_price'], 2) }}€</span>
            </div>
            @endif
        </div>

        <div class="next-steps">
            <h4 style="margin: 0 0 15px 0; color: #856404;">🚀 Wie geht es weiter?</h4>
            <div class="step">Der Vermieter erhält Ihre Anfrage und Ihre Kontaktdaten</div>
            <div class="step">Sie werden in der Regel innerhalb von 24 Stunden kontaktiert</div>
            <div class="step">Besprechen Sie Details wie genauen Preis, Übergabe und Kaution</div>
            <div class="step">Bei Einigung können Sie den Mietvertrag abschließen</div>
        </div>

        @if($request_details['message'])
        <div class="rental-info">
            <h4 style="margin: 0 0 15px 0; color: #495057;">💬 Ihre Nachricht</h4>
            <p style="margin: 0; white-space: pre-line; font-style: italic;">{{ $request_details['message'] }}</p>
        </div>
        @endif

        <div style="text-align: center; margin: 30px 0; padding: 20px; background: #e7f3ff; border-radius: 6px;">
            <h4 style="margin: 0 0 10px 0; color: #0d6efd;">💡 Tipp</h4>
            <p style="margin: 0; color: #495057;">
                Antworten Sie schnell auf die Nachricht des Vermieters, um Ihre Chancen zu erhöhen. 
                Beliebte Artikel sind oft schnell vergeben!
            </p>
        </div>

        <div class="footer">
            <p><strong>Fragen oder Probleme?</strong></p>
            <p>Kontaktieren Sie unseren Kundenservice unter support@{{ config('app.domain', 'example.com') }}</p>
            <p style="margin: 10px 0;">
                <a href="{{ config('app.url') }}" style="color: #667eea;">Zurück zur Website</a>
            </p>
            <hr style="margin: 20px 0;">
            <p style="font-size: 12px; color: #999;">
                Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.
            </p>
        </div>
    </div>
</body>
</html>
