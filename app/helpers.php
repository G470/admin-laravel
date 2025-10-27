<?php

// Global String Helper Functions for Blade Templates
if (!function_exists('str_limit')) {
    /**
     * Limit the number of characters in a string.
     *
     * @param string $value
     * @param int $limit
     * @param string $end
     * @return string
     */
    function str_limit($value, $limit = 100, $end = '...')
    {
        return \Illuminate\Support\Str::limit($value, $limit, $end);
    }
}

if (!function_exists('str_slug')) {
    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param string $title
     * @param string $separator
     * @return string
     */
    function str_slug($title, $separator = '-')
    {
        return \Illuminate\Support\Str::slug($title, $separator);
    }
}

if (!function_exists('str_plural')) {
    /**
     * Get the plural form of an English word.
     *
     * @param string $value
     * @param int $count
     * @return string
     */
    function str_plural($value, $count = 2)
    {
        return \Illuminate\Support\Str::plural($value, $count);
    }
}

if (!function_exists('str_title')) {
    /**
     * Convert a value to title case.
     *
     * @param string $value
     * @return string
     */
    function str_title($value)
    {
        return \Illuminate\Support\Str::title($value);
    }
}

if (!function_exists('setting')) {
    /**
     * Get a setting value from configuration or database
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        static $settings = null;

        // Initialize settings cache if not already loaded
        if ($settings === null) {
            $settings = [
                // Category SEO Templates
                'category_meta_title_template' => '{category} mieten deutschlandweit - Inlando',
                'category_meta_description_template' => '{category} gesucht? Entdecken Sie hier zahlreiche Angebote für {category}. Mieten Sie jetzt zum Bestpreis Ihren Wunschartikel auf Inlando!',
                'category_default_text_template' => '{category} mieten - Inlando

{category} gesucht? Auf Inlando sind Sie genau richtig! Wir bieten Ihnen eine große Auswahl an Mietartikeln für {category}, die Ihre Bedürfnisse erfüllen. Egal ob Sie {category} für einen kurzen Zeitraum oder längerfristig benötigen - bei uns finden Sie das passende Angebot.',

                // Location SEO Templates
                'location_meta_title_template' => '{category} in {city} mieten - Inlando',
                'location_meta_description_template' => '{category} in {city} gesucht? Entdecken Sie hier zahlreiche Angebote für {category}. Mieten Sie jetzt zum Bestpreis Ihren Wunschartikel auf Inlando!',
                'location_default_text_template' => '{category} in {city} mieten - Inlando<br/><br/>
Suchen Sie nach {category} in {city}? Inlando ist Ihr idealer Ansprechpartner! Wir präsentieren Ihnen eine breite Palette an Mietartikeln wie {category}, die Ihre Bedürfnisse perfekt erfüllen. Egal ob Sie {category} für eine kurzfristige Nutzung oder länger benötigen - bei uns werden Sie fündig.',

                // Default site settings
                'site_name' => 'Inlando',
                'site_description' => 'Die führende Mietplattform für alle Ihre Bedürfnisse',
                'meta_title' => 'Inlando - Mieten leicht gemacht',
                'meta_description' => 'Entdecken Sie auf Inlando eine große Auswahl an Mietartikeln. Von Fahrzeugen bis hin zu Geräten - finden Sie was Sie brauchen.',
                'meta_keywords' => 'mieten, vermietung, inlando, mietartikel, fahrzeuge, geräte',
            ];

            // In a real application, you would load these from a database or config file
            // For now, we use the static array above as fallback

            // TODO: Implement database-backed settings
            // $dbSettings = DB::table('settings')->pluck('value', 'key')->toArray();
            // $settings = array_merge($settings, $dbSettings);
        }

        return $settings[$key] ?? $default;
    }
}

if (!function_exists('update_setting')) {
    /**
     * Update a setting value
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    function update_setting($key, $value)
    {
        // TODO: Implement database update
        // return DB::table('settings')->updateOrInsert(['key' => $key], ['value' => $value]);

        // For now, just return true as a placeholder
        return true;
    }
}