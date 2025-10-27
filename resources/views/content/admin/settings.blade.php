@extends('layouts/contentNavbarLayout')

@section('title', 'Systemeinstellungen')

@section('vendor-style')
    <!-- FormValidation CSS -->
    @vite(['resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('vendor-script')
    <!-- FormValidation JS -->
    @vite(['resources/assets/vendor/libs/@form-validation/popular.js'])
@endsection

@section('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Tab-Wechsel über URL-Hash
            let hash = window.location.hash;
            if (hash) {
                const tabElement = document.querySelector('.nav-link[href="' + hash + '"]');
                if (tabElement) {
                    const tab = new bootstrap.Tab(tabElement);
                    tab.show();
                }
            }

            // Bei Klick auf Tab, Hash in URL setzen
            document.querySelectorAll('.nav-link').forEach(function(tab) {
                tab.addEventListener('shown.bs.tab', function (e) {
                    window.location.hash = e.target.hash;
                });
            });

            // Form validation and submission
            const settingsForm = document.getElementById('settingsForm');
            if (settingsForm) {
                settingsForm.addEventListener('submit', function (e) {
                    // Basic client-side validation
                    let isValid = true;

                    // Check required fields
                    this.querySelectorAll('input[required], select[required]').forEach(function(field) {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('is-invalid');
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    });

                    if (!isValid) {
                        e.preventDefault();
                        if (typeof toastr !== 'undefined') {
                            toastr.error('Bitte füllen Sie alle Pflichtfelder aus.');
                        } else {
                            alert('Bitte füllen Sie alle Pflichtfelder aus.');
                        }
                        return false;
                    }

                    // Show loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i> Speichere...';
                    submitBtn.disabled = true;

                    // Reset button after a delay to show feedback
                    setTimeout(function() {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 2000);
                });
            }

            // Auto-save for important toggles
            const maintenanceMode = document.getElementById('maintenance_mode');
            if (maintenanceMode) {
                maintenanceMode.addEventListener('change', function() {
                    if (this.checked) {
                        if (!confirm('Sind Sie sicher, dass Sie den Wartungsmodus aktivieren möchten? Dies macht die Website für Benutzer unzugänglich.')) {
                            this.checked = false;
                            return false;
                        }
                    }
                });
            }

            // Validate payment methods selection
            document.querySelectorAll('input[name="payment_methods[]"]').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const checkedCount = document.querySelectorAll('input[name="payment_methods[]"]:checked').length;
                    if (checkedCount === 0) {
                        if (typeof toastr !== 'undefined') {
                            toastr.warning('Mindestens eine Zahlungsmethode muss ausgewählt werden.');
                        } else {
                            alert('Mindestens eine Zahlungsmethode muss ausgewählt werden.');
                        }
                        this.checked = true;
                    }
                });
            });
        });

        // Cache clear function
        function clearCache() {
            if (confirm('Möchten Sie wirklich den Cache leeren?')) {
                fetch('{{ route("admin.settings.cache-clear") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Cache wurde erfolgreich geleert.');
                    } else {
                        alert('Cache wurde erfolgreich geleert.');
                    }
                })
                .catch(error => {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Fehler beim Leeren des Caches.');
                    } else {
                        alert('Fehler beim Leeren des Caches.');
                    }
                });
            }
        }

        // Backup function
        function createBackup() {
            if (confirm('Möchten Sie ein Backup erstellen?')) {
                fetch('{{ route("admin.settings.backup") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (typeof toastr !== 'undefined') {
                        toastr.success('Backup wurde erfolgreich erstellt.');
                    } else {
                        alert('Backup wurde erfolgreich erstellt.');
                    }
                })
                .catch(error => {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Fehler beim Erstellen des Backups.');
                    } else {
                        alert('Fehler beim Erstellen des Backups.');
                    }
                });
            }
        }

        // SMTP Test function
        function testSMTP() {
            const testEmail = document.getElementById('test_email').value;
            if (!testEmail) {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Bitte geben Sie eine Test-E-Mail-Adresse ein.');
                } else {
                    alert('Bitte geben Sie eine Test-E-Mail-Adresse ein.');
                }
                return;
            }

            if (!confirm('Möchten Sie eine Test-E-Mail an ' + testEmail + ' senden?')) {
                return;
            }

            const btn = document.querySelector('button[onclick="testSMTP()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i> Sende Test...';
            btn.disabled = true;

            fetch('{{ route("admin.settings.test-smtp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    test_email: testEmail
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'HTTP Error: ' + response.status);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message || 'Test-E-Mail wurde erfolgreich gesendet. Bitte überprüfen Sie Ihr Postfach.');
                    } else {
                        alert(data.message || 'Test-E-Mail wurde erfolgreich gesendet. Bitte überprüfen Sie Ihr Postfach.');
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message || 'Fehler beim Senden der Test-E-Mail.');
                    } else {
                        alert(data.message || 'Fehler beim Senden der Test-E-Mail.');
                    }
                }
            })
            .catch(error => {
                console.error('SMTP Test Error:', error);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Fehler beim Senden der Test-E-Mail: ' + error.message);
                } else {
                    alert('Fehler beim Senden der Test-E-Mail: ' + error.message);
                }
            })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }
    </script>
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Systemeinstellungen
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Systemeinstellungen</h5>
                    <p class="card-subtitle">Konfigurieren Sie die grundlegenden Einstellungen der Plattform</p>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Fehler beim Speichern:</strong>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form id="settingsForm" method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        @method('PUT')
                        <!-- Tabs für Einstellungs-Kategorien -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general"
                                    aria-controls="general" role="tab" aria-selected="true">
                                    <i class="ti ti-settings me-1"></i> Allgemein
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="payment-tab" data-bs-toggle="tab" href="#payment"
                                    aria-controls="payment" role="tab" aria-selected="false">
                                    <i class="ti ti-currency-euro me-1"></i> Zahlungen
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="notification-tab" data-bs-toggle="tab" href="#notification"
                                    aria-controls="notification" role="tab" aria-selected="false">
                                    <i class="ti ti-bell me-1"></i> Benachrichtigungen
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="seo-tab" data-bs-toggle="tab" href="#seo" aria-controls="seo"
                                    role="tab" aria-selected="false">
                                    <i class="ti ti-search me-1"></i> SEO
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="api-tab" data-bs-toggle="tab" href="#api" aria-controls="api"
                                    role="tab" aria-selected="false">
                                    <i class="ti ti-code me-1"></i> API
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="integrations-tab" data-bs-toggle="tab" href="#integrations" aria-controls="integrations"
                                    role="tab" aria-selected="false">
                                    <i class="ti ti-plug me-1"></i> Integrationen
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="smtp-tab" data-bs-toggle="tab" href="#smtp" aria-controls="smtp"
                                    role="tab" aria-selected="false">
                                    <i class="ti ti-mail me-1"></i> SMTP
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Inhalte -->
                        <div class="tab-content">
                            <!-- Allgemeine Einstellungen -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel"
                                aria-labelledby="general-tab">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="site_name" class="form-label">Website-Name</label>
                                        <input type="text" class="form-control" id="site_name" name="site_name"
                                            value="{{ old('site_name', $settingsData['site_name']) }}" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label for="site_description" class="form-label">Website-Beschreibung</label>
                                        <input type="text" class="form-control" id="site_description" name="site_description"
                                            value="{{ old('site_description', $settingsData['site_description']) }}" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="admin_email" class="form-label">Admin E-Mail-Adresse</label>
                                        <input type="email" class="form-control" id="admin_email" name="admin_email"
                                            value="{{ old('admin_email', $settingsData['admin_email']) }}" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label for="support_email" class="form-label">Support E-Mail-Adresse</label>
                                        <input type="email" class="form-control" id="support_email" name="support_email"
                                            value="{{ old('support_email', $settingsData['support_email']) }}" required />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="default_language" class="form-label">Standard-Sprache</label>
                                        <select id="default_language" class="form-select" name="default_language">
                                            <option value="de" {{ old('default_language', $settingsData['default_language']) == 'de' ? 'selected' : '' }}>Deutsch</option>
                                            <option value="en" {{ old('default_language', $settingsData['default_language']) == 'en' ? 'selected' : '' }}>Englisch</option>
                                            <option value="fr" {{ old('default_language', $settingsData['default_language']) == 'fr' ? 'selected' : '' }}>Französisch</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="date_format" class="form-label">Datumsformat</label>
                                        <select id="date_format" class="form-select" name="date_format">
                                            <option value="DD.MM.YYYY" {{ old('date_format', $settingsData['date_format']) == 'DD.MM.YYYY' ? 'selected' : '' }}>DD.MM.YYYY</option>
                                            <option value="MM/DD/YYYY" {{ old('date_format', $settingsData['date_format']) == 'MM/DD/YYYY' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                            <option value="YYYY-MM-DD" {{ old('date_format', $settingsData['date_format']) == 'YYYY-MM-DD' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mt-3">
                                            <input class="form-check-input" type="checkbox" id="maintenance_mode"
                                                name="maintenance_mode" value="1" {{ old('maintenance_mode', $settingsData['maintenance_mode']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="maintenance_mode">Wartungsmodus
                                                aktivieren</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mt-3">
                                            <input class="form-check-input" type="checkbox" id="enable_registration"
                                                name="enable_registration" value="1" {{ old('enable_registration', $settingsData['enable_registration']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_registration">Benutzerregistrierung
                                                aktivieren</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="maintenance_message" class="form-label">Wartungsmeldung</label>
                                        <textarea class="form-control" id="maintenance_message" name="maintenance_message"
                                            rows="3">{{ old('maintenance_message', $settingsData['maintenance_message']) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Zahlungseinstellungen -->
                            <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="currency" class="form-label">Standardwährung</label>
                                        <select id="currency" class="form-select" name="currency">
                                            <option value="EUR" {{ old('currency', $settingsData['currency']) == 'EUR' ? 'selected' : '' }}>Euro (€)</option>
                                            <option value="USD" {{ old('currency', $settingsData['currency']) == 'USD' ? 'selected' : '' }}>US-Dollar ($)</option>
                                            <option value="GBP" {{ old('currency', $settingsData['currency']) == 'GBP' ? 'selected' : '' }}>Britisches Pfund (£)</option>
                                            <option value="CHF" {{ old('currency', $settingsData['currency']) == 'CHF' ? 'selected' : '' }}>Schweizer Franken (CHF)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tax_rate" class="form-label">Standard-Steuersatz (%)</label>
                                        <input type="number" class="form-control" id="tax_rate" name="tax_rate" 
                                            value="{{ old('tax_rate', $settingsData['tax_rate']) }}"
                                            min="0" max="100" step="0.1" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Zahlungsmethoden</label>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="payment_paypal"
                                                        name="payment_methods[]" value="paypal" 
                                                        {{ (old('payment_methods') ? in_array('paypal', old('payment_methods')) : in_array('paypal', $settingsData['payment_methods'] ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="payment_paypal">PayPal</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="payment_stripe"
                                                        name="payment_methods[]" value="stripe" 
                                                        {{ (old('payment_methods') ? in_array('stripe', old('payment_methods')) : in_array('stripe', $settingsData['payment_methods'] ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="payment_stripe">Kreditkarte
                                                        (Stripe)</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="payment_transfer"
                                                        name="payment_methods[]" value="transfer" 
                                                        {{ (old('payment_methods') ? in_array('transfer', old('payment_methods')) : in_array('transfer', $settingsData['payment_methods'] ?? [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="payment_transfer">Überweisung</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="service_fee_percentage" class="form-label">Servicegebühr für Mieter
                                            (%)</label>
                                        <input type="number" class="form-control" id="service_fee_percentage"
                                            name="service_fee_percentage" value="{{ old('service_fee_percentage', $settingsData['service_fee_percentage']) }}" min="0" max="100" step="0.1" />
                                    </div>
                                    <div class="col-md-6">
                                        <label for="host_fee_percentage" class="form-label">Servicegebühr für Vermieter
                                            (%)</label>
                                        <input type="number" class="form-control" id="host_fee_percentage"
                                            name="host_fee_percentage" value="{{ old('host_fee_percentage', $settingsData['host_fee_percentage']) }}" min="0" max="100" step="0.1" />
                                    </div>
                                </div>
                            </div>

                            <!-- Benachrichtigungseinstellungen -->
                            <div class="tab-pane fade" id="notification" role="tabpanel" aria-labelledby="notification-tab">
                                <div class="mb-3">
                                    <label class="form-label">E-Mail-Benachrichtigungen</label>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Ereignis</th>
                                                    <th>Admin</th>
                                                    <th>Vermieter</th>
                                                    <th>Mieter</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Neue Buchung</td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="notify_admin_new_booking" name="notify_admin_new_booking" value="1"
                                                                {{ old('notify_admin_new_booking', $settingsData['notify_admin_new_booking']) ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="notify_vendor_new_booking" name="notify_vendor_new_booking" value="1"
                                                                {{ old('notify_vendor_new_booking', $settingsData['notify_vendor_new_booking']) ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="notify_renter_new_booking" name="notify_renter_new_booking" value="1"
                                                                {{ old('notify_renter_new_booking', $settingsData['notify_renter_new_booking']) ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Buchung bestätigt</td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="notify_admin_booking_confirm"
                                                                name="notify_admin_booking_confirm" value="1"
                                                                {{ old('notify_admin_booking_confirm', $settingsData['notify_admin_booking_confirm']) ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="notify_vendor_booking_confirm"
                                                                name="notify_vendor_booking_confirm" value="1"
                                                                {{ old('notify_vendor_booking_confirm', $settingsData['notify_vendor_booking_confirm']) ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="notify_renter_booking_confirm"
                                                                name="notify_renter_booking_confirm" value="1"
                                                                {{ old('notify_renter_booking_confirm', $settingsData['notify_renter_booking_confirm']) ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Zahlung erhalten</td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="notify_admin_payment_received"
                                                                name="notify_admin_payment_received" value="1"
                                                                {{ old('notify_admin_payment_received', $settingsData['notify_admin_payment_received']) ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="notify_vendor_payment_received"
                                                                name="notify_vendor_payment_received" value="1"
                                                                {{ old('notify_vendor_payment_received', $settingsData['notify_vendor_payment_received']) ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="notify_renter_payment_received"
                                                                name="notify_renter_payment_received" value="1"
                                                                {{ old('notify_renter_payment_received', $settingsData['notify_renter_payment_received']) ? 'checked' : '' }}>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO Einstellungen -->
                            <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="meta_title" class="form-label">Standard Meta-Titel</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title"
                                            value="{{ old('meta_title', $settingsData['meta_title']) }}" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="meta_description" class="form-label">Standard Meta-Beschreibung</label>
                                        <textarea class="form-control" id="meta_description" name="meta_description"
                                            rows="3">{{ old('meta_description', $settingsData['meta_description']) }}</textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="meta_keywords" class="form-label">Standard Meta-Keywords</label>
                                        <input type="text" class="form-control" id="meta_keywords" name="meta_keywords"
                                            value="{{ old('meta_keywords', $settingsData['meta_keywords']) }}" />
                                        <small class="text-muted">Durch Kommas getrennte Keywords</small>
                                    </div>
                                </div>

                                <hr class="my-4">
                                <h6 class="mb-3">SEO-Templates mit Variablen-Ersetzung</h6>
                                <div class="alert alert-info d-flex mb-3">
                                    <i class="ti ti-info-circle me-2"></i>
                                    <div>
                                        <strong>Verfügbare Variablen:</strong><br>
                                        <code>{category}</code> - Kategoriename, 
                                        <code>{city}</code> - Stadtname, 
                                        <code>{postcode}</code> - Postleitzahl,
                                        <code>{state}</code> - Bundesland,
                                        <code>{country}</code> - Land
                                    </div>
                                </div>

                                <!-- Kategorien SEO Templates -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Sub Kategorie-Meta-Beschreibung Global</h6>
                                        <small class="text-muted">Hier können Sie die Standard-Meta-Beschreibung für Kategorien festlegen.</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="category_meta_title_template" class="form-label">Meta-Titel</label>
                                            <input type="text" class="form-control" id="category_meta_title_template" 
                                                name="category_meta_title_template" maxlength="60"
                                                value="{{ old('category_meta_title_template', $settingsData['category_meta_title_template'] ?? '{category} mieten deutschlandweit - Inlando') }}" />
                                            <div class="form-text">
                                                <span class="text-muted">{{ strlen(old('category_meta_title_template', $settingsData['category_meta_title_template'] ?? '{category} mieten deutschlandweit - Inlando')) }}/60 Zeichen</span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="category_meta_description_template" class="form-label">Meta-Beschreibung</label>
                                            <textarea class="form-control" id="category_meta_description_template" 
                                                name="category_meta_description_template" rows="3" maxlength="160">{{ old('category_meta_description_template', $settingsData['category_meta_description_template'] ?? '{category} gesucht? Entdecken Sie hier zahlreiche Angebote für {category}. Mieten Sie jetzt zum Bestpreis Ihren Wunschartikel auf Inlando!') }}</textarea>
                                            <div class="form-text">
                                                <span class="text-muted">{{ strlen(old('category_meta_description_template', $settingsData['category_meta_description_template'] ?? '{category} gesucht? Entdecken Sie hier zahlreiche Angebote für {category}. Mieten Sie jetzt zum Bestpreis Ihren Wunschartikel auf Inlando!')) }}/160 Zeichen</span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="category_default_text_template" class="form-label">Text</label>
                                            <textarea class="form-control" id="category_default_text_template" 
                                                name="category_default_text_template" rows="4">{{ old('category_default_text_template', $settingsData['category_default_text_template'] ?? '{category} mieten - Inlando

{category} gesucht? Auf Inlando sind Sie genau richtig! Wir bieten Ihnen eine große Auswahl an Mietartikeln für {category}, die Ihre Bedürfnisse erfüllen. Egal ob Sie {category} für einen kurzen Zeitraum oder längerfristig benötigen - bei uns finden Sie das passende Angebot.') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Standort SEO Templates -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Standort-Meta-Beschreibung Global</h6>
                                        <small class="text-muted">Hier können Sie die Standard-Meta-Beschreibung für Standorte festlegen.</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="location_meta_title_template" class="form-label">Meta-Titel</label>
                                            <input type="text" class="form-control" id="location_meta_title_template" 
                                                name="location_meta_title_template" maxlength="60"
                                                value="{{ old('location_meta_title_template', $settingsData['location_meta_title_template'] ?? '{category} in {city} mieten - Inlando') }}" />
                                            <div class="form-text">
                                                <span class="text-muted">{{ strlen(old('location_meta_title_template', $settingsData['location_meta_title_template'] ?? '{category} in {city} mieten - Inlando')) }}/60 Zeichen</span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="location_meta_description_template" class="form-label">Meta-Beschreibung</label>
                                            <textarea class="form-control" id="location_meta_description_template" 
                                                name="location_meta_description_template" rows="3" maxlength="160">{{ old('location_meta_description_template', $settingsData['location_meta_description_template'] ?? '{category} in {city} gesucht? Entdecken Sie hier zahlreiche Angebote für {category}. Mieten Sie jetzt zum Bestpreis Ihren Wunschartikel auf Inlando!') }}</textarea>
                                            <div class="form-text">
                                                <span class="text-muted">{{ strlen(old('location_meta_description_template', $settingsData['location_meta_description_template'] ?? '{category} in {city} gesucht? Entdecken Sie hier zahlreiche Angebote für {category}. Mieten Sie jetzt zum Bestpreis Ihren Wunschartikel auf Inlando!')) }}/160 Zeichen</span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="location_default_text_template" class="form-label">Text</label>
                                            <textarea class="form-control" id="location_default_text_template" 
                                                name="location_default_text_template" rows="4">{{ old('location_default_text_template', $settingsData['location_default_text_template'] ?? '{category} in {city} mieten - Inlando<br/><br/>
Suchen Sie nach {category} in {city}? Inlando ist Ihr idealer Ansprechpartner! Wir präsentieren Ihnen eine breite Palette an Mietartikeln wie {category}, die Ihre Bedürfnisse perfekt erfüllen. Egal ob Sie {category} für eine kurzfristige Nutzung oder länger benötigen - bei uns werden Sie fündig.') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- API Einstellungen -->
                            <div class="tab-pane fade" id="api" role="tabpanel" aria-labelledby="api-tab">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mt-3 mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_api" name="enable_api" value="1"
                                                {{ old('enable_api', $settingsData['enable_api']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_api">API-Zugriff aktivieren</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mt-3 mb-3">
                                            <input class="form-check-input" type="checkbox" id="enable_developer_mode"
                                                name="enable_developer_mode" value="1"
                                                {{ old('enable_developer_mode', $settingsData['enable_developer_mode']) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_developer_mode">Entwicklermodus
                                                aktivieren</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="api_rate_limit" class="form-label">API Rate Limit (Requests pro Minute)</label>
                                        <input type="number" class="form-control" id="api_rate_limit" name="api_rate_limit"
                                            value="{{ old('api_rate_limit', $settingsData['api_rate_limit']) }}" min="10" max="1000" />
                                        <small class="text-muted">Maximale Anzahl API-Requests pro Minute pro Benutzer</small>
                                    </div>
                                </div>

                                <div class="alert alert-info d-flex mb-3">
                                    <i class="ti ti-info-circle me-2"></i>
                                    <div>
                                        API-Schlüssel können in den Benutzereinstellungen generiert werden. Der
                                        Entwicklermodus aktiviert erweiterte Protokollierung und Debugging-Funktionen.
                                    </div>
                                </div>
                            </div>

                            <!-- Integration Einstellungen -->
                            <div class="tab-pane fade" id="integrations" role="tabpanel" aria-labelledby="integrations-tab">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="alert alert-info d-flex">
                                            <i class="ti ti-info-circle me-2"></i>
                                            <div>
                                                <strong>Karten-Integrationen:</strong> Konfigurieren Sie hier die Karten-Dienste für Geocoding und Standortanzeige. 
                                                Mindestens ein Dienst sollte aktiviert sein, um die Standortfunktionen zu nutzen.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Google Maps Integration -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="ti ti-brand-google me-2"></i>Google Maps Integration
                                        </h6>
                                        <small class="text-muted">Für Geocoding und interaktive Karten</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="google_maps_enabled" 
                                                        name="google_maps_enabled" value="1" 
                                                        {{ old('google_maps_enabled', $settingsData['google_maps_enabled'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="google_maps_enabled">
                                                        Google Maps aktivieren
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-8">
                                                <label for="google_maps_api_key" class="form-label">Google Maps API-Schlüssel</label>
                                                <input type="text" class="form-control" id="google_maps_api_key" 
                                                    name="google_maps_api_key" 
                                                    value="{{ old('google_maps_api_key', $settingsData['google_maps_api_key'] ?? '') }}" 
                                                    placeholder="AIzaSyB..." />
                                                <div class="form-text">
                                                    API-Schlüssel für Google Maps, Geocoding und Places API. 
                                                    <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">
                                                        API-Schlüssel erstellen
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-warning d-flex">
                                            <i class="ti ti-alert-triangle me-2"></i>
                                            <div>
                                                <strong>Hinweis:</strong> Google Maps API ist kostenpflichtig nach dem kostenlosen Kontingent. 
                                                Stellen Sie sicher, dass Sie die Nutzungsbedingungen und Preise verstehen.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- OpenStreetMap Integration -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="ti ti-map me-2"></i>OpenStreetMap Integration
                                        </h6>
                                        <small class="text-muted">Kostenlose Alternative für Geocoding und Karten</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="openstreetmap_enabled" 
                                                        name="openstreetmap_enabled" value="1" 
                                                        {{ old('openstreetmap_enabled', $settingsData['openstreetmap_enabled'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="openstreetmap_enabled">
                                                        OpenStreetMap aktivieren
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-8">
                                                <label for="openstreetmap_api_key" class="form-label">OpenStreetMap API-Schlüssel (optional)</label>
                                                <input type="text" class="form-control" id="openstreetmap_api_key" 
                                                    name="openstreetmap_api_key" 
                                                    value="{{ old('openstreetmap_api_key', $settingsData['openstreetmap_api_key'] ?? '') }}" 
                                                    placeholder="Für erweiterte Funktionen..." />
                                                <div class="form-text">
                                                    Optional: API-Schlüssel für erweiterte OpenStreetMap-Dienste (Nominatim, etc.)
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-success d-flex">
                                            <i class="ti ti-check me-2"></i>
                                            <div>
                                                <strong>Vorteil:</strong> OpenStreetMap ist kostenlos und Open Source. 
                                                Perfekt für kleinere Projekte oder als Backup-Option.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Integration Status -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">Integration Status</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="ti ti-brand-google me-2 {{ $settingsData['google_maps_enabled'] ?? false ? 'text-success' : 'text-muted' }}"></i>
                                                    <span class="fw-medium">Google Maps:</span>
                                                    <span class="ms-2 badge {{ $settingsData['google_maps_enabled'] ?? false ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $settingsData['google_maps_enabled'] ?? false ? 'Aktiviert' : 'Deaktiviert' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="ti ti-map me-2 {{ $settingsData['openstreetmap_enabled'] ?? false ? 'text-success' : 'text-muted' }}"></i>
                                                    <span class="fw-medium">OpenStreetMap:</span>
                                                    <span class="ms-2 badge {{ $settingsData['openstreetmap_enabled'] ?? false ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $settingsData['openstreetmap_enabled'] ?? false ? 'Aktiviert' : 'Deaktiviert' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if(($settingsData['google_maps_enabled'] ?? false) || ($settingsData['openstreetmap_enabled'] ?? false))
                                            <div class="alert alert-success d-flex mt-3">
                                                <i class="ti ti-check me-2"></i>
                                                <div>
                                                    <strong>Status:</strong> Mindestens ein Karten-Dienst ist aktiviert. 
                                                    Die Standortfunktionen sind verfügbar.
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-warning d-flex mt-3">
                                                <i class="ti ti-alert-triangle me-2"></i>
                                                <div>
                                                    <strong>Status:</strong> Kein Karten-Dienst ist aktiviert. 
                                                    Standortfunktionen sind nicht verfügbar.
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Google reCAPTCHA Integration -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="ti ti-shield-check me-2"></i>Google reCAPTCHA Integration
                                        </h6>
                                        <small class="text-muted">Für Spam-Schutz und Bot-Erkennung</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="recaptcha_enabled" 
                                                        name="recaptcha_enabled" value="1" 
                                                        {{ old('recaptcha_enabled', $settingsData['recaptcha_enabled'] ?? false) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="recaptcha_enabled">
                                                        reCAPTCHA aktivieren
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="recaptcha_site_key" class="form-label">reCAPTCHA Site Key</label>
                                                <input type="text" class="form-control" id="recaptcha_site_key" 
                                                    name="recaptcha_site_key" 
                                                    value="{{ old('recaptcha_site_key', $settingsData['recaptcha_site_key'] ?? '') }}" 
                                                    placeholder="6Lc..." />
                                                <div class="form-text">
                                                    Öffentlicher Schlüssel für das Frontend
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="recaptcha_secret_key" class="form-label">reCAPTCHA Secret Key</label>
                                                <input type="password" class="form-control" id="recaptcha_secret_key" 
                                                    name="recaptcha_secret_key" 
                                                    value="{{ old('recaptcha_secret_key', $settingsData['recaptcha_secret_key'] ?? '') }}" 
                                                    placeholder="6Lc..." />
                                                <div class="form-text">
                                                    Geheimer Schlüssel für die Server-Validierung
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-info d-flex">
                                            <i class="ti ti-info-circle me-2"></i>
                                            <div>
                                                <strong>Hinweis:</strong> reCAPTCHA schützt Formulare vor Spam und Bots. 
                                                <a href="https://www.google.com/recaptcha/admin" target="_blank">
                                                    reCAPTCHA-Schlüssel erstellen
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SMTP Einstellungen -->
                            <div class="tab-pane fade" id="smtp" role="tabpanel" aria-labelledby="smtp-tab">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="alert alert-info d-flex">
                                            <i class="ti ti-info-circle me-2"></i>
                                            <div>
                                                <strong>SMTP-Konfiguration:</strong> Konfigurieren Sie hier die E-Mail-Versand-Einstellungen für die Plattform. 
                                                Diese Einstellungen überschreiben die Standard-Laravel-Mail-Konfiguration.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- SMTP Server Einstellungen -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="ti ti-server me-2"></i>SMTP Server Konfiguration
                                        </h6>
                                        <small class="text-muted">Grundlegende SMTP-Einstellungen</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="smtp_host" class="form-label">SMTP Host</label>
                                                <input type="text" class="form-control" id="smtp_host" 
                                                    name="smtp_host" 
                                                    value="{{ old('smtp_host', $settingsData['smtp_host'] ?? '') }}" 
                                                    placeholder="smtp.gmail.com" />
                                                <div class="form-text">
                                                    SMTP-Server-Adresse (z.B. smtp.gmail.com, smtp.mailgun.org)
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="smtp_port" class="form-label">SMTP Port</label>
                                                <input type="number" class="form-control" id="smtp_port" 
                                                    name="smtp_port" 
                                                    value="{{ old('smtp_port', $settingsData['smtp_port'] ?? 587) }}" 
                                                    min="1" max="65535" />
                                                <div class="form-text">
                                                    SMTP-Port (587 für TLS, 465 für SSL, 25 für unverschlüsselt)
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="smtp_encryption" class="form-label">Verschlüsselung</label>
                                                <select id="smtp_encryption" class="form-select" name="smtp_encryption">
                                                    <option value="tls" {{ old('smtp_encryption', $settingsData['smtp_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                                    <option value="ssl" {{ old('smtp_encryption', $settingsData['smtp_encryption'] ?? 'tls') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                                </select>
                                                <div class="form-text">
                                                    Verschlüsselungsmethode für die SMTP-Verbindung
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- SMTP Authentifizierung -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="ti ti-user-check me-2"></i>SMTP Authentifizierung
                                        </h6>
                                        <small class="text-muted">Anmeldedaten für den SMTP-Server</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="smtp_username" class="form-label">SMTP Benutzername</label>
                                                <input type="text" class="form-control" id="smtp_username" 
                                                    name="smtp_username" 
                                                    value="{{ old('smtp_username', $settingsData['smtp_username'] ?? '') }}" 
                                                    placeholder="user@example.com" />
                                                <div class="form-text">
                                                    E-Mail-Adresse oder Benutzername für SMTP-Authentifizierung
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="smtp_password" class="form-label">SMTP Passwort</label>
                                                <input type="password" class="form-control" id="smtp_password" 
                                                    name="smtp_password" 
                                                    value="{{ old('smtp_password', $settingsData['smtp_password'] ?? '') }}" 
                                                    placeholder="••••••••" />
                                                <div class="form-text">
                                                    Passwort oder App-Passwort für SMTP-Authentifizierung
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-warning d-flex">
                                            <i class="ti ti-alert-triangle me-2"></i>
                                            <div>
                                                <strong>Sicherheitshinweis:</strong> Das SMTP-Passwort wird verschlüsselt gespeichert. 
                                                Für Gmail verwenden Sie ein App-Passwort anstelle Ihres normalen Passworts.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Absender Einstellungen -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">
                                            <i class="ti ti-mail-send me-2"></i>Absender Einstellungen
                                        </h6>
                                        <small class="text-muted">Standard-Absender für E-Mails</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="smtp_from_address" class="form-label">Absender E-Mail-Adresse</label>
                                                <input type="email" class="form-control" id="smtp_from_address" 
                                                    name="smtp_from_address" 
                                                    value="{{ old('smtp_from_address', $settingsData['smtp_from_address'] ?? '') }}" 
                                                    placeholder="noreply@inlando.de" />
                                                <div class="form-text">
                                                    Standard-E-Mail-Adresse für ausgehende E-Mails
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="smtp_from_name" class="form-label">Absender Name</label>
                                                <input type="text" class="form-control" id="smtp_from_name" 
                                                    name="smtp_from_name" 
                                                    value="{{ old('smtp_from_name', $settingsData['smtp_from_name'] ?? '') }}" 
                                                    placeholder="Inlando" />
                                                <div class="form-text">
                                                    Anzeigename für ausgehende E-Mails
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- SMTP Test -->
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="card-title mb-0">SMTP Test</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="test_email" class="form-label">Test E-Mail-Adresse</label>
                                                <input type="email" class="form-control" id="test_email" 
                                                    placeholder="test@example.com" />
                                                <div class="form-text">
                                                    E-Mail-Adresse für den SMTP-Test
                                                </div>
                                            </div>
                                            <div class="col-md-6 d-flex align-items-end">
                                                <button type="button" class="btn btn-outline-primary" onclick="testSMTP()">
                                                    <i class="ti ti-send me-1"></i>
                                                    SMTP Test senden
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="alert alert-info d-flex mt-3">
                                            <i class="ti ti-info-circle me-2"></i>
                                            <div>
                                                <strong>Test-Funktion:</strong> Senden Sie eine Test-E-Mail, um die SMTP-Konfiguration zu überprüfen. 
                                                Die E-Mail wird an die angegebene Adresse gesendet.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="ti ti-device-floppy me-1"></i>
                                Einstellungen speichern
                            </button>
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-label-secondary">
                                <i class="ti ti-arrow-left me-1"></i>
                                Zurücksetzen
                            </a>
                            <div class="float-end">
                                <button type="button" class="btn btn-warning me-2" onclick="clearCache()">
                                    <i class="ti ti-trash me-1"></i>
                                    Cache leeren
                                </button>
                                <button type="button" class="btn btn-info" onclick="createBackup()">
                                    <i class="ti ti-download me-1"></i>
                                    Backup erstellen
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection