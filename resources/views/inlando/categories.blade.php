@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Kategorienübersicht')

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

        .landing-hero-bg {
            background: #2222229c;
            background-size: cover;
            background-position: center;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;

        }



        /* This is just to transition when you change the viewport size. */
        * {
            transition: all 0.5s ease-out;
        }
    </style>
@endsection

@section('content')
    <!-- 🔎 Hero-Sektion mit Suchformular -->
    <section class="section-py landing-hero-bg position-relative d-flex align-items-center justify-content-center">
        <div class="hero-image position-absolute top-0 start-0 w-100 h-100"
            style="background-size: cover; background-position: center;">
        </div>
        <div class="gradient position-absolute top-0 start-0 w-100 h-100"></div>

        <div class="container position-relative d-flex align-items-center justify-content-evenly" style="height: 500px;">
            <div class="hero-text-box text-center z-1 rounded-3 position-relative mt-n4 py-5 my-5 flex-grow-1">
                <h1 class="text-white mb-0 display-6 fw-bold">{{ $heroTitle }}</h1>
                <div class="search-form bg-white p-4 rounded-3 shadow mt-4">
                    @livewire('search-form')
                </div>
            </div>
        </div>
    </section>

    <!-- 🧭 Kategorien durchsuchen -->
    @if($categoriesSectionEnabled)
        <section class="section-py">
            <div class="container">
                <h2 class="text-center mb-2 display-6">{{ $categoriesSectionTitle }}</h2>
                <p class="text-center mb-5 text-body">{{ $categoriesSectionSubtitle }}</p>
                <div class="row gy-4 mt-2">
                    @forelse($categories as $category)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route('category.show', $category->slug) }}" class="text-decoration-none">
                                <div class="card card-hover h-100 shadow-sm border-0">
                                    <img src="{{ $category->category_image ?: asset('assets/images/categories/default.svg') }}"
                                        class="card-img-top" alt="{{ $category->name }}">
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
    @endif

    <!-- 🚌 Themenbereich: Wohnmobil entdecken -->
    @if($wohnmobilSectionEnabled)
        <section class="section-py bg-body">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-md-8 text-center">
                        <h2 class="mb-2 display-6">{{ $wohnmobilSectionTitle }}</h2>
                        <p class="mb-4 text-body">{{ $wohnmobilSectionSubtitle }}</p>
                        <a href="{{ $wohnmobilSectionButtonLink }}"
                            class="btn btn-primary btn-lg waves-effect waves-light">{{ $wohnmobilSectionButtonText }}</a>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- 🎪 Eventartikel mieten -->
    @if($eventsSectionEnabled)
        <section class="section-py">
            <div class="container">
                <h2 class="text-center mb-2 display-6">{{ $eventsSectionTitle }}</h2>
                <p class="text-center mb-5 text-body">{{ $eventsSectionSubtitle }}</p>
                <div class="row gy-4 mt-2">
                    @forelse($eventItems as $item)
                        <div class="col-6 col-md-3">
                            <div class="card card-hover h-100 shadow-sm border-0">
                                <img src="{{ $item->image }}" class="card-img-top" alt="{{ $item->name }}">
                                <div class="card-body text-center">
                                    <h5 class="card-title fw-semibold text-heading">{{ $item->name }}</h5>
                                    <a href="{{ route('category.show', $item->slug) }}"
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
                    <a href="{{ $eventsSectionButtonLink }}"
                        class="btn btn-primary waves-effect waves-light">{{ $eventsSectionButtonText }}</a>
                </div>
            </div>
        </section>
    @endif

    <!-- 🚛 Nutzfahrzeuge & Freizeitfahrzeuge -->
    @if($vehiclesSectionEnabled)
        <section class="section-py bg-body">
            <div class="container">
                <h2 class="text-center mb-2 display-6">{{ $vehiclesSectionTitle }}</h2>
                <p class="text-center mb-5 text-body">{{ $vehiclesSectionSubtitle }}</p>
                <div class="row gy-4 mt-2">
                    @forelse($vehicles as $vehicle)
                        <div class="col-12 col-md-4">
                            <div class="card card-hover h-100 shadow-sm border-0">
                                <img src="{{ $vehicle->image }}" class="card-img-top" alt="{{ $vehicle->name }}">
                                <div class="card-body text-center">
                                    <h5 class="card-title fw-semibold text-heading">{{ $vehicle->name }}</h5>
                                    <p class="card-text text-body">{{ $vehicle->description }}</p>
                                    <a href="{{ route('category.show', $vehicle->slug) }}"
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
                    <a href="{{ $vehiclesSectionButtonLink }}"
                        class="btn btn-primary waves-effect waves-light">{{ $vehiclesSectionButtonText }}</a>
                </div>
            </div>
        </section>
    @endif

    <!-- 🏗️ Baumaschinen & Bauzubehör -->
    @if($constructionSectionEnabled)
        <section class="section-py">
            <div class="container">
                <h2 class="text-center mb-2 display-6">{{ $constructionSectionTitle }}</h2>
                <p class="text-center mb-5 text-body">{{ $constructionSectionSubtitle }}</p>
                <div class="row gy-4 mt-2">
                    @forelse($constructionTools as $tool)
                        <div class="col-12 col-md-4">
                            <div class="card card-hover h-100 shadow-sm border-0">
                                <img src="{{ $tool->image }}" class="card-img-top" alt="{{ $tool->name }}">
                                <div class="card-body text-center">
                                    <h5 class="card-title fw-semibold text-heading">{{ $tool->name }}</h5>
                                    <p class="card-text text-body">{{ $tool->description }}</p>
                                    <a href="{{ route('category.show', $tool->slug) }}"
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
                    <a href="{{ $constructionSectionButtonLink }}"
                        class="btn btn-primary waves-effect waves-light">{{ $constructionSectionButtonText }}</a>
                </div>
            </div>
        </section>
    @endif
@endsection