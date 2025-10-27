@extends('layouts/contentNavbarLayout')

@section('title', 'Profil')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            $('.select2').select2();

            // Profilbild-Vorschau
            $('#upload-image').change(function () {
                var file = this.files[0];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#preview-image').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Vendor /</span> Profil
    </h4>

    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-4">
                <li class="nav-item">
                    <a class="nav-link active" href="javascript:void(0);"><i class="ti ti-user-circle me-1 ti-xs"></i>
                        Persönliche Daten</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0);"><i class="ti ti-lock me-1 ti-xs"></i> Sicherheit</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0);"><i class="ti ti-credit-card me-1 ti-xs"></i>
                        Zahlungsmethoden</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0);"><i class="ti ti-bell me-1 ti-xs"></i>
                        Benachrichtigungen</a>
                </li>
            </ul>

            <div class="card mb-4">
                <h5 class="card-header">Profildetails</h5>
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img src="{{asset('assets/img/avatars/1.png')}}" alt="user-avatar"
                            class="d-block w-px-100 h-px-100 rounded" id="preview-image" />
                        <div class="button-wrapper">
                            <label for="upload-image" class="btn btn-primary me-2 mb-3" tabindex="0">
                                <span class="d-none d-sm-block">Neues Foto hochladen</span>
                                <i class="ti ti-upload d-block d-sm-none"></i>
                                <input type="file" id="upload-image" class="account-file-input" hidden
                                    accept="image/png, image/jpeg" />
                            </label>
                            <button type="button" class="btn btn-label-secondary mb-3">
                                <i class="ti ti-refresh-dot d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Zurücksetzen</span>
                            </button>
                            <div class="text-muted">Erlaubt sind JPG, GIF oder PNG. Maximale Größe: 800K</div>
                        </div>
                    </div>
                </div>
                <hr class="my-0" />
                <div class="card-body">
                    <form method="POST" action="{{ route('vendor.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        <!-- Persönliche Daten -->
                        <div class="row">
                            <div class="mb-3 col-md-3">
                                <label for="salutation" class="form-label">Anrede</label>
                                <select id="salutation" name="salutation" class="form-select">
                                    <option value="Herr">Herr</option>
                                    <option value="Frau">Frau</option>
                                    <option value="Divers">Divers</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-3">
                                <label for="firstName" class="form-label">Vorname</label>
                                <input class="form-control" type="text" id="firstName" name="firstName"
                                    value="{{ old('firstName', $user->first_name ?? '') }}" />
                            </div>
                            <div class="mb-3 col-md-3">
                                <label for="lastName" class="form-label">Nachname</label>
                                <input class="form-control" type="text" id="lastName" name="lastName"
                                    value="{{ old('lastName', $user->last_name ?? '') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-4">
                                <label for="address" class="form-label">Straße</label>
                                <input class="form-control" type="text" id="address" name="address"
                                    value="{{ old('address', $user->address ?? '') }}" />
                            </div>
                            <div class="mb-3 col-md-2">
                                <label for="house_number" class="form-label">Hausnr.</label>
                                <input class="form-control" type="text" id="house_number" name="house_number"
                                    value="{{ old('house_number', $user->house_number ?? '') }}" />
                            </div>
                            <div class="mb-3 col-md-3">
                                <label for="address_addition" class="form-label">Adresszusatz</label>
                                <input class="form-control" type="text" id="address_addition" name="address_addition"
                                    value="{{ old('address_addition', $user->address_addition ?? '') }}" />
                            </div>
                            <div class="mb-3 col-md-3">
                                <label for="zipCode" class="form-label">PLZ</label>
                                <input class="form-control" type="text" id="zipCode" name="zipCode"
                                    value="{{ old('zipCode', $user->zip ?? '') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-4">
                                <label for="city" class="form-label">Ort</label>
                                <input class="form-control" type="text" id="city" name="city"
                                    value="{{ old('city', $user->city ?? '') }}" />
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="country" class="form-label">Land</label>
                                <select id="country" name="country" class="select2 form-select">
                                    <option value="">Bitte wählen</option>
                                    <option value="DE" {{ (old('country', $user->country ?? '') == 'DE') ? 'selected' : '' }}>
                                        Deutschland</option>
                                    <option value="AT" {{ (old('country', $user->country ?? '') == 'AT') ? 'selected' : '' }}>
                                        Österreich</option>
                                    <option value="CH" {{ (old('country', $user->country ?? '') == 'CH') ? 'selected' : '' }}>
                                        Schweiz</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="phone" class="form-label">Telefonnummer</label>
                                <input class="form-control" type="text" id="phone" name="phone"
                                    value="{{ old('phone', $user->phone ?? '') }}" />
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="mobile" class="form-label">Mobil Nummer</label>
                                <input class="form-control" type="text" id="mobile" name="mobile"
                                    value="{{ old('mobile', $user->mobile ?? '') }}" />
                            </div>
                        </div>
                        <hr>
                        <!-- Unternehmensdaten -->
                        <h5>Über dein Unternehmen</h5>
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="company_name" class="form-label">Firmenname</label>
                                <input class="form-control" type="text" id="company_name" name="company_name"
                                    value="{{ old('company_name', $user->company_name ?? '') }}" />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="company_logo" class="form-label">Firmenlogo</label>
                                <input class="form-control" type="file" id="company_logo" name="company_logo"
                                    accept="image/*" />
                                @if($user->company_logo)
                                    <img src="{{ asset('storage/' . $user->company_logo) }}" alt="Firmenlogo" class="mt-2"
                                        style="max-height:60px;">
                                @endif
                            </div>
                            <div class="mb-3 col-md-12">
                                <label for="company_banner" class="form-label">Firmenbanner</label>
                                <input class="form-control" type="file" id="company_banner" name="company_banner"
                                    accept="image/*" />
                                @if($user->company_banner)
                                    <img src="{{ asset('storage/' . $user->company_banner) }}" alt="Firmenbanner" class="mt-2"
                                        style="max-height:120px;">
                                @endif
                            </div>
                            <div class="mb-3 col-md-12">
                                <label for="company_description" class="form-label">Unternehmensbeschreibung</label>
                                <textarea class="form-control" id="company_description" name="company_description"
                                    rows="3">{{ old('company_description', $user->company_description ?? '') }}</textarea>
                            </div>
                            <div class="mb-3 col-md-12">
                                <label for="company_legal" class="form-label">Rechtliche Angaben</label>
                                <textarea class="form-control" id="company_legal" name="company_legal"
                                    rows="2">{{ old('company_legal', $user->company_legal ?? '') }}</textarea>
                            </div>
                        </div>
                        <hr>
                        <!-- Rechnungsadresse -->
                        <h5>Rechnungsadresse</h5>
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-secondary">Rechnungsadresse ändern</button>
                        </div>
                        <hr>
                        <!-- E-Mail ändern -->
                        <h5>E-Mail Adresse ändern</h5>
                        <div class="row">
                            <div class="mb-3 col-md-4">
                                <label for="email" class="form-label">Aktuelle E-Mail Adresse</label>
                                <input class="form-control" type="email" id="email" name="email"
                                    value="{{ old('email', $user->email ?? '') }}" readonly />
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="new_email" class="form-label">Neue E-Mail Adresse</label>
                                <input class="form-control" type="email" id="new_email" name="new_email" />
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="new_email_confirmation" class="form-label">E-Mail Adresse bestätigen</label>
                                <input class="form-control" type="email" id="new_email_confirmation"
                                    name="new_email_confirmation" />
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="email_password" class="form-label">Passwort</label>
                                <input class="form-control" type="password" id="email_password" name="email_password" />
                            </div>
                            <div class="mb-3 col-md-4 align-self-end">
                                <button type="submit" name="action" value="change_email"
                                    class="btn btn-outline-primary">E-Mail Adresse ändern</button>
                            </div>
                        </div>
                        <hr>
                        <!-- Passwort ändern -->
                        <h5>Passwort ändern</h5>
                        <div class="row">
                            <div class="mb-3 col-md-4">
                                <label for="current_password" class="form-label">Aktuelles Passwort</label>
                                <input class="form-control" type="password" id="current_password" name="current_password" />
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="new_password" class="form-label">Neues Passwort</label>
                                <input class="form-control" type="password" id="new_password" name="new_password" />
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="new_password_confirmation" class="form-label">Neues Passwort bestätigen</label>
                                <input class="form-control" type="password" id="new_password_confirmation"
                                    name="new_password_confirmation" />
                            </div>
                            <div class="mb-3 col-md-12">
                                <div id="password-strength" class="form-text">Passwortstärke: <span></span></div>
                            </div>
                            <div class="mb-3 col-md-4 align-self-end">
                                <button type="submit" name="action" value="change_password"
                                    class="btn btn-outline-primary">Passwort ändern</button>
                            </div>
                        </div>
                        <hr>
                        <!-- Newsletter -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter" value="1" {{ old('newsletter', $user->newsletter ?? false) ? 'checked' : '' }} />
                            <label class="form-check-label" for="newsletter">
                                Freu dich auf regelmäßige Mails von uns mit Updates, Tipps, Aktionen und Angeboten – und
                                wenn du mal genug hast, kannst du dich jederzeit abmelden.
                            </label>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2">Speichern</button>
                            <button type="reset" class="btn btn-label-secondary">Abbrechen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection