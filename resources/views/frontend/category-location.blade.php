@extends('layouts.app')

@section('title', $seoData['title'])
@section('meta_description', $seoData['description'])
@if(isset($seoData['keywords']))
@section('meta_keywords', $seoData['keywords'])
@endif

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Kategorien</a></li>
            @foreach($categoryPath as $index => $categorySlug)
                @if($loop->last)
                    <li class="breadcrumb-item active">{{ $category->name }}</li>
                @else
                    <li class="breadcrumb-item"><a href="#">{{ $categorySlug }}</a></li>
                @endif
            @endforeach
            <li class="breadcrumb-item active">{{ $locationData['name'] }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">{{ $category->name }} mieten in {{ $locationData['name'] }}</h1>
                    <p class="text-muted mb-0">
                        {{ $rentals->total() }} {{ Str::plural('Angebot', $rentals->total()) }} gefunden
                        @if($seoData['source'] !== 'system_fallback')
                            <span class="badge badge-primary ms-2">SEO optimiert</span>
                        @endif
                    </p>
                </div>
                <div>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- SEO Content -->
            @if(isset($seoData['content']) && $seoData['content'])
            <div class="card mb-4">
                <div class="card-body">
                    {!! $seoData['content'] !!}
                </div>
            </div>
            @endif

            <!-- Rentals Grid -->
            <div class="row">
                @forelse($rentals as $rental)
                <div class="col-md-6 col-xl-4 mb-4">
                    <div class="card h-100">
                        @if($rental->images->count() > 0)
                        <img src="{{ $rental->images->first()->url }}" class="card-img-top" alt="{{ $rental->title }}" style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $rental->title }}</h5>
                            <p class="card-text flex-grow-1">{{ Str::limit($rental->description, 100) }}</p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">
                                        <i class="ti ti-map-pin"></i> {{ $rental->location->city }}
                                    </span>
                                    <span class="fw-bold">
                                        ab {{ number_format($rental->price_range_day, 2) }} €/Tag
                                    </span>
                                </div>
                                <a href="{{ route('rental.show', $rental->id) }}" class="btn btn-primary w-100">
                                    Details ansehen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="ti ti-search-off display-1 text-muted"></i>
                        <h3 class="mt-3">Keine Angebote gefunden</h3>
                        <p class="text-muted">Für {{ $category->name }} in {{ $locationData['name'] }} sind aktuell keine Angebote verfügbar.</p>
                        <a href="{{ route('categories.index') }}" class="btn btn-primary">Alle Kategorien anzeigen</a>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($rentals->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $rentals->links() }}
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Related Categories -->
            @if($relatedCategories->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Ähnliche Kategorien</h6>
                </div>
                <div class="card-body">
                    @foreach($relatedCategories as $relatedCategory)
                    <a href="{{ route('category.location.show', [
                        'category1' => $relatedCategory->slug,
                        'location' => $locationData['slug'] ?? Str::slug($locationData['city'])
                    ]) }}" class="d-block text-decoration-none mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ $relatedCategory->name }}</span>
                            <small class="text-muted">
                                <i class="ti ti-arrow-right"></i>
                            </small>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Nearby Locations -->
            @if($nearbyLocations->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Auch verfügbar in</h6>
                </div>
                <div class="card-body">
                    @foreach($nearbyLocations->take(8) as $nearbyLocation)
                    <a href="{{ route('category.location.show', [
                        'category1' => $category->slug,
                        'location' => Str::slug($nearbyLocation->city)
                    ]) }}" class="d-block text-decoration-none mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ $nearbyLocation->city }}</span>
                            <small class="text-muted">
                                {{ $nearbyLocation->rentals_count }} 
                                {{ Str::plural('Angebot', $nearbyLocation->rentals_count) }}
                            </small>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Quick Links -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Schnellzugriff</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('location.show', Str::slug($locationData['city'])) }}" class="d-block text-decoration-none mb-2">
                        <i class="ti ti-map-pin me-2"></i>Alle Kategorien in {{ $locationData['name'] }}
                    </a>
                    <a href="{{ route('cities.overview') }}" class="d-block text-decoration-none mb-2">
                        <i class="ti ti-list me-2"></i>Alle Standorte
                    </a>
                    <a href="{{ route('categories.index') }}" class="d-block text-decoration-none">
                        <i class="ti ti-category me-2"></i>Alle Kategorien
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="GET">
                    <div class="mb-3">
                        <label class="form-label">Preis pro Tag</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="number" name="price_min" class="form-control" placeholder="Min" value="{{ request('price_min') }}">
                            </div>
                            <div class="col-6">
                                <input type="number" name="price_max" class="form-control" placeholder="Max" value="{{ request('price_max') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sortierung</label>
                        <select name="sort" class="form-select">
                            <option value="">Standard</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Preis aufsteigend</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Preis absteigend</option>
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Neueste zuerst</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Filter anwenden</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
