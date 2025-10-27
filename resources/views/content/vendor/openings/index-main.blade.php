@extends('layouts/contentNavbarLayout')

@section('title', 'Öffnungszeiten verwalten')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/flatpickr/flatpickr.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header Section -->
  <div class="card mb-4" style="background: linear-gradient(135deg, #7dd3fc 0%, #67e8f9 100%); color: white;">
    <div class="card-body">
      <h3 class="mb-3 text-white">Öffnungszeiten</h3>
      <p class="mb-0 text-white opacity-90">
        Um Mietern mitzuteilen, wann du an deinen verschiedenen Standorten erreichbar bist, 
        kannst du hier verschiedene Öffnungszeiten angeben. Lege zunächst deine Standard-Öffnungszeiten fest.
      </p>
    </div>
  </div>

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

  <!-- Standard Opening Hours Section -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title mb-3">Deine Standard-Öffnungszeiten</h4>
          <p class="text-muted mb-4">
            Deine Standard-Öffnungszeiten werden dann angezeigt, wenn du für einen Standort keine eigenen 
            Öffnungszeiten festgelegt hast oder wenn jemand auf Inlando nach Angeboten ohne Standortangabe sucht.
          </p>
          
          <!-- Show current default hours summary -->
          @if($defaultOpenings->count() > 0)
            <div class="row mb-3">
              @php
                $days = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'];
              @endphp
              @foreach($days as $index => $dayName)
                @php
                  $dayNumber = $index + 1;
                  $opening = $defaultOpenings->get($dayNumber);
                @endphp
                <div class="col-lg-3 col-md-4 col-6 mb-2">
                  <div class="d-flex align-items-center">
                    <strong class="me-2">{{ $dayName }}:</strong>
                    @if($opening && !$opening->is_closed)
                      <span class="badge bg-label-success">{{ $opening->open_time }} - {{ $opening->close_time }}</span>
                    @else
                      <span class="badge bg-label-danger">Geschlossen</span>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="alert alert-info">
              <i class="ti ti-info-circle me-2"></i>
              Noch keine Standard-Öffnungszeiten festgelegt.
            </div>
          @endif
          
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#defaultHoursModal">
            BEARBEITEN
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Locations Opening Hours Section -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title mb-0">Öffnungszeiten deiner Standorte</h4>
        </div>
        <div class="card-body">
          @if($locationsWithStatus->count() > 0)
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>LAND</th>
                    <th>PLZ</th>
                    <th>ORT</th>
                    <th>STRASSE, NR.</th>
                    <th>VERWENDET</th>
                    <th>AKTIONEN</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($locationsWithStatus as $location)
                    <tr>
                      <td>
                        @if($location->country && is_object($location->country) && isset($location->country->code))
                          <img src="{{ asset('assets/vendor/fonts/flags/1x1/' . strtolower($location->country->code) . '.svg') }}" 
                               alt="{{ $location->country->name }}" class="me-2" style="width: 20px; height: 15px;">
                          {{ $location->country->name }}
                        @else
                          @php
                            $countryCode = $location->country ?? $location->country_code ?? 'DE';
                            // Handle if country is still a string
                            if (is_string($countryCode) && strlen($countryCode) > 2) {
                              $countryCode = 'DE'; // fallback
                            }
                            $countryName = match($countryCode) {
                              'DE' => 'Deutschland',
                              'AT' => 'Österreich', 
                              'CH' => 'Schweiz',
                              'FR' => 'Frankreich',
                              'IT' => 'Italien',
                              default => ucfirst(strtolower($countryCode))
                            };
                          @endphp
                          <img src="{{ asset('assets/vendor/fonts/flags/1x1/' . strtolower($countryCode) . '.svg') }}" 
                               alt="{{ $countryName }}" class="me-2" style="width: 20px; height: 15px;">
                          {{ $countryName }}
                        @endif
                      </td>
                      <td>{{ $location->postal_code }}</td>
                      <td>{{ $location->city }}</td>
                      <td>{{ $location->street_address }}</td>
                      <td>
                        @if($location->opening_status === 'custom')
                          <span class="badge bg-label-success">Eigene</span>
                        @else
                          <span class="badge bg-label-info">Standard</span>
                        @endif
                      </td>
                      <td>
                        <a href="{{ route('vendor-openings-edit', ['locationId' => $location->id]) }}" 
                           class="btn btn-sm btn-info me-1">edit</a>
                        @if($location->opening_status === 'custom')
                          <form method="POST" action="{{ route('vendor-openings-remove', ['locationId' => $location->id]) }}" 
                                style="display: inline;" onsubmit="return confirm('Möchten Sie die individuellen Öffnungszeiten für diesen Standort wirklich entfernen?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">remove</button>
                          </form>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-4">
              <i class="ti ti-map-pins display-4 text-muted mb-3"></i>
              <h5 class="text-muted">Keine Standorte vorhanden</h5>
              <p class="text-muted">Erstellen Sie zuerst einen Standort, um Öffnungszeiten zu verwalten.</p>
              <a href="{{ route('vendor-locations') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i>Standort hinzufügen
              </a>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Default Hours Modal -->
