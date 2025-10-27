<?php

namespace App\Http\Controllers\Inlando;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Rental;

class InlandoStartpageController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * Configuration for Vuexy layout
     *
     * @return array
     */
    private function getPageConfigs()
    {
        return [
            'bodyClass' => 'landing-page',
            'navbarType' => 'fixed',
            'footerFixed' => false,
            'pageHeader' => false,
            'defaultLayout' => 'front'
        ];
    }

    /**
     * Display the homepage with categories.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Configure layout options for Vuexy theme
        $pageConfigs = $this->getPageConfigs();

        // Get real categories from database, or fallback to sample data if none exist
        $dbCategories = Category::limit(8)->get();
        
        if ($dbCategories->count() > 0) {
            $categories = $dbCategories;
        } else {
            // Sample data for categories (fallback)
            $categories = collect([
                (object) [
                    'name' => 'Wohnmobile',
                    'slug' => 'wohnmobile',
                    'image' => asset('assets/images/categories/wohnmobil.svg')
                ],
                (object) [
                    'name' => 'Baumaschinen',
                    'slug' => 'baumaschinen',
                    'image' => asset('assets/images/categories/baumaschinen.svg')
                ],
                (object) [
                    'name' => 'Eventartikel',
                    'slug' => 'eventartikel',
                    'image' => asset('assets/images/categories/event.svg')
                ],
                (object) [
                    'name' => 'Anhänger',
                    'slug' => 'anhaenger',
                    'image' => asset('assets/images/categories/anhaenger.svg')
                ],
                (object) [
                    'name' => 'Werkzeug',
                    'slug' => 'werkzeug',
                    'image' => asset('assets/images/categories/werkzeug.svg')
                ],
                (object) [
                    'name' => 'Garten & Freizeit',
                    'slug' => 'garten-freizeit',
                    'image' => asset('assets/images/categories/garten.svg')
                ],
                (object) [
                    'name' => 'Sport & Spiel',
                    'slug' => 'sport-spiel',
                    'image' => asset('assets/images/categories/sport.svg')
                ],
                (object) [
                    'name' => 'Elektronik',
                    'slug' => 'elektronik',
                    'image' => asset('assets/images/categories/elektronik.svg')
                ],
            ]);
        }

        // Sample data for event items (these could also come from database in the future)
        $eventItems = [
            (object) [
                'name' => 'Bühne',
                'slug' => 'buhne',
                'image' => asset('assets/images/events/buhne.svg')
            ],
            (object) [
                'name' => 'Dekoration',
                'slug' => 'dekoration',
                'image' => asset('assets/images/events/dekoration.svg')
            ],
            (object) [
                'name' => 'Kostüme',
                'slug' => 'kostume',
                'image' => asset('assets/images/events/kostume.svg')
            ],
            (object) [
                'name' => 'Veranstaltungstechnik',
                'slug' => 'veranstaltungstechnik',
                'image' => asset('assets/images/events/technik.svg')
            ],
        ];

        // Sample data for vehicles
        $vehicles = [
            (object) [
                'name' => 'Wohnmobil',
                'slug' => 'wohnmobil',
                'description' => 'Erlebe Freiheit und Abenteuer mit unseren komfortablen Wohnmobilen.',
                'image' => asset('assets/images/vehicles/wohnmobil.svg')
            ],
            (object) [
                'name' => 'Anhänger',
                'slug' => 'anhaenger',
                'description' => 'Für jeden Transport die passende Lösung.',
                'image' => asset('assets/images/vehicles/anhaenger.svg')
            ],
            (object) [
                'name' => 'Transporter',
                'slug' => 'transporter',
                'description' => 'Zuverlässige Transporter für Umzüge und große Einkäufe.',
                'image' => asset('assets/images/vehicles/transporter.svg')
            ],
        ];

        // Sample data for construction tools
        $constructionTools = [
            (object) [
                'name' => 'Abbruch & Recycling',
                'slug' => 'abbruch-recycling',
                'description' => 'Professionelle Werkzeuge für Abbruch- und Recyclingarbeiten.',
                'image' => asset('assets/images/construction/abbruch.svg')
            ],
            (object) [
                'name' => 'Arbeitsbühne',
                'slug' => 'arbeitsbuehne',
                'description' => 'Sichere Arbeitsbühnen für Arbeiten in großer Höhe.',
                'image' => asset('assets/images/construction/arbeitsbuehne.svg')
            ],
            (object) [
                'name' => 'Aufzug / LKW Förderung',
                'slug' => 'aufzug-lkw',
                'description' => 'Leistungsstarke Aufzüge für den Transport schwerer Lasten.',
                'image' => asset('assets/images/construction/aufzug.svg')
            ],
        ];

        // Return view with data
        return view('inlando.categories', compact(
            'categories',
            'eventItems',
            'vehicles',
            'constructionTools'
        ))->with('pageConfigs', $pageConfigs);
    }

}
