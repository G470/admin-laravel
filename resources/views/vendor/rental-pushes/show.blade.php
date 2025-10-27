@extends('layouts/contentNavbarLayout')

@section('title', 'Artikel-Push Details')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Page Header -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-container">
                    <div class="row">
                        <div
                            class="col-12 col-md-7 d-flex align-items-center justify-content-md-start justify-content-center">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="ti ti-rocket"></i>
                                    </span>
                                </div>
                                <div>
                                    <h4 class="mb-0">Artikel-Push Details</h4>
                                    <p class="text-muted mb-0">{{ $rentalPush->rental->title }}</p>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-12 col-md-5 d-flex align-items-center justify-content-md-end justify-content-center mt-3 mt-md-0">
                            <div class="d-flex gap-2">
                                <a href="{{ route('vendor.rental-pushes.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-arrow-left me-1"></i>Zurück zur Übersicht
                                </a>
                                <a href="{{ route('vendor.rental-pushes.edit', $rentalPush) }}" class="btn btn-primary">
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
            <!-- Push Details -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-info-circle me-2"></i>Push-Kampagne Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Artikel</label>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        @if($rentalPush->rental->images && count($rentalPush->rental->images) > 0)
                                            <img src="{{ asset('storage/' . $rentalPush->rental->images[0]) }}"
                                                alt="Rental Image" class="rounded">
                                        @else
                                            <img src="{{asset('assets/img/backgrounds/1.jpg')}}" alt="Default Image"
                                                class="rounded">
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $rentalPush->rental->title }}</h6>
                                        <small class="text-muted">{{ $rentalPush->rental->price_range_hour }}€/h</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <div>
                                    <span
                                        class="badge bg-label-{{ $rentalPush->status_color }}">{{ $rentalPush->status_label }}</span>
                                    @if($rentalPush->featured)
                                        <span class="badge bg-label-warning ms-1">Featured</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Kategorie</label>
                                <p class="mb-0">{{ $rentalPush->category->name }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Standort</label>
                                <p class="mb-0">{{ $rentalPush->location->name }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Frequenz</label>
                                <p class="mb-0">{{ $rentalPush->frequency_label }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Credits pro Push</label>
                                <p class="mb-0">{{ $rentalPush->credits_per_push }} Credit</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campaign Timeline -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-calendar me-2"></i>Kampagnen-Zeitraum
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Startdatum</label>
                                <p class="mb-0">{{ $rentalPush->start_date->format('d.m.Y H:i') }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Enddatum</label>
                                <p class="mb-0">{{ $rentalPush->end_date->format('d.m.Y H:i') }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Kampagnendauer</label>
                                <p class="mb-0">{{ $rentalPush->start_date->diffInDays($rentalPush->end_date) }} Tag(e)</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Erstellt am</label>
                                <p class="mb-0">{{ $rentalPush->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Credit Usage -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-currency-euro me-2"></i>Credit-Verbrauch
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Verbrauchte Credits</label>
                                <p class="mb-0 text-primary fw-bold">{{ $rentalPush->credits_used }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Benötigte Credits</label>
                                <p class="mb-0">{{ $rentalPush->total_credits_needed }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Verbleibende Credits</label>
                                <p class="mb-0">{{ $rentalPush->remaining_credits }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Fortschritt</label>
                                <p class="mb-0 text-primary fw-bold">{{ $rentalPush->progress_percentage }}%</p>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 10px;">
                            <div class="progress-bar" role="progressbar"
                                style="width: {{ $rentalPush->progress_percentage }}%"
                                aria-valuenow="{{ $rentalPush->progress_percentage }}" aria-valuemin="0"
                                aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Push History -->
                @if($rentalPush->creditTransactions->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-history me-2"></i>Push-Verlauf
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Datum</th>
                                            <th>Credits</th>
                                            <th>Nächster Push</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rentalPush->creditTransactions->take(10) as $transaction)
                                            <tr>
                                                <td>{{ $transaction->push_executed_at->format('d.m.Y H:i') }}</td>
                                                <td>{{ $transaction->credits_used }} Credit(s)</td>
                                                <td>
                                                    @if($transaction->next_push_at)
                                                        {{ $transaction->next_push_at->format('d.m.Y H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($rentalPush->creditTransactions->count() > 10)
                                <div class="text-center mt-3">
                                    <small class="text-muted">Zeige die letzten 10 Pushes von
                                        {{ $rentalPush->creditTransactions->count() }} insgesamt</small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4 mb-4">
                <!-- Current Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-activity me-2"></i>Aktueller Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div>
                                <span
                                    class="badge bg-label-{{ $rentalPush->status_color }}">{{ $rentalPush->status_label }}</span>
                            </div>
                        </div>
                        @if($rentalPush->last_push_at)
                            <div class="mb-3">
                                <label class="form-label">Letzter Push</label>
                                <p class="mb-0">{{ $rentalPush->last_push_at->format('d.m.Y H:i') }}</p>
                            </div>
                        @endif
                        @if($rentalPush->next_push_at && $rentalPush->status === 'active')
                            <div class="mb-3">
                                <label class="form-label">Nächster Push</label>
                                <p class="mb-0">{{ $rentalPush->next_push_at->format('d.m.Y H:i') }}</p>
                                <small class="text-muted">{{ $rentalPush->time_until_next_push }}</small>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Aktiv</label>
                            <p class="mb-0">
                                <i class="ti ti-{{ $rentalPush->is_active ? 'check text-success' : 'x text-danger' }}"></i>
                                {{ $rentalPush->is_active ? 'Ja' : 'Nein' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-settings me-2"></i>Schnellaktionen
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($rentalPush->status === 'active' || $rentalPush->status === 'paused')
                                <form action="{{ route('vendor.rental-pushes.toggle-status', $rentalPush) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-outline-{{ $rentalPush->status === 'active' ? 'warning' : 'success' }} w-100">
                                        <i class="ti ti-{{ $rentalPush->status === 'active' ? 'pause' : 'play' }} me-1"></i>
                                        {{ $rentalPush->status === 'active' ? 'Pausieren' : 'Aktivieren' }}
                                    </button>
                                </form>
                            @endif
                            @if($rentalPush->status !== 'cancelled')
                                <form action="{{ route('vendor.rental-pushes.destroy', $rentalPush) }}" method="POST"
                                    onsubmit="return confirm('Sind Sie sicher, dass Sie diese Push-Kampagne abbrechen möchten?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="ti ti-trash me-1"></i>Kampagne abbrechen
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Campaign Statistics -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ti ti-chart-bar me-2"></i>Kampagnen-Statistiken
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <i class="ti ti-rocket"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $rentalPush->creditTransactions->count() }}</h6>
                                        <small class="text-muted">Pushes</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-success">
                                            <i class="ti ti-currency-euro"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $rentalPush->credits_used }}</h6>
                                        <small class="text-muted">Credits</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-info">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $rentalPush->start_date->diffInDays($rentalPush->end_date) }}
                                        </h6>
                                        <small class="text-muted">Tage</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class="ti ti-percentage"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $rentalPush->progress_percentage }}%</h6>
                                        <small class="text-muted">Fortschritt</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection