    <form action="<?php echo e(route('search')); ?>" method="GET" wire:ignore>
        <div class="row g-3">

            
            <div class="col-12 col-md-4">
                <div class="form-floating position-relative">
                    
                    <input type="text" class="form-control" id="searchQuery" name="query" placeholder="Ich suche..."
                        value="<?php echo e($query); ?>" autocomplete="off">
                    <label for="searchQuery">Was möchtest du mieten?</label>

                    
                    <button type="button" class="btn btn-lg btn-outline-secondary clear-btn d-none p-4"
                        data-target="searchQuery"
                        style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); z-index: 10; ">
                        <i class="ti ti-x" style="font-size: 12px;"></i>
                    </button>

                    
                    <div id="searchSuggestions" class="search-suggestions" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 1050; 
                                background: white; border: 1px solid #d9dee3; border-radius: 6px; 
                                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); max-height: 400px; overflow-y: auto;">

                        
                        <div id="categoriesSection" style="display: none;">
                            <div class="px-3 py-2 bg-light border-bottom">
                                <small class="text-muted fw-semibold">KATEGORIEN</small>
                            </div>
                            <div id="categoriesList"></div>
                        </div>

                        
                        <div id="searchInCategoriesSection" style="display: none;">
                            <div class="px-3 py-2 bg-light border-bottom">
                                <small class="text-muted fw-semibold">SUCHEN IN KATEGORIEN</small>
                            </div>
                            <div id="searchInCategoriesList"></div>
                        </div>

                        
                        <div id="searchInAllSection" style="display: none;">
                            <div class="search-option px-3 py-2 d-flex align-items-center"
                                style="cursor: pointer; border-top: 1px solid #f1f1f1;" data-type="search-all">
                                <i class="ti ti-search me-2"></i>
                                <span id="searchInAllText"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-12 col-md-3 d-flex ">
                <div class="form-floating position-relative w-100">
                    <input type="text" class="form-control" id="location" name="location" placeholder="Ort / PLZ"
                        value="<?php echo e($location); ?>" autocomplete="off">
                    <label for="location">Wo?</label>

                    
                    <button type="button" class="btn btn-lg btn-outline-secondary clear-btn d-none p-4"
                        data-target="location"
                        style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); z-index: 10; ">
                        <i class="ti ti-x" style="font-size: 12px;"></i>
                    </button>

                    
                    <div id="locationSuggestions" class="location-suggestions" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 1050; 
                                background: white; border: 1px solid #d9dee3; border-radius: 6px; 
                                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); max-height: 300px; overflow-y: auto;">

                        
                        <div class="px-3 py-2 bg-light border-bottom">
                            <small class="text-muted fw-semibold">ORTE & PLZ</small>
                        </div>

                        
                        <div id="locationSuggestionsList"></div>

                        
                        <div id="locationNoResults" class="px-3 py-2 text-muted" style="display: none;">
                            <small>Keine Ergebnisse gefunden</small>
                        </div>
                    </div>
                </div>
                
            </div>

            
            <div class="col-12 col-md-1">
                <div class="position-relative">
                    <div class="country-dropdown" style="height: 58px;">
                        
                        <button type="button"
                            class="form-control d-flex align-items-center justify-content-between country-dropdown-btn"
                            style="height: 58px; border: 1px solid #d9dee3; background: #fff; text-align: left;">
                            <span class="selected-country d-flex align-items-center">
                                <!--[if BLOCK]><![endif]--><?php if($countryCode): ?>
                                    <?php
    $selectedCountry = $this->activeCountries->firstWhere('code', $countryCode);
                                    ?>
                                    <!--[if BLOCK]><![endif]--><?php if($selectedCountry && file_exists(public_path('assets/img/flags/' . strtolower($selectedCountry->code) . '.svg'))): ?>
                                        <img src="<?php echo e(asset('assets/img/flags/' . strtolower($selectedCountry->code) . '.svg')); ?>"
                                            alt="<?php echo e($selectedCountry->name); ?>"
                                            style="width: 20px; height: 15px; margin-right: 8px;">
                                        <?php echo e($selectedCountry->code); ?>

                                    <?php elseif($selectedCountry): ?>
                                        <i class="flag-icon flag-icon-<?php echo e(strtolower($selectedCountry->code)); ?> me-2"></i>
                                        <?php echo e($selectedCountry->code); ?>

                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                <?php else: ?>
                                    <i class="ti ti-world me-2"></i>
                                    Alle
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </span>
                            <i class="ti ti-chevron-down"></i>
                        </button>

                        
                        <div class="country-dropdown-menu"
                            style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; 
                                    background: white; border: 1px solid #d9dee3; border-radius: 6px; 
                                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); max-height: 200px; overflow-y: auto;">

                            
                            <div class="country-option p-2 d-flex align-items-center" data-value=""
                                style="cursor: pointer; border-bottom: 1px solid #f1f1f1;">
                                <i class="ti ti-world me-2"></i>
                                <span>Alle</span>
                            </div>

                            
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->activeCountries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="country-option p-2 d-flex align-items-center <?php echo e($countryCode == $country->code ? 'selected' : ''); ?>"
                                    data-value="<?php echo e($country->code); ?>"
                                    style="cursor: pointer; border-bottom: 1px solid #f1f1f1;">
                                    <!--[if BLOCK]><![endif]--><?php if(file_exists(public_path('assets/img/flags/' . strtolower($country->code) . '.svg'))): ?>
                                        <img src="<?php echo e(asset('assets/img/flags/' . strtolower($country->code) . '.svg')); ?>"
                                            alt="<?php echo e($country->name); ?>" style="width: 20px; height: 15px; margin-right: 8px;">
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <span><?php echo e($country->code); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                    
                    <input type="hidden" id="countryCode" name="countryCode" value="<?php echo e($countryCode); ?>">
                </div>
            </div>

            
            <input type="hidden" id="dateFrom" name="dateFrom" value="<?php echo e($dateFrom); ?>">
            <input type="hidden" id="dateTo" name="dateTo" value="<?php echo e($dateTo); ?>">
            <input type="hidden" id="selectedCountryCode" name="country" value="de">

            
            <div class="col-12 col-md-3">
                <div class="form-floating position-relative">
                    <input type="text" class="form-control" id="dateRange" name="dateRange"
                        placeholder="Zeitraum auswählen" readonly value="<?php echo e($dateRange); ?>">
                    <label for="dateRange">Zeitraum</label>

                    
                    <button type="button" class="btn btn-lg btn-outline-secondary clear-btn d-none p-4"
                        data-target="dateRange"
                        style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); z-index: 10; ">
                        <i class="ti ti-x" style="font-size: 12px;"></i>
                    </button>
                </div>
            </div>

            
            <div class="col-12 col-md-1">
                <button type="submit" class="btn btn-primary h-100 w-100">
                    <i class="ti ti-search"></i>
                </button>
            </div>
        </div>
            <script>
                document.addEventListener('livewire:load', function () {
                    // Re-Initialisierung nach Livewire-Load
                    if (typeof window.initializeClearButtons === 'function') {
                        window.initializeClearButtons();
                    }
                    if (typeof window.initializeDateRangePicker === 'function') {
                        window.initializeDateRangePicker();
                    }

                    // Länder-Dropdown initialisieren
                    initializeCountryDropdown();
                });


                function initializeCountryDropdown() {
                    const dropdownBtn = document.querySelector('.country-dropdown-btn');
                    const dropdownMenu = document.querySelector('.country-dropdown-menu');
                    const hiddenInput = document.querySelector('#countryCode');
                    const selectedCountrySpan = document.querySelector('.selected-country');
                    const chevronIcon = dropdownBtn.querySelector('.ti-chevron-down');

                    if (!dropdownBtn || !dropdownMenu) return;

                    // Dropdown Toggle bei Button-Klick
                    dropdownBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        const isOpen = dropdownMenu.style.display === 'block';
                        dropdownMenu.style.display = isOpen ? 'none' : 'block';
                        chevronIcon.classList.toggle('ti-chevron-up', !isOpen);
                        chevronIcon.classList.toggle('ti-chevron-down', isOpen);
                    });

                    // Länder-Optionen Event-Handler
                    const countryOptions = document.querySelectorAll('.country-option');
                    countryOptions.forEach(option => {
                        option.addEventListener('click', function () {
                            const value = this.getAttribute('data-value');
                            const flagElement = this.querySelector('img, i[class*="flag-icon"], i[class*="ti-world"]');
                            const textElement = this.querySelector('span');

                            // Verstecktes Input aktualisieren
                            hiddenInput.value = value;

                            // Selected-Klasse von allen Optionen entfernen
                            countryOptions.forEach(opt => opt.classList.remove('selected'));

                            // Selected-Klasse zur geklickten Option hinzufügen
                            this.classList.add('selected');

                            // Button-Anzeige aktualisieren
                            if (flagElement && textElement) {
                                const flagClone = flagElement.cloneNode(true);
                                selectedCountrySpan.innerHTML = '';
                                selectedCountrySpan.appendChild(flagClone);
                                selectedCountrySpan.appendChild(document.createTextNode(textElement.textContent));
                            }

                            // Dropdown schließen
                            dropdownMenu.style.display = 'none';
                            chevronIcon.classList.add('ti-chevron-down');
                            chevronIcon.classList.remove('ti-chevron-up');

                            // Location-Suggestions aktualisieren wenn Location-Feld Inhalt hat
                            const locationInput = document.querySelector('#location');
                            if (locationInput && locationInput.value.trim().length >= 2) {
                                const event = new Event('input', { bubbles: true });
                                locationInput.dispatchEvent(event);
                            }
                        });

                        // Hover-Effekte
                        option.addEventListener('mouseenter', function () {
                            this.style.backgroundColor = '#f8f9fa';
                        });

                        option.addEventListener('mouseleave', function () {
                            this.style.backgroundColor = '';
                        });
                    });

                    // Dropdown bei Klick außerhalb schließen
                    document.addEventListener('click', function (e) {
                        if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                            dropdownMenu.style.display = 'none';
                            chevronIcon.classList.add('ti-chevron-down');
                            chevronIcon.classList.remove('ti-chevron-up');
                        }
                    });
                }

                /**
                * LOCATION SUGGESTIONS FUNKTIONALITÄT
                * ===================================
                *
                * Verwaltet die Location-Suggestions basierend auf PLZ/Stadt-Daten
                * aus den postal_codes Tabellen
                */
                function initializeLocationSuggestions() {
                    const locationInput = document.querySelector('#location');
                    const locationSuggestionsDropdown = document.querySelector('#locationSuggestions');
                    const locationSuggestionsList = document.querySelector('#locationSuggestionsList');
                    const locationNoResults = document.querySelector('#locationNoResults');
                    let locationSearchTimeout;

                    if (!locationInput || !locationSuggestionsDropdown) return;

                    // Input-Events behandeln
                    locationInput.addEventListener('input', function () {
                        const query = this.value.trim();
                        const countryCode = document.querySelector('#countryCode').value || 'DE';

                        clearTimeout(locationSearchTimeout);

                        if (query.length < 2) { hideLocationSuggestions(); return; } // Debounce für Location-Suchanfragen (300ms)
                        locationSearchTimeout = setTimeout(() => {
                            fetchLocationSuggestions(query, countryCode);
                        }, 300);
                    });

                    // Focus-Events behandeln
                    locationInput.addEventListener('focus', function () {
                        const query = this.value.trim();
                        const countryCode = document.querySelector('#countryCode').value || 'DE';
                        if (query.length >= 2) {
                            fetchLocationSuggestions(query, countryCode);
                        }
                    });

                    // Vorschläge bei Klick außerhalb ausblenden
                    document.addEventListener('click', function (e) {
                        if (!locationInput.contains(e.target) && !locationSuggestionsDropdown.contains(e.target)) {
                            hideLocationSuggestions();
                        }
                    });

                    /**
                    * Location-Suggestions von der API abrufen
                    * @param {string} query - Suchanfrage
                    * @param {string} countryCode - Länder-Code
                    */
                    function fetchLocationSuggestions(query, countryCode) {
                        fetch(`/api/locations/suggestions?query=${encodeURIComponent(query)}&countryCode=${encodeURIComponent(countryCode)}`)
                            .then(response => response.json())
                            .then(data => {
                                displayLocationSuggestions(data.suggestions, query);
                            })
                            .catch(error => {
                                console.error('Error fetching location suggestions:', error);
                                hideLocationSuggestions();
                            });
                    }

                    /**
                    * Location-Suggestions anzeigen
                    * @param {Array} suggestions - API-Antwort mit Location-Daten
                    * @param {string} query - Ursprüngliche Suchanfrage
                    */
                    function displayLocationSuggestions(suggestions, query) {
                        // Vorherige Ergebnisse löschen
                        locationSuggestionsList.innerHTML = '';

                        if (suggestions && suggestions.length > 0) {
                            suggestions.forEach(suggestion => {
                                const item = createLocationItem(suggestion, query);
                                locationSuggestionsList.appendChild(item);
                            });
                            locationNoResults.style.display = 'none';
                        } else {
                            locationNoResults.style.display = 'block';
                        }

                        // Dropdown anzeigen
                        locationSuggestionsDropdown.style.display = 'block';
                    }

                    /**
                    * Location-Item erstellen
                    * @param {Object} suggestion - Location-Daten
                    * @param {string} query - Suchanfrage
                    * @returns {HTMLElement} Erstelltes DOM-Element
                    */
                    function createLocationItem(suggestion, query) {
                        const item = document.createElement('div');
                        item.className = 'location-option px-3 py-2 d-flex align-items-center';
                        item.style.cssText = 'cursor: pointer; border-bottom: 1px solid #f8f9fa;';
                        item.setAttribute('data-type', 'location');
                        item.setAttribute('data-postal-code', suggestion.postal_code);
                        item.setAttribute('data-city', suggestion.city);
                        item.setAttribute('data-sub-city', suggestion.sub_city || '');
                        item.setAttribute('data-region', suggestion.region || '');

                        // Highlight query in display name
                        const displayName = suggestion.display_name;
                        const highlightedName = displayName.replace(
                            new RegExp(`(${query})`, 'gi'),
                            '<strong>$1</strong>'
                        );

                        item.innerHTML = `
                    <i class="ti ti-map-pin me-2"></i>
                    <div class="flex-grow-1">
                        <div class="fw-semibold">${highlightedName}</div>
                        ${suggestion.region ? `<small class="text-muted">${suggestion.region}</small>` : ''}
                    </div>
                    `;

                        // Click-Handler für Location-Auswahl
                        item.addEventListener('click', function () {
                            locationInput.value = suggestion.display_name;
                            hideLocationSuggestions();
                        });

                        // Hover-Effekt
                        item.addEventListener('mouseenter', function () {
                            this.style.backgroundColor = '#f8f9fa';
                        });
                        item.addEventListener('mouseleave', function () {
                            this.style.backgroundColor = '';
                        });

                        return item;
                    }

                    /**
                    * Location-Suggestions ausblenden
                    */
                    function hideLocationSuggestions() {
                        locationSuggestionsDropdown.style.display = 'none';
                    }
                }

                /**
                * SUCHVORSCHLÄGE FUNKTIONALITÄT
                * =============================
                *
                * Verwaltet die intelligente Suchvorschläge mit Kategorie-Filterung
                * und Auto-Complete Funktionalität
                */
                function initializeSearchSuggestions() {
                    const searchInput = document.querySelector('#searchQuery');
                    const suggestionsDropdown = document.querySelector('#searchSuggestions');
                    let searchTimeout;

                    if (!searchInput || !suggestionsDropdown) return;

                    // Input-Events behandeln
                    searchInput.addEventListener('input', function () {
                        const query = this.value.trim();

                        clearTimeout(searchTimeout);

                        if (query.length < 2) { hideSuggestions(); return; } // Debounce für Suchanfragen (300ms)
                        searchTimeout = setTimeout(() => {
                            fetchSuggestions(query);
                        }, 300);
                    });

                    // Focus-Events behandeln
                    searchInput.addEventListener('focus', function () {
                        const query = this.value.trim();
                        if (query.length >= 2) {
                            fetchSuggestions(query);
                        }
                    });

                    // Vorschläge bei Klick außerhalb ausblenden
                    document.addEventListener('click', function (e) {
                        if (!searchInput.contains(e.target) && !suggestionsDropdown.contains(e.target)) {
                            hideSuggestions();
                        }
                    });

                    /**
                    * Suchvorschläge von der API abrufen
                    * @param {string} query - Suchanfrage
                    */
                    function fetchSuggestions(query) {
                        fetch(`/api/categories/suggestions?query=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                displaySuggestions(data, query);
                            })
                            .catch(error => {
                                console.error('Error fetching suggestions:', error);
                                hideSuggestions();
                            });
                    }

                    /**
                    * Suchvorschläge anzeigen
                    * @param {Object} data - API-Antwort mit Kategorien und Suchoptionen
                    * @param {string} query - Ursprüngliche Suchanfrage
                    */
                    function displaySuggestions(data, query) {
                        const categoriesSection = document.querySelector('#categoriesSection');
                        const categoriesList = document.querySelector('#categoriesList');
                        const searchInCategoriesSection = document.querySelector('#searchInCategoriesSection');
                        const searchInCategoriesList = document.querySelector('#searchInCategoriesList');
                        const searchInAllSection = document.querySelector('#searchInAllSection');
                        const searchInAllText = document.querySelector('#searchInAllText');

                        // Vorherige Ergebnisse löschen
                        categoriesList.innerHTML = '';
                        searchInCategoriesList.innerHTML = '';

                        // Kategorien-Sektion anzeigen/verstecken
                        if (data.categories && data.categories.length > 0) {
                            data.categories.forEach(category => {
                                const item = createCategoryItem(category);
                                categoriesList.appendChild(item);
                            });
                            categoriesSection.style.display = 'block';
                        } else {
                            categoriesSection.style.display = 'none';
                        }

                        // "Suchen in Kategorien" Sektion anzeigen/verstecken
                        if (data.searchOptions && data.searchOptions.length > 1) {
                            // "Suchen in allen Kategorien" Option für diese Sektion ausschließen
                            const categorySearchOptions = data.searchOptions.slice(0, -1);

                            categorySearchOptions.forEach(option => {
                                const item = createSearchInCategoryItem(option, query);
                                searchInCategoriesList.appendChild(item);
                            });
                            searchInCategoriesSection.style.display = 'block';
                        } else {
                            searchInCategoriesSection.style.display = 'none';
                        }

                        // "Suchen in allen Kategorien" anzeigen
                        if (query) {
                            searchInAllText.textContent = `${query} in allen Kategorien`;
                            searchInAllSection.style.display = 'block';
                        } else {
                            searchInAllSection.style.display = 'none';
                        }

                        // Dropdown anzeigen
                        suggestionsDropdown.style.display = 'block';
                    }

                    /**
                    * Kategorie-Item erstellen
                    * @param {Object} category - Kategorie-Objekt
                    * @returns {HTMLElement} Erstelltes DOM-Element
                    */
                    function createCategoryItem(category) {
                        const item = document.createElement('div');
                        item.className = 'search-option px-3 py-2 d-flex align-items-center';
                        item.style.cssText = 'cursor: pointer; border-bottom: 1px solid #f8f9fa;';
                        item.setAttribute('data-type', 'category');
                        item.setAttribute('data-category-id', category.id);
                        item.setAttribute('data-category-slug', category.slug);

                        item.innerHTML = `
                        <i class="ti ti-folder me-2"></i>
                        <span>${category.name}</span>
                        `;

                        // Click-Handler für Kategorie-Navigation
                        item.addEventListener('click', function () {
                            window.location.href = `/category/${category.slug}`;
                        });

                        // Hover-Effekt
                        item.addEventListener('mouseenter', function () {
                            this.style.backgroundColor = '#f8f9fa';
                        });
                        item.addEventListener('mouseleave', function () {
                            this.style.backgroundColor = '';
                        });

                        return item;
                    }

                    /**
                    * "Suchen in Kategorie" Item erstellen
                    * @param {Object} option - Suchoption-Objekt
                    * @param {string} query - Suchanfrage
                    * @returns {HTMLElement} Erstelltes DOM-Element
                    */
                    function createSearchInCategoryItem(option, query) {
                        const item = document.createElement('div');
                        item.className = 'search-option px-3 py-2 d-flex align-items-center';
                        item.style.cssText = 'cursor: pointer; border-bottom: 1px solid #f8f9fa;';
                        item.setAttribute('data-type', 'search-in-category');
                        item.setAttribute('data-category-id', option.id);

                        item.innerHTML = `
                        <i class="ti ti-tag me-2"></i>
                        <span><strong>${query}</strong> in ${option.name}</span>
                        `;

                        // Click-Handler für Kategorie-spezifische Suche
                        item.addEventListener('click', function () {
                            const form = document.querySelector('form');
                            const searchQueryInput = document.querySelector('#searchQuery');

                            // Suchanfrage setzen
                            searchQueryInput.value = query;

                            // Verstecktes Input für Kategorie-Filter hinzufügen
                            let categoryInput = document.querySelector('input[name="category"]');
                            if (!categoryInput) {
                                categoryInput = document.createElement('input');
                                categoryInput.type = 'hidden';
                                categoryInput.name = 'category';
                                form.appendChild(categoryInput);
                            }
                            categoryInput.value = option.id;

                            // Formular absenden
                            form.submit();
                        });

                        // Hover-Effekt
                        item.addEventListener('mouseenter', function () {
                            this.style.backgroundColor = '#f8f9fa';
                        });
                        item.addEventListener('mouseleave', function () {
                            this.style.backgroundColor = '';
                        });

                        return item;
                    }

                    // "Suchen in allen Kategorien" Click-Handler
                    const searchInAllOption = document.querySelector('[data-type="search-all"]');
                    if (searchInAllOption) {
                        searchInAllOption.addEventListener('click', function () {
                            const form = document.querySelector('form');
                            // Formular mit aktueller Suchanfrage absenden
                            form.submit();
                        });

                        searchInAllOption.addEventListener('mouseenter', function () {
                            this.style.backgroundColor = '#f8f9fa';
                        });
                        searchInAllOption.addEventListener('mouseleave', function () {
                            this.style.backgroundColor = '';
                        });
                    }

                    /**
                    * Suchvorschläge ausblenden
                    */
                    function hideSuggestions() {
                        suggestionsDropdown.style.display = 'none';
                    }
                }

                // INITIALISIERUNG
                // ===============

                // Alle Komponenten beim DOM-Load initialisieren
                document.addEventListener('DOMContentLoaded', function () {
                    initializeCountryDropdown();
                    initializeSearchSuggestions();
                    initializeLocationSuggestions();

                    // Clear-Button für Suchfeld behandeln
                    const searchClearBtn = document.querySelector('.clear-btn[data-target="searchQuery"]');
                    if (searchClearBtn) {
                        searchClearBtn.addEventListener('click', function () {
                            const suggestionsDropdown = document.querySelector('#searchSuggestions');
                            if (suggestionsDropdown) {
                                suggestionsDropdown.style.display = 'none';
                            }
                        });
                    }

                    // Clear-Button für Location-Feld behandeln
                    const locationClearBtn = document.querySelector('.clear-btn[data-target="location"]');
                    if (locationClearBtn) {
                        locationClearBtn.addEventListener('click', function () {
                            const locationSuggestionsDropdown = document.querySelector('#locationSuggestions');
                            if (locationSuggestionsDropdown) {
                                locationSuggestionsDropdown.style.display = 'none';
                            }
                        });
                    }

                    console.log('Country dropdown, search suggestions and location suggestions initialized');
                });

                // Re-Initialisierung bei Livewire-Load
                document.addEventListener('livewire:load', function () {
                    initializeCountryDropdown();
                    initializeSearchSuggestions();
                    initializeLocationSuggestions();
                });
            </script>

    </form>
<?php /**PATH /Users/g470/Sites/platform_architecture_big/development/admin-laravel/resources/views/livewire/search-form.blade.php ENDPATH**/ ?>