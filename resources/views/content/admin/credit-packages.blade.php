@extends('layouts/layoutMaster')

@section('title', 'Credit-Pakete Verwaltung')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">
                                <i class="ti ti-coins text-primary me-2"></i>Credit-Pakete Verwaltung
                            </h5>
                            <small class="text-muted">Erstellen und verwalten Sie Credit-Pakete für Vendor</small>
                        </div>
                        <div>
                            <a href="{{ route('admin.credit-packages.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i>Neues Paket
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="avatar avatar-md mx-auto mb-3">
                            <span class="avatar-initial bg-label-primary rounded">
                                <i class="ti ti-package"></i>
                            </span>
                        </div>
                        <h5 class="mb-1">{{ $stats['total_packages'] }}</h5>
                        <small class="text-muted">Gesamt Pakete</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="avatar avatar-md mx-auto mb-3">
                            <span class="avatar-initial bg-label-success rounded">
                                <i class="ti ti-check"></i>
                            </span>
                        </div>
                        <h5 class="mb-1">{{ $stats['active_packages'] }}</h5>
                        <small class="text-muted">Aktive Pakete</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="avatar avatar-md mx-auto mb-3">
                            <span class="avatar-initial bg-label-info rounded">
                                <i class="ti ti-currency-euro"></i>
                            </span>
                        </div>
                        <h5 class="mb-1">€{{ number_format($stats['total_revenue'], 2) }}</h5>
                        <small class="text-muted">Gesamtumsatz</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="avatar avatar-md mx-auto mb-3">
                            <span class="avatar-initial bg-label-warning rounded">
                                <i class="ti ti-shopping-cart"></i>
                            </span>
                        </div>
                        <h5 class="mb-1">{{ $stats['total_sales'] }}</h5>
                        <small class="text-muted">Verkäufe</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Packages Table -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="ti ti-list me-2"></i>Credit-Pakete
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Reihenfolge</th>
                                    <th>Paket</th>
                                    <th>Credits</th>
                                    <th>Preise</th>
                                    <th>Rabatt</th>
                                    <th>Status</th>
                                    <th>Verkäufe</th>
                                    <th>Umsatz</th>
                                    <th class="text-end">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($packages as $package)
                                    <tr>
                                        <td>
                                            <span class="badge bg-label-secondary">{{ $package->sort_order }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-0">{{ $package->name }}</h6>
                                                @if($package->description)
                                                    <small class="text-muted">{{ $package->description }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-coin text-warning me-1"></i>
                                                <strong>{{ number_format($package->credits_amount) }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                @if($package->is_discounted)
                                                    <span
                                                        class="text-decoration-line-through text-muted">€{{ number_format($package->standard_price, 2) }}</span><br>
                                                @endif
                                                <strong
                                                    class="text-primary">€{{ number_format($package->offer_price, 2) }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            @if($package->discount_percentage > 0)
                                                <span class="badge bg-label-success">-{{ $package->discount_percentage }}%</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-outline-{{ $package->is_active ? 'success' : 'secondary' }}"
                                                onclick="togglePackageStatus({{ $package->id }})">
                                                @if($package->is_active)
                                                    <i class="ti ti-check me-1"></i>Aktiv
                                                @else
                                                    <i class="ti ti-x me-1"></i>Inaktiv
                                                @endif
                                            </button>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-info">{{ $package->purchases_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <strong>€{{ number_format($package->purchases_sum_amount_paid ?? 0, 2) }}</strong>
                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                    data-bs-toggle="dropdown">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.credit-packages.edit', $package) }}">
                                                            <i class="ti ti-edit me-2"></i>Bearbeiten
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.credit-packages.duplicate', $package) }}">
                                                            <i class="ti ti-copy me-2"></i>Duplizieren
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <form method="POST"
                                                            action="{{ route('admin.credit-packages.destroy', $package) }}"
                                                            onsubmit="return confirm('Sind Sie sicher, dass Sie dieses Paket löschen möchten?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="ti ti-trash me-2"></i>Löschen
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="ti ti-package-off text-muted mb-2" style="font-size: 2rem;"></i>
                                                <span class="text-muted">Keine Credit-Pakete vorhanden</span>
                                                <a href="{{ route('admin.credit-packages.create') }}"
                                                    class="btn btn-primary btn-sm mt-2">
                                                    <i class="ti ti-plus me-1"></i>Erstes Paket erstellen
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function togglePackageStatus(packageId) {
                fetch(`/admin/credit-packages/${packageId}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Fehler beim Ändern des Status');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Netzwerk-Fehler');
                    });
            }
        </script>
    @endpush
@endsection