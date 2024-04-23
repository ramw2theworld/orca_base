<?php

return [
    // Default configuration values
    'payment'=>[
        'stripe'=>[
            'public_key'=> env('STRIPE_PUBLIC_KEY'),
            'secret_key'=> env('STRIPE_SECRET_KEY'),
        ],
    ]
];
