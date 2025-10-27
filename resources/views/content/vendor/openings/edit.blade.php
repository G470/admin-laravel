@extends('layouts/contentNavbarLayout')

@section('title', $location ? 'Öffnungszeiten bearbeiten' : 'Neue Öffnungszeiten')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $location ? 'Öffnungszeiten für ' . $location->name : 'Neue Öffnungszeiten' }}
                    </h4>
                </div>
                <div class="card-body">
                    @if($location)
                        <form action="{{ route('vendor-openings-update', $location->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                @foreach(['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag', 'Sonntag'] as $day)
                                    <div class="col-md-6 mb-2">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5 class="card-title">{{ $day }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Von</label>
                                                            <input type="time" class="form-control"
                                                                name="openings[{{ strtolower($day) }}][from]"
                                                                value="{{ old('openings.' . strtolower($day) . '.from') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Bis</label>
                                                            <input type="time" class="form-control"
                                                                name="openings[{{ strtolower($day) }}][to]"
                                                                value="{{ old('openings.' . strtolower($day) . '.to') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check mt-1">
                                                    <input type="checkbox" class="form-check-input"
                                                        name="openings[{{ strtolower($day) }}][closed]"
                                                        id="closed_{{ strtolower($day) }}" value="1">
                                                    <label class="form-check-label" for="closed_{{ strtolower($day) }}">
                                                        Geschlossen
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row mt-2">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Speichern</button>
                                    <a href="{{ route('vendor-openings') }}" class="btn btn-secondary">Zurück</a>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-warning">
                            Bitte wählen Sie zuerst einen Standort aus.
                        </div>
                        <a href="{{ route('vendor-locations') }}" class="btn btn-primary">Zu den Standorten</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection