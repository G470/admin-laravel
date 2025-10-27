<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RentOutController extends Controller
{
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
     * Display the rent out page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pageConfigs = $this->getPageConfigs();

        // Benefits of renting out
        $benefits = [
            (object) [
                'title' => 'Zusätzliches Einkommen',
                'description' => 'Verdiene Geld mit Gegenständen, die du nicht täglich nutzt.',
                'icon' => 'ti-coin',
                'color' => 'success'
            ],
            (object) [
                'title' => 'Einfache Verwaltung',
                'description' => 'Verwalte deine Vermietungen bequem über unser Dashboard.',
                'icon' => 'ti-dashboard',
                'color' => 'primary'
            ],
            (object) [
                'title' => 'Sichere Abwicklung',
                'description' => 'Alle Transaktionen sind versichert und sicher abgewickelt.',
                'icon' => 'ti-shield-check',
                'color' => 'info'
            ],
            (object) [
                'title' => 'Große Reichweite',
                'description' => 'Erreiche tausende potenzielle Mieter in deiner Region.',
                'icon' => 'ti-users',
                'color' => 'warning'
            ]
        ];

        // Popular rental categories
        $categories = [
            (object) [
                'name' => 'Werkzeuge',
                'earnings' => '€150-300/Monat',
                'image' => asset('assets/images/categories/werkzeug.svg')
            ],
            (object) [
                'name' => 'Gartengeräte',
                'earnings' => '€100-250/Monat',
                'image' => asset('assets/images/categories/garten.svg')
            ],
            (object) [
                'name' => 'Sportausrüstung',
                'earnings' => '€80-200/Monat',
                'image' => asset('assets/images/categories/sport.svg')
            ],
            (object) [
                'name' => 'Elektronik',
                'earnings' => '€200-500/Monat',
                'image' => asset('assets/images/categories/elektronik.svg')
            ]
        ];

        return view('inlando.rent-out', compact('benefits', 'categories'))->with('pageConfigs', $pageConfigs);
    }
}
