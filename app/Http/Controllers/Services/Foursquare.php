<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\Curl;

class Foursquare
{
    private $client_id, $secret;

    public function __construct()
    {
        $this->domain    = config('services.foursquare.domain');
        $this->client_id = config('services.foursquare.client_id');
        $this->secret    = config('services.foursquare.secret');
    }

    public function exploreVenues($coordinates)
    {
        $url = "{$this->domain}/venues/explore?" .
                "ll={$coordinates}" .
                "&section=topPicks" .
                "&client_id={$this->client_id}" .
                "&client_secret={$this->secret}" .
                "&v=20151018" .
                "&limit=20";

        $options[CURLOPT_URL] = $url;

        return Curl::request($options);
    }
}