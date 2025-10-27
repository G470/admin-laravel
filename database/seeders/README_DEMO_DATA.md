# 🎯 Demo-Daten für Inlando Rental Platform

Diese Demo-Seeder erstellen umfassende Test- und Demonstrationsdaten für die Inlando Rental Platform.

## 📊 Übersicht der Demo-Daten

### 1. **Buchungen (BookingDemoSeeder)**
- **Anzahl**: ~150 Buchungen
- **Status**: `pending`, `confirmed`, `cancelled`, `completed`
- **Rental-Typen**: `hourly`, `daily`, `once`
- **Features**:
  - Realistische Daten basierend auf Status
  - Berechnete Preise basierend auf Rental-Typ
  - Vermieter-Notizen je nach Status
  - Provision (15%) automatisch berechnet
  - Guest-Daten für alle Buchungen

### 2. **Nachrichten (BookingMessagesDemoSeeder)**
- **Anzahl**: ~200+ Nachrichten
- **Verteilung**: 80% der Buchungen haben Nachrichten
- **Features**:
  - Realistische Konversationen zwischen Vendor und Kunde
  - Kontextabhängige Nachrichten je nach Buchungsstatus
  - Lesebestätigungen (85% gelesen)
  - Zeitliche Abfolge der Nachrichten

### 3. **Reviews (ReviewsDemoSeeder)**
- **Anzahl**: ~100+ Reviews
- **Verteilung**: 70% der abgeschlossenen Buchungen bekommen Reviews
- **Features**:
  - Realistische Bewertungsverteilung (mehr 4-5 Sterne)
  - Kontextabhängige Kommentare basierend auf Rating
  - Verknüpfung mit tatsächlichen Buchungen
  - Status: 75% published, 25% pending
  - 80% verifizierte Reviews

### 4. **Statistiken (RentalStatisticsSeeder)**
- **Zeitraum**: 365 Tage historische Daten
- **Metriken**: Views, Favorites, Inquiries, Bookings, Revenue
- **Features**:
  - Saisonale Anpassungen (Sommer/Winter)
  - Wochentag-Effekte (Wochenende weniger Business)
  - Kategorie-basierte Popularität
  - Featured Rentals bekommen mehr Traffic
  - Preis-basierte Popularity-Faktoren

## 🚀 Verwendung

### Alle Demo-Daten erstellen:
```bash
php artisan db:seed --class=DemoDataSeeder
```

### Einzelne Seeder ausführen:
```bash
php artisan db:seed --class=BookingDemoSeeder
php artisan db:seed --class=BookingMessagesDemoSeeder
php artisan db:seed --class=ReviewsDemoSeeder
php artisan db:seed --class=RentalStatisticsSeeder
```

### Mit Custom Command:
```bash
# Neue Demo-Daten erstellen
php artisan seed:demo-data

# Bestehende Demo-Daten löschen und neu erstellen
php artisan seed:demo-data --fresh
```

## 📋 Voraussetzungen

Die folgenden Daten müssen bereits existieren:
- ✅ **Users** (Vendors und Customers)
- ✅ **Rentals** (aktive Mietobjekte)
- ✅ **Categories** (für Rental-Zuordnung)
- ✅ **Locations** (für geografische Daten)

## 🎯 Realistische Datenverteilung

### Buchungsstatus:
- `pending`: 25%
- `confirmed`: 35%
- `cancelled`: 15%
- `completed`: 25%

### Bewertungsverteilung:
- ⭐⭐⭐⭐⭐ (5 Sterne): 37%
- ⭐⭐⭐⭐ (4 Sterne): 35%
- ⭐⭐⭐ (3 Sterne): 15%
- ⭐⭐ (2 Sterne): 8%
- ⭐ (1 Stern): 5%

### Saisonale Trends:
- **Frühling/Sommer**: +80% für Outdoor/Event-Equipment
- **Winter**: +40% für Indoor/Heizungs-Equipment
- **Weihnachtszeit**: +120% für Event/Party-Equipment
- **Bausaison**: +60% für Baumaschinen (März-September)

## 🔧 Technische Details

### Datenbankstruktur:
```sql
-- Neue Tabelle für Statistiken
rental_statistics (
    rental_id, date, views, favorites, 
    inquiries, bookings, revenue
)
```

### Beziehungen:
- `bookings` → `rental_id`, `renter_id`
- `booking_messages` → `booking_id`, `user_id`
- `reviews` → `rental_id`, `user_id`
- `rental_statistics` → `rental_id`

### Performance:
- Indizes auf häufig abgefragte Felder
- Unique Constraints für Datenintegrität
- Soft Deletes wo sinnvoll

## 📊 Verwendung für Dashboards

Die generierten Daten eignen sich perfekt für:
- **Vendor Dashboards**: Booking-Übersichten, Umsatzstatistiken
- **Admin Analytics**: Platform-weite Metriken, Trend-Analysen
- **Customer Views**: Review-Systeme, Buchungshistorie
- **Reporting**: Performance-Reports, Conversion-Analysen

## 🎨 Demo-Szenarien

Die Daten unterstützen folgende Demo-Szenarien:
1. **Vendor Onboarding**: Zeige wie erfolgreiche Vermietung aussieht
2. **Customer Journey**: Von Suche bis Review
3. **Admin Management**: Platform-Überwachung und -steuerung
4. **Support Cases**: Verschiedene Buchungsstatus und Probleme
5. **Business Intelligence**: Trend-Analysen und Forecasting

## 🔄 Aktualisierung

Die Demo-Daten können jederzeit mit `--fresh` Flag neu generiert werden:
```bash
php artisan seed:demo-data --fresh
```

Dies löscht alle bestehenden Demo-Daten und erstellt neue, ohne die Grunddaten (Users, Rentals, etc.) zu beeinträchtigen.
