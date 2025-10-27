@php use Illuminate\Support\Str; @endphp

@extends('layouts/contentNavbarLayout')

@section('title', 'Vendor Dashboard')

@section('content')
    <div class="row">
        <div class="col-lg-8 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Willkommen {{ auth()->user()->name }}! 🎉</h5>
                            <p class="mb-4">
                                Sie haben <span class="fw-bold">{{ $stats['pending_bookings'] }}</span> neue Anfragen.
                            </p>
                            <a href="{{ route('vendor.bookings.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-primary">Anfragen anzeigen</a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140" alt="View Badge User">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('assets/img/icons/unicons/chart-success.png') }}" alt="chart success" class="rounded">
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt3" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt3">
                                        <a class="dropdown-item" href="{{ route('vendor.rentals.index') }}">Alle anzeigen</a>
                                    </div>
                                </div>
                            </div>
                            <span>Objekte</span>
                            <h3 class="card-title mb-2">{{ $stats['total_rentals'] }}</h3>
                            <small class="text-success fw-semibold"><i class="ti ti-up"></i> Aktiv: {{ $stats['active_rentals'] }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card" class="rounded">
                                </div>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="cardOpt6" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt6">
                                        <a class="dropdown-item" href="{{ route('vendor.bookings.index') }}">Alle anzeigen</a>
                                    </div>
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Anfragen</span>
                            <h3 class="card-title text-nowrap mb-1">{{ $stats['total_bookings'] }}</h3>
                            <small class="text-warning fw-semibold">
                                <i class="ti ti-clock"></i> 
                                Ausstehend: {{ $stats['pending_bookings'] }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Total Revenue -->
        <div class="col-12 col-lg-8 order-2 order-md-3 order-lg-2 mb-4">
            <div class="card">
                <div class="row row-bordered g-0">
                    <div class="col-md-8">
                        <h5 class="card-header m-0 me-2 pb-3">Gesamtumsatz</h5>
                        <div id="totalRevenueChart" class="px-2"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="growthReportId" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        2023
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="growthReportId">
                                        <a class="dropdown-item" href="javascript:void(0);">2023</a>
                                        <a class="dropdown-item" href="javascript:void(0);">2022</a>
                                        <a class="dropdown-item" href="javascript:void(0);">2021</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="growthChart"></div>
                        <div class="text-center fw-semibold pt-3 mb-2">62% Company Growth</div>

                        <div class="d-flex px-xxl-4 px-lg-2 p-4 gap-xxl-3 gap-lg-1 gap-3 justify-content-between">
                            <div class="d-flex">
                                <div class="me-2">
                                    <span class="badge bg-label-primary p-2"><i class="ti ti-chart-pie-2 ti-xs"></i></span>
                                </div>
                                <div class="d-flex flex-column">
                                    <small>2023</small>
                                    <h6 class="mb-0">€32.5k</h6>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="me-2">
                                    <span class="badge bg-label-info p-2"><i class="ti ti-chart-pie-2 ti-xs"></i></span>
                                </div>
                                <div class="d-flex flex-column">
                                    <small>2022</small>
                                    <h6 class="mb-0">€41.2k</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Total Revenue -->

        <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
            <div class="row">
                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('assets/img/icons/unicons/paypal.png') }}" alt="paypal" class="rounded">
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Umsatz</span>
                            <h3 class="card-title mb-2">€{{ number_format($stats['this_month_earnings'] ?? 0, 2) }}</h3>
                            <small class="text-success fw-semibold"><i class="ti ti-up"></i> +28.14%</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card" class="rounded">
                                </div>
                            </div>
                            <span class="fw-semibold d-block mb-1">Standorte</span>
                            <h3 class="card-title mb-2">{{ $stats['total_locations'] ?? 0 }}</h3>
                            <small class="text-success fw-semibold"><i class="ti ti-up"></i> Aktiv: {{ $stats['active_locations'] ?? 0 }}</small>
                        </div>
                    </div>
                </div>

                <!-- Bookings with Chart -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                                <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                                    <div class="card-title">
                                        <h5 class="text-nowrap mb-2">Profile Report</h5>
                                        <span class="badge bg-label-warning rounded-pill">YEAR 2023</span>
                                    </div>
                                    <div class="mt-sm-auto">
                                        <small class="text-success text-nowrap fw-semibold"><i class="ti ti-chevron-up"></i> 68.2%</small>
                                        <h3 class="mb-0">€84.7k</h3>
                                    </div>
                                </div>
                                <div id="profileReportChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="row">
        <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Aktuelle Anfragen</h5>
                        <small class="text-muted">{{ $stats['pending_bookings'] }} ausstehend</small>
                    </div>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="orederStatistics" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                            <a class="dropdown-item" href="{{ route('vendor.bookings.index') }}">Alle anzeigen</a>
                            <a class="dropdown-item" href="{{ route('vendor.bookings.index', ['status' => 'pending']) }}">Nur ausstehende</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex flex-column align-items-center gap-1">
                            <h2 class="mb-2">{{ $stats['total_bookings'] }}</h2>
                            <span>Gesamt</span>
                        </div>
                        <div id="orderStatisticsChart"></div>
                    </div>
                    <ul class="p-0 m-0">
                        @forelse($recentBookings as $booking)
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    @if($booking->rental && $booking->rental->images && $booking->rental->images->count() > 0)
                                        <img src="{{ asset('storage/' . $booking->rental->images->first()->image_path) }}" 
                                             alt="{{ $booking->rental->title }}" 
                                             class="rounded"
                                             style="width: 38px; height: 38px; object-fit: cover;">
                                    @else
                                        <span class="avatar-initial rounded bg-label-secondary">
                                            <i class="ti ti-photo"></i>
                                        </span>
                                    @endif
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">{{ $booking->first_name }} {{ $booking->last_name }}</h6>
                                        <small class="text-muted d-block mb-1">
                                            @if($booking->rental)
                                                {{ Str::limit($booking->rental->title, 25) }}
                                            @else
                                                Artikel gelöscht
                                            @endif
                                        </small>
                                    </div>
                                    <div class="user-progress">
                                        <div class="d-flex justify-content-center">
                                            @switch($booking->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Ausstehend</span>
                                                    @break
                                                @case('confirmed')
                                                    <span class="badge bg-success">Bestätigt</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">Abgebrochen</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                            @endswitch
                                        </div>
                                        <small class="text-muted">{{ $booking->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="d-flex justify-content-center align-items-center" style="height: 100px;">
                                <div class="text-center">
                                    <i class="ti ti-calendar-x ti-lg text-muted mb-2"></i>
                                    <p class="text-muted mt-2">Keine aktuellen Anfragen</p>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!--/ Order Statistics -->

        <!-- Expense Overview -->
        <div class="col-md-6 col-lg-4 order-1 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <ul class="nav nav-pills" role="tablist">
                        <li class="nav-item">
                            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tabs-line-card-income" aria-controls="navs-tabs-line-card-income" aria-selected="true">Einkommen</button>
                        </li>
                        <li class="nav-item">
                            <button type="button" class="nav-link" role="tab">Ausgaben</button>
                        </li>
                    </ul>
                </div>
                <div class="card-body px-0">
                    <div class="tab-content p-0">
                        <div class="tab-pane fade show active" id="navs-tabs-line-card-income" role="tabpanel">
                            <div class="d-flex p-4 pt-3">
                                <div class="avatar flex-shrink-0 me-3">
                                    <img src="{{ asset('assets/img/icons/unicons/wallet.png') }}" alt="User">
                                </div>
                                <div>
                                    <small class="text-muted d-block">Gesamt Balance</small>
                                    <div class="d-flex align-items-center">
                                        <h6 class="mb-0 me-1">€{{ number_format($stats['this_month_earnings'] ?? 0, 2) }}</h6>
                                        <small class="text-success fw-semibold">
                                            <i class="ti ti-chevron-up"></i>
                                            42.9%
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div id="incomeChart"></div>
                            <div class="d-flex justify-content-center pt-4 gap-2">
                                <div class="flex-shrink-0">
                                    <div id="expensesOfWeek"></div>
                                </div>
                                <div>
                                    <p class="mb-n1 mt-1">Ausgaben dieser Woche</p>
                                    <small class="text-muted">€39 weniger als letzte Woche</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Expense Overview -->

        <!-- Transactions -->
        <div class="col-md-6 col-lg-4 order-2 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Transaktionen</h5>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="transactionID" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                            <a class="dropdown-item" href="javascript:void(0);">Letzte 28 Tage</a>
                            <a class="dropdown-item" href="javascript:void(0);">Letzter Monat</a>
                            <a class="dropdown-item" href="javascript:void(0);">Letztes Jahr</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <img src="{{ asset('assets/img/icons/unicons/paypal.png') }}" alt="User" class="rounded">
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <small class="text-muted d-block mb-1">Paypal</small>
                                    <h6 class="mb-0">Zahlung erhalten</h6>
                                </div>
                                <div class="user-progress d-flex align-items-center gap-1">
                                    <h6 class="mb-0">+€24.8</h6>
                                    <span class="text-muted">EUR</span>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <img src="{{ asset('assets/img/icons/unicons/wallet.png') }}" alt="User" class="rounded">
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <small class="text-muted d-block mb-1">Wallet</small>
                                    <h6 class="mb-0">Mac'D</h6>
                                </div>
                                <div class="user-progress d-flex align-items-center gap-1">
                                    <h6 class="mb-0">+€13.5</h6>
                                    <span class="text-muted">EUR</span>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <img src="{{ asset('assets/img/icons/unicons/chart.png') }}" alt="User" class="rounded">
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <small class="text-muted d-block mb-1">Transfer</small>
                                    <h6 class="mb-0">Refund</h6>
                                </div>
                                <div class="user-progress d-flex align-items-center gap-1">
                                    <h6 class="mb-0">+€9.87</h6>
                                    <span class="text-muted">EUR</span>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <img src="{{ asset('assets/img/icons/unicons/cc-success.png') }}" alt="User" class="rounded">
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <small class="text-muted d-block mb-1">Credit Card</small>
                                    <h6 class="mb-0">Ordered Food</h6>
                                </div>
                                <div class="user-progress d-flex align-items-center gap-1">
                                    <h6 class="mb-0">-€12.16</h6>
                                    <span class="text-muted">EUR</span>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <img src="{{ asset('assets/img/icons/unicons/wallet.png') }}" alt="User" class="rounded">
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <small class="text-muted d-block mb-1">Wallet</small>
                                    <h6 class="mb-0">Starbucks</h6>
                                </div>
                                <div class="user-progress d-flex align-items-center gap-1">
                                    <h6 class="mb-0">-€74.19</h6>
                                    <span class="text-muted">EUR</span>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex">
                            <div class="avatar flex-shrink-0 me-3">
                                <img src="{{ asset('assets/img/icons/unicons/cc-warning.png') }}" alt="User" class="rounded">
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <small class="text-muted d-block mb-1">Mastercard</small>
                                    <h6 class="mb-0">Anfragen verwalten</h6>
                                </div>
                                <div class="user-progress d-flex align-items-center gap-1">
                                    <h6 class="mb-0">-€1.98</h6>
                                    <span class="text-muted">EUR</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!--/ Transactions -->
    </div>
@endsection
