@extends('layouts.layoutMaster')

@section('title', 'Admin Dashboard - Inlando')

@section('vendor-style')
@vite([
    'resources/assets/vendor/libs/apex-charts/apex-charts.css',
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css'
])
@endsection

@section('vendor-script')
@vite([
    'resources/assets/vendor/libs/apex-charts/apexcharts.js',
    'resources/assets/vendor/libs/moment/moment.js'
])
@endsection

@section('page-script')
@vite(['resources/assets/js/admin-dashboard.js'])
@endsection

@section('content')
<!-- Dashboard Header -->
<div class="row">
    <div class="col-12">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-7">
                        <h5 class="card-title text-white mb-1">Willkommen im Admin Dashboard! 👋</h5>
                        <p class="mb-4 text-white-50">
                            Hier ist ein Überblick über die wichtigsten Kennzahlen Ihrer Inlando-Plattform.
                        </p>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-light">
                            <i class="ti ti-users me-1"></i> Benutzer verwalten
                        </a>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{asset('assets/img/illustrations/man-with-laptop.png')}}"
                                height="140" alt="View Badge User">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Core Statistics Cards -->
<div class="row">
    <!-- Total Users -->
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <div class="avatar-initial bg-label-success rounded">
                            <i class="ti ti-users ti-md"></i>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                            <a class="dropdown-item" href="{{ route('admin.users.index') }}">Alle anzeigen</a>
                            <a class="dropdown-item" href="{{ route('admin.users.create') }}">Neuen Benutzer erstellen</a>
                        </div>
                    </div>
                </div>
                <span class="fw-medium d-block mb-1">Benutzer</span>
                <h3 class="card-title mb-2">{{ number_format($stats['total_users']) }}</h3>
                <small class="text-success fw-medium">
                    <i class="ti ti-arrow-up"></i> +{{ $stats['new_users_this_week'] }} diese Woche
                </small>
                @if($stats['user_growth_percentage'] >= 0)
                    <small class="text-success">(+{{ $stats['user_growth_percentage'] }}%)</small>
                @else
                    <small class="text-danger">({{ $stats['user_growth_percentage'] }}%)</small>
                @endif
            </div>
        </div>
    </div>

    <!-- Total Vendors -->
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <div class="avatar-initial bg-label-info rounded">
                            <i class="ti ti-building-store ti-md"></i>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="cardOpt4" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                            <a class="dropdown-item" href="javascript:void(0);">Alle Anbieter</a>
                            <a class="dropdown-item" href="javascript:void(0);">Aktive Anbieter</a>
                        </div>
                    </div>
                </div>
                <span class="fw-medium d-block mb-1">Anbieter</span>
                <h3 class="card-title mb-2">{{ number_format($stats['total_vendors']) }}</h3>
                <small class="text-muted">{{ $stats['active_vendors'] }} aktiv ({{ $stats['vendor_activation_rate'] }}%)</small>
            </div>
        </div>
    </div>

    <!-- Total Rentals -->
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <div class="avatar-initial bg-label-warning rounded">
                            <i class="ti ti-package ti-md"></i>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="cardOpt5" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt5">
                            <a class="dropdown-item" href="{{ route('admin.rentals.index') }}">Alle anzeigen</a>
                            <a class="dropdown-item" href="javascript:void(0);">Genehmigung erforderlich</a>
                        </div>
                    </div>
                </div>
                <span class="fw-medium d-block mb-1">Vermietungsobjekte</span>
                <h3 class="card-title mb-2">{{ number_format($stats['total_rentals']) }}</h3>
                <small class="text-warning">{{ $stats['pending_rentals'] }} ausstehend</small>
                <small class="text-success">({{ $stats['rental_approval_rate'] }}% genehmigt)</small>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-lg-3 col-md-6 col-12 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="card-title d-flex align-items-start justify-content-between">
                    <div class="avatar flex-shrink-0">
                        <div class="avatar-initial bg-label-primary rounded">
                            <i class="ti ti-currency-euro ti-md"></i>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                            <a class="dropdown-item" href="{{ route('admin.bills.index') }}">Rechnungen anzeigen</a>
                            <a class="dropdown-item" href="javascript:void(0);">Finanzberichte</a>
                        </div>
                    </div>
                </div>
                <span class="fw-medium d-block mb-1">Gesamtumsatz</span>
                <h3 class="card-title mb-2">€{{ number_format($stats['total_revenue'], 2) }}</h3>
                <small class="text-primary">€{{ number_format($stats['this_month_revenue'], 2) }} diesen Monat</small>
                @if($stats['revenue_growth_percentage'] >= 0)
                    <small class="text-success">(+{{ $stats['revenue_growth_percentage'] }}%)</small>
                @else
                    <small class="text-danger">({{ $stats['revenue_growth_percentage'] }}%)</small>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Charts and Analytics -->
