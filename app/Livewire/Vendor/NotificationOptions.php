<?php

namespace App\Livewire\Vendor;

use Livewire\Component;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class NotificationOptions extends Component
{
    // Default settings
    public $defaultNotificationEmail = '';
    public $notificationPreferences = [];

    // Location-specific settings
    public $showLocationModal = false;
    public $editingLocationId = null;
    public $locationNotificationEmail = '';
    public $useCustomNotifications = false;
    public $currentLocation = null;

    // State management
    public $isEditingDefault = false;

    protected $rules = [
        'defaultNotificationEmail' => 'required|email|max:255',
        'locationNotificationEmail' => 'required_if:useCustomNotifications,true|nullable|email|max:255',
    ];

    protected $messages = [
        'defaultNotificationEmail.required' => 'Standard-Benachrichtigungs-E-Mail ist erforderlich.',
        'defaultNotificationEmail.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
        'locationNotificationEmail.required_if' => 'E-Mail-Adresse ist erforderlich, wenn spezifische Benachrichtigungen aktiviert sind.',
        'locationNotificationEmail.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->defaultNotificationEmail = $user->default_notification_email ?? '';
        $this->notificationPreferences = $user->notification_preferences ?? [];
    }

    /**
     * Toggle default settings edit mode
     */
    public function toggleDefaultEdit()
    {
        $this->isEditingDefault = !$this->isEditingDefault;
        if (!$this->isEditingDefault) {
            // Reset to original values if canceling
            $user = Auth::user();
            $this->defaultNotificationEmail = $user->default_notification_email ?? '';
        }
        $this->resetValidation();
    }

    /**
     * Save default notification settings
     */
    public function saveDefaultSettings()
    {
        $this->validate(['defaultNotificationEmail' => 'required|email|max:255']);

        $user = Auth::user();
        $user->update([
            'default_notification_email' => $this->defaultNotificationEmail,
            'notification_preferences' => $this->notificationPreferences,
        ]);

        $this->isEditingDefault = false;
        session()->flash('message', 'Standard-Benachrichtigungsoptionen erfolgreich gespeichert.');
    }

    /**
     * Open location edit modal
     */
    public function editLocation($locationId)
    {
        $location = Location::where('id', $locationId)
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        $this->editingLocationId = $locationId;
        $this->currentLocation = $location;
        $this->locationNotificationEmail = $location->notification_email ?? '';
        $this->useCustomNotifications = $location->use_custom_notifications;
        $this->showLocationModal = true;
        $this->resetValidation();
    }

        /**
     * Save location notification settings
     */
    public function saveLocationSettings()
    {
        $this->validate([
            'locationNotificationEmail' => 'required_if:useCustomNotifications,true|nullable|email|max:255',
        ]);

        $location = Location::find($this->editingLocationId);
        
        $email = $this->useCustomNotifications ? $this->locationNotificationEmail : null;
        $location->updateNotificationSettings($email, $this->useCustomNotifications);

        $this->closeLocationModal();
        session()->flash('message', 'Standort-Benachrichtigungen erfolgreich gespeichert.');
    }

    /**
     * Reset location to default settings
     */
    public function resetLocationToDefault($locationId)
    {
        $location = Location::where('id', $locationId)
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        $location->updateNotificationSettings(null, false);
        session()->flash('message', 'Standort auf Standard-Einstellungen zurückgesetzt.');
    }

    /**
     * Close location modal
     */
    public function closeLocationModal()
    {
        $this->showLocationModal = false;
        $this->editingLocationId = null;
        $this->currentLocation = null;
        $this->locationNotificationEmail = '';
        $this->useCustomNotifications = false;
        $this->resetValidation();
    }

    /**
     * Render component
     */
    public function render()
    {
        $user = Auth::user();
        $locations = $user->getLocationsWithNotificationStatus();

        return view('livewire.vendor.notification-options', compact('locations'));
    }
}
