@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Suchergebnisse')

@section('styles')
<style>
    .card-hover {
        transition: all 0.25s ease;
    }

    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .form-check-input:checked {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }
</style>
    {{-- JavaScript Integration for Dynamic Filters --}}
    <script>
        document.addEventListener('livewire:initialized', function () {
            // Listen for filter changes from the RentalFieldFilter component
            Livewire.on('searchWithFilters', (data) => {
                console.log('🔍 Search with filters triggered:', data);
                
                // Build URL with current search parameters plus dynamic filters
                const url = new URL(window.location.href);
                
                // Keep existing search parameters
                if (data.query) {
                    url.searchParams.set('query', data.query);
                }
                
                // Add dynamic field filters
                if (data.filters && Object.keys(data.filters).length > 0) {
                    // Encode filters as JSON in URL parameter
                    url.searchParams.set('filters', JSON.stringify(data.filters));
                } else {
                    url.searchParams.delete('filters');
                }
                
                // Reload page with new parameters
                window.location.href = url.toString();
            });

            // Listen for filter removal events
            Livewire.on('filterRemoved', (data) => {
                console.log('🗑️ Filter removed:', data);
                
                // Trigger a new search without the removed filter
                const currentFilters = @json($dynamicFilters ?? []);
                
                // Remove the specific filter
                if (currentFilters && currentFilters[data.fieldName]) {
                    delete currentFilters[data.fieldName];
                }
                
                // Trigger search with updated filters
                Livewire.dispatch('searchWithFilters', {
                    query: '{{ $query ?? "" }}',
                    filters: currentFilters
                });
            });
        });
    </script>
@endsection

@section('content')
    <!-- Search Form -->
    <section class="bg-body py-3 border-bottom">
        <div class="container">
            @livewire('search-form', [
                'query' => $query ?? '',
                'location' => $location ?? '',
                'dateFrom' => $dateFrom ?? '',
                'dateTo' => $dateTo ?? '',
                'dateRange' => isset($dateFrom) && isset($dateTo) ? 
                    \Carbon\Carbon::parse($dateFrom)->format('d.m.Y') . ' - ' . \Carbon\Carbon::parse($dateTo)->format('d.m.Y') : ''
            ])
        </div>
    </section>

    <!-- Search Results -->
    <section class="section-py">
        <div class="container">
            <h1 class="mb-2 fw-semibold">Suchergebnisse</h1>


            <div class="row mb-3">
                <div class="col">
                    @if(isset($rentals) && $rentals->count() > 0 && ($query || $location))
                        <p>Deine Suche nach <strong>"{{ $query ?? '' }}"</strong>
                        @if($location ?? false)
                            in <strong>{{ $location }}</strong>
                        @endif
                        @if(($dateFrom ?? false) && ($dateTo ?? false))
                            vom <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('d.m.Y') }}</strong> bis <strong>{{ \Carbon\Carbon::parse($dateTo)->format('d.m.Y') }}</strong>
                        @endif
                        </p>
                    @elseif(isset($rentals) && $rentals->count() > 0)
                        <p class="text-primary">
                            <i class="ti ti-info-circle me-1"></i>
                            Keine spezifischen Ergebnisse gefunden - hier sind alle verfügbaren Artikel
                        </p>
                    @endif
                </div>
            </div>

            <!-- Filter section -->
            <div class="row mb-4 g-4">
                
                <div class="col-12 col-lg-3">


            {{-- Dynamic Field Filters --}}
            @if(isset($categoryId) && $categoryId)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="ti ti-filter me-2"></i>Zusätzliche Filter
                                </h5>
                                @livewire('frontend.rental-field-filter', ['categoryId' => $categoryId], 'search-filters-' . $categoryId)
                            </div>
                        </div>
                    </div>
                </div>
            @endif




                    <div class="card shadow-none border">
                        <div class="card-header border-bottom">
                            <h5 class="card-title mb-0">Filter</h5>
                        </div>
                        
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Kategorie</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="category1">
                                    <label class="form-check-label" for="category1">
                                        Wohnmobile
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="category2">
                                    <label class="form-check-label" for="category2">
                                        Baumaschinen
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="category3">
                                    <label class="form-check-label" for="category3">
                                        Eventartikel
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Preis</label>
                                <div class="d-flex align-items-center">
                                    <input type="number" class="form-control form-control-sm" placeholder="Min €">
                                    <span class="mx-2">-</span>
                                    <input type="number" class="form-control form-control-sm" placeholder="Max €">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Bewertung</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="rating5">
                                    <label class="form-check-label" for="rating5">
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star-filled text-warning"></i>
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="rating4">
                                    <label class="form-check-label" for="rating4">
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star-filled text-warning"></i>
                                        <i class="ti ti-star text-warning"></i> & höher
                                    </label>
                                </div>
                            </div>

                            <button class="btn btn-primary w-100 waves-effect waves-light">Filter anwenden</button>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="m-0 text-body">{{ $rentals->total() ?? 0 }} Ergebnisse gefunden</p>
                        <div class="d-flex align-items-center">
                            <label class="me-2 text-body">Sortieren nach:</label>
                            <select class="form-select form-select-sm" style="width: auto;">
                                <option>Beliebtheit</option>
                                <option>Preis: aufsteigend</option>
                                <option>Preis: absteigend</option>
                                <option>Bewertung</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-4">
                        @forelse($rentals as $rental)
                            <div class="col-12">
                                <x-rental-card :rental="$rental" />
                            </div>
                        @empty
                            <!-- No results found and no rentals available at all -->
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="ti ti-search-off ti-xl text-muted mb-3"></i>
                                    <h5 class="text-muted">Derzeit keine Artikel verfügbar</h5>
                                    <p class="text-muted">Es sind momentan keine Artikel zum Mieten verfügbar. Schauen Sie später wieder vorbei.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if(isset($rentals) && $rentals->hasPages())
                    <nav class="mt-4">
                        {{ $rentals->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </nav>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
