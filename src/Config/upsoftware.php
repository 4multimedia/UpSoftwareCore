<?php

    return [
        'api_prefix' => 'api',
        'allowed_id' => ['127.0.0.1'],
        'otp' => [
            'time' => 30,
            'retry_time' => 5,
            'type' => 'digits',
            'unique' => false,
            'history' => true,
            'length' => 6,
            'login' => true,
            'register' => true,
            'reset' => true
        ],
        'tenancy' => true,
        'rows_per_page' => 100,
        'login' => [
            'scenario' => 'otp', // otp, none
        ],
        'user_resources' => \Upsoftware\Auth\Http\Resources\UserResource::class,
        'register' => [
            'scenario' => 'otp', // otp, otp_auto, auto, activate
        ],

        /*
        |--------------------------------------------------------------------------
        | Dodatkowe pola rejestracyjne
        |--------------------------------------------------------------------------
        |
        | Tutaj możesz zdefiniować dodatkowe pola, które będą zbierane podczas rejestracji.
        | Każde pole będzie automatycznie walidowane i przypisywane do użytkownika.
        |
        */
        'register_additional_fields' => [],

        /*
        |--------------------------------------------------------------------------
        | Akcje po rejestracji
        |--------------------------------------------------------------------------
        |
        | Możesz zdefiniować funkcje, które zostaną wywołane po zakończeniu procesu
        | rejestracji. Możesz na przykład wysłać powiadomienie, zapisać użytkownika
        | do dodatkowej tabeli itp.
        |
        */
        'store_register' => function($request) {},
        'after_register' => function ($user, $request) {},
    ];
