<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories using Livewire component
     */
    public function index()
    {
        return view('content.admin.categories');
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        $categories = Category::whereNull('parent_id')->ordered()->get();
        return view('content.admin.categories-create', compact('categories'));
    }

    /**
     * Store a newly created category (if not using Livewire)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:online,offline',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);
        
        // Set order for new category
        $maxOrder = Category::where('parent_id', $validated['parent_id'])->max('order') ?? 0;
        $validated['order'] = $maxOrder + 10;

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategorie wurde erfolgreich erstellt.');
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::whereNull('parent_id')->where('id', '!=', $id)->ordered()->get();
        return view('content.admin.categories-edit', compact('category', 'categories'));
    }

    /**
     * Update the specified category (if not using Livewire)
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:online,offline',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Kategorie wurde erfolgreich aktualisiert.');
    }

    /**
     * Remove the specified category (if not using Livewire)
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        if ($category->children()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Diese Kategorie kann nicht gelöscht werden, da sie Unterkategorien enthält.');
        }

        if ($category->rentals()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Diese Kategorie kann nicht gelöscht werden, da sie Vermietungsobjekte enthält.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategorie wurde erfolgreich gelöscht.');
    }
}
