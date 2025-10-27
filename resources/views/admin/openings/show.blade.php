@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Öffnungszeiten Details')

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Öffnungszeiten Details</h5>
                    <div>
                        <a href="{{ route('admin.openings.edit', $opening->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="ti ti-edit me-1"></i>
                            Bearbeiten
                        </a>
                        <a href="{{ route('admin.openings.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ti ti-arrow-left me-1"></i>
                            Zurück
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Standort Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $opening->location->name ?? 'Unbekannt' }}</td>
                                </tr>
                                <tr>
                                    <th>Adresse:</th>
                                    <td>{{ $opening->location->address ?? 'Nicht angegeben' }}</td>
                                </tr>
                                <tr>
                                    <th>Anbieter:</th>
                                    <td>{{ $opening->location->user->name ?? 'Unbekannt' }}</td>
                                </tr>
                                <tr>
                                    <th>E-Mail:</th>
                                    <td>{{ $opening->location->user->email ?? 'Nicht angegeben' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Öffnungszeiten Details</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th>Wochentag:</th>
                                    <td>
                                        <span class="badge bg-label-info">{{ $opening->day_name }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @if($opening->is_closed)
                                            <span class="badge bg-danger">Geschlossen</span>
                                        @else
                                            <span class="badge bg-success">Geöffnet</span>
                                        @endif
                                    </td>
                                </tr>
                                @if(!$opening->is_closed)
                                    <tr>
                                        <th>Öffnungszeit:</th>
                                        <td>{{ \Carbon\Carbon::parse($opening->open_time)->format('H:i') }} Uhr</td>
                                    </tr>
                                    <tr>
                                        <th>Schließzeit:</th>
                                        <td>{{ \Carbon\Carbon::parse($opening->close_time)->format('H:i') }} Uhr</td>
                                    </tr>
                                    @if($opening->break_start && $opening->break_end)
                                        <tr>
                                            <th>Pause:</th>
                                            <td>
                                                {{ \Carbon\Carbon::parse($opening->break_start)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($opening->break_end)->format('H:i') }} Uhr
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                                @if($opening->notes)
                                    <tr>
                                        <th>Notizen:</th>
                                        <td>{{ $opening->notes }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>Erstellt:</th>
                                    <td>{{ $opening->created_at->format('d.m.Y H:i') }} Uhr</td>
                                </tr>
                                <tr>
                                    <th>Aktualisiert:</th>
                                    <td>{{ $opening->updated_at->format('d.m.Y H:i') }} Uhr</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if(!$opening->is_closed)
                        <div class="alert alert-info mt-4">
                            <h6><i class="ti ti-info-circle me-2"></i>Öffnungszeiten Übersicht</h6>
                            <p class="mb-0">
                                <strong>{{ $opening->day_name }}:</strong> {{ $opening->formatted_hours }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
