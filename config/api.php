<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Token Header
    |--------------------------------------------------------------------------
    |
    | This value is the name of the header that will be used to validate
    | API requests. You may change this to any header name you prefer.
    |
    */

    'token_header' => env('API_TOKEN_HEADER', 'x-api-token'),

    /*
    |--------------------------------------------------------------------------
    | API Token
    |--------------------------------------------------------------------------
    |
    | This is the static API token that will be used to validate requests.
    | All API requests must include this token in the header specified above.
    | If left empty, the API token validation will be skipped.
    |
    */

    'token' => env('API_TOKEN'),

];
