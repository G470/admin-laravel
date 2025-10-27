/**
 * Main - Front Pages
 */
'use strict';

window.isRtl = false;
window.isDarkStyle = document.documentElement.getAttribute('data-style') === 'dark';
window.templateName = window.templateName || 'inlando';





  // second alert to test if the file is loaded
  // Make sure Helpers is loaded
  if (typeof window.Helpers === 'undefined') {
    console.error('Helpers is not defined! Make sure helpers.js is loaded before front-main.js');
    throw new Error('Helpers is not defined! Make sure helpers.js is loaded before front-main.js');
  }

  const menu = document.getElementById('navbarSupportedContent'),
    nav = document.querySelector('.layout-navbar'),
    navItemLink = document.querySelectorAll('.navbar-nav .nav-link');

  // Initialised custom options if checked
  setTimeout(function () {
    if (window.Helpers && window.Helpers.initCustomOptionCheck) {
      window.Helpers.initCustomOptionCheck();
    }
  }, 1000);

  if (typeof Waves !== 'undefined') {
    Waves.init();
    Waves.attach(".btn[class*='btn-']:not([class*='btn-outline-']):not([class*='btn-label-'])", ['waves-light']);
    Waves.attach("[class*='btn-outline-']");
    Waves.attach("[class*='btn-label-']");
    Waves.attach('.pagination .page-item .page-link');
  }

  // Init BS Tooltip
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Accordion active class
  const accordionActiveFunction = function (e) {
    if (e.type == 'show.bs.collapse' || e.type == 'show.bs.collapse') {
      e.target.closest('.accordion-item').classList.add('active');
    } else {
      e.target.closest('.accordion-item').classList.remove('active');
    }
  };

  const accordionTriggerList = [].slice.call(document.querySelectorAll('.accordion'));
  const accordionList = accordionTriggerList.map(function (accordionTriggerEl) {
    accordionTriggerEl.addEventListener('show.bs.collapse', accordionActiveFunction);
    accordionTriggerEl.addEventListener('hide.bs.collapse', accordionActiveFunction);
  });

  // If layout is RTL add .dropdown-menu-end class to .dropdown-menu
  if (isRtl && window.Helpers) {
    window.Helpers._addClass('dropdown-menu-end', document.querySelectorAll('#layout-navbar .dropdown-menu'));
  }

  // Navbar
  window.addEventListener('scroll', e => {
    if (window.scrollY > 10) {
      nav.classList.add('navbar-active');
    } else {
      nav.classList.remove('navbar-active');
    }
  });
  window.addEventListener('load', e => {
    if (window.scrollY > 10) {
      nav.classList.add('navbar-active');
    } else {
      nav.classList.remove('navbar-active');
    }
  });

  // Function to close the mobile menu
  function closeMenu() {
    menu.classList.remove('show');
  }

  document.addEventListener('click', function (event) {
    // Check if the clicked element is inside mobile menu
    if (!menu.contains(event.target)) {
      closeMenu();
    }
  });
  navItemLink.forEach(link => {
    link.addEventListener('click', event => {
      if (!link.classList.contains('dropdown-toggle')) {
        closeMenu();
      } else {
        event.preventDefault();
      }
    });
  });

  // If layout is RTL add .dropdown-menu-end class to .dropdown-menu
  if (isRtl && window.Helpers) {
    window.Helpers._addClass('dropdown-menu-end', document.querySelectorAll('.dropdown-menu'));
  }

  // Mega dropdown
  const megaDropdown = document.querySelectorAll('.nav-link.mega-dropdown');
  if (megaDropdown) {
    megaDropdown.forEach(e => {
      new MegaDropdown(e);
    });
  }

  //Style Switcher (Light/Dark/System Mode)
  let styleSwitcher = document.querySelector('.dropdown-style-switcher');
  const activeStyle = document.documentElement.getAttribute('data-style');

  // Get style from local storage or use 'system' as default
  let storedStyle =
    localStorage.getItem('templateCustomizer-' + templateName + '--Style') || //if no template style then use Customizer style
    (window.templateCustomizer?.settings?.defaultStyle ?? 'light'); //!if there is no Customizer then use default style as light

  // Set style on click of style switcher item if template customizer is enabled
  if (window.templateCustomizer && styleSwitcher) {
    let styleSwitcherItems = [].slice.call(styleSwitcher.children[1].querySelectorAll('.dropdown-item'));
    styleSwitcherItems.forEach(function (item) {
      item.classList.remove('active');
      item.addEventListener('click', function () {
        let currentStyle = this.getAttribute('data-theme');
        if (currentStyle === 'light') {
          window.templateCustomizer.setStyle('light');
        } else if (currentStyle === 'dark') {
          window.templateCustomizer.setStyle('dark');
        } else {
          window.templateCustomizer.setStyle('system');
        }
      });
      setTimeout(() => {
        if (item.getAttribute('data-theme') === activeStyle) {
          // Add 'active' class to the item if it matches the activeStyle
          item.classList.add('active');
        }
      }, 1000);
    });

    // Update style switcher icon based on the stored style

    const styleSwitcherIcon = styleSwitcher.querySelector('i');

    if (storedStyle === 'light') {
      styleSwitcherIcon.classList.add('ti-sun');
      new bootstrap.Tooltip(styleSwitcherIcon, {
        title: 'Light Mode',
        fallbackPlacements: ['bottom']
      });
    } else if (storedStyle === 'dark') {
      styleSwitcherIcon.classList.add('ti-moon-stars');
      new bootstrap.Tooltip(styleSwitcherIcon, {
        title: 'Dark Mode',
        fallbackPlacements: ['bottom']
      });
    } else {
      styleSwitcherIcon.classList.add('ti-device-desktop-analytics');
      new bootstrap.Tooltip(styleSwitcherIcon, {
        title: 'System Mode',
        fallbackPlacements: ['bottom']
      });
    }
  }

  // Run switchImage function based on the stored style
  switchImage(storedStyle);

  // Update light/dark image based on current style
  function switchImage(style) {
    if (style === 'system') {
      if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
        style = 'dark';
      } else {
        style = 'light';
      }
    }
    const switchImagesList = [].slice.call(document.querySelectorAll('[data-app-' + style + '-img]'));
    switchImagesList.map(function (imageEl) {
      const setImage = imageEl.getAttribute('data-app-' + style + '-img');
      imageEl.src = assetsPath + 'img/' + setImage; // Using window.assetsPath to get the exact relative path
    });
  }


