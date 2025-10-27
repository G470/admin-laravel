<?php

namespace App\Livewire\Vendor\Rentals;

use Livewire\Component;
use App\Models\Category;
use Livewire\Attributes\On;

class Categories extends Component
{
    public $isSearchMode = false;
    public $searchTerm = '';
    public $selectedCategory = null;
    public $selectedFirstLevel = null;
    public $selectedSecondLevel = null;
    public $selectedThirdLevel = null;

    public $firstLevelCategories = [];
    public $secondLevelCategories = [];
    public $thirdLevelCategories = [];
    public $filteredCategories = [];

    // Initial data from parent component
    public $initialData = [];
    public $categoryId = null;

    public function mount($categoryId = null)
    {
        $this->categoryId = $categoryId;

        // Load initial category if provided
        if ($categoryId) {
            $this->loadCategoryById($categoryId);
        }

        $this->loadFirstLevelCategories();
    }

    public function handleCategorySelected($categoryData)
    {
        // Handle category selection from external source
        if (is_array($categoryData) && isset($categoryData['id'])) {
            $this->loadCategoryById($categoryData['id']);
        } elseif (is_numeric($categoryData)) {
            $this->loadCategoryById($categoryData);
        }
    }

    public function loadFirstLevelCategories()
    {
        $this->firstLevelCategories = Category::whereNull('parent_id')
            ->online()
            ->ordered()
            ->get()
            ->toArray();
    }

    public function selectFirstLevel($categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category)
            return;

        $this->selectedFirstLevel = $category->toArray();
        $this->selectedSecondLevel = null;
        $this->selectedThirdLevel = null;
        $this->selectedCategory = null;

        // Load children
        $this->secondLevelCategories = $category->children()
            ->online()
            ->ordered()
            ->get()
            ->toArray();

        $this->thirdLevelCategories = [];

        // If no children, this is selectable
        if (empty($this->secondLevelCategories)) {
            $this->selectCategory($categoryId);
        }
    }

    public function selectSecondLevel($categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category)
            return;

        $this->selectedSecondLevel = $category->toArray();
        $this->selectedThirdLevel = null;
        $this->selectedCategory = null;

        // Load children
        $this->thirdLevelCategories = $category->children()
            ->online()
            ->ordered()
            ->get()
            ->toArray();

        // If no children, this is selectable
        if (empty($this->thirdLevelCategories)) {
            $this->selectCategory($categoryId);
        }
    }

    public function selectThirdLevel($categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category)
            return;

        $this->selectedThirdLevel = $category->toArray();
        $this->selectCategory($categoryId);
    }

    public function selectCategory($categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category)
            return;

        $this->selectedCategory = $category->toArray();

        // When selecting from search, also load the hierarchy to show the path
        $this->loadCategoryById($categoryId);

        // Clear search when category is selected
        if ($this->isSearchMode) {
            $this->searchTerm = '';
            $this->filteredCategories = [];
        }

        // Dispatch event to parent component (Livewire 3 syntax)
        $this->dispatch('categorySelected', $category->toArray());

        // Also dispatch to browser for debugging and update the hidden input
        $this->js("
            console.log('📤 Dispatching categorySelected from Livewire:', " . json_encode($category->toArray()) . ");
            
            // Update the hidden category_id input
            const categoryInput = document.getElementById('category_id');
            if (categoryInput) {
                categoryInput.value = " . $categoryId . ";
                console.log('Updated category_id input to:', " . $categoryId . ");
            }
            
            window.dispatchEvent(new CustomEvent('categorySelectedFromLivewire', {
                detail: " . json_encode($category->toArray()) . "
            }));
        ");
    }

    public function removeCategorySelection()
    {
        $this->selectedCategory = null;
        $this->selectedFirstLevel = null;
        $this->selectedSecondLevel = null;
        $this->selectedThirdLevel = null;
        $this->secondLevelCategories = [];
        $this->thirdLevelCategories = [];

        // Dispatch event to parent component (Livewire 3 syntax)
        $this->dispatch('categoryRemoved');

        // Clear the hidden category_id input
        $this->js("
            const categoryInput = document.getElementById('category_id');
            if (categoryInput) {
                categoryInput.value = '';
                console.log('Cleared category_id input');
            }
        ");
    }

    public function updatedSearchTerm()
    {
        if (strlen($this->searchTerm) >= 2) {
            $categories = Category::where('name', 'LIKE', '%' . $this->searchTerm . '%')
                ->online()
                ->ordered()
                ->with(['parent', 'parent.parent'])
                ->get();

            $this->filteredCategories = $categories->map(function ($category) {
                $categoryArray = $category->toArray();
                $categoryArray['full_path'] = $this->buildCategoryPath($category);
                return $categoryArray;
            })->toArray();
        } else {
            $this->filteredCategories = [];
        }
    }

    private function buildCategoryPath($category)
    {
        $path = [];
        $current = $category;

        while ($current) {
            array_unshift($path, $current->name);
            $current = $current->parent;
        }

        return implode(' > ', $path);
    }

    private function loadCategoryById($categoryId)
    {
        $category = Category::find($categoryId);
        if (!$category) {
            return;
        }

        $this->selectedCategory = $category->toArray();

        // Load the hierarchy
        $path = [];
        $current = $category;

        while ($current) {
            array_unshift($path, $current);
            $current = $current->parent;
        }

        // Set the levels based on the path without triggering selection events
        if (count($path) >= 1) {
            $this->selectedFirstLevel = $path[0]->toArray();
            // Load second level categories directly
            $this->secondLevelCategories = $path[0]->children()
                ->online()
                ->ordered()
                ->get()
                ->toArray();
        }

        if (count($path) >= 2) {
            $this->selectedSecondLevel = $path[1]->toArray();
            // Load third level categories directly
            $this->thirdLevelCategories = $path[1]->children()
                ->online()
                ->ordered()
                ->get()
                ->toArray();
        }

        if (count($path) >= 3) {
            $this->selectedThirdLevel = $path[2]->toArray();
        }

        // Dispatch event to notify parent component about initial category
        if ($this->categoryId) {
            $this->dispatch('categorySelected', $this->selectedCategory);

            // Update the hidden input when loading initial category
            $this->js("
                const categoryInput = document.getElementById('category_id');
                if (categoryInput) {
                    categoryInput.value = " . $this->categoryId . ";
                    console.log('Set initial category_id input to:', " . $this->categoryId . ");
                }
            ");
        }
    }

    public function render()
    {
        return view('livewire.vendor.rentals.categories');
    }
}
