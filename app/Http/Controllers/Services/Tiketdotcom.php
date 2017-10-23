<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Services\Curl;

class Tiketdotcom
{
    private $domain,
            $business_id,
            $business_name,
            $secret,
            $header,
            $output,
            $language;

    public function __construct ()
    {
        // tiketcom variables
        $this->domain        = config('services.tiketdotcom.domain');
        $this->business_id   = config('services.tiketdotcom.business_id');
        $this->business_name = config('services.tiketdotcom.business_name');
        $this->secret        = config('services.tiketdotcom.secret');
        $this->header        = "twh: $this->business_id;$this->business_name;";
        $this->language      = 'en';
        $this->output        = 'json';

        if (!session('token')) {
            $this->getToken();
        }

    }

    public function flights($post_data = array())
    {
        $post_data['token']  = session('token');
        $post_data['v']      = '3';
        $post_data['lang']   = $this->language;
        $post_data['output'] = $this->output;

        $header = [];
        $header[] = $this->header;

        $query_data = http_build_query($post_data);

        $url = "{$this->domain}/search/flight?$query_data";

        $options[CURLOPT_HTTPHEADER] = $header;
        $options[CURLOPT_URL] = $url;

        return Curl::request($options);

    }

    public function hotels($post_data = array())
    {
        $post_data['token']  = session('token');
        $post_data['lang']   = $this->language;
        $post_data['output'] = $this->output;

        $header = [];
        $header[] = $this->header;

        $query_data = http_build_query($post_data);

        $url = "{$this->domain}/search/hotel?$query_data";

        $options[CURLOPT_HTTPHEADER] = $header;
        $options[CURLOPT_URL] = $url;

        return Curl::request($options);
    }

    /////////////////////////////////////
    // All Private Functions Goes Here //
    /////////////////////////////////////

    private function getToken()
    {
        $header = [];
        $header[] = $this->header;

        $url = "{$this->domain}/apiv1/payexpress?method=getToken&secretkey={$this->secret}&output=json";
        $options[CURLOPT_URL] = $url;

        $response = Curl::request($options);

        session(['token' => json_decode($response, true)['token']]);
    }


}