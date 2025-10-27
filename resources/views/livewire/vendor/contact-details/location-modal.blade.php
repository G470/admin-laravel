<div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-address-book me-2"></i>Kontaktdaten bearbeiten
                </h5>
                <button type="button" class="btn-close" wire:click="closeLocationModal"></button>
            </div>
            <form wire:submit="saveLocationContactDetails">
                <div class="modal-body">
                    @if($currentLocation)
                        <div class="alert alert-info">
                            <strong>Standort:</strong>
                            {{ $currentLocation->city }}, {{ $currentLocation->postal_code }}
                            @if($currentLocation->name)
                                <br><small><strong>Name:</strong> {{ $currentLocation->name }}</small>
                            @endif
                            @if($currentLocation->street_address)
                                <br><small>{{ $currentLocation->street_address }}
                                    @if($currentLocation->additional_address)
                                        {{ $currentLocation->additional_address }}
                                    @endif</small>
                            @endif
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="useCustomContactDetails"
                                    wire:model.live="useCustomContactDetails">
                                <label class="form-check-label" for="useCustomContactDetails">
                                    Spezifische Kontaktdaten für diesen Standort verwenden
                                </label>
                            </div>
                            @if(!$useCustomContactDetails)
                                <small class="text-muted">
                                    Standard-Kontaktdaten werden verwendet
                                </small>
                            @endif
                        </div>

                        @if($useCustomContactDetails)
                            <div class="row">
                                <!-- Firma -->
                                <div class="col-md-6 mb-3">
                                    <label for="modal_company_name" class="form-label">Firma</label>
                                    <div class="input-group">
                                        <input type="text"
                                            class="form-control @error('contactFields.company_name') is-invalid @enderror"
                                            id="modal_company_name" wire:model="contactFields.company_name"
                                            placeholder="Firmenname">
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" type="checkbox"
                                                wire:model="visibilityToggles.show_company_name"
                                                title="Im Frontend anzeigen">
                                        </div>
                                    </div>
                                    @error('contactFields.company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Anrede -->
                                <div class="col-md-6 mb-3">
                                    <label for="modal_salutation" class="form-label">Anrede</label>
                                    <div class="input-group">
                                        <select class="form-select @error('contactFields.salutation') is-invalid @enderror"
                                            id="modal_salutation" wire:model="contactFields.salutation">
                                            <option value="">Bitte wählen</option>
                                            <option value="Herr">Herr</option>
                                            <option value="Frau">Frau</option>
                                            <option value="Divers">Divers</option>
                                        </select>
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" type="checkbox"
                                                wire:model="visibilityToggles.show_salutation"
                                                title="Im Frontend anzeigen">
                                        </div>
                                    </div>
                                    @error('contactFields.salutation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Vorname -->
                                <div class="col-md-6 mb-3">
                                    <label for="modal_first_name" class="form-label">Vorname</label>
                                    <div class="input-group">
                                        <input type="text"
                                            class="form-control @error('contactFields.first_name') is-invalid @enderror"
                                            id="modal_first_name" wire:model="contactFields.first_name"
                                            placeholder="Vorname">
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" type="checkbox"
                                                wire:model="visibilityToggles.show_first_name"
                                                title="Im Frontend anzeigen">
                                        </div>
                                    </div>
                                    @error('contactFields.first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Nachname -->
                                <div class="col-md-6 mb-3">
                                    <label for="modal_last_name" class="form-label">Nachname</label>
                                    <div class="input-group">
                                        <input type="text"
                                            class="form-control @error('contactFields.last_name') is-invalid @enderror"
                                            id="modal_last_name" wire:model="contactFields.last_name"
                                            placeholder="Nachname">
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" type="checkbox"
                                                wire:model="visibilityToggles.show_last_name"
                                                title="Im Frontend anzeigen">
                                        </div>
                                    </div>
                                    @error('contactFields.last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Straße -->
                                <div class="col-md-6 mb-3">
                                    <label for="modal_street" class="form-label">Straße</label>
                                    <div class="input-group">
                                        <input type="text"
                                            class="form-control @error('contactFields.street') is-invalid @enderror"
                                            id="modal_street" wire:model="contactFields.street" placeholder="Straßenname">
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
                                    <label for="modal_house_number" class="form-label">Hausnummer</label>
                                    <div class="input-group">
                                        <input type="text"
                                            class="form-control @error('contactFields.house_number') is-invalid @enderror"
                                            id="modal_house_number" wire:model="contactFields.house_number"
                                            placeholder="123a">
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" type="checkbox"
                                                wire:model="visibilityToggles.show_house_number"
                                                title="Im Frontend anzeigen">
                                        </div>
                                    </div>
                                    @error('contactFields.house_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- PLZ -->
                                <div class="col-md-6 mb-3">
                                    <label for="modal_postal_code" class="form-label">PLZ</label>
                                    <div class="input-group">
                                        <input type="text"
                                            class="form-control @error('contactFields.postal_code') is-invalid @enderror"
                                            id="modal_postal_code" wire:model="contactFields.postal_code"
                                            placeholder="12345">
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" type="checkbox"
                                                wire:model="visibilityToggles.show_postal_code"
                                                title="Im Frontend anzeigen">
                                        </div>
                                    </div>
                                    @error('contactFields.postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Ort -->
                                <div class="col-md-6 mb-3">
                                    <label for="modal_city" class="form-label">Ort</label>
                                    <div class="input-group">
                                        <input type="text"
                                            class="form-control @error('contactFields.city') is-invalid @enderror"
                                            id="modal_city" wire:model="contactFields.city" placeholder="Musterstadt">
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" type="checkbox"
                                                wire:model="visibilityToggles.show_city" title="Im Frontend anzeigen">
                                        </div>
                                    </div>
                                    @error('contactFields.city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Land -->
                                <div class="col-md-6 mb-3">
                                    <label for="modal_country_id" class="form-label">Land</label>
                                    <div class="input-group">
                                        <select class="form-select @error('contactFields.country_id') is-invalid @enderror"
                                            id="modal_country_id" wire:model="contactFields.country_id">
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
                                    <label for="modal_phone" class="form-label">Telefon</label>
                                    <div class="input-group">
                                        <input type="tel"
                                            class="form-control @error('contactFields.phone') is-invalid @enderror"
                                            id="modal_phone" wire:model="contactFields.phone"
                                            placeholder="+49 123 456789">
                                        <div class="input-group-text">
                                            <input class="form-check-input mt-0" type="checkbox"
                                                wire:model="visibilityToggles.show_phone" title="Im Frontend anzeigen">
                                        </div>
                                    </div>
                                    @error('contactFields.phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Mobil -->
                                <div class="col-md-6 mb-3">
                                    <label for="modal_mobile" class="form-label">Mobil</label>
                                    <div class="input-group">
                                        <input type="tel"
                                            class="form-control @error('contactFields.mobile') is-invalid @enderror"
                                            id="modal_mobile" wire:model="contactFields.mobile"
                                            placeholder="+49 123 456789">
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
                                    <label for="modal_whatsapp" class="form-label">WhatsApp</label>
                                    <div class="input-group">
                                        <input type="tel"
                                            class="form-control @error('contactFields.whatsapp') is-invalid @enderror"
                                            id="modal_whatsapp" wire:model="contactFields.whatsapp"
                                            placeholder="+49 123 456789">
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
                                    <label for="modal_website" class="form-label">Website</label>
                                    <div class="input-group">
                                        <input type="url"
                                            class="form-control @error('contactFields.website') is-invalid @enderror"
                                            id="modal_website" wire:model="contactFields.website"
                                            placeholder="https://www.example.com">
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
                        @else
                            <div class="alert alert-light">
                                <h6>Standard-Kontaktdaten werden verwendet</h6>
                                <p class="mb-0">
                                    Für diesen Standort werden die Standard-Kontaktdaten angezeigt, die Sie oben definiert
                                    haben.
                                    Aktivieren Sie den Schalter oben, um standortspezifische Kontaktdaten zu hinterlegen.
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeLocationModal">
                        <i class="ti ti-x me-1"></i>Abbrechen
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check me-1"></i>Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>