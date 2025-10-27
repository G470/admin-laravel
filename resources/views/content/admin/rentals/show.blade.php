@extends('layouts/contentNavbarLayout')

@section('title', 'Vermietungsobjekt: ' . $rental->title)

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-container">
                    <div class="row">
                        <div
                            class="col-12 col-md-7 d-flex align-items-center justify-content-md-start justify-content-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="ti ti-home-2"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="mb-0">Vermietungsobjekt Details</h4>
                                    <p class="text-muted mb-0">{{ $rental->title }}</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-12 col-md-5 d-flex align-items-center justify-content-md-end justify-content-center mt-3 mt-md-0">
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.rentals.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Zurück zur Übersicht
                                </a>
                                <a href="{{ route('admin.rentals.edit', $rental) }}" class="btn btn-primary">
                                    <i class="ti ti-edit me-1"></i>Bearbeiten
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Main Content -->
        <div class="row">
            <!-- Rental Details -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-info-circle me-2"></i>Grundinformationen
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Name</label>
                                <p class="mb-0">{{ $rental->title }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <div>
                                    <span
                                        class="badge bg-label-{{ $rental->status == 'active' ? 'success' : ($rental->status == 'inactive' ? 'secondary' : ($rental->status == 'pending' ? 'warning' : 'danger')) }}">
                                        {{ ucfirst($rental->status) }}
                                    </span>
                                    @if($rental->featured)
                                        <span class="badge bg-label-warning ms-1">Featured</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold">Beschreibung</label>
                                <div class="border rounded p-3 bg-light">
                                    {!! $rental->description !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Information -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-currency-euro me-2"></i>Preise
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Preis pro Stunde</label>
                                <p class="mb-0 text-primary fw-bold">{{ number_format($rental->price_range_hour, 2) }}
                                    {{ $rental->currency ?? '€' }}
                                </p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Preis pro Tag</label>
                                <p class="mb-0">
                                    {{ $rental->price_range_day ? number_format($rental->price_range_day, 2) . ' ' . ($rental->currency ?? '€') : '-' }}
                                </p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Einmaliger Preis</label>
                                <p class="mb-0">
                                    {{ $rental->price_range_once ? number_format($rental->price_range_once, 2) . ' ' . ($rental->currency ?? '€') : '-' }}
                                </p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-semibold">Servicegebühr</label>
                                <p class="mb-0">
                                    {{ $rental->service_fee ? number_format($rental->service_fee, 2) . ' ' . ($rental->currency ?? '€') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-map-pin me-2"></i>Standort
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Standort</label>
                                <p class="mb-0">{{ $rental->location ? $rental->location->name : '-' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Stadt</label>
                                <p class="mb-0">{{ $rental->city ? $rental->city->city : '-' }}</p>
                            </div>
                            @if($rental->latitude && $rental->longitude)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-semibold">Koordinaten</label>
                                    <p class="mb-0">{{ $rental->latitude }}, {{ $rental->longitude }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- SEO Information -->
                @if($rental->meta_title || $rental->meta_description || $rental->meta_keywords)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-search me-2"></i>SEO-Informationen
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($rental->meta_title)
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-semibold">Meta-Titel</label>
                                        <p class="mb-0">{{ $rental->meta_title }}</p>
                                    </div>
                                @endif
                                @if($rental->meta_description)
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-semibold">Meta-Beschreibung</label>
                                        <p class="mb-0">{{ $rental->meta_description }}</p>
                                    </div>
                                @endif
                                @if($rental->meta_keywords)
                                    <div class="col-12 mb-3">
                                        <label class="form-label fw-semibold">Meta-Schlüsselwörter</label>
                                        <p class="mb-0">{{ $rental->meta_keywords }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 mb-4">
                <!-- Vendor Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-user me-2"></i>Anbieter
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($rental->vendor)
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        {{ substr($rental->vendor->name, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $rental->vendor->name }}</h6>
                                    <small class="text-muted">{{ $rental->vendor->email }}</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="mailto:{{ $rental->vendor->email }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-mail me-1"></i>E-Mail
                                </a>
                                @if($rental->vendor->phone)
                                    <a href="tel:{{ $rental->vendor->phone }}" class="btn btn-sm btn-outline-success">
                                        <i class="ti ti-phone me-1"></i>Anrufen
                                    </a>
                                @endif
                            </div>
                        @else
                            <p class="text-muted mb-0">Kein Anbieter zugeordnet</p>
                        @endif
                    </div>
                </div>

                <!-- Category Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-category me-2"></i>Kategorie
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($rental->category)
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded bg-label-info">
                                        <i class="ti ti-category"></i>
                                    </span>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $rental->category->name }}</h6>
                                    @if($rental->category->description)
                                        <small class="text-muted">{{ Str::limit($rental->category->description, 50) }}</small>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p class="text-muted mb-0">Keine Kategorie zugeordnet</p>
                        @endif
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-chart-bar me-2"></i>Statistiken
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-info">
                                            <i class="ti ti-eye"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $rental->views ?? 0 }}</h6>
                                        <small class="text-muted">Aufrufe</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $rental->bookings->count() }}</h6>
                                        <small class="text-muted">Buchungen</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class="ti ti-star"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $rental->reviews->count() }}</h6>
                                        <small class="text-muted">Bewertungen</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="ti ti-heart"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $rental->favorites_count ?? 0 }}</h6>
                                        <small class="text-muted">Favoriten</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-settings me-2"></i>Schnellaktionen
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <form action="{{ route('admin.rentals.toggle-status', $rental) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="btn btn-outline-{{ $rental->status == 'active' ? 'warning' : 'success' }} w-100">
                                    <i class="ti ti-toggle-{{ $rental->status == 'active' ? 'right' : 'left' }} me-1"></i>
                                    {{ $rental->status == 'active' ? 'Deaktivieren' : 'Aktivieren' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.rentals.toggle-featured', $rental) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="btn btn-outline-{{ $rental->featured ? 'secondary' : 'warning' }} w-100">
                                    <i class="ti ti-star{{ $rental->featured ? '-filled' : '' }} me-1"></i>
                                    {{ $rental->featured ? 'Featured entfernen' : 'Als Featured markieren' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.rentals.destroy', $rental) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Sind Sie sicher, dass Sie dieses Vermietungsobjekt löschen möchten?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="ti ti-trash me-1"></i>Löschen
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="row">
            <!-- Recent Bookings -->
            @if($rental->bookings->count() > 0)
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-calendar me-2"></i>Letzte Buchungen
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Buchungs-ID</th>
                                            <th>Kunde</th>
                                            <th>Datum</th>
                                            <th>Status</th>
                                            <th>Preis</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rental->bookings->take(5) as $booking)
                                            <tr>
                                                <td>#{{ $booking->id }}</td>
                                                <td>{{ $booking->user ? $booking->user->name : 'Unbekannt' }}</td>
                                                <td>{{ $booking->created_at->format('d.m.Y H:i') }}</td>
                                                <td>
                                                    <span
                                                        class="badge bg-label-{{ $booking->status == 'confirmed' ? 'success' : ($booking->status == 'pending' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ number_format($booking->total_price, 2) }} €</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Reviews -->
            @if($rental->reviews->count() > 0)
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-star me-2"></i>Letzte Bewertungen
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($rental->reviews->take(3) as $review)
                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded bg-label-primary">
                                                        {{ substr($review->user ? $review->user->name : 'U', 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $review->user ? $review->user->name : 'Unbekannt' }}</h6>
                                                    <small class="text-muted">{{ $review->created_at->format('d.m.Y') }}</small>
                                                </div>
                                            </div>
                                            <div class="mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="ti ti-star{{ $i <= $review->rating ? '-filled' : '' }} text-warning"></i>
                                                @endfor
                                            </div>
                                            <p class="mb-0">{{ Str::limit($review->comment, 100) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection