@extends('layouts/contentNavbarLayout')

@section('title', 'Artikel-Push bearbeiten')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Page Header -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-container">
                    <div class="row">
                        <div
                            class="col-12 col-md-7 d-flex align-items-center justify-content-md-start justify-content-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="ti ti-edit"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="mb-0">Artikel-Push bearbeiten</h4>
                                    <p class="text-muted mb-0">{{ $rentalPush->rental->title }}</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-12 col-md-5 d-flex align-items-center justify-content-md-end justify-content-center mt-3 mt-md-0">
                            <div class="d-flex gap-2">
                                <a href="{{ route('vendor.rental-pushes.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Zurück zur Übersicht
                                </a>
                                <a href="{{ route('vendor.rental-pushes.show', $rentalPush) }}"
                                    class="btn btn-outline-primary">
                                    <i class="ti ti-eye me-1"></i>Anzeigen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Current Status -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="card-title mb-1">
                                    <i class="ti ti-info-circle me-2"></i>Aktueller Status
                                </h6>
                                <p class="card-text mb-0">
                                    Status: <span
                                        class="badge bg-label-{{ $rentalPush->status_color }}">{{ $rentalPush->status_label }}</span>
                                    | Credits verbraucht:
                                    <strong>{{ $rentalPush->credits_used }}/{{ $rentalPush->total_credits_needed }}</strong>
                                    | Fortschritt: <strong>{{ $rentalPush->progress_percentage }}%</strong>
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                @if($rentalPush->status === 'active' || $rentalPush->status === 'paused')
                                    <form action="{{ route('vendor.rental-pushes.toggle-status', $rentalPush) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="btn btn-{{ $rentalPush->status === 'active' ? 'warning' : 'success' }} btn-sm">
                                            <i class="ti ti-{{ $rentalPush->status === 'active' ? 'pause' : 'play' }} me-1"></i>
                                            {{ $rentalPush->status === 'active' ? 'Pausieren' : 'Aktivieren' }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-settings me-2"></i>Push-Kampagne bearbeiten
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('vendor.rental-pushes.update', $rentalPush) }}" method="POST" id="pushForm">
                            @csrf
                            @method('PUT')

                            <!-- Article Selection -->
                            <div class="mb-4">
                                <label for="rental_id" class="form-label">Artikel *</label>
                                <select class="form-select @error('rental_id') is-invalid @enderror" id="rental_id"
                                    name="rental_id" required>
                                    <option value="">Artikel auswählen</option>
                                    @foreach($rentals as $rental)
                                        <option value="{{ $rental->id }}" {{ old('rental_id', $rentalPush->rental_id) == $rental->id ? 'selected' : '' }}>
                                            {{ $rental->title }} - {{ $rental->price_range_hour }}€/h
                                        </option>
                                    @endforeach
                                </select>
                                @error('rental_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category and Location -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label">Kategorie *</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                        name="category_id" required>
                                        <option value="">Kategorie auswählen</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $rentalPush->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="location_id" class="form-label">Standort *</label>
                                    <select class="form-select @error('location_id') is-invalid @enderror" id="location_id"
                                        name="location_id" required>
                                        <option value="">Standort auswählen</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('location_id', $rentalPush->location_id) == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Frequency Selection -->
                            <div class="mb-4">
                                <label for="frequency" class="form-label">Push-Frequenz *</label>
                                <select class="form-select @error('frequency') is-invalid @enderror" id="frequency"
                                    name="frequency" required>
                                    @foreach(App\Models\RentalPush::getFrequencyOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ old('frequency', $rentalPush->frequency) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date Range -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label">Startdatum *</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                        id="start_date" name="start_date"
                                        value="{{ old('start_date', $rentalPush->start_date->format('Y-m-d')) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date" class="form-label">Enddatum *</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                        id="end_date" name="end_date"
                                        value="{{ old('end_date', $rentalPush->end_date->format('Y-m-d')) }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="mb-4">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                                    required>
                                    @foreach(App\Models\RentalPush::getStatusOptions() as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', $rentalPush->status) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('vendor.rental-pushes.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-x me-1"></i>Abbrechen
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-1"></i>Änderungen speichern
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Current Campaign Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-info-circle me-2"></i>Kampagnen-Info
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Aktueller Zeitraum</label>
                            <div class="d-flex justify-content-between">
                                <span>Von:</span>
                                <span>{{ $rentalPush->start_date->format('d.m.Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Bis:</span>
                                <span>{{ $rentalPush->end_date->format('d.m.Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Dauer:</span>
                                <span>{{ $rentalPush->start_date->diffInDays($rentalPush->end_date) }} Tag(e)</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Push-Statistiken</label>
                            <div class="d-flex justify-content-between">
                                <span>Frequenz:</span>
                                <span>{{ $rentalPush->frequency_label }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Verbrauchte Credits:</span>
                                <span>{{ $rentalPush->credits_used }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Verbleibende Credits:</span>
                                <span>{{ $rentalPush->remaining_credits }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">Fortschritt:</span>
                            <span class="fw-bold text-primary">{{ $rentalPush->progress_percentage }}%</span>
                        </div>
                        <div class="progress mt-2" style="height: 8px;">
                            <div class="progress-bar" role="progressbar"
                                style="width: {{ $rentalPush->progress_percentage }}%"
                                aria-valuenow="{{ $rentalPush->progress_percentage }}" aria-valuemin="0"
                                aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Last Push Info -->
                @if($rentalPush->last_push_at)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-clock me-2"></i>Letzter Push
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">Ausgeführt am:</small>
                                <div class="fw-semibold">{{ $rentalPush->last_push_at->format('d.m.Y H:i') }}</div>
                            </div>
                            @if($rentalPush->next_push_at)
                                <div class="mb-2">
                                    <small class="text-muted">Nächster Push:</small>
                                    <div class="fw-semibold">{{ $rentalPush->next_push_at->format('d.m.Y H:i') }}</div>
                                </div>
                                <div>
                                    <small class="text-muted">Verbleibende Zeit:</small>
                                    <div class="fw-semibold text-primary">{{ $rentalPush->time_until_next_push }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-settings me-2"></i>Schnellaktionen
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($rentalPush->status === 'active' || $rentalPush->status === 'paused')
                                <form action="{{ route('vendor.rental-pushes.toggle-status', $rentalPush) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-outline-{{ $rentalPush->status === 'active' ? 'warning' : 'success' }} w-100">
                                        <i class="ti ti-{{ $rentalPush->status === 'active' ? 'pause' : 'play' }} me-1"></i>
                                        {{ $rentalPush->status === 'active' ? 'Pausieren' : 'Aktivieren' }}
                                    </button>
                                </form>
                            @endif
                            @if($rentalPush->status !== 'cancelled')
                                <form action="{{ route('vendor.rental-pushes.destroy', $rentalPush) }}" method="POST"
                                    onsubmit="return confirm('Sind Sie sicher, dass Sie diese Push-Kampagne abbrechen möchten?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="ti ti-trash me-1"></i>Kampagne abbrechen
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Form validation for date changes
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');
            const frequency = document.getElementById('frequency');

            function validateDates() {
                const start = new Date(startDate.value);
                const end = new Date(endDate.value);

                if (start && end && start >= end) {
                    endDate.setCustomValidity('Enddatum muss nach dem Startdatum liegen');
                } else {
                    endDate.setCustomValidity('');
                }
            }

            startDate.addEventListener('change', validateDates);
            endDate.addEventListener('change', validateDates);
        });
    </script>
@endpush