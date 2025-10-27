@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Breadcrumb Test')

@section('content')
    <section class="section-py">
        <div class="container">
            <h1 class="mb-4">Breadcrumb Component Test</h1>
            
            <div class="row">
                <div class="col-12 mb-4">
                    <h3>Einfache Verwendung</h3>
                    <livewire:frontend.breadcrumb :items="['Kategorien', 'Events', 'Hochzeiten']" />
                </div>
                
                <div class="col-12 mb-4">
                    <h3>Erweiterte Verwendung</h3>
                    <livewire:frontend.breadcrumb 
                        :items="[
                            ['text' => 'Kategorien', 'url' => '/kategorien', 'icon' => 'ti ti-category'],
                            ['text' => 'Events', 'url' => '/kategorien/events', 'icon' => 'ti ti-calendar'],
                            ['text' => 'Hochzeiten', 'active' => true, 'icon' => 'ti ti-heart']
                        ]"
                        :show-home="true"
                        :home-text="'Startseite'"
                        :max-items="4"
                    />
                </div>
                
                <div class="col-12 mb-4">
                    <h3>Ohne Home-Link</h3>
                    <livewire:frontend.breadcrumb 
                        :items="['Dashboard', 'Vermietungen', 'Neue Vermietung']"
                        :show-home="false"
                    />
                </div>
                
                <div class="col-12 mb-4">
                    <h3>Viele Items (mit Kürzung)</h3>
                    <livewire:frontend.breadcrumb 
                        :items="['Level 1', 'Level 2', 'Level 3', 'Level 4', 'Level 5', 'Level 6', 'Level 7']"
                        :max-items="4"
                    />
                </div>
            </div>
        </div>
    </section>
@endsection 