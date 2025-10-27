@extends('layouts/contentNavbarLayout')

@section('title', 'Öffnungszeiten verwalten')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/flatpickr/flatpickr.js'])
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize Flatpickr for time inputs
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
    
    // Handle opening/closing toggle
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
    
    // Handle break toggle
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
    
    // Copy Monday to weekdays
    const copyToWeekdaysBtn = document.getElementById('copyToWeekdays');
    if (copyToWeekdaysBtn) {
      copyToWeekdaysBtn.addEventListener('click', function() {
        const mondayOpen = document.getElementById('day-1-open').checked;
        const mondayOpenTime = document.getElementById('day-1-open-time').value;
        const mondayCloseTime = document.getElementById('day-1-close-time').value;
        const mondayHasBreak = document.getElementById('day-1-has-break').checked;
        const mondayBreakStart = document.getElementById('day-1-break-start').value;
        const mondayBreakEnd = document.getElementById('day-1-break-end').value;
        
        // Apply to Tuesday through Friday (days 2-5)
        for (let i = 2; i <= 5; i++) {
          const dayOpen = document.getElementById(`day-${i}-open`);
          const dayOpenTime = document.getElementById(`day-${i}-open-time`);
          const dayCloseTime = document.getElementById(`day-${i}-close-time`);
          const dayHasBreak = document.getElementById(`day-${i}-has-break`);
          const dayBreakStart = document.getElementById(`day-${i}-break-start`);
          const dayBreakEnd = document.getElementById(`day-${i}-break-end`);
          
          if (dayOpen) {
            dayOpen.checked = mondayOpen;
            dayOpen.dispatchEvent(new Event('change'));
          }
          if (dayOpenTime) dayOpenTime.value = mondayOpenTime;
          if (dayCloseTime) dayCloseTime.value = mondayCloseTime;
          if (dayHasBreak) {
            dayHasBreak.checked = mondayHasBreak;
            dayHasBreak.dispatchEvent(new Event('change'));
          }
          if (dayBreakStart) dayBreakStart.value = mondayBreakStart;
          if (dayBreakEnd) dayBreakEnd.value = mondayBreakEnd;
        }
      });
    }
  });
</script>
@endsection

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Vendor / <a href="{{ route('vendor-locations') }}">Standorte</a> /</span> Öffnungszeiten verwalten
</h4>

