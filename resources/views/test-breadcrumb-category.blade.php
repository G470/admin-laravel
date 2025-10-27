@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Breadcrumb Category Test')

@section('content')
    <section class="section-py">
        <div class="container">
            <h1 class="mb-4">Breadcrumb mit Kategorie-Hierarchie Test</h1>
            
            <div class="row">
                <div class="col-12 mb-4">
                    <h3>Automatische Generierung aus Kategorie</h3>
                    <p class="text-muted">Die Breadcrumb wird automatisch aus der Kategorie-Hierarchie generiert.</p>
                    
                    @php
                        // Beispiel-Kategorien für den Test
                        $rootCategory = new \App\Models\Category([
                            'id' => 1,
                            'name' => 'Events',
                            'type' => 'events',
                            'parent_id' => null
                        ]);
                        
                        $subCategory = new \App\Models\Category([
                            'id' => 2,
                            'name' => 'Hochzeiten',
                            'type' => 'events',
                            'parent_id' => 1
                        ]);
                        
                        $subSubCategory = new \App\Models\Category([
                            'id' => 3,
                            'name' => '4er Duschcontainer',
                            'type' => 'events',
                            'parent_id' => 2
                        ]);
                        
                        // Parent-Beziehungen setzen
                        $subCategory->setRelation('parent', $rootCategory);
                        $subSubCategory->setRelation('parent', $subCategory);
                    @endphp
                    
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>Root-Kategorie: {{ $rootCategory->name }}</h5>
                        </div>
                        <div class="card-body">
                            @livewire('frontend.breadcrumb', [
                                'category' => $rootCategory,
                                'autoGenerateFromCategory' => true,
                                'showHome' => true,
                                'homeText' => 'Startseite'
                            ])
                        </div>
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>Unterkategorie: {{ $subCategory->name }}</h5>
                        </div>
                        <div class="card-body">
                            @livewire('frontend.breadcrumb', [
                                'category' => $subCategory,
                                'autoGenerateFromCategory' => true,
                                'showHome' => true,
                                'homeText' => 'Startseite'
                            ])
                        </div>
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5>Unter-Unterkategorie: {{ $subSubCategory->name }}</h5>
                        </div>
                        <div class="card-body">
                            @livewire('frontend.breadcrumb', [
                                'category' => $subSubCategory,
                                'autoGenerateFromCategory' => true,
                                'showHome' => true,
                                'homeText' => 'Startseite'
                            ])
                        </div>
                    </div>
                </div>
                
                <div class="col-12 mb-4">
                    <h3>Vergleich: Manuelle vs. Automatische Generierung</h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Manuell definiert</h6>
                                </div>
                                <div class="card-body">
                                    @livewire('frontend.breadcrumb', [
                                        'items' => [
                                            ['text' => 'Events', 'url' => '/kategorien/events', 'icon' => 'ti ti-calendar-event'],
                                            ['text' => 'Hochzeiten', 'url' => '/kategorien/events/hochzeiten', 'icon' => 'ti ti-heart'],
                                            ['text' => '4er Duschcontainer', 'active' => true, 'icon' => 'ti ti-droplet']
                                        ],
                                        'showHome' => true,
                                        'homeText' => 'Startseite'
                                    ])
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Automatisch generiert</h6>
                                </div>
                                <div class="card-body">
                                    @livewire('frontend.breadcrumb', [
                                        'category' => $subSubCategory,
                                        'autoGenerateFromCategory' => true,
                                        'showHome' => true,
                                        'homeText' => 'Startseite'
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                    <h3>Icon-Mapping Test</h3>
                    <p class="text-muted">Verschiedene Kategorien mit automatischen Icons:</p>
                    
                    @php
                        $testCategories = [
                            new \App\Models\Category(['name' => 'Fahrzeuge', 'type' => 'fahrzeuge']),
                            new \App\Models\Category(['name' => 'Baumaschinen', 'type' => 'baumaschinen']),
                            new \App\Models\Category(['name' => 'Garten Equipment', 'type' => 'garten']),
                            new \App\Models\Category(['name' => 'Elektronik', 'type' => 'elektronik']),
                            new \App\Models\Category(['name' => 'Sport & Freizeit', 'type' => 'sport']),
                        ];
                    @endphp
                    
                    @foreach($testCategories as $testCategory)
                        <div class="card mb-2">
                            <div class="card-body">
                                @livewire('frontend.breadcrumb', [
                                    'category' => $testCategory,
                                    'autoGenerateFromCategory' => true,
                                    'showHome' => false
                                ])
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection 