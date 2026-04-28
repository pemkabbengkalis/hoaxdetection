<?php

return [

    'title' => 'Login',

    'heading' => 'Sistem Informasi Hoaks Tracer Kabupaten Bengkalis',

    'actions' => [

        'register' => [
            'before' => 'or',
            'label' => 'sign up for an account',
        ],

        'request_password_reset' => [
            'label' => 'Forgot password?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Silakan masukkan email anda',
        ],

        'password' => [
            'label' => 'Silakan masukkan password anda',
        ],

        'remember' => [
            'label' => 'Ingat saya',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Masuk',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'email atau password salah',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Terlalu banyak percobaan login',
            'body' => 'Silakan coba lagi dalam :seconds detik.',
        ],

    ],

];
