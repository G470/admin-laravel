<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('content.admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'admin_email' => 'required|email',
            'support_email' => 'required|email',
            'default_language' => 'required|string|max:2',
            'date_format' => 'required|string|max:20',
            'maintenance_mode' => 'boolean',
            'enable_registration' => 'boolean',
            'maintenance_message' => 'nullable|string',
            'currency' => 'required|string|max:3',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'payment_methods' => 'required|array',
            'service_fee_percentage' => 'required|numeric|min:0|max:100',
            'host_fee_percentage' => 'required|numeric|min:0|max:100',
            'notify_admin_new_booking' => 'boolean',
            'notify_vendor_new_booking' => 'boolean',
            'notify_renter_new_booking' => 'boolean',
            'notify_admin_booking_confirm' => 'boolean',
            'notify_vendor_booking_confirm' => 'boolean',
            'notify_renter_booking_confirm' => 'boolean',
            'notify_admin_payment_received' => 'boolean',
            'notify_vendor_payment_received' => 'boolean',
            'notify_renter_payment_received' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'enable_api' => 'boolean',
            'enable_developer_mode' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Einstellungen wurden erfolgreich gespeichert.');
    }

    public function clearCache()
    {
        \Artisan::call('cache:clear');
        \Artisan::call('config:clear');
        \Artisan::call('view:clear');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Cache wurde erfolgreich gelöscht.');
    }

    public function backup()
    {
        \Artisan::call('backup:run');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Backup wurde erfolgreich erstellt.');
    }

    public function maintenanceMode(Request $request)
    {
        $enabled = $request->boolean('enabled');
        $message = $request->input('message');

        Setting::set('maintenance_mode', $enabled);
        if ($message) {
            Setting::set('maintenance_message', $message);
        }

        if ($enabled) {
            \Artisan::call('down', ['--message' => $message]);
        } else {
            \Artisan::call('up');
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Wartungsmodus wurde erfolgreich ' . ($enabled ? 'aktiviert' : 'deaktiviert') . '.');
    }
}