<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Location;
use App\Models\LocationContactDetail;
use App\Models\VendorContactDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactDetailsController extends Controller
{
    /**
     * Display contact details management page
     */
    public function index()
    {
        $user = Auth::user();
        $locations = $user->locations_with_contact_status;
        $countries = Country::orderBy('name')->get();
        $defaultContactDetails = $user->defaultContactDetails;
        if (!$defaultContactDetails) {
            // Create default contact details if not exists
            // This ensures that the vendor always has a default contact details entry
            // which can be used for new locations or as a fallback
            // This is a one-time operation, so we can safely create it here
            // if it doesn't exist
            // This will not affect existing locations that already have custom contact details
            // but will ensure that new locations can use the default contact details
            // and that the vendor has a fallback contact details entry
            // This is useful for vendors who might not have set up their contact details yet
            // or for new vendors who just registered
            $defaultContactDetails = new VendorContactDetail();
            $defaultContactDetails->vendor_id = $user->id;
            $defaultContactDetails->company_name = $user->company_name;
            $defaultContactDetails->salutation = $user->salutation;
            $defaultContactDetails->first_name = $user->first_name;
            $defaultContactDetails->last_name = $user->last_name;
            $defaultContactDetails->street = $user->street;
            $defaultContactDetails->house_number = $user->house_number;
            $defaultContactDetails->postal_code = $user->postal_code;
            $defaultContactDetails->city = $user->city;
            $defaultContactDetails->country_id = $user->country_id;
            $defaultContactDetails->phone = $user->phone;
            $defaultContactDetails->mobile = $user->mobile;
            $defaultContactDetails->whatsapp = $user->whatsapp;
            $defaultContactDetails->website = $user->website;
            //$defaultContactDetails->save();
        }

        return view('content.vendor.contact-details.index', compact(
            'user',
            'locations',
            'countries',
            'defaultContactDetails'
        ));
    }

    /**
     * Update default contact details
     */
    public function updateDefault(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'salutation' => 'nullable|in:Herr,Frau,Divers',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'house_number' => 'nullable|string|max:20',
            'postal_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',

            // Visibility toggles
            'show_company_name' => 'boolean',
            'show_salutation' => 'boolean',
            'show_first_name' => 'boolean',
            'show_last_name' => 'boolean',
            'show_street' => 'boolean',
            'show_house_number' => 'boolean',
            'show_postal_code' => 'boolean',
            'show_city' => 'boolean',
            'show_country' => 'boolean',
            'show_phone' => 'boolean',
            'show_mobile' => 'boolean',
            'show_whatsapp' => 'boolean',
            'show_website' => 'boolean',
        ]);

        $user = Auth::user();

        $contactDetails = VendorContactDetail::updateOrCreate(
            ['vendor_id' => $user->id],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Standard-Kontaktdaten erfolgreich aktualisiert.',
            'contact_details' => $contactDetails,
        ]);
    }

    /**
     * Get location contact details for edit modal
     */
    public function getLocationDetails($locationId)
    {
        $location = Location::where('id', $locationId)
            ->where('vendor_id', Auth::id())
            ->with(['contactDetails.country', 'country'])
            ->firstOrFail();

        $countries = Country::orderBy('name')->get();
        $defaultDetails = Auth::user()->defaultContactDetails;

        return response()->json([
            'location' => $location,
            'contact_details' => $location->contactDetails,
            'default_details' => $defaultDetails,
            'countries' => $countries,
        ]);
    }

    /**
     * Update location-specific contact details
     */
    public function updateLocation(Request $request, $locationId)
    {
        $validated = $request->validate([
            'use_custom_contact_details' => 'boolean',
            'company_name' => 'nullable|string|max:255',
            'salutation' => 'nullable|in:Herr,Frau,Divers',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'house_number' => 'nullable|string|max:20',
            'postal_code' => 'nullable|string|max:20',
            'city' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'whatsapp' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',

            // Visibility toggles
            'show_company_name' => 'boolean',
            'show_salutation' => 'boolean',
            'show_first_name' => 'boolean',
            'show_last_name' => 'boolean',
            'show_street' => 'boolean',
            'show_house_number' => 'boolean',
            'show_postal_code' => 'boolean',
            'show_city' => 'boolean',
            'show_country' => 'boolean',
            'show_phone' => 'boolean',
            'show_mobile' => 'boolean',
            'show_whatsapp' => 'boolean',
            'show_website' => 'boolean',
        ]);

        $location = Location::where('id', $locationId)
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        $contactDetails = LocationContactDetail::updateOrCreate(
            ['location_id' => $location->id],
            $validated
        );

        $useCustom = $validated['use_custom_contact_details'] ?? false;

        return response()->json([
            'success' => true,
            'message' => 'Standort-Kontaktdaten erfolgreich aktualisiert.',
            'status' => $useCustom ? 'Eigene' : 'Standard',
            'contact_details' => $contactDetails,
        ]);
    }

    /**
     * Reset location to use default contact details
     */
    public function resetLocation($locationId)
    {
        $location = Location::where('id', $locationId)
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        $contactDetails = LocationContactDetail::updateOrCreate(
            ['location_id' => $location->id],
            ['use_custom_contact_details' => false]
        );

        return response()->json([
            'success' => true,
            'message' => 'Standort auf Standard-Kontaktdaten zurückgesetzt.',
            'status' => 'Standard',
        ]);
    }
}