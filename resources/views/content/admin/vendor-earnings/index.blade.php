@extends('layouts/contentNavbarLayout')

@section('title', 'Vendor Earnings - Admin')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/apex-charts/apex-charts.css',
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css'
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/apex-charts/apexcharts.js',
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'
])
@endsection

@section('content')
<div class="row">
    <!-- Summary Cards -->
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">Aktive Vendors</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">{{ $vendors->where('created_at', '>=', now()->subMonth())->count() }}</h4>
                            <small class="text-success">+5%</small>
                        </div>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="ti ti-users ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">Gesamtumsatz</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">€24,580</h4>
                            <small class="text-success">+12%</small>
                        </div>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-success rounded p-2">
                            <i class="ti ti-currency-euro ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">Kommission</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">€2,458</h4>
                            <small class="text-success">+8%</small>
                        </div>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-info rounded p-2">
                            <i class="ti ti-chart-line ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="card-info">
                        <p class="card-text">Ausstehende Zahlungen</p>
                        <div class="d-flex align-items-end mb-2">
                            <h4 class="card-title mb-0 me-2">€1,240</h4>
                            <small class="text-warning">Pending</small>
                        </div>
                    </div>
                    <div class="card-icon">
                        <span class="badge bg-label-warning rounded p-2">
                            <i class="ti ti-clock ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Vendors List -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="ti ti-chart-line me-2"></i>Vendor Earnings Overview
                </h5>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary btn-sm">Export PDF</button>
                    <button type="button" class="btn btn-outline-primary btn-sm">Export Excel</button>
                </div>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Vendor</th>
                                <th>Vermietungen</th>
                                <th>Umsatz</th>
                                <th>Kommission (10%)</th>
                                <th>Letzter Umsatz</th>
                                <th>Status</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendors as $vendor)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            @if($vendor->avatar)
                                                <img src="{{ asset('storage/' . $vendor->avatar) }}" alt="Avatar" class="rounded-circle">
                                            @else
                                                <div class="avatar-initial bg-label-primary rounded-circle">
                                                    {{ substr($vendor->name, 0, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $vendor->name }}</h6>
                                            <small class="text-muted">{{ $vendor->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-label-info">{{ $vendor->rentals->count() }}</span>
                                </td>
                                <td>
                                    <strong>€{{ number_format(rand(1000, 15000), 2) }}</strong>
                                </td>
                                <td>
                                    <span class="text-success">€{{ number_format(rand(100, 1500), 2) }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $vendor->updated_at->format('d.m.Y') }}</small>
                                </td>
                                <td>
                                    @if($vendor->is_vendor)
                                        <span class="badge bg-success">Aktiv</span>
                                    @else
                                        <span class="badge bg-secondary">Inaktiv</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.vendor-earnings.show', $vendor->id) }}" 
                                           class="btn btn-outline-primary" title="Details anzeigen">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-info" 
                                                onclick="generateReport({{ $vendor->id }})" title="Report generieren">
                                            <i class="ti ti-file-text"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success" 
                                                onclick="processPayment({{ $vendor->id }})" title="Zahlung verarbeiten">
                                            <i class="ti ti-credit-card"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="ti ti-users-off fs-3 mb-2 d-block text-muted"></i>
                                    Keine Vendors gefunden
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($vendors->hasPages())
                    <div class="mt-4">
                        {{ $vendors->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function generateReport(vendorId) {
    if (confirm('Report für diesen Vendor generieren?')) {
        window.open(`/admin/vendor-earnings/${vendorId}/report`, '_blank');
    }
}

function processPayment(vendorId) {
    if (confirm('Zahlung für diesen Vendor verarbeiten?')) {
        // Implement payment processing logic
        alert('Zahlungsverarbeitung würde hier implementiert werden.');
    }
}

// Initialize DataTable if needed
document.addEventListener('DOMContentLoaded', function() {
    // Add any chart initialization here if needed
});
</script>
@endsection
