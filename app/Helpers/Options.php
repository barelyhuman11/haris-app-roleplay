<?php

namespace App\Helpers;

use App\Models\Option;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use GeoIp2\Database\Reader;

class Options
{
    private $options = [];

    private $widgets = [];

    public function __construct()
    {
        $options = Option::where('autoload', 'Y')
            ->get();

        $this->widgets = $options->groupBy('name')
            ->toArray();

        $this->options = $options->mapWithKeys(function ($option) {
                return [$option['name'] => $option['value']];
            })
            ->toArray();
    }

    public function get($name = null, $default = null)
    {
        if ($name) {
            if (isset($this->options[$name])) {
                return $this->options[$name];
            }
            else {
                return $default;
            }
        }
        else {
            return $this->options;
        }
    }

    public function widgets($name = null)
    {
        $CrawlerDetect = new CrawlerDetect();

        if ($name) {
            if (isset($this->widgets[$name]) && is_array($this->widgets[$name])) {
                $output = [];
                foreach ($this->widgets[$name] as $widget) {
                    $widget = json_decode($widget['value'], true);

                    // Filter user agent
                    if (isset($widget['user_agent'])) {
                        if ($widget['user_agent'] == 'BOT' && !$CrawlerDetect->isCrawler()) {
                            continue;
                        }

                        if ($widget['user_agent'] == 'HUMAN' && $CrawlerDetect->isCrawler()) {
                            continue;
                        }
                    }
                    // [END] Filter user agent

                    // Filter countries
                    $databaseFile = resource_path('database/GeoLite2-Country.mmdb');
                    $MaxMind = new Reader($databaseFile);

                    $userIP = request()->ip();
                    
                    try {
                        $record = $MaxMind->country($userIP);
                        $userCountryCode = $record->country->isoCode;
                    }
                    catch (\Exception $e) {
                        // continue;
                    }

                    if (
                        isset($widget['countries']) &&
                        ($widget['countries'] == 'BLACKLIST' || $widget['countries'] == 'WHITELIST') &&
                        isset($widget['list_countries']) &&
                        is_array($widget['list_countries'])
                    ) {
                        $listCountries = $widget['list_countries'];
                        
                        if ($widget['countries'] == 'BLACKLIST' && in_array($userCountryCode, $listCountries)) {
                            continue;
                        }

                        if ($widget['countries'] == 'WHITELIST' && !in_array($userCountryCode, $listCountries)) {
                            continue;
                        }
                    }
                    // [END] Filter countries

                    $output[] = $widget['content'];
                }
                return implode('', $output);
            }
        }
        else {
            return $this->widgets;
        }
    }

    public function set($name = null, $value = null, $unique = true)
    {
        if (!empty($name)) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            if ($unique) {
                Option::updateOrCreate(
                    ['name' => $name],
                    ['value' => $value]
                );
            }
            else {
                Option::create([
                    'name' => $name,
                    'value' => $value,
                ]);
            }
        }
    }

    public function update(Option $widget, $name = null, $value = null)
    {
        if (!empty($name)) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            $widget->update([
                'name' => $name,
                'value' => $value,
            ]);
        }
    }

    public function setMany($options)
    {
        foreach ($options as $optionName => $optionValue) {
            $this->set($optionName, $optionValue);
        }
    }
}