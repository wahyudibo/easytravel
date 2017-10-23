<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'tiketdotcom' => [
        'domain'        => env('TIKETDOTCOM_DOMAIN'),
        'business_id'   => env('TIKETDOTCOM_BUSINESS_ID'),
        'business_name' => env('TIKETDOTCOM_BUSINESS_NAME'),
        'secret'        => env('TIKETDOTCOM_SECRET')
    ],

    'google' => [
        'secret' => env('GOOGLE_SECRET'),
        'maps' => [
            'domain' => env('GOOGLEMAPS_DOMAIN')
        ],

    ],

    'foursquare' =>[
        'domain'    => env('FOURSQUARE_DOMAIN'),
        'client_id' => env('FOURSQUARE_CLIENT_ID'),
        'secret'    => env('FOURSQUARE_SECRET')
    ],

];
