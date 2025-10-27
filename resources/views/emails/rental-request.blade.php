<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Neue Mietanfrage</title>
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
            border-bottom: 2px solid #667eea;
        }
        .header h1 {
            color: #667eea;
            margin: 0;
            font-size: 24px;
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
        .message-box {
            background: #e7f3ff;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            margin: 20px 0;
        }
        .price-highlight {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
        }
        .price-amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
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
            <h1>🎉 Neue Mietanfrage!</h1>
            <p>Sie haben eine neue Anfrage für Ihr Mietobjekt erhalten</p>
        </div>

        <div class="rental-info">
            <h3 style="margin-top: 0; color: #495057;">📦 Angefragtes Objekt</h3>
            <div class="info-row">
                <span class="label">Titel:</span>
                <span class="value">{{ $rental->title }}</span>
            </div>
            @if($rental->category)
            <div class="info-row">
                <span class="label">Kategorie:</span>
                <span class="value">{{ $rental->category->name }}</span>
            </div>
            @endif
            @if($rental->location)
            <div class="info-row">
                <span class="label">Standort:</span>
                <span class="value">{{ $rental->location->city }}, {{ $rental->location->postcode }}</span>
            </div>
            @endif
        </div>

        <div class="rental-info">
            <h3 style="margin-top: 0; color: #495057;">👤 Interessent</h3>
            <div class="info-row">
                <span class="label">Name:</span>
                <span class="value">{{ $requester['name'] }}</span>
            </div>
            <div class="info-row">
                <span class="label">E-Mail:</span>
                <span class="value">{{ $requester['email'] }}</span>
            </div>
            @if($requester['phone'])
            <div class="info-row">
                <span class="label">Telefon:</span>
                <span class="value">{{ $requester['phone'] }}</span>
            </div>
            @endif
        </div>

        <div class="rental-info">
            <h3 style="margin-top: 0; color: #495057;">📅 Mietdetails</h3>
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
            @php
                $dateFrom = \Carbon\Carbon::parse($request_details['date_from']);
                $dateTo = \Carbon\Carbon::parse($request_details['date_to']);
                $duration = $dateFrom->diffInDays($dateTo);
            @endphp
            <div class="info-row">
                <span class="label">Dauer:</span>
                <span class="value">{{ $duration }} {{ $duration == 1 ? 'Tag' : 'Tage' }}</span>
            </div>
        </div>

        @if($request_details['estimated_price'] > 0)
        <div class="price-highlight">
            <h4 style="margin: 0 0 10px 0; color: #495057;">💰 Geschätzter Preis</h4>
            <div class="price-amount">{{ number_format($request_details['estimated_price'], 2) }}€</div>
            <small style="color: #6c757d;">*Unverbindliche Schätzung basierend auf Ihren Preisen</small>
        </div>
        @endif

        @if($request_details['message'])
        <div class="message-box">
            <h4 style="margin: 0 0 15px 0; color: #495057;">💬 Nachricht vom Interessenten</h4>
            <p style="margin: 0; white-space: pre-line;">{{ $request_details['message'] }}</p>
        </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <p><strong>Antworten Sie direkt auf diese E-Mail</strong> oder kontaktieren Sie den Interessenten über die angegebenen Kontaktdaten.</p>
            <p style="color: #6c757d; font-size: 14px;">
                Tipp: Eine schnelle Antwort erhöht Ihre Chancen auf eine erfolgreiche Vermietung!
            </p>
        </div>

        <div class="footer">
            <p>Diese E-Mail wurde automatisch von Ihrem Vermietungsportal gesendet.</p>
            <p style="margin: 5px 0;">
                <a href="{{ config('app.url') }}" style="color: #667eea;">{{ config('app.name') }}</a>
            </p>
        </div>
    </div>
</body>
</html>
