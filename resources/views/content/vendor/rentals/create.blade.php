@extends('layouts/contentNavbarLayout')

@section('title', 'Neues Vermietungsobjekt')

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
        'resources/assets/vendor/libs/quill/editor.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/flatpickr/flatpickr.js',
        'resources/assets/vendor/libs/quill/katex.js',
        'resources/assets/vendor/libs/quill/quill.js'
    ])
@endsection

@section('page-script')
    <script>
        // global variables for location and category inputs must be defined and initialized
        // This ensures they are available for the Livewire events and other scripts
        let locationInput = document.getElementById('location_id');
        let categoryInput = document.getElementById('category_id');
        // Wait for Vite assets to load before initializing
        document.addEventListener('DOMContentLoaded', function () {
            // Wait a bit more to ensure all assets are loaded
            setTimeout(function () {
                initializeComponents();
            }, 100);
        });

        function initializeComponents() {
            // Global editor instances
            let descriptionEditor, productDescEditor, rentalConditionsEditor;

            // Initialize Flatpickr with error handling
            try {
                $('.flatpickr').flatpickr();
            } catch (error) {
                console.error('Flatpickr initialization failed:', error);
            }

            // Initialize Quill editors with proper error handling
            function initializeEditors() {
                try {
                    // Check if Quill is available
                    if (typeof Quill === 'undefined') {
                        console.error('Quill is not loaded yet');
                        return;
                    }

                    const editorConfig = {
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline'],
                                [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                                ['link'],
                                ['clean']
                            ]
                        },
                        theme: 'snow',
                        placeholder: 'Geben Sie hier Ihren Text ein...'
                    };

                    if (document.getElementById('rental_description_editor')) {
                        descriptionEditor = new Quill('#rental_description_editor', editorConfig);
                        console.log('Description editor initialized');
                    }

                    if (document.getElementById('product_description_editor')) {
                        productDescEditor = new Quill('#product_description_editor', editorConfig);
                        console.log('Product description editor initialized');
                    }

                    if (document.getElementById('rental_conditions_editor')) {
                        rentalConditionsEditor = new Quill('#rental_conditions_editor', editorConfig);
                        console.log('Rental conditions editor initialized');
                    }
                } catch (error) {
                    console.error('Quill editor initialization failed:', error);
                }
            }

            // Initialize editors
            initializeEditors();

            // Form submission handler
            $('#createRentalForm').on('submit', function (e) {
                try {
                    console.log('🚀 Form submission started');

                    // Sync editor content
                    if (descriptionEditor) {
                        const descriptionContent = descriptionEditor.root.innerHTML;
                        const descriptionInput = document.getElementById('rental_description');
                        if (descriptionInput) {
                            descriptionInput.value = descriptionContent;
                            console.log('📝 Description synced:', descriptionContent.substring(0, 50) + '...');
                        }
                    }

                    if (productDescEditor) {
                        const productContent = productDescEditor.root.innerHTML;
                        const productInput = document.getElementById('product_description');
                        if (productInput) {
                            productInput.value = productContent;
                        }
                    }

                    if (rentalConditionsEditor) {
                        const conditionsContent = rentalConditionsEditor.root.innerHTML;
                        const conditionsInput = document.getElementById('rental_conditions');
                        if (conditionsInput) {
                            conditionsInput.value = conditionsContent;
                        }
                    }

                    // Validate required fields
                    const categoryId = document.getElementById('category_id');
                    const locationId = document.getElementById('location_id');
                    const rentalTitle = document.getElementById('rental_title');
                    const priceRangesId = document.getElementById('price_ranges_id');

                    console.log('🔍 Validating fields:', {
                        title: rentalTitle?.value,
                        category: categoryId?.value,
                        location: locationId?.value,
                        locationLength: locationId?.value?.length,
                        priceRange: priceRangesId?.value
                    });

                    if (!rentalTitle || !rentalTitle.value.trim()) {
                        e.preventDefault();
                        alert('Bitte geben Sie einen Objekttitel ein.');
                        return false;
                    }

                    if (!categoryId || !categoryId.value) {
                        e.preventDefault();
                        alert('Bitte wählen Sie eine Kategorie aus.');
                        return false;
                    }

                    if (!locationId || !locationId.value) {
                        e.preventDefault();
                        alert('Bitte wählen Sie mindestens einen Standort aus.');
                        return false;
                    }

                    if (!priceRangesId || !priceRangesId.value) {
                        e.preventDefault();
                        alert('Bitte wählen Sie einen Preistyp aus.');
                        return false;
                    }

                    console.log('✅ All validations passed, submitting form');
                    // Let form submit normally - no preventDefault needed

                } catch (error) {
                    console.error('Form submission error:', error);
                    e.preventDefault();
                    return false;
                }
            });
        }


        // Livewire event handlers (separate from main initialization)
        document.addEventListener('DOMContentLoaded', function () {
            // Add global event listeners for debugging
            window.addEventListener('livewire:init', () => {
                console.log('Livewire initialized');
            });

            // Alternative event listening approach
            document.addEventListener('livewire:initialized', function () {
                console.log('Livewire components initialized');

                // Location updates
                Livewire.on('locationsUpdated', (event) => {
                    console.log('🔵 Locations updated event:', event);
                    if (event && event.locations && Array.isArray(event.locations)) {
                        // Full location objects with names
                        updateLocationDisplay(event.locations, 'objects');
                    } else if (event && event.location_ids && Array.isArray(event.location_ids)) {
                        // Just IDs
                        updateLocationDisplay(event.location_ids, 'ids');
                    } else if (event && Array.isArray(event)) {
                        // Array of IDs or objects
                        if (event.length > 0 && typeof event[0] === 'object' && event[0].id) {
                            updateLocationDisplay(event, 'objects');
                        } else {
                            updateLocationDisplay(event, 'ids');
                        }
                    }
                });
            });

            // Category selection - Fixed event handling
            Livewire.on('categorySelected', (category) => {
                console.log('🟢 Category selected event received:', category);
                if (category && category[0].id) {
                    updateCategoryDisplay(category[0]);
                } else {
                    console.error('❌ Category data is invalid:', category);
                }
            });

            // Category removal
            Livewire.on('categoryRemoved', () => {
                console.log('🟡 Category selection removed');
                const categoryInput = document.getElementById('category_id');
                const selectedCategoryDiv = document.getElementById('selected-category');

                if (categoryInput) {
                    categoryInput.value = '';
                }

                if (selectedCategoryDiv) {
                    selectedCategoryDiv.innerHTML = `
                                                                <div class="alert alert-warning d-flex align-items-center">
                                                                    <i class="ti ti-info-circle me-2"></i>
                                                                    <span>Bitte wählen Sie eine Kategorie aus</span>
                                                                </div>
                                                            `;
                }
            });

            // Location creation success
            Livewire.on('locationCreated', (event) => {
                console.log('🟣 Location created:', event);
                if (event && event.message) {
                    showSuccessMessage(event.message);
                }
            });
        });



        // Listen for custom events from Livewire
        window.addEventListener('categorySelectedFromLivewire', function (event) {
            console.log('🔥 Custom category event received:', event.detail);
            if (event.detail && event.detail.id) {
                updateCategoryDisplay(event.detail);
            }
        });







        // Helper functions
        function updateLocationDisplay(locationData, dataType = 'ids') {
            const locationInput = document.getElementById('location_id');
            const selectedLocationsDiv = document.getElementById('selected-locations');
            console.log('Updating location display with:', locationData, 'Data type:', dataType);
            // locationData 0 is array with two objects that include arrays  that needs to be displayed'
            console.log(locationData[0]);
            // console.log returns {locations: Array(1), location_ids: Array(1)} I want only the location_ids
            console.log(locationData[0].location_ids);
            locationInput.value = locationData[0].location_ids.join(',');
            var locationcount = locationData[0].locations.length;
            selectedLocationsDiv.innerHTML = `
                                           <div class="alert alert-info d-flex align-items-center">
                                               <i class="ti ti-map-pin me-2"></i>
                                               <span>${locationcount} Standorte ausgewählt</span>
                                           </div>
                                       `;
        }

        function updateCategoryDisplay(category) {
            console.log('Updating category display with:', category);
            const categoryInput = document.getElementById('category_id');
            const selectedCategoryDiv = document.getElementById('selected-category');

            if (categoryInput) {
                categoryInput.value = category.id;
                console.log('Category ID set to:', category.id);
            } else {
                console.error('Category input field not found!');
            }

            if (selectedCategoryDiv) {
                selectedCategoryDiv.innerHTML = `
                                                    <div class="alert alert-success d-flex align-items-center">
                                                        <i class="ti ti-check-circle me-2"></i>
                                                        <div>
                                                            <strong>Ausgewählte Kategorie:</strong> ${category.name}
                                                            <small class="d-block text-muted">Sie können die Kategorie über den Button ändern</small>
                                                        </div>
                                                    </div>
                                                `;
            } else {
                console.error('Selected category div not found!');
            }
        }

        function showSuccessMessage(message) {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = 'toast show position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                                                        <div class="toast-header bg-success text-white">
                                                            <i class="ti ti-check me-2"></i>
                                                            <strong class="me-auto">Erfolg</strong>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                                                        </div>
                                                        <div class="toast-body">
                                                            ${message}
                                                        </div>
                                                    `;

            document.body.appendChild(toast);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 5000);
        }
    </script>
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Vendor / <a href="{{ route('vendor.rentals.index') }}">Vermietungsobjekte</a>
            /</span>
        Neues Objekt
    </h4>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-x me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-triangle me-2"></i>
            <strong>Validierungsfehler:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- livewire komponente für Kategorie auswahl -->
    @livewire('vendor.rentals.categories', [
        'initial-data' => [
            'category_id' => old('category_id', null)
        ]
    ])
    <!-- livewire komponente für Standorte auswahl -->
    @livewire('vendor.rentals.locations', [
        'initial-data' => [
            'location_ids' => old('location_ids', [])
        ]
    ])

    <!-- Dynamic Rental Form - wird über Livewire Events gesteuert -->
    <div id="dynamic-rental-form-container">
        @livewire('vendor.dynamic-rental-form', [
            'rental' => null,
            'categoryId' => null,
            'initialData' => [
                'category_id' => old('category_id', null)
            ]
        ])
    </div>   
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Neues Vermietungsobjekt erstellen</h5>
                </div>
                <div class="card-body">
                        <form id="createRentalForm" method="post" action="{{ route('vendor-rental-save') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12 mb-4">
                                <h6 class="mb-3">1. Grundinformationen</h6>
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <label for="rental_title" class="form-label">Objekttitel</label>
                                            <input type="text" class="form-control" id="rental_title" name="rental_title"
                                            placeholder="z.B. Gemütliches Ferienhaus am See"
                                            value="{{ old('rental_title') }}" required>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="rental_description" class="form-label">Beschreibung</label>
                                        <div id="rental_description_editor">
                                            <p>Beschreiben Sie hier Ihr Vermietungsobjekt...</p>
                                        </div>
                                        <input type="hidden" name="rental_description" id="rental_description">
                                            <small class="form-text text-muted">Nutzen Sie die Formatierungsoptionen (fett,
                                            unterstrichen), um wichtige Informationen hervorzuheben.</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Standort</label>
                                        <div id="selected-locations">
                                            <div class="alert alert-warning d-flex align-items-center">
                                                <i class="ti ti-info-circle me-2"></i>
                                                <span>Bitte wählen Sie mindestens einen Standort aus</span>
                                            </div>
                                        </div>
                                            <input type="hidden" id="location_id" name="location_id" value="" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Kategorie</label>
                                        <div id="selected-category">
                                            <div class="alert alert-warning d-flex align-items-center">
                                                <i class="ti ti-info-circle me-2"></i>
                                                <span>Bitte wählen Sie eine Kategorie aus</span>
                                            </div>
                                        </div>
                                        <input type="hidden" id="category_id" name="category_id" value="" required>
                                    </div>
                                </div>
                                    {{-- Dynamic Rental Fields Section --}}
                                <div id="dynamic-fields-section" class="row" style="display: none;">
                                    <div class="col-12">
                                        <hr class="my-4">
                                        <h6 class="mb-3">
                                            <i class="ti ti-template me-2"></i>Zusätzliche Informationen
                                        </h6>
                                        <div id="dynamic-fields-container">
                                            {{-- Dynamic fields will be loaded here via Livewire --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-4">
                            <h6 class="mb-3">2. Preisgestaltung</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price_ranges_id" class="form-label">Preistyp</label>
                                        <select class="form-select" id="price_ranges_id" name="price_ranges_id" required>
                                        <option value="">Bitte wählen...</option>
                                        <option value="1">Stundenpreis</option>
                                        <option value="2">Tagespreis</option>
                                        <option value="3">Einmalpreis</option>
                                        <option value="4">Saisonpreis</option>
                                        </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="price_range_hour" class="form-label">Stundenpreis (€)</label>
                                    <input type="number" class="form-control" id="price_range_hour" name="price_range_hour"
                                            min="0" step="0.01">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="price_range_day" class="form-label">Tagespreis (€)</label>
                                    <input type="number" class="form-control" id="price_range_day" name="price_range_day"
                                        min="0" step="0.01">
                                </div>
                                    <div class="col-md-6 mb-3">
                                    <label for="price_range_once" class="form-label">Einmalpreis (€)</label>
                                    <input type="number" class="form-control" id="price_range_once" name="price_range_once"
                                        min="0" step="0.01">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="service_fee" class="form-label">Servicegebühr (€)</label>
                                    <input type="number" class="form-control" id="service_fee" name="service_fee" min="0"
                                        step="0.01" value="0" placeholder="0.00">
                                    <small class="form-text text-muted">Optional: Zusätzliche Servicegebühr</small>
                                </div>
                            </div>
                            </div>
                        <div class="col-12 mb-4">
                            <h6 class="mb-3">3. Bildverwaltung</h6>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="alert alert-info">
                                        <i class="ti ti-info-circle me-2"></i>
                                        <strong>Hinweis:</strong> Bilder können nach dem ersten Speichern des
                                            Vermietungsobjekts hochgeladen werden.
                                    </div>
                                </div>
                            </div>
                        </div>
                            <div class="col-12 mb-4">
                            <h6 class="mb-3">4. Dokumente</h6>
                            <div class="alert alert-info mb-3">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Hinweis:</strong> Alle Dokumente müssen im PDF-Format vorliegen und dürfen maximal 10MB groß sein.
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="rental_terms_condition" class="form-label">AGB</label>
                                    <div class="input-group">
                                        <input class="form-control rental-document-input" type="file" id="rental_terms_condition"
                                               name="rental_terms_condition" accept=".pdf" data-max-size="10">
                                        <button type="button" class="btn btn-outline-secondary clear-file-btn" data-target="rental_terms_condition">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    <div class="file-info mt-1" id="rental_terms_condition_info" style="display: none;">
                                        <small class="text-success">
                                            <i class="ti ti-check-circle me-1"></i>
                                            <span class="file-name"></span> (<span class="file-size"></span>)
                                        </small>
                                    </div>
                                    <div class="file-error mt-1" id="rental_terms_condition_error" style="display: none;">
                                        <small class="text-danger">
                                            <i class="ti ti-alert-triangle me-1"></i>
                                            <span class="error-message"></span>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                        <label for="rental_specifications" class="form-label">Spezifikationen</label>
                                    <div class="input-group">
                                        <input class="form-control rental-document-input" type="file" id="rental_specifications"
                                               name="rental_specifications" accept=".pdf" data-max-size="10">
                                        <button type="button" class="btn btn-outline-secondary clear-file-btn" data-target="rental_specifications">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    <div class="file-info mt-1" id="rental_specifications_info" style="display: none;">
                                        <small class="text-success">
                                            <i class="ti ti-check-circle me-1"></i>
                                            <span class="file-name"></span> (<span class="file-size"></span>)
                                        </small>
                                    </div>
                                    <div class="file-error mt-1" id="rental_specifications_error" style="display: none;">
                                        <small class="text-danger">
                                            <i class="ti ti-alert-triangle me-1"></i>
                                            <span class="error-message"></span>
                                        </small>
                                    </div>
                                </div>
                                    <div class="col-md-4 mb-3">
                                    <label for="rental_owner_info" class="form-label">Vermieterinformationen</label>
                                    <div class="input-group">
                                        <input class="form-control rental-document-input" type="file" id="rental_owner_info" 
                                               name="rental_owner_info" accept=".pdf" data-max-size="10">
                                        <button type="button" class="btn btn-outline-secondary clear-file-btn" data-target="rental_owner_info">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    <div class="file-info mt-1" id="rental_owner_info_info" style="display: none;">
                                        <small class="text-success">
                                            <i class="ti ti-check-circle me-1"></i>
                                            <span class="file-name"></span> (<span class="file-size"></span>)
                                        </small>
                                    </div>
                                    <div class="file-error mt-1" id="rental_owner_info_error" style="display: none;">
                                        <small class="text-danger">
                                            <i class="ti ti-alert-triangle me-1"></i>
                                            <span class="error-message"></span>
                                        </small>
                                    </div>
                                </div>
                                    <div class="col-md-4 mb-3">
                                    <label for="rental_contract" class="form-label">Mietvertrag</label>
                                    <div class="input-group">
                                        <input class="form-control rental-document-input" type="file" id="rental_contract" 
                                               name="rental_contract" accept=".pdf" data-max-size="10">
                                        <button type="button" class="btn btn-outline-secondary clear-file-btn" data-target="rental_contract">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    <div class="file-info mt-1" id="rental_contract_info" style="display: none;">
                                        <small class="text-success">
                                            <i class="ti ti-check-circle me-1"></i>
                                            <span class="file-name"></span> (<span class="file-size"></span>)
                                        </small>
                                    </div>
                                    <div class="file-error mt-1" id="rental_contract_error" style="display: none;">
                                        <small class="text-danger">
                                            <i class="ti ti-alert-triangle me-1"></i>
                                            <span class="error-message"></span>
                                        </small>
                                    </div>
                                </div>
                                    <div class="col-md-4 mb-3">
                                    <label for="rental_directions" class="form-label">Anfahrtsbeschreibung</label>
                                    <div class="input-group">
                                        <input class="form-control rental-document-input" type="file" id="rental_directions" 
                                               name="rental_directions" accept=".pdf" data-max-size="10">
                                        <button type="button" class="btn btn-outline-secondary clear-file-btn" data-target="rental_directions">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    <div class="file-info mt-1" id="rental_directions_info" style="display: none;">
                                        <small class="text-success">
                                            <i class="ti ti-check-circle me-1"></i>
                                            <span class="file-name"></span> (<span class="file-size"></span>)
                                        </small>
                                    </div>
                                    <div class="file-error mt-1" id="rental_directions_error" style="display: none;">
                                        <small class="text-danger">
                                            <i class="ti ti-alert-triangle me-1"></i>
                                            <span class="error-message"></span>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="rental_catalog" class="form-label">Mietkatalog</label>
                                    <div class="input-group">
                                        <input class="form-control rental-document-input" type="file" id="rental_catalog" 
                                               name="rental_catalog" accept=".pdf" data-max-size="10">
                                        <button type="button" class="btn btn-outline-secondary clear-file-btn" data-target="rental_catalog">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    <div class="file-info mt-1" id="rental_catalog_info" style="display: none;">
                                        <small class="text-success">
                                            <i class="ti ti-check-circle me-1"></i>
                                            <span class="file-name"></span> (<span class="file-size"></span>)
                                        </small>
                                    </div>
                                    <div class="file-error mt-1" id="rental_catalog_error" style="display: none;">
                                        <small class="text-danger">
                                            <i class="ti ti-alert-triangle me-1"></i>
                                            <span class="error-message"></span>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="rental_floor_plan" class="form-label">Grundriss</label>
                                    <div class="input-group">
                                        <input class="form-control rental-document-input" type="file" id="rental_floor_plan" 
                                               name="rental_floor_plan" accept=".pdf" data-max-size="10">
                                        <button type="button" class="btn btn-outline-secondary clear-file-btn" data-target="rental_floor_plan">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    <div class="file-info mt-1" id="rental_floor_plan_info" style="display: none;">
                                        <small class="text-success">
                                            <i class="ti ti-check-circle me-1"></i>
                                            <span class="file-name"></span> (<span class="file-size"></span>)
                                        </small>
                                    </div>
                                    <div class="file-error mt-1" id="rental_floor_plan_error" style="display: none;">
                                        <small class="text-danger">
                                            <i class="ti ti-alert-triangle me-1"></i>
                                            <span class="error-message"></span>
                                        </small>
                                    </div>
                                    </div>
                                <div class="col-md-4 mb-3">
                                    <label for="rental_prices_seasons" class="form-label">Preise und
                                        Saisonzeiten</label>
                                    <div class="input-group">
                                        <input class="form-control rental-document-input" type="file" id="rental_prices_seasons"
                                               name="rental_prices_seasons" accept=".pdf" data-max-size="10">
                                        <button type="button" class="btn btn-outline-secondary clear-file-btn" data-target="rental_prices_seasons">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    <div class="file-info mt-1" id="rental_prices_seasons_info" style="display: none;">
                                        <small class="text-success">
                                            <i class="ti ti-check-circle me-1"></i>
                                            <span class="file-name"></span> (<span class="file-size"></span>)
                                        </small>
                                    </div>
                                    <div class="file-error mt-1" id="rental_prices_seasons_error" style="display: none;">
                                        <small class="text-danger">
                                            <i class="ti ti-alert-triangle me-1"></i>
                                            <span class="error-message"></span>
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="rental_safety_info" class="form-label">Sicherheitshinweise</label>
                                    <div class="input-group">
                                        <input class="form-control rental-document-input" type="file" id="rental_safety_info"
                                               name="rental_safety_info" accept=".pdf" data-max-size="10">
                                        <button type="button" class="btn btn-outline-secondary clear-file-btn" data-target="rental_safety_info">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    <div class="file-info mt-1" id="rental_safety_info_info" style="display: none;">
                                        <small class="text-success">
                                            <i class="ti ti-check-circle me-1"></i>
                                            <span class="file-name"></span> (<span class="file-size"></span>)
                                        </small>
                                    </div>
                                    <div class="file-error mt-1" id="rental_safety_info_error" style="display: none;">
                                        <small class="text-danger">
                                            <i class="ti ti-alert-triangle me-1"></i>
                                            <span class="error-message"></span>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="rental_additional_info" class="form-label">Weitere Informationen</label>
                                    <div class="input-group">
                                        <input class="form-control rental-document-input" type="file" id="rental_additional_info"
                                               name="rental_additional_info" accept=".pdf" data-max-size="10">
                                        <button type="button" class="btn btn-outline-secondary clear-file-btn" data-target="rental_additional_info">
                                            <i class="ti ti-x"></i>
                                        </button>
                                    </div>
                                    <div class="file-info mt-1" id="rental_additional_info_info" style="display: none;">
                                        <small class="text-success">
                                            <i class="ti ti-check-circle me-1"></i>
                                            <span class="file-name"></span> (<span class="file-size"></span>)
                                        </small>
                                    </div>
                                    <div class="file-error mt-1" id="rental_additional_info_error" style="display: none;">
                                        <small class="text-danger">
                                            <i class="ti ti-alert-triangle me-1"></i>
                                            <span class="error-message"></span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mb-4">
                            <h6 class="mb-3">5. Zusatzinformationen</h6>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="product_description" class="form-label">Produktbeschreibung</label>
                                    <div id="product_description_editor">
                                        <p>Beschreiben Sie hier die Ausstattung und Besonderheiten Ihres
                                            Vermietungsobjekts...</p>
                                    </div>
                                    <input type="hidden" name="product_description" id="product_description">
                                    <small class="form-text text-muted">Nutzen Sie die Formatierungsoptionen (fett,
                                            unterstrichen), um wichtige Informationen hervorzuheben.</small>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="rental_conditions" class="form-label">Mietbedingungen</label>
                                    <div id="rental_conditions_editor">
                                        <p>Beschreiben Sie hier die Mietbedingungen...</p>
                                    </div>
                                    <input type="hidden" name="rental_conditions" id="rental_conditions">
                                    <small class="form-text text-muted">Nutzen Sie die Formatierungsoptionen (fett,
                                        unterstrichen), um wichtige Informationen hervorzuheben.</small>
                                    </div>
                            </div>
                        </div>
                        <div class="col-12 text-end">
                            <button type="button" class="btn btn-label-secondary me-2">Abbrechen</button>
                            <button type="submit" class="btn btn-primary">Speichern</button>
                        </div>
                </div>
                </form>
                </div>
        </div>
    </div>
    </div>
    {{-- JavaScript for Dynamic Fields Integration --}}
    <script>
            document.addEventListener('DOMContentLoaded', function () {
            // Initialize rental document upload handlers
            initializeRentalDocumentUploads();
            
            // Listen for category selection events from existing category component
            document.addEventListener('categorySelectedFromLivewire', function (event) {
                const category = event.detail;
                    const categoryId = category ? category.id : null;
                loadDynamicFields(categoryId);
            });
            // Also listen for Livewire events
                Livewire.on('categorySelected', (category) => {
                const categoryId = category && category.id ? category.id : null;
                loadDynamicFields(categoryId);
            });
        });

        // Initialize rental document upload handlers
        function initializeRentalDocumentUploads() {
            // File size validation and clear button functionality
            const maxFileSizeMB = 10; // 10MB limit
            const maxFileSizeBytes = maxFileSizeMB * 1024 * 1024;

            // Handle file input changes
            document.querySelectorAll('.rental-document-input').forEach(input => {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const inputId = this.id;
                    
                    if (file) {
                        // Validate file size
                        if (file.size > maxFileSizeBytes) {
                            showFileError(inputId, `Datei zu groß. Maximale Größe: ${maxFileSizeMB}MB`);
                            this.value = ''; // Clear the input
                            return;
                        }

                        // Validate file type
                        if (!file.type.includes('pdf')) {
                            showFileError(inputId, 'Nur PDF-Dateien sind erlaubt');
                            this.value = ''; // Clear the input
                            return;
                        }

                        // Show success info
                        showFileInfo(inputId, file.name, formatFileSize(file.size));
                    } else {
                        hideFileInfo(inputId);
                    }
                });
            });

            // Handle clear button clicks
            document.querySelectorAll('.clear-file-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    
                    if (input) {
                        input.value = ''; // Clear the file input
                        hideFileInfo(targetId);
                        hideFileError(targetId);
                    }
                });
            });
        }

        // Helper functions for file handling
        function showFileInfo(inputId, fileName, fileSize) {
            const infoDiv = document.getElementById(inputId + '_info');
            const errorDiv = document.getElementById(inputId + '_error');
            
            if (infoDiv) {
                infoDiv.querySelector('.file-name').textContent = fileName;
                infoDiv.querySelector('.file-size').textContent = fileSize;
                infoDiv.style.display = 'block';
            }
            
            if (errorDiv) {
                errorDiv.style.display = 'none';
            }
        }

        function showFileError(inputId, message) {
            const errorDiv = document.getElementById(inputId + '_error');
            const infoDiv = document.getElementById(inputId + '_info');
            
            if (errorDiv) {
                errorDiv.querySelector('.error-message').textContent = message;
                errorDiv.style.display = 'block';
            }
            
            if (infoDiv) {
                infoDiv.style.display = 'none';
            }
        }

        function hideFileInfo(inputId) {
            const infoDiv = document.getElementById(inputId + '_info');
            if (infoDiv) {
                infoDiv.style.display = 'none';
            }
        }

        function hideFileError(inputId) {
            const errorDiv = document.getElementById(inputId + '_error');
            if (errorDiv) {
                errorDiv.style.display = 'none';
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function loadDynamicFields(categoryId) {
            const dynamicFieldsSection = document.getElementById('dynamic-fields-section');
                const dynamicFieldsContainer = document.getElementById('dynamic-fields-container');
            if (!dynamicFieldsSection || !dynamicFieldsContainer) {
                    console.log('Dynamic fields containers not found');
                    return;
                }

                if (!categoryId) {
                    dynamicFieldsSection.style.display = 'none';
                    return;
                }
            // Show the section with a smooth animation
            dynamicFieldsSection.style.display = 'block';
            // Show loading indicator
            dynamicFieldsContainer.innerHTML = `
                    <div class="d-flex justify-content-center align-items-center py-4">
                        <div class="spinner-border text-primary me-3" role="status">
                            <span class="visually-hidden">Lade dynamische Felder...</span>
                            </div>
                            <span class="text-muted">Lade zusätzliche Felder für diese Kategorie...</span>
                        </div>
                    `;

                            // Load Livewire component dynamically via AJAX
                fetch(`/dynamic-fields/${categoryId}`, {
                    method: 'GET',
    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.text();
                })
                .then(html => {
                    dynamicFieldsContainer.innerHTML = html;

                    // Initialize Livewire components in the new content (Livewire v3 compatible)
                    if (typeof Livewire !== 'undefined') {
                        try {
                            // Try different Livewire methods for compatibility
                            if (typeof Livewire.restart === 'function') {
                                Livewire.restart();
                        } else if (typeof Livewire.start === 'function') {
                            Livewire.start();
                        } else if (typeof Livewire.rescan === 'function') {
                                Livewire.rescan();
                            } else {
                                console.log('Livewire loaded but no rescan method available');
                            }
                        } catch (livewireError) {
                            console.warn('Livewire initialization failed:', livewireError);
                    }
                } else {
                    console.log('Livewire not available for dynamic content initialization');
                    }
                })
                    .catch(error => {
                        console.error('Error loading dynamic fields:', error);
                        dynamicFieldsContainer.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="ti ti-alert-triangle me-2"></i>
                                <strong>Fehler beim Laden der Felder</strong><br>
                                Bitte versuchen Sie es erneut oder wählen Sie eine andere Kategorie.
                            </div>
                        `;
                    });
            }
        </script>
@endsection