@extends('layouts/contentNavbarLayout')

@section('title', 'Standort bearbeiten')

@section('page-style')
    <style>
        .suggestions-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-height: 200px;
            overflow-y: auto;
            z-index: 1050;
        }

        .suggestion-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
        }

        .suggestion-item:hover {
            background-color: #f8f9fa;
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        .suggestion-item .suggestion-text {
            font-weight: 500;
            color: #333;
        }

        .suggestion-item .suggestion-details {
            font-size: 0.875rem;
            color: #666;
            margin-top: 2px;
        }

        .suggestion-item .suggestion-highlight {
            background-color: #fff3cd;
            padding: 1px 2px;
            border-radius: 2px;
        }
    </style>
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Validation for main location checkbox
            const isMainCheckbox = document.getElementById('is_main');
            const warningElement = document.getElementById('mainLocationWarning');

            if (isMainCheckbox && warningElement) {
                isMainCheckbox.addEventListener('change', function () {
                    if (this.checked) {
                        warningElement.classList.remove('d-none');
                    } else {
                        warningElement.classList.add('d-none');
                    }
                });
            }

            // Form validation before submission
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function (e) {
                    const requiredFields = ['street_address', 'postal_code', 'city', 'country'];
                    let hasErrors = false;

                    requiredFields.forEach(function (fieldName) {
                        const field = document.getElementById(fieldName);
                        if (field && !field.value.trim()) {
                            field.classList.add('is-invalid');
                            hasErrors = true;
                        } else if (field) {
                            field.classList.remove('is-invalid');
                        }
                    });

                    if (hasErrors) {
                        e.preventDefault();
                        alert('Bitte füllen Sie alle Pflichtfelder aus.');
                        return false;
                    }
                });
            }

            // Remove invalid class on input
            const requiredInputs = document.querySelectorAll('input[required], select[required]');
            requiredInputs.forEach(function (input) {
                input.addEventListener('input', function () {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });

                input.addEventListener('change', function () {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            });

            // Auto-dismiss alerts after 5 seconds
            setTimeout(function () {
                const alerts = document.querySelectorAll('.alert-dismissible');
                alerts.forEach(function (alert) {
                    const closeBtn = alert.querySelector('.btn-close');
                    if (closeBtn) {
                        closeBtn.click();
                    }
                });
            }, 5000);

            // Initialize location autocomplete functionality
            initializeLocationAutocomplete();
        });

        // Location autocomplete functionality
        function initializeLocationAutocomplete() {
            const countrySelect = document.getElementById('country');
            const postalCodeInput = document.getElementById('postal_code');
            const cityInput = document.getElementById('city');
            const postalCodeSuggestions = document.getElementById('postal_code_suggestions');
            const citySuggestions = document.getElementById('city_suggestions');

            let postalCodeTimeout;
            let cityTimeout;

            // Initialize postal code autocomplete
            if (postalCodeInput && postalCodeSuggestions) {
                postalCodeInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    const countryCode = countrySelect ? countrySelect.value : 'DE';

                    clearTimeout(postalCodeTimeout);

                    if (query.length < 2 || !countryCode) {
                        postalCodeSuggestions.style.display = 'none';
                        return;
                    }

                    postalCodeTimeout = setTimeout(() => {
                        fetchLocationSuggestions(query, countryCode, 'postal_code', postalCodeSuggestions, postalCodeInput);
                    }, 300);
                });

                postalCodeInput.addEventListener('focus', function() {
                    const query = this.value.trim();
                    const countryCode = countrySelect ? countrySelect.value : 'DE';
                    
                    if (query.length >= 2 && countryCode) {
                        fetchLocationSuggestions(query, countryCode, 'postal_code', postalCodeSuggestions, postalCodeInput);
                    }
                });
            }

            // Initialize city autocomplete
            if (cityInput && citySuggestions) {
                cityInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    const countryCode = countrySelect ? countrySelect.value : 'DE';

                    clearTimeout(cityTimeout);

                    if (query.length < 2 || !countryCode) {
                        citySuggestions.style.display = 'none';
                        return;
                    }

                    cityTimeout = setTimeout(() => {
                        fetchLocationSuggestions(query, countryCode, 'city', citySuggestions, cityInput);
                    }, 300);
                });

                cityInput.addEventListener('focus', function() {
                    const query = this.value.trim();
                    const countryCode = countrySelect ? countrySelect.value : 'DE';
                    
                    if (query.length >= 2 && countryCode) {
                        fetchLocationSuggestions(query, countryCode, 'city', citySuggestions, cityInput);
                    }
                });
            }

            // Country change handler
            if (countrySelect) {
                countrySelect.addEventListener('change', function() {
                    // Clear suggestions when country changes
                    if (postalCodeSuggestions) postalCodeSuggestions.style.display = 'none';
                    if (citySuggestions) citySuggestions.style.display = 'none';
                    
                    // Clear inputs if they don't match the new country
                    const selectedCountry = this.value;
                    if (postalCodeInput && postalCodeInput.value) {
                        // Optionally clear postal code when country changes
                        // postalCodeInput.value = '';
                    }
                    if (cityInput && cityInput.value) {
                        // Optionally clear city when country changes
                        // cityInput.value = '';
                    }
                });
            }

            // Close suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!postalCodeInput?.contains(e.target) && !postalCodeSuggestions?.contains(e.target)) {
                    if (postalCodeSuggestions) postalCodeSuggestions.style.display = 'none';
                }
                if (!cityInput?.contains(e.target) && !citySuggestions?.contains(e.target)) {
                    if (citySuggestions) citySuggestions.style.display = 'none';
                }
            });
        }

        // Fetch location suggestions from API
        async function fetchLocationSuggestions(query, countryCode, type, suggestionsContainer, inputElement) {
            try {
                const response = await fetch(`/api/postal-codes/location-suggestions?query=${encodeURIComponent(query)}&country=${encodeURIComponent(countryCode)}&type=${type}&limit=10`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();

                if (data.suggestions && data.suggestions.length > 0) {
                    displaySuggestions(data.suggestions, query, suggestionsContainer, inputElement, type);
                } else {
                    suggestionsContainer.style.display = 'none';
                }
            } catch (error) {
                console.error('Error fetching location suggestions:', error);
                suggestionsContainer.style.display = 'none';
            }
        }

        // Display suggestions in dropdown
        function displaySuggestions(suggestions, query, container, inputElement, type) {
            container.innerHTML = '';

            suggestions.forEach(suggestion => {
                const item = document.createElement('div');
                item.className = 'suggestion-item';
                
                let displayText, detailsText;
                
                if (type === 'postal_code') {
                    displayText = suggestion.postal_code;
                    detailsText = `${suggestion.city}${suggestion.region ? ', ' + suggestion.region : ''}`;
                } else {
                    displayText = suggestion.city;
                    detailsText = `${suggestion.postal_code}${suggestion.region ? ', ' + suggestion.region : ''}`;
                }

                // Highlight the query in the display text
                const highlightedText = displayText.replace(
                    new RegExp(`(${query})`, 'gi'),
                    '<span class="suggestion-highlight">$1</span>'
                );

                item.innerHTML = `
                    <div class="suggestion-text">${highlightedText}</div>
                    <div class="suggestion-details">${detailsText}</div>
                `;

                item.addEventListener('click', function() {
                    if (type === 'postal_code') {
                        inputElement.value = suggestion.postal_code;
                        // Auto-fill city if available
                        const cityInput = document.getElementById('city');
                        if (cityInput && suggestion.city) {
                            cityInput.value = suggestion.city;
                        }
                    } else {
                        inputElement.value = suggestion.city;
                        // Auto-fill postal code if available
                        const postalCodeInput = document.getElementById('postal_code');
                        if (postalCodeInput && suggestion.postal_code) {
                            postalCodeInput.value = suggestion.postal_code;
                        }
                    }
                    
                    container.style.display = 'none';
                    
                    // Trigger input event to update form validation
                    const event = new Event('input', { bubbles: true });
                    inputElement.dispatchEvent(event);
                });

                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8f9fa';
                });

                item.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });

                container.appendChild(item);
            });

            container.style.display = 'block';
        }
    </script>
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Vendor / <a href="{{ route('vendor-locations') }}">Standorte</a> /</span> Standort
        bearbeiten
    </h4>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Standort #{{ $location->id ?? 'Neu' }} bearbeiten</h5>
                </div>
                <div class="card-body">
                    @if($location && $location->id)
                        <form method="POST" action="{{ route('vendor-location-update', ['id' => $location->id]) }}">
                            @csrf
                            @method('PUT')
                    @else
                            <form method="POST" action="{{ route('vendor-location-save') }}">
                                @csrf
                        @endif

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="street_address">Straße und Hausnummer *</label>
                                        <input type="text" class="form-control" id="street_address" name="street_address"
                                            value="{{ $location->street_address ?? '' }}" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="additional_address">Adresszusatz</label>
                                        <input type="text" class="form-control" id="additional_address"
                                            name="additional_address" value="{{ $location->additional_address ?? '' }}" />
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="country">Land *</label>
                                        <select class="form-select" id="country" name="country" required>
                                            <option value="">Land auswählen</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->code }}" 
                                                    {{ ($location->country ?? '') == $country->code ? 'selected' : '' }}
                                                    data-country-id="{{ $country->id }}">
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="postal_code">Postleitzahl *</label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control" id="postal_code" name="postal_code"
                                                value="{{ $location->postal_code ?? '' }}" required 
                                                placeholder="PLZ eingeben..." autocomplete="off" />
                                            <div id="postal_code_suggestions" class="suggestions-dropdown" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="city">Stadt *</label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control" id="city" name="city"
                                                value="{{ $location->city ?? '' }}" required 
                                                placeholder="Stadt eingeben..." autocomplete="off" />
                                            <div id="city_suggestions" class="suggestions-dropdown" style="display: none;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="location_name">Standortname</label>
                                        <input type="text" class="form-control" id="location_name" name="location_name"
                                            value="{{ $location->name ?? '' }}" />
                                        <small class="text-muted">Ein optionaler Name für diesen Standort zur besseren
                                            Unterscheidung.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="contact_phone">Telefonnummer</label>
                                        <input type="tel" class="form-control" id="contact_phone" name="contact_phone"
                                            value="{{ $location->phone ?? '' }}" />
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="location_description">Beschreibung des
                                            Standorts</label>
                                        <textarea class="form-control" id="location_description" name="location_description"
                                            rows="3">{{ $location->description ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-2">
                                        <input type="hidden" name="is_main" value="0">
                                        <input class="form-check-input" type="checkbox" id="is_main" name="is_main"
                                            value="1" {{ ($location->is_main ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_main">Als Hauptstandort festlegen</label>
                                    </div>
                                    <div class="alert alert-warning d-flex align-items-center {{ ($location->is_main ?? false) ? '' : 'd-none' }}"
                                        id="mainLocationWarning">
                                        <i class="ti ti-alert-circle me-2"></i>
                                        <div>Wenn Sie diesen Standort als Hauptstandort festlegen, wird der bisherige
                                            Hauptstandort zu einem normalen Standort.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch mb-2">
                                        <input type="hidden" name="is_active" value="0">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                            value="1" {{ ($location->is_active ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Standort ist aktiv</label>
                                    </div>
                                    <small class="text-muted d-block">Inaktive Standorte werden in der Vermietung nicht
                                        angezeigt.</small>
                                </div>
                            </div>

                            <div class="divider divider-dashed">
                                <div class="divider-text">Koordinaten</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="latitude">Breitengrad (Latitude)</label>
                                        <input type="text" class="form-control" id="latitude" name="latitude"
                                            value="{{ $location->latitude ?? '' }}" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="longitude">Längengrad (Longitude)</label>
                                        <input type="text" class="form-control" id="longitude" name="longitude"
                                            value="{{ $location->longitude ?? '' }}" />
                                    </div>
                                </div>
                            </div>

                           <!-- <div class="row mb-4">
                                <div class="col-12">
                                    <div class="border rounded p-3"
                                        style="height: 250px; background-color: #e9ecef; display: flex; align-items: center; justify-content: center;">
                                        <span class="text-muted">Karte würde hier angezeigt werden</span>
                                    </div>
                                    <small class="text-muted d-block mt-2">Sie können den Standort auf der Karte
                                        verschieben, um
                                        die genauen Koordinaten zu setzen.</small>
                                </div>
                            </div> -->

                            <div class="row justify-content-end">
                                <div class="col-sm-12">
                                    <a href="{{ route('vendor-locations') }}"
                                        class="btn btn-label-secondary me-2">Abbrechen</a>
                                    <button type="submit" class="btn btn-primary">Änderungen speichern</button>
                                </div>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Öffnungszeiten-Karte -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Öffnungszeiten</h5>
                    <a href="{{ route('vendor-openings-index', ['locationId' => $location->id ?? '']) }}"
                        class="btn btn-primary btn-sm">
                        <i class="ti ti-edit me-1"></i> Öffnungszeiten verwalten
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Wochentag</th>
                                    <th>Status</th>
                                    <th>Öffnungszeit</th>
                                    <th>Schließzeit</th>
                                    <th>Mittagspause</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $days = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'];
                                    $defaultOpeningTimes = ['08:00', '08:00', '08:00', '08:00', '08:00', '10:00', null];
                                    $defaultClosingTimes = ['18:00', '18:00', '18:00', '18:00', '16:00', '14:00', null];
                                    $defaultBreakTimes = ['12:00-13:00', '12:00-13:00', '12:00-13:00', '12:00-13:00', '12:00-13:00', null, null];
                                @endphp

                                @foreach($days as $index => $day)
                                    @php
                                        $dayNumber = $index + 1; // Convert to 1-7 (Monday-Sunday)
                                        $opening = isset($openingHours[$dayNumber]) ? (object) $openingHours[$dayNumber] : null;

                                        // Use real data if available, otherwise fall back to defaults
                                        $isOpen = $opening ? !$opening->is_closed : ($defaultOpeningTimes[$index] !== null);
                                        $openTime = $opening ? $opening->open_time : $defaultOpeningTimes[$index];
                                        $closeTime = $opening ? $opening->close_time : $defaultClosingTimes[$index];
                                        $breakTime = $opening && $opening->break_start && $opening->break_end
                                            ? $opening->break_start . '-' . $opening->break_end
                                            : $defaultBreakTimes[$index];
                                    @endphp
                                    <tr>
                                        <td>{{ $day }}</td>
                                        <td>
                                            @if($isOpen)
                                                <span class="badge bg-label-success">Geöffnet</span>
                                            @else
                                                <span class="badge bg-label-danger">Geschlossen</span>
                                            @endif
                                        </td>
                                        <td>{{ $openTime ?? '-' }}</td>
                                        <td>{{ $closeTime ?? '-' }}</td>
                                        <td>{{ $breakTime ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection