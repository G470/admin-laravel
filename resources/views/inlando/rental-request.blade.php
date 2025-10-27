@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Mietanfrage für ' . $rental->title)

@section('styles')
<style>
    .request-form-wrapper {
        min-height: 80vh;
        padding: 2rem 0;
    }
    
    .rental-summary {
        background: var(--bs-light);
        border-radius: 0.5rem;
        padding: 1.5rem;
        border: 1px solid var(--bs-border-color);
    }
    
    .rental-summary .rental-image {
        height: 150px;
        background: var(--bs-gray-200);
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--bs-gray-600);
    }
    
    .price-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 1rem;
        padding: 0.5rem 1rem;
        font-weight: 600;
    }
    
    .form-card {
        border: 1px solid var(--bs-border-color);
        border-radius: 0.75rem;
        padding: 2rem;
        background: white;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: var(--bs-breadcrumb-divider, ">") !important;
    }
    
    .estimated-price {
        background: rgba(var(--bs-success-rgb), 0.1);
        border: 1px solid rgba(var(--bs-success-rgb), 0.2);
        border-radius: 0.5rem;
        padding: 1rem;
    }
    
    /* Date Range Picker Styles */
    .daterangepicker {
        z-index: 3000;
    }
    
    .daterangepicker .ranges li {
        color: #333;
    }
    
    .daterangepicker .ranges li.active {
        background-color: var(--bs-primary);
        color: white;
    }
</style>

