@extends('layouts/contentNavbarLayoutFrontend')

@section('title', $rental->title . ' - Mieten')

@section('styles')
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        .rental-gallery {
            position: relative;
        }

        .rental-gallery .main-image {
            height: 400px;
            object-fit: cover;
            border-radius: 0.5rem;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }

        /* Swiper Styles */
        .rental-swiper {
            width: 100%;
            height: 400px;
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .rental-swiper .swiper-slide {
            text-align: center;
            font-size: 18px;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .rental-swiper .swiper-slide img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .rental-swiper .swiper-button-next,
        .rental-swiper .swiper-button-prev {
            color: var(--bs-primary);
            background: rgba(255, 255, 255, 0.9);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-top: -20px;
        }

        .rental-swiper .swiper-button-next:after,
        .rental-swiper .swiper-button-prev:after {
            font-size: 16px;
            font-weight: bold;
        }

        .rental-swiper .swiper-pagination-bullet {
            background: var(--bs-primary);
            opacity: 0.4;
        }

        .rental-swiper .swiper-pagination-bullet-active {
            opacity: 1;
        }

        .rental-thumbnail-swiper {
            height: 80px;
            margin-top: 10px;
        }

        .rental-thumbnail-swiper .swiper-slide {
            width: auto;
            height: 80px;
            cursor: pointer;
            opacity: 0.6;
            border-radius: 0.375rem;
            overflow: hidden;
        }

        .rental-thumbnail-swiper .swiper-slide-thumb-active {
            opacity: 1;
        }

        .rental-thumbnail-swiper .swiper-slide img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }

        .amenity-badge {
            background-color: rgba(var(--bs-success-rgb), 0.1);
            border: 1px solid rgba(var(--bs-success-rgb), 0.2);
            color: var(--bs-success);
        }

        .vendor-card {
            border: 1px solid var(--bs-border-color);
            border-radius: 0.5rem;
            padding: 1.5rem;
            background: var(--bs-body-bg);
        }

        .booking-card {
            position: sticky;
            top: 2rem;
            border: 1px solid var(--bs-border-color);
            border-radius: 0.5rem;
            padding: 1.5rem;
            background: var(--bs-body-bg);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .price-display {
            font-size: 2rem;
            font-weight: 700;
            color: var(--bs-primary);
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: var(--bs-breadcrumb-divider, ">") !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('search') }}">Suche</a></li>
                @if($rental->category)
                    <li class="breadcrumb-item"><a
                            href="{{ route('search', ['query' => $rental->category->name]) }}">{{ $rental->category->name }}</a>
                    </li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ $rental->title }}</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Rental Gallery -->
                @livewire('frontend.rental-gallery', ['rental' => $rental], key('rental-gallery-'.$rental->id))

                <!-- Rental Info -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h1 class="h3 mb-2">{{ $rental->title }}</h1>
                                <div class="d-flex align-items-center text-muted mb-2">
                                    <i class="ti ti-map-pin me-1"></i>
                                    @if($rental->location)
                                        <span>{{ $rental->location->city }}, {{ $rental->location->postcode }}</span>
                                    @else
                                        <span>Standort wird bei Buchung bekannt gegeben</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-end">
                                @if($rental->category)
                                    <span class="badge bg-label-primary">{{ $rental->category->name }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5>Beschreibung</h5>
                            <p class="text-body">{{ $rental->description ?: 'Keine Beschreibung verfügbar.' }}</p>
                        </div>

                        {{-- Dynamic Field Display --}}
                        @livewire('frontend.rental-field-display', ['rental' => $rental], 'rental-fields-' . $rental->id)

                        <!-- Mietbedingungen -->
                        @if(!empty($rental->rules) && is_array($rental->rules))
                            <div class="mb-4">
                                <h5>Mietbedingungen</h5>
                                <ul class="list-unstyled">
                                    @foreach($rental->rules as $rule)
                                        <li class="mb-2">
                                            <i class="ti ti-check text-success me-2"></i>
                                            {{ $rule }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-4">
                            <h5>Preise</h5>
                            <div class="row">
                                @if($rental->price_range_hour > 0)
                                    <div class="col-md-4 mb-3">
                                        <div class="card bg-label-info">
                                            <div class="card-body text-center">
                                                <i class="ti ti-clock ti-lg mb-2"></i>
                                                <h6>Stundenpreis</h6>
                                                <h4 class="text-info">{{ $rental->price_range_hour }}€</h4>
                                                <small class="text-muted">pro Stunde</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($rental->price_range_day > 0)
                                    <div class="col-md-4 mb-3">
                                        <div class="card bg-label-primary">
                                            <div class="card-body text-center">
                                                <i class="ti ti-calendar ti-lg mb-2"></i>
                                                <h6>Tagespreis</h6>
                                                <h4 class="text-primary">{{ $rental->price_range_day }}€</h4>
                                                <small class="text-muted">pro Tag</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($rental->price_range_once > 0)
                                    <div class="col-md-4 mb-3">
                                        <div class="card bg-label-success">
                                            <div class="card-body text-center">
                                                <i class="ti ti-receipt ti-lg mb-2"></i>
                                                <h6>Pauschalpreis</h6>
                                                <h4 class="text-success">{{ $rental->price_range_once }}€</h4>
                                                <small class="text-muted">einmalig</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($rental->service_fee > 0)
                                <div class="alert alert-info">
                                    <i class="ti ti-info-circle me-1"></i>
                                    <strong>Servicegebühr:</strong> {{ $rental->service_fee }}€ wird zusätzlich berechnet
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <h5>Verfügbarkeit</h5>
                            <div class="alert alert-success">
                                <i class="ti ti-check me-1"></i>
                                <strong>Verfügbar:</strong> Kontaktieren Sie den Vermieter für aktuelle Verfügbarkeit
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vendor Info -->
                <div class="vendor-card mb-4">
                    <h5 class="mb-3">Vermieter</h5>
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-lg me-3">
                            <a href="{{ route('vendor.profile', $rental->vendor->id) }}">
                                <img src="{{ asset('assets/img/avatars/1.png') }}" alt="{{ $rental->vendor->name }}"
                                    class="rounded-circle">
                            </a>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <a href="{{ route('vendor.profile', $rental->vendor->id) }}" class="text-decoration-none">
                                    {{ $rental->vendor->name }}
                                </a>
                            </h6>
                            <p class="text-muted mb-1">Vermieter seit {{ $rental->vendor->created_at->format('Y') }}</p>
                            <div class="d-flex align-items-center text-muted">
                                <i class="ti ti-mail me-1"></i>
                                <span>{{ $rental->vendor->email }}</span>
                            </div>
                            <a href="{{ route('vendor.profile', $rental->vendor->id) }}" class="text-decoration-none small">
                                <i class="ti ti-eye me-1"></i>Vollständiges Profil ansehen
                            </a>
                        </div>
                        <div>
                            <button class="btn btn-outline-primary">
                                <i class="ti ti-message-circle me-1"></i>
                                Kontakt
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Other Rentals by same vendor -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Weitere Artikel vom Vermieter</h5>
                        <div class="row g-3">
                            @foreach($rental->vendor->rentals as $otherRental)
                                @if($otherRental->id !== $rental->id)
                                    <div class="col-12">
                                        <x-rental-card :rental="$otherRental" />
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Booking Card -->
                <div class="booking-card">
                    <div class="text-center mb-3">
                        <div class="price-display">{{ $rental->price_range_day }}€</div>
                        <small class="text-muted">pro Tag</small>
                        @if($rental->price_range_hour > 0)
                            <div class="mt-2">
                                <span class="h5">{{ $rental->price_range_hour }}€</span>
                                <small class="text-muted">pro Stunde</small>
                            </div>
                        @endif
                    </div>

                    <form>
                        <div class="row mb-3">
                            <div class="col-12">
                                <!-- Date Range Picker -->
                                <div class="form-floating position-relative">
                                    <input type="text" class="form-control" id="dateRange" name="dateRange"
                                        placeholder="Zeitraum auswählen" readonly value="{{ $dateRange }}">
                                    <label for="dateRange">Zeitraum</label>
                                    <button type="button"
                                        class="btn btn-lg btn-outline-secondary clear-btn {{ !empty($dateRange) ? 'd-none' : '' }} p-4"
                                        data-target="dateRange"
                                        style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); z-index: 10; ">
                                        <i class="ti ti-x" style="font-size: 12px;"></i>
                                    </button>
                                </div>

                            </div>
                            <div class="col-6">
                                <input type="hidden" class="form-control" id="dateFrom" name="dateFrom">
                            </div>
                            <div class="col-6">
                                <input type="hidden" class="form-control" id="dateTo" name="dateTo">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="rentalType" class="form-label">Mietart</label>
                            <select class="form-select" id="rentalType" name="rentalType">
                                @if($rental->price_range_hour > 0)
                                    <option value="hourly">Stundenweise ({{ $rental->price_range_hour }}€/h)</option>
                                @endif
                                @if($rental->price_range_day > 0)
                                    <option value="daily">Täglich ({{ $rental->price_range_day }}€/Tag)</option>
                                @endif
                                @if($rental->price_range_once > 0)
                                    <option value="once">Pauschal ({{ $rental->price_range_once }}€)</option>
                                @endif
                            </select>
                        </div>
                        <!-- Standorte -->
                        <div class="mb-3">
                            <label for="location" class="form-label">Standort</label>
                            <!-- select of available locations for this rental. Default is set from request -->
                            <select class="form-select" id="location" name="location">
                                @if($rental->location)
                                    <option value="{{ $rental->location->id }}" selected>{{ $rental->location->city }},
                                        {{ $rental->location->postcode }}
                                    </option>
                                @else
                                    <option value="">Standort wird bei Buchung bekannt gegeben</option>
                                @endif
                            </select>
                        </div>

                        @auth
                            <button type="button" class="btn btn-primary w-100 mb-3" onclick="requestBooking()">
                                <i class="ti ti-calendar-plus me-1"></i>
                                Jetzt anfragen
                            </button>
                        @else
                            <a href="{{ route('rental.request', $rental->id) }}" class="btn btn-primary w-100 mb-3">
                                <i class="ti ti-calendar-plus me-1"></i>
                                Jetzt anfragen
                            </a>
                        @endauth

                        <button type="button" class="btn btn-outline-primary w-100 mb-2">
                            <i class="ti ti-heart me-1"></i>
                            Zu Favoriten
                        </button>

                        <button type="button" class="btn btn-outline-secondary w-100">
                            <i class="ti ti-share me-1"></i>
                            Teilen
                        </button>
                    </form>

                    <hr>

                    <div class="small text-muted">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Grundpreis (3 Tage)</span>
                            <span>{{ number_format($rental->price_range_day * 3, 2) }}€</span>
                        </div>
                        @if($rental->service_fee > 0)
                            <div class="d-flex justify-content-between mb-1">
                                <span>Servicegebühr</span>
                                <span>{{ number_format($rental->service_fee, 2) }}€</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mb-1">
                            <span>Kaution</span>
                            <span>Nach Vereinbarung</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-semibold">
                            <span>Gesamt (ca.)</span>
                            <span>{{ number_format($rental->price_range_day * 3 + $rental->service_fee, 2) }}€</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h6 class="card-title">Wichtige Informationen</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="ti ti-clock text-primary me-2"></i>
                                <span class="small">Schnelle Antwort vom Vermieter</span>
                            </li>
                            <li class="mb-2">
                                <i class="ti ti-shield-check text-success me-2"></i>
                                <span class="small">Sicherer Zahlungsschutz</span>
                            </li>
                            <li class="mb-2">
                                <i class="ti ti-phone text-info me-2"></i>
                                <span class="small">Kundenservice verfügbar</span>
                            </li>
                            <li>
                                <i class="ti ti-message-circle text-warning me-2"></i>
                                <span class="small">Direkte Kommunikation mit Vermieter</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Swiper JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        function requestBooking() {
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;
            const rentalType = document.getElementById('rentalType').value;

            if (!dateFrom || !dateTo) {
                alert('Bitte wählen Sie ein Von- und Bis-Datum aus.');
                return;
            }

            if (new Date(dateFrom) >= new Date(dateTo)) {
                alert('Das Bis-Datum muss nach dem Von-Datum liegen.');
                return;
            }

            // Here you would typically redirect to a booking page or open a modal
            alert('Buchungsanfrage würde gesendet werden für:\n' +
                'Zeitraum: ' + dateFrom + ' - ' + dateTo + '\n' +
                'Mietart: ' + rentalType);
        }

        // Set minimum date to today
        document.getElementById('dateFrom').min = new Date().toISOString().split('T')[0];
        document.getElementById('dateTo').min = new Date().toISOString().split('T')[0];

        // Update "Bis" minimum date when "Von" date changes
        document.getElementById('dateFrom').addEventListener('change', function () {
            document.getElementById('dateTo').min = this.value;
        });
    </script>
@endsection