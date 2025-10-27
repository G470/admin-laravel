@extends('layouts/contentNavbarLayoutFrontend')

@section('title', 'Breadcrumb Real Data Test')

@section('content')
    <section class="section-py">
        <div class="container">
            <h1 class="mb-4">Breadcrumb mit echten Kategorien Test</h1>
            
            <div class="row">
                <div class="col-12 mb-4">
                    <h3>Echte Kategorien aus der Datenbank</h3>
                    <p class="text-muted">Die Breadcrumb wird mit echten Kategorien aus der Datenbank getestet.</p>
                    
                    @php
                        // Echte Kategorien aus der Datenbank laden
                        $categories = \App\Models\Category::with('parent')
                            ->where('status', 'online')
                            ->orderBy('name')
                            ->limit(10)
                            ->get();
                    @endphp
                    
                    @if($categories->count() > 0)
                        @foreach($categories as $category)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5>{{ $category->name }}</h5>
                                    <small class="text-muted">
                                        ID: {{ $category->id }} | 
                                        Slug: {{ $category->slug ?? 'kein Slug' }} | 
                                        Type: {{ $category->type ?? 'kein Type' }} | 
                                        Parent: {{ $category->parent ? $category->parent->name : 'Root' }}
                                    </small>
                                </div>
                                <div class="card-body">
                                    @livewire('frontend.breadcrumb', [
                                        'category' => $category,
                                        'autoGenerateFromCategory' => true,
                                        'showHome' => true,
                                        'homeText' => 'Startseite'
                                    ])
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <h5>Keine Kategorien gefunden</h5>
                            <p>Es wurden keine online Kategorien in der Datenbank gefunden. Bitte erstellen Sie zuerst einige Kategorien.</p>
                        </div>
                    @endif
                </div>
                
                <div class="col-12 mb-4">
                    <h3>Kategorien mit Hierarchie</h3>
                    <p class="text-muted">Kategorien mit Parent-Child-Beziehungen:</p>
                    
                    @php
                        // Kategorien mit Parent-Beziehungen laden
                        $hierarchicalCategories = \App\Models\Category::with('parent')
                            ->where('status', 'online')
                            ->whereNotNull('parent_id')
                            ->orderBy('name')
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @if($hierarchicalCategories->count() > 0)
                        @foreach($hierarchicalCategories as $category)
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5>{{ $category->name }}</h5>
                                    <small class="text-muted">
                                        Parent: {{ $category->parent ? $category->parent->name : 'Root' }}
                                    </small>
                                </div>
                                <div class="card-body">
                                    @livewire('frontend.breadcrumb', [
                                        'category' => $category,
                                        'autoGenerateFromCategory' => true,
                                        'showHome' => true,
                                        'homeText' => 'Startseite'
                                    ])
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-info">
                            <h5>Keine hierarchischen Kategorien gefunden</h5>
                            <p>Es wurden keine Kategorien mit Parent-Child-Beziehungen gefunden.</p>
                        </div>
                    @endif
                </div>
                
                <div class="col-12">
                    <h3>Debug-Informationen</h3>
                    <div class="card">
                        <div class="card-body">
                            <h6>Verfügbare Routen:</h6>
                            <ul>
                                <li><code>category.show</code> - Für einzelne Kategorien mit Slug</li>
                                <li><code>categories.type</code> - Für Kategorie-Typen</li>
                            </ul>
                            
                            <h6>Kategorie-Statistiken:</h6>
                            <ul>
                                <li>Gesamt Kategorien: {{ \App\Models\Category::count() }}</li>
                                <li>Online Kategorien: {{ \App\Models\Category::where('status', 'online')->count() }}</li>
                                <li>Kategorien mit Slug: {{ \App\Models\Category::whereNotNull('slug')->count() }}</li>
                                <li>Kategorien mit Parent: {{ \App\Models\Category::whereNotNull('parent_id')->count() }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection 