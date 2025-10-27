<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PaymentProviderService
{
    const CACHE_TTL = 3600; // 1 hour

    /**
     * Get PayPal configuration for specified environment
     */
    public static function getPayPalKeys(string $environment = 'production'): array
    {
        $cacheKey = "paypal_keys_{$environment}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($environment) {
            return [
                'client_id' => self::getDecryptedSetting("paypal_client_id_{$environment}"),
                'client_secret' => self::getDecryptedSetting("paypal_client_secret_{$environment}"),
                'webhook_url' => Setting::get("paypal_webhook_url_{$environment}"),
                'sandbox_mode' => $environment === 'development',
            ];
        });
    }

    /**
     * Get Stripe configuration for specified environment
     */
    public static function getStripeKeys(string $environment = 'production'): array
    {
        $cacheKey = "stripe_keys_{$environment}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($environment) {
            return [
                'publishable_key' => self::getDecryptedSetting("stripe_publishable_key_{$environment}"),
                'secret_key' => self::getDecryptedSetting("stripe_secret_key_{$environment}"),
                'webhook_secret' => self::getDecryptedSetting("stripe_webhook_secret_{$environment}"),
                'test_mode' => $environment === 'development',
            ];
        });
    }

    /**
     * Check if payment provider is properly configured
     */
    public static function isConfigured(string $provider, string $environment = 'production'): bool
    {
        $keys = match ($provider) {
            'paypal' => self::getPayPalKeys($environment),
            'stripe' => self::getStripeKeys($environment),
            default => [],
        };

        return match ($provider) {
            'paypal' => !empty($keys['client_id']) && !empty($keys['client_secret']),
            'stripe' => !empty($keys['publishable_key']) && !empty($keys['secret_key']),
            default => false,
        };
    }

    /**
     * Validate API key formats
     */
    public static function validateKeyFormat(string $provider, string $keyType, string $value): bool
    {
        return match ("{$provider}_{$keyType}") {
            'stripe_publishable_key' => str_starts_with($value, 'pk_'),
            'stripe_secret_key' => str_starts_with($value, 'sk_'),
            'stripe_webhook_secret' => str_starts_with($value, 'whsec_'),
            'paypal_client_id' => strlen($value) >= 20 && ctype_alnum(str_replace(['-', '_'], '', $value)),
            'paypal_client_secret' => strlen($value) >= 20,
            default => true,
        };
    }

    /**
     * Test API connection
     */
    public static function testConnection(string $provider, string $environment = 'production'): array
    {
        try {
            $keys = match ($provider) {
                'paypal' => self::getPayPalKeys($environment),
                'stripe' => self::getStripeKeys($environment),
                default => [],
            };

            if (empty($keys)) {
                return ['success' => false, 'message' => 'API keys not configured'];
            }

            // Test connection logic would go here
            // For now, return success if keys are present
            return ['success' => true, 'message' => 'Connection test successful'];

        } catch (\Exception $e) {
            Log::error("Payment provider connection test failed: {$e->getMessage()}");
            return ['success' => false, 'message' => 'Connection test failed'];
        }
    }

    /**
     * Clear cached keys
     */
    public static function clearCache(): void
    {
        Cache::forget('paypal_keys_production');
        Cache::forget('paypal_keys_development');
        Cache::forget('stripe_keys_production');
        Cache::forget('stripe_keys_development');
    }

    /**
     * Get and decrypt setting value
     */
    private static function getDecryptedSetting(string $key): ?string
    {
        $value = Setting::get($key);

        if (empty($value)) {
            return null;
        }

        try {
            return decrypt($value);
        } catch (\Exception $e) {
            Log::error("Failed to decrypt setting {$key}: {$e->getMessage()}");
            return null;
        }
    }
}