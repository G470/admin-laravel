@extends('layouts.contentNavbarLayout')

@section('title', $seoData['meta_title'] ?? $category->name)

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Kategorien</a></li>
            @if($category->parent)
                <li class="breadcrumb-item"><a href="{{ route('category.show', $category->parent->slug) }}">{{ $category->parent->name }}</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <!-- Category Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h1 class="mb-2">{{ $category->name }}</h1>
                            @if($category->description)
                                <p class="text-muted mb-0">{{ $category->description }}</p>
                            @endif
                        </div>
                        <div class="col-md-4 text-md-end">
                            @if($category->category_image)
                                <img src="{{ asset('storage/' . $category->category_image) }}" 
                                     alt="{{ $category->name }}" 
                                     class="img-fluid rounded" 
                                     style="max-height: 120px;">
                            @endif
                        </div>
                    </div>
                    
                    @if(isset($location) && $location)
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-map-pin text-primary me-2"></i>
                                <span class="fw-semibold">Standort: {{ $location->city }}</span>
                                @if($location->postcode)
                                    <span class="text-muted ms-1">({{ $location->postcode }})</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Category Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4>{{ $rentals->total() }}</h4>
                    <p class="mb-0">Verfügbare Artikel</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4>{{ $category->children()->count() }}</h4>
                    <p class="mb-0">Unterkategorien</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h4>{{ $category->rentals()->distinct('vendor_id')->count('vendor_id') }}</h4>
                    <p class="mb-0">Anbieter</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4>{{ $category->rentals()->avg('rental_price') ? '€' . number_format($category->rentals()->avg('rental_price'), 2) : 'N/A' }}</h4>
                    <p class="mb-0">⌀ Preis/Tag</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subcategories -->
    @if($category->children->count() > 0)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Unterkategorien</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($category->children as $subcategory)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                @if($subcategory->category_image)
                                                    <img src="{{ asset('storage/' . $subcategory->category_image) }}" 
                                                         alt="{{ $subcategory->name }}" 
                                                         class="me-3 rounded" 
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="ti ti-category text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $subcategory->name }}</h6>
                                                    <small class="text-muted">{{ $subcategory->rentals()->count() }} Artikel</small>
                                                </div>
                                            </div>
                                            @if($subcategory->description)
                                                <p class="text-muted small">{{ Str::limit($subcategory->description, 100) }}</p>
                                            @endif
                                            <a href="{{ route('category.show', $subcategory->slug) }}" class="btn btn-outline-primary btn-sm">
                                                Anzeigen
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Rental Listings -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Verfügbare Artikel</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="ti ti-filter me-1"></i>Filter
                        </button>
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="ti ti-sort-ascending me-1"></i>Sortieren
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($rentals->count() > 0)
                        <div class="row">
                            @foreach($rentals as $rental)
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card h-100">
                                        @if($rental->rental_images && count($rental->rental_images) > 0)
                                            <img src="{{ asset('storage/' . $rental->rental_images[0]) }}" 
                                                 class="card-img-top" alt="{{ $rental->rental_title }}"
                                                 style="height: 200px; object-fit: cover;">
                                        @else
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                                 style="height: 200px;">
                                                <i class="ti ti-photo text-muted" style="font-size: 3rem;"></i>
                                            </div>
                                        @endif
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $rental->rental_title }}</h6>
                                            <p class="card-text text-muted small">{{ Str::limit($rental->rental_description, 100) }}</p>
                                            <div class="d-flex justify-content-between align-items-end">
                                                <div>
                                                    <span class="fw-bold text-primary">€{{ number_format($rental->rental_price, 2) }}</span>
                                                    <small class="text-muted">/Tag</small>
                                                </div>
                                                <a href="{{ route('rental.show', $rental->id) }}" class="btn btn-primary btn-sm">
                                                    Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $rentals->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="ti ti-inbox text-muted mb-3" style="font-size: 4rem;"></i>
                            <h5 class="text-muted">Keine Artikel gefunden</h5>
                            <p class="text-muted">In dieser Kategorie sind derzeit keine Artikel verfügbar.</p>
                            @if($category->children->count() > 0)
                                <p class="text-muted">Schauen Sie sich die Unterkategorien oben an.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include SEO Content at the bottom -->
@include('partials.category-seo-content')
@endsection

<!-- Additional JavaScript for category page functionality -->
@section('page-script')
<script>
$(document).ready(function() {
    // Filter and sort functionality can be added here
    console.log('Category page loaded:', '{{ $category->name }}');
    
    // Track category view for analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', 'view_item_list', {
            'item_list_id': 'category_{{ $category->id }}',
            'item_list_name': '{{ $category->name }}',
            'items': [
                @foreach($rentals as $rental)
                {
                    'item_id': '{{ $rental->id }}',
                    'item_name': '{{ $rental->rental_title }}',
                    'item_category': '{{ $category->name }}',
                    'price': {{ $rental->rental_price }},
                    'quantity': 1
                }@if(!$loop->last),@endif
                @endforeach
            ]
        });
    }
});
</script>
@endsection 