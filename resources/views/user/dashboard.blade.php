@extends('layouts/contentNavbarLayout')

@section('title', 'Benutzer Dashboard')

@section('content')
  <div class="row">
    <div class="col-12">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Willkommen, {{ $user->name }}!</h5>
      <span class="badge 
        @if($user->is_admin) bg-danger @elseif($user->is_vendor) bg-success @else bg-primary @endif">
        @if($user->is_admin) Admin @elseif($user->is_vendor) Vendor @else Kunde @endif
      </span>
      </div>
      <div class="card-body">
      <div class="row">
        <div class="col-md-4 col-sm-6 mb-4">
        <div class="card bg-primary text-white">
          <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
            <h3 class="card-title text-white mb-1">{{ $totalBookings }}</h3>
            <p class="card-text">Gesamte Anfragen</p>
            </div>
            <div class="avatar">
            <div class="avatar-initial bg-label-light rounded">
              <i class="ti ti-calendar ti-md"></i>
            </div>
            </div>
          </div>
          </div>
        </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-4">
        <div class="card bg-success text-white">
          <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
            <h3 class="card-title text-white mb-1">{{ $activeBookings }}</h3>
            <p class="card-text">Aktive Anfragen</p>
            </div>
            <div class="avatar">
            <div class="avatar-initial bg-label-light rounded">
              <i class="ti ti-clock ti-md"></i>
            </div>
            </div>
          </div>
          </div>
        </div>
        </div>

        <div class="col-md-4 col-sm-6 mb-4">
        <div class="card bg-info text-white">
          <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
            <h3 class="card-title text-white mb-1">
              {{ $user->last_login_at ? $user->last_login_at->format('d.m.Y') : 'Nie' }}</h3>
            <p class="card-text">Letzter Login</p>
            </div>
            <div class="avatar">
            <div class="avatar-initial bg-label-light rounded">
              <i class="ti ti-user ti-md"></i>
            </div>
            </div>
          </div>
          </div>
        </div>
        </div>
      </div>
      </div>
    </div>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Letzte Anfragen</h5>
      <a href="{{ route('user.bookings') }}" class="btn btn-primary btn-sm">Alle anzeigen</a>
      </div>
      <div class="card-body">
      @if($recentBookings->count() > 0)
      <div class="table-responsive">
      <table class="table table-striped">
      <thead>
        <tr>
        <th>Artikel</th>
        <th>Zeitraum</th>
        <th>Status</th>
        <th>Gesamtpreis</th>
        <th>Aktionen</th>
        </tr>
      </thead>
      <tbody>
        @foreach($recentBookings as $booking)
      <tr>
      <td>{{ $booking->rental->title ?? 'N/A' }}</td>
      <td>
      {{ \Carbon\Carbon::parse($booking->start_date)->format('d.m.Y') }} -
      {{ \Carbon\Carbon::parse($booking->end_date)->format('d.m.Y') }}
      </td>
      <td>
      <span class="badge 
        @if($booking->status === 'confirmed') bg-success
      @elseif($booking->status === 'pending') bg-warning
      @elseif($booking->status === 'cancelled') bg-danger
      @else bg-secondary @endif">
      {{ ucfirst($booking->status) }}
      </span>
      </td>
      <td>€{{ number_format($booking->total_price, 2) }}</td>
      <td>
      <a href="{{ route('user.booking.details', $booking->id) }}" class="btn btn-sm btn-outline-primary">
      <i class="ti ti-eye"></i> Details
      </a>
      </td>
      </tr>
      @endforeach
      </tbody>
      </table>
      </div>
    @else
      <div class="text-center py-4">
      <i class="ti ti-calendar-off ti-lg text-muted mb-3"></i>
      <h6 class="text-muted">Noch keine Anfragen vorhanden</h6>
      <p class="text-muted mb-3">Stöbern Sie durch unsere Artikel und machen Sie Ihre erste Anfrage!</p>
      <a href="{{ route('home') }}" class="btn btn-primary">Artikel durchstöbern</a>
      </div>
    @endif
      </div>
    </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-6">
    <div class="card">
      <div class="card-header">
      <h5 class="mb-0">Schnellzugriff</h5>
      </div>
      <div class="card-body">
      <div class="list-group list-group-flush">
        <a href="{{ route('user.profile') }}"
        class="list-group-item list-group-item-action d-flex align-items-center">
        <i class="ti ti-user me-2"></i>
        Profil bearbeiten
        </a>
        <a href="{{ route('user.bookings') }}"
        class="list-group-item list-group-item-action d-flex align-items-center">
        <i class="ti ti-calendar me-2"></i>
        Alle Anfragen
        </a>
        <a href="{{ route('user.favorites') }}"
        class="list-group-item list-group-item-action d-flex align-items-center">
        <i class="ti ti-heart me-2"></i>
        Favoriten
        </a>
        <a href="{{ route('user.settings') }}"
        class="list-group-item list-group-item-action d-flex align-items-center">
        <i class="ti ti-settings me-2"></i>
        Einstellungen
        </a>
      </div>
      </div>
    </div>
    </div>

    @if($user->is_vendor || $user->is_admin)
    <div class="col-md-6">
    <div class="card">
      <div class="card-header">
      <h5 class="mb-0">
      @if($user->is_vendor) Vendor @endif
      @if($user->is_admin) Admin @endif
      Bereich
      </h5>
      </div>
      <div class="card-body">
      <div class="list-group list-group-flush">
      @if($user->is_vendor || $user->is_admin)
      <a href="{{ route('vendor-dashboard') }}"
      class="list-group-item list-group-item-action d-flex align-items-center">
      <i class="ti ti-dashboard me-2"></i>
      Vendor Dashboard
      </a>
      <a href="{{ route('vendor-rentals') }}"
      class="list-group-item list-group-item-action d-flex align-items-center">
      <i class="ti ti-box me-2"></i>
      Meine Artikel verwalten
      </a>
      @endif
      @if($user->is_admin)
      <a href="{{ route('admin.dashboard') }}"
      class="list-group-item list-group-item-action d-flex align-items-center">
      <i class="ti ti-shield me-2"></i>
      Admin Panel
      </a>
      @endif
      </div>
      </div>
    </div>
    </div>
    @endif
  </div>
@endsection