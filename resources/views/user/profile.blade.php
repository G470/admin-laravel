@extends('layouts/contentNavbarLayout')

@section('title', 'Mein Profil')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Persönliche Daten</h5>
      </div>
      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success" role="alert">
            {{ session('success') }}
          </div>
        @endif

        <form method="POST" action="{{ route('user.profile.update') }}">
          @csrf
          
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="name" class="form-label">Name *</label>
              <input type="text" 
                     class="form-control @error('name') is-invalid @enderror" 
                     id="name" 
                     name="name" 
                     value="{{ old('name', $user->name) }}" 
                     required>
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">E-Mail-Adresse *</label>
              <input type="email" 
                     class="form-control @error('email') is-invalid @enderror" 
                     id="email" 
                     name="email" 
                     value="{{ old('email', $user->email) }}" 
                     required>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="phone" class="form-label">Telefonnummer</label>
              <input type="text" 
                     class="form-control @error('phone') is-invalid @enderror" 
                     id="phone" 
                     name="phone" 
                     value="{{ old('phone', $user->phone) }}">
              @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="city" class="form-label">Stadt</label>
              <input type="text" 
                     class="form-control @error('city') is-invalid @enderror" 
                     id="city" 
                     name="city" 
                     value="{{ old('city', $user->city) }}">
              @error('city')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-8 mb-3">
              <label for="address" class="form-label">Adresse</label>
              <input type="text" 
                     class="form-control @error('address') is-invalid @enderror" 
                     id="address" 
                     name="address" 
                     value="{{ old('address', $user->address) }}">
              @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-4 mb-3">
              <label for="postal_code" class="form-label">PLZ</label>
              <input type="text" 
                     class="form-control @error('postal_code') is-invalid @enderror" 
                     id="postal_code" 
                     name="postal_code" 
                     value="{{ old('postal_code', $user->postal_code) }}">
              @error('postal_code')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="country" class="form-label">Land</label>
              <select class="form-select @error('country') is-invalid @enderror" 
                      id="country" 
                      name="country">
                <option value="">Land auswählen</option>
                <option value="Deutschland" {{ old('country', $user->country) === 'Deutschland' ? 'selected' : '' }}>Deutschland</option>
                <option value="Österreich" {{ old('country', $user->country) === 'Österreich' ? 'selected' : '' }}>Österreich</option>
                <option value="Schweiz" {{ old('country', $user->country) === 'Schweiz' ? 'selected' : '' }}>Schweiz</option>
              </select>
              @error('country')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary me-2">
                <i class="ti ti-device-floppy me-1"></i>
                Speichern
              </button>
              <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                <i class="ti ti-arrow-left me-1"></i>
                Zurück
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
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Konto-Informationen</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label">Benutzerrolle</label>
              <div>
                <span class="badge 
                  @if($user->is_admin) bg-danger @elseif($user->is_vendor) bg-success @else bg-primary @endif">
                  @if($user->is_admin) Administrator @elseif($user->is_vendor) Anbieter @else Kunde @endif
                </span>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label">Registriert seit</label>
              <div>{{ $user->created_at->format('d.m.Y H:i') }} Uhr</div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label">Letzter Login</label>
              <div>{{ $user->last_login_at ? $user->last_login_at->format('d.m.Y H:i') . ' Uhr' : 'Nie' }}</div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label">E-Mail verifiziert</label>
              <div>
                @if($user->email_verified_at)
                  <span class="badge bg-success">
                    <i class="ti ti-check me-1"></i>Verifiziert
                  </span>
                @else
                  <span class="badge bg-warning">
                    <i class="ti ti-clock me-1"></i>Nicht verifiziert
                  </span>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
