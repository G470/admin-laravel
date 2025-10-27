@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Baumaschinen & Bauzubehör')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Baumaschinen & Bauzubehör</h1>
            <p class="lead mb-5">Professionelles Equipment für dein Bauprojekt</p>
        </div>
    </div>

    <div class="row gy-4">
        @forelse($constructionCategories as $category)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('category.show', $category->slug) }}" class="text-decoration-none">
                    <div class="card card-hover h-100 shadow-sm border-0">
                        @if($category->image)
                            <img src="{{ $category->image }}" class="card-img-top" alt="{{ $category->name }}">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="ti ti-tool ti-xl text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body text-center">
                            <h5 class="card-title fw-semibold text-heading">{{ $category->name }}</h5>
                            @if($category->description)
                                <p class="card-text small text-muted">{{ Str::limit($category->description, 80) }}</p>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="ti ti-info-circle me-2"></i>
                    Derzeit sind keine Baumaschinen verfügbar.
                </div>
            </div>
        @endforelse
    </div>

    <div class="row mt-5">
        <div class="col-12 text-center">
            <a href="{{ route('search') }}" class="btn btn-outline-primary">
                <i class="ti ti-search me-2"></i>Alle Kategorien durchsuchen
            </a>
        </div>
    </div>
</div>
@endsection
