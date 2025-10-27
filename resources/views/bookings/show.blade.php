@extends('layouts/contentNavbarLayout')

@section('title', 'Buchungsdetails')

@section('styles')
<style>
    .booking-details-wrapper {
        min-height: 80vh;
        padding: 2rem 0;
    }
    
    .booking-card {
        border: 1px solid var(--bs-border-color);
        border-radius: 0.75rem;
        padding: 2rem;
        background: white;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 2rem;
    }
    
    .status-badge {
        border-radius: 1rem;
        padding: 0.5rem 1rem;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .rental-summary {
        background: var(--bs-light);
        border-radius: 0.5rem;
        padding: 1.5rem;
        border: 1px solid var(--bs-border-color);
    }
    
    .rental-image {
        height: 120px;
        background: var(--bs-gray-200);
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--bs-gray-600);
    }
    
    .timeline {
        position: relative;
        padding-left: 3rem;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 1rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--bs-border-color);
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -2rem;
        top: 0.5rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background: var(--bs-primary);
        border: 3px solid white;
        box-shadow: 0 0 0 2px var(--bs-border-color);
    }
    
    .timeline-item.completed::before {
        background: var(--bs-success);
    }
    
    .timeline-item.cancelled::before {
        background: var(--bs-danger);
    }
</style>
@endsection