// script for livewire search form 
  document.addEventListener('livewire:load', function () {
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
      searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const query = this.querySelector('input[name="query"]').value;
        if (query) {
          window.location.href = '/search?query=' + encodeURIComponent(query);
        }
      });
    }
  });

// Clear button functionality for search form
document.addEventListener('DOMContentLoaded', function () {
    console.log('DOMContentLoaded: Initializing clear buttons from front-main.js');
    
    // Clear button functionality
    function toggleClearButton(input) {
        console.log('toggleClearButton called for:', input.id, 'value:', input.value);
        const clearBtn = input.parentNode.querySelector('.clear-btn');
        if (clearBtn) {
            if (input.value.trim() !== '') {
                console.log('Showing clear button for:', input.id);
                clearBtn.classList.remove('d-none');
            } else {
                console.log('Hiding clear button for:', input.id);
                clearBtn.classList.add('d-none');
            }
        }
    }

    // Initialize clear buttons on page load
    const inputs = document.querySelectorAll('#searchQuery, #location, #dateRange');
    console.log('Found inputs:', inputs.length);
    
    inputs.forEach(function(input) {
        console.log('Setting up clear button for:', input.id);
        toggleClearButton(input);
        
        // Add input event listeners
        input.addEventListener('input', function() {
            console.log('Input event for:', this.id, 'value:', this.value);
            toggleClearButton(this);
        });
        
        input.addEventListener('keyup', function() {
            toggleClearButton(this);
        });
        
        input.addEventListener('paste', function() {
            setTimeout(() => toggleClearButton(this), 10);
        });
    });

    // Handle clear button clicks
    const clearButtons = document.querySelectorAll('.clear-btn');
    console.log('Found clear buttons:', clearButtons.length);
    
    clearButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-target');
            const target = document.getElementById(targetId);
            console.log('Clear button clicked for:', targetId);
            
            if (target) {
                if (targetId === 'dateRange') {
                    // Clear date range and hidden fields
                    target.value = '';
                    const dateFrom = document.getElementById('dateFrom');
                    const dateTo = document.getElementById('dateTo');
                    if (dateFrom) dateFrom.value = '';
                    if (dateTo) dateTo.value = '';
                } else {
                    // Clear regular input
                    target.value = '';
                }
                
                toggleClearButton(target);
                target.focus();
            }
        });
    });
});

  // now we init daterangepicker for #dateRange in livewire search form
document.addEventListener('DOMContentLoaded', function () {
    console.log('Initializing daterangepicker...');
    
    // Check if moment is available
    if (typeof moment === 'undefined') {
        console.error('moment is not available! Cannot initialize daterangepicker.');
        return;
    }
    
    const dateRangeInput = document.querySelector('#dateRange');
    
    if (dateRangeInput) {
        console.log('DateRange input found, setting up daterangepicker');
        
        try {
            $(dateRangeInput).daterangepicker({
                autoUpdateInput: true,
                locale: {
                    cancelLabel: 'Löschen',
                    applyLabel: 'Übernehmen',
                    fromLabel: 'Von',
                    toLabel: 'Bis',
                    customRangeLabel: 'Benutzerdefiniert',
                    weekLabel: 'W',
                    daysOfWeek: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
                    monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni',
                        'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
                    firstDay: 1,
                    format: 'DD.MM.YYYY'
                },
                startDate: moment(),
                endDate: moment().add(7, 'days'),
                minDate: moment(),
                opens: 'center',
                drops: 'down',
                alwaysShowCalendars: true,
                showDropdowns: true,
                ranges: {
                    'Heute': [moment(), moment()],
                    'Morgen': [moment().add(1, 'days'), moment().add(1, 'days')],
                    'Nächste 7 Tage': [moment(), moment().add(6, 'days')],
                    'Nächste 30 Tage': [moment(), moment().add(29, 'days')],
                    'Dieses Wochenende': [moment().day(6), moment().day(7)]
                }
            });

            $(dateRangeInput).on('apply.daterangepicker', function (ev, picker) {
                console.log('Daterangepicker apply event');
                $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
                $('#dateFrom').val(picker.startDate.format('YYYY-MM-DD'));
                $('#dateTo').val(picker.endDate.format('YYYY-MM-DD'));
                // Trigger clear button update
                const event = new Event('input');
                this.dispatchEvent(event);
            });

            $(dateRangeInput).on('cancel.daterangepicker', function (ev, picker) {
                console.log('Daterangepicker cancel event');
                $(this).val('');
                $('#dateFrom').val('');
                $('#dateTo').val('');
                // Trigger clear button update
                const event = new Event('input');
                this.dispatchEvent(event);
            });
            
            console.log('Daterangepicker initialized successfully');
        } catch (error) {
            console.error('Error initializing daterangepicker:', error);
        }
    } else {
        console.log('DateRange input not found');
    }
});

// Country dropdown functionality
document.addEventListener('DOMContentLoaded', function () {
    const countryOptions = document.querySelectorAll('.country-option');
    const selectedFlag = document.getElementById('selectedFlag');
    const selectedCountry = document.getElementById('selectedCountry');
    const countryCodeInput = document.getElementById('selectedCountryCode');
    
    if (countryOptions.length && selectedFlag && selectedCountry) {
        countryOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                
                const countryCode = this.getAttribute('data-country');
                const countryName = this.getAttribute('data-name');
                const flagSrc = this.querySelector('img').src;
                
                // Update the dropdown button
                selectedFlag.src = flagSrc;
                selectedFlag.alt = countryName;
                selectedCountry.textContent = countryName;
                
                // Update the hidden input field
                if (countryCodeInput) {
                    countryCodeInput.value = countryCode;
                }
                
                // Clear location autocomplete when country changes
                const locationInput = document.getElementById('location');
                if (locationInput && window.locationAutocomplete) {
                    window.locationAutocomplete.clearSuggestions();
                }
                
                // Here you could add logic to update the search form or trigger a country change event
                console.log('Country changed to:', countryName, 'Code:', countryCode);
                
                // Optionally trigger a custom event for other components to listen to
                const countryChangeEvent = new CustomEvent('countryChanged', {
                    detail: { country: countryCode, name: countryName }
                });
                document.dispatchEvent(countryChangeEvent);
            });
        });
    }
});