<!-- Date Range Picker CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')
<div class="container-xxl">
    <div class="request-form-wrapper">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('search') }}">Suche</a></li>
                @if($rental->category)
                <li class="breadcrumb-item"><a href="{{ route('search', ['query' => $rental->category->name]) }}">{{ $rental->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item"><a href="{{ route('rentals.show', $rental->id) }}">{{ $rental->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mietanfrage</li>
            </ol>
        </nav>

        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-x me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="row">
            <!-- Form -->
            <div class="col-lg-8">
                <div class="form-card">
                    <div class="text-center mb-4">
                        <h2 class="h3 fw-bold mb-2">Mietanfrage senden</h2>
                        <p class="text-muted">Füllen Sie das Formular aus, um eine Anfrage an den Vermieter zu senden</p>
                    </div>

                    <form method="POST" action="{{ route('rental-request-store', $rental->id) }}">
                        @csrf

                        <!-- Personal Information -->
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="ti ti-user me-2"></i>
                                Ihre Kontaktdaten
                            </h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           required
                                           placeholder="Ihr vollständiger Name">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-semibold">E-Mail <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}" 
                                           required
                                           placeholder="ihre@email.de">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold">Telefon</label>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}"
                                           placeholder="+49 123 456789">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Rental Details -->
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="ti ti-calendar me-2"></i>
                                Mietdetails
                            </h5>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="daterange" class="form-label fw-semibold">Mietdauer <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('date_from') is-invalid @enderror @error('date_to') is-invalid @enderror" 
                                           id="daterange" 
                                           placeholder="Klicken Sie hier, um Datum zu wählen"
                                           required
                                           readonly
                                           style="cursor: pointer;">
                                    
                                    <!-- Hidden fields to store the actual dates -->
                                    <input type="hidden" 
                                           id="date_from" 
                                           name="date_from" 
                                           value="{{ old('date_from', $dateFrom ?? '') }}">
                                    <input type="hidden" 
                                           id="date_to" 
                                           name="date_to" 
                                           value="{{ old('date_to', $dateTo ?? '') }}">
                                    
                                    @error('date_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @error('date_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="rental_type" class="form-label fw-semibold">Mietart <span class="text-danger">*</span></label>
                                    <select class="form-select @error('rental_type') is-invalid @enderror" 
                                            id="rental_type" 
                                            name="rental_type" 
                                            required>
                                        <option value="">Mietart wählen</option>
                                        @if($rental->price_range_hour > 0)
                                        <option value="hourly" {{ old('rental_type') == 'hourly' ? 'selected' : '' }}>
                                            Stundenweise ({{ $rental->price_range_hour }}€/h)
                                        </option>
                                        @endif
                                        @if($rental->price_range_day > 0)
                                        <option value="daily" {{ old('rental_type') == 'daily' ? 'selected' : '' }}>
                                            Täglich ({{ $rental->price_range_day }}€/Tag)
                                        </option>
                                        @endif
                                        @if($rental->price_range_once > 0)
                                        <option value="once" {{ old('rental_type') == 'once' ? 'selected' : '' }}>
                                            Pauschal ({{ $rental->price_range_once }}€)
                                        </option>
                                        @endif
                                    </select>
                                    @error('rental_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Estimated Price Display -->
                            <div id="estimated-price-container" class="estimated-price d-none">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Geschätzter Preis:</span>
                                    <span class="h5 mb-0 text-success" id="estimated-price">0€</span>
                                </div>
                                <small class="text-muted">*Unverbindliche Schätzung. Der finale Preis wird mit dem Vermieter vereinbart.</small>
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="mb-4">
                            <h5 class="mb-3">
                                <i class="ti ti-message me-2"></i>
                                Ihre Nachricht
                            </h5>

                            <div class="mb-3">
                                <label for="message" class="form-label fw-semibold">Nachricht <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('message') is-invalid @enderror" 
                                          id="message" 
                                          name="message" 
                                          rows="5" 
                                          required
                                          placeholder="Beschreiben Sie Ihren Bedarf, stellen Sie Fragen oder teilen Sie wichtige Details mit...">{{ old('message') }}</textarea>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Teilen Sie dem Vermieter mit, wofür Sie den Artikel benötigen und ob Sie spezielle Anforderungen haben.
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input @error('terms') is-invalid @enderror" 
                                       type="checkbox" 
                                       id="terms" 
                                       name="terms" 
                                       value="1" 
                                       {{ old('terms') ? 'checked' : '' }}
                                       required>
                                <label class="form-check-label" for="terms">
                                    Ich stimme den <a href="#" class="text-decoration-none">Nutzungsbedingungen</a> 
                                    und der <a href="#" class="text-decoration-none">Datenschutzerklärung</a> zu <span class="text-danger">*</span>
                                </label>
                                @error('terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('rentals.show', $rental->id) }}" class="btn btn-outline-secondary me-md-2">
                                <i class="ti ti-arrow-left me-1"></i>
                                Zurück
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-send me-1"></i>
                                Anfrage senden
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Rental Summary -->
            <div class="col-lg-4">
                <div class="rental-summary sticky-top" style="top: 2rem;">
                    <h6 class="mb-3">Anfrage für:</h6>
                    
                    <div class="rental-image mb-3">
                        <div class="text-center">
                            <i class="ti ti-package ti-xl mb-2"></i>
                            <p class="mb-0 small text-muted">Produktbild</p>
                        </div>
                    </div>

                    <h6 class="mb-2">{{ $rental->title }}</h6>
                    
                    @if($rental->category)
                        <span class="badge bg-label-primary mb-2">{{ $rental->category->name }}</span>
                    @endif

                    <div class="d-flex align-items-center text-muted mb-3">
                        <i class="ti ti-map-pin me-1"></i>
                        @if($rental->location)
                            <span>{{ $rental->location->city }}, {{ $rental->location->postcode }}</span>
                        @else
                            <span>Standort wird bei Buchung bekannt gegeben</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <h6 class="mb-2">Verfügbare Preise:</h6>
                        @if($rental->price_range_hour > 0)
                        <div class="d-flex justify-content-between mb-1">
                            <span>Stunde:</span>
                            <span class="price-badge">{{ $rental->price_range_hour }}€</span>
                        </div>
                        @endif
                        @if($rental->price_range_day > 0)
                        <div class="d-flex justify-content-between mb-1">
                            <span>Tag:</span>
                            <span class="price-badge">{{ $rental->price_range_day }}€</span>
                        </div>
                        @endif
                        @if($rental->price_range_once > 0)
                        <div class="d-flex justify-content-between mb-1">
                            <span>Pauschal:</span>
                            <span class="price-badge">{{ $rental->price_range_once }}€</span>
                        </div>
                        @endif
                    </div>

                    @if($rental->service_fee > 0)
                    <div class="alert alert-info small">
                        <i class="ti ti-info-circle me-1"></i>
                        <strong>Servicegebühr:</strong> {{ $rental->service_fee }}€
                    </div>
                    @endif

                    <hr>

                    <div class="text-center">
                        <h6 class="mb-2">Vermieter</h6>
                        <div class="d-flex align-items-center justify-content-center">
                            <div class="avatar avatar-sm me-2">
                                <img src="{{ asset('assets/img/avatars/1.png') }}" alt="{{ $rental->user->name }}" class="rounded-circle">
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $rental->user->name }}</h6>
                                <small class="text-muted">Seit {{ $rental->user->created_at->format('Y') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Date Range Picker Dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
$(document).ready(function() {
    console.log('DOM ready, initializing date range picker...');
    
    // Check if jQuery and required libraries are loaded
    if (typeof moment === 'undefined') {
        console.error('Moment.js is not loaded');
        return;
    }
    
    if (typeof $.fn.daterangepicker === 'undefined') {
        console.error('DateRangePicker is not loaded');
        return;
    }

    // Initialize date range picker
    try {
        $('#daterange').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Löschen',
                applyLabel: 'Übernehmen',
                fromLabel: 'Von',
                toLabel: 'Bis',
                customRangeLabel: 'Benutzerdefiniert',
                weekLabel: 'W',
                daysOfWeek: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
                monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni',
                    'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
                firstDay: 1,
                format: 'DD.MM.YYYY'
            },
            startDate: moment(),
            endDate: moment().add(7, 'days'),
            minDate: moment(),
            opens: 'left',
            drops: 'down',
            alwaysShowCalendars: true,
            showDropdowns: true,
            ranges: {
                '1 Tag': [moment(), moment()],
                '2 Tage': [moment(), moment().add(1, 'days')],
                '1 Woche': [moment(), moment().add(6, 'days')],
                '2 Wochen': [moment(), moment().add(13, 'days')],
                '1 Monat': [moment(), moment().add(29, 'days')]
            }
        });
        
        console.log('Date range picker initialized successfully');
    } catch (error) {
        console.error('Error initializing date range picker:', error);
    }

    // Handle date range selection
    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
        
        // Update hidden fields with ISO format for backend
        $('#date_from').val(picker.startDate.format('YYYY-MM-DD'));
        $('#date_to').val(picker.endDate.format('YYYY-MM-DD'));
        
        // Calculate estimated price
        calculateEstimatedPrice();
    });

    // Handle clear/cancel
    $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('#date_from').val('');
        $('#date_to').val('');
        $('#estimated-price-container').addClass('d-none');
    });

    // If there are old values, set them in the date range picker
    const oldDateFrom = $('#date_from').val();
    const oldDateTo = $('#date_to').val();
    
    if (oldDateFrom && oldDateTo) {
        const startDate = moment(oldDateFrom);
        const endDate = moment(oldDateTo);
        $('#daterange').val(startDate.format('DD.MM.YYYY') + ' - ' + endDate.format('DD.MM.YYYY'));
        calculateEstimatedPrice();
    }

    // Rental type change handler
    $('#rental_type').on('change', calculateEstimatedPrice);
});

