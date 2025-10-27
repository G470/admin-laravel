<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\PaymentProviderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentProviderController extends Controller
{
    /**
     * Display payment provider configuration page
     */
    public function index()
    {
        // Get PayPal configuration
        $paypalProduction = [
            'client_id' => $this->getDecryptedSetting('paypal_client_id_production'),
            'client_secret' => $this->maskSecret($this->getDecryptedSetting('paypal_client_secret_production')),
            'webhook_url' => $this->getDecryptedSetting('paypal_webhook_url_production'),
        ];

        $paypalDevelopment = [
            'client_id' => $this->getDecryptedSetting('paypal_client_id_development'),
            'client_secret' => $this->maskSecret($this->getDecryptedSetting('paypal_client_secret_development')),
            'webhook_url' => $this->getDecryptedSetting('paypal_webhook_url_development'),
        ];

        // Get Stripe configuration
        $stripeProduction = [
            'publishable_key' => $this->getDecryptedSetting('stripe_publishable_key_production'),
            'secret_key' => $this->maskSecret($this->getDecryptedSetting('stripe_secret_key_production')),
            'webhook_secret' => $this->maskSecret($this->getDecryptedSetting('stripe_webhook_secret_production')),
        ];

        $stripeDevelopment = [
            'publishable_key' => $this->getDecryptedSetting('stripe_publishable_key_development'),
            'secret_key' => $this->maskSecret($this->getDecryptedSetting('stripe_secret_key_development')),
            'webhook_secret' => $this->maskSecret($this->getDecryptedSetting('stripe_webhook_secret_development')),
        ];

        // Get subscription pricing rules
        $pricingRules = [
            'base_monthly_fee' => Setting::get('subscription_base_monthly_fee', 29.99),
            'price_per_rental' => Setting::get('subscription_price_per_rental', 2.50),
            'price_per_category' => Setting::get('subscription_price_per_category', 5.00),
            'price_per_location' => Setting::get('subscription_price_per_location', 3.00),
            'minimum_monthly_fee' => Setting::get('subscription_minimum_monthly_fee', 19.99),
            'volume_discount_threshold' => Setting::get('subscription_volume_discount_threshold', 100.00),
            'volume_discount_percentage' => Setting::get('subscription_volume_discount_percentage', 10),
        ];

        // Get current active environments with fallback defaults
        $activeEnvironments = [
            'paypal' => Setting::get('paypal_active_environment', 'production'),
            'stripe' => Setting::get('stripe_active_environment', 'production'),
        ];

        // Ensure we always have valid values
        if (!isset($activeEnvironments['paypal'])) {
            $activeEnvironments['paypal'] = 'production';
        }
        if (!isset($activeEnvironments['stripe'])) {
            $activeEnvironments['stripe'] = 'production';
        }


        return view('content.admin.payment-providers', compact(
            'paypalProduction',
            'paypalDevelopment',
            'stripeProduction',
            'stripeDevelopment',
            'pricingRules',
            'activeEnvironments'
        ));
    }

    /**
     * Update payment provider configuration
     */
    public function update(Request $request)
    {
        $request->validate([
            'paypal_client_id_production' => 'nullable|string',
            'paypal_client_secret_production' => 'nullable|string',
            'paypal_webhook_url_production' => 'nullable|url',
            'paypal_client_id_development' => 'nullable|string',
            'paypal_client_secret_development' => 'nullable|string',
            'paypal_webhook_url_development' => 'nullable|url',
            'stripe_publishable_key_production' => 'nullable|string',
            'stripe_secret_key_production' => 'nullable|string',
            'stripe_webhook_secret_production' => 'nullable|string',
            'stripe_publishable_key_development' => 'nullable|string',
            'stripe_secret_key_development' => 'nullable|string',
            'stripe_webhook_secret_development' => 'nullable|string',
            'subscription_base_monthly_fee' => 'required|numeric|min:0',
            'subscription_price_per_rental' => 'required|numeric|min:0',
            'subscription_price_per_category' => 'required|numeric|min:0',
            'subscription_price_per_location' => 'required|numeric|min:0',
            'subscription_minimum_monthly_fee' => 'required|numeric|min:0',
            'subscription_volume_discount_threshold' => 'required|numeric|min:0',
            'subscription_volume_discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        // Update PayPal settings
        $paypalFields = [
            'paypal_client_id_production',
            'paypal_client_secret_production',
            'paypal_webhook_url_production',
            'paypal_client_id_development',
            'paypal_client_secret_development',
            'paypal_webhook_url_development'
        ];

        foreach ($paypalFields as $field) {
            if ($request->has($field) && $request->$field) {
                Setting::set($field, encrypt($request->$field), 'payment_providers');
            }
        }

        // Update Stripe settings
        $stripeFields = [
            'stripe_publishable_key_production',
            'stripe_secret_key_production',
            'stripe_webhook_secret_production',
            'stripe_publishable_key_development',
            'stripe_secret_key_development',
            'stripe_webhook_secret_development'
        ];

        foreach ($stripeFields as $field) {
            if ($request->has($field) && $request->$field) {
                Setting::set($field, encrypt($request->$field), 'payment_providers');
            }
        }

        // Update subscription pricing
        $pricingFields = [
            'subscription_base_monthly_fee',
            'subscription_price_per_rental',
            'subscription_price_per_category',
            'subscription_price_per_location',
            'subscription_minimum_monthly_fee',
            'subscription_volume_discount_threshold',
            'subscription_volume_discount_percentage'
        ];

        foreach ($pricingFields as $field) {
            Setting::set($field, $request->$field, 'subscription');
        }

        // Clear cached keys
        PaymentProviderService::clearCache();

        return redirect()->back()->with('success', 'Payment provider configuration updated successfully.');
    }

    /**
     * Test payment provider connection
     */
    public function testConnection(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:paypal,stripe',
            'environment' => 'required|in:production,development',
        ]);

        $result = PaymentProviderService::testConnection($request->provider, $request->environment);

        return response()->json($result);
    }

    /**
     * Get provider configuration status
     */
    public function status()
    {
        return response()->json([
            'paypal' => [
                'production' => PaymentProviderService::isConfigured('paypal', 'production'),
                'development' => PaymentProviderService::isConfigured('paypal', 'development'),
            ],
            'stripe' => [
                'production' => PaymentProviderService::isConfigured('stripe', 'production'),
                'development' => PaymentProviderService::isConfigured('stripe', 'development'),
            ],
        ]);
    }

    /**
     * Toggle payment provider environment mode
     */
    public function toggleEnvironment(Request $request)
    {
        try {
            $request->validate([
                'provider' => 'required|in:paypal,stripe',
                'environment' => 'required|in:production,development',
            ]);

            $provider = $request->provider;
            $environment = $request->environment;


            // Store the active environment for this provider
            $setting = Setting::firstOrNew(['key' => "{$provider}_active_environment"]);
            $setting->value = $environment;
            $setting->group = 'payment_providers';
            $setting->save();

            // Clear cached keys
            PaymentProviderService::clearCache();


            return response()->json([
                'success' => true,
                'message' => ucfirst($provider) . ' environment switched to ' . ucfirst($environment),
                'environment' => $environment,
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to update environment: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current active environment for payment providers
     */
    public function getActiveEnvironments()
    {
        return response()->json([
            'paypal' => Setting::get('paypal_active_environment', 'production'),
            'stripe' => Setting::get('stripe_active_environment', 'production'),
        ]);
    }

    /**
     * Get decrypted setting value for display
     */
    private function getDecryptedSetting(string $key): ?string
    {
        $value = Setting::get($key);

        if (empty($value)) {
            return null;
        }

        try {
            return decrypt($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Mask secret values for display
     */
    private function maskSecret(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $length = strlen($value);
        if ($length <= 8) {
            return str_repeat('*', $length);
        }

        return substr($value, 0, 4) . str_repeat('*', $length - 8) . substr($value, -4);
    }
}
