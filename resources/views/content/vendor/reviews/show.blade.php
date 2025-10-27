@extends('layouts/layoutMaster')

@section('title', 'Bewertung Details')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
])
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/moment/moment.js',
  'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/vendor-reviews-show.js'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Page Header -->
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Bewertung Details</h5>
          <a href="{{ route('vendor.bewertungen') }}" class="btn btn-outline-secondary">
            <i class="ti ti-arrow-left me-1"></i>Zurück zur Übersicht
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <!-- Review Details -->
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">Bewertung</h5>
        </div>
        <div class="card-body">
          <!-- Rating and User Info -->
          <div class="d-flex align-items-start mb-4">
            <div class="avatar avatar-lg me-3">
              <span class="avatar-initial rounded-circle bg-label-primary fs-4">
                {{ strtoupper(substr($review->user->first_name ?? 'U', 0, 1)) }}
              </span>
            </div>
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h6 class="mb-1">{{ $review->user->first_name ?? 'Unbekannt' }} {{ $review->user->last_name ?? '' }}</h6>
                  <div class="d-flex align-items-center mb-2">
                    @for($i = 1; $i <= 5; $i++)
                      <i class="ti ti-star{{ $i <= $review->rating ? '-filled text-warning' : ' text-muted' }} me-1"></i>
                    @endfor
                    <span class="ms-2 fw-semibold">{{ $review->rating }}/5</span>
                  </div>
                  <small class="text-muted">
                    Bewertet am {{ $review->created_at->format('d.m.Y \u\m H:i') }} Uhr
                    @if($review->stay_date)
                      • Aufenthalt: {{ $review->stay_date->format('d.m.Y') }}
                    @endif
                  </small>
                </div>
                <div class="d-flex align-items-center gap-2">
                  @if($review->is_verified)
                    <span class="badge bg-success">Verifiziert</span>
                  @endif
                  @switch($review->status)
                    @case('published')
                      <span class="badge bg-success">Veröffentlicht</span>
                      @break
                    @case('pending')
                      <span class="badge bg-warning">Ausstehend</span>
                      @break
                    @case('hidden')
                      <span class="badge bg-secondary">Versteckt</span>
                      @break
                    @default
                      <span class="badge bg-light">{{ ucfirst($review->status) }}</span>
                  @endswitch
                </div>
              </div>
            </div>
          </div>

          <!-- Review Comment -->
          <div class="mb-4">
            <h6 class="mb-2">Kommentar:</h6>
            <div class="bg-light p-3 rounded">
              {{ $review->comment ?: 'Kein Kommentar hinterlassen.' }}
            </div>
          </div>

          <!-- Replies Section -->
          @if($review->replies->count() > 0)
            <div class="border-top pt-4">
              <h6 class="mb-3">Antworten ({{ $review->replies->count() }})</h6>
              @foreach($review->replies as $reply)
                <div class="d-flex align-items-start mb-3">
                  <div class="avatar avatar-sm me-3">
                    <span class="avatar-initial rounded-circle bg-label-info">
                      {{ strtoupper(substr($reply->user->first_name ?? 'V', 0, 1)) }}
                    </span>
                  </div>
                  <div class="flex-grow-1">
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <strong class="text-primary">{{ $reply->user->first_name ?? 'Vermieter' }} {{ $reply->user->last_name ?? '' }}</strong>
                        <small class="text-muted">{{ $reply->created_at->format('d.m.Y H:i') }}</small>
                      </div>
                      <p class="mb-0">{{ $reply->comment }}</p>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif

          <!-- Reply Form -->
          @if($review->replies->count() === 0)
            <div class="border-top pt-4" id="reply">
              <h6 class="mb-3">Auf Bewertung antworten</h6>
              <form action="#" method="POST">
                @csrf
                <div class="mb-3">
                  <label for="reply_comment" class="form-label">Ihre Antwort</label>
                  <textarea class="form-control" id="reply_comment" name="comment" rows="4" 
                            placeholder="Verfassen Sie eine höfliche und professionelle Antwort..."></textarea>
                </div>
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="ti ti-send me-1"></i>Antwort senden
                  </button>
                  <button type="button" class="btn btn-outline-secondary">Abbrechen</button>
                </div>
              </form>
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Rental Information -->
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">Bewertetes Objekt</h5>
        </div>
        <div class="card-body">
          @if($review->rental)
            <div class="text-center mb-3">
              @if($review->rental->images->count() > 0)
                <img src="{{ asset('storage/' . $review->rental->images->first()->image_path) }}" 
                     alt="{{ $review->rental->title }}" 
                     class="img-fluid rounded mb-3" 
                     style="max-height: 200px; object-fit: cover;">
              @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 200px;">
                  <i class="ti ti-photo-off display-6 text-muted"></i>
                </div>
              @endif
            </div>

            <h6 class="mb-2">{{ $review->rental->title }}</h6>
            
            @if($review->rental->location)
              <div class="d-flex align-items-center mb-2">
                <i class="ti ti-map-pin me-1 text-muted"></i>
                <small class="text-muted">{{ $review->rental->location->name }}</small>
              </div>
            @endif

            @if($review->rental->category)
              <div class="d-flex align-items-center mb-3">
                <i class="ti ti-category me-1 text-muted"></i>
                <small class="text-muted">{{ $review->rental->category->name }}</small>
              </div>
            @endif

            <div class="d-grid">
              <a href="{{ route('vendor.rentals.edit', $review->rental->id) }}" class="btn btn-outline-primary">
                <i class="ti ti-edit me-1"></i>Objekt bearbeiten
              </a>
            </div>
          @else
            <div class="text-center py-3">
              <i class="ti ti-alert-circle display-6 text-muted mb-2"></i>
              <p class="text-muted mb-0">Objekt nicht verfügbar</p>
            </div>
          @endif
        </div>
      </div>

      <!-- Review Statistics -->
      <div class="card mt-4">
        <div class="card-header">
          <h5 class="card-title mb-0">Bewertungsstatistiken</h5>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span>Bewertungen gesamt:</span>
            <strong>{{ Auth::user()->rentals->sum(function($rental) { return $rental->reviews->count(); }) }}</strong>
          </div>
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span>Durchschnittsbewertung:</span>
            <strong>
              {{ number_format(Auth::user()->rentals->flatMap->reviews->where('status', 'published')->avg('rating') ?? 0, 1) }}
              <i class="ti ti-star-filled text-warning"></i>
            </strong>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <span>Verifizierte Bewertungen:</span>
            <strong>{{ Auth::user()->rentals->flatMap->reviews->where('is_verified', true)->count() }}</strong>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
