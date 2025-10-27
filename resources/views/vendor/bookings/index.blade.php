@extends('layouts/contentNavbarLayout')
@section('title', 'Anfrageverwaltung')

@section('content')
    <div class="row">
        <!-- Statistics Cards -->
        <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">Gesamte Anfragen</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $totalBookings }}</h4>
                            </div>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-primary rounded p-2">
                                <i class="ti ti-calendar ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">Ausstehende Anfragen</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $pendingBookings }}</h4>
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

        <div class="col-xl-4 col-lg-6 col-md-12 col-sm-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="card-info">
                            <p class="card-text">Bestätigte Anfragen</p>
                            <div class="d-flex align-items-end mb-2">
                                <h4 class="card-title mb-0 me-2">{{ $confirmedBookings }}</h4>
                            </div>
                        </div>
                        <div class="card-icon">
                            <span class="badge bg-label-success rounded p-2">
                                <i class="ti ti-check ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Anfrageverwaltung</h5>
        </div>

        <!-- Filter Tabs -->
        <div class="card-body border-bottom">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-pills mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == '' ? 'active' : '' }}" 
                           href="{{ route('vendor.bookings.index') }}">
                            <i class="tf-icons ti ti-list me-1"></i>
                            Alle ({{ $totalBookings }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" 
                           href="{{ route('vendor.bookings.index', ['status' => 'pending']) }}">
                            <i class="tf-icons ti ti-clock me-1"></i>
                            Ausstehend ({{ $pendingBookings }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') == 'confirmed' ? 'active' : '' }}" 
                           href="{{ route('vendor.bookings.index', ['status' => 'confirmed']) }}">
                            <i class="tf-icons ti ti-check me-1"></i>
                            Bestätigt ({{ $confirmedBookings }})
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Anfrage-ID</th>
                        <th>Artikel</th>
                        <th>Kunde</th>
                        <th>Zeitraum</th>
                        <th>Betrag</th>
                        <th>Status</th>
                        <th>Datum</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($bookings as $booking)
                        <tr>
                            <td>
                                <strong>#{{ $booking->id }}</strong>
                                @if($booking->booking_token)
                                    <br>
                                    <small class="text-muted">{{ substr($booking->booking_token, 0, 8) }}...</small>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-start">
                                    @if($booking->rental && $booking->rental->images && $booking->rental->images->count() > 0)
                                        <img src="{{ asset('storage/' . $booking->rental->images->first()->image_path) }}" 
                                             alt="{{ $booking->rental->title }}" 
                                             class="rounded me-2" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px; min-width: 50px;">
                                            <i class="ti ti-photo text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h6 class="mb-0">
                                            @if($booking->rental)
                                                <a href="{{ route('vendor.rental', $booking->rental->id) }}" class="text-decoration-none">
                                                    {{ $Str::limit($booking->rental->title, 30) }}
                                                </a>
                                            @else
                                                <span class="text-muted">Artikel gelöscht</span>
                                            @endif
                                        </h6>
                                        @if($booking->rental)
                                            <small class="text-muted">{{ $booking->rental->price_per_day }}€/Tag</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <h6 class="mb-0">{{ $booking->first_name }} {{ $booking->last_name }}</h6>
                                    <small class="text-muted">{{ $booking->email }}</small>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ \Carbon\Carbon::parse($booking->start_date)->format('d.m.Y') }}</strong>
                                    <br>
                                    <small class="text-muted">bis {{ \Carbon\Carbon::parse($booking->end_date)->format('d.m.Y') }}</small>
                                    <br>
                                    <small class="text-primary">{{ $booking->duration }} Tage</small>
                                </div>
                            </td>
                            <td>
                                <strong>{{ number_format($booking->total_amount, 2) }}€</strong>
                                @if($booking->price_per_day)
                                    <br>
                                    <small class="text-muted">{{ $booking->price_per_day }}€/Tag</small>
                                @endif
                            </td>
                            <td>
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
                                    @case('completed')
                                        <span class="badge bg-info">Abgeschlossen</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                @endswitch
                            </td>
                            <td>
                                <small>{{ $booking->created_at->format('d.m.Y H:i') }}</small>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('vendor.bookings.show', $booking->id) }}">
                                            <i class="ti ti-eye me-1"></i> Details anzeigen
                                        </a>
                                        @if($booking->status === 'pending')
                                            <a class="dropdown-item" href="{{ route('vendor.bookings.confirm', $booking->id) }}">
                                                <i class="ti ti-check me-1"></i> Bestätigen
                                            </a>
                                            <form method="POST" action="{{ route('vendor.bookings.reject', $booking->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item text-danger" 
                                                        onclick="return confirm('Sind Sie sicher, dass Sie diese Anfrage ablehnen möchten?')">
                                                    <i class="ti ti-x me-1"></i> Ablehnen
                                                </button>
                                            </form>
                                        @endif
                                        @if($booking->booking_token)
                                            <a class="dropdown-item" href="{{ route('booking.token', $booking->booking_token) }}" target="_blank">
                                                <i class="ti ti-external-link me-1"></i> Öffentlich anzeigen
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="ti ti-calendar-x ti-xl text-muted mb-3"></i>
                                    <h5 class="mt-3 mb-2">Keine Anfragen gefunden</h5>
                                    <p class="text-muted">
                                        @if(request('status'))
                                            Sie haben noch keine Anfragen erhalten.
                                        @else
                                            Keine Anfragen mit dem Status "{{ $status }}" gefunden.
                                        @endif
                                    </p>
                                    @if(request('status'))
                                        <a href="{{ route('vendor.bookings.index') }}" class="btn btn-primary">
                                            Alle Anfragen anzeigen
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($bookings->hasPages())
            <div class="card-footer">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
@endsection