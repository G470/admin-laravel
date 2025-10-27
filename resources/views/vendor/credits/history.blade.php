@extends('layouts/contentNavbarLayout')

@section('title', 'Credit-Verlauf')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <!-- Header -->
  <div class="row">
    <div class="col-12">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="{{ route('vendor.credits.index') }}">Credits</a>
          </li>
          <li class="breadcrumb-item active">Verlauf</li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <h5 class="card-title mb-0">
              <i class="ti ti-history text-primary me-2"></i>Credit-Verlauf
            </h5>
            <small class="text-muted">Übersicht über alle Ihre Credit-Käufe und Ausgaben</small>
          </div>
          <div class="d-flex align-items-center">
            <div class="me-3 text-end">
              <small class="text-muted d-block">Aktuelles Guthaben</small>
              <h5 class="mb-0 text-primary">
                <i class="ti ti-coin me-1"></i>{{ number_format(\App\Models\VendorCredit::getVendorBalance(auth()->id())) }} Credits
              </h5>
            </div>
            <a href="{{ route('vendor.credits.index') }}" class="btn btn-primary">
              <i class="ti ti-plus me-1"></i>Credits kaufen
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row mt-4">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body text-center">
          <div class="avatar avatar-md mx-auto mb-3">
            <span class="avatar-initial bg-label-primary rounded">
              <i class="ti ti-currency-euro"></i>
            </span>
          </div>
          <h5 class="mb-1">€{{ number_format($totalSpent, 2) }}</h5>
          <small class="text-muted">Gesamtausgaben</small>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body text-center">
          <div class="avatar avatar-md mx-auto mb-3">
            <span class="avatar-initial bg-label-success rounded">
              <i class="ti ti-coins"></i>
            </span>
          </div>
          <h5 class="mb-1">{{ number_format($totalCreditsUsed) }}</h5>
          <small class="text-muted">Credits verwendet</small>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body text-center">
          <div class="avatar avatar-md mx-auto mb-3">
            <span class="avatar-initial bg-label-info rounded">
              <i class="ti ti-shopping-cart"></i>
            </span>
          </div>
          <h5 class="mb-1">{{ $credits->total() }}</h5>
          <small class="text-muted">Gesamte Käufe</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Purchase History -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header">
          <h6 class="card-title mb-0">
            <i class="ti ti-list me-2"></i>Kaufhistorie
          </h6>
        </div>
        <div class="card-body">
          @if($credits->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>Bestellung</th>
                    <th>Datum</th>
                    <th>Paket</th>
                    <th>Credits</th>
                    <th>Betrag</th>
                    <th>Status</th>
                    <th>Verwendung</th>
                    <th>Aktionen</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($credits as $credit)
                    <tr>
                      <td>
                        <div>
                          <strong>#{{ $credit->id }}</strong>
                          @if($credit->payment_reference)
                            <br><small class="text-muted">{{$credit->payment_reference }}</small>
                          @endif
                        </div>
                      </td>
                      <td>
                        <div>
                          <strong>{{ $credit->purchased_at ? $credit->purchased_at->format('d.m.Y') : '—' }}</strong>
                          @if($credit->purchased_at)
                            <br><small class="text-muted">{{ $credit->purchased_at->format('H:i') }}</small>
                          @endif
                        </div>
                      </td>
                      <td>
                        @if($credit->creditPackage)
                          <div>
                            <strong>{{ $credit->creditPackage->name }}</strong>
                            @if($credit->creditPackage->description)
                              <br><small class="text-muted">{{ $credit->creditPackage->description }}</small>
                            @endif
                          </div>
                        @else
                          <span class="text-muted">Paket gelöscht</span>
                        @endif
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <i class="ti ti-coin text-warning me-1"></i>
                          <strong>{{ number_format($credit->credits_purchased) }}</strong>
                        </div>
                      </td>
                      <td>
                        <strong>€{{ number_format($credit->amount_paid, 2) }}</strong>
                        @if($credit->creditPackage && $credit->creditPackage->discount_percentage > 0)
                          <br><small class="text-success">-{{ $credit->creditPackage->discount_percentage }}% Rabatt</small>
                        @endif
                      </td>
                      <td>
                        @switch($credit->payment_status)
                          @case('completed')
                            <span class="badge bg-success">
                              <i class="ti ti-check me-1"></i>Abgeschlossen
                            </span>
                            @break
                          @case('pending')
                            <span class="badge bg-warning">
                              <i class="ti ti-clock me-1"></i>Ausstehend
                            </span>
                            @break
                          @case('failed')
                            <span class="badge bg-danger">
                              <i class="ti ti-x me-1"></i>Fehlgeschlagen
                            </span>
                            @break
                          @case('refunded')
                            <span class="badge bg-info">
                              <i class="ti ti-refresh me-1"></i>Rückerstattet
                            </span>
                            @break
                          @default
                            <span class="badge bg-secondary">{{ ucfirst($credit->payment_status) }}</span>
                        @endswitch
                      </td>
                      <td>
                        @if($credit->payment_status === 'completed')
                          <div class="d-flex align-items-center">
                            <div class="progress me-2" style="width: 80px; height: 8px;">
                              <div class="progress-bar 
                                {{ $credit->usage_percentage >= 100 ? 'bg-danger' : ($credit->usage_percentage >= 75 ? 'bg-warning' : 'bg-success') }}" 
                                style="width: {{ min($credit->usage_percentage, 100) }}%">
                              </div>
                            </div>
                            <small class="fw-semibold">{{ $credit->used_credits }}/{{ $credit->credits_purchased }}</small>
                          </div>
                          <small class="text-muted">
                            Verbleibend: 
                            <span class="text-primary fw-semibold">{{ $credit->credits_remaining }}</span>
                          </small>
                        @else
                          <span class="text-muted">—</span>
                        @endif
                      </td>
                      <td>
                        <div class="dropdown">
                          <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                  data-bs-toggle="dropdown">
                            <i class="ti ti-dots-vertical"></i>
                          </button>
                          <ul class="dropdown-menu">
                            @if($credit->payment_status === 'completed')
                              <li>
                                <a class="dropdown-item" href="#" onclick="viewCreditDetails({{ $credit }})">
                                  <i class="ti ti-eye me-2"></i>Details anzeigen
                                </a>
                              </li>
                              <li>
                                <a class="dropdown-item" href="#" onclick="downloadInvoice({{ $credit->id }})">
                                  <i class="ti ti-download me-2"></i>Rechnung laden
                                </a>
                              </li>
                            @elseif($credit->payment_status === 'pending')
                              <li>
                                <a class="dropdown-item" href="{{ route('vendor.credits.payment', $credit) }}">
                                  <i class="ti ti-credit-card me-2"></i>Zahlung abschließen
                                </a>
                              </li>
                            @elseif($credit->payment_status === 'failed')
                              <li>
                                <a class="dropdown-item" href="#" onclick="retryPayment({{ $credit->id }})">
                                  <i class="ti ti-refresh me-2"></i>Erneut versuchen
                                </a>
                              </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                              <a class="dropdown-item text-muted" href="mailto:support@inlando.com?subject=Credit Kauf #{{ $credit->id }}">
                                <i class="ti ti-help me-2"></i>Support kontaktieren
                              </a>
                            </li>
                          </ul>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            @if($credits->hasPages())
              <div class="d-flex justify-content-center mt-4">
                {{ $credits->links() }}
              </div>
            @endif
          @else
            <div class="text-center py-5">
              <i class="ti ti-shopping-cart-off text-muted mb-3" style="font-size: 3rem;"></i>
              <h6 class="text-muted mb-2">Noch keine Credit-Käufe</h6>
              <p class="text-muted mb-4">Sie haben noch keine Credits gekauft. Starten Sie jetzt und heben Sie Ihre Artikel hervor!</p>
              <a href="{{ route('vendor.credits.index') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i>Erstes Credit-Paket kaufen
              </a>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Credit Details Modal -->
<div class="modal fade" id="creditDetailsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="ti ti-info-circle me-2"></i>Credit-Details
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="creditDetailsContent">
          <!-- Content will be populated by JavaScript -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Schließen</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function viewCreditDetails(credit) {
    const modal = new bootstrap.Modal(document.getElementById('creditDetailsModal'));
    const content = document.getElementById('creditDetailsContent');
    
    content.innerHTML = `
        <div class="row">
            <div class="col-6">
                <strong>Bestellnummer:</strong><br>
                <span class="text-muted">#${credit.id}</span>
            </div>
            <div class="col-6">
                <strong>Kaufdatum:</strong><br>
                <span class="text-muted">${new Date(credit.purchased_at).toLocaleDateString('de-DE')}</span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-6">
                <strong>Paket:</strong><br>
                <span class="text-muted">${credit.credit_package?.name || 'Paket gelöscht'}</span>
            </div>
            <div class="col-6">
                <strong>Credits gekauft:</strong><br>
                <span class="text-muted">${credit.credits_purchased.toLocaleString()}</span>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-6">
                <strong>Bezahlter Betrag:</strong><br>
                <span class="text-muted">€${credit.amount_paid}</span>
            </div>
            <div class="col-6">
                <strong>Credits verbleibend:</strong><br>
                <span class="text-primary fw-bold">${credit.credits_remaining.toLocaleString()}</span>
            </div>
        </div>
        ${credit.payment_reference ? `
        <hr>
        <div>
            <strong>Transaktions-ID:</strong><br>
            <span class="text-muted">${credit.payment_reference}</span>
        </div>
        ` : ''}
    `;
    
    modal.show();
}

function downloadInvoice(creditId) {
    // Placeholder for invoice download
    alert(`Rechnung für Kauf #${creditId} wird heruntergeladen...\n\nFunktion noch nicht implementiert.`);
}

function retryPayment(creditId) {
    // Redirect to payment page
    window.location.href = `/vendor/credits/payment/${creditId}`;
}
</script>
@endpush
@endsection 