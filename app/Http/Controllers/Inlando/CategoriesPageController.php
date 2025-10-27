<?php

namespace App\Http\Controllers\Inlando;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoriesPageController extends Controller
{
    /**
     * Display the categories page with admin-configured content.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Load categories page settings
        $settings = Setting::where('group', 'categories_page')->get()->keyBy('key');

        // Get hero section data with proper fallbacks
        $heroTitle = $settings->get('categories_hero_title')?->value ?? 'Finde und miete, was du brauchst';
        $heroSubtitle = $settings->get('categories_hero_subtitle')?->value ?? 'Entdecke verschiedene Kategorien von Artikeln, die du mieten kannst';
        $heroImage = $settings->get('categories_hero_image')?->value ?? asset('images/hero-bg.jpg');

        // Main categories section
        $categoriesSectionEnabled = (bool) ($settings->get('categories_section_enabled')?->value ?? true);
        $categoriesSectionTitle = $settings->get('categories_section_title')?->value ?? 'Kategorien durchsuchen';
        $categoriesSectionSubtitle = $settings->get('categories_section_subtitle')?->value ?? 'Entdecke verschiedene Kategorien von Artikeln, die du mieten kannst';
        $categoriesSectionCategoryIds = $settings->get('categories_section_categories')?->value ?? [];

        // Get main categories based on admin selection
        $categories = collect();
        if ($categoriesSectionEnabled && !empty($categoriesSectionCategoryIds)) {
            $categories = Category::whereIn('id', $categoriesSectionCategoryIds)
                ->online()
                ->ordered()
                ->get();
        } else {
            // Fallback to first 8 categories if no selection
            $categories = Category::online()->ordered()->limit(8)->get();
        }

        // Wohnmobil section
        $wohnmobilSectionEnabled = (bool) ($settings->get('wohnmobil_section_enabled')?->value ?? true);
        $wohnmobilSectionTitle = $settings->get('wohnmobil_section_title')?->value ?? 'Wohnmobil entdecken';
        $wohnmobilSectionSubtitle = $settings->get('wohnmobil_section_subtitle')?->value ?? 'Entdecke die Freiheit auf vier Rädern – miete ein Wohnmobil und erlebe deinen perfekten Urlaub. Flexibel, unabhängig und mit allem Komfort, den du brauchst.';
        $wohnmobilSectionButtonText = $settings->get('wohnmobil_section_button_text')?->value ?? 'Jetzt entdecken';
        $wohnmobilSectionButtonLink = $settings->get('wohnmobil_section_button_link')?->value ?? '#';

        // Events section
        $eventsSectionEnabled = (bool) ($settings->get('events_section_enabled')?->value ?? true);
        $eventsSectionTitle = $settings->get('events_section_title')?->value ?? 'Eventartikel mieten';
        $eventsSectionSubtitle = $settings->get('events_section_subtitle')?->value ?? 'Alles was du für dein nächstes Event benötigst';
        $eventsSectionCategoryIds = $settings->get('events_section_categories')?->value ?? [];
        $eventsSectionButtonText = $settings->get('events_section_button_text')?->value ?? 'Alle Eventartikel anzeigen';
        $eventsSectionButtonLink = $settings->get('events_section_button_link')?->value ?? '/kategorien/events';

        // Get event items based on admin selection
        $eventItems = collect();
        if ($eventsSectionEnabled && !empty($eventsSectionCategoryIds)) {
            $eventItems = Category::whereIn('id', $eventsSectionCategoryIds)
                ->online()
                ->ordered()
                ->get()
                ->map(function ($category) {
                    return (object) [
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'image' => $category->category_image ?: asset('assets/images/events/default.svg')
                    ];
                });
        } else {
            // Fallback to event-related categories
            $eventItems = Category::online()
                ->where(function ($query) {
                    $query->where('name', 'like', '%event%')
                        ->orWhere('name', 'like', '%bühne%')
                        ->orWhere('name', 'like', '%dekoration%')
                        ->orWhere('name', 'like', '%veranstaltung%');
                })
                ->limit(4)
                ->get()
                ->map(function ($category) {
                    return (object) [
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'image' => $category->category_image ?: asset('assets/images/events/default.svg')
                    ];
                });
        }

        // Vehicles section
        $vehiclesSectionEnabled = (bool) ($settings->get('vehicles_section_enabled')?->value ?? true);
        $vehiclesSectionTitle = $settings->get('vehicles_section_title')?->value ?? 'Nutzfahrzeuge & Freizeitfahrzeuge';
        $vehiclesSectionSubtitle = $settings->get('vehicles_section_subtitle')?->value ?? 'Für Transport, Urlaub und Ausflüge';
        $vehiclesSectionCategoryIds = $settings->get('vehicles_section_categories')?->value ?? [];
        $vehiclesSectionButtonText = $settings->get('vehicles_section_button_text')?->value ?? 'Alle Fahrzeuge anzeigen';
        $vehiclesSectionButtonLink = $settings->get('vehicles_section_button_link')?->value ?? '/kategorien/vehicles';

        // Get vehicles based on admin selection
        $vehicles = collect();
        if ($vehiclesSectionEnabled && !empty($vehiclesSectionCategoryIds)) {
            $vehicles = Category::whereIn('id', $vehiclesSectionCategoryIds)
                ->online()
                ->ordered()
                ->get()
                ->map(function ($category) {
                    return (object) [
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description ?: 'Für Transport, Urlaub und Ausflüge',
                        'image' => $category->category_image ?: asset('assets/images/vehicles/default.svg')
                    ];
                });
        } else {
            // Fallback to vehicle-related categories
            $vehicles = Category::online()
                ->where(function ($query) {
                    $query->where('name', 'like', '%fahrzeug%')
                        ->orWhere('name', 'like', '%auto%')
                        ->orWhere('name', 'like', '%wohnmobil%')
                        ->orWhere('name', 'like', '%anhänger%');
                })
                ->limit(3)
                ->get()
                ->map(function ($category) {
                    return (object) [
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description ?: 'Für Transport, Urlaub und Ausflüge',
                        'image' => $category->category_image ?: asset('assets/images/vehicles/default.svg')
                    ];
                });
        }

        // Construction section
        $constructionSectionEnabled = (bool) ($settings->get('construction_section_enabled')?->value ?? true);
        $constructionSectionTitle = $settings->get('construction_section_title')?->value ?? 'Baumaschinen & Bauzubehör';
        $constructionSectionSubtitle = $settings->get('construction_section_subtitle')?->value ?? 'Professionelles Equipment für dein Bauprojekt';
        $constructionSectionCategoryIds = $settings->get('construction_section_categories')?->value ?? [];
        $constructionSectionButtonText = $settings->get('construction_section_button_text')?->value ?? 'Alle Baumaschinen anzeigen';
        $constructionSectionButtonLink = $settings->get('construction_section_button_link')?->value ?? '/kategorien/construction';

        // Get construction tools based on admin selection
        $constructionTools = collect();
        if ($constructionSectionEnabled && !empty($constructionSectionCategoryIds)) {
            $constructionTools = Category::whereIn('id', $constructionSectionCategoryIds)
                ->online()
                ->ordered()
                ->get()
                ->map(function ($category) {
                    return (object) [
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description ?: 'Professionelles Equipment für dein Projekt',
                        'image' => $category->category_image ?: asset('assets/images/construction/default.svg')
                    ];
                });
        } else {
            // Fallback to construction-related categories
            $constructionTools = Category::online()
                ->where(function ($query) {
                    $query->where('name', 'like', '%bau%')
                        ->orWhere('name', 'like', '%maschine%')
                        ->orWhere('name', 'like', '%abbruch%')
                        ->orWhere('name', 'like', '%bühne%')
                        ->orWhere('name', 'like', '%aufzug%');
                })
                ->limit(3)
                ->get()
                ->map(function ($category) {
                    return (object) [
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description ?: 'Professionelles Equipment für dein Projekt',
                        'image' => $category->category_image ?: asset('assets/images/construction/default.svg')
                    ];
                });
        }
        return view('inlando.categories', compact(
            'heroTitle',
            'heroSubtitle',
            'heroImage',
            'categoriesSectionEnabled',
            'categoriesSectionTitle',
            'categoriesSectionSubtitle',
            'categories',
            'wohnmobilSectionEnabled',
            'wohnmobilSectionTitle',
            'wohnmobilSectionSubtitle',
            'wohnmobilSectionButtonText',
            'wohnmobilSectionButtonLink',
            'eventsSectionEnabled',
            'eventsSectionTitle',
            'eventsSectionSubtitle',
            'eventItems',
            'eventsSectionButtonText',
            'eventsSectionButtonLink',
            'vehiclesSectionEnabled',
            'vehiclesSectionTitle',
            'vehiclesSectionSubtitle',
            'vehicles',
            'vehiclesSectionButtonText',
            'vehiclesSectionButtonLink',
            'constructionSectionEnabled',
            'constructionSectionTitle',
            'constructionSectionSubtitle',
            'constructionTools',
            'constructionSectionButtonText',
            'constructionSectionButtonLink'
        ));
    }
}