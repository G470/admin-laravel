@extends('layouts/contentNavbarLayoutFrontend')
@section('title', 'Meine Anfragen')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Meine Anfragen</h5>
                        <p class="text-muted mb-0">Verwalten Sie Ihre Mietanfragen</p>
                    </div>

                    <!-- Status Filter Tabs -->
                    <div class="card-body border-bottom">
                        <ul class="nav nav-pills nav-fill">
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') == '' ? 'active' : '' }}" 
                                   href="{{ route('user.bookings') }}">
                                    <i class="ti ti-list me-1"></i>
                                    Alle ({{ $totalBookings }})
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" 
                                   href="{{ route('user.bookings', ['status' => 'pending']) }}">
                                    <i class="ti ti-clock me-1"></i>
                                    Ausstehend ({{ $pendingBookings }})
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request('status') == 'confirmed' ? 'active' : '' }}" 
                                   href="{{ route('user.bookings', ['status' => 'confirmed']) }}">
                                    <i class="ti ti-check me-1"></i>
                                    Bestätigt ({{ $confirmedBookings }})
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Anfrage-ID</th>
                                    <th>Artikel</th>
                                    <th>Vermieter</th>
                                    <th>Zeitraum</th>
                                    <th>Betrag</th>
                                    <th>Status</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td>
                                            <strong>#{{ $booking->id }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $booking->created_at->format('d.m.Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($booking->rental && $booking->rental->images->count() > 0)
                                                    <img src="{{ asset('storage/' . $booking->rental->images->first()->image_path) }}" 
                                                         alt="{{ $booking->rental->title }}" 
                                                         class="rounded me-2" 
                                                         style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 40px; height: 40px;">
                                                        <i class="ti ti-photo text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    @if($booking->rental)
                                                        <h6 class="mb-0">{{ Str::limit($booking->rental->title, 30) }}</h6>
                                                        <small class="text-muted">{{ $booking->rental->category->name ?? 'Keine Kategorie' }}</small>
                                                    @else
                                                        <h6 class="mb-0 text-muted">Artikel nicht verfügbar</h6>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($booking->rental && $booking->rental->user)
                                                <div>
                                                    <h6 class="mb-0">{{ $booking->rental->user->name }}</h6>
                                                    <small class="text-muted">{{ $booking->rental->location->city ?? '' }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">Nicht verfügbar</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ \Carbon\Carbon::parse($booking->start_date)->format('d.m.Y') }}</strong>
                                                <br>
                                                <small class="text-muted">bis {{ \Carbon\Carbon::parse($booking->end_date)->format('d.m.Y') }}</small>
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
                                                    <span class="badge bg-danger">Storniert</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-info">Abgeschlossen</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                        type="button" data-bs-toggle="dropdown">
                                                    <i class="ti ti-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if($booking->booking_token)
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('booking.token', $booking->booking_token) }}">
                                                                <i class="ti ti-eye me-2"></i>Details anzeigen
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if($booking->status === 'pending')
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form method="POST" action="{{ route('user.bookings.cancel', $booking->id) }}" 
                                                                  onsubmit="return confirm('Möchten Sie diese Anfrage wirklich stornieren?')">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="ti ti-x me-2"></i>Stornieren
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                    @if($booking->rental)
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('rental.show', $booking->rental->id) }}">
                                                                <i class="ti ti-external-link me-2"></i>Artikel anzeigen
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="ti ti-calendar-x ti-xl text-muted mb-3"></i>
                                                <h5 class="mt-3 mb-2">Keine Anfragen gefunden</h5>
                                                <p class="text-muted mb-4">
                                                    @if(request('status'))
                                                        Sie haben noch keine Anfragen getätigt.
                                                    @else
                                                        Keine Anfragen mit dem Status "{{ $status }}" gefunden.
                                                    @endif
                                                </p>
                                                @if(request('status'))
                                                    <a href="{{ route('user.bookings') }}" class="btn btn-primary">
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
            </div>

            <!-- Sidebar with Statistics -->
            <div class="col-lg-4">
                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3 class="card-title text-primary">{{ $pendingBookings }}</h3>
                                <p class="card-text">Ausstehende Anfragen</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3 class="card-title text-success">{{ $confirmedBookings }}</h3>
                                <p class="card-text">Bestätigte Anfragen</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
