<?php

namespace App\Livewire\Frontend;

use App\Models\Rental;
use App\Models\Category;
use App\Helpers\DynamicRentalFields;
use Livewire\Component;
use Livewire\WithPagination;

class RentalList extends Component
{
    use WithPagination;

    public Category $category;
    public array $filters = [];
    public int $perPage = 12;

    protected $queryString = [
        'filters' => ['except' => []],
    ];

    protected $listeners = [
        'rentalFiltersUpdated' => 'updateFilters'
    ];

    public function mount(Category $category)
    {
        $this->category = $category;

        // Load children categories recursively if they exist
        if ($this->category->children->count() > 0) {
            $this->category->load([
                'children' => function ($query) {
                    $query->where('status', 'online')
                        ->with([
                            'children' => function ($subQuery) {
                                $subQuery->where('status', 'online');
                            }
                        ]);
                }
            ]);
        }
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->filters = [];
        $this->resetPage();
    }

    public function updateFilters($newFilters)
    {
        $this->filters = $newFilters;
        $this->resetPage();
    }

    public function render()
    {
        // Get category IDs including subcategories recursively
        $categoryIds = $this->getCategoryIds();

        $query = Rental::query()
            ->whereIn('category_id', $categoryIds)
            ->where('status', 'active')
            ->with(['location', 'category', 'user', 'additionalLocations']);

        // Apply dynamic field filters
        if (!empty($this->filters)) {
            $query = DynamicRentalFields::applyFilters($query, $this->filters);
        }

        $rentals = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Group rentals by category for better organization
        $rentalsByCategory = $this->groupRentalsByCategory($rentals);

        // Get category hierarchy information
        $categoryHierarchy = $this->getCategoryHierarchy();

        return view('livewire.frontend.rental-list', [
            'rentals' => $rentals,
            'rentalsByCategory' => $rentalsByCategory,
            'categoryIds' => $categoryIds,
            'hasSubcategories' => $this->category->children->count() > 0,
            'categoryHierarchy' => $categoryHierarchy
        ]);
    }

    /**
     * Get all category IDs including the current category and its subcategories recursively
     */
    private function getCategoryIds(): array
    {
        $categoryIds = [$this->category->id];

        // Add subcategory IDs recursively if they exist
        if ($this->category->children->count() > 0) {
            $subcategoryIds = $this->getCategoryIdsRecursive($this->category->id);
            $categoryIds = array_merge($categoryIds, $subcategoryIds);
        }

        return array_unique($categoryIds);
    }

    /**
     * Recursively get all subcategory IDs
     */
    private function getCategoryIdsRecursive($categoryId): array
    {
        $categoryIds = [];

        $children = Category::where('parent_id', $categoryId)
            ->where('status', 'online')
            ->get();

        foreach ($children as $child) {
            $categoryIds[] = $child->id;

            // Recursively get sub-subcategories
            $subCategoryIds = $this->getCategoryIdsRecursive($child->id);
            $categoryIds = array_merge($categoryIds, $subCategoryIds);
        }

        return $categoryIds;
    }

    /**
     * Group rentals by category for better organization
     */
    private function groupRentalsByCategory($rentals): array
    {
        $grouped = [];

        foreach ($rentals as $rental) {
            $categoryName = $rental->category->name ?? 'Unbekannte Kategorie';

            if (!isset($grouped[$categoryName])) {
                $grouped[$categoryName] = [];
            }

            $grouped[$categoryName][] = $rental;
        }

        return $grouped;
    }

    /**
     * Get category hierarchy information for display
     */
    private function getCategoryHierarchy(): array
    {
        $hierarchy = [
            'main_category' => $this->category->name,
            'total_categories' => 0,
            'subcategories' => 0,
            'sub_subcategories' => 0,
            'category_tree' => []
        ];

        if ($this->category->children->count() > 0) {
            $hierarchy['subcategories'] = $this->category->children->count();
            
            foreach ($this->category->children as $child) {
                $childInfo = [
                    'name' => $child->name,
                    'id' => $child->id,
                    'subcategories' => $child->children->count(),
                    'sub_subcategories' => []
                ];
                
                if ($child->children->count() > 0) {
                    foreach ($child->children as $subChild) {
                        $childInfo['sub_subcategories'][] = [
                            'name' => $subChild->name,
                            'id' => $subChild->id
                        ];
                        $hierarchy['sub_subcategories']++;
                    }
                }
                
                $hierarchy['category_tree'][] = $childInfo;
            }
        }

        $hierarchy['total_categories'] = 1 + $hierarchy['subcategories'] + $hierarchy['sub_subcategories'];
        
        return $hierarchy;
    }
}