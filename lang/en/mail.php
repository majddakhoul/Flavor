<?php

return [
    'orders' => [
        'confirmation' => [
            'subject' => 'Order :reference received',
            'heading' => 'Your order is in',
            'intro' => 'The kitchen has your order. We will email you when the status changes.',
        ],
        'status' => [
            'subject' => 'Order :reference is :status',
            'heading' => 'Order update',
            'intro' => 'The status of your order has changed.',
        ],
    ],
    'reservations' => [
        'ticket' => [
            'subject' => 'Reservation :code confirmed',
            'heading' => 'Your table is held',
            'intro' => 'Show this code at the door and we will seat you.',
        ],
        'status' => [
            'subject' => 'Reservation :code is :status',
            'heading' => 'Reservation update',
            'intro' => 'The status of your reservation has changed.',
        ],
    ],
    'people' => [
        'credentials' => [
            'subject' => 'Your Flavor account is ready',
            'heading' => 'Welcome to the team, :name',
            'intro' => 'Here is the password for your first sign in.',
            'footnote' => 'Change this password after your first sign in.',
        ],
    ],
    'auth' => [
        'verification' => [
            'subject' => 'Your verification code',
            'heading' => 'Confirm your email, :name',
            'intro' => 'Enter this code on the verification page.',
        ],
    ],
    'inventory' => [
        'low-stock' => [
            'subject' => 'Low stock: :ingredient',
            'heading' => 'Stock is running low',
            'intro' => 'An ingredient has dropped to or below its threshold.',
        ],
    ],
];
