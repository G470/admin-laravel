@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Öffnungszeiten bearbeiten')

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Öffnungszeiten bearbeiten</h5>
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

                    <form method="POST" action="{{ route('admin.openings.update', $opening->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="location_id" class="form-label">Standort *</label>
                            <select class="form-select @error('location_id') is-invalid @enderror" 
                                    id="location_id" name="location_id" required>
                                <option value="">Standort wählen</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" 
                                            {{ (old('location_id', $opening->location_id) == $location->id) ? 'selected' : '' }}>
                                        {{ $location->name }} - {{ $location->user->name ?? 'Unbekannt' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="day_of_week" class="form-label">Wochentag *</label>
                            <select class="form-select @error('day_of_week') is-invalid @enderror" 
                                    id="day_of_week" name="day_of_week" required>
                                <option value="">Wochentag wählen</option>
                                @foreach(App\Models\Opening::$daysOfWeek as $value => $label)
                                    <option value="{{ $value }}" {{ (old('day_of_week', $opening->day_of_week) == $value) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('day_of_week')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_closed" name="is_closed" value="1"
                                       {{ old('is_closed', $opening->is_closed) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_closed">
                                    An diesem Tag geschlossen
                                </label>
                            </div>
                        </div>

                        <div id="time-fields" style="{{ old('is_closed', $opening->is_closed) ? 'display: none;' : '' }}">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="open_time" class="form-label">Öffnungszeit *</label>
                                    <input type="time" class="form-control @error('open_time') is-invalid @enderror" 
                                           id="open_time" name="open_time" 
                                           value="{{ old('open_time', $opening->open_time ? \Carbon\Carbon::parse($opening->open_time)->format('H:i') : '') }}">
                                    @error('open_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="close_time" class="form-label">Schließzeit *</label>
                                    <input type="time" class="form-control @error('close_time') is-invalid @enderror" 
                                           id="close_time" name="close_time" 
                                           value="{{ old('close_time', $opening->close_time ? \Carbon\Carbon::parse($opening->close_time)->format('H:i') : '') }}">
                                    @error('close_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="break_start" class="form-label">Pause Start (optional)</label>
                                    <input type="time" class="form-control @error('break_start') is-invalid @enderror" 
                                           id="break_start" name="break_start" 
                                           value="{{ old('break_start', $opening->break_start ? \Carbon\Carbon::parse($opening->break_start)->format('H:i') : '') }}">
                                    @error('break_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="break_end" class="form-label">Pause Ende (optional)</label>
                                    <input type="time" class="form-control @error('break_end') is-invalid @enderror" 
                                           id="break_end" name="break_end" 
                                           value="{{ old('break_end', $opening->break_end ? \Carbon\Carbon::parse($opening->break_end)->format('H:i') : '') }}">
                                    @error('break_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notizen (optional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Besondere Hinweise für diesen Tag...">{{ old('notes', $opening->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.openings.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-arrow-left me-1"></i>
                                Zurück
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-device-floppy me-1"></i>
                                Aktualisieren
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
    const isClosedCheckbox = document.getElementById('is_closed');
    const timeFields = document.getElementById('time-fields');
    
    isClosedCheckbox.addEventListener('change', function() {
        if (this.checked) {
            timeFields.style.display = 'none';
            // Clear required attributes when hidden
            timeFields.querySelectorAll('input[required]').forEach(input => {
                input.removeAttribute('required');
            });
        } else {
            timeFields.style.display = 'block';
            // Add required attributes back
            document.getElementById('open_time').setAttribute('required', '');
            document.getElementById('close_time').setAttribute('required', '');
        }
    });
});
</script>
@endsection
