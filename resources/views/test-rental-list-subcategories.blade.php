@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'RentalList Subcategories Test')

@section('content')
    <section class="section-py">
        <div class="container">
            <h1 class="mb-4">RentalList mit Unterkategorien Test</h1>
            
            <div class="row">
                <div class="col-12 mb-4">
                    <h3>Kategorien mit Unterkategorien und Vermietungen</h3>
                    <p class="text-muted">Test der erweiterten RentalList-Funktionalität, die auch Vermietungen aus Unterkategorien anzeigt.</p>
                    
                    @php
                        // Kategorien mit Unterkategorien und Vermietungen laden
                        $categoriesWithRentals = \App\Models\Category::with(['children' => function ($query) {
                            $query->where('status', 'online');
                        }, 'rentals' => function ($query) {
                            $query->where('status', 'active');
                        }])
                        ->where('status', 'online')
                        ->whereHas('children')
                        ->orderBy('name')
                        ->limit(3)
                        ->get();
                    @endphp
                    
                    @if($categoriesWithRentals->count() > 0)
                        @foreach($categoriesWithRentals as $category)
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5>{{ $category->name }}</h5>
                                    <small class="text-muted">
                                        {{ $category->children->count() }} Unterkategorien | 
                                        {{ $category->rentals->count() }} eigene Vermietungen
                                    </small>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6>Unterkategorien:</h6>
                                            @if($category->children->count() > 0)
                                                <ul class="list-unstyled">
                                                    @foreach($category->children as $subCategory)
                                                        @php
                                                            $rentalCount = \App\Models\Rental::where('category_id', $subCategory->id)
                                                                ->where('status', 'active')
                                                                ->count();
                                                        @endphp
                                                        <li class="mb-2">
                                                            <a href="{{ route('category.show', $subCategory->slug) }}" 
                                                               class="text-decoration-none">
                                                                <i class="ti ti-chevron-right me-2 text-primary"></i>
                                                                {{ $subCategory->name }}
                                                                @if($rentalCount > 0)
                                                                    <span class="badge bg-success ms-2">{{ $rentalCount }}</span>
                                                                @endif
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <p class="text-muted">Keine Unterkategorien vorhanden.</p>
                                            @endif
                                        </div>
                                        <div class="col-md-8">
                                            <h6>Vermietungen (inkl. Unterkategorien):</h6>
                                            @php
                                                // Get all category IDs including subcategories
                                                $categoryIds = [$category->id];
                                                if ($category->children->count() > 0) {
                                                    $subcategoryIds = $category->children->pluck('id')->toArray();
                                                    $categoryIds = array_merge($categoryIds, $subcategoryIds);
                                                }
                                                
                                                // Get rentals from all categories
                                                $allRentals = \App\Models\Rental::whereIn('category_id', $categoryIds)
                                                    ->where('status', 'active')
                                                    ->with(['category', 'location', 'user'])
                                                    ->orderBy('created_at', 'desc')
                                                    ->limit(5)
                                                    ->get();
                                                
                                                // Group by category
                                                $groupedRentals = [];
                                                foreach ($allRentals as $rental) {
                                                    $categoryName = $rental->category->name ?? 'Unbekannte Kategorie';
                                                    if (!isset($groupedRentals[$categoryName])) {
                                                        $groupedRentals[$categoryName] = [];
                                                    }
                                                    $groupedRentals[$categoryName][] = $rental;
                                                }
                                            @endphp
                                            
                                            @if($allRentals->count() > 0)
                                                @foreach($groupedRentals as $categoryName => $categoryRentals)
                                                    <div class="mb-3">
                                                        <h6 class="text-primary">
                                                            <i class="ti ti-category me-2"></i>
                                                            {{ $categoryName }}
                                                            <span class="badge bg-primary ms-2">{{ count($categoryRentals) }}</span>
                                                        </h6>
                                                        @foreach($categoryRentals as $rental)
                                                            <div class="card mb-2">
                                                                <div class="card-body p-3">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <div>
                                                                            <h6 class="mb-1">{{ $rental->title }}</h6>
                                                                            <small class="text-muted">
                                                                                von {{ $rental->user->company_name ?? $rental->user->name ?? 'Unbekannt' }}
                                                                            </small>
                                                                        </div>
                                                                        <div class="text-end">
                                                                            <small class="text-muted">{{ $rental->category->name }}</small>
                                                                            @if($rental->location)
                                                                                <br><small class="text-muted">{{ $rental->location->city }}</small>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="text-muted">Keine Vermietungen in dieser Kategorie und Unterkategorien gefunden.</p>
                                            @endif
                                            
                                            <div class="mt-3">
                                                <a href="{{ route('category.show', $category->slug) }}" 
                                                   class="btn btn-primary btn-sm">
                                                    <i class="ti ti-eye me-2"></i>
                                                    Alle Vermietungen anzeigen
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <h5>Keine Kategorien mit Unterkategorien und Vermietungen gefunden</h5>
                            <p>Es wurden keine Kategorien mit Unterkategorien und aktiven Vermietungen in der Datenbank gefunden.</p>
                        </div>
                    @endif
                </div>
                
                <div class="col-12">
                    <h3>Statistiken</h3>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-primary">{{ \App\Models\Rental::where('status', 'active')->count() }}</h4>
                                    <p class="mb-0">Aktive Vermietungen</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-success">{{ \App\Models\Category::whereHas('rentals')->count() }}</h4>
                                    <p class="mb-0">Kategorien mit Vermietungen</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-info">{{ \App\Models\Category::whereHas('children')->whereHas('rentals')->count() }}</h4>
                                    <p class="mb-0">Hauptkategorien mit Vermietungen</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-warning">{{ \App\Models\Category::whereNotNull('parent_id')->whereHas('rentals')->count() }}</h4>
                                    <p class="mb-0">Unterkategorien mit Vermietungen</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection 