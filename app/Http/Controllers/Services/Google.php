<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\Curl;

class Google
{
    private $secret, $googlemaps_domain;

    public function __construct()
    {
        $this->secret = config('services.google.secret');
        $this->googlemaps_domain = config('services.google.maps.domain');
    }

    public function geocodes($location)
    {
        $url = "{$this->googlemaps_domain}/api/geocode/json?address=$location&key={$this->secret}";
        $options[CURLOPT_URL] = $url;

        return Curl::request($options);
    }

    public function distance($origin, $destination)
    {
        $url = "{$this->googlemaps_domain}/api/distancematrix/json?origins=$origin&destinations=$destination&key={$this->secret}";

        $options[CURLOPT_URL] = $url;

        return Curl::request($options);
    }
}