@php use Illuminate\Support\Str; @endphp

@extends('layouts/contentNavbarLayoutBackend')

@section('title', 'Anfrage bestätigen')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
            <span class="text-muted fw-light">Anfragen /</span> Anfrage bestätigen #{{ $booking->id }}
        </ol>
    </nav>

    <div class="row">
        <!-- Booking Confirmation Card -->
        <div class="col-xl-8 col-lg-7 col-md-7 order-0 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Anfrage bestätigen</h5>
                        <p class="text-muted mb-0">Bitte überprüfen Sie die Anfragedetails vor der Bestätigung</p>
                    </div>
                </div>
                <div class="card-body">
                    @if($booking->status !== 'pending')
                        <div class="alert alert-warning">
                            <i class="ti ti-alert-triangle me-2"></i>
                            Diese Anfrage kann nicht bestätigt werden. Status: {{ ucfirst($booking->status) }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Anfrage-ID</h6>
                            <p class="mb-3">#{{ $booking->id }}</p>

                            <h6 class="text-muted mb-1">Kunde</h6>
                            <p class="mb-3">
                                <strong>{{ $booking->first_name }} {{ $booking->last_name }}</strong><br>
                                <a href="mailto:{{ $booking->email }}" class="text-decoration-none">{{ $booking->email }}</a><br>
                                @if($booking->phone)
                                    <a href="tel:{{ $booking->phone }}" class="text-decoration-none">{{ $booking->phone }}</a>
                                @endif
                            </p>

                            <h6 class="text-muted mb-1">Zeitraum</h6>
                            <p class="mb-3">
                                <strong>{{ \Carbon\Carbon::parse($booking->start_date)->format('d.m.Y') }}</strong>
                                bis
                                <strong>{{ \Carbon\Carbon::parse($booking->end_date)->format('d.m.Y') }}</strong>
                                <br>
                                <small class="text-primary">({{ $booking->duration }} Tage)</small>
                            </p>

                            <h6 class="text-muted mb-1">Gesamtbetrag</h6>
                            <p class="mb-3">
                                <strong class="fs-5 text-primary">{{ number_format($booking->total_amount, 2) }}€</strong>
                                @if($booking->price_per_day)
                                    <br>
                                    <small class="text-muted">{{ $booking->price_per_day }}€ pro Tag</small>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6">
                            @if($booking->message)
                                <h6 class="text-muted mb-1">Nachricht vom Kunden</h6>
                                <div class="bg-light p-3 rounded mb-3">
                                    <p class="mb-0">{{ $booking->message }}</p>
                                </div>
                            @endif

                            <h6 class="text-muted mb-1">Angefragter Artikel</h6>
                            @if($booking->rental)
                                <div class="d-flex align-items-start mb-3">
                                    @if($booking->rental->images->count() > 0)
                                        <img src="{{ asset('storage/' . $booking->rental->images->first()->image_path) }}" 
                                             alt="{{ $booking->rental->title }}" 
                                             class="rounded me-3" 
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 80px; min-width: 80px;">
                                            <i class="ti ti-photo text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-1">{{ $booking->rental->title }}</h6>
                                        <p class="text-muted mb-0">{{ Str::limit($booking->rental->description, 100) }}</p>
                                        <small class="text-primary">{{ $booking->rental->price_per_day }}€/Tag</small>
                                    </div>
                                </div>
                            @else
                                <p class="text-muted">Artikel nicht mehr verfügbar</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Aktionen</h5>
                </div>
                <div class="card-body">
                    @if($booking->status === 'pending')
                        <div class="d-grid gap-2">
                            <form method="POST" action="{{ route('vendor.bookings.confirm', $booking->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Sind Sie sicher, dass Sie diese Anfrage bestätigen möchten?')">
                                    <i class="ti ti-check me-1"></i>
                                    Anfrage bestätigen
                                </button>
                            </form>

                            <form method="POST" action="{{ route('vendor.bookings.reject', $booking->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Sind Sie sicher, dass Sie diese Anfrage ablehnen möchten?')">
                                    <i class="ti ti-x me-1"></i>
                                    Anfrage ablehnen
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle me-2"></i>
                            Diese Anfrage kann nicht mehr bearbeitet werden.
                        </div>
                    @endif

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="{{ route('vendor.bookings.show', $booking->id) }}" class="btn btn-outline-primary">
                            <i class="ti ti-eye me-1"></i>
                            Details anzeigen
                        </a>
                        <a href="{{ route('vendor.bookings.index') }}" class="btn btn-outline-secondary">
                            <i class="ti ti-arrow-left me-1"></i>
                            Zurück zur Übersicht
                        </a>
                    </div>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Anfrageverlauf</h5>
                </div>
                <div class="card-body">
                    <ul class="timeline">
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-primary"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="timeline-title">Anfrage erstellt</h6>
                                    <small class="text-muted">{{ $booking->created_at->format('d.m.Y H:i') }}</small>
                                </div>
                                <p class="mb-0">Anfrage von {{ $booking->first_name }} {{ $booking->last_name }} eingegangen</p>
                            </div>
                        </li>

                        @if($booking->status !== 'pending')
                            <li class="timeline-item timeline-item-transparent">
                                <span class="timeline-point 
                                    @if($booking->status === 'confirmed') timeline-point-success
                                    @elseif($booking->status === 'cancelled') timeline-point-danger
                                    @else timeline-point-info @endif"></span>
                                <div class="timeline-event">
                                    <div class="timeline-header mb-1">
                                        <h6 class="timeline-title">
                                            @switch($booking->status)
                                                @case('confirmed')
                                                    Anfrage bestätigt
                                                    @break
                                                @case('cancelled')
                                                    Anfrage abgebrochen
                                                    @break
                                                @case('completed')
                                                    Anfrage abgeschlossen
                                                    @break
                                                @default
                                                    Status geändert
                                            @endswitch
                                        </h6>
                                        <small class="text-muted">{{ $booking->updated_at->format('d.m.Y H:i') }}</small>
                                    </div>
                                    <p class="mb-0">Status wurde auf "{{ ucfirst($booking->status) }}" geändert</p>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
