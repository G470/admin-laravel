@extends('layouts/contentNavbarLayout')

@section('title', 'Neuer Artikel-Push')

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
                                        <i class="ti ti-rocket"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="mb-0">Neuer Artikel-Push</h4>
                                    <p class="text-muted mb-0">Erstellen Sie eine neue Push-Kampagne</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-12 col-md-5 d-flex align-items-center justify-content-md-end justify-content-center mt-3 mt-md-0">
                            <div class="d-flex gap-2">
                                <a href="{{ route('vendor.rental-pushes.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Zurück zur Übersicht
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

        <!-- Credit Balance Info -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="card-title mb-1">
                                    <i class="ti ti-wallet me-2"></i>Ihr Credit-Guthaben
                                </h6>
                                <p class="card-text mb-0">
                                    Verfügbare Credits: <strong>{{ $vendorBalance }}</strong>
                                    @if($vendorBalance < 10)
                                        <span class="badge bg-warning ms-2">Niedrig</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('vendor.credits.index') }}" class="btn btn-primary btn-sm">
                                    <i class="ti ti-plus me-1"></i>Credits kaufen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Form -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-settings me-2"></i>Push-Kampagne konfigurieren
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('vendor.rental-pushes.store') }}" method="POST" id="pushForm">
                            @csrf

                            <!-- Article Selection -->
                            <div class="mb-4">
                                <label for="rental_id" class="form-label">Artikel auswählen *</label>
                                <select class="form-select @error('rental_id') is-invalid @enderror" id="rental_id"
                                    name="rental_id" required>
                                    <option value="">Artikel auswählen</option>
                                    @foreach($rentals as $rental)
                                        <option value="{{ $rental->id }}" {{ old('rental_id') == $rental->id ? 'selected' : '' }}>
                                            {{ $rental->title }} - {{ $rental->price_range_hour }}€/h
                                        </option>
                                    @endforeach
                                </select>
                                @error('rental_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Wählen Sie das Vermietungsobjekt aus, das Sie pushen
                                    möchten.</small>
                            </div>

                            <!-- Category and Location -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label">Kategorie *</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                        name="category_id" required>
                                        <option value="">Kategorie auswählen</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                            <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
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
                                        <option value="{{ $value }}" {{ old('frequency', 7) == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Wählen Sie, wie oft Ihr Artikel täglich nach oben
                                    geschoben werden soll.</small>
                            </div>

                            <!-- Date Range -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label">Startdatum *</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                        id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}"
                                        min="{{ date('Y-m-d') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="end_date" class="form-label">Enddatum *</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                        id="end_date" name="end_date"
                                        value="{{ old('end_date', date('Y-m-d', strtotime('+7 days'))) }}"
                                        min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('vendor.rental-pushes.index') }}" class="btn btn-secondary">
                                    <i class="ti ti-x me-1"></i>Abbrechen
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="ti ti-check me-1"></i>Push-Kampagne erstellen
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Cost Calculator -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-calculator me-2"></i>Kostenrechner
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Zeitraum</label>
                            <div class="d-flex justify-content-between">
                                <span>Von:</span>
                                <span id="displayStartDate">-</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Bis:</span>
                                <span id="displayEndDate">-</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Dauer:</span>
                                <span id="duration">-</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Push-Berechnung</label>
                            <div class="d-flex justify-content-between">
                                <span>Frequenz:</span>
                                <span id="displayFrequency">-</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Pushes pro Tag:</span>
                                <span id="pushesPerDay">-</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Gesamt Pushes:</span>
                                <span id="totalPushes">-</span>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">Benötigte Credits:</span>
                            <span class="fw-bold text-primary" id="requiredCredits">-</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Verfügbar:</span>
                            <span id="availableCredits">{{ $vendorBalance }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="fw-semibold">Verbleibend:</span>
                            <span class="fw-bold" id="remainingCredits">{{ $vendorBalance }}</span>
                        </div>
                    </div>
                </div>

                <!-- Tips -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-lightbulb me-2"></i>Tipps für erfolgreiche Pushes
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="ti ti-check text-success me-2"></i>
                                <small>Wählen Sie die richtige Kategorie für maximale Reichweite</small>
                            </li>
                            <li class="mb-2">
                                <i class="ti ti-check text-success me-2"></i>
                                <small>7x täglich ist optimal für hohe Sichtbarkeit</small>
                            </li>
                            <li class="mb-2">
                                <i class="ti ti-check text-success me-2"></i>
                                <small>Kürzere Kampagnen sind oft effektiver</small>
                            </li>
                            <li class="mb-2">
                                <i class="ti ti-check text-success me-2"></i>
                                <small>Überwachen Sie Ihre Push-Performance</small>
                            </li>
                            <li>
                                <i class="ti ti-check text-success me-2"></i>
                                <small>Pausieren Sie bei Bedarf und sparen Sie Credits</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');
            const frequency = document.getElementById('frequency');
            const vendorBalance = {{ $vendorBalance }};

            function updateCalculator() {
                const start = new Date(startDate.value);
                const end = new Date(endDate.value);
                const freq = parseInt(frequency.value);

                if (start && end && freq) {
                    const daysDiff = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                    const totalPushes = daysDiff * freq;
                    const requiredCredits = totalPushes;
                    const remainingCredits = vendorBalance - requiredCredits;

                    // Update display
                    document.getElementById('displayStartDate').textContent = start.toLocaleDateString('de-DE');
                    document.getElementById('displayEndDate').textContent = end.toLocaleDateString('de-DE');
                    document.getElementById('duration').textContent = daysDiff + ' Tag(e)';
                    document.getElementById('displayFrequency').textContent = freq + 'x am Tag';
                    document.getElementById('pushesPerDay').textContent = freq;
                    document.getElementById('totalPushes').textContent = totalPushes;
                    document.getElementById('requiredCredits').textContent = requiredCredits;
                    document.getElementById('remainingCredits').textContent = remainingCredits;

                    // Update colors
                    const remainingElement = document.getElementById('remainingCredits');
                    if (remainingCredits < 0) {
                        remainingElement.className = 'fw-bold text-danger';
                    } else if (remainingCredits < 10) {
                        remainingElement.className = 'fw-bold text-warning';
                    } else {
                        remainingElement.className = 'fw-bold text-success';
                    }
                }
            }

            // Add event listeners
            startDate.addEventListener('change', updateCalculator);
            endDate.addEventListener('change', updateCalculator);
            frequency.addEventListener('change', updateCalculator);

            // Initial calculation
            updateCalculator();

            // Form validation
            document.getElementById('pushForm').addEventListener('submit', function (e) {
                const requiredCredits = parseInt(document.getElementById('requiredCredits').textContent);
                if (requiredCredits > vendorBalance) {
                    e.preventDefault();
                    alert('Sie haben nicht genügend Credits für diese Kampagne. Bitte kaufen Sie mehr Credits oder reduzieren Sie die Kampagnendauer/Frequenz.');
                }
            });
        });
    </script>
@endpush