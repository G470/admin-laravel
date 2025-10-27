<?php

namespace App\Livewire\Vendor;

use App\Models\Country;
use App\Models\Location;
use App\Models\LocationContactDetail;
use App\Models\VendorContactDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ContactDetails extends Component
{
    // Default contact details properties
    public $defaultContactDetails = [];
    public $isEditingDefault = false;

    // Location-specific properties
    public $showLocationModal = false;
    public $editingLocationId = null;
    public $currentLocation = null;
    public $locationContactDetails = [];
    public $useCustomContactDetails = false;

    // Contact form fields
    public $contactFields = [
        'company_name' => '',
        'salutation' => '',
        'first_name' => '',
        'last_name' => '',
        'street' => '',
        'house_number' => '',
        'postal_code' => '',
        'city' => '',
        'country_id' => null,
        'phone' => '',
        'mobile' => '',
        'whatsapp' => '',
        'website' => '',
    ];

    // Visibility toggles
    public $visibilityToggles = [
        'show_company_name' => true,
        'show_salutation' => true,
        'show_first_name' => true,
        'show_last_name' => true,
        'show_street' => true,
        'show_house_number' => true,
        'show_postal_code' => true,
        'show_city' => true,
        'show_country' => true,
        'show_phone' => true,
        'show_mobile' => true,
        'show_whatsapp' => true,
        'show_website' => true,
    ];

    protected $rules = [
        'contactFields.company_name' => 'nullable|string|max:255',
        'contactFields.salutation' => 'nullable|in:Herr,Frau,Divers',
        'contactFields.first_name' => 'nullable|string|max:255',
        'contactFields.last_name' => 'nullable|string|max:255',
        'contactFields.street' => 'nullable|string|max:255',
        'contactFields.house_number' => 'nullable|string|max:20',
        'contactFields.postal_code' => 'nullable|string|max:20',
        'contactFields.city' => 'nullable|string|max:255',
        'contactFields.country_id' => 'nullable|exists:countries,id',
        'contactFields.phone' => 'nullable|string|max:50',
        'contactFields.mobile' => 'nullable|string|max:50',
        'contactFields.whatsapp' => 'nullable|string|max:50',
        'contactFields.website' => 'nullable|url|max:255',
    ];

    protected $messages = [
        'contactFields.salutation.in' => 'Bitte wählen Sie eine gültige Anrede.',
        'contactFields.country_id.exists' => 'Bitte wählen Sie ein gültiges Land.',
        'contactFields.website.url' => 'Bitte geben Sie eine gültige Website-URL ein.',
    ];

    public function mount()
    {
        $this->loadDefaultContactDetails();
        
        // Debug: Check if default contact details are loaded
        if (app()->environment('local')) {
            logger('ContactDetails mounted', [
                'defaultContactDetails' => $this->defaultContactDetails,
                'contactFields' => $this->contactFields,
                'user_id' => Auth::id()
            ]);
        }
    }

    /**
     * Load default contact details
     */
    private function loadDefaultContactDetails()
    {
        $user = Auth::user();
        $defaultDetails = $user->defaultContactDetails;

        if ($defaultDetails) {
            $this->defaultContactDetails = $defaultDetails->toArray();
        } else {
            $this->defaultContactDetails = [];
        }
    }

    /**
     * Toggle default contact details edit mode
     */
    public function toggleDefaultEdit()
    {
        $this->isEditingDefault = !$this->isEditingDefault;

        if ($this->isEditingDefault) {
            $this->loadContactFieldsFromDefault();
        } else {
            $this->resetContactFields();
        }

        $this->resetValidation();
    }

    /**
     * Load contact fields from default details
     */
    private function loadContactFieldsFromDefault()
    {
        if (!empty($this->defaultContactDetails)) {
            foreach ($this->contactFields as $field => $value) {
                $this->contactFields[$field] = $this->defaultContactDetails[$field] ?? '';
            }

            foreach ($this->visibilityToggles as $toggle => $value) {
                $this->visibilityToggles[$toggle] = $this->defaultContactDetails[$toggle] ?? true;
            }
        } else {
            // If no default details exist, ensure fields are empty
            $this->resetContactFields();
        }
    }
    /**
     * Edit location contact details
     */
    public function editLocation($locationId)
    {
        // Validate the location ID
        if (!$locationId || !is_numeric($locationId)) {
            session()->flash('error', 'Ungültige Standort-ID.');
            return;
        }

        // Load location and verify it exists
        $location = Location::find($locationId);
        if (!$location) {
            session()->flash('error', 'Standort nicht gefunden.');
            return;
        }

        // Verify user owns this location
        if ($location->vendor_id !== Auth::id()) {
            session()->flash('error', 'Zugriff auf diesen Standort nicht berechtigt.');
            return;
        }

        // Reset the form fields
        $this->resetContactFields();

        // Load the location contact details
        $this->loadLocationContactDetails($locationId);

        // Set the editing location ID and open the modal
        $this->editingLocationId = $locationId;
        $this->showLocationModal = true;
        $this->currentLocation = $location;
    }
    /**
     * Clean ENUM fields by converting empty strings to null
     */
    private function cleanEnumFields($data)
    {
        // List of ENUM fields that need empty string -> null conversion
        $enumFields = ['salutation'];

        foreach ($enumFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        return $data;
    }

    /**
     * Save default contact details
     */
    public function saveDefaultContactDetails()
    {
        $this->validate();

        $user = Auth::user();
        $data = array_merge($this->contactFields, $this->visibilityToggles);

        // Clean ENUM fields
        $data = $this->cleanEnumFields($data);

        // Add vendor_id to the data
        $data['vendor_id'] = $user->id;

        VendorContactDetail::updateOrCreate(
            ['vendor_id' => $user->id],
            $data
        );

        // Reload the contact details to show updated data
        $this->loadDefaultContactDetails();
        $this->isEditingDefault = false;
        $this->resetContactFields();

        session()->flash('message', 'Standard-Kontaktdaten erfolgreich gespeichert.');

        // Emit refresh event to update the display
        $this->dispatch('contactDetailsUpdated');
    }

    /**
     * Load locations with contact status
     */
    private function loadLocationsWithContactStatus()
    {
        return Auth::user()->locations_with_contact_status;
    }

    /**
     * Toggle location modal
     */
    public function openLocationModal($locationId)
    {
        $this->editingLocationId = $locationId;
        $this->currentLocation = collect($this->loadLocationsWithContactStatus())
            ->firstWhere('id', $locationId);

        if ($this->currentLocation) {
            $this->loadLocationContactDetails($locationId);
            $this->showLocationModal = true;
        }
    }

    /**
     * Handle useCustomContactDetails toggle
     */
    public function updatedUseCustomContactDetails($value)
    {
        // Debug logging
        if (app()->environment('local')) {
            logger('Checkbox toggled', [
                'value' => $value,
                'editingLocationId' => $this->editingLocationId,
                'before_contactFields' => $this->contactFields
            ]);
        }

        if (!$value) {
            // If switching to standard contact details, clear the form
            $this->resetContactFields();
        } else {
            // If switching to custom contact details, load existing data if available
            if ($this->editingLocationId) {
                $this->loadExistingContactData($this->editingLocationId);
            }
        }

        // Debug logging after change
        if (app()->environment('local')) {
            logger('After checkbox change', [
                'useCustomContactDetails' => $this->useCustomContactDetails,
                'contactFields' => $this->contactFields
            ]);
        }
    }

    /**
     * Load existing contact data without changing the toggle state
     */
    private function loadExistingContactData($locationId)
    {
        $locationContactDetails = LocationContactDetail::where('location_id', $locationId)->first();

        if ($locationContactDetails && $locationContactDetails->use_custom_contact_details) {
            // Load the saved custom contact data
            $this->contactFields = [
                'company_name' => $locationContactDetails->company_name ?? '',
                'salutation' => $locationContactDetails->salutation ?? '',
                'first_name' => $locationContactDetails->first_name ?? '',
                'last_name' => $locationContactDetails->last_name ?? '',
                'street' => $locationContactDetails->street ?? '',
                'house_number' => $locationContactDetails->house_number ?? '',
                'postal_code' => $locationContactDetails->postal_code ?? '',
                'city' => $locationContactDetails->city ?? '',
                'country_id' => $locationContactDetails->country_id,
                'phone' => $locationContactDetails->phone ?? '',
                'mobile' => $locationContactDetails->mobile ?? '',
                'whatsapp' => $locationContactDetails->whatsapp ?? '',
                'website' => $locationContactDetails->website ?? '',
            ];

            $this->visibilityToggles = [
                'show_company_name' => $locationContactDetails->show_company_name ?? true,
                'show_salutation' => $locationContactDetails->show_salutation ?? true,
                'show_first_name' => $locationContactDetails->show_first_name ?? true,
                'show_last_name' => $locationContactDetails->show_last_name ?? true,
                'show_street' => $locationContactDetails->show_street ?? true,
                'show_house_number' => $locationContactDetails->show_house_number ?? true,
                'show_postal_code' => $locationContactDetails->show_postal_code ?? true,
                'show_city' => $locationContactDetails->show_city ?? true,
                'show_country' => $locationContactDetails->show_country ?? true,
                'show_phone' => $locationContactDetails->show_phone ?? true,
                'show_mobile' => $locationContactDetails->show_mobile ?? true,
                'show_whatsapp' => $locationContactDetails->show_whatsapp ?? true,
                'show_website' => $locationContactDetails->show_website ?? true,
            ];
        } else {
            // No existing custom data, start with empty fields
            $this->resetContactFields();
        }
    }

    /**
     * Load location contact details (for initial modal load)
     */
    private function loadLocationContactDetails($locationId)
    {
        $locationContactDetails = LocationContactDetail::where('location_id', $locationId)->first();

        if ($locationContactDetails) {
            // Set the toggle state based on saved preference
            $this->useCustomContactDetails = $locationContactDetails->use_custom_contact_details;

            if ($this->useCustomContactDetails) {
                // Load saved custom contact data
                $this->contactFields = [
                    'company_name' => $locationContactDetails->company_name ?? '',
                    'salutation' => $locationContactDetails->salutation ?? '',
                    'first_name' => $locationContactDetails->first_name ?? '',
                    'last_name' => $locationContactDetails->last_name ?? '',
                    'street' => $locationContactDetails->street ?? '',
                    'house_number' => $locationContactDetails->house_number ?? '',
                    'postal_code' => $locationContactDetails->postal_code ?? '',
                    'city' => $locationContactDetails->city ?? '',
                    'country_id' => $locationContactDetails->country_id,
                    'phone' => $locationContactDetails->phone ?? '',
                    'mobile' => $locationContactDetails->mobile ?? '',
                    'whatsapp' => $locationContactDetails->whatsapp ?? '',
                    'website' => $locationContactDetails->website ?? '',
                ];

                $this->visibilityToggles = [
                    'show_company_name' => $locationContactDetails->show_company_name ?? true,
                    'show_salutation' => $locationContactDetails->show_salutation ?? true,
                    'show_first_name' => $locationContactDetails->show_first_name ?? true,
                    'show_last_name' => $locationContactDetails->show_last_name ?? true,
                    'show_street' => $locationContactDetails->show_street ?? true,
                    'show_house_number' => $locationContactDetails->show_house_number ?? true,
                    'show_postal_code' => $locationContactDetails->show_postal_code ?? true,
                    'show_city' => $locationContactDetails->show_city ?? true,
                    'show_country' => $locationContactDetails->show_country ?? true,
                    'show_phone' => $locationContactDetails->show_phone ?? true,
                    'show_mobile' => $locationContactDetails->show_mobile ?? true,
                    'show_whatsapp' => $locationContactDetails->show_whatsapp ?? true,
                    'show_website' => $locationContactDetails->show_website ?? true,
                ];
            } else {
                // Reset fields if not using custom contact details
                $this->resetContactFields();
            }
        } else {
            // No saved preferences, default to standard contact details
            $this->useCustomContactDetails = false;
            $this->resetContactFields();
        }
    }

    /**
     * Save location contact details
     */
    public function saveLocationContactDetails()
    {
        $this->validate();

        $data = array_merge(
            $this->contactFields,
            $this->visibilityToggles,
            [
                'use_custom_contact_details' => $this->useCustomContactDetails,
                'location_id' => $this->editingLocationId
            ]
        );

        // Clean ENUM fields
        $data = $this->cleanEnumFields($data);

        LocationContactDetail::updateOrCreate(
            ['location_id' => $this->editingLocationId],
            $data
        );

        $this->closeLocationModal();
        session()->flash('message', 'Standort-Kontaktdaten erfolgreich gespeichert.');
    }

    /**
     * Reset location to default contact details
     */
    public function resetLocationToDefault($locationId)
    {
        $location = Location::where('id', $locationId)
            ->where('vendor_id', Auth::id())
            ->first();

        if (!$location) {
            session()->flash('error', 'Standort nicht gefunden oder keine Berechtigung.');
            return;
        }

        LocationContactDetail::updateOrCreate(
            ['location_id' => $location->id],
            ['use_custom_contact_details' => false]
        );

        session()->flash('message', 'Standort auf Standard-Kontaktdaten zurückgesetzt.');
    }

    /**
     * Close location modal and reset form
     */
    public function closeLocationModal()
    {
        $this->showLocationModal = false;
        $this->editingLocationId = null;
        $this->currentLocation = null;
        $this->useCustomContactDetails = false;
        $this->resetContactFields();
        $this->resetValidation();
    }

    /**
     * Reset contact form fields
     */
    private function resetContactFields()
    {
        foreach ($this->contactFields as $field => $value) {
            $this->contactFields[$field] = '';
        }

        foreach ($this->visibilityToggles as $toggle => $value) {
            $this->visibilityToggles[$toggle] = true;
        }
    }

    /**
     * Render the component
     */
    public function render()
    {
        $user = Auth::user();
        $locations = $user->locations_with_contact_status;
        
        
        $countries = Country::orderBy('name')->get();
        if ($this->editingLocationId) {
            $currentLocation = Location::find($this->editingLocationId);
            if ($currentLocation) {
                $this->currentLocation = $currentLocation;
                $this->loadLocationContactDetails($this->editingLocationId);
            }
        }
        return view('livewire.vendor.contact-details', compact('locations', 'countries'));
    }
}