<div class="row">
    <!-- Revenue Chart -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Monatliche Umsatzentwicklung</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="revenueChart" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="revenueChart">
                        <a class="dropdown-item" href="javascript:void(0);">Dieses Jahr</a>
                        <a class="dropdown-item" href="javascript:void(0);">Letztes Jahr</a>
                        <a class="dropdown-item" href="javascript:void(0);">Letzte 6 Monate</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="monthlyRevenueChart"></div>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Systemstatus</h5>
                <span class="badge bg-label-success">{{ ucfirst($systemHealth['system_status']) }}</span>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <h6 class="mb-0 me-3">Server Status</h6>
                    <span class="badge bg-label-success">Online</span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted">Speicherplatz</small>
                    <small class="text-muted">{{ $systemHealth['storage_usage_percentage'] }}% belegt</small>
                </div>
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar" role="progressbar" style="width: {{ $systemHealth['storage_usage_percentage'] }}%;" 
                         aria-valuenow="{{ $systemHealth['storage_usage_percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted">Aktive Benutzer (24h)</small>
                    <small class="text-muted">{{ $systemHealth['active_users_24h'] }}</small>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted">DB Verbindungen</small>
                    <small class="text-muted">{{ $systemHealth['db_connections'] }}</small>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <small class="text-muted">Freier Speicher</small>
                    <small class="text-muted">{{ $systemHealth['storage_free_gb'] }} GB</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Metrics -->
