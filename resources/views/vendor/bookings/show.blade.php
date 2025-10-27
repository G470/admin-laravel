@php use Illuminate\Support\Str; @endphp

@extends('layouts/contentNavbarLayout')

@section('title', 'Anfragedetails')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('vendor-dashboard') }}">Dashboard</a></li>
            <span class="text-muted fw-light">Anfragen /</span> Anfragedetails #{{ $booking->id }}
        </ol>
    </nav>

    <div class="row">
        <!-- Booking Details Card -->
        <div class="col-xl-8 col-lg-7 col-md-7 order-0 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Anfragedetails</h5>
                        <small class="text-muted">Anfrage #{{ $booking->id }} vom {{ $booking->created_at->format('d.m.Y H:i') }}</small>
                    </div>
                    <div>
                        @switch($booking->status)
                            @case('pending')
                                <span class="badge bg-warning fs-6">Ausstehend</span>
                                @break
                            @case('confirmed')
                                <span class="badge bg-success fs-6">Bestätigt</span>
                                @break
                            @case('cancelled')
                                <span class="badge bg-danger fs-6">Abgebrochen</span>
                                @break
                            @case('completed')
                                <span class="badge bg-info fs-6">Abgeschlossen</span>
                                @break
                            @default
                                <span class="badge bg-secondary fs-6">{{ ucfirst($booking->status) }}</span>
                        @endswitch
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">Anfrage-ID</h6>
                            <p class="mb-3">#{{ $booking->id }}</p>

                            <h6 class="text-muted mb-1">Anfragedatum</h6>
                            <p class="mb-3">{{ $booking->created_at->format('d.m.Y H:i') }}</p>

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
                            <h6 class="text-muted mb-1">Kunde</h6>
                            <p class="mb-3">
                                <strong>{{ $booking->first_name }} {{ $booking->last_name }}</strong><br>
                                <a href="mailto:{{ $booking->email }}" class="text-decoration-none">{{ $booking->email }}</a><br>
                                @if($booking->phone)
                                    <a href="tel:{{ $booking->phone }}" class="text-decoration-none">{{ $booking->phone }}</a>
                                @endif
                            </p>

                            @if($booking->message)
                                <h6 class="text-muted mb-1">Nachricht</h6>
                                <div class="bg-light p-3 rounded mb-3">
                                    <p class="mb-0">{{ $booking->message }}</p>
                                </div>
                            @endif

                            @if($booking->booking_token)
                                <h6 class="text-muted mb-1">Öffentlicher Link</h6>
                                <p class="mb-3">
                                    <a href="{{ route('booking.token', $booking->booking_token) }}" target="_blank" class="text-decoration-none">
                                        <i class="ti ti-external-link me-1"></i>
                                        Anfrage öffentlich anzeigen
                                    </a>
                                </p>
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

    <!-- Rental Details Card -->
    @if($booking->rental)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Artikel Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        @if($booking->rental->images && $booking->rental->images->count() > 0)
                            <img src="{{ asset('storage/' . $booking->rental->images->first()->image_path) }}" 
                                 alt="{{ $booking->rental->title }}" 
                                 class="img-thumbnail"
                                 style="width: 100%; max-width: 120px;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                 style="width: 120px; height: 120px;">
                                <i class="ti ti-photo text-muted ti-xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-10">
                        <h5>
                            <a href="{{ route('vendor.rentals.index', $booking->rental->id) }}" class="text-decoration-none">
                                {{ $booking->rental->title }}
                            </a>
                        </h5>
                        @if($booking->rental->description)
                            <p class="text-muted">{{ $Str::limit($booking->rental->description, 200) }}</p>
                        @endif
                        <div class="row">
                            <div class="col-md-3">
                                <small class="text-muted">Kategorie:</small><br>
                                <strong>{{ $booking->rental->category->name ?? 'Nicht zugeordnet' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Standort:</small><br>
                                <strong>{{ $booking->rental->location->city ?? 'Nicht angegeben' }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Preis pro Tag:</small><br>
                                <strong>{{ $booking->rental->price_per_day }}€</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Status:</small><br>
                                <span class="badge bg-{{ $booking->rental->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($booking->rental->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
// Auto-scroll to bottom of messages when new message arrives
document.addEventListener('livewire:load', function () {
    Livewire.on('messageAdded', () => {
        const messagesContainer = document.querySelector('.messages-container');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    });
});

// Scroll to bottom on page load
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.querySelector('.messages-container');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});
</script>

<style>
/* Initial message styling */
.initial-message .message-bubble {
    position: relative;
}

.initial-message .message-bubble::before {
    content: '';
    position: absolute;
    top: -5px;
    left: -5px;
    right: -5px;
    bottom: -5px;
    background: linear-gradient(45deg, #007bff, #0056b3);
    border-radius: 1.2rem;
    z-index: -1;
    opacity: 0.1;
}

.initial-message {
    background: rgba(0, 123, 255, 0.05);
    border-left: 3px solid #007bff;
    margin: 0 -15px 15px -15px;
    padding: 15px !important;
}
</style>
@endpush
