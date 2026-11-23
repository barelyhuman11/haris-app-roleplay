<?php

namespace App\Helpers;

class Widgets
{
    public static function detail($widgetJSON, $key = null, $default = null)
    {
        $widgetJSON = json_decode($widgetJSON, true);
    
        if (isset($widgetJSON[$key])) {
            return $widgetJSON[$key];
        }
        else {
            return $default;
        }
    }
}