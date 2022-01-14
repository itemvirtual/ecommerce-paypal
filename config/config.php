<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paypal user
    |--------------------------------------------------------------------------
    |
    | https://developer.paypal.com/
    |
    | In "My Apps & Credentials" Create a REST API App
    |
    */
    'client_id' => env('ECOMMERCE_PAYPAL_CLIENT_ID'),
    'secret' => env('ECOMMERCE_PAYPAL_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Paypal urls
    |--------------------------------------------------------------------------
    |
    |
    */
    'return_url' => env('ECOMMERCE_PAYPAL_RETURN_URL'),
    'cancel_url' => env('ECOMMERCE_PAYPAL_CANCEL_URL'),

    /*
    |--------------------------------------------------------------------------
    | Paypal config
    |--------------------------------------------------------------------------
    |
    |
    */
    'currency_code' => 'EUR',

    /*
    |--------------------------------------------------------------------------
    | SDK configuration
    |--------------------------------------------------------------------------
    |
    |
    */
    'settings' => [
        /**
         * Available option 'sandbox' or 'live'
         */
        'mode' => env('ECOMMERCE_PAYPAL_MODE', 'sandbox'),
    ],

];