// Location autocomplete functionality
document.addEventListener('DOMContentLoaded', function () {
    const locationInput = document.getElementById('location');
    const suggestionsList = document.getElementById('suggestions');
    let debounceTimer;

    if(locationInput && suggestionsList) {
        locationInput.addEventListener('input', function() {
            const query = this.value.trim();

            // Clear previous timer
            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }

            if (query.length < 2) {
                suggestionsList.innerHTML = '';
                suggestionsList.style.display = 'none';
                return;
            }

            // Debounce the API call
            debounceTimer = setTimeout(async () => {
                try {
                    // Get selected country
                    const countryCode = document.getElementById('selectedCountryCode').value || 'de';
                    const response = await fetch(`/api/locations?query=${encodeURIComponent(query)}&country=${countryCode}`);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const data = await response.json();

                    // Clear previous suggestions
                    suggestionsList.innerHTML = '';

                    if (data.length === 0) {
                        suggestionsList.style.display = 'none';
                        return;
                    }

                    data.forEach(location => {
                        const option = document.createElement('div');
                        option.className = 'suggestion-item';
                        option.style.cssText = `
                            padding: 12px 15px;
                            cursor: pointer;
                            border-bottom: 1px solid #f0f0f0;
                            display: flex;
                            align-items: center;
                            transition: background-color 0.2s;
                        `;
                        
                        option.innerHTML = `
                            <i class="ti ti-map-pin" style="margin-right: 10px; color: #666; font-size: 16px;"></i>
                            <div>
                                <div style="font-weight: 500;">${location.city}</div>
                                <small style="color: #666;">${location.postcode}, ${location.country}</small>
                            </div>
                        `;
                        
                        option.dataset.value = location.display;
                        option.dataset.city = location.city;
                        option.dataset.postcode = location.postcode;
                        
                        option.addEventListener('click', function() {
                            locationInput.value = this.dataset.value;
                            suggestionsList.innerHTML = '';
                            suggestionsList.style.display = 'none';
                            
                            // Trigger input event to update clear button
                            const event = new Event('input');
                            locationInput.dispatchEvent(event);
                        });
                        
                        option.addEventListener('mouseenter', function() {
                            this.style.backgroundColor = '#f5f5f5';
                        });
                        
                        option.addEventListener('mouseleave', function() {
                            this.style.backgroundColor = '';
                        });
                        
                        suggestionsList.appendChild(option);
                    });
                    
                    suggestionsList.style.display = 'block';
                } catch (error) {
                    console.error('Error fetching location suggestions:', error);
                    suggestionsList.innerHTML = '';
                    suggestionsList.style.display = 'none';
                }
            }, 300);
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(event) {
            if (!locationInput.contains(event.target) && !suggestionsList.contains(event.target)) {
                suggestionsList.style.display = 'none';
            }
        });
        
        // Handle keyboard navigation
        locationInput.addEventListener('keydown', function(e) {
            const items = suggestionsList.querySelectorAll('.suggestion-item');
            let selectedIndex = Array.from(items).findIndex(item => item.style.backgroundColor === 'rgb(245, 245, 245)');
            
            switch(e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    if (selectedIndex < items.length - 1) {
                        if (selectedIndex >= 0) items[selectedIndex].style.backgroundColor = '';
                        selectedIndex++;
                        items[selectedIndex].style.backgroundColor = '#f5f5f5';
                    }
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    if (selectedIndex > 0) {
                        items[selectedIndex].style.backgroundColor = '';
                        selectedIndex--;
                        items[selectedIndex].style.backgroundColor = '#f5f5f5';
                    }
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (selectedIndex >= 0 && items[selectedIndex]) {
                        items[selectedIndex].click();
                    }
                    break;
                case 'Escape':
                    suggestionsList.style.display = 'none';
                    break;
            }
        });
        
        // Store reference for external access
        window.locationAutocomplete = {
            clearSuggestions: function() {
                if (suggestionsList) {
                    suggestionsList.innerHTML = '';
                    suggestionsList.style.display = 'none';
                }
            }
        };
    } else {
        // Create empty object if elements don't exist
        window.locationAutocomplete = {
            clearSuggestions: function() {
                console.log('Location autocomplete not available - elements missing');
            }
        };
    }

    // Favorite button functionality
    document.addEventListener('click', function(e) {
        const favoriteBtn = e.target.closest('.favorite-btn');
        if (favoriteBtn) {
            e.preventDefault();
            const rentalId = favoriteBtn.dataset.rentalId;
            let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
            
            if (favorites.includes(rentalId)) {
                // Remove from favorites
                favorites = favorites.filter(id => id !== rentalId);
                favoriteBtn.classList.remove('active', 'btn-danger');
                favoriteBtn.classList.add('btn-outline-danger');
            } else {
                // Add to favorites
                favorites.push(rentalId);
                favoriteBtn.classList.add('active', 'btn-danger');
                favoriteBtn.classList.remove('btn-outline-danger');
            }
            
            localStorage.setItem('favorites', JSON.stringify(favorites));
            updateFavoriteButtons();
        }
    });

    function updateFavoriteButtons() {
        const favorites = JSON.parse(localStorage.getItem('favorites')) || [];
        document.querySelectorAll('.favorite-btn').forEach(btn => {
            const rentalId = btn.dataset.rentalId;
            if (favorites.includes(rentalId)) {
                btn.classList.add('active', 'btn-danger');
                btn.classList.remove('btn-outline-danger');
            } else {
                btn.classList.remove('active', 'btn-danger');
                btn.classList.add('btn-outline-danger');
            }
        });
    }

    updateFavoriteButtons();

});

