@extends('layouts/contentNavbarLayout')

@section('title', 'Credits & Promotion')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0">
              <i class="ti ti-coins text-primary me-2"></i>Credits & Promotion
            </h5>
            <small class="text-muted">Kaufen Sie Credits und heben Sie Ihre Artikel hervor</small>
          </div>
          <div>
            <div class="d-flex align-items-center">
              <div class="me-3">
                <small class="text-muted d-block">Aktuelles Guthaben</small>
                <h4 class="mb-0 text-primary">
                  <i class="ti ti-coin me-1"></i>{{ number_format($creditBalance) }} Credits
                </h4>
              </div>
              <a href="{{ route('vendor.credits.history') }}" class="btn btn-outline-secondary">
                <i class="ti ti-history me-1"></i>Verlauf
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Credit Packages -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h6 class="card-title mb-0">
            <i class="ti ti-package me-2"></i>Credit-Pakete kaufen
          </h6>
        </div>
        <div class="card-body">
          @if($availablePackages->count() > 0)
            <div class="row">
              @foreach($availablePackages as $package)
                <div class="col-lg-4 col-md-6 mb-4">
                  <div class="card border {{ $package->getPopularityScore() > 50 ? 'border-primary' : '' }} h-100">
                    @if($package->getPopularityScore() > 50)
                      <div class="card-badge">
                        <span class="badge bg-primary">Beliebt</span>
                      </div>
                    @endif
                    
                    <div class="card-body text-center">
                      <div class="avatar avatar-lg mx-auto mb-3">
                        <span class="avatar-initial bg-label-primary rounded">
                          <i class="ti ti-coins"></i>
                        </span>
                      </div>
                      
                      <h5 class="card-title">{{ $package->name }}</h5>
                      <p class="text-muted">{{ number_format($package->credits_amount) }} Credits</p>
                      
                      <div class="pricing mb-3">
                        @if($package->is_discounted)
                          <div class="text-decoration-line-through text-muted">
                            €{{ number_format($package->standard_price, 2) }}
                          </div>
                        @endif
                        <div class="h4 text-primary mb-1">
                          €{{ number_format($package->offer_price, 2) }}
                        </div>
                        @if($package->discount_percentage > 0)
                          <span class="badge bg-success">-{{ $package->discount_percentage }}% Rabatt</span>
                        @endif
                      </div>
                      
                      @if($package->description)
                        <p class="text-muted small mb-3">{{ $package->description }}</p>
                      @endif
                      
                      <div class="d-grid">
                        <form method="POST" action="{{ route('vendor.credits.purchase', $package) }}">
                          @csrf
                          <button type="submit" class="btn btn-primary">
                            <i class="ti ti-shopping-cart me-1"></i>Kaufen
                          </button>
                        </form>
                      </div>
                      
                      <small class="text-muted mt-2 d-block">
                        {{ number_format($package->price_per_credit, 3) }}€ pro Credit
                      </small>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-4">
              <i class="ti ti-package-off text-muted mb-2" style="font-size: 3rem;"></i>
              <h6 class="text-muted">Keine Credit-Pakete verfügbar</h6>
              <p class="text-muted">Aktuell sind keine Credit-Pakete zum Kauf verfügbar.</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Active Promotions -->
  @if($activePromotions->count() > 0)
  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h6 class="card-title mb-0">
            <i class="ti ti-trending-up me-2"></i>Aktive Hervorhebungen
          </h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Artikel</th>
                  <th>Kategorie</th>
                  <th>Typ</th>
                  <th>Credits</th>
                  <th>Läuft ab</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($activePromotions as $promotion)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        @if($promotion->rental && $promotion->rental->images->count() > 0)
                          <img src="{{ asset('storage/' . $promotion->rental->images->first()->image_path) }}" 
                               alt="{{ $promotion->rental->title }}" 
                               class="rounded me-2" 
                               style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                          <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial bg-label-secondary rounded">
                              <i class="ti ti-package"></i>
                            </span>
                          </div>
                        @endif
                        <div>
                          @if($promotion->rental)
                            <h6 class="mb-0">{{ Str::limit($promotion->rental->title, 30) }}</h6>
                            <small class="text-muted">ID: {{ $promotion->rental->id }}</small>
                          @else
                            <span class="text-muted">Artikel nicht gefunden</span>
                          @endif
                        </div>
                      </div>
                    </td>
                    <td>
                      @if($promotion->category)
                        <span class="badge bg-label-info">{{ $promotion->category->name }}</span>
                      @else
                        <span class="text-muted">—</span>
                      @endif
                    </td>
                    <td>
                      @switch($promotion->promotion_type)
                        @case('premium')
                          <span class="badge bg-warning">Premium</span>
                          @break
                        @case('featured')
                          <span class="badge bg-primary">Featured</span>
                          @break
                        @case('highlighted')
                          <span class="badge bg-info">Highlighted</span>
                          @break
                        @default
                          <span class="badge bg-secondary">{{ $promotion->promotion_type }}</span>
                      @endswitch
                    </td>
                    <td>
                      <span class="text-primary fw-semibold">{{ $promotion->credits_spent }}</span>
                    </td>
                    <td>
                      <div>
                        <strong>{{ $promotion->expires_at->format('d.m.Y H:i') }}</strong>
                        @if($promotion->getRemainingDays() > 0)
                          <br><small class="text-success">{{ $promotion->getRemainingDays() }} Tage</small>
                        @else
                          <br><small class="text-warning">{{ $promotion->getRemainingHours() }} Stunden</small>
                        @endif
                      </div>
                    </td>
                    <td>
                      @if($promotion->isCurrentlyActive())
                        <span class="badge bg-success">Aktiv</span>
                      @else
                        <span class="badge bg-secondary">Abgelaufen</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- Recent Purchases -->
  @if($purchaseHistory->count() > 0)
  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h6 class="card-title mb-0">
            <i class="ti ti-history me-2"></i>Letzte Käufe
          </h6>
          <a href="{{ route('vendor.credits.history') }}" class="btn btn-sm btn-outline-primary">
            Alle anzeigen
          </a>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table">
              <thead class="table-light">
                <tr>
                  <th>Datum</th>
                  <th>Paket</th>
                  <th>Credits</th>
                  <th>Betrag</th>
                  <th>Status</th>
                  <th>Verbraucht</th>
                </tr>
              </thead>
              <tbody>
                @foreach($purchaseHistory as $credit)
                  <tr>
                    <td>{{ $credit->purchased_at ? $credit->purchased_at->format('d.m.Y H:i') : '—' }}</td>
                    <td>
                      @if($credit->creditPackage)
                        <strong>{{ $credit->creditPackage->name }}</strong>
                      @else
                        <span class="text-muted">Paket gelöscht</span>
                      @endif
                    </td>
                    <td>{{ number_format($credit->credits_purchased) }}</td>
                    <td>€{{ number_format($credit->amount_paid, 2) }}</td>
                    <td>
                      @switch($credit->payment_status)
                        @case('completed')
                          <span class="badge bg-success">Abgeschlossen</span>
                          @break
                        @case('pending')
                          <span class="badge bg-warning">Ausstehend</span>
                          @break
                        @case('failed')
                          <span class="badge bg-danger">Fehlgeschlagen</span>
                          @break
                        @case('refunded')
                          <span class="badge bg-info">Rückerstattet</span>
                          @break
                        @default
                          <span class="badge bg-secondary">{{ $credit->payment_status }}</span>
                      @endswitch
                    </td>
                    <td>
                      <div class="d-flex align-items-center">
                        <div class="progress me-2" style="width: 60px; height: 6px;">
                          <div class="progress-bar" style="width: {{ $credit->usage_percentage }}%"></div>
                        </div>
                        <small>{{ $credit->used_credits }}/{{ $credit->credits_purchased }}</small>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- How it Works -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card bg-label-primary">
        <div class="card-body">
          <div class="d-flex align-items-start">
            <div class="avatar avatar-lg me-3">
              <span class="avatar-initial bg-primary rounded">
                <i class="ti ti-info-circle"></i>
              </span>
            </div>
            <div>
              <h6 class="mb-2">So funktioniert es</h6>
              <div class="row">
                <div class="col-md-3">
                  <div class="text-center mb-3">
                    <div class="avatar avatar-md mx-auto mb-2">
                      <span class="avatar-initial bg-label-primary rounded">
                        <i class="ti ti-shopping-cart"></i>
                      </span>
                    </div>
                    <h6 class="mb-1">1. Credits kaufen</h6>
                    <small class="text-muted">Wählen Sie ein Credit-Paket und schließen Sie den Kauf ab</small>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="text-center mb-3">
                    <div class="avatar avatar-md mx-auto mb-2">
                      <span class="avatar-initial bg-label-primary rounded">
                        <i class="ti ti-target"></i>
                      </span>
                    </div>
                    <h6 class="mb-1">2. Artikel auswählen</h6>
                    <small class="text-muted">Wählen Sie die Artikel, die Sie hervorheben möchten</small>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="text-center mb-3">
                    <div class="avatar avatar-md mx-auto mb-2">
                      <span class="avatar-initial bg-label-primary rounded">
                        <i class="ti ti-trending-up"></i>
                      </span>
                    </div>
                    <h6 class="mb-1">3. Hervorheben</h6>
                    <small class="text-muted">Ihre Artikel erscheinen ganz oben in den Suchergebnissen</small>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="text-center mb-3">
                    <div class="avatar avatar-md mx-auto mb-2">
                      <span class="avatar-initial bg-label-primary rounded">
                        <i class="ti ti-chart-line"></i>
                      </span>
                    </div>
                    <h6 class="mb-1">4. Mehr Anfragen</h6>
                    <small class="text-muted">Erhalten Sie mehr Aufmerksamkeit und Buchungsanfragen</small>
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

<style>
.card-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  z-index: 10;
}
</style>
@endsection 