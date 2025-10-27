<form wire:submit="saveDefaultContactDetails">
    <div class="row">
        <!-- Firma -->
        <div class="col-md-6 mb-3">
            <label for="company_name" class="form-label">Firma</label>
            <div class="input-group">
                <input type="text" class="form-control @error('contactFields.company_name') is-invalid @enderror"
                    id="company_name" wire:model="contactFields.company_name" placeholder="Firmenname">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox"
                        wire:model="visibilityToggles.show_company_name" title="Im Frontend anzeigen">
                </div>
            </div>
            @error('contactFields.company_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Anrede -->
        <div class="col-md-6 mb-3">
            <label for="salutation" class="form-label">Anrede</label>
            <div class="input-group">
                <select class="form-select @error('contactFields.salutation') is-invalid @enderror" id="salutation"
                    wire:model="contactFields.salutation">
                    <option value="">Bitte wählen</option>
                    <option value="Herr">Herr</option>
                    <option value="Frau">Frau</option>
                    <option value="Divers">Divers</option>
                </select>
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox"
                        wire:model="visibilityToggles.show_salutation" title="Im Frontend anzeigen">
                </div>
            </div>
            @error('contactFields.salutation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Vorname -->
        <div class="col-md-6 mb-3">
            <label for="first_name" class="form-label">Vorname</label>
            <div class="input-group">
                <input type="text" class="form-control @error('contactFields.first_name') is-invalid @enderror"
                    id="first_name" wire:model="contactFields.first_name" placeholder="Vorname">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox"
                        wire:model="visibilityToggles.show_first_name" title="Im Frontend anzeigen">
                </div>
            </div>
            @error('contactFields.first_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Nachname -->
        <div class="col-md-6 mb-3">
            <label for="last_name" class="form-label">Nachname</label>
            <div class="input-group">
                <input type="text" class="form-control @error('contactFields.last_name') is-invalid @enderror"
                    id="last_name" wire:model="contactFields.last_name" placeholder="Nachname">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox"
                        wire:model="visibilityToggles.show_last_name" title="Im Frontend anzeigen">
                </div>
            </div>
            @error('contactFields.last_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Straße -->
        <div class="col-md-6 mb-3">
            <label for="street" class="form-label">Straße</label>
            <div class="input-group">
                <input type="text" class="form-control @error('contactFields.street') is-invalid @enderror" id="street"
                    wire:model="contactFields.street" placeholder="Straßenname">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox"
                        wire:model="visibilityToggles.show_street" title="Im Frontend anzeigen">
                </div>
            </div>
            @error('contactFields.street')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Hausnummer -->
        <div class="col-md-6 mb-3">
            <label for="house_number" class="form-label">Hausnummer</label>
            <div class="input-group">
                <input type="text" class="form-control @error('contactFields.house_number') is-invalid @enderror"
                    id="house_number" wire:model="contactFields.house_number" placeholder="123a">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox"
                        wire:model="visibilityToggles.show_house_number" title="Im Frontend anzeigen">
                </div>
            </div>
            @error('contactFields.house_number')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- PLZ -->
        <div class="col-md-6 mb-3">
            <label for="postal_code" class="form-label">PLZ</label>
            <div class="input-group">
                <input type="text" class="form-control @error('contactFields.postal_code') is-invalid @enderror"
                    id="postal_code" wire:model="contactFields.postal_code" placeholder="12345">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox"
                        wire:model="visibilityToggles.show_postal_code" title="Im Frontend anzeigen">
                </div>
            </div>
            @error('contactFields.postal_code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Ort -->
        <div class="col-md-6 mb-3">
            <label for="city" class="form-label">Ort</label>
            <div class="input-group">
                <input type="text" class="form-control @error('contactFields.city') is-invalid @enderror" id="city"
                    wire:model="contactFields.city" placeholder="Musterstadt">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox" wire:model="visibilityToggles.show_city"
                        title="Im Frontend anzeigen">
                </div>
            </div>
            @error('contactFields.city')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Land -->
        <div class="col-md-6 mb-3">
            <label for="country_id" class="form-label">Land</label>
            <div class="input-group">
                <select class="form-select @error('contactFields.country_id') is-invalid @enderror" id="country_id"
                    wire:model="contactFields.country_id">
                    <option value="">Bitte wählen</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                    @endforeach
                </select>
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox"
                        wire:model="visibilityToggles.show_country" title="Im Frontend anzeigen">
                </div>
            </div>
            @error('contactFields.country_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Telefon -->
        <div class="col-md-6 mb-3">
            <label for="phone" class="form-label">Telefon</label>
            <div class="input-group">
                <input type="tel" class="form-control @error('contactFields.phone') is-invalid @enderror" id="phone"
                    wire:model="contactFields.phone" placeholder="+49 123 456789">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox" wire:model="visibilityToggles.show_phone"
                        title="Im Frontend anzeigen">
                </div>
            </div>
            @error('contactFields.phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Mobil -->
        <div class="col-md-6 mb-3">
            <label for="mobile" class="form-label">Mobil</label>
            <div class="input-group">
                <input type="tel" class="form-control @error('contactFields.mobile') is-invalid @enderror" id="mobile"
                    wire:model="contactFields.mobile" placeholder="+49 123 456789">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox"
                        wire:model="visibilityToggles.show_mobile" title="Im Frontend anzeigen">
                </div>
            </div>
            @error('contactFields.mobile')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- WhatsApp -->
        <div class="col-md-6 mb-3">
            <label for="whatsapp" class="form-label">WhatsApp</label>
            <div class="input-group">
                <input type="tel" class="form-control @error('contactFields.whatsapp') is-invalid @enderror"
                    id="whatsapp" wire:model="contactFields.whatsapp" placeholder="+49 123 456789">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox"
                        wire:model="visibilityToggles.show_whatsapp" title="Im Frontend anzeigen">
                </div>
            </div>
            @error('contactFields.whatsapp')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Website -->
        <div class="col-md-6 mb-3">
            <label for="website" class="form-label">Website</label>
            <div class="input-group">
                <input type="url" class="form-control @error('contactFields.website') is-invalid @enderror" id="website"
                    wire:model="contactFields.website" placeholder="https://www.example.com">
                <div class="input-group-text">
                    <input class="form-check-input mt-0" type="checkbox"
                        wire:model="visibilityToggles.show_website" title="Im Frontend anzeigen">
                </div>
            </div>
            @error('contactFields.website')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-success">
            <i class="ti ti-check me-1"></i>Speichern
        </button>
        <button type="button" class="btn btn-outline-secondary" wire:click="toggleDefaultEdit">
            <i class="ti ti-x me-1"></i>Abbrechen
        </button>
    </div>
</form>