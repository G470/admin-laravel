<div>
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-white mb-1">{{ $stats['total'] }}</h4>
                            <span>Gesamte Anfragen</span>
                        </div>
                        <i class="ti ti-calendar ti-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-white mb-1">{{ $stats['pending'] }}</h4>
                            <span>Ausstehend</span>
                        </div>
                        <i class="ti ti-clock ti-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-white mb-1">{{ $stats['confirmed'] }}</h4>
                            <span>Bestätigt</span>
                        </div>
                        <i class="ti ti-check ti-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card {{ $isVendor ? 'bg-info' : 'bg-secondary' }} text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($isVendor)
                                <h4 class="text-white mb-1">€{{ number_format($stats['revenue'], 2, ',', '.') }}</h4>
                                <span>Umsatz</span>
                            @else
                                <h4 class="text-white mb-1">{{ $stats['completed'] }}</h4>
                                <span>Abgeschlossen</span>
                            @endif
                        </div>
                        <i class="ti {{ $isVendor ? 'ti-currency-euro' : 'ti-check-circle' }} ti-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 mb-3 mb-md-0">
                    <input type="text" wire:model.debounce.300ms="search" class="form-control"
                        placeholder="Anfragen durchsuchen...">
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <select wire:model="selectedStatus" class="form-select">
                        <option value="all">Alle Status</option>
                        <option value="pending">Ausstehend</option>
                        <option value="confirmed">Bestätigt</option>
                        <option value="completed">Abgeschlossen</option>
                        <option value="cancelled">Storniert</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button wire:click="$refresh" class="btn btn-outline-primary">
                            <i class="ti ti-refresh"></i>
                            Aktualisieren
                        </button>
                        @if($isVendor)
                            <a href="{{ route('vendor.bookings.index') }}" class="btn btn-outline-secondary">
                                <i class="ti ti-external-link"></i>
                                Vollansicht
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                @if($isVendor)
                    Anfragenverwaltung
                @else
                    Meine Anfragen
                @endif
            </h5>
        </div>

        <div class="card-body p-0">
            @if($bookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Artikel</th>
                                <th>
                                    @if($isVendor)
                                        Kunde
                                    @else
                                        Anbieter
                                    @endif
                                </th>
                                <th>Zeitraum</th>
                                <th>Status</th>
                                <th>Preis</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>
                                        <a href="{{ route('bookings.show', $booking->id) }}"
                                            class="fw-semibold text-decoration-none">
                                            #{{ $booking->id }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($booking->rental->main_image)
                                                <img src="{{ $booking->rental->main_image }}" alt="{{ $booking->rental->title }}"
                                                    class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                    style="width: 40px; height: 40px;">
                                                    <i class="ti ti-photo text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $booking->rental->title }}</h6>
                                                <small class="text-muted">{{ $booking->rental_type_label ?? 'Täglich' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($isVendor)
                                            <div>
                                                <strong>{{ $booking->guest_name ?? $booking->renter->name ?? 'Unbekannt' }}</strong>
                                                <br>
                                                <small
                                                    class="text-muted">{{ $booking->guest_email ?? $booking->renter->email ?? '' }}</small>
                                            </div>
                                        @else
                                            <div>
                                                <strong>{{ $booking->rental->user->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $booking->rental->user->email }}</small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $booking->start_date->format('d.m.Y') }}</strong>
                                            <br>
                                            <small class="text-muted">bis {{ $booking->end_date->format('d.m.Y') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $booking->status_color }}">
                                            {{ $booking->status_label }}
                                        </span>
                                        @if($booking->messages_count > 0)
                                            <br>
                                            <small class="text-primary">
                                                <i class="ti ti-message-circle"></i> {{ $booking->messages_count }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>€{{ number_format($booking->total_price, 2, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="ti ti-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('bookings.show', $booking->id) }}">
                                                        <i class="ti ti-eye me-2"></i>Details
                                                    </a>
                                                </li>

                                                @if($isVendor && $booking->canBeConfirmed())
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <button wire:click="confirmBooking({{ $booking->id }})"
                                                            class="dropdown-item text-success">
                                                            <i class="ti ti-check me-2"></i>Bestätigen
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button wire:click="rejectBooking({{ $booking->id }})"
                                                            class="dropdown-item text-danger"
                                                            onclick="return confirm('Sind Sie sicher?')">
                                                            <i class="ti ti-x me-2"></i>Ablehnen
                                                        </button>
                                                    </li>
                                                @endif

                                                @if(!$isVendor && $booking->canBeCancelled())
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <button wire:click="cancelBooking({{ $booking->id }})"
                                                            class="dropdown-item text-danger"
                                                            onclick="return confirm('Sind Sie sicher, dass Sie diese Anfrage stornieren möchten?')">
                                                            <i class="ti ti-x me-2"></i>Stornieren
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="ti ti-calendar-off" style="font-size: 4rem; color: #d1d5db;"></i>
                    <h5 class="mt-3 mb-2">Keine Anfragen gefunden</h5>
                    <p class="text-muted">
                        @if($search)
                            Keine Anfragen entsprechen Ihrer Suche "{{ $search }}".
                        @elseif($selectedStatus !== 'all')
                            Keine Anfragen mit dem Status "{{ $selectedStatus }}" gefunden.
                        @else
                            @if($isVendor)
                                Sie haben noch keine Anfragen erhalten.
                            @else
                                Sie haben noch keine Anfragen erstellt.
                            @endif
                        @endif
                    </p>
                    @if(!$isVendor && $selectedStatus === 'all' && !$search)
                        <a href="{{ route('search') }}" class="btn btn-primary">
                            <i class="ti ti-search me-1"></i>
                            Artikel suchen
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @error('general')
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @enderror
</div>