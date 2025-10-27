<?php

namespace App\Helpers;

use App\Models\Category;
use App\Models\Location;

class SeoHelper
{
    /**
     * Replace variables in SEO templates
     *
     * @param string $template
     * @param array $variables
     * @return string
     */
    public static function replaceVariables(string $template, array $variables = []): string
    {
        if (empty($template)) {
            return '';
        }

        // Define replacements
        $replacements = [];

        // Add provided variables
        foreach ($variables as $key => $value) {
            $replacements['{' . $key . '}'] = $value ?? '';
        }

        // Replace variables in template
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Get SEO data for category with fallback to global templates
     *
     * @param Category $category
     * @param Location|null $location
     * @return array
     */
    public static function getCategorySeoData(Category $category, Location $location = null): array
    {
        // Prepare variables for replacement
        $variables = [
            'category' => $category->name,
            'city' => $location->city ?? '',
            'postcode' => $location->postcode ?? '',
            'state' => $location->state ?? '',
            'country' => $location->country ?? 'Deutschland',
        ];

        // Get global templates from settings
        $globalTemplates = self::getGlobalSeoTemplates();

        // Use category-specific values if available, otherwise use global templates
        $metaTitle = $category->meta_title
            ? $category->meta_title
            : self::replaceVariables($globalTemplates['category_meta_title_template'], $variables);

        $metaDescription = $category->meta_description
            ? $category->meta_description
            : self::replaceVariables($globalTemplates['category_meta_description_template'], $variables);

        $defaultText = $category->default_text_content
            ? $category->default_text_content
            : self::replaceVariables($globalTemplates['category_default_text_template'], $variables);

        return [
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'default_text' => $defaultText,
            'variables' => $variables,
        ];
    }

    /**
     * Get SEO data for location with fallback to global templates
     *
     * @param Location $location
     * @param Category|null $category
     * @return array
     */
    public static function getLocationSeoData(Location $location, Category $category = null): array
    {
        // Prepare variables for replacement
        $variables = [
            'category' => $category->name ?? 'Mietartikel',
            'city' => $location->city,
            'postcode' => $location->postcode,
            'state' => $location->state,
            'country' => $location->country ?? 'Deutschland',
        ];

        // Get global templates from settings
        $globalTemplates = self::getGlobalSeoTemplates();

        // Use location-specific values if available, otherwise use global templates
        $metaTitle = self::replaceVariables($globalTemplates['location_meta_title_template'], $variables);
        $metaDescription = self::replaceVariables($globalTemplates['location_meta_description_template'], $variables);
        $defaultText = self::replaceVariables($globalTemplates['location_default_text_template'], $variables);

        return [
            'meta_title' => $metaTitle,
            'meta_description' => $metaDescription,
            'default_text' => $defaultText,
            'variables' => $variables,
        ];
    }

    /**
     * Get global SEO templates from settings
     *
     * @return array
     */
    private static function getGlobalSeoTemplates(): array
    {
        // In a real application, these would come from a settings table or config
        // For now, we'll use default values that match the settings form
        return [
            'category_meta_title_template' => setting('category_meta_title_template', '{category} mieten deutschlandweit - Inlando'),
            'category_meta_description_template' => setting('category_meta_description_template', '{category} gesucht? Entdecken Sie hier zahlreiche Angebote für {category}. Mieten Sie jetzt zum Bestpreis Ihren Wunschartikel auf Inlando!'),
            'category_default_text_template' => setting('category_default_text_template', '{category} mieten - Inlando

{category} gesucht? Auf Inlando sind Sie genau richtig! Wir bieten Ihnen eine große Auswahl an Mietartikeln für {category}, die Ihre Bedürfnisse erfüllen. Egal ob Sie {category} für einen kurzen Zeitraum oder längerfristig benötigen - bei uns finden Sie das passende Angebot.'),
            'location_meta_title_template' => setting('location_meta_title_template', '{category} in {city} mieten - Inlando'),
            'location_meta_description_template' => setting('location_meta_description_template', '{category} in {city} gesucht? Entdecken Sie hier zahlreiche Angebote für {category}. Mieten Sie jetzt zum Bestpreis Ihren Wunschartikel auf Inlando!'),
            'location_default_text_template' => setting('location_default_text_template', '{category} in {city} mieten - Inlando<br/><br/>
Suchen Sie nach {category} in {city}? Inlando ist Ihr idealer Ansprechpartner! Wir präsentieren Ihnen eine breite Palette an Mietartikeln wie {category}, die Ihre Bedürfnisse perfekt erfüllen. Egal ob Sie {category} für eine kurzfristige Nutzung oder länger benötigen - bei uns werden Sie fündig.'),
        ];
    }

    /**
     * Generate breadcrumb-friendly title
     *
     * @param string $template
     * @param array $variables
     * @return string
     */
    public static function generateBreadcrumbTitle(string $template, array $variables = []): string
    {
        return self::replaceVariables($template, $variables);
    }

    /**
     * Validate SEO template for required variables
     *
     * @param string $template
     * @param array $requiredVariables
     * @return array
     */
    public static function validateTemplate(string $template, array $requiredVariables = []): array
    {
        $errors = [];

        // Check for required variables
        foreach ($requiredVariables as $variable) {
            if (strpos($template, '{' . $variable . '}') === false) {
                $errors[] = "Missing required variable: {" . $variable . "}";
            }
        }

        // Check for unknown variables
        preg_match_all('/\{([^}]+)\}/', $template, $matches);
        $templateVariables = $matches[1] ?? [];
        $allowedVariables = ['category', 'city', 'postcode', 'state', 'country'];

        foreach ($templateVariables as $variable) {
            if (!in_array($variable, $allowedVariables)) {
                $errors[] = "Unknown variable: {" . $variable . "}";
            }
        }

        return $errors;
    }
}