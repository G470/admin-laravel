@extends('layouts/contentNavbarLayout')

@section('title', 'Credit-Vergabe bearbeiten')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Credit-Vergabe bearbeiten</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.credit-grants.show', $creditGrant->id) }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left me-1"></i>Zurück zu Details
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ti ti-edit text-primary me-2"></i>
                        Credit-Vergabe #{{ $creditGrant->id }} bearbeiten
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.credit-grants.update', $creditGrant->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Vendor Selection -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vendor_id" class="form-label">
                                        Vendor <span class="text-danger">*</span>
                                    </label>
                                    <select name="vendor_id" id="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror" required>
                                        <option value="">Vendor auswählen...</option>
                                        @foreach($vendors as $vendor)
                                            <option value="{{ $vendor->id }}" {{ old('vendor_id', $creditGrant->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                                {{ $vendor->name }} ({{ $vendor->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vendor_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Wählen Sie den Vendor aus, der Credits erhalten soll.
                                    </small>
                                </div>
                            </div>

                            <!-- Credit Package -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="credit_package_id" class="form-label">
                                        Credit-Paket <span class="text-danger">*</span>
                                    </label>
                                    <select name="credit_package_id" id="credit_package_id" class="form-select @error('credit_package_id') is-invalid @enderror" required>
                                        <option value="">Paket auswählen...</option>
                                        @foreach($creditPackages as $package)
                                            <option value="{{ $package->id }}" 
                                                data-credits="{{ $package->credits_amount }}"
                                                data-price="{{ $package->standard_price }}"
                                                {{ old('credit_package_id', $creditGrant->credit_package_id) == $package->id ? 'selected' : '' }}>
                                                {{ $package->name }} ({{ $package->credits_amount }} Credits - {{ number_format($package->standard_price, 2, ',', '.') }}€)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('credit_package_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Wählen Sie das Credit-Paket als Referenz für die Vergabe.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Credits Amount -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="credits_amount" class="form-label">
                                        Credits <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="credits_amount" id="credits_amount" 
                                        class="form-control @error('credits_amount') is-invalid @enderror"
                                        value="{{ old('credits_amount', $creditGrant->credits_granted) }}" min="1" max="10000" required>
                                    @error('credits_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Anzahl der zu vergebenden Credits (1-10.000).
                                    </small>
                                </div>
                            </div>

                            <!-- Grant Type -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="grant_type" class="form-label">
                                        Vergabe-Typ <span class="text-danger">*</span>
                                    </label>
                                    <select name="grant_type" id="grant_type" class="form-select @error('grant_type') is-invalid @enderror" required>
                                        <option value="">Typ auswählen...</option>
                                        <option value="admin_grant" {{ old('grant_type', $creditGrant->grant_type) == 'admin_grant' ? 'selected' : '' }}>
                                            Admin-Vergabe
                                        </option>
                                        <option value="compensation" {{ old('grant_type', $creditGrant->grant_type) == 'compensation' ? 'selected' : '' }}>
                                            Entschädigung
                                        </option>
                                        <option value="bonus" {{ old('grant_type', $creditGrant->grant_type) == 'bonus' ? 'selected' : '' }}>
                                            Bonus
                                        </option>
                                        <option value="correction" {{ old('grant_type', $creditGrant->grant_type) == 'correction' ? 'selected' : '' }}>
                                            Korrektur
                                        </option>
                                    </select>
                                    @error('grant_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Kategorie der Credit-Vergabe.
                                    </small>
                                </div>
                            </div>

                            <!-- Grant Date -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="grant_date" class="form-label">
                                        Vergabe-Datum <span class="text-danger">*</span>
                                    </label>
                                    <input type="datetime-local" name="grant_date" id="grant_date" 
                                        class="form-control @error('grant_date') is-invalid @enderror"
                                        value="{{ old('grant_date', $creditGrant->grant_date->format('Y-m-d\TH:i')) }}" required>
                                    @error('grant_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Datum und Uhrzeit der Vergabe.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Reason -->
                        <div class="mb-3">
                            <label for="reason" class="form-label">
                                Grund für Vergabe <span class="text-danger">*</span>
                            </label>
                            <textarea name="reason" id="reason" rows="3" 
                                class="form-control @error('reason') is-invalid @enderror" 
                                placeholder="Beschreiben Sie den Grund für die Credit-Vergabe..." required>{{ old('reason', $creditGrant->reason) }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Kurze Beschreibung des Grundes für die Credit-Vergabe (max. 500 Zeichen).
                            </small>
                        </div>

                        <!-- Internal Note -->
                        <div class="mb-3">
                            <label for="internal_note" class="form-label">
                                Interne Notiz <span class="text-muted">(optional)</span>
                            </label>
                            <textarea name="internal_note" id="internal_note" rows="2" 
                                class="form-control @error('internal_note') is-invalid @enderror" 
                                placeholder="Interne Notiz für Administratoren...">{{ old('internal_note', $creditGrant->internal_note) }}</textarea>
                            @error('internal_note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Interne Notiz für Administratoren (wird nicht an den Vendor weitergegeben).
                            </small>
                        </div>

                        <!-- Summary Card -->
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="ti ti-info-circle text-info me-2"></i>
                                    Zusammenfassung
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Vendor:</strong> <span id="selected-vendor">-</span></p>
                                        <p class="mb-1"><strong>Credit-Paket:</strong> <span id="selected-package">-</span></p>
                                        <p class="mb-1"><strong>Vergabe-Typ:</strong> <span id="selected-type">-</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Credits:</strong> <span id="selected-credits">-</span></p>
                                        <p class="mb-1"><strong>Geschätzter Wert:</strong> <span id="estimated-value">-</span></p>
                                        <p class="mb-1"><strong>Vergabe-Datum:</strong> <span id="selected-date">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.credit-grants.show', $creditGrant->id) }}" class="btn btn-secondary">
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
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const vendorSelect = document.getElementById('vendor_id');
    const packageSelect = document.getElementById('credit_package_id');
    const creditsInput = document.getElementById('credits_amount');
    const typeSelect = document.getElementById('grant_type');
    const dateInput = document.getElementById('grant_date');
    
    // Update summary when form changes
    function updateSummary() {
        // Vendor
        const selectedVendor = vendorSelect.options[vendorSelect.selectedIndex];
        document.getElementById('selected-vendor').textContent = selectedVendor.text || '-';
        
        // Package
        const selectedPackage = packageSelect.options[packageSelect.selectedIndex];
        document.getElementById('selected-package').textContent = selectedPackage.text || '-';
        
        // Credits
        document.getElementById('selected-credits').textContent = creditsInput.value || '-';
        
        // Type
        const selectedType = typeSelect.options[typeSelect.selectedIndex];
        document.getElementById('selected-type').textContent = selectedType.text || '-';
        
        // Date
        const selectedDate = dateInput.value ? new Date(dateInput.value).toLocaleString('de-DE') : '-';
        document.getElementById('selected-date').textContent = selectedDate;
        
        // Estimated value
        if (selectedPackage.dataset.credits && creditsInput.value) {
            const packageCredits = parseInt(selectedPackage.dataset.credits);
            const packagePrice = parseFloat(selectedPackage.dataset.price);
            const requestedCredits = parseInt(creditsInput.value);
            
            if (packageCredits > 0) {
                const creditValue = packagePrice / packageCredits;
                const estimatedValue = creditValue * requestedCredits;
                document.getElementById('estimated-value').textContent = 
                    estimatedValue.toFixed(2).replace('.', ',') + ' €';
            } else {
                document.getElementById('estimated-value').textContent = '-';
            }
        } else {
            document.getElementById('estimated-value').textContent = '-';
        }
    }
    
    // Add event listeners
    vendorSelect.addEventListener('change', updateSummary);
    packageSelect.addEventListener('change', updateSummary);
    creditsInput.addEventListener('input', updateSummary);
    typeSelect.addEventListener('change', updateSummary);
    dateInput.addEventListener('change', updateSummary);
    
    // Initial update
    updateSummary();
    
    // Auto-fill credits when package is selected
    packageSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.dataset.credits) {
            creditsInput.value = selectedOption.dataset.credits;
            updateSummary();
        }
    });
});
</script>
@endpush
@endsection 