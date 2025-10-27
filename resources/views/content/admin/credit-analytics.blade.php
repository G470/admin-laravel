@extends('layouts/contentNavbarLayout')

@section('title', 'Credit-Berichte')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
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
                                <i class="ti ti-chart-line text-primary me-2"></i>Credit-System Berichte
                            </h5>
                            <small class="text-muted">Umfassende Analyse der Credit-Verkäufe und Vendor-Aktivitäten</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary" onclick="exportAnalytics()">
                                <i class="ti ti-download me-1"></i>Export
                            </button>
                            <a href="{{ route('admin.credit-packages.index') }}" class="btn btn-primary">
                                <i class="ti ti-package me-1"></i>Pakete verwalten
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Statistics -->
        <div class="row mt-4">
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex flex-column">
                                <div class="card-title mb-auto">
                                    <h5 class="mb-1 text-nowrap">Gesamtumsatz</h5>
                                    <small>Credit-Verkäufe</small>
                                </div>
                                <div class="chart-statistics">
                                    <h3 class="card-title mb-1">€{{ number_format($totalRevenue, 2) }}</h3>
                                    @if($revenueGrowth >= 0)
                                        <small class="text-success text-nowrap fw-medium">
                                            <i class="ti ti-chevron-up me-1"></i>+{{ number_format($revenueGrowth, 1) }}%
                                        </small>
                                    @else
                                        <small class="text-danger text-nowrap fw-medium">
                                            <i class="ti ti-chevron-down me-1"></i>{{ number_format($revenueGrowth, 1) }}%
                                        </small>
                                    @endif
                                </div>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial bg-success rounded">
                                    <i class="ti ti-currency-euro"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex flex-column">
                                <div class="card-title mb-auto">
                                    <h5 class="mb-1 text-nowrap">Credits verkauft</h5>
                                    <small>Gesamtmenge</small>
                                </div>
                                <div class="chart-statistics">
                                    <h3 class="card-title mb-1">{{ number_format($totalCredits) }}</h3>
                                    @if($creditsGrowth >= 0)
                                        <small class="text-success text-nowrap fw-medium">
                                            <i class="ti ti-chevron-up me-1"></i>+{{ number_format($creditsGrowth, 1) }}%
                                        </small>
                                    @else
                                        <small class="text-danger text-nowrap fw-medium">
                                            <i class="ti ti-chevron-down me-1"></i>{{ number_format($creditsGrowth, 1) }}%
                                        </small>
                                    @endif
                                </div>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial bg-warning rounded">
                                    <i class="ti ti-coins"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex flex-column">
                                <div class="card-title mb-auto">
                                    <h5 class="mb-1 text-nowrap">Aktive Vendors</h5>
                                    <small>Mit Credits</small>
                                </div>
                                <div class="chart-statistics">
                                    <h3 class="card-title mb-1">{{ $activeVendors }}</h3>
                                    @if($vendorGrowth >= 0)
                                        <small class="text-success text-nowrap fw-medium">
                                            <i class="ti ti-chevron-up me-1"></i>+{{ number_format($vendorGrowth, 1) }}%
                                        </small>
                                    @else
                                        <small class="text-danger text-nowrap fw-medium">
                                            <i class="ti ti-chevron-down me-1"></i>{{ number_format($vendorGrowth, 1) }}%
                                        </small>
                                    @endif
                                </div>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial bg-info rounded">
                                    <i class="ti ti-users"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="d-flex flex-column">
                                <div class="card-title mb-auto">
                                    <h5 class="mb-1 text-nowrap">Conversion Rate</h5>
                                    <small>Käufe / Besucher</small>
                                </div>
                                <div class="chart-statistics">
                                    <h3 class="card-title mb-1">{{ number_format($conversionRate, 1) }}%</h3>
                                    @if($conversionGrowth >= 0)
                                        <small class="text-success text-nowrap fw-medium">
                                            <i class="ti ti-chevron-up me-1"></i>+{{ number_format($conversionGrowth, 1) }}%
                                        </small>
                                    @else
                                        <small class="text-danger text-nowrap fw-medium">
                                            <i class="ti ti-chevron-down me-1"></i>{{ number_format($conversionGrowth, 1) }}%
                                        </small>
                                    @endif
                                </div>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial bg-primary rounded">
                                    <i class="ti ti-trending-up"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mt-4">
            <!-- Revenue Chart -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div>
                            <h5 class="card-title mb-0">Umsatzentwicklung</h5>
                            <small class="text-muted">Monatliche Credit-Verkäufe</small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-text-secondary rounded-pill text-muted border-0 p-2 me-n1" type="button"
                                id="revenueChartOptions" data-bs-toggle="dropdown">
                                <i class="ti ti-dots-vertical ti-sm"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="revenueChartOptions">
                                <li><a class="dropdown-item" href="#" onclick="updateRevenueChart('6months')">6 Monate</a>
                                </li>
                                <li><a class="dropdown-item" href="#" onclick="updateRevenueChart('1year')">1 Jahr</a></li>
                                <li><a class="dropdown-item" href="#" onclick="updateRevenueChart('all')">Alle Daten</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="revenueChart"></div>
                    </div>
                </div>
            </div>

            <!-- Package Performance -->
            <div class="col-xl-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Beliebteste Pakete</h5>
                        <small class="text-muted">Nach Verkaufszahlen</small>
                    </div>
                    <div class="card-body">
                        <div id="packageChart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Analytics -->
        <div class="row mt-4">
            <!-- Package Performance Table -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-package me-2"></i>Package-Performance
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Package</th>
                                        <th>Credits</th>
                                        <th>Preis</th>
                                        <th>Verkauft</th>
                                        <th>Umsatz</th>
                                        <th>Popularität</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($packages as $package)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm me-3">
                                                        <span class="avatar-initial bg-label-primary rounded">
                                                            <i class="ti ti-package"></i>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $package->name }}</h6>
                                                        @if($package->description)
                                                            <small class="text-muted">{{ $package->description }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ number_format($package->credits_amount) }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <span
                                                        class="fw-medium">€{{ number_format($package->offer_price, 2) }}</span>
                                                    @if($package->discount_percentage > 0)
                                                        <br><small
                                                            class="text-success">-{{ $package->discount_percentage }}%</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ number_format($package->total_purchases) }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="fw-medium text-success">€{{ number_format($package->total_revenue, 2) }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 60px; height: 6px;">
                                                        <div class="progress-bar"
                                                            style="width: {{ min($package->popularity_score, 100) }}%">
                                                        </div>
                                                    </div>
                                                    <small>{{ number_format($package->popularity_score, 1) }}%</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($package->is_active)
                                                    <span class="badge bg-success">Aktiv</span>
                                                @else
                                                    <span class="badge bg-secondary">Inaktiv</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="ti ti-package-off text-muted mb-2" style="font-size: 2rem;"></i>
                                                <div class="text-muted">Keine Credit-Pakete vorhanden</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Vendors -->
            <div class="col-xl-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-trophy me-2"></i>Top Vendors
                        </h5>
                        <small class="text-muted">Nach Ausgaben</small>
                    </div>
                    <div class="card-body">
                        @forelse($topVendors as $index => $vendor)
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-sm me-3">
                                    <span
                                        class="avatar-initial bg-label-{{ $index === 0 ? 'warning' : ($index === 1 ? 'secondary' : 'dark') }} rounded">
                                        {{ $index + 1 }}
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">{{ $vendor->name ?? 'Vendor #' . $vendor->vendor_id }}</h6>
                                            <small class="text-muted">{{ $vendor->purchase_count }} Käufe</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-medium text-success">€{{ number_format($vendor->total_spent, 2) }}
                                            </div>
                                            <small class="text-muted">{{ number_format($vendor->total_credits) }}
                                                Credits</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="ti ti-users-off text-muted mb-2" style="font-size: 2rem;"></i>
                                <div class="text-muted">Keine Vendor-Daten verfügbar</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Performance -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-calendar-stats me-2"></i>Monatliche Performance
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Monat</th>
                                        <th>Verkäufe</th>
                                        <th>Umsatz</th>
                                        <th>Credits</th>
                                        <th>Ø Wert/Kauf</th>
                                        <th>Neue Vendors</th>
                                        <th>Wachstum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($monthlyStats as $month)
                                        <tr>
                                            <td>
                                                <div class="fw-medium">{{ $month->month_name }}</div>
                                                <small class="text-muted">{{ $month->year }}</small>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ number_format($month->total_sales) }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="fw-medium text-success">€{{ number_format($month->total_revenue, 2) }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ number_format($month->total_credits) }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-medium">€{{ number_format($month->avg_order_value, 2) }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ $month->new_vendors }}</span>
                                            </td>
                                            <td>
                                                @if($month->growth_rate >= 0)
                                                    <span class="badge bg-success">
                                                        <i
                                                            class="ti ti-trending-up me-1"></i>+{{ number_format($month->growth_rate, 1) }}%
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i
                                                            class="ti ti-trending-down me-1"></i>{{ number_format($month->growth_rate, 1) }}%
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="ti ti-calendar-off text-muted mb-2" style="font-size: 2rem;"></i>
                                                <div class="text-muted">Keine monatlichen Daten verfügbar</div>
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
    </div>

