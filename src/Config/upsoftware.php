<?php

    return [
        'api_prefix' => 'api',
        'otp' => [
            'time' => 30,
            'type' => 'digits', // digits, letters,
            'unique' => false,
            'history' => true,
            'length' => 6,
            'login' => true,
            'register' => true,
            'reset' => true
        ]
    ];
