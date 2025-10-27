<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class Categories extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $showModal = false;
    public $editMode = false;
    public $categoryId;
    public $name = '';
    public $slug = '';
    public $parent_id = '';
    public $status = 'online';
    public $description = '';
    public $meta_title = '';
    public $meta_description = '';
    public $default_text_content = '';
    public $category_image = '';
    public $category_image_upload = null;
    public $current_category_image = '';
    public $form_template_display_style = 'show_only_rentals';
    public $expandedCategories = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'parent_id' => 'nullable|exists:categories,id',
        'status' => 'required|in:online,offline',
        'description' => 'nullable|string',
        'meta_title' => 'nullable|string|max:60',
        'meta_description' => 'nullable|string|max:160',
        'default_text_content' => 'nullable|string',
        'category_image_upload' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'form_template_display_style' => 'required|in:show_only_rentals,show_category_details_and_subcategories,show_category_details_and_rentals',
    ];

    protected $listeners = [
        'deleteCategory',
        'updateSortOrder',
        'toggleExpand',
    ];

    public function mount()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage('page');
    }

    public function updatedSearch()
    {
        if (!empty($this->search)) {
            // When searching, expand all categories to show results
            $this->expandedCategories = Category::pluck('id')->toArray();
        } else {
            // Reset expanded state when clearing search
            $this->expandedCategories = [];
        }
    }

    public function render()
    {
        if (!empty($this->search)) {
            // Search mode: find all matching categories
            $categories = Category::where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('meta_title', 'like', '%' . $this->search . '%')
                    ->orWhere('meta_description', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
                ->with([
                    'parent',
                    'children.children' => function ($childQuery) {
                        $childQuery->ordered();
                    }
                ])
                ->ordered()
                ->paginate(15);
        } else {
            // Normal mode: show only root categories with hierarchies
            $categories = Category::with([
                'children.children' => function ($query) {
                    $query->ordered();
                }
            ])
                ->whereNull('parent_id')
                ->ordered()
                ->paginate(15);
        }

        // All categories for the parent selection dropdown
        $allCategories = Category::with([
            'children.children' => function ($query) {
                $query->ordered();
            }
        ])
            ->whereNull('parent_id')
            ->ordered()
            ->get();

        return view('livewire.admin.categories', [
            'categories' => $categories,
            'allCategories' => $allCategories,
        ]);
    }



    public function showCreateModal()
    {
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function showEditModal($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->parent_id = $category->parent_id;
        $this->status = $category->status;
        $this->description = $category->description ?? '';
        $this->meta_title = $category->meta_title ?? '';
        $this->meta_description = $category->meta_description ?? '';
        $this->default_text_content = $category->default_text_content ?? '';
        $this->category_image = $category->category_image ?? '';
        $this->current_category_image = $category->category_image ?? '';
        $this->category_image_upload = null;
        $this->form_template_display_style = $category->form_template_display_style ?? 'show_only_rentals';
        $this->editMode = true;
        $this->showModal = true;
    }

    public function saveCategory()
    {
        $this->validate();

        // Handle image upload
        $imagePath = $this->category_image;
        if ($this->category_image_upload) {
            // Delete old image if it exists and we're editing
            if ($this->editMode && $this->current_category_image) {
                Storage::disk('public')->delete($this->current_category_image);
            }

            // Store new image
            $imagePath = $this->category_image_upload->store('categories', 'public');
        }

        $data = [
            'name' => $this->name,
            'slug' => $this->slug ?: \Str::slug($this->name),
            'parent_id' => $this->parent_id ?: null,
            'status' => $this->status == 'on' ? 'online' : ($this->status == 'online' ? 'online' : 'offline'),
            'description' => $this->description,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'default_text_content' => $this->default_text_content,
            'category_image' => $imagePath,
            'form_template_display_style' => $this->form_template_display_style,
        ];

        if ($this->editMode) {
            $category = Category::findOrFail($this->categoryId);
            $category->update($data);
            session()->flash('message', 'Kategorie erfolgreich aktualisiert.');
        } else {
            // Set order for new category
            $maxOrder = Category::where('parent_id', $data['parent_id'])->max('order') ?? 0;
            $data['order'] = $maxOrder + 10;

            Category::create($data);
            session()->flash('message', 'Kategorie erfolgreich erstellt.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleStatus($id)
    {
        $category = Category::findOrFail($id);
        $category->update([
            'status' => $category->status === 'online' ? 'offline' : 'online'
        ]);

        session()->flash('message', 'Status erfolgreich geändert.');
    }

    public function confirmDelete($id)
    {
        $this->dispatch('confirm-delete', ['id' => $id]);
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);

        // Check if category has children
        if ($category->children()->count() > 0) {
            session()->flash('error', 'Kategorie kann nicht gelöscht werden, da sie Unterkategorien enthält.');
            return;
        }

        // Check if category has rentals
        if ($category->rentals()->count() > 0) {
            session()->flash('error', 'Kategorie kann nicht gelöscht werden, da sie Vermietungen enthält.');
            return;
        }

        $category->delete();
        session()->flash('message', 'Kategorie erfolgreich gelöscht.');
    }

    public function updateSortOrder($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            Category::where('id', $id)->update(['order' => ($index + 1) * 10]);
        }

        session()->flash('message', 'Reihenfolge erfolgreich aktualisiert.');
    }

    public function toggleExpand($categoryId)
    {
        if (in_array($categoryId, $this->expandedCategories)) {
            // Remove from expanded list
            $this->expandedCategories = array_diff($this->expandedCategories, [$categoryId]);
        } else {
            // Add to expanded list
            $this->expandedCategories[] = $categoryId;
        }
    }

    public function isExpanded($categoryId)
    {
        return in_array($categoryId, $this->expandedCategories) || !empty($this->search);
    }

    public function updatedName()
    {
        if (empty($this->slug)) {
            $this->slug = \Str::slug($this->name);
        }
    }

    public function removeImage()
    {
        $this->category_image_upload = null;
        if ($this->editMode) {
            $this->category_image = '';
        }
        $this->resetValidation(['category_image_upload']);
    }

    private function resetForm()
    {
        $this->categoryId = null;
        $this->name = '';
        $this->slug = '';
        $this->parent_id = '';
        $this->status = 'online';
        $this->description = '';
        $this->meta_title = '';
        $this->meta_description = '';
        $this->default_text_content = '';
        $this->category_image = '';
        $this->category_image_upload = null;
        $this->current_category_image = '';
        $this->form_template_display_style = 'show_only_rentals';
        $this->resetValidation();
    }
}
