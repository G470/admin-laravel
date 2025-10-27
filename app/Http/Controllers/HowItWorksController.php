<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HowItWorksController extends Controller
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
     * Display the how it works page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pageConfigs = $this->getPageConfigs();

        // Steps for renters
        $renterSteps = [
            (object) [
                'step' => '1',
                'title' => 'Artikel suchen',
                'description' => 'Durchsuche unsere große Auswahl an verfügbaren Artikeln in deiner Nähe.',
                'icon' => 'ti-search'
            ],
            (object) [
                'step' => '2',
                'title' => 'Reservieren',
                'description' => 'Wähle deine gewünschten Daten und reserviere den Artikel mit wenigen Klicks.',
                'icon' => 'ti-calendar'
            ],
            (object) [
                'step' => '3',
                'title' => 'Abholen',
                'description' => 'Klären Sie mit dem Vermieter ob der Artikel abgeholt under gebracht wird.',
                'icon' => 'ti-car'
            ],
            (object) [
                'step' => '4',
                'title' => 'Nutzen & Zurückgeben',
                'description' => 'Nutze den Artikel und gib ihn nach der Mietzeit zurück.',
                'icon' => 'ti-check'
            ]
        ];

        // Steps for landlords
        $landlordSteps = [
            (object) [
                'step' => '1',
                'title' => 'Artikel einstellen',
                'description' => 'Erstelle ein Inserat für deinen Artikel mit Fotos und Beschreibung.',
                'icon' => 'ti-plus'
            ],
            (object) [
                'step' => '2',
                'title' => 'Anfragen erhalten',
                'description' => 'Erhalte Buchungsanfragen von interessierten Mietern.',
                'icon' => 'ti-message'
            ],
            (object) [
                'step' => '3',
                'title' => 'Übergeben',
                'description' => 'Übergib den Artikel zum vereinbarten Zeitpunkt an den Mieter.',
                'icon' => 'ti-handshake'
            ],
            (object) [
                'step' => '4',
                'title' => 'Verdienen',
                'description' => 'Erhalte deine Vergütung und verdiene mit ungenutzten Gegenständen.',
                'icon' => 'ti-coin'
            ]
        ];

        return view('inlando.how-it-works', compact('renterSteps', 'landlordSteps'))->with('pageConfigs', $pageConfigs);
    }
}
