@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Öffnungszeiten verwalten')

@section('content')
<div class="container-xxl">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Öffnungszeiten verwalten</h5>
                    <div>
                        <a href="{{ route('admin.openings.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-1"></i>
                            Neue Öffnungszeiten
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <select name="location_id" class="form-select">
                                    <option value="">Alle Standorte</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" 
                                                {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }} - {{ $location->user->name ?? 'Unbekannt' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Suchen..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="ti ti-search me-1"></i>
                                    Filtern
                                </button>
                                <a href="{{ route('admin.openings.index') }}" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh me-1"></i>
                                    Zurücksetzen
                                </a>
                            </div>
                        </div>
                    </form>

                    @if($openings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Standort</th>
                                        <th>Anbieter</th>
                                        <th>Wochentag</th>
                                        <th>Öffnungszeiten</th>
                                        <th>Status</th>
                                        <th>Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($openings as $opening)
                                        <tr>
                                            <td>
                                                <strong>{{ $opening->location->name ?? 'Unbekannt' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $opening->location->address ?? '' }}</small>
                                            </td>
                                            <td>{{ $opening->location->user->name ?? 'Unbekannt' }}</td>
                                            <td>
                                                <span class="badge bg-label-info">{{ $opening->day_name }}</span>
                                            </td>
                                            <td>
                                                @if($opening->is_closed)
                                                    <span class="text-danger">Geschlossen</span>
                                                @else
                                                    <strong>{{ $opening->formatted_hours }}</strong>
                                                @endif
                                                @if($opening->notes)
                                                    <br>
                                                    <small class="text-muted">{{ $opening->notes }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($opening->is_closed)
                                                    <span class="badge bg-danger">Geschlossen</span>
                                                @else
                                                    <span class="badge bg-success">Geöffnet</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                            type="button" data-bs-toggle="dropdown">
                                                        Aktionen
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.openings.show', $opening->id) }}">
                                                                <i class="ti ti-eye me-2"></i>Anzeigen
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('admin.openings.edit', $opening->id) }}">
                                                                <i class="ti ti-edit me-2"></i>Bearbeiten
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.openings.destroy', $opening->id) }}" 
                                                                  style="display: inline;"
                                                                  onsubmit="return confirm('Sind Sie sicher?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="ti ti-trash me-2"></i>Löschen
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $openings->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ti ti-clock-off" style="font-size: 4rem; color: #d1d5db;"></i>
                            <h5 class="mt-3 mb-2">Keine Öffnungszeiten gefunden</h5>
                            <p class="text-muted">
                                @if(request('search') || request('location_id'))
                                    Keine Öffnungszeiten entsprechen Ihren Filterkriterien.
                                @else
                                    Es wurden noch keine Öffnungszeiten erstellt.
                                @endif
                            </p>
                            <a href="{{ route('admin.openings.create') }}" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i>
                                Erste Öffnungszeiten erstellen
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
