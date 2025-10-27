@php use Illuminate\Support\Str; @endphp

@extends('layouts/contentNavbarLayout')

@section('title', 'Persönliche Daten - Vendor')

@section('content')
    <div class="row">
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
            <!-- Profile Picture Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="user-avatar-section">
                        <div class="d-flex align-items-center flex-column">
                            @if($vendor->profile_image)
                                <img class="img-fluid rounded my-4" src="{{ asset('storage/' . $vendor->profile_image) }}"
                                    height="110" width="110" alt="User avatar">
                            @else
                                <img class="img-fluid rounded my-4" src="{{ asset('assets/img/avatars/1.png') }}" height="110"
                                    width="110" alt="User avatar">
                            @endif
                            <div class="user-info text-center">
                                <h4 class="mb-2">{{ $vendor->company_name ?: $vendor->name }}</h4>
                                <span class="badge bg-label-success">Verifizierter Anbieter</span>
                            </div>
                        </div>
                    </div>

                    <!-- Avatar Upload Form -->
                    <div class="avatar-upload-section border-top pt-3 mt-3">
                        <form method="POST" action="{{ route('vendor.avatar.update') }}" enctype="multipart/form-data"
                            class="text-center">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="profile_image" class="form-label small text-muted">Profilbild ändern</label>
                                <input type="file" class="form-control form-control-sm" id="profile_image"
                                    name="profile_image" accept="image/jpeg,image/png,image/jpg,image/gif"
                                    onchange="previewAvatar(this)">
                                <small class="text-muted d-block mt-1">Max. 2MB, JPG, PNG, GIF</small>
                            </div>

                            <!-- Preview container -->
                            <div id="avatar-preview" class="mb-3" style="display: none;">
                                <img id="preview-img" class="img-fluid rounded" style="max-height: 100px; max-width: 100px;"
                                    alt="Vorschau">
                            </div>

                            <div class="d-flex gap-2 justify-content-center">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class='bx bx-upload'></i> Hochladen
                                </button>
                                @if($vendor->profile_image)
                                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteAvatar()">
                                        <i class='bx bx-trash'></i> Löschen
                                    </button>
                                @endif
                            </div>
                        </form>

                        @if($vendor->profile_image)
                            <!-- Delete Avatar Form (hidden) -->
                            <form id="delete-avatar-form" method="POST" action="{{ route('vendor.avatar.delete') }}"
                                style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </div>
                    <div class="d-flex justify-content-around flex-wrap my-4 py-3">
                        <div class="d-flex align-items-start me-4 mt-3 gap-3">
                            <span class="badge bg-label-primary p-2 rounded"><i class='bx bx-check bx-sm'></i></span>
                            <div>
                                <h5 class="mb-0">{{ $vendor->rentals()->where('status', 'online')->count() }}</h5>
                                <span>Aktive Objekte</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mt-3 gap-3">
                            <span class="badge bg-label-primary p-2 rounded"><i class='bx bx-calendar bx-sm'></i></span>
                            <div>
                                <h5 class="mb-0">{{ $vendor->created_at->format('M Y') }}</h5>
                                <span>Mitglied seit</span>
                            </div>
                        </div>
                    </div>
                    <h5 class="pb-2 border-bottom mb-4">Details</h5>
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <span class="fw-bold me-2">E-Mail:</span>
                                <span>{{ $vendor->email }}</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-bold me-2">Status:</span>
                                <span class="badge bg-label-success">Aktiv</span>
                            </li>
                            <li class="mb-3">
                                <span class="fw-bold me-2">Rolle:</span>
                                <span>Anbieter</span>
                            </li>
                            @if($vendor->country)
                                <li class="mb-3">
                                    <span class="fw-bold me-2">Land:</span>
                                    <span>{{ $vendor->country }}</span>
                                </li>
                            @endif
                            @if($vendor->phone)
                                <li class="mb-3">
                                    <span class="fw-bold me-2">Telefon:</span>
                                    <span>{{ $vendor->phone }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Personal Data Form -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Persönliche Daten</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vendor.personal-data.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="salutation" class="form-label">Anrede</label>
                                <select class="form-select" id="salutation" name="salutation">
                                    <option value="">Bitte wählen</option>
                                    @foreach($salutations as $key => $value)
                                        <option value="{{ $key }}" {{ old('salutation', $vendor->salutation) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="first_name" class="form-label">Vorname *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                    value="{{ old('first_name', $vendor->first_name) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="last_name" class="form-label">Nachname *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                    value="{{ old('last_name', $vendor->last_name) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="street" class="form-label">Straße *</label>
                                <input type="text" class="form-control" id="street" name="street"
                                    value="{{ old('street', $vendor->street) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="house_number" class="form-label">Hausnr.</label>
                                <input type="text" class="form-control" id="house_number" name="house_number"
                                    value="{{ old('house_number', $vendor->house_number) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="address_addition" class="form-label">Adresszusatz</label>
                                <input type="text" class="form-control" id="address_addition" name="address_addition"
                                    value="{{ old('address_addition', $vendor->address_addition) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="postal_code" class="form-label">PLZ *</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code"
                                    value="{{ old('postal_code', $vendor->postal_code) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="city" class="form-label">Ort *</label>
                                <input type="text" class="form-control" id="city" name="city"
                                    value="{{ old('city', $vendor->city) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="country" class="form-label">Land *</label>
                                <select class="form-select" id="country" name="country" required>
                                    <option value="">Bitte wählen</option>
                                    @foreach($countries as $key => $value)
                                        <option value="{{ $key }}" {{ old('country', $vendor->country) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telefonnummer</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    value="{{ old('phone', $vendor->phone) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mobile" class="form-label">Mobil Nummer</label>
                                <input type="text" class="form-control" id="mobile" name="mobile"
                                    value="{{ old('mobile', $vendor->mobile) }}">
                            </div>
                        </div>

                        <div class="pt-3">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Speichern</button>
                            <button type="reset" class="btn btn-label-secondary">Zurücksetzen</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Company Information Form -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Über dein Unternehmen</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vendor.company-data.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="company_name" class="form-label">Firmenname *</label>
                                <input type="text" class="form-control" id="company_name" name="company_name"
                                    value="{{ old('company_name', $vendor->company_name) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company_logo" class="form-label">Firmenlogo</label>
                                <input type="file" class="form-control" id="company_logo" name="company_logo"
                                    accept="image/*">
                                @if($vendor->company_logo)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $vendor->company_logo) }}" alt="Company Logo"
                                            class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                                <small class="text-muted">Max. 2MB, JPG, PNG, GIF</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="company_banner" class="form-label">Firmenbanner</label>
                                <input type="file" class="form-control" id="company_banner" name="company_banner"
                                    accept="image/*">
                                @if($vendor->company_banner)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $vendor->company_banner) }}" alt="Company Banner"
                                            class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                @endif
                                <small class="text-muted">Max. 4MB, JPG, PNG, GIF</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="company_description" class="form-label">Unternehmensbeschreibung</label>
                                <textarea class="form-control" id="company_description" name="company_description" rows="4"
                                    placeholder="Beschreiben Sie Ihr Unternehmen und Ihre Dienstleistungen...">{{ old('company_description', $vendor->company_description) }}</textarea>
                                <small class="text-muted">Diese Informationen werden in Ihrem öffentlichen Profil
                                    angezeigt.</small>
                            </div>
                        </div>

                        <div class="pt-3">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Speichern</button>
                            <button type="reset" class="btn btn-label-secondary">Zurücksetzen</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Billing Address Form -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Rechnungsadresse</h5>
                    <small class="text-muted">Wird für die Rechnungsstellung verwendet</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('vendor.billing-address.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="billing_street" class="form-label">Straße *</label>
                                <input type="text" class="form-control" id="billing_street" name="billing_street"
                                    value="{{ old('billing_street', $vendor->billing_street) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="billing_house_number" class="form-label">Hausnr.</label>
                                <input type="text" class="form-control" id="billing_house_number"
                                    name="billing_house_number"
                                    value="{{ old('billing_house_number', $vendor->billing_house_number) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="billing_address_addition" class="form-label">Adresszusatz</label>
                                <input type="text" class="form-control" id="billing_address_addition"
                                    name="billing_address_addition"
                                    value="{{ old('billing_address_addition', $vendor->billing_address_addition) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="billing_postal_code" class="form-label">PLZ *</label>
                                <input type="text" class="form-control" id="billing_postal_code" name="billing_postal_code"
                                    value="{{ old('billing_postal_code', $vendor->billing_postal_code) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="billing_city" class="form-label">Ort *</label>
                                <input type="text" class="form-control" id="billing_city" name="billing_city"
                                    value="{{ old('billing_city', $vendor->billing_city) }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="billing_country" class="form-label">Land *</label>
                                <select class="form-select" id="billing_country" name="billing_country" required>
                                    <option value="">Bitte wählen</option>
                                    @foreach($countries as $key => $value)
                                        <option value="{{ $key }}" {{ old('billing_country', $vendor->billing_country) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="pt-3">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Speichern</button>
                            <button type="reset" class="btn btn-label-secondary">Zurücksetzen</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Email Form -->
            <div class="card mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">E-Mail Adresse ändern</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info" role="alert">
                        <h6 class="alert-heading fw-bold mb-1">Sichere E-Mail-Änderung</h6>
                        <p class="mb-0">
                            <strong>Aktuelle E-Mail-Adresse:</strong> {{ $vendor->email }}<br>
                            <small class="text-muted">Nach der Eingabe einer neuen E-Mail-Adresse erhalten Sie eine
                                Bestätigungs-E-Mail. Die Änderung wird erst nach Klick auf den Bestätigungslink
                                wirksam.</small>
                        </p>
                    </div>

                    <form method="POST" action="{{ route('vendor.email.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="new_email" class="form-label">Neue E-Mail Adresse *</label>
                                <input type="email" class="form-control" id="new_email" name="new_email"
                                    value="{{ old('new_email') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="new_email_confirmation" class="form-label">E-Mail Adresse bestätigen *</label>
                                <input type="email" class="form-control" id="new_email_confirmation"
                                    name="new_email_confirmation" value="{{ old('new_email_confirmation') }}" required>
                            </div>
                        </div>

                        <div class="pt-3">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">E-Mail ändern</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Form -->
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Passwort ändern</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning" role="alert">
                            <h6 class="alert-heading fw-bold mb-1">Passwort-Anforderungen:</h6>
                            <p class="mb-0">Mindestens 8 Zeichen lang. Ein sicheres Passwort sollte Groß- und Kleinbuchstaben,
                                Zahlen und Sonderzeichen enthalten.</p>
                        </div>

                        <form method="POST" action="{{ route('vendor.password.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="current_password" class="form-label">Aktuelles Passwort *</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password"
                                        required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="new_password" class="form-label">Neues Passwort *</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="new_password_confirmation" class="form-label">Neues Passwort bestätigen
                                        *</label>
                                    <input type="password" class="form-control" id="new_password_confirmation"
                                        name="new_password_confirmation" required>
                                </div>
                            </div>

                            <div class="pt-3">
                                <button type="submit" class="btn btn-primary me-sm-3 me-1">Passwort ändern</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            // Auto-fill billing address from personal address
            $('#fillBillingFromPersonal').on('click', function () {
                $('#billing_street').val($('#street').val());
                $('#billing_house_number').val($('#house_number').val());
                $('#billing_address_addition').val($('#address_addition').val());
                $('#billing_postal_code').val($('#postal_code').val());
                $('#billing_city').val($('#city').val());
                $('#billing_country').val($('#country').val()).trigger('change');
            });

            // Form validation
            $('form').on('submit', function () {
                const requiredFields = $(this).find('[required]');
                let hasErrors = false;

                requiredFields.each(function () {
                    if (!$(this).val()) {
                        $(this).addClass('is-invalid');
                        hasErrors = true;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (hasErrors) {
                    alert('Bitte füllen Sie alle Pflichtfelder aus.');
                    return false;
                }
            });
        });

        // Avatar preview function
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Check file size (2MB = 2 * 1024 * 1024 bytes)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Die Datei ist zu groß. Maximale Größe: 2MB');
                    input.value = '';
                    $('#avatar-preview').hide();
                    return;
                }

                // Check file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    alert('Ungültiges Dateiformat. Erlaubt sind: JPG, PNG, GIF');
                    input.value = '';
                    $('#avatar-preview').hide();
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#preview-img').attr('src', e.target.result);
                    $('#avatar-preview').show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#avatar-preview').hide();
            }
        }

        // Delete avatar function
        function deleteAvatar() {
            if (confirm('Möchten Sie Ihr Profilbild wirklich löschen?')) {
                document.getElementById('delete-avatar-form').submit();
            }
        }
    </script>
@endsection