// Calculate estimated price when form values change
function calculateEstimatedPrice() {
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;
    const rentalType = document.getElementById('rental_type').value;
    const container = document.getElementById('estimated-price-container');
    const priceElement = document.getElementById('estimated-price');

    if (!dateFrom || !dateTo || !rentalType) {
        container.classList.add('d-none');
        return;
    }

    const startDate = new Date(dateFrom);
    const endDate = new Date(dateTo);
    const timeDiff = endDate.getTime() - startDate.getTime();
    const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24)) + 1; // Include both start and end day

    if (daysDiff <= 0) {
        container.classList.add('d-none');
        return;
    }

    let estimatedPrice = 0;
    const hourlyRate = {{ $rental->price_range_hour ?? 0 }};
    const dailyRate = {{ $rental->price_range_day ?? 0 }};
    const onceRate = {{ $rental->price_range_once ?? 0 }};

    switch (rentalType) {
        case 'hourly':
            // Assume 8 hours per day for estimation
            estimatedPrice = daysDiff * 8 * hourlyRate;
            break;
        case 'daily':
            estimatedPrice = daysDiff * dailyRate;
            break;
        case 'once':
            estimatedPrice = onceRate;
            break;
    }

    if (estimatedPrice > 0) {
        priceElement.textContent = estimatedPrice.toFixed(2) + '€';
        container.classList.remove('d-none');
    } else {
        container.classList.add('d-none');
    }
}
</script>
