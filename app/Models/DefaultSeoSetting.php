<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultSeoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'scope',
        'scope_value',
        'meta_title_template',
        'meta_description_template',
        'meta_keywords_template',
        'content_template',
        'settings',
        'priority',
        'status'
    ];

    protected $casts = [
        'settings' => 'array',
        'priority' => 'integer'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByPriority($query)
    {
        return $query->orderByDesc('priority');
    }

    /**
     * Get default SEO for category + location combination
     */
    public static function getDefaultSeo($category = null, $location = null, $country = 'DE')
    {
        $placeholders = [
            '{category}' => $category ? $category->name : '',
            '{category_slug}' => $category ? $category->slug : '',
            '{city}' => $location['city'] ?? '',
            '{state}' => $location['state'] ?? '',
            '{country}' => $country,
            '{country_name}' => self::getCountryName($country),
        ];

        // Try to find most specific match first
        $queries = [
            // Most specific: category + country
            ['type' => 'category_location', 'scope' => 'country', 'scope_value' => $country],
            // Category only
            ['type' => 'category_location', 'scope' => null, 'scope_value' => null],
            // Location only
            ['type' => 'location_only', 'scope' => 'country', 'scope_value' => $country],
            // Global fallback
            ['type' => 'global', 'scope' => null, 'scope_value' => null],
        ];

        foreach ($queries as $queryParams) {
            $setting = self::active()
                          ->where('type', $queryParams['type'])
                          ->when($queryParams['scope'], function ($q) use ($queryParams) {
                              $q->where('scope', $queryParams['scope'])
                                ->where('scope_value', $queryParams['scope_value']);
                          })
                          ->when(!$queryParams['scope'], function ($q) {
                              $q->whereNull('scope');
                          })
                          ->byPriority()
                          ->first();

            if ($setting) {
                return [
                    'title' => self::replacePlaceholders($setting->meta_title_template, $placeholders),
                    'description' => self::replacePlaceholders($setting->meta_description_template, $placeholders),
                    'keywords' => self::replacePlaceholders($setting->meta_keywords_template, $placeholders),
                    'content' => self::replacePlaceholders($setting->content_template, $placeholders),
                    'source' => 'default_setting',
                    'setting_id' => $setting->id
                ];
            }
        }

        // Ultimate fallback
        return [
            'title' => self::generateFallbackTitle($category, $location),
            'description' => self::generateFallbackDescription($category, $location),
            'keywords' => self::generateFallbackKeywords($category, $location),
            'content' => self::generateFallbackContent($category, $location),
            'source' => 'system_fallback'
        ];
    }

    /**
     * Replace placeholders in template
     */
    private static function replacePlaceholders($template, $placeholders)
    {
        if (!$template) return null;
        
        return str_replace(array_keys($placeholders), array_values($placeholders), $template);
    }

    /**
     * Get country name by code
     */
    private static function getCountryName($code)
    {
        $countries = [
            'DE' => 'Deutschland',
            'AT' => 'Österreich',
            'CH' => 'Schweiz'
        ];

        return $countries[$code] ?? $code;
    }

    /**
     * Generate fallback title
     */
    private static function generateFallbackTitle($category, $location)
    {
        if ($category && $location) {
            return "{$category->name} mieten in {$location['city']} - Inlando";
        } elseif ($location) {
            return "Vermieten in {$location['city']} - Inlando";
        } elseif ($category) {
            return "{$category->name} mieten - Inlando";
        }
        
        return "Vermieten - Inlando";
    }

    /**
     * Generate fallback description
     */
    private static function generateFallbackDescription($category, $location)
    {
        if ($category && $location) {
            return "Finden Sie {$category->name} zur Miete in {$location['city']}. Große Auswahl, faire Preise, einfache Buchung bei Inlando.";
        } elseif ($location) {
            return "Finden Sie alles zur Miete in {$location['city']}. Große Auswahl, faire Preise, einfache Buchung bei Inlando.";
        } elseif ($category) {
            return "Finden Sie {$category->name} zur Miete. Große Auswahl, faire Preise, einfache Buchung bei Inlando.";
        }
        
        return "Finden Sie alles zur Miete. Große Auswahl, faire Preise, einfache Buchung bei Inlando.";
    }

    /**
     * Generate fallback keywords
     */
    private static function generateFallbackKeywords($category, $location)
    {
        $keywords = ['mieten', 'Vermietung', 'Inlando'];
        
        if ($category) {
            $keywords[] = $category->name;
        }
        
        if ($location) {
            $keywords[] = $location['city'];
        }
        
        return implode(', ', $keywords);
    }

    /**
     * Generate fallback content
     */
    private static function generateFallbackContent($category, $location)
    {
        if ($category && $location) {
            $categoryName = $category->name;
            $cityName = $location['city'];
            
            return "
            <h1>{$categoryName} mieten in {$cityName}</h1>
            <p>Suchen Sie {$categoryName} zur Miete in {$cityName}? Bei Inlando finden Sie eine große Auswahl an hochwertigen {$categoryName} von verifizierten Anbietern.</p>
            
            <h2>Warum {$categoryName} in {$cityName} bei Inlando mieten?</h2>
            <ul>
                <li>Große Auswahl an {$categoryName} in {$cityName}</li>
                <li>Faire und transparente Preise</li>
                <li>Einfache Online-Buchung</li>
                <li>Verifizierte Anbieter</li>
                <li>Schnelle Verfügbarkeitsprüfung</li>
            </ul>
            ";
        }
        
        return "<h1>Willkommen bei Inlando</h1><p>Ihre Plattform für Vermietungen.</p>";
    }
}