@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
    <script>
        // Revenue Chart
        let revenueChart;
        const revenueChartOptions = {
            chart: {
                type: 'area',
                height: 300,
                toolbar: {
                    show: false
                }
            },
            colors: ['#28c76f'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            series: [{
                name: 'Umsatz',
                data: @json($monthlyStats->pluck('total_revenue')->toArray())
            }],
            xaxis: {
                categories: @json($monthlyStats->pluck('month_name')->toArray()),
                labels: {
                    style: {
                        colors: '#6c757d'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return '€' + value.toLocaleString();
                    },
                    style: {
                        colors: '#6c757d'
                    }
                }
            },
            grid: {
                borderColor: '#e7eef7',
                strokeDashArray: 8
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return '€' + value.toLocaleString();
                    }
                }
            }
        };

        // Package Performance Chart
        const packageChartOptions = {
            chart: {
                type: 'donut',
                height: 300
            },
            colors: ['#28c76f', '#00bad1', '#ff9f43', '#ea5455', '#7367f0'],
            series: @json($packages->take(5)->pluck('total_purchases')->toArray()),
            labels: @json($packages->take(5)->pluck('name')->toArray()),
            legend: {
                position: 'bottom'
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return Math.round(val) + '%';
                }
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return value + ' Verkäufe';
                    }
                }
            }
        };

        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function () {
            if (document.getElementById('revenueChart')) {
                revenueChart = new ApexCharts(document.getElementById('revenueChart'), revenueChartOptions);
                revenueChart.render();
            }

            if (document.getElementById('packageChart')) {
                const packageChart = new ApexCharts(document.getElementById('packageChart'), packageChartOptions);
                packageChart.render();
            }
        });

        // Chart Functions
        function updateRevenueChart(period) {
            // Placeholder for different time periods
            console.log('Updating chart for period:', period);
            // In real implementation, make AJAX call to get data for specific period
        }

        function exportAnalytics() {
            // Placeholder for export functionality
            alert('Export-Funktionalität wird implementiert...\n\nWird CSV/Excel Export mit allen Analytics-Daten enthalten.');
        }

        // Auto-refresh every 5 minutes
        setInterval(function () {
            // In real implementation, update charts with fresh data
            console.log('Auto-refreshing analytics data...');
        }, 300000);
    </script>
@endsection