<?php

return [
    'base_url' => env('PS_BASE_URL'),
    'api_key' => env('PS_API_KEY'),
    'timeout' => 15,
    'retry' => [
        'times' => 3,
        'sleep' => 200,
    ],
];