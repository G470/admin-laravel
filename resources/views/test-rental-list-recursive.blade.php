@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'RentalList Rekursive Test')

@section('content')
    <section class="section-py">
        <div class="container">
            <h1 class="mb-4">RentalList Rekursive Unterkategorien Test</h1>

            <div class="row">
                <div class="col-12 mb-4">
                    <h3>Kategorien mit rekursiven Unterkategorien und Vermietungen</h3>
                    <p class="text-muted">Test der erweiterten RentalList-Funktionalität mit rekursiver
                        Unter-Unterkategorien-Unterstützung.</p>

                    @php
                        // Kategorien mit rekursiven Unterkategorien und Vermietungen laden
                        $categoriesWithRecursiveRentals = \App\Models\Category::with([
                            'children' => function ($query) {
                                $query->where('status', 'online')
                                    ->with([
                                        'children' => function ($subQuery) {
                                            $subQuery->where('status', 'online');
                                        }
                                    ]);
                            },
                            'rentals' => function ($query) {
                                $query->where('status', 'active');
                            }
                        ])
                            ->where('status', 'online')
                            ->whereHas('children.children') // Nur Kategorien mit Unter-Unterkategorien
                            ->orderBy('name')
                            ->limit(3)
                            ->get();
                    @endphp

                    @if($categoriesWithRecursiveRentals->count() > 0)
                        @foreach($categoriesWithRecursiveRentals as $category)
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
                                            <h6>Rekursive Kategorie-Struktur:</h6>
                                            @if($category->children->count() > 0)
                                                <div class="category-tree">
                                                    @foreach($category->children as $child)
                                                        <div class="category-level-1 mb-2">
                                                            <div class="d-flex align-items-center">
                                                                <i class="ti ti-chevron-right me-2 text-primary"></i>
                                                                <strong>{{ $child->name }}</strong>
                                                                @php
                                                                    $childRentalCount = \App\Models\Rental::where('category_id', $child->id)
                                                                        ->where('status', 'active')
                                                                        ->count();
                                                                @endphp
                                                                @if($childRentalCount > 0)
                                                                    <span class="badge bg-success ms-2">{{ $childRentalCount }}</span>
                                                                @endif
                                                            </div>

                                                            @if($child->children->count() > 0)
                                                                <div class="category-level-2 ms-4 mt-1">
                                                                    @foreach($child->children as $subChild)
                                                                        <div class="d-flex align-items-center mb-1">
                                                                            <i class="ti ti-chevron-right me-2 text-secondary"></i>
                                                                            <span>{{ $subChild->name }}</span>
                                                                            @php
                                                                                $subChildRentalCount = \App\Models\Rental::where('category_id', $subChild->id)
                                                                                    ->where('status', 'active')
                                                                                    ->count();
                                                                            @endphp
                                                                            @if($subChildRentalCount > 0)
                                                                                <span class="badge bg-info ms-2">{{ $subChildRentalCount }}</span>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted">Keine Unterkategorien vorhanden.</p>
                                            @endif
                                        </div>
                                        <div class="col-md-8">
                                            <h6>Vermietungen (rekursiv inkl. Unter-Unterkategorien):</h6>
                                            @php
                                                // Rekursiv alle Kategorie-IDs sammeln
                                                function getRecursiveCategoryIds($categoryId)
                                                {
                                                    $categoryIds = [$categoryId];

                                                    $children = \App\Models\Category::where('parent_id', $categoryId)
                                                        ->where('status', 'online')
                                                        ->get();

                                                    foreach ($children as $child) {
                                                        $categoryIds[] = $child->id;
                                                        $subCategoryIds = getRecursiveCategoryIds($child->id);
                                                        $categoryIds = array_merge($categoryIds, $subCategoryIds);
                                                    }

                                                    return array_unique($categoryIds);
                                                }

                                                $allCategoryIds = getRecursiveCategoryIds($category->id);

                                                // Get rentals from all categories recursively
                                                $allRentals = \App\Models\Rental::whereIn('category_id', $allCategoryIds)
                                                    ->where('status', 'active')
                                                    ->with(['category', 'location', 'user'])
                                                    ->orderBy('created_at', 'desc')
                                                    ->limit(8)
                                                    ->get();

                                                // Group by category with hierarchy info
                                                $groupedRentals = [];
                                                foreach ($allRentals as $rental) {
                                                    $categoryName = $rental->category->name ?? 'Unbekannte Kategorie';
                                                    $categoryLevel = $rental->category->parent_id ?
                                                        ($rental->category->parent->parent_id ? 'sub-sub' : 'sub') : 'main';

                                                    if (!isset($groupedRentals[$categoryName])) {
                                                        $groupedRentals[$categoryName] = [
                                                            'rentals' => [],
                                                            'level' => $categoryLevel
                                                        ];
                                                    }

                                                    $groupedRentals[$categoryName]['rentals'][] = $rental;
                                                }
                                            @endphp

                                            @if($allRentals->count() > 0)
                                                <div class="alert alert-success mb-3">
                                                    <strong>Rekursive Suche erfolgreich!</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        Durchsuchte Kategorien: {{ count($allCategoryIds) }}
                                                        ({{ $category->name }} + {{ count($allCategoryIds) - 1 }} Unterkategorien
                                                        rekursiv)
                                                    </small>
                                                </div>

                                                @foreach($groupedRentals as $categoryName => $categoryData)
                                                    <div class="mb-3">
                                                        <h6 class="mb-2">
                                                            @if($categoryData['level'] === 'main')
                                                                <i class="ti ti-category me-2 text-primary"></i>
                                                            @elseif($categoryData['level'] === 'sub')
                                                                <i class="ti ti-category me-2 text-success"></i>
                                                            @else
                                                                <i class="ti ti-category me-2 text-info"></i>
                                                            @endif
                                                            {{ $categoryName }}
                                                            <span class="badge bg-primary ms-2">{{ count($categoryData['rentals']) }}</span>
                                                            @if($categoryData['level'] === 'sub')
                                                                <span class="badge bg-success ms-1">Unterkategorie</span>
                                                            @elseif($categoryData['level'] === 'sub-sub')
                                                                <span class="badge bg-info ms-1">Unter-Unterkategorie</span>
                                                            @endif
                                                        </h6>
                                                        @foreach($categoryData['rentals'] as $rental)
                                                            <div class="card mb-2">
                                                                <div class="card-body p-3">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <div>
                                                                            <h6 class="mb-1">{{ $rental->title }}</h6>
                                                                            <small class="text-muted">
                                                                                von
                                                                                {{ $rental->user->company_name ?? $rental->user->name ?? 'Unbekannt' }}
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
                                                <p class="text-muted">Keine Vermietungen in dieser Kategorie und rekursiven
                                                    Unterkategorien gefunden.</p>
                                            @endif

                                            <div class="mt-3">
                                                <a href="{{ route('category.show', $category->slug) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="ti ti-eye me-2"></i>
                                                    Alle Vermietungen anzeigen (rekursiv)
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <h5>Keine Kategorien mit rekursiven Unterkategorien gefunden</h5>
                            <p>Es wurden keine Kategorien mit Unter-Unterkategorien und aktiven Vermietungen in der Datenbank
                                gefunden.</p>
                        </div>
                    @endif
                </div>

                <div class="col-12">
                    <h3>Rekursive Kategorie-Statistiken</h3>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-primary">{{ \App\Models\Rental::where('status', 'active')->count() }}
                                    </h4>
                                    <p class="mb-0">Aktive Vermietungen</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-success">
                                        {{ \App\Models\Category::whereHas('children.children')->count() }}</h4>
                                    <p class="mb-0">Kategorien mit Unter-Unterkategorien</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-info">
                                        {{ \App\Models\Category::whereNotNull('parent_id')->whereHas('children')->count() }}
                                    </h4>
                                    <p class="mb-0">Unterkategorien mit Kindern</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h4 class="text-warning">
                                        {{ \App\Models\Category::whereNotNull('parent_id')->whereNull('parent_id')->count() }}
                                    </h4>
                                    <p class="mb-0">Endkategorien (ohne Kinder)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <h3>Rekursive Kategorie-Hierarchie Test</h3>
                    <div class="card">
                        <div class="card-body">
                            <h6>Test der rekursiven Kategorie-ID-Sammlung:</h6>
                            @php
                                // Test der rekursiven Funktion
                                function testRecursiveCategoryIds($categoryId, $depth = 0)
                                {
                                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $depth);
                                    $category = \App\Models\Category::find($categoryId);

                                    if (!$category)
                                        return '';

                                    $output = $indent . "• {$category->name} (ID: {$category->id})<br>";

                                    $children = \App\Models\Category::where('parent_id', $categoryId)
                                        ->where('status', 'online')
                                        ->get();

                                    foreach ($children as $child) {
                                        $output .= testRecursiveCategoryIds($child->id, $depth + 1);
                                    }

                                    return $output;
                                }

                                // Test mit einer Kategorie, die Unterkategorien hat
                                $testCategory = \App\Models\Category::whereHas('children')
                                    ->where('status', 'online')
                                    ->first();
                            @endphp

                            @if($testCategory)
                                <div class="mt-3">
                                    <strong>Beispiel-Hierarchie für "{{ $testCategory->name }}":</strong>
                                    <div class="mt-2 p-3 bg-light rounded">
                                        {!! testRecursiveCategoryIds($testCategory->id) !!}
                                    </div>
                                </div>
                            @else
                                <p class="text-muted">Keine Kategorie mit Unterkategorien für Test gefunden.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection