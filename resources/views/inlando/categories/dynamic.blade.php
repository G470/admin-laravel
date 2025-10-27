@php
// include string helper for Str::limit
use Illuminate\Support\Str;
@endphp
@php
$config = [
    'title' => 'Kategorien',
    'description' => 'Entdecken Sie unsere vielfältigen Kategorien von Mietartikeln.',
    'icon' => 'ti ti-category',
];
@extends('layouts/contentNavbarLayoutFrontend')

@section('title', $config['title'])

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">{{ $config['title'] }}</h1>
            <p class="lead mb-5">{{ $config['description'] }}</p>
        </div>
    </div>

    <div class="row gy-4">
        @forelse($categories as $category)
            <div class="col-6 col-md-4 col-lg-3">
                <a href="{{ route('category.show', $category->slug) }}" class="text-decoration-none">
                    <div class="card card-hover h-100 shadow-sm border-0">
                        @if($category->image)
                            <img src="{{ $category->image }}" class="card-img-top" alt="{{ $category->name }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="{{ $config['icon'] }} ti-xl text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body text-center">
                            <h5 class="card-title fw-semibold text-heading">{{ $category->name }}</h5>
                            @if($category->description)
                                <p class="card-text small text-muted">{{ Str::limit($category->description, 80) }}</p>
                            @endif
                            <div class="mt-2">
                                <span class="badge bg-label-primary">
                                    {{ $category->rentals()->count() }} Artikel
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="ti ti-info-circle me-2"></i>
                    Derzeit sind keine {{ strtolower($config['title']) }} verfügbar.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination if needed -->
    @if($categories instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="row mt-5">
            <div class="col-12 d-flex justify-content-center">
                {{ $categories->links() }}
            </div>
        </div>
    @endif

    <div class="row mt-5">
        <div class="col-12 text-center">
            <a href="{{ route('search') }}" class="btn btn-outline-primary me-3">
                <i class="ti ti-search me-2"></i>Alle Kategorien durchsuchen
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="ti ti-home me-2"></i>Zur Startseite
            </a>
        </div>
    </div>
</div>
@endsection
