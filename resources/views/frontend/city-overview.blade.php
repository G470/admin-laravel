@extends('layouts.app')

@section('title', 'Standorte - Alle Städte und Regionen')
@section('meta_description', 'Entdecken Sie alle verfügbaren Standorte für Vermietungen. Finden Sie Ausrüstung und Equipment in Ihrer Stadt.')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-3">Alle Standorte</h1>
            <p class="text-muted">Entdecken Sie Mietangebote in allen verfügbaren Städten und Regionen</p>
        </div>
    </div>

    <!-- Country Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="d-flex gap-3 align-items-center">
                        <label class="form-label mb-0">Land:</label>
                        <select name="country" class="form-select w-auto" onchange="this.form.submit()">
                            <option value="DE" {{ $country === 'DE' ? 'selected' : '' }}>🇩🇪 Deutschland</option>
                            <option value="AT" {{ $country === 'AT' ? 'selected' : '' }}>🇦🇹 Österreich</option>
                            <option value="CH" {{ $country === 'CH' ? 'selected' : '' }}>🇨🇭 Schweiz</option>
                        </select>
                        <span class="text-muted">{{ $cities->flatten()->count() }} Städte gefunden</span>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Cities -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-star me-2"></i>Top Städte</h5>
                </div>
                <div class="card-body">
                    @forelse($topCities as $city)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <a href="{{ route('location.show', Str::slug($city->city)) }}" class="text-decoration-none">
                                <strong>{{ $city->city }}</strong>
                            </a>
                            @if($city->state)
                                <br><small class="text-muted">{{ $city->state }}</small>
                            @endif
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary">{{ $city->rentals_count }}</span>
                            <br><small class="text-muted">Angebote</small>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">Keine Top-Städte gefunden</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- States/Regions -->
        @if($states->count() > 0)
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-map me-2"></i>Regionen</h5>
                </div>
                <div class="card-body">
                    @foreach($states as $state)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <a href="{{ route('cities.overview', ['country' => $country, 'state' => $state->state]) }}" class="text-decoration-none">
                                <strong>{{ $state->state }}</strong>
                            </a>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-secondary">{{ $state->rentals_count }}</span>
                            <br><small class="text-muted">Angebote</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- All Cities A-Z -->
        <div class="col-lg-{{ $states->count() > 0 ? '4' : '8' }}">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="ti ti-list me-2"></i>Alle Städte A-Z</h5>
                </div>
                <div class="card-body">
                    @forelse($cities as $letter => $citiesInLetter)
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2">{{ $letter }}</h6>
                        <div class="row">
                            @foreach($citiesInLetter as $city)
                            <div class="col-md-6 mb-2">
                                <a href="{{ route('location.show', Str::slug($city->city)) }}" class="text-decoration-none d-flex justify-content-between align-items-center">
                                    <span>{{ $city->city }}</span>
                                    <small class="text-muted">({{ $city->rentals_count }})</small>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="ti ti-map-off display-1 text-muted"></i>
                        <h4 class="mt-3">Keine Städte gefunden</h4>
                        <p class="text-muted">Für {{ $country }} sind aktuell keine Städte mit Angeboten verfügbar.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>Ihre Stadt nicht dabei?</h4>
                    <p class="mb-3">Werden Sie Anbieter und erweitern Sie unser Netzwerk</p>
                    <a href="{{ route('rent-out') }}" class="btn btn-light">Jetzt vermieten</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
