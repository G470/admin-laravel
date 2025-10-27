<?php

if (!function_exists('setting')) {
    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        static $settings = null;
        
        // Cache settings for performance
        if ($settings === null) {
            $settings = \App\Models\Setting::all()->pluck('value', 'key');
        }
        
        return $settings->get($key, $default);
    }
}

if (!function_exists('setting_group')) {
    /**
     * Get all settings for a specific group
     *
     * @param string $group
     * @return array
     */
    function setting_group($group)
    {
        return \App\Models\Setting::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }
}

if (!function_exists('update_setting')) {
    /**
     * Update a setting value
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @return bool
     */
    function update_setting($key, $value, $group = 'general')
    {
        return \App\Models\Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => gettype($value)
            ]
        );
    }
}