<div class="row">
    <!-- Top Categories -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Top Kategorien</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="topCategories" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="topCategories">
                        <a class="dropdown-item" href="{{ route('admin.categories.index') }}">Alle Kategorien</a>
                        <a class="dropdown-item" href="javascript:void(0);">Kategorie-Analyse</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach($performance['top_categories']->take(5) as $category)
                        <li class="mb-3 pb-1">
                            <div class="d-flex align-items-start">
                                <div class="badge bg-label-primary p-2 me-3 rounded">
                                    <i class="ti ti-category ti-sm"></i>
                                </div>
                                <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">{{ $category['name'] }}</h6>
                                        <small class="text-muted">{{ $category['rentals_count'] }} Objekte</small>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ round(($category['rentals_count'] / max($stats['total_rentals'], 1)) * 100, 1) }}%</h6>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Top Vendors -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Top Anbieter</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="topVendors" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="topVendors">
                        <a class="dropdown-item" href="javascript:void(0);">Alle Anbieter</a>
                        <a class="dropdown-item" href="javascript:void(0);">Anbieter-Analyse</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach($performance['top_vendors']->take(5) as $vendor)
                        <li class="mb-3 pb-1">
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <div class="avatar-initial bg-label-success rounded-circle">
                                        {{ substr($vendor['name'], 0, 1) }}
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between w-100 flex-wrap">
                                    <div class="me-2">
                                        <h6 class="mb-0">{{ $vendor['name'] }}</h6>
                                        <small class="text-muted">{{ $vendor['rentals_count'] }} Objekte</small>
                                    </div>
                                    <div>
                                        <span class="badge bg-label-primary">€{{ number_format($vendor['total_revenue'], 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Top Locations -->
    <div class="col-md-12 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Beliebte Standorte</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="topLocations" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="topLocations">
                        <a class="dropdown-item" href="{{ route('admin.locations.index') }}">Alle Standorte</a>
                        <a class="dropdown-item" href="javascript:void(0);">Standort-Analyse</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach($performance['top_locations']->take(5) as $location)
                        <li class="mb-3 pb-1">
                            <div class="d-flex align-items-start">
                                <div class="badge bg-label-info p-2 me-3 rounded">
                                    <i class="ti ti-map-pin ti-sm"></i>
                                </div>
                                <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">{{ $location['city'] }}</h6>
                                        <small class="text-muted">{{ $location['postcode'] }}</small>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $location['rentals_count'] }}</h6>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <!-- Recent Users -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Neue Benutzer</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="newUsers" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="newUsers">
                        <a class="dropdown-item" href="{{ route('admin.users.index') }}">Alle Benutzer</a>
                        <a class="dropdown-item" href="{{ route('admin.users.create') }}">Neuen Benutzer erstellen</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach($recentActivity['recent_users']->take(5) as $user)
                        <li class="mb-3 pb-1">
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <div class="avatar-initial bg-label-primary rounded-circle">
                                        {{ substr($user['name'], 0, 1) }}
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between w-100 flex-wrap">
                                    <div class="me-2">
                                        <h6 class="mb-0">{{ $user['name'] }}</h6>
                                        <small class="text-muted">{{ $user['email'] }}</small>
                                    </div>
                                    <div class="text-end">
                                        @if($user['is_vendor'])
                                            <span class="badge bg-label-success">Anbieter</span>
                                        @elseif($user['is_admin'])
                                            <span class="badge bg-label-danger">Admin</span>
                                        @else
                                            <span class="badge bg-label-primary">Kunde</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">{{ $user['created_at']->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Neueste Buchungen</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="recentBookings" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="recentBookings">
                        <a class="dropdown-item" href="javascript:void(0);">Alle Buchungen</a>
                        <a class="dropdown-item" href="javascript:void(0);">Ausstehende Buchungen</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach($recentActivity['recent_bookings']->take(5) as $booking)
                        <li class="mb-3 pb-1">
                            <div class="d-flex align-items-start">
                                <div class="avatar me-3">
                                    <div class="avatar-initial bg-label-warning rounded-circle">
                                        <i class="ti ti-calendar ti-sm"></i>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">{{ $booking['renter_name'] }}</h6>
                                        <small class="text-muted">{{ $booking['rental_name'] }}</small>
                                        <br>
                                        <small class="text-muted">{{ $booking['created_at']->diffForHumans() }}</small>
                                    </div>
                                    <div class="text-end">
                                        <h6 class="mb-0">€{{ number_format($booking['total_amount'], 2) }}</h6>
                                        @if($booking['status'] === 'completed')
                                            <span class="badge bg-label-success">Abgeschlossen</span>
                                        @elseif($booking['status'] === 'pending')
                                            <span class="badge bg-label-warning">Ausstehend</span>
                                        @else
                                            <span class="badge bg-label-secondary">{{ ucfirst($booking['status']) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Platform Health Summary -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Plattform-Übersicht</h5>
                <div class="d-flex align-items-center">
                    <span class="badge bg-label-primary me-2">{{ $performance['total_reviews'] }} Bewertungen</span>
                    <span class="badge bg-label-success">⭐ {{ $performance['average_rating'] }}/5</span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <div class="avatar-initial bg-label-success rounded">
                                    <i class="ti ti-check ti-md"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $stats['booking_completion_rate'] }}%</h5>
                                <small class="text-muted">Buchungsabschlussrate</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <div class="avatar-initial bg-label-info rounded">
                                    <i class="ti ti-users ti-md"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $stats['vendor_activation_rate'] }}%</h5>
                                <small class="text-muted">Anbieter-Aktivierungsrate</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <div class="avatar-initial bg-label-warning rounded">
                                    <i class="ti ti-package ti-md"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $stats['rental_approval_rate'] }}%</h5>
                                <small class="text-muted">Objekt-Genehmigungsrate</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <div class="avatar-initial bg-label-primary rounded">
                                    <i class="ti ti-star ti-md"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $performance['average_rating'] }}/5</h5>
                                <small class="text-muted">Durchschnittsbewertung</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Data for JavaScript -->
<script>
window.chartData = {
    monthlyRevenue: @json(array_values($analytics['monthly_revenue'])),
    monthlyLabels: ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez']
};
</script>

@endsection