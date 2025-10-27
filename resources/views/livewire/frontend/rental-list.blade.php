<div class="rental-list">
    @if($hasSubcategories)
        <div class="alert alert-info mb-4">
            <div class="d-flex align-items-center">
                <i class="ti ti-info-circle me-2"></i>
                <div>
                    <strong>Erweiterte Suche:</strong>
                    Es werden Artikel aus der Kategorie "{{ $category->name }}" und allen Unterkategorien (rekursiv)
                    angezeigt.
                    <small class="d-block text-muted mt-1">
                        Durchsuchte Kategorien: {{ count($categoryIds) }}
                        ({{ $categoryHierarchy['main_category'] }} + {{ $categoryHierarchy['subcategories'] }}
                        Unterkategorien + {{ $categoryHierarchy['sub_subcategories'] }} Unter-Unterkategorien)
                    </small>

                    @if($categoryHierarchy['subcategories'] > 0)
                        <div class="mt-2">
                            <small class="text-muted">
                                <strong>Kategorie-Struktur:</strong>
                                @foreach($categoryHierarchy['category_tree'] as $child)
                                    <span class="badge bg-light text-dark me-1">
                                        {{ $child['name'] }}
                                        @if($child['subcategories'] > 0)
                                            <span class="badge bg-secondary ms-1">{{ $child['subcategories'] }}</span>
                                        @endif
                                    </span>
                                @endforeach
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if($hasSubcategories && count($rentalsByCategory) > 1)
        {{-- Show grouped results when there are multiple categories --}}
        @foreach($rentalsByCategory as $categoryName => $categoryRentals)
            <div class="mb-4">
                <h6 class="text-primary mb-3">
                    <i class="ti ti-category me-2"></i>
                    {{ $categoryName }}
                    <span class="badge bg-primary ms-2">{{ count($categoryRentals) }}</span>
                </h6>
                @foreach($categoryRentals as $rental)
                    <div class="mb-3">
                        @include('components.rental-card', ['rental' => $rental])
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
        {{-- Show flat list when no subcategories or only one category --}}
        @forelse($rentals as $rental)
            <div class="mb-4">
                @include('components.rental-card', ['rental' => $rental])
            </div>
        @empty
            <div class="mb-4">
                <div class="text-center py-5">
                    <i class="ti ti-search-off ti-xl text-muted mb-3"></i>
                    <h5 class="text-muted">Keine Artikel in dieser Kategorie</h5>
                    <p class="text-muted">
                        @if(!empty($filters))
                            Keine Artikel gefunden mit den ausgewählten Filtern.
                        @else
                            In der Kategorie "{{ $category->name }}" sind derzeit keine Artikel verfügbar.
                        @endif
                    </p>
                </div>
            </div>
        @endforelse
    @endif

    <!-- Pagination -->
    @if($rentals->hasPages())
        <nav class="mt-4">
            {{ $rentals->links() }}
        </nav>
    @endif
</div>