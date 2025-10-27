@extends('layouts/contentLayoutMaster')

@section('title', 'Bewertungen & Rezensionen')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css',
  'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.css'
])
@endsection

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/app-vendor-reviews.scss'])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/app-vendor-reviews.js'])
@stack('page-scripts')
@endsection

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="user-profile-header-banner">
        <img src="{{ asset('assets/img/pages/profile-banner.png') }}" alt="Banner image" class="rounded-top img-fluid">
      </div>
      <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
        <div class="flex-grow-1 mt-3 mt-sm-5">
          <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
            <div class="user-profile-info">
              <h4>Bewertungen & Rezensionen</h4>
              <p class="mb-0">Verwalten Sie hier alle Bewertungen und Rezensionen für Ihre Vermietungsobjekte.</p>
            </div>
            <a href="javascript:void(0)" class="btn btn-primary">
              <i class="ti ti-info-circle me-1"></i>Hilfe
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-md-3 col-6">
    <div class="card shadow-none bg-label-primary h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2 pb-1">
          <div class="avatar me-2">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="ti ti-star-filled ti-md"></i>
            </span>
          </div>
          <h4 class="ms-1 mb-0">{{ number_format($stats['average_rating'], 1) }}</h4>
        </div>
        <p class="mb-1">Durchschnittliche Bewertung</p>
        <div class="rating-stars mb-2">
          @php
            $avgRating = round($stats['average_rating']);
          @endphp
          @for ($i = 1; $i <= 5; $i++)
            @if ($i <= $avgRating)
              <i class="ti ti-star-filled text-warning me-1"></i>
            @else
              <i class="ti ti-star text-muted me-1"></i>
            @endif
          @endfor
        </div>
        <p class="mb-0">Basierend auf allen veröffentlichten Bewertungen</p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card shadow-none bg-label-success h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2 pb-1">
          <div class="avatar me-2">
            <span class="avatar-initial rounded bg-label-success">
              <i class="ti ti-message-circle ti-md"></i>
            </span>
          </div>
          <h4 class="ms-1 mb-0">{{ $stats['total_reviews'] }}</h4>
        </div>
        <p class="mb-1">Gesamtzahl der Bewertungen</p>
        <div class="d-flex align-items-center">
          <div class="progress w-75 me-2" style="height: 8px;">
            <div class="progress-bar bg-success" style="width: 100%" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <span>100%</span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card shadow-none bg-label-warning h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2 pb-1">
          <div class="avatar me-2">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="ti ti-clock ti-md"></i>
            </span>
          </div>
          <h4 class="ms-1 mb-0">{{ $stats['pending_reviews'] }}</h4>
        </div>
        <p class="mb-1">Ausstehende Bewertungen</p>
        <div class="d-flex align-items-center">
          <div class="progress w-75 me-2" style="height: 8px;">
            <div class="progress-bar bg-warning" style="width: {{ $stats['total_reviews'] > 0 ? ($stats['pending_reviews'] / $stats['total_reviews'] * 100) : 0 }}%" role="progressbar" aria-valuenow="{{ $stats['total_reviews'] > 0 ? ($stats['pending_reviews'] / $stats['total_reviews'] * 100) : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <span>{{ $stats['total_reviews'] > 0 ? number_format(($stats['pending_reviews'] / $stats['total_reviews'] * 100), 0) : 0 }}%</span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-6">
    <div class="card shadow-none bg-label-info h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2 pb-1">
          <div class="avatar me-2">
            <span class="avatar-initial rounded bg-label-info">
              <i class="ti ti-user-check ti-md"></i>
            </span>
          </div>
          <h4 class="ms-1 mb-0">{{ $stats['verified_reviews'] }}</h4>
        </div>
        <p class="mb-1">Verifizierte Bewertungen</p>
        <div class="d-flex align-items-center">
          <div class="progress w-75 me-2" style="height: 8px;">
            <div class="progress-bar bg-info" style="width: {{ $stats['total_reviews'] > 0 ? ($stats['verified_reviews'] / $stats['total_reviews'] * 100) : 0 }}%" role="progressbar" aria-valuenow="{{ $stats['total_reviews'] > 0 ? ($stats['verified_reviews'] / $stats['total_reviews'] * 100) : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          <span>{{ $stats['total_reviews'] > 0 ? number_format(($stats['verified_reviews'] / $stats['total_reviews'] * 100), 0) : 0 }}%</span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Bewertungen verwalten</h5>
        <div class="dropdown">
          <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ti ti-download me-1"></i> Exportieren
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="javascript:void(0);">Excel</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">CSV</a></li>
            <li><a class="dropdown-item" href="javascript:void(0);">PDF</a></li>
          </ul>
        </div>
      </div>
      <div class="card-body">
        <div class="row mb-4">
          <div class="col-md-4 reviews-rating-filter">
            <label for="FilterTransaction" class="form-label">Bewertung filtern</label>
            <select class="form-select" id="FilterTransaction">
              <option value="" selected>Alle Bewertungen</option>
              <option value="5">5 Sterne</option>
              <option value="4">4 Sterne</option>
              <option value="3">3 Sterne</option>
              <option value="2">2 Sterne</option>
              <option value="1">1 Stern</option>
            </select>
          </div>
          <div class="col-md-4 reviews-status-filter">
            <label for="FilterStatus" class="form-label">Status filtern</label>
            <select class="form-select" id="FilterStatus">
              <option value="" selected>Alle Status</option>
              <option value="published">Veröffentlicht</option>
              <option value="pending">Ausstehend</option>
              <option value="rejected">Abgelehnt</option>
            </select>
          </div>
          <div class="col-md-4 reviews-search">
            <label for="searchReviews" class="form-label">Suchen</label>
            <div class="input-group input-group-merge">
              <span class="input-group-text" id="basic-addon-search31"><i class="ti ti-search"></i></span>
              <input type="text" class="form-control" id="searchReviews" placeholder="Suchen..." aria-label="Suchen..." aria-describedby="basic-addon-search31">
            </div>
          </div>
        </div>

        <!-- Livewire component will be loaded here -->
        @livewire('vendor.reviews.reviews-list')
      </div>
    </div>
  </div>
</div>
@endsection
