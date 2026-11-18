<?php

return [
    'green_api' => [
        'url' => env('GREEN_API_URL', 'https://api.green-api.com'),
        'id_instance' => env('GREEN_API_ID_INSTANCE'),
        'token' => env('GREEN_API_TOKEN'),
        'country_code' => env('WHATSAPP_DEFAULT_COUNTRY_CODE', '243'),
    ],

     'green_api' => [
        'url' => env('GREEN_API_URL', 'https://api.green-api.com'),
        'id_instance' => env('GREEN_API_ID_INSTANCE'),
        'token' => env('GREEN_API_TOKEN'),
        'country_code' => env('WHATSAPP_DEFAULT_COUNTRY_CODE', '243'),
        'admin_numbers' => array_filter(array_map('trim', explode(',', env('WHATSAPP_ADMIN_NUMBERS', '')))),
    ],
];


