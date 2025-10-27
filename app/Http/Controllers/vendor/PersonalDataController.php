<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class PersonalDataController extends Controller
{
    /**
     * Display the vendor personal data form.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vendor = Auth::user();

        // Countries list for dropdown
        $countries = [
            'Deutschland' => 'Deutschland',
            'Österreich' => 'Österreich',
            'Schweiz' => 'Schweiz',
            'Frankreich' => 'Frankreich',
            'Italien' => 'Italien',
            'Niederlande' => 'Niederlande',
            'Belgien' => 'Belgien',
            'Polen' => 'Polen',
            'Tschechien' => 'Tschechien',
        ];

        // Salutations for dropdown
        $salutations = [
            'Herr' => 'Herr',
            'Frau' => 'Frau',
            'Divers' => 'Divers',
        ];

        return view('vendor.personal-data', compact('vendor', 'countries', 'salutations'));
    }

    /**
     * Update vendor personal data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePersonalData(Request $request)
    {
        $vendor = Auth::user();

        $validated = $request->validate([
            'salutation' => 'nullable|string|in:Herr,Frau,Divers',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'house_number' => 'nullable|string|max:20',
            'address_addition' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
        ]);

        // Update personal data fields
        $vendor->update([
            'salutation' => $validated['salutation'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => $validated['first_name'] . ' ' . $validated['last_name'], // Update full name
            'street' => $validated['street'],
            'house_number' => $validated['house_number'],
            'address_addition' => $validated['address_addition'],
            'postal_code' => $validated['postal_code'],
            'city' => $validated['city'],
            'country' => $validated['country'],
            'phone' => $validated['phone'],
            'mobile' => $validated['mobile'],
        ]);

        return redirect()->route('vendor.personal-data')
            ->with('success', 'Persönliche Daten wurden erfolgreich aktualisiert.');
    }

    /**
     * Update company information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateCompanyData(Request $request)
    {
        $vendor = Auth::user();

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_description' => 'nullable|string|max:2000',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $updateData = [
            'company_name' => $validated['company_name'],
            'company_description' => $validated['company_description'],
        ];

        // Handle company logo upload
        if ($request->hasFile('company_logo')) {
            // Delete old logo if exists
            if ($vendor->company_logo) {
                Storage::disk('public')->delete($vendor->company_logo);
            }

            $logoPath = $request->file('company_logo')->store('company/logos', 'public');
            $updateData['company_logo'] = $logoPath;
        }

        // Handle company banner upload
        if ($request->hasFile('company_banner')) {
            // Delete old banner if exists
            if ($vendor->company_banner) {
                Storage::disk('public')->delete($vendor->company_banner);
            }

            $bannerPath = $request->file('company_banner')->store('company/banners', 'public');
            $updateData['company_banner'] = $bannerPath;
        }

        $vendor->update($updateData);

        return redirect()->route('vendor.personal-data')
            ->with('success', 'Unternehmensdaten wurden erfolgreich aktualisiert.');
    }

    /**
     * Update billing address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateBillingAddress(Request $request)
    {
        $vendor = Auth::user();

        $validated = $request->validate([
            'billing_street' => 'required|string|max:255',
            'billing_house_number' => 'nullable|string|max:20',
            'billing_address_addition' => 'nullable|string|max:255',
            'billing_postal_code' => 'required|string|max:10',
            'billing_city' => 'required|string|max:255',
            'billing_country' => 'required|string|max:255',
        ]);

        $vendor->update([
            'billing_street' => $validated['billing_street'],
            'billing_house_number' => $validated['billing_house_number'],
            'billing_address_addition' => $validated['billing_address_addition'],
            'billing_postal_code' => $validated['billing_postal_code'],
            'billing_city' => $validated['billing_city'],
            'billing_country' => $validated['billing_country'],
        ]);

        return redirect()->route('vendor.personal-data')
            ->with('success', 'Rechnungsadresse wurde erfolgreich aktualisiert.');
    }

    /**
     * Request email address change (sends confirmation email).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateEmail(Request $request)
    {
        $vendor = Auth::user();

        $validated = $request->validate([
            'new_email' => 'required|email|unique:users,email,' . $vendor->id,
            'new_email_confirmation' => 'required|same:new_email',
        ]);

        // Check if the new email is different from current
        if ($validated['new_email'] === $vendor->email) {
            return redirect()->route('vendor.personal-data')
                ->with('error', 'Die neue E-Mail-Adresse muss sich von der aktuellen unterscheiden.');
        }

        try {
            // Create email change token
            $emailChangeToken = \App\Models\EmailChangeToken::createToken(
                $vendor->id,
                $validated['new_email']
            );

            // Send confirmation email to the new email address
            \Illuminate\Support\Facades\Notification::route('mail', $validated['new_email'])
                ->notify(new \App\Notifications\EmailChangeConfirmation($emailChangeToken));

            return redirect()->route('vendor.personal-data')
                ->with('success', 'Bestätigungs-E-Mail wurde an ' . $validated['new_email'] . ' gesendet. Bitte bestätigen Sie die Änderung durch Klick auf den Link in der E-Mail.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Email change request failed: ' . $e->getMessage());

            return redirect()->route('vendor.personal-data')
                ->with('error', 'Fehler beim Senden der Bestätigungs-E-Mail. Bitte versuchen Sie es erneut.');
        }
    }

    /**
     * Confirm email address change.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function confirmEmailChange($token)
    {
        $emailChangeToken = \App\Models\EmailChangeToken::findValidToken($token);

        if (!$emailChangeToken) {
            return redirect()->route('vendor.personal-data')
                ->with('error', 'Ungültiger oder abgelaufener Bestätigungslink.');
        }

        try {
            $user = $emailChangeToken->user;

            // Update the user's email
            $user->update([
                'email' => $emailChangeToken->new_email,
                'email_verified_at' => now(), // Mark as verified since they confirmed via email
            ]);

            // Mark token as used
            $emailChangeToken->markAsUsed();

            // Log the user out to force re-authentication with new email
            Auth::logout();

            return redirect()->route('login')
                ->with('success', 'E-Mail-Adresse wurde erfolgreich geändert. Bitte melden Sie sich mit Ihrer neuen E-Mail-Adresse an.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Email change confirmation failed: ' . $e->getMessage());

            return redirect()->route('vendor.personal-data')
                ->with('error', 'Fehler beim Bestätigen der E-Mail-Änderung. Bitte versuchen Sie es erneut.');
        }
    }

    /**
     * Cancel email address change.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function cancelEmailChange($token)
    {
        $emailChangeToken = \App\Models\EmailChangeToken::findValidToken($token);

        if (!$emailChangeToken) {
            return redirect()->route('vendor.personal-data')
                ->with('error', 'Ungültiger oder abgelaufener Link.');
        }

        // Mark token as used to prevent reuse
        $emailChangeToken->markAsUsed();

        return redirect()->route('vendor.personal-data')
            ->with('success', 'E-Mail-Änderung wurde abgebrochen.');
    }

    /**
     * Update password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $vendor = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $vendor->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return redirect()->route('vendor.personal-data')
            ->with('success', 'Passwort wurde erfolgreich geändert.');
    }

    /**
     * Update vendor avatar/profile image.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAvatar(Request $request)
    {
        $vendor = Auth::user();

        $validated = $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete old profile image if exists
        if ($vendor->profile_image) {
            Storage::disk('public')->delete($vendor->profile_image);
        }

        // Store new profile image
        $imagePath = $request->file('profile_image')->store('profile/avatars', 'public');

        $vendor->update([
            'profile_image' => $imagePath,
        ]);

        return redirect()->route('vendor.personal-data')
            ->with('success', 'Profilbild wurde erfolgreich aktualisiert.');
    }

    /**
     * Delete vendor avatar/profile image.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteAvatar()
    {
        $vendor = Auth::user();

        // Delete profile image if exists
        if ($vendor->profile_image) {
            Storage::disk('public')->delete($vendor->profile_image);

            $vendor->update([
                'profile_image' => null,
            ]);

            return redirect()->route('vendor.personal-data')
                ->with('success', 'Profilbild wurde erfolgreich gelöscht.');
        }

        return redirect()->route('vendor.personal-data')
            ->with('info', 'Kein Profilbild zum Löschen vorhanden.');
    }
}
