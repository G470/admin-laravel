@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Inlando')

@section('styles')
<style>
    .hero-section {
        position: relative;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .hero-image {
        position: relative;
    }

    .hero-image::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .search-form {
        position: relative;
        z-index: 2;
    }

    .card-hover {
        transition: all 0.25s ease;
    }

    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endsection

@section('content')
<!-- 🔎 Hero-Sektion mit Suchformular -->
<section class="section-py landing-hero-bg position-relative">
    <div class="container" style="height: 500px;">
        <div class="hero-text-box text-center z-1 rounded-3 position-relative mt-n4 py-5 my-5 flex-grow-1">
            <h1 class="text-white mb-0 display-6 fw-bold">Finde und miete, was du brauchst</h1>
            <div class="search-form bg-white p-4 rounded-3 shadow mt-4">
                <form action="{{ route('search') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="searchQuery" name="query"
                                    placeholder="Was möchtest du mieten?">
                                <label for="searchQuery">Was möchtest du mieten?</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="location" name="location"
                                    placeholder="Ort / PLZ">
                                <label for="location">Ort / PLZ</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="dateFrom" name="dateFrom">
                                <label for="dateFrom">Von</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="dateTo" name="dateTo">
                                <label for="dateTo">Bis</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-1">
                            <button type="submit" class="btn btn-primary h-100 w-100">
                                <i class="ti ti-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- 🧭 Kategorien durchsuchen -->
<section class="section-py">
    <div class="container">
        <h2 class="text-center mb-2 display-6">Kategorien durchsuchen</h2>
        <p class="text-center mb-5 text-body">Entdecke verschiedene Kategorien von Artikeln, die du mieten kannst</p>
        <div class="row gy-4 mt-2">
            @forelse($categories as $category)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('category.show', $category->slug) }}" class="text-decoration-none">
                    <div class="card card-hover h-100 shadow-sm border-0">
                        <img src="{{ $category->image }}" class="card-img-top" alt="{{ $category->name }}">
                        <div class="card-body text-center">
                            <h5 class="card-title fw-semibold text-heading">{{ $category->name }}</h5>
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-0">Keine Kategorien verfügbar.</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- 🚌 Themenbereich: Wohnmobil entdecken -->
<section class="section-py bg-body">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 text-center">
                <h2 class="mb-2 display-6">Wohnmobil entdecken</h2>
                <p class="mb-4 text-body">Entdecke die Freiheit auf vier Rädern – miete ein Wohnmobil und erlebe deinen
                    perfekten Urlaub. Flexibel, unabhängig und mit allem Komfort, den du brauchst.</p>
                <a href="#" class="btn btn-primary btn-lg waves-effect waves-light">Jetzt entdecken</a>
            </div>
        </div>
    </div>
</section>

<!-- 🎪 Eventartikel mieten -->
<section class="section-py">
    <div class="container">
        <h2 class="text-center mb-2 display-6">Eventartikel mieten</h2>
        <p class="text-center mb-5 text-body">Alles was du für dein nächstes Event benötigst</p>
        <div class="row gy-4 mt-2">
            @forelse($eventItems as $item)
            <div class="col-6 col-md-3">
                <div class="card card-hover h-100 shadow-sm border-0">
                    <img src="{{ $item->image }}" class="card-img-top" alt="{{ $item->name }}">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-semibold text-heading">{{ $item->name }}</h5>
                        <a href="{{ route('category.event', $item->slug) }}"
                            class="btn btn-outline-primary mt-2 waves-effect">Anzeigen</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-0">Keine Eventartikel verfügbar.</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('categories.events') }}" class="btn btn-primary waves-effect waves-light">Alle
                Eventartikel anzeigen</a>
        </div>
    </div>
</section>

<!-- 🚛 Nutzfahrzeuge & Freizeitfahrzeuge -->
<section class="section-py bg-body">
    <div class="container">
        <h2 class="text-center mb-2 display-6">Nutzfahrzeuge & Freizeitfahrzeuge</h2>
        <p class="text-center mb-5 text-body">Für Transport, Urlaub und Ausflüge</p>
        <div class="row gy-4 mt-2">
            @forelse($vehicles as $vehicle)
            <div class="col-12 col-md-4">
                <div class="card card-hover h-100 shadow-sm border-0">
                    <img src="{{ $vehicle->image }}" class="card-img-top" alt="{{ $vehicle->name }}">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-semibold text-heading">{{ $vehicle->name }}</h5>
                        <p class="card-text text-body">{{ $vehicle->description }}</p>
                        <a href="{{ route('category.vehicles', $vehicle->slug) }}"
                            class="btn btn-outline-primary waves-effect">Anzeigen</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-0">Keine Fahrzeuge verfügbar.</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('categories.vehicles') }}" class="btn btn-primary waves-effect waves-light">Alle Fahrzeuge
                anzeigen</a>
        </div>
    </div>
</section>

<!-- 🏗️ Baumaschinen & Bauzubehör -->
<section class="section-py">
    <div class="container">
        <h2 class="text-center mb-2 display-6">Baumaschinen & Bauzubehör</h2>
        <p class="text-center mb-5 text-body">Professionelles Equipment für dein Bauprojekt</p>
        <div class="row gy-4 mt-2">
            @forelse($constructionTools as $tool)
            <div class="col-12 col-md-4">
                <div class="card card-hover h-100 shadow-sm border-0">
                    <img src="{{ $tool->image }}" class="card-img-top" alt="{{ $tool->name }}">
                    <div class="card-body text-center">
                        <h5 class="card-title fw-semibold text-heading">{{ $tool->name }}</h5>
                        <p class="card-text text-body">{{ $tool->description }}</p>
                        <a href="{{ route('category.construction', $tool->slug) }}"
                            class="btn btn-outline-primary waves-effect">Anzeigen</a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center">
                <div class="card">
                    <div class="card-body">
                        <p class="mb-0">Keine Baumaschinen verfügbar.</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('categories.construction') }}" class="btn btn-primary waves-effect waves-light">Alle
                Baumaschinen anzeigen</a>
        </div>
    </div>
</section>
@endsection