// Category Autocomplete for Search Query Input
document.addEventListener('DOMContentLoaded', function () {
    const searchQueryInput = document.getElementById('searchQuery');
    if (!searchQueryInput) return;
    
    let categorySuggestionsContainer;
    let categoryCurrentSuggestions = [];
    let categorySelectedIndex = -1;
    let categoryDebounceTimer;
    
    // Create suggestions container
    function createCategorySuggestionsContainer() {
        if (categorySuggestionsContainer) return;
        
        categorySuggestionsContainer = document.createElement('div');
        categorySuggestionsContainer.className = 'category-suggestions-container';
        categorySuggestionsContainer.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-height: 400px;
            overflow-y: auto;
            z-index: 1001;
            display: none;
        `;
        
        searchQueryInput.parentNode.appendChild(categorySuggestionsContainer);
        searchQueryInput.parentNode.style.position = 'relative';
    }
    
    createCategorySuggestionsContainer();
    
    // Fetch category suggestions from API
    async function fetchCategorySuggestions(query) {
        try {
            const response = await fetch(`/api/categories/suggestions?query=${encodeURIComponent(query)}&limit=8`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching category suggestions:', error);
            return { categories: [], searchOptions: [], query: query };
        }
    }
    
    // Display category suggestions
    function displayCategorySuggestions(data) {
        if (!categorySuggestionsContainer) return;
        
        const { categories, searchOptions, query } = data;
        categoryCurrentSuggestions = [...categories, ...searchOptions];
        categorySelectedIndex = -1;
        
        if (categoryCurrentSuggestions.length === 0) {
            categorySuggestionsContainer.style.display = 'none';
            return;
        }
        
        let html = '';
        
        // Add category matches section
        if (categories.length > 0) {
            html += '<div style="padding: 8px 15px; background: #f8f9fa; border-bottom: 1px solid #eee; font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase;">Kategorien</div>';
            
            categories.forEach((category, index) => {
                html += `
                    <div class="category-suggestion-item" data-index="${index}" style="
                        padding: 12px 15px;
                        cursor: pointer;
                        border-bottom: 1px solid #f0f0f0;
                        display: flex;
                        align-items: center;
                    ">
                        <i class="ti ti-folder" style="margin-right: 10px; color: #666; font-size: 16px;"></i>
                        <span style="font-weight: 500;">${category.name}</span>
                    </div>
                `;
            });
        }
        
        // Add separator and search options
        if (categories.length > 0 && searchOptions.length > 0) {
            html += '<div style="padding: 8px 15px; background: #f8f9fa; border-bottom: 1px solid #eee; font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase;">Suchen In Kategorien</div>';
        }
        
        // Add search in category options
        searchOptions.forEach((option, index) => {
            const actualIndex = categories.length + index;
            const isAllCategories = option.name === 'allen Kategorien';
            
            html += `
                <div class="category-suggestion-item" data-index="${actualIndex}" style="
                    padding: 12px 15px;
                    cursor: pointer;
                    border-bottom: 1px solid #f0f0f0;
                    display: flex;
                    align-items: center;
                ">
                    <i class="ti ${isAllCategories ? 'ti-search' : 'ti-tag'}" style="margin-right: 10px; color: #666; font-size: 16px;"></i>
                    <span>
                        <strong style="font-style: italic;">${query}</strong> in ${option.name}
                    </span>
                </div>
            `;
        });
        
        categorySuggestionsContainer.innerHTML = html;
        categorySuggestionsContainer.style.display = 'block';
        
        // Add event listeners
        categorySuggestionsContainer.querySelectorAll('.category-suggestion-item').forEach((item, index) => {
            item.addEventListener('click', () => selectCategorySuggestion(index));
            
            item.addEventListener('mouseenter', () => {
                clearCategorySelection();
                item.style.backgroundColor = '#f5f5f5';
                categorySelectedIndex = index;
            });
            
            item.addEventListener('mouseleave', () => {
                item.style.backgroundColor = '';
            });
        });
    }
    
    // Select a category suggestion
    function selectCategorySuggestion(index) {
        if (index >= 0 && index < categoryCurrentSuggestions.length) {
            const suggestion = categoryCurrentSuggestions[index];
            
            if (suggestion.type === 'category') {
                // Direct category selection - navigate to category page
                searchQueryInput.value = suggestion.name;
                hideCategorySuggestions();
                
                // Trigger search or navigate to category
                setTimeout(() => {
                    window.location.href = `/kategorien/${suggestion.slug}`;
                }, 100);
            } else {
                // Search in category option - perform search with category filter
                const searchForm = searchQueryInput.closest('form');
                if (searchForm) {
                    // Set the query value
                    searchQueryInput.value = suggestion.searchText.split(' in ')[0]; // Extract just the query part
                    
                    // Add category filter if not "all categories"
                    if (suggestion.id) {
                        // Add hidden input for category filter
                        let categoryInput = searchForm.querySelector('input[name="category"]');
                        if (!categoryInput) {
                            categoryInput = document.createElement('input');
                            categoryInput.type = 'hidden';
                            categoryInput.name = 'category';
                            searchForm.appendChild(categoryInput);
                        }
                        categoryInput.value = suggestion.id;
                    }
                    
                    hideCategorySuggestions();
                    
                    // Submit the form
                    setTimeout(() => {
                        searchForm.submit();
                    }, 100);
                }
            }
            
            // Trigger input event to update clear button
            const event = new Event('input');
            searchQueryInput.dispatchEvent(event);
        }
    }
    
    // Hide category suggestions
    function hideCategorySuggestions() {
        if (categorySuggestionsContainer) {
            categorySuggestionsContainer.style.display = 'none';
        }
        categorySelectedIndex = -1;
    }
    
    // Clear selection highlighting
    function clearCategorySelection() {
        if (categorySuggestionsContainer) {
            categorySuggestionsContainer.querySelectorAll('.category-suggestion-item').forEach(item => {
                item.style.backgroundColor = '';
            });
        }
    }
    
    // Navigate through suggestions with keyboard
    function navigateCategorySuggestions(direction) {
        if (categoryCurrentSuggestions.length === 0) return;
        
        clearCategorySelection();
        
        if (direction === 'down') {
            categorySelectedIndex = (categorySelectedIndex + 1) % categoryCurrentSuggestions.length;
        } else {
            categorySelectedIndex = categorySelectedIndex <= 0 ? categoryCurrentSuggestions.length - 1 : categorySelectedIndex - 1;
        }
        
        // Highlight selected item
        const items = categorySuggestionsContainer.querySelectorAll('.category-suggestion-item');
        if (items[categorySelectedIndex]) {
            items[categorySelectedIndex].style.backgroundColor = '#f5f5f5';
            items[categorySelectedIndex].scrollIntoView({ block: 'nearest' });
        }
    }
    
    // Input event listener with debounce
    searchQueryInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(categoryDebounceTimer);
        
        if (query.length < 2) {
            hideCategorySuggestions();
            return;
        }
        
        categoryDebounceTimer = setTimeout(async () => {
            const suggestions = await fetchCategorySuggestions(query);
            displayCategorySuggestions(suggestions);
        }, 300);
    });
    
    // Keyboard navigation
    searchQueryInput.addEventListener('keydown', function(e) {
        if (!categorySuggestionsContainer || categorySuggestionsContainer.style.display === 'none') return;
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                navigateCategorySuggestions('down');
                break;
            case 'ArrowUp':
                e.preventDefault();
                navigateCategorySuggestions('up');
                break;
            case 'Enter':
                e.preventDefault();
                if (categorySelectedIndex >= 0) {
                    selectCategorySuggestion(categorySelectedIndex);
                }
                break;
            case 'Escape':
                hideCategorySuggestions();
                break;
        }
    });
    
    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchQueryInput.contains(e.target) && !categorySuggestionsContainer.contains(e.target)) {
            hideCategorySuggestions();
        }
    });
    
    // Store reference for external access
    window.categoryAutocomplete = {
        clearSuggestions: hideCategorySuggestions
    };
});

// End of main - Front Pages
// This is a test alert from front-main.js!