<div class="modal fade" id="defaultHoursModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Standard-Öffnungszeiten bearbeiten</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="defaultHoursForm">
        @csrf
        <div class="modal-body">
          @php
            $days = [
              1 => 'Montag', 2 => 'Dienstag', 3 => 'Mittwoch', 4 => 'Donnerstag',
              5 => 'Freitag', 6 => 'Samstag', 7 => 'Sonntag'
            ];
          @endphp
          
          @foreach($days as $dayId => $dayName)
            @php
              $existing = $defaultOpenings->get($dayId);
              $isOpen = $existing && !$existing->is_closed;
              $openTime = $existing ? $existing->open_time : '';
              $closeTime = $existing ? $existing->close_time : '';
              $hasBreak = $existing && $existing->break_start && $existing->break_end;
              $breakStart = $existing ? $existing->break_start : '';
              $breakEnd = $existing ? $existing->break_end : '';
            @endphp
            
            <div class="card shadow-none bg-light border mb-3">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-check form-switch">
                      <input type="hidden" name="days[{{ $dayId }}][is_open]" value="0">
                      <input class="form-check-input toggle-opening" type="checkbox" 
                             id="default-day-{{ $dayId }}-open" 
                             name="days[{{ $dayId }}][is_open]" value="1" 
                             data-day="{{ $dayId }}" {{ $isOpen ? 'checked' : '' }}>
                      <label class="form-check-label fw-semibold" for="default-day-{{ $dayId }}-open">
                        {{ $dayName }}
                      </label>
                    </div>
                  </div>
                  
                  <div class="col-md-8 time-inputs-{{ $dayId }} {{ $isOpen ? '' : 'd-none' }}">
                    <div class="row">
                      <div class="col-md-5">
                        <div class="input-group">
                          <span class="input-group-text">Von</span>
                          <input type="text" class="form-control time-picker" 
                                 id="default-day-{{ $dayId }}-open-time" 
                                 name="days[{{ $dayId }}][open_time]" 
                                 value="{{ $openTime }}">
                        </div>
                      </div>
                      
                      <div class="col-md-5">
                        <div class="input-group">
                          <span class="input-group-text">Bis</span>
                          <input type="text" class="form-control time-picker" 
                                 id="default-day-{{ $dayId }}-close-time" 
                                 name="days[{{ $dayId }}][close_time]" 
                                 value="{{ $closeTime }}">
                        </div>
                      </div>
                      
                      <div class="col-md-2">
                        <div class="form-check form-switch">
                          <input type="hidden" name="days[{{ $dayId }}][has_break]" value="0">
                          <input class="form-check-input toggle-break" type="checkbox" 
                                 id="default-day-{{ $dayId }}-has-break" 
                                 name="days[{{ $dayId }}][has_break]" value="1"
                                 data-day="{{ $dayId }}" {{ $hasBreak ? 'checked' : '' }}>
                          <label class="form-check-label" for="default-day-{{ $dayId }}-has-break">Pause</label>
                        </div>
                      </div>
                    </div>
                    
                    <div class="row mt-2 break-inputs-{{ $dayId }} {{ $hasBreak && $isOpen ? '' : 'd-none' }}">
                      <div class="col-md-5">
                        <div class="input-group">
                          <span class="input-group-text">Pause von</span>
                          <input type="text" class="form-control time-picker" 
                                 id="default-day-{{ $dayId }}-break-start" 
                                 name="days[{{ $dayId }}][break_start]" 
                                 value="{{ $breakStart }}">
                        </div>
                      </div>
                      
                      <div class="col-md-5">
                        <div class="input-group">
                          <span class="input-group-text">Pause bis</span>
                          <input type="text" class="form-control time-picker" 
                                 id="default-day-{{ $dayId }}-break-end" 
                                 name="days[{{ $dayId }}][break_end]" 
                                 value="{{ $breakEnd }}">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Abbrechen</button>
          <button type="submit" class="btn btn-primary">Speichern</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Initialize Flatpickr for modal time inputs
  const timePickerElements = document.querySelectorAll('.time-picker');
  if (timePickerElements.length > 0 && typeof flatpickr !== 'undefined') {
    timePickerElements.forEach(function(element) {
      flatpickr(element, {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
      });
    });
  }
  
  // Modal form toggles
  const toggleOpeningElements = document.querySelectorAll('.toggle-opening');
  toggleOpeningElements.forEach(function(element) {
    element.addEventListener('change', function() {
      const dayId = this.getAttribute('data-day');
      const timeInputs = document.querySelector(`.time-inputs-${dayId}`);
      if (timeInputs) {
        if (this.checked) {
          timeInputs.classList.remove('d-none');
        } else {
          timeInputs.classList.add('d-none');
        }
      }
    });
  });
  
  const toggleBreakElements = document.querySelectorAll('.toggle-break');
  toggleBreakElements.forEach(function(element) {
    element.addEventListener('change', function() {
      const dayId = this.getAttribute('data-day');
      const breakInputs = document.querySelector(`.break-inputs-${dayId}`);
      if (breakInputs) {
        if (this.checked) {
          breakInputs.classList.remove('d-none');
        } else {
          breakInputs.classList.add('d-none');
        }
      }
    });
  });
  
  // Handle modal form submission
  const defaultHoursForm = document.getElementById('defaultHoursForm');
  if (defaultHoursForm) {
    defaultHoursForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      fetch('{{ route("vendor-openings-update-defaults") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Close modal and reload page to show updated data
          const modal = bootstrap.Modal.getInstance(document.getElementById('defaultHoursModal'));
          modal.hide();
          location.reload();
        } else {
          alert('Fehler: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
      });
    });
  }

  // Auto-dismiss alerts after 5 seconds
  setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
      const closeBtn = alert.querySelector('.btn-close');
      if (closeBtn) {
        closeBtn.click();
      }
    });
  }, 5000);
});
</script>
@endsection
