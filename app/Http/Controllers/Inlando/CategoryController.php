<?php

namespace App\Http\Controllers\Inlando;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Location;
use App\Helpers\SeoHelper;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show($slug, Request $request)
    {
        $category = Category::with([
            'children' => function ($query) {
                $query->withCount('rentals')
                    ->where('status', 'online')
                    ->orderBy('name');
            }
        ])->where('slug', $slug)->firstOrFail();

        // Get location from query parameters if provided
        $location = null;
        if ($request->has('city') || $request->has('postcode')) {
            $location = Location::when($request->city, function ($query) use ($request) {
                return $query->where('city', $request->city);
            })
                ->when($request->postcode, function ($query) use ($request) {
                    return $query->where('postcode', $request->postcode);
                })
                ->first();
        }

        // Generate SEO data using helper
        $seoData = SeoHelper::getCategorySeoData($category, $location);

        return view('inlando.category-show', compact('category', 'seoData', 'location'));
    }

    /**
     * Dynamic category type handler - works with any category type from database.
     *
     * @param  string  $type
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function categoryType($type, Request $request)
    {
        // Define category type mappings and their search terms
        $typeMap = [
            'events' => [
                'title' => 'Eventartikel mieten',
                'description' => 'Alles was du für dein nächstes Event benötigst',
                'icon' => 'ti-calendar-event',
                'search_terms' => ['Event', 'Party', 'Feier', 'Hochzeit', 'Geburtstag', 'Veranstaltung']
            ],
            'fahrzeuge' => [
                'title' => 'Fahrzeuge mieten',
                'description' => 'Nutzfahrzeuge & Freizeitfahrzeuge für Transport, Urlaub und Ausflüge',
                'icon' => 'ti-car',
                'search_terms' => ['Fahrzeug', 'Auto', 'Wohnmobil', 'LKW', 'Transporter', 'PKW']
            ],
            'baumaschinen' => [
                'title' => 'Baumaschinen & Bauzubehör',
                'description' => 'Professionelles Equipment für dein Bauprojekt',
                'icon' => 'ti-tool',
                'search_terms' => ['Bau', 'Werkzeug', 'Maschine', 'Bohrer', 'Säge', 'Hammer']
            ],
            'garten' => [
                'title' => 'Garten & Outdoor',
                'description' => 'Gartengeräte und Outdoor-Equipment',
                'icon' => 'ti-plant',
                'search_terms' => ['Garten', 'Outdoor', 'Rasenmäher', 'Heckenschere', 'Camping']
            ],
            'elektronik' => [
                'title' => 'Elektronik & Technik',
                'description' => 'Technische Geräte und Elektronik',
                'icon' => 'ti-device-desktop',
                'search_terms' => ['Elektronik', 'Technik', 'Computer', 'Kamera', 'Audio', 'Video']
            ],
            'sport' => [
                'title' => 'Sport & Freizeit',
                'description' => 'Sportgeräte und Freizeitausrüstung',
                'icon' => 'ti-ball-football',
                'search_terms' => ['Sport', 'Freizeit', 'Fitness', 'Fahrrad', 'Ski', 'Tennis']
            ]
        ];

        // Get type configuration or use defaults
        $config = $typeMap[$type] ?? [
            'title' => ucfirst($type) . ' mieten',
            'description' => 'Entdecke unsere ' . $type . ' Kategorien',
            'icon' => 'ti-category',
            'search_terms' => [ucfirst($type)]
        ];

        // Build dynamic query based on search terms
        $query = Category::query();
        foreach ($config['search_terms'] as $index => $term) {
            if ($index === 0) {
                $query->where('name', 'like', "%{$term}%");
            } else {
                $query->orWhere('name', 'like', "%{$term}%");
            }
        }

        // Also search in description field
        foreach ($config['search_terms'] as $term) {
            $query->orWhere('description', 'like', "%{$term}%");
        }

        $categories = $query->get();

        // Get location from query parameters if provided
        $location = null;
        if ($request->has('city') || $request->has('postcode')) {
            $location = Location::when($request->city, function ($query) use ($request) {
                return $query->where('city', $request->city);
            })
                ->when($request->postcode, function ($query) use ($request) {
                    return $query->where('postcode', $request->postcode);
                })
                ->first();
        }

        // Create a virtual category for SEO data generation
        $virtualCategory = new Category(['name' => $config['title']]);
        $seoData = SeoHelper::getCategorySeoData($virtualCategory, $location);

        return view('inlando.categories.dynamic', compact('categories', 'config', 'type', 'seoData', 'location'));
    }
}
