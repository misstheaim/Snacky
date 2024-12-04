<?php

return [
    'hostname' => env('UZUM_HOST'),
    'graphql_url' => env('UZUM_GRAPHQL_URL'),
    'token_url' => env('UZUM_TOKEN_URL'),
    'categories_url' => env('UZUM_CATEGORIES_URL'),
    'product_url' => env('UZUM_PRODUCT_URL'),
    'user_agent_header' => env('USER_AGENT'),
    'accept_encodeing_header' => 'gzip, deflate, br',
    'x_iid_header' => env('UZUM_X_IID'),
    'accept_language_header' => [
        'ru' => 'ru-RU',
        'uz' => 'uz-UZ',
    ],
    'referer_header' => 'https://uzum.uz/',
];
