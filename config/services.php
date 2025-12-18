<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'pusher' => [
        'key' => env('PUSHER_APP_KEY', '443fb7eb8273230a07de'),
        'secret' => env('PUSHER_APP_SECRET', '85b45764ff857c901bf5'),
        'app_id' => env('PUSHER_APP_ID', '1101241'),
        'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
    ],

];
