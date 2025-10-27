@extends('layouts/contentNavbarLayout')

@section('title', 'Vermietungsobjekt bearbeiten: ' . $rental->title)

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-container">
                    <div class="row">
                        <div
                            class="col-12 col-md-7 d-flex align-items-center justify-content-md-start justify-content-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="ti ti-home-2"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="mb-0">Vermietungsobjekt bearbeiten</h4>
                                    <p class="text-muted mb-0">{{ $rental->title }}</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-12 col-md-5 d-flex align-items-center justify-content-md-end justify-content-center mt-3 mt-md-0">
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.rentals.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Zurück zur Übersicht
                                </a>
                                <a href="{{ route('admin.rentals.show', $rental) }}" class="btn btn-outline-primary">
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

        <!-- Edit Form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-edit me-2"></i>Vermietungsobjekt bearbeiten
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.rentals.update', $rental) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Basic Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="fw-semibold mb-3">
                                        <i class="ti ti-info-circle me-2"></i>Grundinformationen
                                    </h6>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label">Name *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                        name="title" value="{{ old('title', $rental->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status"
                                        name="status" required>
                                        <option value="">Status auswählen</option>
                                        <option value="active" {{ old('status', $rental->status) == 'active' ? 'selected' : '' }}>Aktiv</option>
                                        <option value="inactive" {{ old('status', $rental->status) == 'inactive' ? 'selected' : '' }}>Inaktiv</option>
                                        <option value="pending" {{ old('status', $rental->status) == 'pending' ? 'selected' : '' }}>Prüfung ausstehend</option>
                                        <option value="rejected" {{ old('status', $rental->status) == 'rejected' ? 'selected' : '' }}>Abgelehnt</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">Beschreibung *</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                        id="description" name="description" rows="4"
                                        required>{{ old('description', $rental->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Vendor and Category -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="fw-semibold mb-3">
                                        <i class="ti ti-users me-2"></i>Anbieter & Kategorie
                                    </h6>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="vendor_id" class="form-label">Anbieter *</label>
                                    <select class="form-select @error('vendor_id') is-invalid @enderror" id="vendor_id"
                                        name="vendor_id" required>
                                        <option value="">Anbieter auswählen</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('vendor_id', $rental->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->name }} ({{ $vendor->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Kategorie *</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                        name="category_id" required>
                                        <option value="">Kategorie auswählen</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $rental->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Location Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="fw-semibold mb-3">
                                        <i class="ti ti-map-pin me-2"></i>Standort
                                    </h6>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="location_id" class="form-label">Standort</label>
                                    <select class="form-select @error('location_id') is-invalid @enderror" id="location_id"
                                        name="location_id">
                                        <option value="">Standort auswählen</option>
                                        @foreach($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('location_id', $rental->location_id) == $location->id ? 'selected' : '' }}>
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('location_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="city_id" class="form-label">Stadt</label>
                                    <select class="form-select @error('city_id') is-invalid @enderror" id="city_id"
                                        name="city_id">
                                        <option value="">Stadt auswählen</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ old('city_id', $rental->city_id) == $city->id ? 'selected' : '' }}>
                                                {{ $city->city }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('city_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Pricing -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="fw-semibold mb-3">
                                        <i class="ti ti-currency-euro me-2"></i>Preise
                                    </h6>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="price_range_hour" class="form-label">Preis pro Stunde *</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01"
                                            class="form-control @error('price_range_hour') is-invalid @enderror"
                                            id="price_range_hour" name="price_range_hour"
                                            value="{{ old('price_range_hour', $rental->price_range_hour) }}" required>
                                        <span class="input-group-text">{{ $rental->currency ?? '€' }}</span>
                                    </div>
                                    @error('price_range_hour')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="price_range_day" class="form-label">Preis pro Tag</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01"
                                            class="form-control @error('price_range_day') is-invalid @enderror"
                                            id="price_range_day" name="price_range_day"
                                            value="{{ old('price_range_day', $rental->price_range_day) }}">
                                        <span class="input-group-text">{{ $rental->currency ?? '€' }}</span>
                                    </div>
                                    @error('price_range_day')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="price_range_once" class="form-label">Einmaliger Preis</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01"
                                            class="form-control @error('price_range_once') is-invalid @enderror"
                                            id="price_range_once" name="price_range_once"
                                            value="{{ old('price_range_once', $rental->price_range_once) }}">
                                        <span class="input-group-text">{{ $rental->currency ?? '€' }}</span>
                                    </div>
                                    @error('price_range_once')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="service_fee" class="form-label">Servicegebühr</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01"
                                            class="form-control @error('service_fee') is-invalid @enderror" id="service_fee"
                                            name="service_fee" value="{{ old('service_fee', $rental->service_fee) }}">
                                        <span class="input-group-text">{{ $rental->currency ?? '€' }}</span>
                                    </div>
                                    @error('service_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="currency" class="form-label">Währung *</label>
                                    <select class="form-select @error('currency') is-invalid @enderror" id="currency"
                                        name="currency" required>
                                        <option value="EUR" {{ old('currency', $rental->currency) == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                        <option value="USD" {{ old('currency', $rental->currency) == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                        <option value="CHF" {{ old('currency', $rental->currency) == 'CHF' ? 'selected' : '' }}>CHF (CHF)</option>
                                    </select>
                                    @error('currency')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Additional Settings -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="fw-semibold mb-3">
                                        <i class="ti ti-settings me-2"></i>Zusätzliche Einstellungen
                                    </h6>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="featured" name="featured"
                                            value="1" {{ old('featured', $rental->featured) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="featured">
                                            Als Featured markieren
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="fw-semibold mb-3">
                                        <i class="ti ti-search me-2"></i>SEO-Informationen
                                    </h6>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="meta_title" class="form-label">Meta-Titel</label>
                                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                        id="meta_title" name="meta_title"
                                        value="{{ old('meta_title', $rental->meta_title) }}">
                                    @error('meta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="meta_keywords" class="form-label">Meta-Schlüsselwörter</label>
                                    <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror"
                                        id="meta_keywords" name="meta_keywords"
                                        value="{{ old('meta_keywords', $rental->meta_keywords) }}">
                                    @error('meta_keywords')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="meta_description" class="form-label">Meta-Beschreibung</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                        id="meta_description" name="meta_description"
                                        rows="3">{{ old('meta_description', $rental->meta_description) }}</textarea>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Coordinates -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="fw-semibold mb-3">
                                        <i class="ti ti-map me-2"></i>Koordinaten
                                    </h6>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="latitude" class="form-label">Breitengrad</label>
                                    <input type="number" step="any"
                                        class="form-control @error('latitude') is-invalid @enderror" id="latitude"
                                        name="latitude" value="{{ old('latitude', $rental->latitude) }}">
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitude" class="form-label">Längengrad</label>
                                    <input type="number" step="any"
                                        class="form-control @error('longitude') is-invalid @enderror" id="longitude"
                                        name="longitude" value="{{ old('longitude', $rental->longitude) }}">
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.rentals.index') }}" class="btn btn-secondary">
                                            <i class="ti ti-x me-1"></i>Abbrechen
                                        </a>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ti ti-device-floppy me-1"></i>Speichern
                                            </button>
                                            <a href="{{ route('admin.rentals.show', $rental) }}"
                                                class="btn btn-outline-primary">
                                                <i class="ti ti-eye me-1"></i>Vorschau
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information Cards -->
        <div class="row mt-4">
            <!-- Rental Statistics -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-chart-bar me-2"></i>Statistiken
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-info">
                                            <i class="ti ti-eye"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $rental->views ?? 0 }}</h6>
                                        <small class="text-muted">Aufrufe</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $rental->bookings->count() }}</h6>
                                        <small class="text-muted">Buchungen</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class="ti ti-star"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $rental->reviews->count() }}</h6>
                                        <small class="text-muted">Bewertungen</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="ti ti-heart"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $rental->favorites_count ?? 0 }}</h6>
                                        <small class="text-muted">Favoriten</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rental Information -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-info-circle me-2"></i>Informationen
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Erstellt:</span>
                                    <span class="fw-semibold">{{ $rental->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Aktualisiert:</span>
                                    <span class="fw-semibold">{{ $rental->updated_at->format('d.m.Y H:i') }}</span>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Status:</span>
                                    <span
                                        class="badge bg-label-{{ $rental->status == 'active' ? 'success' : ($rental->status == 'inactive' ? 'secondary' : ($rental->status == 'pending' ? 'warning' : 'danger')) }}">
                                        {{ ucfirst($rental->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Featured:</span>
                                    <span class="badge bg-label-{{ $rental->featured ? 'warning' : 'secondary' }}">
                                        {{ $rental->featured ? 'Ja' : 'Nein' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Auto-save functionality
        let autoSaveTimer;
        const form = document.querySelector('form');

        form.addEventListener('input', function () {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function () {
                // Show auto-save indicator
                const saveButton = document.querySelector('button[type="submit"]');
                const originalText = saveButton.innerHTML;
                saveButton.innerHTML = '<i class="ti ti-loader me-1"></i>Speichern...';
                saveButton.disabled = true;

                // Auto-save logic could be implemented here
                setTimeout(function () {
                    saveButton.innerHTML = originalText;
                    saveButton.disabled = false;
                }, 1000);
            }, 2000);
        });
    </script>
@endpush