@extends('layouts/layoutMaster')

@section('title', 'Neues Credit-Paket erstellen')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.credit-packages.index') }}">Credit-Pakete</a>
                        </li>
                        <li class="breadcrumb-item active">Neues Paket</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-plus text-primary me-2"></i>Neues Credit-Paket erstellen
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.credit-packages.store') }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-8">
                                    <!-- Package Details -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Paketname *</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name') }}"
                                                placeholder="z.B. Starter-Paket, Premium Bundle">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="credits_amount" class="form-label">Anzahl Credits *</label>
                                            <input type="number"
                                                class="form-control @error('credits_amount') is-invalid @enderror"
                                                id="credits_amount" name="credits_amount"
                                                value="{{ old('credits_amount') }}" min="1" max="10000"
                                                placeholder="z.B. 10, 25, 50, 100">
                                            @error('credits_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Beschreibung</label>
                                        <textarea class="form-control @error('description') is-invalid @enderror"
                                            id="description" name="description" rows="3"
                                            placeholder="Optionale Beschreibung des Pakets...">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Pricing -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="standard_price" class="form-label">Standardpreis (€) *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">€</span>
                                                <input type="number"
                                                    class="form-control @error('standard_price') is-invalid @enderror"
                                                    id="standard_price" name="standard_price"
                                                    value="{{ old('standard_price') }}" step="0.01" min="0.01" max="9999.99"
                                                    placeholder="29.99">
                                            </div>
                                            @error('standard_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="offer_price" class="form-label">Angebotspreis (€) *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">€</span>
                                                <input type="number"
                                                    class="form-control @error('offer_price') is-invalid @enderror"
                                                    id="offer_price" name="offer_price" value="{{ old('offer_price') }}"
                                                    step="0.01" min="0.01" max="9999.99" placeholder="19.99">
                                            </div>
                                            @error('offer_price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Muss kleiner oder gleich dem Standardpreis
                                                sein</small>
                                        </div>
                                    </div>

                                    <!-- Display Settings -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="sort_order" class="form-label">Reihenfolge *</label>
                                            <input type="number"
                                                class="form-control @error('sort_order') is-invalid @enderror"
                                                id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                                max="999" placeholder="0">
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">0 = erste Position, höhere Zahlen = spätere
                                                Position</small>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Status</label>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" id="is_active"
                                                    name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_active">
                                                    Paket ist aktiv
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">Inaktive Pakete werden Vendors nicht
                                                angezeigt</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview Card -->
                                <div class="col-md-4">
                                    <div class="card border">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0">
                                                <i class="ti ti-eye me-2"></i>Vorschau
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="package-preview">
                                                <div class="text-center">
                                                    <div class="avatar avatar-lg mx-auto mb-3">
                                                        <span class="avatar-initial bg-label-primary rounded">
                                                            <i class="ti ti-coins"></i>
                                                        </span>
                                                    </div>
                                                    <h5 class="package-name mb-1">Paketname</h5>
                                                    <p class="text-muted package-credits">0 Credits</p>

                                                    <div class="pricing mb-3">
                                                        <div class="standard-price text-decoration-line-through text-muted"
                                                            style="display: none;">
                                                            €0.00
                                                        </div>
                                                        <div class="offer-price h4 text-primary">€0.00</div>
                                                        <div class="discount-badge" style="display: none;">
                                                            <span class="badge bg-success">-0%</span>
                                                        </div>
                                                    </div>

                                                    <div class="package-description text-muted small"
                                                        style="display: none;">
                                                        Beschreibung
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('admin.credit-packages.index') }}"
                                            class="btn btn-outline-secondary">
                                            <i class="ti ti-arrow-left me-1"></i>Zurück
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-1"></i>Paket erstellen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Form fields
                const nameField = document.getElementById('name');
                const creditsField = document.getElementById('credits_amount');
                const standardPriceField = document.getElementById('standard_price');
                const offerPriceField = document.getElementById('offer_price');
                const descriptionField = document.getElementById('description');

                // Preview elements
                const packageName = document.querySelector('.package-name');
                const packageCredits = document.querySelector('.package-credits');
                const standardPrice = document.querySelector('.standard-price');
                const offerPrice = document.querySelector('.offer-price');
                const discountBadge = document.querySelector('.discount-badge');
                const packageDescription = document.querySelector('.package-description');

                function updatePreview() {
                    // Update name
                    packageName.textContent = nameField.value || 'Paketname';

                    // Update credits
                    const credits = parseInt(creditsField.value) || 0;
                    packageCredits.textContent = `${credits.toLocaleString()} Credits`;

                    // Update pricing
                    const standard = parseFloat(standardPriceField.value) || 0;
                    const offer = parseFloat(offerPriceField.value) || 0;

                    standardPrice.textContent = `€${standard.toFixed(2)}`;
                    offerPrice.textContent = `€${offer.toFixed(2)}`;

                    // Show/hide standard price and discount
                    if (standard > offer && offer > 0) {
                        standardPrice.style.display = 'block';
                        discountBadge.style.display = 'block';
                        const discount = Math.round(((standard - offer) / standard) * 100);
                        discountBadge.querySelector('.badge').textContent = `-${discount}%`;
                    } else {
                        standardPrice.style.display = 'none';
                        discountBadge.style.display = 'none';
                    }

                    // Update description
                    if (descriptionField.value.trim()) {
                        packageDescription.textContent = descriptionField.value;
                        packageDescription.style.display = 'block';
                    } else {
                        packageDescription.style.display = 'none';
                    }
                }

                // Add event listeners
                [nameField, creditsField, standardPriceField, offerPriceField, descriptionField].forEach(field => {
                    if (field) {
                        field.addEventListener('input', updatePreview);
                    }
                });

                // Initial preview update
                updatePreview();

                // Validate offer price <= standard price
                function validatePricing() {
                    const standard = parseFloat(standardPriceField.value) || 0;
                    const offer = parseFloat(offerPriceField.value) || 0;

                    if (offer > standard && standard > 0) {
                        offerPriceField.setCustomValidity('Angebotspreis muss kleiner oder gleich dem Standardpreis sein');
                    } else {
                        offerPriceField.setCustomValidity('');
                    }
                }

                standardPriceField.addEventListener('change', validatePricing);
                offerPriceField.addEventListener('change', validatePricing);
            });
        </script>
    @endpush
@endsection