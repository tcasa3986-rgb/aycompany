<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    function setting($key, $default = null)
    {
        static $settings = [];

        if (empty($settings)) {
            try {
                $settings = Setting::all()->pluck('value', 'key')->toArray();
            } catch (\Exception $e) {
                return $default;
            }
        }

        return $settings[$key] ?? $default;
    }
}
