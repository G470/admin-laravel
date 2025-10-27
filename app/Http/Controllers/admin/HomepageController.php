<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepageController extends Controller
{
    /**
     * Display the homepage settings form.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $homepageSettings = Setting::where('group', 'homepage')->get()->keyBy('key');
        $categoriesPageSettings = Setting::where('group', 'categories_page')->get()->keyBy('key');

        // Get all categories for selection
        $allCategories = Category::online()->ordered()->get();

        return view('content.admin.homepage', [
            'settings' => $homepageSettings,
            'categoriesPageSettings' => $categoriesPageSettings,
            'allCategories' => $allCategories
        ]);
    }

    /**
     * Update the homepage settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:500',
            'hero_button_text' => 'nullable|string|max:100',
            'hero_button_url' => 'nullable|string|max:255',
            'hero_image' => 'nullable|image|max:2048',
            'featured_section_title' => 'nullable|string|max:255',
            'featured_section_subtitle' => 'nullable|string|max:500',
            'testimonial_section_title' => 'nullable|string|max:255',
            'testimonial_section_subtitle' => 'nullable|string|max:500',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_keywords' => 'nullable|string|max:500',
        ]);

        // Save regular settings
        foreach ($validated as $key => $value) {
            if ($key !== 'hero_image') {
                Setting::updateOrCreate(
                    ['key' => $key, 'group' => 'homepage'],
                    ['value' => $value]
                );
            }
        }

        // Handle image upload if present
        if ($request->hasFile('hero_image')) {
            // Delete old image if it exists
            $oldImageSetting = Setting::where('key', 'hero_image')->where('group', 'homepage')->first();
            if ($oldImageSetting && $oldImageSetting->value) {
                Storage::disk('public')->delete($oldImageSetting->value);
            }

            // Upload new image
            $path = $request->file('hero_image')->store('homepage', 'public');
            Setting::updateOrCreate(
                ['key' => 'hero_image', 'group' => 'homepage'],
                ['value' => $path]
            );
        }

        return redirect()->route('admin.homepage.index')
            ->with('success', 'Homepage-Einstellungen wurden erfolgreich aktualisiert.');
    }

    /**
     * Update the categories page settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateCategoriesPage(Request $request)
    {
        $validated = $request->validate([
            // Hero section
            'categories_hero_title' => 'nullable|string|max:255',
            'categories_hero_subtitle' => 'nullable|string|max:500',
            'categories_hero_image' => 'nullable|image|max:2048',

            // Main categories section
            'categories_section_enabled' => 'boolean',
            'categories_section_title' => 'nullable|string|max:255',
            'categories_section_subtitle' => 'nullable|string|max:500',
            'categories_section_categories' => 'nullable|array',
            'categories_section_categories.*' => 'exists:categories,id',

            // Wohnmobil section
            'wohnmobil_section_enabled' => 'boolean',
            'wohnmobil_section_title' => 'nullable|string|max:255',
            'wohnmobil_section_subtitle' => 'nullable|string|max:500',
            'wohnmobil_section_button_text' => 'nullable|string|max:100',
            'wohnmobil_section_button_link' => 'nullable|string|max:255',

            // Event items section
            'events_section_enabled' => 'boolean',
            'events_section_title' => 'nullable|string|max:255',
            'events_section_subtitle' => 'nullable|string|max:500',
            'events_section_categories' => 'nullable|array',
            'events_section_categories.*' => 'exists:categories,id',
            'events_section_button_text' => 'nullable|string|max:100',
            'events_section_button_link' => 'nullable|string|max:255',

            // Vehicles section
            'vehicles_section_enabled' => 'boolean',
            'vehicles_section_title' => 'nullable|string|max:255',
            'vehicles_section_subtitle' => 'nullable|string|max:500',
            'vehicles_section_categories' => 'nullable|array',
            'vehicles_section_categories.*' => 'exists:categories,id',
            'vehicles_section_button_text' => 'nullable|string|max:100',
            'vehicles_section_button_link' => 'nullable|string|max:255',

            // Construction tools section
            'construction_section_enabled' => 'boolean',
            'construction_section_title' => 'nullable|string|max:255',
            'construction_section_subtitle' => 'nullable|string|max:500',
            'construction_section_categories' => 'nullable|array',
            'construction_section_categories.*' => 'exists:categories,id',
            'construction_section_button_text' => 'nullable|string|max:100',
            'construction_section_button_link' => 'nullable|string|max:255',
        ]);

        // Save categories page settings
        foreach ($validated as $key => $value) {
            if ($key !== 'categories_hero_image') {
                Setting::updateOrCreate(
                    ['key' => $key, 'group' => 'categories_page'],
                    ['value' => $value]
                );
            }
        }

        // Handle hero image upload if present
        if ($request->hasFile('categories_hero_image')) {
            // Delete old image if it exists
            $oldImageSetting = Setting::where('key', 'categories_hero_image')->where('group', 'categories_page')->first();
            if ($oldImageSetting && $oldImageSetting->value) {
                Storage::disk('public')->delete($oldImageSetting->value);
            }

            // Upload new image
            $path = $request->file('categories_hero_image')->store('categories_page', 'public');
            Setting::updateOrCreate(
                ['key' => 'categories_hero_image', 'group' => 'categories_page'],
                ['value' => $path]
            );
        }

        return redirect()->route('admin.homepage.index')
            ->with('success', 'Kategorien-Seite Einstellungen wurden erfolgreich aktualisiert.');
    }
}