@section('content')
<div class="container-xxl">
    <div class="booking-details-wrapper">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                @auth
                <!-- if is vendor, show vendor bookings -->
                @if(Auth::user()->is_vendor)
                    <li class="breadcrumb-item"><a href="{{ route('vendor.bookings.index') }}">Meine Buchungen</a></li>
                @else
                    <li class="breadcrumb-item"><a href="{{ route('user.bookings.index') }}">Meine Buchungen</a></li>
                @endif
                @endauth
                <li class="breadcrumb-item active">Buchungsdetails</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Left Sidebar - Booking Details -->
            <div class="col-lg-4">
                <div class="booking-card">
                    <!-- Booking Header -->
                    <div class="card-header bg-primary text-white mb-3 rounded p-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="ti ti-calendar-event ti-lg"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-white">Anfrage #{{ substr($booking->booking_token ?? $booking->id, -8) }}</h6>
                                <small class="text-white-50">Erstellt am {{ $booking->created_at->format('d.m.Y') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">Status:</span>
                            <span class="badge bg-{{ 
                                $booking->status === 'pending' ? 'warning' : 
                                ($booking->status === 'confirmed' ? 'success' : 
                                ($booking->status === 'completed' ? 'primary' : 'danger')) 
                            }}">
                                @switch($booking->status)
                                    @case('pending') Ausstehend @break
                                    @case('confirmed') Bestätigt @break
                                    @case('completed') Abgeschlossen @break
                                    @case('cancelled') Abgelehnt @break
                                    @default {{ ucfirst($booking->status) }}
                                @endswitch
                            </span>
                        </div>
                        @if($booking->status === 'cancelled')
                            <div class="alert alert-danger py-2 px-3 mb-0">
                                <small><i class="ti ti-info-circle me-1"></i>Das Angebot des Vermieters wurde abgelehnt.</small>
                            </div>
                        @elseif($booking->status === 'confirmed')
                            <div class="alert alert-success py-2 px-3 mb-0">
                                <small><i class="ti ti-check me-1"></i>Ihre Buchung wurde bestätigt!</small>
                            </div>
                        @endif
                    </div>

                    <!-- Customer Details -->
                    <div class="mb-3">
                        <h6 class="mb-2">Ihre Details</h6>
                        <div class="border rounded p-3  bg-white p-3 rounded">
                            <div class="mb-2">
                                <small class="text-muted">Name</small>
                                <div class="fw-semibold">{{ $booking->guest_name ?? $booking->renter->name ?? 'Unbekannt' }}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">E-Mail</small>
                                <div>{{ $booking->guest_email ?? $booking->renter->email ?? 'Nicht angegeben' }}</div>
                            </div>
                            @if($booking->guest_phone)
                                <div>
                                    <small class="text-muted">Telefon</small>
                                    <div>{{ $booking->guest_phone }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Rental Product -->
                    <div class="mb-3">
                        <h6 class="mb-2">Angefragtes Produkt</h6>
                        <div class="border rounded p-3  bg-white p-3 rounded">
                            <div class="d-flex">
                                @if($booking->rental->main_image)
                                    <img src="{{ $booking->rental->main_image }}" alt="{{ $booking->rental->title }}" 
                                         class="me-3 rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="me-3 bg-light rounded d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px;">
                                        <i class="ti ti-photo text-muted"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $booking->rental->title }}</div>
                                    <small class="text-muted">
                                        <i class="ti ti-building-store me-1"></i>
                                        {{ $booking->rental->user->name }}
                                    </small>
                                    @if($booking->rental->location)
                                        <div>
                                            <small class="text-muted">
                                                <i class="ti ti-map-pin me-1"></i>
                                                <!-- Assuming location is json, we can decode it -->
                                                {{ json_decode($booking->rental->location)->city ?? 'Unbekannt' }},
                                                {{ json_decode($booking->rental->location)->state ?? 'Unbekannt' }},
                                                {{ json_decode($booking->rental->location)->postal_code ?? 'Unbekannt' }},
                                                {{ json_decode($booking->rental->location)->country ?? 'Unbekannt' }},
                                                {{ json_decode($booking->rental->location)->address ?? 'Unbekannt' }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rental Period -->
                    <div class="mb-3">
                        <h6 class="mb-2">Mietdauer</h6>
                        <div class="border rounded p-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted">Von</small>
                                    <div class="fw-semibold">{{ $booking->start_date->format('d.m.Y') }}</div>
                                    <div class="small">{{ $booking->start_date->format('H:i') }} Uhr</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Bis</small>
                                    <div class="fw-semibold">{{ $booking->end_date->format('d.m.Y') }}</div>
                                    <div class="small">{{ $booking->end_date->format('H:i') }} Uhr</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price Summary -->
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $booking->start_date->diffInDays($booking->end_date) + 1 }} Tag{{ $booking->start_date->diffInDays($booking->end_date) + 1 > 1 ? 'e' : '' }}</span>
                            <span class="fw-semibold">{{ number_format($booking->total_price, 2, ',', '.') }} €</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Servicegebühr</small>
                            <small>0,00 €</small>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center fw-bold">
                            <span>Gesamtpreis</span>
                            <span class="text-primary">{{ number_format($booking->total_price, 2, ',', '.') }} €</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4">
                        <a href="{{ route('search') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="ti ti-search me-1"></i>
                            Ähnliche Angebote anzeigen
                        </a>
                        
                        <a href="{{ route('rentals.show', $booking->rental->id) }}" class="btn btn-outline-secondary w-100">
                            <i class="ti ti-eye me-1"></i>
                            Artikel anzeigen
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Side - Original Booking Info & Chat -->
            <div class="col-lg-8">
                <!-- Original Booking Information (simplified) -->
                <div class="booking-card mb-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h4 class="mb-2">Buchung #{{ $booking->id }}</h4>
                            <p class="text-muted mb-0">
                                <i class="ti ti-calendar me-1"></i>
                                Erstellt am {{ $booking->created_at->format('d.m.Y H:i') }}
                            </p>
                        </div>
                        <span class="status-badge bg-{{ 
                            $booking->status === 'pending' ? 'warning' : 
                            ($booking->status === 'confirmed' ? 'success' : 
                            ($booking->status === 'completed' ? 'primary' : 'danger')) 
                        }}">
                            @switch($booking->status)
                                @case('pending') Ausstehend @break
                                @case('confirmed') Bestätigt @break
                                @case('completed') Abgeschlossen @break
                                @case('cancelled') Abgelehnt @break
                                @default {{ ucfirst($booking->status) }}
                            @endswitch
                        </span>
                    </div>

                    @if($booking->message)
                        <!-- Customer Message -->
                        <div class="mb-4">
                            <h6 class="mb-3">Ihre ursprüngliche Nachricht</h6>
                            <div class="alert alert-info">
                                <i class="ti ti-message-circle me-2"></i>
                                {{ $booking->message }}
                            </div>
                        </div>
                    @endif

                    @if($booking->vendor_notes && (Auth::check() && (Auth::user()->is_vendor || Auth::id() === $booking->renter_id)))
                        <!-- Vendor Notes -->
                        <div class="mb-4">
                            <h6 class="mb-3">Notizen vom Anbieter</h6>
                            <div class="alert alert-warning">
                                <i class="ti ti-notes me-2"></i>
                                {{ $booking->vendor_notes }}
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons for Vendor -->
                    @if(Auth::check() && Auth::user()->is_vendor && $booking->rental->vendor_id === Auth::id())
                        <div class="text-end">
                            @if($booking->canBeConfirmed())
                                <form action="{{ route('vendor.bookings.confirm', $booking->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success me-2">
                                        <i class="ti ti-check me-1"></i>
                                        Bestätigen
                                    </button>
                                </form>
                                <form action="{{ route('vendor.bookings.reject', $booking->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-danger me-2" 
                                            onclick="return confirm('Sind Sie sicher, dass Sie diese Buchung ablehnen möchten?')">
                                        <i class="ti ti-x me-1"></i>
                                        Ablehnen
                                    </button>
                                </form>
                            @endif

                            @if($booking->status === 'confirmed')
                                <form action="{{ route('vendor.bookings.complete', $booking->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-info">
                                        <i class="ti ti-check-circle me-1"></i>
                                        Als abgeschlossen markieren
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                    <!-- Action Buttons for Customer -->
                    @if($booking->canBeCancelled() && Auth::check() && Auth::id() === $booking->renter_id)
                        <div class="text-end">
                            <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-danger" 
                                        onclick="return confirm('Sind Sie sicher, dass Sie diese Buchung stornieren möchten?')">
                                    <i class="ti ti-x me-1"></i>
                                    Buchung stornieren
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Chat Messages -->
                @livewire('booking-messages', ['booking' => $booking])
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@livewireScripts
<script>
    // Auto-refresh page when booking status changes
    window.addEventListener('booking-status-changed', function(event) {
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    });
</script>
@endsection
