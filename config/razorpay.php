<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Razorpay API Credentials
    |--------------------------------------------------------------------------
    |
    | Your Razorpay API Key ID and Key Secret. These are used for creating
    | orders and verifying payment signatures. Use test keys for development.
    |
    */

    'key_id' => env('RAZORPAY_KEY_ID', ''),
    'key_secret' => env('RAZORPAY_KEY_SECRET', ''),

];
