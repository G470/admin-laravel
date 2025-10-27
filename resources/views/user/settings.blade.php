@extends('layouts/contentNavbarLayout')

@section('title', 'Einstellungen')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Benutzereinstellungen</h5>
      </div>
      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success" role="alert">
            {{ session('success') }}
          </div>
        @endif

        <form method="POST" action="{{ route('user.settings.update') }}">
          @csrf
          
          <div class="row">
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h6 class="mb-0">Benachrichtigungen</h6>
                </div>
                <div class="card-body">
                  <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="notifications_email" name="notifications_email" value="1" checked>
                    <label class="form-check-label" for="notifications_email">
                      E-Mail-Benachrichtigungen
                    </label>
                  </div>
                  
                  <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="notifications_sms" name="notifications_sms" value="1">
                    <label class="form-check-label" for="notifications_sms">
                      SMS-Benachrichtigungen
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h6 class="mb-0">Präferenzen</h6>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label for="language" class="form-label">Sprache</label>
                    <select class="form-select" id="language" name="language">
                      <option value="de" selected>Deutsch</option>
                      <option value="en">English</option>
                    </select>
                  </div>
                  
                  <div class="mb-3">
                    <label for="timezone" class="form-label">Zeitzone</label>
                    <select class="form-select" id="timezone" name="timezone">
                      <option value="Europe/Berlin" selected>Europe/Berlin</option>
                      <option value="Europe/Vienna">Europe/Vienna</option>
                      <option value="Europe/Zurich">Europe/Zurich</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-4">
            <div class="col-12">
              <button type="submit" class="btn btn-primary me-2">
                <i class="ti ti-device-floppy me-1"></i>
                Einstellungen speichern
              </button>
              <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i>
                Zurück zum Dashboard
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">
  <div class="col-12">
    <div class="card border-danger">
      <div class="card-header bg-danger text-white">
        <h6 class="mb-0 text-white">Gefährlicher Bereich</h6>
      </div>
      <div class="card-body">
        <h6 class="text-danger">Konto löschen</h6>
        <p class="text-muted mb-3">
          Wenn Sie Ihr Konto löschen, werden alle Ihre Daten dauerhaft entfernt. 
          Diese Aktion kann nicht rückgängig gemacht werden.
        </p>
        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
          <i class="ti ti-trash me-1"></i>
          Konto löschen
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger">Konto löschen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Sind Sie sicher, dass Sie Ihr Konto löschen möchten?</p>
        <p class="text-danger"><strong>Diese Aktion kann nicht rückgängig gemacht werden!</strong></p>
        <form id="deleteAccountForm" method="POST" action="#">
          @csrf
          @method('DELETE')
          <div class="mb-3">
            <label for="password_confirmation" class="form-label">Passwort zur Bestätigung eingeben:</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
        <button type="submit" form="deleteAccountForm" class="btn btn-danger">Konto unwiderruflich löschen</button>
      </div>
    </div>
  </div>
</div>
@endsection
