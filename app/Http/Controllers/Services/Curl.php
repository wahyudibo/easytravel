<?php

namespace App\Http\Controllers\Services;

class Curl
{
    public static function request ($user_options, $verbose = false)
    {
        // default options
        $options[CURLOPT_FOLLOWLOCATION] = true;
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLOPT_SSL_VERIFYHOST] = 2;
        $options[CURLOPT_SSL_VERIFYPEER] = true;
        $options[CURLOPT_CAINFO]         = dirname(getcwd()) . '/storage/app/cacert.pem';
        // $options[CURLOPT_PROXY]         = '127.0.0.1:9999';
        // $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_USERAGENT]      = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36';
        $options[CURLOPT_ENCODING]       = 'gzip, deflate';
        $options[CURLOPT_HEADER]         = false;

        $options = $user_options + $options;

        //do curl
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $output = curl_exec($ch);

        if($verbose){
          $info = curl_getinfo($ch);
          $errno = curl_errno($ch);
          $error = curl_error($ch);
          $response = compact('info', 'errno', 'error', 'output');
        } else {
          $response = $output;
        }

        curl_close($ch);

        return $response;
    }
}
