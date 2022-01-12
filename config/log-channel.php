<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Ecommerce Paypal Log Channel
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    |
    */

    'ecommerce-paypal' => [
        'driver' => 'daily',
        'path' => storage_path('logs/ecommerce-paypal.log'),
        'level' => 'debug',
        'days' => 30,
    ],

];
