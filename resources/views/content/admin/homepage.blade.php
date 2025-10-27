@extends('layouts/contentNavbarLayout')

@section('title', 'Homepage Einstellungen')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/quill/editor.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/quill/katex.min.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/quill/highlight.min.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/quill/quill.min.js')}}"></script>
    <script src="{{asset('assets/vendor/libs/jquery/jquery.js')}}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            $('.select2').select2();

            // Tab-Wechsel über URL-Hash
            let hash = window.location.hash;
            if (hash) {
                $('.nav-link[href="' + hash + '"]').tab('show');
            }

            // Bei Klick auf Tab, Hash in URL setzen
            $('.nav-link').on('shown.bs.tab', function (e) {
                window.location.hash = e.target.hash;
            });

            // Rich-Text-Editor initialisieren
            const heroEditor = new Quill('#heroContentEditor', {
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });

            const featuresEditor = new Quill('#featuresContentEditor', {
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });

            const aboutEditor = new Quill('#aboutContentEditor', {
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                theme: 'snow'
            });

            // Formular-Einreichung
            $('#homepageForm').on('submit', function (e) {
                e.preventDefault();

                // Textfelder mit Editor-Inhalten füllen
                $('#heroContent').val(heroEditor.root.innerHTML);
                $('#featuresContent').val(featuresEditor.root.innerHTML);
                $('#aboutContent').val(aboutEditor.root.innerHTML);

                // Simulierte Speicherung (würde einen AJAX-Request ausführen)
                setTimeout(function () {
                    toastr.success('Die Homepage-Einstellungen wurden erfolgreich gespeichert.');
                }, 1000);
            });

            // Categories page form submission
            $('#categoriesPageForm').on('submit', function (e) {
                // Form will submit normally to the server
                // No need to prevent default or add custom handling
            });
        });
    </script>
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Admin /</span> Homepage Einstellungen
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Homepage Einstellungen</h5>
                    <p class="card-subtitle">Konfigurieren Sie die Inhalte der Homepage</p>
                </div>
                <div class="card-body">
                    <form id="homepageForm" onsubmit="return false">
                        <!-- Tabs für Content-Bereiche -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="hero-tab" data-bs-toggle="tab" href="#hero"
                                    aria-controls="hero" role="tab" aria-selected="true">
                                    <i class="ti ti-photo me-1"></i> Hero-Bereich
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="features-tab" data-bs-toggle="tab" href="#features"
                                    aria-controls="features" role="tab" aria-selected="false">
                                    <i class="ti ti-list-check me-1"></i> Features
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="about-tab" data-bs-toggle="tab" href="#about" aria-controls="about"
                                    role="tab" aria-selected="false">
                                    <i class="ti ti-info-circle me-1"></i> Über uns
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="seo-homepage-tab" data-bs-toggle="tab" href="#seo-homepage"
                                    aria-controls="seo-homepage" role="tab" aria-selected="false">
                                    <i class="ti ti-search me-1"></i> SEO Einstellungen
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="categories-page-tab" data-bs-toggle="tab" href="#categories-page"
                                    aria-controls="categories-page" role="tab" aria-selected="false">
                                    <i class="ti ti-category me-1"></i> Kategorien-Seite
                                </a>
                            </li>
                        </ul>

                        <!-- Tab Inhalte -->
                        <div class="tab-content">
                            <!-- Hero-Bereich Einstellungen -->
                            <div class="tab-pane fade show active" id="hero" role="tabpanel" aria-labelledby="hero-tab">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="heroTitle" class="form-label">Überschrift</label>
                                        <input type="text" class="form-control" id="heroTitle" name="heroTitle"
                                            value="Vermieten Sie Ihre Räume einfach und sicher" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="heroSubtitle" class="form-label">Untertitel</label>
                                        <input type="text" class="form-control" id="heroSubtitle" name="heroSubtitle"
                                            value="Die Plattform für einfache und sichere Vermietung von Objekten aller Art" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="heroContentEditor" class="form-label">Beschreibungstext</label>
                                        <div id="heroContentEditor" style="height: 150px">
                                            <p>Inlando bietet Ihnen die Möglichkeit, Ihre Räume einfach und sicher zu
                                                vermieten. Registrieren Sie sich noch heute und starten Sie mit der
                                                Vermietung.</p>
                                        </div>
                                        <input type="hidden" id="heroContent" name="heroContent">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="heroImage" class="form-label">Hero-Bild</label>
                                        <input type="file" class="form-control" id="heroImage" name="heroImage" />
                                        <small class="text-muted">Empfohlene Bildgröße: 1920x1080px</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="heroButtonText" class="form-label">Button-Text</label>
                                        <input type="text" class="form-control" id="heroButtonText" name="heroButtonText"
                                            value="Jetzt starten" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="heroButtonLink" class="form-label">Button-Link</label>
                                        <input type="text" class="form-control" id="heroButtonLink" name="heroButtonLink"
                                            value="/registrierung" />
                                    </div>
                                    <div class="col-md-6">
                                        <label for="heroButtonColor" class="form-label">Button-Farbe</label>
                                        <select id="heroButtonColor" class="select2 form-select" name="heroButtonColor">
                                            <option value="primary" selected>Primär</option>
                                            <option value="secondary">Sekundär</option>
                                            <option value="success">Erfolg</option>
                                            <option value="danger">Gefahr</option>
                                            <option value="warning">Warnung</option>
                                            <option value="info">Info</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Features Einstellungen -->
                            <div class="tab-pane fade" id="features" role="tabpanel" aria-labelledby="features-tab">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="featuresTitle" class="form-label">Überschrift</label>
                                        <input type="text" class="form-control" id="featuresTitle" name="featuresTitle"
                                            value="Unsere Features" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="featuresSubtitle" class="form-label">Untertitel</label>
                                        <input type="text" class="form-control" id="featuresSubtitle"
                                            name="featuresSubtitle" value="Warum Sie mit Inlando vermieten sollten" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="featuresContentEditor" class="form-label">Beschreibungstext</label>
                                        <div id="featuresContentEditor" style="height: 150px">
                                            <p>Entdecken Sie die Vorteile von Inlando und warum immer mehr Vermieter auf
                                                unsere Plattform setzen.</p>
                                        </div>
                                        <input type="hidden" id="featuresContent" name="featuresContent">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="form-label">Features</label>
                                    </div>
                                </div>

                                <div class="feature-items">
                                    <div class="row mb-3 feature-item">
                                        <div class="col-md-3">
                                            <label for="featureIcon1" class="form-label">Icon</label>
                                            <select id="featureIcon1" class="select2 form-select" name="featureIcon[]">
                                                <option value="ti-shield-check" selected>Sicherheit</option>
                                                <option value="ti-cash">Bezahlung</option>
                                                <option value="ti-user">Benutzer</option>
                                                <option value="ti-clock">Zeit</option>
                                                <option value="ti-settings">Einstellungen</option>
                                                <option value="ti-star">Bewertung</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="featureTitle1" class="form-label">Titel</label>
                                            <input type="text" class="form-control" id="featureTitle1" name="featureTitle[]"
                                                value="Sicher & Zuverlässig" />
                                        </div>
                                        <div class="col-md-5">
                                            <label for="featureDescription1" class="form-label">Beschreibung</label>
                                            <textarea class="form-control" id="featureDescription1"
                                                name="featureDescription[]"
                                                rows="2">Wir sorgen für die Sicherheit Ihrer Daten und Zahlungen.</textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3 feature-item">
                                        <div class="col-md-3">
                                            <label for="featureIcon2" class="form-label">Icon</label>
                                            <select id="featureIcon2" class="select2 form-select" name="featureIcon[]">
                                                <option value="ti-shield-check">Sicherheit</option>
                                                <option value="ti-cash" selected>Bezahlung</option>
                                                <option value="ti-user">Benutzer</option>
                                                <option value="ti-clock">Zeit</option>
                                                <option value="ti-settings">Einstellungen</option>
                                                <option value="ti-star">Bewertung</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="featureTitle2" class="form-label">Titel</label>
                                            <input type="text" class="form-control" id="featureTitle2" name="featureTitle[]"
                                                value="Einfache Bezahlung" />
                                        </div>
                                        <div class="col-md-5">
                                            <label for="featureDescription2" class="form-label">Beschreibung</label>
                                            <textarea class="form-control" id="featureDescription2"
                                                name="featureDescription[]"
                                                rows="2">Verschiedene Zahlungsmethoden für maximale Flexibilität.</textarea>
                                        </div>
                                    </div>

                                    <div class="row mb-3 feature-item">
                                        <div class="col-md-3">
                                            <label for="featureIcon3" class="form-label">Icon</label>
                                            <select id="featureIcon3" class="select2 form-select" name="featureIcon[]">
                                                <option value="ti-shield-check">Sicherheit</option>
                                                <option value="ti-cash">Bezahlung</option>
                                                <option value="ti-user">Benutzer</option>
                                                <option value="ti-clock" selected>Zeit</option>
                                                <option value="ti-settings">Einstellungen</option>
                                                <option value="ti-star">Bewertung</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="featureTitle3" class="form-label">Titel</label>
                                            <input type="text" class="form-control" id="featureTitle3" name="featureTitle[]"
                                                value="Zeitsparend" />
                                        </div>
                                        <div class="col-md-5">
                                            <label for="featureDescription3" class="form-label">Beschreibung</label>
                                            <textarea class="form-control" id="featureDescription3"
                                                name="featureDescription[]"
                                                rows="2">Automatisierte Prozesse sparen Ihnen wertvolle Zeit.</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="addFeature">
                                            <i class="ti ti-plus me-1"></i> Feature hinzufügen
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Über uns Einstellungen -->
                            <div class="tab-pane fade" id="about" role="tabpanel" aria-labelledby="about-tab">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="aboutTitle" class="form-label">Überschrift</label>
                                        <input type="text" class="form-control" id="aboutTitle" name="aboutTitle"
                                            value="Über Inlando" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="aboutSubtitle" class="form-label">Untertitel</label>
                                        <input type="text" class="form-control" id="aboutSubtitle" name="aboutSubtitle"
                                            value="Ihre Plattform für einfache und sichere Vermietung" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="aboutContentEditor" class="form-label">Beschreibungstext</label>
                                        <div id="aboutContentEditor" style="height: 200px">
                                            <p>Inlando wurde 2023 gegründet, um die Vermietung von Räumen und Objekten zu
                                                vereinfachen. Unser Ziel ist es, eine sichere und benutzerfreundliche
                                                Plattform anzubieten, die sowohl Vermietern als auch Mietern einen Mehrwert
                                                bietet.</p>
                                            <p>Mit unserem erfahrenen Team arbeiten wir kontinuierlich daran, das
                                                Nutzererlebnis zu verbessern und neue Funktionen zu entwickeln.</p>
                                        </div>
                                        <input type="hidden" id="aboutContent" name="aboutContent">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="aboutImage" class="form-label">Über uns Bild</label>
                                        <input type="file" class="form-control" id="aboutImage" name="aboutImage" />
                                        <small class="text-muted">Empfohlene Bildgröße: 800x600px</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="aboutButtonText" class="form-label">Button-Text</label>
                                        <input type="text" class="form-control" id="aboutButtonText" name="aboutButtonText"
                                            value="Mehr erfahren" />
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="aboutButtonLink" class="form-label">Button-Link</label>
                                        <input type="text" class="form-control" id="aboutButtonLink" name="aboutButtonLink"
                                            value="/ueber-uns" />
                                    </div>
                                    <div class="col-md-6">
                                        <label for="aboutButtonColor" class="form-label">Button-Farbe</label>
                                        <select id="aboutButtonColor" class="select2 form-select" name="aboutButtonColor">
                                            <option value="primary">Primär</option>
                                            <option value="secondary" selected>Sekundär</option>
                                            <option value="success">Erfolg</option>
                                            <option value="danger">Gefahr</option>
                                            <option value="warning">Warnung</option>
                                            <option value="info">Info</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO Einstellungen -->
                            <div class="tab-pane fade" id="seo-homepage" role="tabpanel" aria-labelledby="seo-homepage-tab">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="seoTitle" class="form-label">SEO Titel</label>
                                        <input type="text" class="form-control" id="seoTitle" name="seoTitle"
                                            value="Inlando - Die Vermietungsplattform für Räume und Objekte" />
                                        <small class="text-muted">Empfohlene Länge: max. 60 Zeichen</small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="seoDescription" class="form-label">Meta Beschreibung</label>
                                        <textarea class="form-control" id="seoDescription" name="seoDescription"
                                            rows="3">Vermieten Sie Ihre Räume und Objekte einfach und sicher über Inlando. Die führende Plattform für Vermietungen aller Art.</textarea>
                                        <small class="text-muted">Empfohlene Länge: 120-160 Zeichen</small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="seoKeywords" class="form-label">Meta Keywords</label>
                                        <input type="text" class="form-control" id="seoKeywords" name="seoKeywords"
                                            value="Vermietung, Raumvermietung, Objekte mieten, sicher vermieten, Inlando" />
                                        <small class="text-muted">Durch Kommas getrennte Keywords</small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="ogImage" class="form-label">Open Graph Bild</label>
                                        <input type="file" class="form-control" id="ogImage" name="ogImage" />
                                        <small class="text-muted">Empfohlene Bildgröße: 1200x630px</small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mt-3">
                                            <input class="form-check-input" type="checkbox" id="indexable" name="indexable"
                                                checked>
                                            <label class="form-check-label" for="indexable">Homepage für Suchmaschinen
                                                indexierbar</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mt-3">
                                            <input class="form-check-input" type="checkbox" id="canonicalSelf"
                                                name="canonicalSelf" checked>
                                            <label class="form-check-label" for="canonicalSelf">Canonical URL auf sich
                                                selbst setzen</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kategorien-Seite Einstellungen -->
                            <div class="tab-pane fade" id="categories-page" role="tabpanel" aria-labelledby="categories-page-tab">
                                <form id="categoriesPageForm" action="{{ route('admin.homepage.categories.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    
                                    <!-- Hero-Bereich für Kategorien-Seite -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="mb-3">Hero-Bereich</h5>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="categories_hero_title" class="form-label">Überschrift</label>
                                            <input type="text" class="form-control" id="categories_hero_title" name="categories_hero_title"
                                                value="{{ $categoriesPageSettings->get('categories_hero_title')?->value ?? 'Finde und miete, was du brauchst' }}" />
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="categories_hero_subtitle" class="form-label">Untertitel</label>
                                            <input type="text" class="form-control" id="categories_hero_subtitle" name="categories_hero_subtitle"
                                                value="{{ $categoriesPageSettings->get('categories_hero_subtitle')?->value ?? 'Entdecke verschiedene Kategorien von Artikeln, die du mieten kannst' }}" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="categories_hero_image" class="form-label">Hero-Bild</label>
                                            <input type="file" class="form-control" id="categories_hero_image" name="categories_hero_image" />
                                            <small class="text-muted">Empfohlene Bildgröße: 1920x1080px</small>
                                        </div>
                                    </div>

                                    <!-- Hauptkategorien Sektion -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="mb-3">Hauptkategorien Sektion</h5>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="categories_section_enabled" name="categories_section_enabled" value="1"
                                                    {{ ($categoriesPageSettings->get('categories_section_enabled')?->value ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="categories_section_enabled">Sektion aktivieren</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="categories_section_title" class="form-label">Überschrift</label>
                                            <input type="text" class="form-control" id="categories_section_title" name="categories_section_title"
                                                value="{{ $categoriesPageSettings->get('categories_section_title')?->value ?? 'Kategorien durchsuchen' }}" />
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="categories_section_subtitle" class="form-label">Untertitel</label>
                                            <input type="text" class="form-control" id="categories_section_subtitle" name="categories_section_subtitle"
                                                value="{{ $categoriesPageSettings->get('categories_section_subtitle')?->value ?? 'Entdecke verschiedene Kategorien von Artikeln, die du mieten kannst' }}" />
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="categories_section_categories" class="form-label">Anzuzeigende Kategorien</label>
                                            <select class="select2 form-select" id="categories_section_categories" name="categories_section_categories[]" multiple>
                                                @foreach($allCategories as $category)
                                                    <option value="{{ $category->id }}" 
                                                        {{ in_array($category->id, $categoriesPageSettings->get('categories_section_categories')?->value ?? []) ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Wählen Sie die Kategorien aus, die in der Hauptsektion angezeigt werden sollen</small>
                                        </div>
                                    </div>

                                    <!-- Wohnmobil Sektion -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="mb-3">Wohnmobil Sektion</h5>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="wohnmobil_section_enabled" name="wohnmobil_section_enabled" value="1"
                                                    {{ ($categoriesPageSettings->get('wohnmobil_section_enabled')?->value ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="wohnmobil_section_enabled">Sektion aktivieren</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="wohnmobil_section_title" class="form-label">Überschrift</label>
                                            <input type="text" class="form-control" id="wohnmobil_section_title" name="wohnmobil_section_title"
                                                value="{{ $categoriesPageSettings->get('wohnmobil_section_title')?->value ?? 'Wohnmobil entdecken' }}" />
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="wohnmobil_section_subtitle" class="form-label">Untertitel</label>
                                            <input type="text" class="form-control" id="wohnmobil_section_subtitle" name="wohnmobil_section_subtitle"
                                                value="{{ $categoriesPageSettings->get('wohnmobil_section_subtitle')?->value ?? 'Entdecke die Freiheit auf vier Rädern – miete ein Wohnmobil und erlebe deinen perfekten Urlaub. Flexibel, unabhängig und mit allem Komfort, den du brauchst.' }}" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="wohnmobil_section_button_text" class="form-label">Button-Text</label>
                                            <input type="text" class="form-control" id="wohnmobil_section_button_text" name="wohnmobil_section_button_text"
                                                value="{{ $categoriesPageSettings->get('wohnmobil_section_button_text')?->value ?? 'Jetzt entdecken' }}" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="wohnmobil_section_button_link" class="form-label">Button-Link</label>
                                            <input type="text" class="form-control" id="wohnmobil_section_button_link" name="wohnmobil_section_button_link"
                                                value="{{ $categoriesPageSettings->get('wohnmobil_section_button_link')?->value ?? '#' }}" />
                                        </div>
                                    </div>

                                    <!-- Eventartikel Sektion -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="mb-3">Eventartikel Sektion</h5>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="events_section_enabled" name="events_section_enabled" value="1"
                                                    {{ ($categoriesPageSettings->get('events_section_enabled')?->value ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="events_section_enabled">Sektion aktivieren</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="events_section_title" class="form-label">Überschrift</label>
                                            <input type="text" class="form-control" id="events_section_title" name="events_section_title"
                                                value="{{ $categoriesPageSettings->get('events_section_title')?->value ?? 'Eventartikel mieten' }}" />
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="events_section_subtitle" class="form-label">Untertitel</label>
                                            <input type="text" class="form-control" id="events_section_subtitle" name="events_section_subtitle"
                                                value="{{ $categoriesPageSettings->get('events_section_subtitle')?->value ?? 'Alles was du für dein nächstes Event benötigst' }}" />
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="events_section_categories" class="form-label">Event-Kategorien</label>
                                            <select class="select2 form-select" id="events_section_categories" name="events_section_categories[]" multiple>
                                                @foreach($allCategories as $category)
                                                    <option value="{{ $category->id }}" 
                                                        {{ in_array($category->id, $categoriesPageSettings->get('events_section_categories')?->value ?? []) ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Wählen Sie die Event-bezogenen Kategorien aus</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="events_section_button_text" class="form-label">Button-Text</label>
                                            <input type="text" class="form-control" id="events_section_button_text" name="events_section_button_text"
                                                value="{{ $categoriesPageSettings->get('events_section_button_text')?->value ?? 'Alle Eventartikel anzeigen' }}" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="events_section_button_link" class="form-label">Button-Link</label>
                                            <input type="text" class="form-control" id="events_section_button_link" name="events_section_button_link"
                                                value="{{ $categoriesPageSettings->get('events_section_button_link')?->value ?? '/kategorien/events' }}" />
                                        </div>
                                    </div>

                                    <!-- Nutzfahrzeuge Sektion -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="mb-3">Nutzfahrzeuge & Freizeitfahrzeuge Sektion</h5>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="vehicles_section_enabled" name="vehicles_section_enabled" value="1"
                                                    {{ ($categoriesPageSettings->get('vehicles_section_enabled')?->value ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="vehicles_section_enabled">Sektion aktivieren</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="vehicles_section_title" class="form-label">Überschrift</label>
                                            <input type="text" class="form-control" id="vehicles_section_title" name="vehicles_section_title"
                                                value="{{ $categoriesPageSettings->get('vehicles_section_title')?->value ?? 'Nutzfahrzeuge & Freizeitfahrzeuge' }}" />
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="vehicles_section_subtitle" class="form-label">Untertitel</label>
                                            <input type="text" class="form-control" id="vehicles_section_subtitle" name="vehicles_section_subtitle"
                                                value="{{ $categoriesPageSettings->get('vehicles_section_subtitle')?->value ?? 'Für Transport, Urlaub und Ausflüge' }}" />
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="vehicles_section_categories" class="form-label">Fahrzeug-Kategorien</label>
                                            <select class="select2 form-select" id="vehicles_section_categories" name="vehicles_section_categories[]" multiple>
                                                @foreach($allCategories as $category)
                                                    <option value="{{ $category->id }}" 
                                                        {{ in_array($category->id, $categoriesPageSettings->get('vehicles_section_categories')?->value ?? []) ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Wählen Sie die fahrzeugbezogenen Kategorien aus</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="vehicles_section_button_text" class="form-label">Button-Text</label>
                                            <input type="text" class="form-control" id="vehicles_section_button_text" name="vehicles_section_button_text"
                                                value="{{ $categoriesPageSettings->get('vehicles_section_button_text')?->value ?? 'Alle Fahrzeuge anzeigen' }}" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="vehicles_section_button_link" class="form-label">Button-Link</label>
                                            <input type="text" class="form-control" id="vehicles_section_button_link" name="vehicles_section_button_link"
                                                value="{{ $categoriesPageSettings->get('vehicles_section_button_link')?->value ?? '/kategorien/vehicles' }}" />
                                        </div>
                                    </div>

                                    <!-- Baumaschinen Sektion -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <h5 class="mb-3">Baumaschinen & Bauzubehör Sektion</h5>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="construction_section_enabled" name="construction_section_enabled" value="1"
                                                    {{ ($categoriesPageSettings->get('construction_section_enabled')?->value ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="construction_section_enabled">Sektion aktivieren</label>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="construction_section_title" class="form-label">Überschrift</label>
                                            <input type="text" class="form-control" id="construction_section_title" name="construction_section_title"
                                                value="{{ $categoriesPageSettings->get('construction_section_title')?->value ?? 'Baumaschinen & Bauzubehör' }}" />
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="construction_section_subtitle" class="form-label">Untertitel</label>
                                            <input type="text" class="form-control" id="construction_section_subtitle" name="construction_section_subtitle"
                                                value="{{ $categoriesPageSettings->get('construction_section_subtitle')?->value ?? 'Professionelles Equipment für dein Bauprojekt' }}" />
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="construction_section_categories" class="form-label">Bau-Kategorien</label>
                                            <select class="select2 form-select" id="construction_section_categories" name="construction_section_categories[]" multiple>
                                                @foreach($allCategories as $category)
                                                    <option value="{{ $category->id }}" 
                                                        {{ in_array($category->id, $categoriesPageSettings->get('construction_section_categories')?->value ?? []) ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted">Wählen Sie die baubezogenen Kategorien aus</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="construction_section_button_text" class="form-label">Button-Text</label>
                                            <input type="text" class="form-control" id="construction_section_button_text" name="construction_section_button_text"
                                                value="{{ $categoriesPageSettings->get('construction_section_button_text')?->value ?? 'Alle Baumaschinen anzeigen' }}" />
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="construction_section_button_link" class="form-label">Button-Link</label>
                                            <input type="text" class="form-control" id="construction_section_button_link" name="construction_section_button_link"
                                                value="{{ $categoriesPageSettings->get('construction_section_button_link')?->value ?? '/kategorien/construction' }}" />
                                        </div>
                                    </div>

                                    <div class="pt-4">
                                        <button type="submit" class="btn btn-primary me-sm-3 me-1">Kategorien-Seite Speichern</button>
                                        <button type="reset" class="btn btn-label-secondary">Zurücksetzen</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Speichern</button>
                            <button type="reset" class="btn btn-label-secondary">Zurücksetzen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection