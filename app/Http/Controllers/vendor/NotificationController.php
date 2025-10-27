<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display notification options index
     */
    public function index()
    {
        $user = Auth::user();
        $locations = $user->getLocationsWithNotificationStatus();

        return view('content.vendor.notifications.index', compact('user', 'locations'));
    }

    /**
     * Update default notification settings
     */
    public function updateDefault(Request $request)
    {
        $request->validate([
            'default_notification_email' => 'required|email|max:255',
            'notification_preferences' => 'nullable|array',
        ]);

        $user = Auth::user();
        $user->update([
            'default_notification_email' => $request->default_notification_email,
            'notification_preferences' => $request->notification_preferences ?? [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Standard-Benachrichtigungsoptionen erfolgreich aktualisiert.',
        ]);
    }

    /**
     * Get location notification settings for edit modal
     */
    public function getLocationSettings($locationId)
    {
        $location = Location::where('id', $locationId)
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        return response()->json([
            'location' => $location,
            'default_email' => Auth::user()->default_notification_email,
        ]);
    }

    /**
     * Update location-specific notification settings
     */
    public function updateLocation(Request $request, $locationId)
    {
        $request->validate([
            'notification_email' => 'required_if:use_custom,true|nullable|email|max:255',
            'use_custom' => 'boolean',
        ]);

        $location = Location::where('id', $locationId)
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        $useCustom = $request->boolean('use_custom');
        $email = $useCustom ? $request->notification_email : null;

        $location->updateNotificationSettings($email, $useCustom);

        return response()->json([
            'success' => true,
            'message' => 'Standort-Benachrichtigungen erfolgreich aktualisiert.',
            'status' => $useCustom ? 'Eigene' : 'Standard',
            'effective_email' => $location->effective_notification_email,
        ]);
    }

    /**
     * Reset location to use default settings
     */
    public function resetLocation($locationId)
    {
        $location = Location::where('id', $locationId)
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        $location->updateNotificationSettings(null, false);

        return response()->json([
            'success' => true,
            'message' => 'Standort auf Standard-Einstellungen zurückgesetzt.',
            'status' => 'Standard',
            'effective_email' => Auth::user()->default_notification_email,
        ]);
    }
}
