<?php

    return [
        'api_prefix' => 'api',
        'allowed_id' => [],
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
