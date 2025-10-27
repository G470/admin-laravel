<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AboutController extends Controller
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
     * Display the about us page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pageConfigs = $this->getPageConfigs();

        // Sample team members
        $teamMembers = [
            (object) [
                'name' => 'Max Mustermann',
                'position' => 'Geschäftsführer',
                'image' => asset('assets/images/team/member1.svg'),
                'description' => 'Mit über 10 Jahren Erfahrung im Sharing-Economy-Bereich führt Max unser Team.'
            ],
            (object) [
                'name' => 'Anna Schmidt',
                'position' => 'Head of Operations',
                'image' => asset('assets/images/team/member2.svg'),
                'description' => 'Anna sorgt dafür, dass alle Vermietungsprozesse reibungslos ablaufen.'
            ],
            (object) [
                'name' => 'Thomas Weber',
                'position' => 'Kundenservice Manager',
                'image' => asset('assets/images/team/member3.svg'),
                'description' => 'Thomas ist unser Experte für Kundenzufriedenheit und Support.'
            ]
        ];

        return view('inlando.about', compact('teamMembers'))->with('pageConfigs', $pageConfigs);
    }
}
