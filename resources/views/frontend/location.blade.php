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
            <li class="breadcrumb-item"><a href="{{ route('cities.overview') }}">Standorte</a></li>
            <li class="breadcrumb-item active">{{ $locationData['name'] }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">Vermieten in {{ $locationData['name'] }}</h1>
                    <p class="text-muted mb-0">
                        {{ $rentals->total() }} {{ Str::plural('Angebot', $rentals->total()) }} gefunden
                        @if($locationData['state'])
                            <span class="badge bg-light text-dark ms-2">{{ $locationData['state'] }}</span>
                        @endif
                        <span class="badge bg-light text-dark ms-2">{{ $locationData['country'] }}</span>
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

            <!-- Top Categories -->
            @if($topCategories->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Beliebte Kategorien in {{ $locationData['name'] }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($topCategories->take(12) as $category)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <a href="{{ route('category.location.show', [
                                'category1' => $category->slug,
                                'location' => Str::slug($locationData['city'])
                            ]) }}" class="text-decoration-none">
                                <div class="d-flex justify-content-between align-items-center p-3 border rounded hover-shadow">
                                    <div>
                                        <strong>{{ $category->name }}</strong>
                                        <br><small class="text-muted">{{ $category->rentals_count }} Angebote</small>
                                    </div>
                                    <i class="ti ti-arrow-right text-primary"></i>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- All Rentals -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Alle Angebote in {{ $locationData['name'] }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($rentals as $rental)
                        <div class="col-md-6 col-xl-4 mb-4">
                            <div class="card h-100">
                                @if($rental->images && $rental->images->count() > 0)
                                <img src="{{ $rental->images->first()->url }}" class="card-img-top" alt="{{ $rental->title }}" style="height: 200px; object-fit: cover;">
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">{{ $rental->title }}</h6>
                                    <p class="card-text flex-grow-1 small">{{ Str::limit($rental->description, 80) }}</p>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted small">
                                                <i class="ti ti-category"></i> {{ $rental->category->name ?? 'Kategorie' }}
                                            </span>
                                            <span class="fw-bold small">
                                                ab {{ number_format($rental->price_range_day, 2) }} €/Tag
                                            </span>
                                        </div>
                                        <a href="{{ route('rental.show', $rental->id) }}" class="btn btn-primary btn-sm w-100">
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
                                <h4 class="mt-3">Keine Angebote gefunden</h4>
                                <p class="text-muted">In {{ $locationData['name'] }} sind aktuell keine Angebote verfügbar.</p>
                                <a href="{{ route('cities.overview') }}" class="btn btn-primary">Andere Standorte entdecken</a>
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
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">{{ $locationData['name'] }} im Überblick</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Angebote total:</span>
                        <strong>{{ $rentals->total() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Kategorien:</span>
                        <strong>{{ $topCategories->count() }}</strong>
                    </div>
                    @if($locationData['state'])
                    <div class="d-flex justify-content-between mb-2">
                        <span>Region:</span>
                        <strong>{{ $locationData['state'] }}</strong>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between">
                        <span>Land:</span>
                        <strong>{{ $locationData['country'] }}</strong>
                    </div>
                </div>
            </div>

            <!-- Top Categories Widget -->
            @if($topCategories->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Top Kategorien</h6>
                </div>
                <div class="card-body">
                    @foreach($topCategories->take(8) as $category)
                    <a href="{{ route('category.location.show', [
                        'category1' => $category->slug,
                        'location' => Str::slug($locationData['city'])
                    ]) }}" class="d-block text-decoration-none mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>{{ $category->name }}</span>
                            <small class="text-muted">{{ $category->rentals_count }}</small>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Navigation -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Navigation</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('cities.overview') }}" class="d-block text-decoration-none mb-2">
                        <i class="ti ti-list me-2"></i>Alle Standorte
                    </a>
                    <a href="{{ route('categories.index') }}" class="d-block text-decoration-none mb-2">
                        <i class="ti ti-category me-2"></i>Alle Kategorien
                    </a>
                    <a href="{{ route('home') }}" class="d-block text-decoration-none">
                        <i class="ti ti-home me-2"></i>Startseite
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
                <h5 class="modal-title">Filter für {{ $locationData['name'] }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="GET">
                    <div class="mb-3">
                        <label class="form-label">Kategorie</label>
                        <select name="category" class="form-select">
                            <option value="">Alle Kategorien</option>
                            @foreach($topCategories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }} ({{ $category->rentals_count }})
                            </option>
                            @endforeach
                        </select>
                    </div>
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

<style>
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transition: box-shadow 0.15s ease-in-out;
}
</style>
@endsection