<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Öffnungszeiten für Standort: {{ $location ? $location->name : 'Bitte wählen Sie einen Standort aus' }}</h5>
        @if($location)
        <button id="copyToWeekdays" type="button" class="btn btn-outline-primary btn-sm">
          <i class="ti ti-copy me-1"></i>Montag auf Werktage kopieren
        </button>
        @endif
      </div>
      <div class="card-body">
        @if(!$location)
        <div class="alert alert-warning">
          <i class="ti ti-alert-triangle me-2"></i>
          Bitte wählen Sie einen Standort aus, um dessen Öffnungszeiten zu verwalten.
        </div>
        @else
        <p class="card-text text-muted mb-4">
          Hier legen Sie fest, wann Ihr Standort für Kunden geöffnet ist. Diese Zeiten werden für die Buchung Ihrer Vermietungsobjekte verwendet.
        </p>
        
        <form method="POST" action="{{ route('vendor-openings-update', ['locationId' => $location->id]) }}">
          @csrf
          @method('PUT')
          
          @php
            $days = [
              1 => 'Montag',
              2 => 'Dienstag',
              3 => 'Mittwoch',
              4 => 'Donnerstag',
              5 => 'Freitag',
              6 => 'Samstag',
              7 => 'Sonntag'
            ];
          @endphp
          
          <!-- Öffnungszeiten für jeden Tag der Woche -->
          @foreach($days as $dayId => $dayName)
            @php
              $existing = isset($existingOpenings[$dayId]) ? $existingOpenings[$dayId] : null;
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
                      <input class="form-check-input toggle-opening" type="checkbox" id="day-{{ $dayId }}-open" 
                             name="days[{{ $dayId }}][is_open]" value="1" data-day="{{ $dayId }}" 
                             {{ $isOpen ? 'checked' : '' }}>
                      <label class="form-check-label fw-semibold" for="day-{{ $dayId }}-open">{{ $dayName }}</label>
                    </div>
                  </div>
                  
                  <div class="col-md-8 time-inputs-{{ $dayId }} {{ $isOpen ? '' : 'd-none' }}">
                    <div class="row">
                      <div class="col-md-5">
                        <div class="mb-0">
                          <div class="input-group">
                            <span class="input-group-text">Von</span>
                            <input type="text" class="form-control time-picker" 
                                   id="day-{{ $dayId }}-open-time" 
                                   name="days[{{ $dayId }}][open_time]" 
                                   value="{{ $openTime }}">
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-5">
                        <div class="mb-0">
                          <div class="input-group">
                            <span class="input-group-text">Bis</span>
                            <input type="text" class="form-control time-picker" 
                                   id="day-{{ $dayId }}-close-time" 
                                   name="days[{{ $dayId }}][close_time]" 
                                   value="{{ $closeTime }}">
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-2">
                        <div class="form-check form-switch">
                          <input type="hidden" name="days[{{ $dayId }}][has_break]" value="0">
                          <input class="form-check-input toggle-break" type="checkbox" 
                                 id="day-{{ $dayId }}-has-break" 
                                 name="days[{{ $dayId }}][has_break]" value="1"
                                 data-day="{{ $dayId }}" 
                                 {{ $hasBreak ? 'checked' : '' }}>
                          <label class="form-check-label" for="day-{{ $dayId }}-has-break">Pause</label>
                        </div>
                      </div>
                    </div>
                    
                    <div class="row mt-2 break-inputs-{{ $dayId }} {{ $hasBreak && $isOpen ? '' : 'd-none' }}">
                      <div class="col-md-5">
                        <div class="mb-0">
                          <div class="input-group">
                            <span class="input-group-text">Pause von</span>
                            <input type="text" class="form-control time-picker" 
                                   id="day-{{ $dayId }}-break-start" 
                                   name="days[{{ $dayId }}][break_start]" 
                                   value="{{ $breakStart }}">
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-5">
                        <div class="mb-0">
                          <div class="input-group">
                            <span class="input-group-text">Pause bis</span>
                            <input type="text" class="form-control time-picker" 
                                   id="day-{{ $dayId }}-break-end" 
                                   name="days[{{ $dayId }}][break_end]" 
                                   value="{{ $breakEnd }}">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
          
          <div class="divider divider-dashed my-4">
            <div class="divider-text">Ausnahmen</div>
          </div>
          
          <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="ti ti-info-circle me-2"></i>
            <div>
              Hier können Sie besondere Schließtage oder geänderte Öffnungszeiten für bestimmte Daten festlegen (z.B. Feiertage oder Betriebsferien).
            </div>
          </div>
          
          <div class="table-responsive mb-3">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Datum</th>
                  <th>Status</th>
                  <th>Von</th>
                  <th>Bis</th>
                  <th>Aktionen</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>01.01.{{ date('Y') }}</td>
                  <td><span class="badge bg-label-danger">Geschlossen</span></td>
                  <td>-</td>
                  <td>-</td>
                  <td>
                    <button type="button" class="btn btn-icon btn-sm btn-outline-danger">
                      <i class="ti ti-trash"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>24.12.{{ date('Y') }}</td>
                  <td><span class="badge bg-label-warning">Geänderte Zeiten</span></td>
                  <td>09:00</td>
                  <td>14:00</td>
                  <td>
                    <button type="button" class="btn btn-icon btn-sm btn-outline-danger">
                      <i class="ti ti-trash"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td>25.12.{{ date('Y') }}</td>
                  <td><span class="badge bg-label-danger">Geschlossen</span></td>
                  <td>-</td>
                  <td>-</td>
                  <td>
                    <button type="button" class="btn btn-icon btn-sm btn-outline-danger">
                      <i class="ti ti-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <button type="button" class="btn btn-outline-primary mb-4">
            <i class="ti ti-plus me-1"></i>Sonderöffnungszeit hinzufügen
          </button>
          
          <div class="row justify-content-end mt-3">
            <div class="col-sm-12">
              <a href="{{ route('vendor-location-edit', ['id' => $location->id]) }}" class="btn btn-label-secondary me-2">Zurück zum Standort</a>
              <button type="submit" class="btn btn-primary">Änderungen speichern</button>
            </div>
          </div>
        </form>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection 