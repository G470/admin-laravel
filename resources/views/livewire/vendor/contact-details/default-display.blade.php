@if(!empty($defaultContactDetails))
    <div class="row">
        @if(!empty($defaultContactDetails['company_name']) && $defaultContactDetails['show_company_name'])
            <div class="col-md-6 mb-2">
                <div class="d-flex align-items-center">
                    <i class="ti ti-building me-2 text-muted"></i>
                    <strong>Firma:</strong>
                    <span class="ms-2">{{ $defaultContactDetails['company_name'] }}</span>
                </div>
            </div>
        @endif

        @php
            $name = [];
            if (!empty($defaultContactDetails['salutation']) && $defaultContactDetails['show_salutation']) {
                $name[] = $defaultContactDetails['salutation'];
            }
            if (!empty($defaultContactDetails['first_name']) && $defaultContactDetails['show_first_name']) {
                $name[] = $defaultContactDetails['first_name'];
            }
            if (!empty($defaultContactDetails['last_name']) && $defaultContactDetails['show_last_name']) {
                $name[] = $defaultContactDetails['last_name'];
            }
        @endphp

        @if(!empty($name))
            <div class="col-md-6 mb-2">
                <div class="d-flex align-items-center">
                    <i class="ti ti-user me-2 text-muted"></i>
                    <strong>Name:</strong>
                    <span class="ms-2">{{ implode(' ', $name) }}</span>
                </div>
            </div>
        @endif

        @php
            $address = [];
            if (!empty($defaultContactDetails['street']) && $defaultContactDetails['show_street']) {
                $street = $defaultContactDetails['street'];
                if (!empty($defaultContactDetails['house_number']) && $defaultContactDetails['show_house_number']) {
                    $street .= ' ' . $defaultContactDetails['house_number'];
                }
                $address[] = $street;
            }

            $cityLine = [];
            if (!empty($defaultContactDetails['postal_code']) && $defaultContactDetails['show_postal_code']) {
                $cityLine[] = $defaultContactDetails['postal_code'];
            }
            if (!empty($defaultContactDetails['city']) && $defaultContactDetails['show_city']) {
                $cityLine[] = $defaultContactDetails['city'];
            }
            if (!empty($cityLine)) {
                $address[] = implode(' ', $cityLine);
            }

            if (!empty($defaultContactDetails['country']) && $defaultContactDetails['show_country']) {
                $address[] = $defaultContactDetails['country'];
            }
        @endphp

        @if(!empty($address))
            <div class="col-md-6 mb-2">
                <div class="d-flex align-items-start">
                    <i class="ti ti-map-pin me-2 text-muted mt-1"></i>
                    <div>
                        <strong>Adresse:</strong>
                        <div class="ms-2">
                            @foreach($address as $line)
                                <div>{{ $line }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(!empty($defaultContactDetails['phone']) && $defaultContactDetails['show_phone'])
            <div class="col-md-6 mb-2">
                <div class="d-flex align-items-center">
                    <i class="ti ti-phone me-2 text-muted"></i>
                    <strong>Telefon:</strong>
                    <span class="ms-2">{{ $defaultContactDetails['phone'] }}</span>
                </div>
            </div>
        @endif

        @if(!empty($defaultContactDetails['mobile']) && $defaultContactDetails['show_mobile'])
            <div class="col-md-6 mb-2">
                <div class="d-flex align-items-center">
                    <i class="ti ti-device-mobile me-2 text-muted"></i>
                    <strong>Mobil:</strong>
                    <span class="ms-2">{{ $defaultContactDetails['mobile'] }}</span>
                </div>
            </div>
        @endif

        @if(!empty($defaultContactDetails['whatsapp']) && $defaultContactDetails['show_whatsapp'])
            <div class="col-md-6 mb-2">
                <div class="d-flex align-items-center">
                    <i class="ti ti-brand-whatsapp me-2 text-muted"></i>
                    <strong>WhatsApp:</strong>
                    <span class="ms-2">{{ $defaultContactDetails['whatsapp'] }}</span>
                </div>
            </div>
        @endif

        @if(!empty($defaultContactDetails['website']) && $defaultContactDetails['show_website'])
            <div class="col-md-6 mb-2">
                <div class="d-flex align-items-center">
                    <i class="ti ti-world me-2 text-muted"></i>
                    <strong>Website:</strong>
                    <a href="{{ $defaultContactDetails['website'] }}" target="_blank" class="ms-2">
                        {{ $defaultContactDetails['website'] }}
                    </a>
                </div>
            </div>
        @endif
    </div>
@else
    <div class="text-center py-4">
        <i class="ti ti-address-book-off fs-1 text-muted mb-3"></i>
        <h6 class="text-muted">Keine Standard-Kontaktdaten angelegt</h6>
        <p class="text-muted">Klicken Sie auf "BEARBEITEN", um Ihre Standard-Kontaktdaten zu hinterlegen.</p>
    </div>
@endif