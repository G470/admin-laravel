@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Buchung erstellen')

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Neue Buchung erstellen</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('bookings.store') }}">
                        @csrf
                        
                        <input type="hidden" name="rental_id" value="{{ $rental->id }}">
                        
                        <!-- Rental Information Display -->
                        <div class="mb-4">
                            <h6>Artikel:</h6>
                            <div class="d-flex align-items-center">
                                @if($rental->main_image)
                                    <img src="{{ $rental->main_image }}" alt="{{ $rental->title }}" 
                                         class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                @endif
                                <div>
                                    <h6 class="mb-1">{{ $rental->title }}</h6>
                                    <small class="text-muted">{{ $rental->category->name ?? 'Keine Kategorie' }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Guest Information (if not logged in) -->
                        @guest
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="guest_name" class="form-label">Name *</label>
                                <input type="text" class="form-control @error('guest_name') is-invalid @enderror" 
                                       id="guest_name" name="guest_name" value="{{ old('guest_name') }}" required>
                                @error('guest_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="guest_email" class="form-label">E-Mail *</label>
                                <input type="email" class="form-control @error('guest_email') is-invalid @enderror" 
                                       id="guest_email" name="guest_email" value="{{ old('guest_email') }}" required>
                                @error('guest_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="guest_phone" class="form-label">Telefon</label>
                                <input type="tel" class="form-control @error('guest_phone') is-invalid @enderror" 
                                       id="guest_phone" name="guest_phone" value="{{ old('guest_phone') }}">
                                @error('guest_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endguest

                        <!-- Booking Details -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Startdatum *</label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" value="{{ old('start_date') }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Enddatum *</label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" value="{{ old('end_date') }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="rental_type" class="form-label">Mietart *</label>
                            <select class="form-select @error('rental_type') is-invalid @enderror" 
                                    id="rental_type" name="rental_type" required>
                                <option value="">Mietart wählen</option>
                                @if($rental->price_range_hour > 0)
                                    <option value="hourly" {{ old('rental_type') == 'hourly' ? 'selected' : '' }}>
                                        Stundenweise (€{{ $rental->price_range_hour }}/h)
                                    </option>
                                @endif
                                @if($rental->price_range_day > 0)
                                    <option value="daily" {{ old('rental_type') == 'daily' ? 'selected' : '' }}>
                                        Täglich (€{{ $rental->price_range_day }}/Tag)
                                    </option>
                                @endif
                                @if($rental->price_range_once > 0)
                                    <option value="once" {{ old('rental_type') == 'once' ? 'selected' : '' }}>
                                        Pauschal (€{{ $rental->price_range_once }})
                                    </option>
                                @endif
                            </select>
                            @error('rental_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Nachricht (optional)</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" name="message" rows="3" 
                                      placeholder="Beschreiben Sie Ihren Bedarf oder stellen Sie Fragen...">{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Price Estimation -->
                        <div id="price-estimate" class="alert alert-info d-none">
                            <strong>Geschätzter Preis: </strong>
                            <span id="estimated-price">€0,00</span>
                            <small class="d-block mt-1">*Unverbindliche Schätzung basierend auf den gewählten Daten</small>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('rentals.show', $rental->id) }}" class="btn btn-outline-secondary">
                                <i class="ti ti-arrow-left me-1"></i>
                                Zurück
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-send me-1"></i>
                                Buchung erstellen
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const rentalTypeSelect = document.getElementById('rental_type');
    const priceEstimate = document.getElementById('price-estimate');
    const estimatedPriceSpan = document.getElementById('estimated-price');

    // Update end date minimum when start date changes
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
        if (endDateInput.value && endDateInput.value < this.value) {
            endDateInput.value = this.value;
        }
        calculatePrice();
    });

    // Calculate price when any input changes
    [startDateInput, endDateInput, rentalTypeSelect].forEach(input => {
        input.addEventListener('change', calculatePrice);
    });

    function calculatePrice() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        const rentalType = rentalTypeSelect.value;

        if (!startDate || !endDate || !rentalType || startDate > endDate) {
            priceEstimate.classList.add('d-none');
            return;
        }

        const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1; // Include both start and end day
        let price = 0;

        const rates = {
            hourly: {{ $rental->price_range_hour ?? 0 }},
            daily: {{ $rental->price_range_day ?? 0 }},
            once: {{ $rental->price_range_once ?? 0 }}
        };

        switch(rentalType) {
            case 'hourly':
                price = days * 8 * rates.hourly; // Assume 8 hours per day
                break;
            case 'daily':
                price = days * rates.daily;
                break;
            case 'once':
                price = rates.once;
                break;
        }

        if (price > 0) {
            estimatedPriceSpan.textContent = '€' + price.toFixed(2).replace('.', ',');
            priceEstimate.classList.remove('d-none');
        } else {
            priceEstimate.classList.add('d-none');
        }
    }
});
</script>
@endsection
